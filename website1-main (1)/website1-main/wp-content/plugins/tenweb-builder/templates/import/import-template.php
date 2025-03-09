<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 11/23/18
 * Time: 12:15 PM
 */

namespace Tenweb_Builder;

use \Elementor\TemplateLibrary\Source_Local;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Core\Base\Document;
use Elementor\Core\Settings\Page\Model;

class ImportTemplate extends Source_Local{

  private $template_url;
  private $template_dir;
  private $template_file;

  public function import(){

    $this->replace_placeholders();

    return $this->import_single_template($this->template_file);
  }

  public function import_single_template($template_file){
    //phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
    $data = json_decode( file_get_contents( $template_file ), true );

    if ( empty( $data ) ) {
      return new \WP_Error( 'file_error', 'Invalid File' );
    }

    $content = $data['content'];
    if ( ! is_array( $content ) ) {
      return new \WP_Error( 'file_error', 'Invalid File' );
    }

    $content = $this->process_export_import_content( $content, 'on_import' );

    $page_settings = [];

    if ( ! empty( $data['page_settings'] ) ) {
      $page = new Model( [
        'id' => 0,
        'settings' => $data['page_settings'],
      ] );

      $page_settings_data = $this->process_element_export_import_content( $page, 'on_import' );

      if ( ! empty( $page_settings_data['settings'] ) ) {
        $page_settings = $page_settings_data['settings'];
      }
    }
    $template_id = $this->save_item( [
      'content' => $content,
      'title' => $data['title'],
      'type' => $data['type'],
      'page_settings' => $page_settings,
    ] );
    return $template_id;
  }

  /**
   * This is simplified copy of Elementor function.
   */
  /**
   * Save local template.
   *
   * Save new or update existing template on the database.
   *
   * @since 1.0.0
   * @access public
   *
   * @param array $template_data Local template data.
   *
   * @return \WP_Error|int The ID of the saved/updated template, `WP_Error` otherwise.
   */
  public function save_item( $template_data ) {
    $type = \Elementor\Plugin::instance()->documents->get_document_type( $template_data['type'], false );

    if ( ! $type ) {
      return new \WP_Error( 'save_error', sprintf( 'Invalid template type "%s".', $template_data['type'] ) );
    }

    $template_id = wp_insert_post( [
      'post_title' => ! empty( $template_data['title'] ) ? $template_data['title'] : __( '(no title)', 'elementor' ),
      'post_status' => 'publish',
      'post_type' => self::CPT,
    ] );

    if ( is_wp_error( $template_id ) ) {
      return $template_id;
    }

    \Elementor\Plugin::instance()->documents->get( $template_id )->set_is_built_with_elementor( true );

    $data = $template_data['content'];
    $document = \Elementor\Plugin::instance()->documents->get( $template_id );

    $editor_data = $document->get_elements_raw_data( $data );

    // We need the `wp_slash` in order to avoid the unslashing during the `update_post_meta`
    $json_value = wp_slash( wp_json_encode( $editor_data ) );

    // Don't use `update_post_meta` that can't handle `revision` post type
    $is_meta_updated = update_metadata( 'post', $template_id, '_elementor_data', $json_value );

    update_metadata( 'post', $template_id, '_elementor_version', ELEMENTOR_VERSION );

    /* Copied from private function save_item_type */
    update_post_meta( $template_id, Document::TYPE_META_KEY, $template_data['type'] );
    wp_set_object_terms( $template_id, $template_data['type'], self::TAXONOMY_TYPE_SLUG );

    if ( ! empty( $template_data['page_settings'] ) ) {
      SettingsManager::get_settings_managers( 'page' )->save_settings( $template_data['page_settings'], $template_id );
    }

    return $template_id;
  }


  public function get_response( $content ) {
    //phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $actions = json_decode( stripslashes( sanitize_text_field( $_REQUEST['actions'] ) ), true );
    reset($actions);
    $template_id = key($actions);
    $response = array(
      $template_id => array(
        "success"=>true,
        "code"=>200,
        "data"=>array( "content" => $content )
      ),
    );
    return $response;
  }


  private function replace_placeholders(){
    //phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
    $content = file_get_contents($this->template_file);
    $content = self::replace_attachment_placeholder($content, $this->template_url);

    //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents
    file_put_contents($this->template_file, $content);
  }


  public function set_template_dir($dir, $url){
    $this->template_dir = $dir;
    $this->template_url = $url;
    $this->template_file = $this->template_dir . 'template.json';
  }


  public static function replace_attachment_placeholder($content, $template_url){
    $replace_url = rtrim($template_url, '/');
    $content = str_replace('{10WEB_REPLACE_ATTACH_PATH}', $replace_url, $content);
    return $content;
  }

  protected function process_element_export_import_content($element, $method){
    if($element->get_type() !== 'container') {
      return parent::process_element_export_import_content($element, $method);
    }
    return $element->get_data();
  }

}
