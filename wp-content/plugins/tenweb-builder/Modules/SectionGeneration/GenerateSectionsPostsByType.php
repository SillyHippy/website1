<?php

namespace Tenweb_Builder\Modules\SectionGeneration;

use Tenweb_Builder\Modules\UploadWidgetsAttachments;

class GenerateSectionsPostsByType
{
    const META_PREFIX = '_builder_generated_sections_';
    const GENERATED_SECTIONS_TYPE = 'ai-generated-sections';
    const SECTIONS_PATH = '/ai20-sections';
    public $sectionGenerationTypes = [
        'basic' => [],
        'ecommerce' => [],
    ];
    private $UploadWidgetsAttachments = null;

    protected static $instance = null;

    protected function __construct() {
        $this->UploadWidgetsAttachments = new UploadWidgetsAttachments();
        $this->process();
    }

    public function process($update_posts = false) {
        $this->checkSectionTypeDescriptions($update_posts);
        $this->fillSectionTypes($update_posts);
    }

    /*
     * Collect all section types with UI names and orders
     */
    private function fillSectionTypes($update_posts)
    {
        $sectionTypes = get_option('section_type_descriptions', []);
        $all_posts_id = $this->fillSectionTypesCPTAll($update_posts);
        if (isset($sectionTypes['error'])) {
            return;
        }
        $uploadedSections = $this->getUploadedSections();
        $sectionTypes = $sectionTypes['data'];
        foreach ($sectionTypes as $sectionType) {
            $section_type = $sectionType['key'];
            $order = $sectionType['ui_order'];
            if( !in_array($section_type, $uploadedSections, true) ) {
                continue;
            }
            $title = $sectionType['ui_name'];
            if( in_array( 'basic', $sectionType['compatible_website_types'], true) ) {
                $this->sectionGenerationTypes['basic'][$order][$section_type] = array(
                    'title' => $title,
                    'post_id' => $all_posts_id
                );
            } elseif( in_array( 'ecommerce', $sectionType['compatible_website_types'], true) ) {
                $this->sectionGenerationTypes['ecommerce'][$order][$section_type] = array(
                    'title' => $title,
                    'post_id' => $all_posts_id
                );
            }
        }
    }

    /*
     * Get all section types from the directory folders
     */
    private function getUploadedSections() {
        $upload_dir = wp_upload_dir()['basedir'];
        $dir_path = $upload_dir . self::SECTIONS_PATH;
        $directories = glob( $dir_path . '/*' , GLOB_ONLYDIR);
        $uploaded_sections = [];
        foreach ($directories as $directory) {
            $section_type = basename($directory);
            // Skip ai-generated-sections they have different logic
            if ( $section_type === self::GENERATED_SECTIONS_TYPE ) {
                continue;
            }
            $uploaded_sections[] = $section_type;
        }
        return $uploaded_sections;
    }

    private function checkSectionTypeDescriptions($update_posts) {
        //if option is not empty and we don't need to update posts, then return
        $option = get_option('section_type_descriptions', []);
        if( ( !empty( $option ) && !$update_posts ) || ( isset( $option['statusCode'] ) && $option['statusCode'] === 200 ) ){
            return $option;
        }
        if (method_exists('\Tenweb_Builder\Modules\ai\TenWebApi', 'getSectionTypeDescriptions')) {
            \Tenweb_Builder\Modules\ai\TenWebApi::get_instance()->getSectionTypeDescriptions();
            $option = get_option('section_type_descriptions', []);
            return $option;
        }
        return $option;
    }

    private function fillSectionTypesCPTAll($update_posts) {
        $type = 'all';
        $post_id = $this->checkUpdateSectionsData( $type, $update_posts );
        return $post_id;
    }

    public function checkUpdateSectionsData( $type, $update_posts = false ) {
        $post_id = $this->checkIfPostCreated($type);
        if( $update_posts ) {
            $post_id = $this->createAllSectionsData( $type, $post_id, $update_posts );
        } else if( !$post_id ) {
            $post_id = $this->createAllSectionsData( $type, $post_id );
        }
        return $post_id;
    }

    private function checkIfPostCreated($type) {
        $meta_key = self::META_PREFIX . $type;
		//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
        $post_id = get_posts(
            array(
                'meta_key'         => $meta_key,//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
                'meta_value'       => 1,//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
                'post_type'        => \Tenweb_Builder\Apps\SectionGeneration::SECTIONS_CPT,
                'numberposts'      => 1
            )
        );
        if( $post_id && isset($post_id[0]->ID) && get_post_status($post_id[0]->ID) === 'publish' ) {
            return $post_id[0]->ID;
        }

        return false;
    }

    private function createAllSectionsData( $type, $post_id, $update_posts = false ) {
        $upload_dir = wp_upload_dir()['basedir'];
        $dir_path = $upload_dir . self::SECTIONS_PATH;
        $all_sections = $this->concatinateSectionsByType( $dir_path, $type );
        if ( $post_id && ( $type === self::GENERATED_SECTIONS_TYPE || $update_posts ) ) {
			//phpcs:ignore Squiz.PHP.CommentedOutCode.Found
            //$this->updateSectionsPostData( $post_id, $all_sections );
            wp_delete_post($post_id);
            $post_id = $this->createPostByType( $type, $all_sections );
        } else if( !$post_id ) {
            $post_id = $this->createPostByType( $type, $all_sections );
        }


        return $post_id;
    }

    private function updateSectionsPostData( $post_id, $elementor_data ) {
        $meta_key = '_elementor_data';
        update_post_meta( $post_id, $meta_key, $elementor_data, true );
        return true;
    }

    private function concatinateSectionsByType($dir_path,$section_type)
    {
        // Check if the directory exists
        if (!is_dir($dir_path)) {
            return false;
        }

        // Check if the directory is not empty
        if (count(scandir($dir_path)) === 2) {
            return false;
        }

        $all_sections = [];
        if( $section_type === 'all' ) {
            $all_sections = $this->testFunctionConcatinateAllSections($dir_path);
        } else {
            $directories = glob($dir_path . '/' . $section_type . '/*', GLOB_ONLYDIR);
            if ($section_type === self::GENERATED_SECTIONS_TYPE) {
                $type = basename($dir_path) . '/' . $section_type;
                $jsonFiles = glob($dir_path . '/' . $section_type . '/*.json', GLOB_BRACE);
                $all_sections = $this->collectSectionsData($jsonFiles, $type);
            } else {
                foreach ($directories as $directory) {
                    $type = basename($dir_path) . '/' . $section_type . '/' . basename($directory);
                    $pattern = $directory . '/*.json';
                    $jsonFiles = glob($pattern, GLOB_BRACE);
                    $all_sections = array_merge($all_sections, $this->collectSectionsData($jsonFiles, $type));
                }
            }
        }

        return $all_sections;
    }

    private function testFunctionConcatinateAllSections($dir_path) {
        // Check if the directory exists
        if (!is_dir($dir_path)) {
            return false;
        }

        // Check if the directory is not empty
        if (count(scandir($dir_path)) === 2) {
            return false;
        }

        $all_sections = [];
        $main_directory = glob( $dir_path . '/*' , GLOB_ONLYDIR);
        foreach ($main_directory as $section_type) {
            $section_type = basename($section_type);
            $directories = glob( $dir_path . '/' . $section_type . '/*' , GLOB_ONLYDIR);
            foreach ($directories as $directory) {
                $type = basename($dir_path) . '/' . $section_type . '/' . basename($directory);
                $pattern = $directory . '/*.json';
                $jsonFiles = glob($pattern, GLOB_BRACE);
                $all_sections = array_merge($all_sections, $this->collectSectionsData($jsonFiles, $type));
            }
        }

        return $all_sections;
    }

    private function collectSectionsData($jsonFiles, $section_type) {
		$all_sections = array();
        foreach ($jsonFiles as $file) {
            $filename = basename($file);
            $json = file_get_contents($file);//phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
            $data = json_decode($json, true);
            $data_element = $this->changeLazyLoadForGalleries($data['elementor_data']['elements']);
            $arr = explode('/', $section_type);
            $type = end($arr);
            $generated_section_name = '';
            $data['compatible_website_types'] = $data['compatible_website_types'] ?? ["basic", "ecommerce"];
            $data['recommended_website_types'] = $data['recommended_website_types'] ?? [];
            $compatible_classes = $this->classesFromArrays($data['compatible_website_types'], 'twbb-sg-compatible-');
            $recommended_classes = $this->classesFromArrays($data['recommended_website_types'], 'twbb-sg-recommended-');

            if( $type === self::GENERATED_SECTIONS_TYPE ) {
                $generated_section_name = $section_type . '/' . $type . '/' . $data['catalogue_name'];
            }
            $section_class = $section_type . '/' . $filename;
            $each_section = [
                'id' => $this->generateUniqueId(),
                'elType' => 'container',
                'settings' => [
                    '_element_id' => 'twbb-sg-section-' . $section_class,
                    'css_classes' => 'twbb-sg-each-section ' . $generated_section_name . ' ' . $recommended_classes . ' ' . $compatible_classes,
                    'content_width' => 'full',
                    'flex_direction' => 'column'
                ],
                'elements' => $data_element,
                'isInner' => 'false',
            ];

            $all_sections[] = $each_section;
        }

        return $all_sections;
    }

    private function changeLazyLoadForGalleries(&$all_data) {
        if( !is_array($all_data) ) {
            return $all_data;
        }
        foreach ($all_data as &$element) {
            if( $element['elType'] === 'container' ) {
                $this->changeLazyLoadForGalleries($element['elements']);
            }
            if( $element['widgetType'] === 'twbb_gallery' )  {
                $element['settings']['lazyload'] = '';
            }
        }
        return $all_data;
    }

    private function createPostByType( $type, $elementor_data ) {
        $post_data = array(
            'post_title'    => 'Generated Sections ' . $type,
            'post_name'     => 'generated_sections_' . $type,
            'post_status'   => 'publish',
            'post_type'     => \Tenweb_Builder\Apps\SectionGeneration::SECTIONS_CPT,
        );

        $this->UploadWidgetsAttachments->upload($elementor_data);
        $post_id = wp_insert_post($post_data);

        update_post_meta($post_id, '_elementor_data',  wp_slash( wp_json_encode( $elementor_data)));
        update_post_meta($post_id, '_elementor_edit_mode', 'builder');
        update_post_meta($post_id, '_elementor_template_type', 'wp-post');
        add_post_meta($post_id, self::META_PREFIX . $type, '1', true);

        return $post_id;
    }

    // Generate a unique ID for each section
    private function generateUniqueId()
    {
        return substr(bin2hex(random_bytes(4)),1);
    }

    private function classesFromArrays($arr, $class_prefix) {
        $arr_classes = array_map(function($type) use ($class_prefix) {
            return $class_prefix . $type;
        }, $arr);

        $recommended_classes = implode(' ', $arr_classes);
        return $recommended_classes;
    }

    public static function getInstance(){
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
