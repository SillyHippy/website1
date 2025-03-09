<?php
namespace Tenweb_Builder;

class RemoteTemplates {

  protected static $instance = null;

  private function __construct(){
    // Commented this out till we implement this feature. If we do.
    // add_filter('option_elementor_remote_info_library', [$this, 'remote_info_library'], 2, 10);

    add_action('wp_ajax_elementor_get_library_data', [$this, 'sync_templates'], 2, 10);
    add_action('wp_ajax_elementor_get_template_data', [$this, 'insert_template'], 1, 1);

	//phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( isset($_REQUEST['actions']) ) {
	  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
      $data = json_decode( stripslashes( sanitize_text_field( $_REQUEST['actions'] ) ), true );
      if ( !empty($data) ) {
        reset($data);
        $key = key($data);
        if (strpos($key, "tenweb_") !== false) {
          add_action('wp_ajax_elementor_ajax', [$this, 'elementor_ajax'], 1, 1);
        }
      }
    }
  }

  /**
   * @param array $data
   *
   * @return bool|\WP_Error
   */
  public function insert_template( $data = [] ) {
    $template_id_str = $data['template_id'];
    $template_id = explode('tenweb_', $template_id_str);

    if(count($template_id) !== 2) {
      return false;
    }

    $template_id = intval($template_id[1]);
    include_once TWBB_DIR . '/templates/import/import.php';
    $import = new Import();

    $single_template = $import->insert_single_template( ['template_id' => $template_id, 'type' => 'template'] );
    if (is_wp_error($single_template)) {
      return new \WP_Error('template_json_not_found', 'Template json not found [template_id: ' . $template_id_str . '].');
    }

	$responses = [];
    $responses[$template_id_str] = $single_template;
    $data = [
      'code' => 200,
      'success' => true,
      'data' => [
        'responses' => $responses
      ]
    ];
	//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
    echo json_encode($data); exit();
  }

  public function remote_info_library($val, $opt){
    if(!wp_doing_ajax()) {
      return $val;
    }

    $this->sync_templates();

    if( gettype($val['categories']) === 'array' ) {
      $val['categories'][] = '10web';
    } else {
      $val['categories'] = substr_replace($val['categories'], ',"10web"', -1, 0);
    }

    $templates = get_option('twbb_templates', []);

    foreach($templates as $template) {

      $screenshots = explode(',', $template['screenshots_url']);

      $val['templates'][] = [
        'id' => 'tenweb_' . $template['id'],
        'title' => $template['title'],
        'thumbnail' => $screenshots[0],
        'tmpl_created' => strtotime('2016/01/01'),
        'author' => '10Web',
        'url' => $template['demo_url'],
        'type' => 'tenweb',
        'subtype' => '10web',
        'tags' => ["About"],
        'menu_order' => 0,
        'popularity_index' => 2,
        'trend_index' => 2,
        'is_pro' => 0,
        'has_page_settings' => 1
      ];
    }


    return $val;

  }

  public function sync_templates($sync = false){

    $types = ['templates', 'sites'];

    foreach( $types as $type ) {
      $opt_name = 'twbb_' . $type;
      if($sync === false || $sync === "") {
		$transient = get_site_transient($opt_name);
        if( $transient !== false ) {
          continue;
        }
      }
      $templates = $this->get_from_remote($type);
      if($templates !== null) {
        update_option($opt_name, $templates);
        set_site_transient($opt_name, 1, 24 * 60 * 60);
      } else {
        set_site_transient($opt_name, 1, 15 * 60);
      }
    }
  }

  public function elementor_ajax(){
    if(check_ajax_referer('elementor_ajax', '_nonce', false) === false) {
      return;
    }

    //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $actions = json_decode(stripslashes(sanitize_text_field( $_POST['actions'] )), true);

    foreach($actions as $action) {
      if($action['action'] === 'get_template_data') {
        $insert = $this->insert_template($action["data"]);
        if($insert === false || is_wp_error($insert)) {
          wp_send_json_error();
        } else {
          wp_send_json_success();
        }
      } else if($action['action'] === 'get_library_data' && !empty($action['data']['sync'])) {
        $this->sync_templates(true);
      }
    }

  }

  /**
   * @param $template_id integer
   * @param $type string template|site
   * @return null|array
   * */
  public function get_templates($template_id = null, $type = 'template') {

    $opt_name = ($type === 'template') ? 'twbb_templates' : 'twbb_sites';

    $templates = get_option($opt_name, []);

    // Do not use cached data if importing site template.
    // TODO: improve this.
    if( empty($templates) || ( null !== $template_id && 'site' === $type ) ) {
      $this->sync_templates( true );
      $templates = get_option($opt_name, []);

      if(empty($templates)) {
        return [];
      }
    }

    if($template_id === null) {
      return $templates;
    }

    foreach($templates as $template) {
      if($template['id'] === $template_id) {
        return $template;
      }
    }

    return null;
  }

  private function get_from_remote($type){

    include_once TENWEB_INCLUDES_DIR . '/class-api.php';
    $url = TENWEB_API_URL . '/'. $type;
    $args = array(
      'method' => 'GET',

    );
    $response = \Tenweb_Manager\Api::get_instance()->request($url, $args);
    if($response !== null && isset($response['data'])) {
      return $response['data'];
    } else {
      return null;
    }
  }

  public static function get_instance(){
    if(self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }
}