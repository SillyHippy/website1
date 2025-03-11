<?php
namespace Tenweb_Builder;

use Tenweb_Builder\Modules\Helper;

class Import {

  private $builder_upload_dir;
  private $builder_upload_url;
  private $import_data;
  protected $import_type = ""; // possible values are ai_regenerate, ai_recreate, default
  protected $delete_last_imported_data;
  protected $uid;
  protected $clear_cache;
  protected $import_site_object;

  public function __construct($import_type="ai_recreate", $delete_last_imported_data=null, $uid=null, $clear_cache=true) {

    $this->import_type = $import_type;
    $this->uid = $uid;
    $this->clear_cache = $clear_cache;

    $this->delete_last_imported_data = $delete_last_imported_data;

    $wp_upload_dir = wp_upload_dir();
    $this->builder_upload_dir = $wp_upload_dir['basedir'] . '/tenweb-builder/';
    $this->builder_upload_url = $wp_upload_dir['baseurl'] . '/tenweb-builder/';
    include_once ABSPATH . '/wp-admin/includes/image.php';

    \Tenweb_Builder\Apps\SVGUpload::getInstance();
  }

  /**
   * @param $template_id
   *
   * @return array|bool|mixed|object|void
   */
  public function get_import_data() {
    if ( !$this->import_data ) {
      $this->import_data = get_option( $this->option_name('tenweb_template_import_data') );
    }
    if ( FALSE !== $this->import_data ) {
      $data = json_decode($this->import_data, TRUE);
    }
    if ( !empty( $data ) ) {
      return $data;
    }

    return FALSE;
  }

  /**
   * @param array $args
   *
   * @return array|bool|\WP_Error
   */
  public function start_import( $args = [] ) {
    $blog_id = !empty( $args[ 'blog_id' ] ) ? $args[ 'blog_id' ] : FALSE;
    if ( $blog_id ) {
      //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.switch_to_blog_switch_to_blog
      switch_to_blog($blog_id);
    }
    add_option($this->option_name('tenweb_import_in_progress'), 1);
    if ( is_array($args) ) {
      $template_id  = !empty($args['template_id']) ? $args['template_id'] : FALSE;
      $template_url = !empty($args['template_url']) ? $args['template_url'] : FALSE;
    } else {
      $template_id  = $args;
      $template_url = FALSE;
    }

    delete_option($this->option_name('tenweb_template_import_data'));

    if($this->import_type === "ai_recreate"){
      update_option('elementor_experiment-container', 'active');
    }else if($this->import_type === "ai_regenerate"){
      update_option('elementor_experiment-additional_custom_breakpoints', 'inactive');

      //TODO remove option changes from here(move to builder install) when elementor will be installed first then builder
      update_option('elementor_experiment-editor_v2', 'active' );
    }
    update_option('elementor_experiment-co_pilot', 'active');
    if($this->delete_last_imported_data){
      $default_option_name = $this->option_name('twbb_imported_site_data');;
      if($this->import_type === "ai_regenerate"){
        $this->delete_last_imported_site_data([], $this->option_name('twbb_imported_site_data_generated'));
        delete_option($default_option_name);
      }else{
        $this->delete_last_imported_site_data([], $default_option_name);
      }

    }

    if ( $template_id ) {
      $template = $this->check_template_id($template_id);
      if ( is_wp_error($template) ) {
        return $template;
      }
      $amazon = $this->check_amazon_authorization($template_id);
      if ( is_wp_error($amazon) ) {
        return $amazon;
      }
      $data = $this->prepare_files_for_import( $amazon['url'], $amazon['headers'], $template );

    } else {
      if ( $template_url ) {
        //phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
        $data = $this->prepare_files_for_import($template_url, ['timeout' => 30], ['slug' => '']);
      }
      else {
        $data = new \WP_Error('invalid_parameters', 'Invalid parameters.' );
      }
    }
    if ( is_wp_error($data) ) {
      return $data;
    }

    //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
    add_option($this->option_name('tenweb_template_import_data'), json_encode($data));

    return TRUE;
  }

  /**
   * @param $template_id
   * @param array $args
   * @return bool|\WP_Error
   */
  public function import_attachments( $template_id, $args = array() ) {
    $data = $this->get_import_data();
    if ( FALSE !== $data ) {
      //Add filter to avoid resizing gif images and
      add_filter( 'file_is_displayable_image', function( $result, $path ) use ( $args ) {
        //phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
        if ( $result === false || ( isset( $args[ 'no_resize' ] ) && 1 == $args[ 'no_resize' ] ) ) {
          return false;
        }
        // this part is copied from file_is_displayable_image()
        //phpcs:ignore Generic.PHP.NoSilencedErrors.Forbidden
        $info = @getimagesize($path);
        if ( defined('IMAGETYPE_GIF') && $info[2] === IMAGETYPE_GIF ) {
          return false;
        }
        return $result;
      }, 2, 10 );

      include_once TWBB_DIR . '/templates/import/import-site.php';
      $import = new \Tenweb_Builder\ImportSite($this->import_type, $this);
      $import->set_template_dir($data['dir'], $data['url']);

      if($import->import_attachment() === false){
        return $import->get_errors()[0];
      }

      $data['attachments'] = $import->import_attachment();
      //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
      update_option($this->option_name('tenweb_template_import_data'), json_encode($data));
      return TRUE;
    }

    return new \WP_Error('import_attachment_failed', 'Failed to import attachments.' );
  }

  /**
   * @param $template_id
   * @return bool|\WP_Error
   */
  public function import_site( $args_form_dash ) {
    $data = $this->get_import_data();
    if ( FALSE !== $data ) {
      include_once TWBB_DIR . '/templates/import/import-site.php';
      $this->import_site_object = new \Tenweb_Builder\ImportSite($this->import_type, $this);
      $this->import_site_object->set_template_dir($data['dir'], $data['url']);
      $return_data = $this->import_site_object->import( $args_form_dash );
        $prefix = ($this->uid) ?? "";
      $uniqid = uniqid($prefix);
      $errors = $this->import_site_object->get_errors();
      if ( is_wp_error( $return_data ) ) {
          $errors[] = $return_data;
      }
      update_option('twbb_import_error_logs', $errors );
      //added this log for demo websites
      update_option('twbb_import_error_logs' . $uniqid, $errors );
      return $return_data;
    }

    return new \WP_Error('import_data_failed', 'Failed to import site data.' );
  }

  /**
   * @param $template_id
   *
   * @return bool
   */
  public function finalize_import( $template_id, $generate_metadata_mode = '' ) {
    $data = $this->get_import_data();
    if ( FALSE !== $data ) {
      include_once TWBB_DIR . '/templates/import/import-site.php';
      $import = new \Tenweb_Builder\ImportSite($this->import_type, $this);

      if($this->import_type === "ai_recreate" || $this->import_type === "ai_regenerate"){
        $imported_data = get_option($import->twbb_imported_site_data, []);
        $must_have_pages_imported = (!empty($imported_data['must_have_pages'])) ? $imported_data['must_have_pages'] : [];

        $this->check_must_have_templates($must_have_pages_imported);
      }

      if($this->import_type === "ai_recreate") {
        $import->change_products_default_names();
      }

      if ( 'bulk' === $generate_metadata_mode ) {
        $this->generate_attachment_meta_data($import);
      }

      if ( is_dir( $data['dir'] ) && $this->clear_cache) {
        $this->delete_dir( $data['dir'] );
        delete_option($this->option_name('tenweb_import_in_progress'));
        $this->clear_site_cache();
      }

      $data = array(
          'data' => 'ok',
          'page_id' => get_option('twbb_last_imported_pageID'),
      );

      //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
      return json_encode($data);
    }

    return FALSE;
  }

  public function check_must_have_templates($must_have_pages_imported) {
      $must_haves = array(
          'archive_must_have',
          '404_must_have',
          'posts_single_must_have'
      );
      foreach( $must_haves as $filename ) {
        //phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
        if(in_array($filename, $must_have_pages_imported)){
          continue;
        }
		//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
        $posts = get_posts(
              array(
                  'posts_per_page' => -1,
                  'post_type' => 'elementor_library',
                  'meta_key' => $filename
              )
          );

          if (empty($posts)) {
              include_once TWBB_DIR . '/templates/import/import-must-have-templates.php';
              $mht = new ImportMustHaveTemplates($filename);
          }
      }
  }

  /**
   * Deprecated
   *
   * @param $args
   *
   * @return array|bool|\WP_Error
   */
  public function import_site_data( $args = [] ) {
    if ( is_array( $args ) ) {
      $template_id = !empty( $args[ 'template_id' ] ) ? $args[ 'template_id' ] : FALSE;
      $page_title = !empty( $args[ 'page_title' ] ) ? $args[ 'page_title' ] : FALSE;
      $post_status = !empty( $args[ 'post_status' ] ) ? $args[ 'post_status' ] : 'draft';
      $menu_term_id = !empty( $args[ 'menu_term_id' ] ) ? $args[ 'menu_term_id' ] : FALSE;
      $menu_item_id = !empty( $args[ 'menu_item_id' ] ) ? $args[ 'menu_item_id' ] : FALSE;
      $menu_item_position = !empty( $args[ 'menu_item_position' ] ) ? $args[ 'menu_item_position' ] : FALSE;
      $blog_id = !empty( $args[ 'blog_id' ] ) ? $args[ 'blog_id' ] : FALSE;
      $import_type = !empty( $args[ 'import_type' ] ) ? $args[ 'import_type' ] : FALSE;
      $theme_id = !empty( $args[ 'theme_id' ] ) ? $args[ 'theme_id' ] : FALSE;
      $args_form_dash = array(
          'template_id' => $template_id,
          'page_title' => $page_title,
          'post_status' => $post_status,
          'menu_term_id' => $menu_term_id,
          'menu_item_id' => $menu_item_id,
          'menu_item_position' => $menu_item_position,
          'blog_id' => $blog_id,
          'import_type' => $import_type,
          'theme_id' => $theme_id
      );

      if(isset($args['color_pallet'])){
        $args_form_dash['color_pallet'] = $args['color_pallet'];
      }


      if(isset($args['font_family'])){
        $args_form_dash['font_family'] = $args['font_family'];
      }

      if ( $blog_id ) {
        //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.switch_to_blog_switch_to_blog
        switch_to_blog($blog_id);
      }
      update_option('twbb_imported_args', $args_form_dash);
    }
    else {
      $template_id = $args;
    }
    $prefix = ($this->uid) ?? "";
    $uniqid = uniqid($prefix);
    $errors = array();
    $result = $this->start_import( $args );
    if ( is_wp_error( $result ) ) {
      $errors[] = $result;
      update_option('twbb_import_error_logs', $errors );
        //added this log for demo websites
      update_option('twbb_import_error_logs' . $uniqid, $errors );
      return $result;
    }

    $result = $this->import_attachments( $template_id );
    if ( is_wp_error( $result ) ) {
      $errors[] = $result;
      update_option('twbb_import_error_logs', $errors );
        //added this log for demo websites
      update_option('twbb_import_error_logs' . $uniqid, $errors );
      return $result;
    }

    $result = $this->import_site( $args_form_dash );
    if ( is_wp_error( $result ) ) {
      return $result;
    }

    return TRUE;
  }

  /**
   * @param $template_id
   *
   * @return array|int|\WP_Error
   */
  public function import_single_template( $template_id ) {

    $template = $this->check_template_id($template_id);

    if (is_wp_error($template)) {
      return $template;
    }
    $amazon = $this->check_amazon_authorization($template_id);
    if (is_wp_error($amazon)) {
      return $amazon;
    }
    $data = $this->prepare_files_for_import($amazon['url'], $amazon['headers'], $template);
    if (is_wp_error($data)) {
      return $data;
    }
    include_once TWBB_DIR . '/templates/import/import-template.php';
    $import = new \Tenweb_Builder\ImportTemplate();
    $import->set_template_dir($data['dir'], $data['url']);
    $import_res = $import->import();
    $this->delete_dir($data['dir']);

    return $import_res;
  }

  /**
   * @param array $matches
   *
   * @return string
   * @throws \Exception
   */
	function _json_replace( $matches = [] ) {
		return $matches[1] . '"' . $this->_str_rand(8) . '"';
	}

  /**
   * @param int $length
   *
   * @return string
   * @throws \Exception
   */
	function _str_rand( $length = 64 ) {
		$length = ($length < 8) ? 8 : $length;
		return bin2hex(random_bytes(($length-($length%2))/2));
	}

	/**
   * @param array $args
   *
   * @return array|\WP_Error|null
   */
  public function insert_single_template( $args=[] ) {
    $type = $args['type'];
    $template_id = $args['template_id'];
    $template = $this->check_template_id($template_id, $type);
    if ( is_wp_error($template) ) {
      return $template;
    }
    $amazon = $this->check_amazon_authorization($template_id);
    if ( is_wp_error($amazon) ) {
      return $amazon;
    }
    $data = $this->prepare_files_for_import($amazon['url'], $amazon['headers'], $template);
    if ( is_wp_error($data) ) {
      return $data;
    }
    $template_file = $data['dir'] . 'template.json';
    if ( file_exists($template_file) ) {
	  //phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
      $template_content = file_get_contents($template_file);
      if ( !empty($template_content) ) {
        $template_json = json_decode($template_content, TRUE);
		//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
        $template_json_encode = json_encode($template_json['content']);
        $template_optput = preg_replace_callback('|("id":)"[a-z0-9]{8}"|', array(
          &$this,
          '_json_replace',
        ), $template_json_encode);
      }
    }
    $this->delete_dir($data['dir']);
    $success = FALSE;
    $content = [];
    if ( !empty($template_json) && !empty($template_optput) ) {
      $success = TRUE;
      $content = json_decode($template_optput);
    }
    $data = [
      'code' => 200,
      'success' => $success,
      'data' => [ 'content' => $content ],
    ];

    return $data;
  }

  /**
   * @param $file_url string
   *
   * @return array|\WP_Error
   * */
  protected function prepare_files_for_import( $file_url, $headers = [], $template = [] ) {
    Helper::set_import_time('start-','prepare_files_for_import');
    $prefix = ($this->uid) ? "import-" . $this->uid . '-' : "import";
    $uniqid = uniqid($prefix);
    $dir = $this->builder_upload_dir . $uniqid . '/';
    $url = $this->builder_upload_url . $uniqid . '/';
    if ( pathinfo($file_url, PATHINFO_EXTENSION) !== 'zip' ) {
      return new \WP_Error('invalid_file', 'Invalid file ' . $file_url);
    }
    $create_dir = $this->create_directory($dir);
    if ( is_wp_error($create_dir) ) {
      return $create_dir;
    }
    $zip_file = $this->download_file($dir, $file_url, $headers);
    if ( is_wp_error($zip_file) ) {
      return $zip_file;
    }
    $unzip = $this->unzip_file($zip_file, $dir);
    if ( is_wp_error($unzip) ) {
      return $unzip;
    }
    $dir .= $template['slug'] . '/';
    $url .= $template['slug'] . '/';

    Helper::set_import_time('end-','prepare_files_for_import');
    return [ "dir" => $dir, "url" => $url, "uniqid" => $uniqid ];
  }

  /**
   * @param $template_id
   *
   * @return array|\WP_Error|null
   */
  protected function check_template_id( $template_id, $type = 'site' ) {
    $template = RemoteTemplates::get_instance()->get_templates( $template_id, $type );
    if ( $template === NULL ) {
      return new \WP_Error('invalid_template_id', 'Invalid template id ' . $template_id . '.');
    }

    return $template;
  }

  /**
   * @param $template_id
   *
   * @return array|\WP_Error|null
   */
  protected function check_amazon_authorization( $template_id ) {
    $amazon = $this->get_amazon_tokens($template_id);
    if ( $amazon === NULL ) {
      return new \WP_Error('amazon_token_error', 'Amazon token error.');
    }

    return $amazon;
  }

  /**
   * @param $template_id
   *
   * @return array|null
   */
  protected function get_amazon_tokens( $template_id ) {
    include_once TENWEB_INCLUDES_DIR . '/class-api.php';
    $url = TENWEB_API_URL .'/templates/' . $template_id . '/request';
    $args = array(
      'method' => 'GET',
    );
    $response = \Tenweb_Manager\Api::get_instance()->request($url, $args);
    if ( $response !== null && !isset($response['error']) ) {
      $file = ltrim($response['path'], TWBB_S3_BUCKET);
      if ( file_exists(TENWEB_INCLUDES_DIR . '/class-amazon.php' ) ) {
        include_once TENWEB_INCLUDES_DIR . '/class-amazon.php';
        $amazon = new \Tenweb_Manager\Amazon( $response['key'], $response['secret'], $response['token'], $file, TWBB_S3_BUCKET );
      }
      else {
        $amazon = new \Tenweb_Authorization\Amazon( $response['key'], $response['secret'], $response['token'], $file, TWBB_S3_BUCKET );
      }

      return $amazon->getRequestData();
    }
    else {
      return NULL;
    }
  }

  /**
   * @param $dir
   * @param $file_url
   * @param $headers
   *
   * @return string|\WP_Error
   */
  private function download_file( $dir, $file_url, $headers ) {
      Helper::set_import_time('start-','download_file');
    $file_content = FALSE;
	//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get, WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
    $response = wp_remote_get( $file_url, array( 'headers' => $headers, 'timeout' => 30 ) );
    if ( is_array($response) ) {
      $file_content = $response['body']; // use the content
    }
    if ( $file_content === FALSE ) {
        Helper::set_import_time('end-','download_file');
      return new \WP_Error('failed_to_download', 'Failed to download ' . $file_url . ' file.');
    }
    $ext = pathinfo($file_url, PATHINFO_EXTENSION);
    $file_name = $dir . uniqid() . '.' . $ext;
	//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents
    $result = file_put_contents($file_name, $file_content);
    if ( $result === FALSE ) {
        Helper::set_import_time('end-','download_file');
      return new \WP_Error('failed_to_create_file', 'Failed to create ' . $file_name . ' file.');
    }
      Helper::set_import_time('end-','download_file');
    return $file_name;
  }

  /**
   * @param $zip_file
   * @param $dir
   *
   * @return bool|\WP_Error
   */
  private function unzip_file( $zip_file, $dir ) {
      Helper::set_import_time('start-','unzip_file');
    if ( class_exists('ZipArchive') ) {
        $zip = new \ZipArchive();
        $res = $zip->open($zip_file);
    } else {
        if ( !class_exists('\PhpZip\ZipFile') ) {
          include_once TWBB_DIR . '/library/vendor/autoload.php';
        }
        $zip = new \PhpZip\ZipFile();
        $res = $zip->openFile($zip_file);
    }

    if ( $res === FALSE ) {
        Helper::set_import_time('end-','unzip_file');
      return new \WP_Error('failed_to_unzip', 'Failed to unzip.');
    }

    $extract = $zip->extractTo($dir);
    if ( $extract === FALSE ) {
        Helper::set_import_time('end-','unzip_file');
      return new \WP_Error('failed_to_extract', 'Failed to extract.');
    }
    $zip->close();
      Helper::set_import_time('end-','unzip_file');
    return TRUE;
  }

  /**
   * @param $dir
   *
   * @return bool|\WP_Error
   */
  private function create_directory( $dir ) {
    //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.directory_mkdir
    if ( !is_dir($dir) && mkdir($dir, 0777, TRUE) === FALSE ) {
      return new \WP_Error('dir_not_created', $dir . ' not created');
    }
    else {
      return TRUE;
    }
  }

  /**
   * @param $dir
   */
  protected function delete_dir( $dir ) {

    if ( !is_dir($dir) ) {
      return;
    }
    if ( substr($dir, strlen($dir) - 1, 1) !== '/' ) {
      $dir .= '/';
    }
    $files = glob($dir . '*', GLOB_MARK);
    foreach ( $files as $file ) {
      if ( is_dir($file) ) {
        $this->delete_dir($file);
      }
      else {
	    //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
        unlink($file);
      }
    }
	//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.directory_rmdir
    rmdir($dir);

    return;
  }


  public function clear_site_cache(){
    $gen_site_data = get_option("twbb_imported_site_data_generated");
    $regenerate_front_critical = $gen_site_data && !empty($gen_site_data["home_page_id"]) && !$gen_site_data['first_import'];
    \Tenweb_Builder\Modules\Helper::clear_site_cache(true, true, $regenerate_front_critical);
  }

  public function generate_attachment_meta_data($import_obj=null){
    if($import_obj === null) {
      include_once TWBB_DIR . '/templates/import/import-site.php';
      $import_obj = new \Tenweb_Builder\ImportSite($this->import_type, $this);
    }
    $import_obj->generate_attachment_meta();
  }

  /**
   * @param array $args empty/trash/delete
   */
  public static function delete_last_imported_site_data($args = array(), $option_name = "twbb_imported_site_data"){

    $wp_error = array();

    $defaults = array(
      'posts' => 'trash',
      'attachments' => '1',
      'terms' => '1',
      'menus' => '1',
      'options' => '1',
    );

    $args = wp_parse_args($args, $defaults);
    $data = get_option($option_name);

    $options_for_reset = [
      "blogname",
      "blogdescription",
      "show_on_front",
      "page_on_front",
      "page_for_posts",
    ];

    if ( !$data && 'delete' === $args['posts']) {
      $wp_error["status"] = "info";
      $wp_error["message"][] = __("There is no imported website template", 'tenweb-builder');
	  //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
      echo json_encode($wp_error);
      die();
    } else if ( !$data ) {
      $wp_error["message"] = __("There is no imported website template", 'tenweb-builder');
	  //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
      return json_encode($wp_error);
    }


    if ( '' !== $args['posts'] ) {
      if ( !empty($data['posts']) ) {
        foreach ( $data['posts'] as $post_id ) {
          if ( 'delete' === $args['posts'] ) {
            $delete_post = wp_delete_post($post_id, TRUE);
            if( !$delete_post || $delete_post === null || $delete_post === false ) {
              $wp_error['message'][] = sprintf(__('Could not delete id: %s post.', 'tenweb-builder'), $post_id);
            }
          }
          else {
            $post = array(
              'ID' => $post_id,
              'post_status' => 'trash',
            );
            wp_update_post($post);
          }
        }
      }
    }
    if ( '' !== $args['attachments'] ) {
      if ( !empty($data['attachments']) ) {
        foreach ( $data['attachments'] as $post_id ) {
          $attachment = wp_delete_attachment($post_id, TRUE);
          if( !$attachment || $attachment === null || $delete_post === false  ) {
            $wp_error['message'][] = sprintf(__('Could not delete image id: %s .', 'tenweb-builder'), $post_id);
          }

        }
      }
    }
    if ( '' !== $args['terms'] ) {
      if ( !empty($data['terms']) ) {
        foreach ( $data['terms'] as $term_data ) {
          $term = wp_delete_term($term_data['term_id'], $term_data['taxonomy']);
          if ( $term === false ) {
            $wp_error['message'][] = sprintf(__('Term with id: %s does not exist.', 'tenweb-builder'), $term_data['term_id']);
          } else if ( $term === 0 ) {
            $wp_error['message'][] = sprintf(__('Could not delete id: %s term.', 'tenweb-builder'), $term_data['term_id']);
          }else if ( is_wp_error( $term ) ) {
            $wp_error['message'][] = sprintf(__('Term: %s', 'tenweb-builder'), $term->get_error_message());
          }

        }
      }
    }
    if ( '' !== $args['menus'] ) {

      if ( !empty($data['menus']) ) {
        foreach ( $data['menus'] as $menu_id ) {
          $nav_menu = wp_delete_nav_menu($menu_id);
          if ( $nav_menu === false ) {
            $wp_error['message'][] = sprintf(__('Could not delete menu by id: %s .', 'tenweb-builder'), $menu_id);
          } else if ( is_wp_error( $nav_menu ) ) {
            $wp_error['message'][] = sprintf(__('Menu: %s', 'tenweb-builder'), $nav_menu->get_error_message());
          }
        }
      }
      if ( !empty($data['options']['nav_menu_locations']) ) {
        set_theme_mod('nav_menu_locations', $data['options']['nav_menu_locations']);
      }
      $options_for_reset[] = 'nav_menu_options';
    }
    if ( '' !== $args['options'] ) {
      if ( !empty($data['options']['custom_logo']) ) {
        set_theme_mod('custom_logo', $data['options']['custom_logo']);
        update_option('custom_logo', $data['options']['custom_logo']);
      }
      foreach ( $options_for_reset as $opt_name ) {
        if ( isset($data['options'][$opt_name]) && $data['options'][$opt_name] !== get_option($opt_name) ) {
          $option = update_option($opt_name, $data['options'][$opt_name]);
          if( !$option ) {
            $wp_error['message'][] = sprintf(__('Could not delete option "%s" .', 'tenweb-builder'), $opt_name);
          }
        }
      }
    }
    delete_option('twbb_imported_site_data');

    if( !empty($wp_error) ) {
      $wp_error['status'] = 'warning';
    } else {
      $wp_error['status'] = 'success';
      $wp_error['message'] = __('Site template successfully deleted', 'tenweb-builder');
    }

    if ( 'delete' === $args['posts']) {
	  //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
      echo json_encode($wp_error);
      die();
    } else {
	  //phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
      return json_encode($wp_error);
    }
  }

  public function get_import_site_object(){
    return $this->import_site_object;
  }

  public function option_name($option_name){
    if(empty($this->uid)){
      return $option_name;
    }

    return $option_name . '_' . $this->uid;
  }
}
