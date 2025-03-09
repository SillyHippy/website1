<?php
namespace Tenweb_Builder;

use Tenweb_Builder\DynamicTags\Module as TagsModule;
use Elementor\Widget_Heading;

if ( !defined('ABSPATH') ) {  exit; }

class Post_Title extends Widget_Heading {

  public function get_name() {
    return Builder::$prefix . '_post-title';
  }

  public function get_title() {
    return __('Post/Page Title', 'tenweb-builder');
  }

  public function get_icon() {
    return 'twbb-post-title twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-builder-widgets' ];
  }

  public function get_keywords() {
	return [ 'post', 'page', 'title', 'heading' ];
  }

  protected function register_controls() {

	parent::register_controls();

    $this->update_control('title', [
                                   'dynamic' => [
                                     'default' => \Elementor\Plugin::instance()->dynamic_tags->tag_data_to_tag_text(NULL, 'tenweb-tag-post-title'),
                                   ],
                                 ], [
                            'recursive' => TRUE,
                          ]);

    $this->update_control('link', [
                                  'dynamic' => [
                                    'default' => \Elementor\Plugin::instance()->dynamic_tags->tag_data_to_tag_text(NULL, 'tenweb-tag-post-url'),
                                  ],
                                ], [
                            'recursive' => TRUE,
                          ]);

    $this->update_control('header_size', [
                                         'default' => 'h1',
                                       ]);
  }

  public function get_common_args() {
    return [
      '_css_classes' => [
        'default' => 'entry-title',
      ],
    ];
  }
//phpcs:disable
  protected function content_template() {
    if ( \Elementor\Plugin::instance()->editor->is_edit_mode() && Templates::get_instance()->is_elementor_template_type() ) {
	  $title = __('Post Title', 'tenweb-builder');
	 ?>
      <#
      settings.title = '<?php echo $title; ?>';
      #>
      <?php
    }
    parent::content_template();
  }
//phpcs:enable
  public function get_settings_for_display( $setting_key = NULL ) {
    $settings = parent::get_settings_for_display($setting_key);
    if ( \Elementor\Plugin::instance()->editor->is_edit_mode() && Templates::get_instance()->is_elementor_template_type() ) {
      $settings['title'] = __('Post Title', 'tenweb-builder');
    }

    return $settings;
  }

  protected function get_html_wrapper_class() {
    return parent::get_html_wrapper_class() . ' elementor-page-title tenweb-page-title elementor-widget-' . parent::get_name();
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Post_Title());
