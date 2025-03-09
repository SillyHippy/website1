<?php
namespace Tenweb_Builder;

class ImportMustHaveTemplates {
    public $mh_options;
    public $filename;

    public function __construct ($filename) {
        $this->filename = $filename;
        $this->mh_options = [
            'archive_must_have' => array (
                'opt_name' => 'twbb_archive_conditions',
                'opt_value'=> array([
                    'condition_type' => 'include',
                    'page_type' => 'archive',
                    'post_type' => 'all',
                    'filter_type' => 'all',
                    'specific_pages' => [],
                    'order' => 0
                ])
            ),
            'posts_single_must_have' => array (
                'opt_name' => 'twbb_singular_conditions',
                'opt_value' => array([
                    'condition_type' => 'include',
                    'page_type' => 'singular',
                    'post_type' => 'post',
                    'filter_type' => 'all',
                    'specific_pages' => [],
                    'order' => 0
                ])
            ),
            '404_must_have' => array (
                'opt_name' => 'twbb_singular_conditions',
                'opt_value' => array([
                    'condition_type' => 'include',
                    'page_type' => 'singular',
                    'post_type' => 'not_found',
                    'filter_type' => 'all',
                    'specific_pages' => [],
                    'order' => 0
                ])
            ),
        ];
        $this->import_mh_templates($filename);
    }

    public function import_mh_templates($filename) {
        include_once TWBB_DIR . '/templates/import/import-template.php';
        $dir = TWBB_DIR . '/templates/must_have_templates/' . $filename . '.json';
        $import_template = new ImportTemplate();
        $template_id = $import_template->import_single_template($dir);
        if ($this->mh_options[$filename]) {
            $this->import_template_options( $filename, $template_id );
        }
        update_post_meta($template_id, $filename, 1);
        update_post_meta($template_id, 'twbb_created_with', 'twbb_imported');
    }

    public function import_template_options( $filename, $template_id ) {
        $opt_name = $this->mh_options[ $filename ]['opt_name'];
        $opt_value = $this->mh_options[ $filename ]['opt_value'];
        $value = get_option($opt_name, []);
        $value[$template_id] = $opt_value;
        if(isset($value)) {
            update_option($opt_name, $value);
        }
    }
}