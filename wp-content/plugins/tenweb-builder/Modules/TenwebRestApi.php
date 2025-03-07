<?php
namespace Tenweb_Builder\Modules;

use Tenweb_Authorization\Helper as BuilderHelper;
use Tenweb_Builder\Templates;

/**
 * Class TenwebRestApi
 *
 * @package Tenweb_Builder\Modules\TenwebRestApi
 */
class TenwebRestApi
{
  private $helper;

  public function __construct(){
      $this->process();
  }

  public function process()
  {
    $this->helper = BuilderHelper::get_instance();
    add_action('rest_api_init', array($this, 'registerRoutes'));
  }

  public function registerRoutes()
  {
    $this->registerTemplatesRoutes();
    $this->registerRouteWPGetPagesTemplates();
    $this->registerRouteWpDeletePages();
    $this->registerRouteWpPublishPages();
    $this->registerRouteWpUnpublishPages();
    $this->registerRouteWpAddBlankPage();
    $this->registerRouteCheckDomain();
    $this->registerRouteSetPageAsHomepage();
    $this->registerRouteRemoveSidebar();
    $this->registerRouteSaveCustomizedTheme();
    $this->registerRoutSiteInfo();
    \Tenweb_Builder\Modules\ai\RestApi::get_instance();
  }

  private function registerRouteSetPageAsHomepage()
  {
    register_rest_route('tenweb-builder/v1', 'set-as-homepage',
      array(
        'methods'             => \WP_REST_Server::EDITABLE, // post method,
        'permission_callback' => array($this, 'checkAuthorization'),
        'callback'            => array($this, 'setAsHomepage'),
        'args'                => array(
          'page_id' => array(
            'type'              => 'string',
            'required'          => true,
            'validate_callback' => function ($param) {
              if (empty($param)) {
                return false;
              } else {
                return true;
              }
            }
          ),
        ),
      )
    );
  }

  private function registerRouteCheckDomain()
  {
    register_rest_route('tenweb-builder/v1', 'check_domain',
      array(
        'methods'             => 'POST',
        'permission_callback' => '__return_true',
        'callback'            => array($this, 'checkDomain'),
      )
    );
  }

  private function registerRouteWPGetPagesTemplates()
  {

    register_rest_route('tenweb-builder/v1', '/get-all-pages', array(
        'methods'             => \WP_REST_Server::READABLE, // get method
        'callback'            => array($this, 'getAllWpPages'),
        'permission_callback' => array($this, 'checkAuthorization'),
      )
    );
  }

  private function registerRouteWpDeletePages()
  {

    register_rest_route('tenweb-builder/v1', '/delete-pages', array(
        'methods'             => \WP_REST_Server::EDITABLE, // post method
        'callback'            => array($this, 'deletePages'),
        'permission_callback' => array($this, 'checkAuthorization'),
        'args'                => array(
          'page_ids' => array(
            'type'              => 'json',
            'required'          => true,
            'validate_callback' => function ($param) {
              if (empty($param)) {
                return false;
              } else {
                return true;
              }
            }
          ),
        ),
      )
    );
  }

  private function registerRouteWpPublishPages()
  {

    register_rest_route('tenweb-builder/v1', '/publish-pages', array(
        'methods'             => \WP_REST_Server::EDITABLE, // post method
        'callback'            => array($this, 'publishPages'),
        'permission_callback' => array($this, 'checkAuthorization'),
        'args'                => array(
          'page_ids' => array(
            'type'              => 'json',
            'required'          => true,
            'validate_callback' => function ($param) {
              if (empty($param)) {
                return false;
              } else {
                return true;
              }
            }
          ),
        ),
      )
    );
  }

  private function registerRouteWpUnpublishPages()
  {

    register_rest_route('tenweb-builder/v1', '/unpublish-pages', array(
        'methods'             => \WP_REST_Server::EDITABLE, // post method
        'callback'            => array($this, 'unpublishPages'),
        'permission_callback' => array($this, 'checkAuthorization'),
        'args'                => array(
          'page_ids' => array(
            'type'              => 'json',
            'required'          => true,
            'validate_callback' => function ($param) {
              if (empty($param)) {
                return false;
              } else {
                return true;
              }
            }
          ),
        ),
      )
    );
  }

  private function registerRouteWpAddBlankPage()
  {
    register_rest_route('tenweb-builder/v1', '/add-blank-page', array(
        'methods'             => \WP_REST_Server::CREATABLE, // post method
        'callback'            => array($this, 'addBlankPage'),
        'permission_callback' => array($this, 'checkAuthorization'),
        'args'                => array(
          'page_title'  => array(
            'type'              => 'string',
            'required'          => true,
            'sanitize_callback' => function ($page_title) {
              if (empty($page_title)) {
                return false;
              } else {
                return sanitize_text_field($page_title);
              }
            }
          ),
          'post_status' => array(
            'type'              => 'string',
            'required'          => true,
            'sanitize_callback' => function ($post_status) {
              if (empty($post_status)) {
                return false;
              } else {
                return sanitize_text_field($post_status);
              }
            }
          ),
        )
      )
    );
  }

  private function registerTemplatesRoutes()
  {

    register_rest_route('10webBuilder/templates', '/list',
      [
        'methods'             => 'GET',
        'callback'            => array($this, 'getTemplates'),
        'permission_callback' => function () {
          return current_user_can('publish_posts');
        },
      ]
    );
  }

  private function registerRouteRemoveSidebar()
  {
    register_rest_route('tenweb-builder/v1', '/remove_sidebar',
      array(
        'methods'             => 'POST',
        'permission_callback' => array($this, 'checkNonceAuthorization'),
        'callback'            => array($this, 'removeSidebar'),
      )
    );
  }

    public function registerRouteSaveCustomizedTheme() {
        register_rest_route('tenweb-builder/v1', 'save_customization',
            array(
                'methods'             => 'POST',
                'permission_callback' => array($this, 'checkPermission'),
                'callback'            => array($this, 'saveCustomization'),
            )
        );
    }

    public function saveCustomization(\WP_REST_Request $request) {
        $params = $request->get_body_params();
        $color = isset($params['color']) ? sanitize_text_field($params['color']) : '';
        $font = isset($params['font']) ? sanitize_text_field($params['font']) : '';
        (new \Tenweb_Builder\Modules\ElementorKit\ElementorKit)->updateUltimateKit($color, $font, null, true);
        wp_send_json_success();
    }

  public function checkAuthorization(\WP_REST_Request $request)
  {
    if (!\Tenweb_Authorization\Login::get_instance()->check_logged_in()) {
      $data_for_response = array(
        "code"    => "unauthorized",
        "message" => "unauthorized",
        "data"    => array(
          "status" => 401
        )
      );

      return new \WP_Error('rest_forbidden', $data_for_response, 401);
    }
    $authorize = \Tenweb_Authorization\Login::get_instance()->authorize($request);
    if (is_array($authorize)) {
      return new \WP_Error('rest_forbidden', $authorize, 401);
    }

    return true;
  }

    public static function checkPermission(\WP_REST_Request $request)
    {
        $nonce = $request->get_headers()['x_wp_nonce'][0];

        if (wp_verify_nonce($nonce, 'wp_rest') === false) {
            return wp_send_json_error("invalid_nonce");
        }

        if (!current_user_can("edit_posts")) {
            return wp_send_json_error("permission_error");
        }

        return true;
    }


  public function checkNonceAuthorization(\WP_REST_Request $request)
  {
    if (!check_ajax_referer('twb_pu_nonce', 'nonce', false)) {
      $data_for_response = array(
        "code"    => "wrong_nonce",
        "message" => "Wrong nonce.",
        "data"    => array(
          "status" => 401
        )
      );
      return new \WP_Error('rest_forbidden', $data_for_response, 401);
    }
    return true;
  }

  /**
   * @param WP_REST_Request $request
   *
   * @return WP_REST_Response
   */
  public function checkDomain(\WP_REST_Request $request)
  {
    if (get_site_option(TENWEB_PREFIX . '_is_available') !== '1') {
      update_site_option(TENWEB_PREFIX . '_is_available', '1');
    }
    $parameters = self::wpUnslashConditional($request->get_body_params());

    if (isset($parameters['confirm_token'])) {
      $confirm_token_saved = get_site_transient(TENWEB_PREFIX . '_confirm_token');
      if ($parameters['confirm_token'] === $confirm_token_saved) {
        $data_for_response = array(
          "code" => "ok",
          "data" => "it_was_me"  // do not change
        );
        $headers_for_response = array('tenweb_check_domain' => "it_was_me");
      } else {
        $data_for_response = array(
          "code" => "ok",
          "data" => "it_was_not_me" // do not change
        );
        $headers_for_response = array('tenweb_check_domain' => "it_was_not_me");
      }
    } else {
      $data_for_response = array(
        "code" => "ok",
        "data" => "alive"  // do not change
      );
      $headers_for_response = array('tenweb_check_domain' => "alive");
    }

    $tenweb_hash = $request->get_header('tenweb-check-hash');
    if (!empty($tenweb_hash)) {
      $encoded = '__' . $tenweb_hash . '.';
      $encoded .= base64_encode(json_encode($data_for_response));//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
      $encoded .= '.' . $tenweb_hash . '__';

      $data_for_response['encoded'] = $encoded;
      BuilderHelper::set_error_log('tenweb-check-hash', $encoded);
    }

    return new \WP_REST_Response($data_for_response, 200, $headers_for_response);
  }

  private function getActiveKit()
  {
    $active_kit_id = get_option('elementor_active_kit');
    /*
     * check if generated by AI
     */
    $is_ai_kit = false;
    $kit_post = get_post($active_kit_id);
	//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
    if (in_array($kit_post->post_title, ['AI recreated Kit', '10Web AI kit'])) {
      $is_ai_kit = true;
    }

    $kit_settings = get_post_meta($active_kit_id, '_elementor_page_settings', true);

    // If there is a custom color or custom typography, don't generate new kit
    if ($is_ai_kit === false && (!empty($kit_settings['custom_colors']) || !empty($kit_settings['custom_typography']))) {
      $is_ai_kit = true;
    }

    $active_kit = array(
      'kit_settings' => $kit_settings,
      'is_ai_kit'    => $is_ai_kit
    );

    return $active_kit;
  }

  public function setAsHomepage(\WP_REST_Request $request)
  {
    $data_for_response = array(
      'builder_status' => 0,
      'message'        => 'Failed to set as frontpage.',
    );
    try {
      $page_id = $request->get_body_params()['page_id'];

      if ($page_id && get_post_status($page_id) === 'publish' && get_post_type($page_id) === 'page') {
        update_option('page_on_front', intval($page_id));
        update_option('show_on_front', 'page');
      }
	  //phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
      $is_updated = get_option('page_on_front') == intval($page_id) ? true : false;
      if ($is_updated) {
        $data_for_response['builder_status'] = 1;
        $data_for_response['message'] = "Page is set as frontpage.";

        return new \WP_REST_Response($data_for_response, 200);
      }
    } catch (\Exception $exception) {
      return new \WP_REST_Response($data_for_response, 500);
    }
  }

  public function getAllWpPages()
  {
    //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
    $all_pages = get_posts(
      array(
        "post_type"      => array("page", 'elementor_library'),
        "posts_per_page" => -1,
        "post_status"    => array('publish', 'pending', 'draft'),
      )
    );
    $all_pages_info = array();
    foreach ($all_pages as $key => $page) {
      if (get_post_meta($page->ID, '_elementor_template_type', true) !== 'kit') {
        $arr = [];
        $arr['ID'] = $page->ID;
        $arr['title'] = $page->post_title;
        $arr['url'] = get_page_link($page->ID);
        $arr['post_modified'] = $page->post_modified;
        $arr['post_date'] = $page->post_date;
        $arr['twbb_created_with'] = get_post_meta($page->ID, 'twbb_created_with', true);
        $arr['post_type'] = $page->post_type;
        $arr['template_type'] = get_post_meta($page->ID, '_elementor_template_type', true);
        $arr['is_edited'] = self::isElementorContentEdited($page->ID);
        if ($arr['post_type'] === 'elementor_library') {
          $arr['template_condition_count'] = count(\Tenweb_Builder\Condition::get_instance()->get_template_condition($page->ID));
        }
        $arr['post_status'] = $page->post_status;
		//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
        if (get_option('page_on_front') == $page->ID) {
          $arr['page_on_front'] = true;
        }
        $all_pages_info[] = $arr;
      }
    }
    if (!empty($all_pages_info)) {
      $all_pages_info[] = $this->getActiveKit();
    }

    return $all_pages_info;
  }

  /**
   * @param $request \WP_REST_Request
   * */
  public static function getTemplates()
  {

    $templates_slugs = Templates::get_instance()->get_templates_slugs();

    $args = [
      'numberposts' => -1,
      'post_type'   => 'elementor_library',
      'post_status' => 'publish',
      'meta_key'    => '_elementor_template_type'
    ];

    $templates_list = [];

    foreach ($templates_slugs as $slug) {
      $args['meta_value'] = $slug;//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
      $posts = get_posts($args);//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts

      $templates_list[$slug] = $posts;
    }

    return $templates_list;
  }

  public function deletePages(\WP_REST_Request $request)
  {
    $data_for_response = array(
      'status'  => 0,
      'message' => 'Failed to delete a page.',
    );
    try {
      $pages = json_decode($request->get_body_params()['page_ids']);
      $is_deleted = wp_delete_post(intval($pages[0]));
      if ($is_deleted) {
        $data_for_response['status'] = 1;
        $data_for_response['message'] = "Page is deleted.";

        return new \WP_REST_Response($data_for_response, 200);
      }
    } catch (\Exception $exception) {
      return new \WP_REST_Response($data_for_response, 500);
    }
  }

  public function publishPages(\WP_REST_Request $request)
  {
    $data_for_response = array(
      'builder_status' => 0,
      'message'        => 'Failed to publish a page.',
    );
    try {
      $pages = json_decode($request->get_body_params()['page_ids']);
      $is_published = false;
      $the_post = array(
        'ID'          => intval($pages[0]),
        'post_status' => 'publish',
      );
      $post_updated = wp_update_post($the_post);
      if (!is_wp_error($post_updated) && $post_updated > 0) {
        $is_published = true;
      }
      if ($is_published) {
        $data_for_response['builder_status'] = 1;
        $data_for_response['message'] = "Page is published.";

        return new \WP_REST_Response($data_for_response, 200);
      }
    } catch (\Exception $exception) {
      return new \WP_REST_Response($data_for_response, 500);
    }
  }

  public function unpublishPages(\WP_REST_Request $request)
  {
    $data_for_response = array(
      'builder_status' => 0,
      'message'        => 'Failed to unpublish a page.',
    );
    try {
      $pages = json_decode($request->get_body_params()['page_ids']);
      $is_unpublished = false;
      $the_post = array(
        'ID'          => intval($pages[0]),
        'post_status' => 'draft',
      );
      $post_updated = wp_update_post($the_post);
      if (!is_wp_error($post_updated) && $post_updated > 0) {
        $is_unpublished = true;
      }
      if ($is_unpublished) {
        $data_for_response['builder_status'] = 1;
        $data_for_response['message'] = "Page is unpublished.";

        return new \WP_REST_Response($data_for_response, 200);
      }
    } catch (\Exception $exception) {
      return new \WP_REST_Response($data_for_response, 500);
    }
  }

  public function addBlankPage(\WP_REST_Request $request)
  {
    $data_for_response = array(
      'status'  => 0,
      'message' => 'Failed to create a page.',
    );
    $page_title = $request->get_body_params()['page_title'];
    $post_status = $request->get_body_params()['post_status'];
    $menu_term_id =
      isset($request->get_body_params()['menu_term_id']) ? $request->get_body_params()['menu_term_id'] : false;
    $menu_item_id =
      isset($request->get_body_params()['menu_item_id']) ? $request->get_body_params()['menu_item_id'] : false;
    $menu_item_position =
      isset($request->get_body_params()['menu_item_position']) ? $request->get_body_params()['menu_item_position'] : false;
    $page_id = wp_insert_post(
      array(
        'post_type'   => 'page',
        'post_title'  => $page_title,
        'post_status' => $post_status,
      )
    );
    try {
      if ($page_id > 0) {
        $change_in_menus = array();
        update_post_meta($page_id, 'twbb_created_with', 'twbb_blank');
        if ($menu_term_id && $menu_item_id && $menu_item_position) {
          $change_in_menus[] = array(
            'menu_term_id'       => $menu_term_id,
            'menu_item_id'       => $menu_item_id,
            'menu_item_position' => $menu_item_position,
          );
        } else if ($post_status === 'publish') {
          /* check if there is header and footer menus(setted from theme) and add page in next available position */
          $menu_ids = [];
          if (isset(get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['header_menu'])) {
            $header_menu_id = get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['header_menu'];
            $menu_ids['header_menu_id'] = $header_menu_id;
          }
          if (isset(get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['footer_menu'])) {
            $footer_menu_id = get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['footer_menu'];
            $menu_ids['footer_menu_id'] = $footer_menu_id;
          }
          foreach ($menu_ids as $menu_id) {
            if ($menu_id !== 0) {
              $menu = wp_get_nav_menu_items($menu_id);
              $menu_item_id = 0;
              $menu_item_position = 0;
              foreach ($menu as $menu_item) {
                foreach ($menu_item->classes as $menu_classes) {
                  if (strpos($menu_classes, 'ai-recreated-menu-item') !== false) {
                    $menu_item_id = $menu_item->ID;
                    $menu_item_position = $menu_item->menu_order;
                    break 2;
                  }
                }
              }
              $change_in_menus[] = array(
                'menu_term_id'       => $menu_id,
                'menu_item_id'       => $menu_item_id,
                'menu_item_position' => $menu_item_position,
              );
            }
          }
        }
        foreach ($change_in_menus as $menu) {
          wp_update_nav_menu_item($menu['menu_term_id'], $menu['menu_item_id'], array(
            'menu-item-title'     => $page_title,
            'menu-item-object-id' => $page_id,
            'menu-item-object'    => 'page',
            'menu-item-status'    => 'publish',
            'menu-item-type'      => 'post_type',
            'menu-item-classes'   => '',
            'menu-item-position'  => $menu['menu_item_position']
          ));
        }
        $data_for_response['status'] = 1;
        $data_for_response['message'] = "The page has been created.";
        $data_for_response['page_url'] = admin_url('post.php?post=' . $page_id . '&action=elementor');

        return new \WP_REST_Response($data_for_response, 200);
      }
    } catch (\Exception $exception) {
      return new \WP_REST_Response($data_for_response, 500);
    }
  }

  public function removeSidebar(\WP_REST_Request $request)
  {
    delete_option('twbb_sidebar');

    return new \WP_REST_Response(array(
      'builder_status' => 0,
      'message'        => 'Success',
    ), 200);

  }

  /*
      * wp 4.4 adds slashes, removes them
      *
      * https://core.trac.wordpress.org/ticket/36419
      **/
  private static function wpUnslashConditional($data)
  {

    global $wp_version;
    if ($wp_version < 4.5) {
      $data = wp_unslash($data);
    }

    return $data;
  }

  private static function isElementorContentEdited($post_id)
  {
    /**
     *
     * The function checks elementor content was edited or no.
     * If revisions are disabled returns null
     *
     * @param $post_id integer
     *
     * @returns boolean|null
     */
    if ((int)WP_POST_REVISIONS === 0) {
      return null;
    }

    // get first version of page
    //phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_numberposts
    $revs = wp_get_post_revisions($post_id, ["order" => "ASC", "numberposts" => 1]);

    if (empty($revs)) {
      return false;
    }

    $revision = $revs[key($revs)];
    $first_elementor_data = get_post_meta($revision->ID, "_elementor_data", true);
    $current_elementor_data = get_post_meta($post_id, "_elementor_data", true);

    return $first_elementor_data != $current_elementor_data;//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
  }

    private function registerRoutSiteInfo()
    {
        register_rest_route('tenweb-builder/v1', 'get_info',
            array(
                'methods' => \WP_REST_Server::EDITABLE, // post method,
                'permission_callback' => array($this, 'checkAuthorization'),//'__return_true',
                'callback' => array($this, 'getSiteInfo'),)
        );
    }

    public function getSiteInfo() {
        $data = [
            'business_name' => '',
            'business_description' => '',
            'business_type' => '',
            'primary_color' => '',
            'secondary_color' => '',
            'font' => '',
            'style' => '',
            'woocommerce' => \Tenweb_Builder\Modules\Utils::getWoocommerceData(),
            'blog' => \Tenweb_Builder\Modules\Utils::getBlogData(),
        ];
        $twbb_site_description = get_option('twbb_site_description');
        if( $twbb_site_description !== null ) {
            if ( isset($twbb_site_description['description']) ) {
                $data['business_description'] = esc_html($twbb_site_description['description']);
            }
            if ( isset($twbb_site_description['name']) ) {
                $data['business_name'] = esc_html($twbb_site_description['name']);
            }

            if ( isset($twbb_site_description['business_type']) ) {
                $data['business_type'] = esc_html($twbb_site_description['business_type']);
            }
        }

        $elementor_settings = \Elementor\Plugin::$instance->kits_manager->get_current_settings();
        $global_system_colors = isset($elementor_settings['system_colors']) ? $elementor_settings['system_colors'] : [];
        $globals_for_send = ['accent', 'secondary'];
        foreach ($global_system_colors as $key => $value) {
            if ( in_array($value['_id'], $globals_for_send, true) ) {
                if (strlen($value['color']) > 7) {
                    $value['color'] = substr($value['color'], 0, 7);
                }

                if ( $value['_id'] === 'accent' ) {
                    $data['primary_color'] = esc_html($value['color']);
                } else if( $value['_id'] === 'secondary' ) {
                    $data['secondary_color'] = esc_html($value['color']);
                }
            }
        }

        $twbb_color_pallets = get_option('twbb_ultimate_kit_styles');
        if( isset($twbb_color_pallets['font_family']) ) {
            $data['font'] = esc_html($twbb_color_pallets['font_family']);
        }

        $twbb_style = get_option('twbb_kit_theme_name');
        if( $twbb_style ) {
            $data['style'] = esc_html($twbb_style);
        }
        return new \WP_REST_Response( $data, 200 );
    }
}
