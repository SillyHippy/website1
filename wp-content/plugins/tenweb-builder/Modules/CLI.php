<?php
namespace Tenweb_Builder\Modules;

use Tenweb_Builder\Import;

class CLI {

  public function __construct() {
    if ( class_exists('WP_CLI') ) {
      \WP_CLI::add_command('10web-import-template', [ $this, 'import_template' ], [
          'before_invoke' => function () {
              Helper::set_import_time('start-','10web-import-template');
          },
          'after_invoke' => function () {
              Helper::set_import_time('end-','10web-import-template', true);
          }
      ]);
      \WP_CLI::add_command('10web-finalize-import', [ $this, 'finalize_import' ], [
          'before_invoke' => function () {
              Helper::set_import_time('start-','10web-finalize-import');
          },
          'after_invoke' => function () {
              Helper::set_import_time('end-','10web-finalize-import', true);
          }
      ]);
      \WP_CLI::add_command('10web-generate-attach-meta-data', [ $this, 'generate_attachment_meta_data' ], [
          'before_invoke' => function () {
              Helper::set_import_time('start-','10web-generate-attach-meta-data');
          },
          'after_invoke' => function () {
              Helper::set_import_time('end-','10web-generate-attach-meta-data', true);
          }
      ]);
      \WP_CLI::add_command('10web-generate_sections', [ $this, 'generate_sections' ], [
          'before_invoke' => function () {
              Helper::set_import_time('start-','10web-generate_sections');
          },
          'after_invoke' => function () {
              Helper::set_import_time('end-','10web-generate_sections', true);
          }
      ]);
    }
  }


  /**
   * @param $args
   * @param $assoc_args
   *
   * [--template_id=int]
   * : Predefined template id
   *
   * [--template_url=string]
   * : AI recreated template url
   *
   * @return void
   */
  public function import_template( $args, $assoc_args ) {
    require_once TWBB_DIR . "/templates/import/import.php";

    $import_type = isset($assoc_args['import_type']) ? $assoc_args['import_type'] : "default";

    $import = new Import($import_type);
    $result = $import->import_site_data( $assoc_args );
    $ai2_action = isset($assoc_args['ai2_action']) ? $assoc_args['ai2_action'] : 'build_site_from_outline';

    if ( !is_wp_error( $result ) ) {
        $last_imported_pageID = get_option('twbb_last_imported_pageID','');
      if ( $import_type === "ai_regenerate" ) {
          $homepage_id = get_option('page_on_front', $last_imported_pageID);
          if( $ai2_action === "build_secondary_page" ) {
              $homepage_id = get_option('twbb_last_imported_secondary_pageID');
          }
      } else {
          $homepage_id = $last_imported_pageID;
      }
      update_option('twbb-import-template-output-pageID', $homepage_id);
      \WP_CLI::success( $homepage_id );
    }
    else {
      update_option("twbb_import_error", $result->get_error_message());
      $result_data = array(
        "error_code" => $result->get_error_code(),
        "error_message" => $result->get_error_message(),
        "result" => $result,
      );
      \WP_CLI::error( json_encode($result_data) );//phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
    }
  }

  public function finalize_import( $args, $assoc_args ) {
    require_once TWBB_DIR . "/templates/import/import.php";
    $import_type = isset($assoc_args['import_type']) ? $assoc_args['import_type'] : "default";
    $import = new Import($import_type);
    //finalize_import
    $import->finalize_import( $assoc_args[ 'template_id' ] , '');
    \WP_CLI::success( 'Template Import Finalized');
  }

  public function generate_attachment_meta_data($args, $assoc_args){
    require_once TWBB_DIR . "/templates/import/import.php";
    $import_type = isset($assoc_args['import_type']) ? $assoc_args['import_type'] : "default";
    $import = new Import($import_type);
    $import->generate_attachment_meta_data();
    \WP_CLI::success( 'Attachments meta data is generated');
  }

  public function generate_sections() {
    require_once TWBB_DIR . "/Modules/SectionGeneration/GenerateSectionsPostsByType.php";
    \Tenweb_Builder\Modules\SectionGeneration\GenerateSectionsPostsByType::getInstance()->process(true);
    \WP_CLI::success( 'Sections are generated');
  }

    public function update_all_cli_import_time($cli_import_time)
    {
        $all_cli_import_time = get_option('twbb_all_cli_import_time', []);
        $all_cli_import_time[] = $cli_import_time;
        update_option('twbb_all_cli_import_time', $all_cli_import_time);
    }

}
