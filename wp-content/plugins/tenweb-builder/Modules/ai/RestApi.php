<?php
namespace Tenweb_Builder\Modules\ai;

class RestApi
{

  const NAMESPACE         = 'ai-builder-tenweb/ai';
  const REST_NONCE_ACTION = 'wp_rest';

  const NOTIFICATION_OPTION       = 'twbb_notification';
  const NOTIFICATION_IMAGE_OPTION = 'twbb_notification_image';
  const NOTIFICATION_SECTION_OPTION = 'twbb_notification_section';

  const AI_OUTPUT            = 'twbb_ai_output';
  const AI_IMAGE_OUTPUT      = 'twbb_ai_image_output';

  const AI_SECTION_OUTPUT      = 'twbb_ai_section_output';

  const CORE_ENDPOINTS       = [
    'builder/new_prompt',
    'builder/simplify_language',
    'builder/make_it_longer',
    'builder/make_it_shorter',
    'builder/fix_spelling_and_grammar',
    'builder/change_tone',
    'builder/translate_to',
  ];
  const CORE_IMAGE_ENDPOINTS = [
    'builder_image/image_generate',
    'builder_image/image_edit',
    'builder_image/image_remove_bg',
    'builder_image/image_upscale',
    'builder_image/image_variations',
    'builder_image/image_expand',
  ];

    const CORE_SECTION_ENDPOINTS = [
        'sections/section_generate',
    ];

  protected static $instance = null;

  public function __construct()
  {
    $this->init_rest_api();
  }

  public function init_rest_api()
  {
    $default_rest_routs_args = [
      'methods'             => 'POST',
      'callback'            => array($this, 'core_default'),
      'permission_callback' => array($this, 'check_permission'),
    ];

    $default_rest_routs = [];
    foreach (self::CORE_ENDPOINTS as $endpoint) {

      $default_rest_routs[$endpoint] = array_merge($default_rest_routs_args, array('args' => [
        'text' => [
          'required'          => true,
          'validate_callback' => array($this, 'validate_not_empty')
        ]
      ]));
    }
    foreach (self::CORE_IMAGE_ENDPOINTS as $endpoint) {
      $default_rest_routs[$endpoint] = [];
    }

    foreach (self::CORE_SECTION_ENDPOINTS as $endpoint) {
      $default_rest_routs[$endpoint] = [];
    }
    foreach ($default_rest_routs as $endpoint => $args) {
      $args = array_merge($default_rest_routs_args, $args);
      register_rest_route(self::NAMESPACE, $endpoint, $args);
    }

    register_rest_route(self::NAMESPACE, 'ai_output',
      array(
        'methods'             => 'POST',
        'callback'            => array($this, 'get_ai_output'),
        'permission_callback' => array($this, 'check_permission'),
      )
    );

    register_rest_route(self::NAMESPACE, 'finish',
      array(
        'methods'             => 'POST',
        'callback'            => array($this, 'store_ai_output'),
        'permission_callback' => array($this, 'check_tenweb_token'),
      )
    );

    register_rest_route(self::NAMESPACE, 'ai_image_download',
      array(
        'methods'             => 'POST',
        'callback'            => array($this, 'ai_image_download'),
        'permission_callback' => array($this, 'check_permission'),
        'args'                => array(
          'image_url' => array(
            'required'          => true,
            'validate_callback' => array($this, 'validate_not_empty')
          )
        )
      )
    );
  }

  public function ai_image_download(\WP_REST_Request $request)
  {
    $params = $request->get_body_params();
    $imageurl = isset($params['image_url']) ? esc_url($params['image_url']) : '';

    if ($imageurl === '') {
      wp_send_json_error();
    }

    require_once(ABSPATH . 'wp-admin/includes/file.php');

    // Save as a temporary file
    $tmp = download_url($imageurl);

    $name = "10web_ai_generated.png";
    $upload_dir = wp_upload_dir();
    // Sets file final destination.
    $filepath = $upload_dir['basedir'] . '/' . $name;

    $file_url = $upload_dir['baseurl'] . '/' . $name;

    // Copies the file to the final destination and deletes temporary file.
    if (!copy($tmp, $filepath)) {
      @unlink($tmp);//phpcs:ignore Generic.PHP.NoSilencedErrors.Forbidden, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
      wp_send_json_error();
    }
    @unlink($tmp);//phpcs:ignore Generic.PHP.NoSilencedErrors.Forbidden, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
    wp_send_json_success(["download_url" => $file_url]);
  }


  public function core_default(\WP_REST_Request $request)
  {
    $route = trim(explode(self::NAMESPACE, $request->get_route())[1], '/');
    $params = [];
    foreach ($request->get_body_params() as $param_name => $value) {
      $param_name = sanitize_textarea_field($param_name);
      if ($param_name === 'image') {
        $value = sanitize_url($value);
      } else {
        $value = sanitize_textarea_field($value);
      }
      $params[$param_name] = $value;
    }

    $notification_option = self::NOTIFICATION_OPTION;
    if (isset($params['action_type']) && $params['action_type'] === 'builder_image') {
      $notification_option = self::NOTIFICATION_IMAGE_OPTION;
    } else if (isset($params['action_type']) && $params['action_type'] === 'sections') {
      $notification_option = self::NOTIFICATION_SECTION_OPTION;
    }

    if (get_site_transient($notification_option) === "in_progress") {
      wp_send_json_error("there_is_in_progress_request");
      die();
    }

    $api_response = TenWebApi::get_instance()->ai_action($params, $route);
    if ($api_response['error']) {

      if (in_array($api_response['error'], ['input_is_long', 'plan_limit_exceeded', 'expectation_failed'])) {//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
        wp_send_json_error($api_response['error']);
          die();
      } else {
        wp_send_json_error("api_error");
          die();
      }
    }

    set_site_transient($notification_option, "in_progress", 2 * MINUTE_IN_SECONDS);
    if(isset($api_response['body']['data']['sync']) && $api_response['body']['data']['sync'] == 1) {//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
      $this->sync_ai_output($api_response['body']['data']);
      return array("success" => true, "data" => $api_response['body']['data']);
    }
    wp_send_json_success();
      die();
  }

  public function get_ai_output()
  {
    $action_type = isset($_POST['action_type']) ? sanitize_text_field( $_POST['action_type'] ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Missing

    $response = [
      "status" => "",
      "output" => ""
    ];

    if ($action_type === 'builder_image') {
      $transient = get_site_transient(self::NOTIFICATION_IMAGE_OPTION);
    } else if ($action_type === 'sections') {
        $transient = get_site_transient(self::NOTIFICATION_SECTION_OPTION);
    } else {
      $transient = get_site_transient(self::NOTIFICATION_OPTION);
    }

    if ($transient === false) {
      $response["status"] = "done";
      wp_send_json_success($response);
    }

    $response["status"] = esc_html($transient);

    if ($transient !== "done") {
      wp_send_json_success($response);
    }

    if ($action_type === 'builder_image') {
      $response['output'] = get_site_option(self::AI_IMAGE_OUTPUT, false);
      $response['limitation'] = Utils::get_limitation('builder_image');
      delete_site_option(self::AI_IMAGE_OUTPUT);
      delete_site_transient(self::NOTIFICATION_IMAGE_OPTION);
    } else if($action_type === 'sections') {
        $response['output'] = get_site_option(self::AI_SECTION_OUTPUT, false);
        $response['limitation'] = Utils::get_limitation('sections');
        delete_site_option(self::AI_SECTION_OUTPUT);
        delete_site_transient(self::NOTIFICATION_SECTION_OPTION);
    } else {
      $response['output'] = htmlspecialchars_decode(wp_kses_post(get_site_option(self::AI_OUTPUT, false)));
      delete_site_option(self::AI_OUTPUT);
      delete_site_transient(self::NOTIFICATION_OPTION);
    }

    wp_send_json_success($response);
  }

  public function store_ai_output(\WP_REST_Request $request)
  {
    $response = $request->get_json_params();

    if (isset($response['actionType']) && $response['actionType'] === "builder_image") {
      update_site_option(self::AI_IMAGE_OUTPUT, $response['output']);
      set_site_transient(self::NOTIFICATION_IMAGE_OPTION, "done", MINUTE_IN_SECONDS);
      Utils::update_limitations($response['limitation'], 'builder_image');
    } else if(isset($response['actionType']) && $response['actionType'] === 'sections') {
        update_site_option(self::AI_SECTION_OUTPUT, $response['output']);
        set_site_transient(self::NOTIFICATION_SECTION_OPTION, "done", MINUTE_IN_SECONDS);
        Utils::update_limitations($response['limitation'], 'sections');
    } else {
      update_site_option(self::AI_OUTPUT, sanitize_textarea_field($response['output']));
      set_site_transient(self::NOTIFICATION_OPTION, "done", MINUTE_IN_SECONDS);
      Utils::update_limitations($response['limitation']);
    }

    wp_send_json_success();
  }

  public function sync_ai_output($response){
    if (isset($response['actionType']) && $response['actionType'] === "builder_image") {
      Utils::update_limitations($response['limitation'], 'builder_image');
      delete_site_option(self::AI_IMAGE_OUTPUT);
      delete_site_transient(self::NOTIFICATION_IMAGE_OPTION);
    } else if (isset($response['actionType']) && $response['actionType'] === "sections") {
        Utils::update_limitations($response['limitation'], 'sections');
        delete_site_option(self::AI_SECTION_OUTPUT);
        delete_site_transient(self::NOTIFICATION_SECTION_OPTION);
    } else {
      Utils::update_limitations($response['limitation']);
      delete_site_option(self::AI_OUTPUT);
      delete_site_transient(self::NOTIFICATION_OPTION);
    }
  }

  public static function check_permission(\WP_REST_Request $request)
  {
    $nonce = $request->get_headers()['x_wp_nonce'][0];

    if (wp_verify_nonce($nonce, self::REST_NONCE_ACTION) === false) {
      return wp_send_json_error("invalid_nonce");
    }

    if (!current_user_can("edit_posts")) {
      return wp_send_json_error("permission_error");
    }

    return true;
  }

  public function check_tenweb_token(\WP_REST_Request $request)
  {
    $auth_header = $request->get_header('tenweb_authorization');

    if (!$auth_header) {
      return false;
    }

    return TenWebApi::get_instance()->check_single_token($auth_header) === false;
  }

  public static function validate_not_empty($prompt)
  {
    return !empty($prompt);
  }

  public static function get_instance()
  {
    if (null === self::$instance) {
      self::$instance = new self;
    }

    return self::$instance;
  }

}
