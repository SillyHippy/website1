<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 7/31/18
 * Time: 4:35 PM
 */

namespace Tenweb_Builder\Modules;


class Helper {

  public static function get_post_types($args = array()){
    $defaults = array(
      'exclude_from_search' => false,
    );

    $args = wp_parse_args($args, $defaults);

    $post_types = get_post_types($args, 'objects');
    return $post_types;
  }

  /**
   * Get request value.
   *
   * @param string $key
   * @param string $default_value
   * @param bool $esc_html
   *
   * @return string|array
   */
  //TODO: Maybe we can get rid of this, not sure if this works as expected, adding sanitize_text_field here for now
  public static function get($key, $default_value = '', $esc_html = true) {
    //phpcs:disable
    if (isset($_GET[$key])) {
      $value = $_GET[$key];
    }
    elseif (isset($_POST[$key])) {
      $value = $_POST[$key];
    }
    elseif (isset($_REQUEST[$key])) {
      $value = $_REQUEST[$key];
    }
    else {
      $value = $default_value;
    }
    if (is_array($value)) {
      array_walk_recursive($value, array('self', 'validate_data'), $esc_html);
    }
    else {
      self::validate_data($value, $esc_html);
    }
    return $value;
	//phpcs:enable
  }

  public static function clear_site_cache($clear_elementor_cache=true, $flush_rewrite=true, $regenerate_home_critical=true){
    if($flush_rewrite) {
      flush_rewrite_rules();//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
    }

    if($clear_elementor_cache) {
      // Regenerate Elementor generated css files.
      \Elementor\Plugin::instance()->files_manager->clear_cache();
    }

    if(class_exists('\TenWebOptimizer\OptimizerAdmin')) {

      \TenWebOptimizer\OptimizerAdmin::get_instance();

      global $TwoSettings;
      $two_critical_pages = $TwoSettings->get_settings("two_critical_pages");

      if($regenerate_home_critical && !empty($two_critical_pages["front_page"])) {
        // after critical regeneration booster cache will be cleared

        $two_critical_pages["front_page"]["wait_until"] = "load";
        $TwoSettings->update_setting("two_critical_pages", $two_critical_pages);
        \TenWebOptimizer\OptimizerUtils::regenerate_critical("front_page");

      } else {

        // booster will clear also hosting cache
        \TenWebOptimizer\OptimizerAdmin::clear_cache(
          false,
          true,
          true,
          true,
          'front_page',
          false,
          true,
          false,
          false);

      }
    } else {
      // if booster is not active clear hosting cache
      do_action('tenweb_purge_all_caches', false);
    }

  }

  /**
   * Validate data.
   *
   * @param $value
   * @param $esc_html
   */
  private static function validate_data(&$value, $esc_html) {
    $value = stripslashes($value);
    if ($esc_html) {
      $value = esc_html(sanitize_text_field( $value ));
    }
  }
    public static function two_redirect( $url ) {
        while (ob_get_level() !== 0) {
            ob_end_clean();
        }
        wp_redirect( $url );
        exit();
    }

    public static function insertAttachmentFromUrl($s3_url, $post_id = 0, $desc = null) {
        if (!function_exists('media_sideload_image')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }

        // Set variables for storage, fix file filename for query strings.
        preg_match('/[^\?]+\.(jpe?g|jpe|gif|png|webp)\b/i', $s3_url, $matches);
        if (!$matches) {
            return new \WP_Error('image_sideload_failed', __('No valid image URL provided.'));
        }

        $file_array = array();
        $file_array['name'] = basename($matches[0]);

        // Download file to temp location.
        $file_array['tmp_name'] = download_url($s3_url);

        // If error storing temporarily, return the error.
        if (is_wp_error($file_array['tmp_name'])) {
            return $file_array['tmp_name'];
        }

        // Do the validation and storage stuff.
        $id = media_handle_sideload($file_array, $post_id, $desc);

        // If error storing permanently, unlink.
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);//phpcs:ignore Generic.PHP.NoSilencedErrors.Forbidden, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
            return $id;
        }

        // Return the attachment post ID.
        return $id;
    }

  public static function elementorTreeWalker(&$elements, $widgetsCallbacks){
    if( empty($elements) ) {
        return [];
    }
    foreach($elements as $key => $element) {

      $widgetType = ($element['elType'] === 'container') ? 'container' : $element['widgetType'];

      if($widgetType && isset($widgetsCallbacks[$widgetType])) {
        $callback = $widgetsCallbacks[$widgetType];
        if(method_exists($callback[0], $callback[1])) {
          if(is_string($callback[0])) {
            $element = $callback[0]::{$callback[1]}($element); // static method call
          }else{
            $element = $callback[0]->{$callback[1]}($element); // object method call
          }
        }
      }

      if(!empty($element['elements'])) {
        self::elementorTreeWalker($element['elements'], $widgetsCallbacks);
      }

      $elements[$key] = $element;
    }

    return $elements;
  }

    public static function set_import_time( $when, $action, $calc_duration = false)
    {
        $cli_import_time = get_option('twbb_last_cli_import_time', []);
        $cli_import_time[$when . $action] = time();
        if( $calc_duration && $when === 'end-') {
            $cli_import_time['duration-' . $action] = $cli_import_time['end-' . $action] - $cli_import_time['start-' . $action];
        }
        update_option('twbb_last_cli_import_time', $cli_import_time);
        if( $action === '10web-generate-attach-meta-data' && $when === 'end-') {
            self::update_all_import_time($cli_import_time);
        }
    }

    public static function update_all_import_time($cli_import_time)
    {
        $all_cli_import_time = get_option('twbb_all_cli_import_time', []);
        $all_cli_import_time[] = $cli_import_time;
        update_option('twbb_all_cli_import_time', $all_cli_import_time);
    }

}
