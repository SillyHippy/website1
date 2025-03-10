<?php

namespace Tenweb_Builder\Modules\ai;

class TenWebApi
{

  protected static $instance = null;

  private $api_url;
  private $access_token;
  private $domain_id;
  private $workspace_id;

  public function __construct()
  {
      $this->setVariables();
  }


    public static function get_instance()
  {
    if (null === self::$instance) {
        self::$instance = new self;
    }

    return self::$instance;
  }

  public function ai_action($params, $rest_route)
  {
    $rest_route = explode("/", $rest_route);

    $type = $rest_route[0];
    $action = $this->transform_route($rest_route[1], $params);
    $url = $this->api_url . 'actions/workspaces/' . $this->workspace_id . '/domains/' . $this->domain_id;
    $body = ["actionName" => $action, "params" => json_encode($params), "actionType" => $type];//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode

    $res = wp_safe_remote_post($url, [
        'headers' => $this->get_headers(),
        'body'    => $body,
        'timeout' => 50000,//phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
    ]);

    $response_code = (int)wp_remote_retrieve_response_code($res);

    $result = array(
      "status_code" => $response_code,
      "body"        => array(),
      "error"       => ""
    );

    if (is_wp_error($res)) {
      $result["error"] = $res->get_error_message();

      return $result;
    }

    if ($response_code !== 200) {
      if ($response_code === 417 && !empty($res['response'])) {
        if (!empty($res['response']['message']) && $res['response']['message'] === 'Expectation Failed') {
		  //phpcs:ignore Squiz.PHP.CommentedOutCode.Found
          //$result["error"] = "input_is_long";
          $result["error"] = "expectation_failed";
          return $result;
        }
      } else if ($response_code === 400) {
        $body = json_decode($res['body'], true);
        if (!empty($body['message']) && $body['message'] === 'You have exceeded your plan limit') {
          $result["error"] = "plan_limit_exceeded";
          return $result;
        }
      }
      $result["error"] = wp_remote_retrieve_response_message($res);
      return $result;
    }

    $result["body"] = json_decode(wp_remote_retrieve_body($res), true);
    return $result;
  }

  public function transform_route($action, &$params)
  {
    $widget_type = !empty($params["widget_type"]) ? $params["widget_type"] : "text";
    if ($action === "simplify_language") {
      $params["text"] = "Simplify the language of the following " . $widget_type . "\n" . $params["text"];

      return "new_prompt";
    } else if ($action === "make_it_longer") {
      $params["text"] = "Make the following " . $widget_type . " longer \n" . $params["text"];

      return "new_prompt";
    } else if ($action === "make_it_shorter") {
      $params["text"] = "Make the following " . $widget_type . " shorter \n" . $params["text"];

      return "new_prompt";
    }

    return $action;
  }

  public function get_limitations($action_type)
  {
    $url = $this->api_url . 'actions/workspaces/' . $this->workspace_id . '/limits';
    $res = wp_safe_remote_get($url, [
      'headers' => $this->get_headers(),
    ]);
    if (wp_remote_retrieve_response_code($res) !== 200) {
      return false;
    }

    $data = json_decode($res['body'], true)['data'];
    Utils::update_limitations($data, $action_type);

    return $data;
  }

  public function check_single_token($token)
  {
    $body = array('one_time_token' => $token);

    $args = array(
      'method'  => 'POST',
      'headers' => [],
      'body'    => $body
    );

    $url = TENWEB_API_URL . '/domains/' . $this->domain_id . '/check-single';
    $args['headers']["Authorization"] = "Bearer " . $this->access_token;
    if (empty($args['headers']["Accept"])) {
      $args['headers']["Accept"] = "application/x.10webmanager.v1+json";
    }
    $args['timeout'] = 50000;//phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
    $result = wp_remote_request($url, $args);

    if (is_wp_error($result)) {
      return false;
    }

    $body = json_decode($result['body'], true);

    if (isset($body['error'])) {
      return false;
    }

    if (wp_remote_retrieve_response_code($result) !== 200) {
      return false;
    }
	//TODO: check this, $response is not defined
    return (!empty($response['status']) && $response['status'] === "ok");//phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
  }

  private function get_headers()
  {
    return [
      'Authorization' => 'Bearer ' . $this->access_token,
      'Accept'        => 'application/x.10webaiassistantapi.v1+json'
    ];
  }

  private function setVariables() {
    $this->access_token = Utils::get_access_token();
    $this->domain_id = Utils::get_domain_id();
    $this->workspace_id = Utils::get_workspace_id();
    $this->api_url = TENWEB_AI_ASSISTANT;
  }

  public function sectionSyncRequest() {
      $api_url = $this->api_url . 'proxy/workspaces/' . $this->workspace_id . '/domains/' . $this->domain_id . '/sync_builder_sections';
      $sections_theme = get_option('twbb_kit_theme_name', 'classic');
      $res = wp_safe_remote_post($api_url, [
          'headers' => $this->get_headers(),
          'body'    => [
              'theme' => $sections_theme
          ],
          'timeout' => 50000,//phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
      ]);

      return $res;
  }

    public function getGeneratedSectionVariations($data) {
        $api_url = $this->api_url . 'proxy/workspaces/' . $this->workspace_id . '/sections/generate_editor_section';
        $data['woocommerce'] = \Tenweb_Builder\Modules\Utils::getWoocommerceData();
        $data['blog'] = \Tenweb_Builder\Modules\Utils::getBlogData();
        $res = wp_safe_remote_post($api_url, [
            'headers' => $this->get_headers(),
            'body'    => $data,
            'timeout' => 70000,//phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
        ]);

        return $res;
    }

    public function getSectionTypeDescriptions() {
        $api_url = $this->api_url . 'proxy/sections/description';
        $res = wp_safe_remote_get($api_url, [
            'headers' => $this->get_headers(),
            'body'    => [],
            'timeout' => 50000,//phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
        ]);
        $data = json_decode(wp_remote_retrieve_body($res), true);
        update_option('section_type_descriptions', $data, true);
        return $res;
    }
}
