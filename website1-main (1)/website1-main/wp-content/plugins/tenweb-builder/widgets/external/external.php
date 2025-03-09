<?php

namespace Tenweb_Builder;
use Elementor\Widget_Base;

class External extends Widget_Base {

  private $prefix;
  private $title;
  private $name;
  private $description;
  private $icon;
  private $slug;

  public function set($data){
    $this->slug = $data['slug'];
    $this->prefix = $data['prefix'];
    $this->title = $data['title'];
    $this->name = $data['name'];
    $this->description = $this->get_plugins_status($this->slug, $this->name);
    $this->icon = $data['icon'];
  }

  public function set_nonce(){
    return wp_create_nonce('twbb');
  }

  public function get_plugins_status($slug, $name) {
    // check if the 10web manager plugin installed
    if(!class_exists('\Tenweb_Manager\Manager')) {
      $description = "<p class='twbb_description'>" . $name . __(' plugin is missing.', 'tenweb-builder') . "</p>";
      return $description;
    }
    $manager = \Tenweb_Manager\Manager::get_product_by('slug', $slug, 'plugin');
    // get null when the user has not logged in
    if(empty($manager)) {
      $description = "<p class='twbb_description'>" . $name . __(' plugin is missing.', 'tenweb-builder') . "</p>";
      return $description;
    }
    // case when plugin installed but not active
    if($manager->is_installed()) {

      $state = $manager->get_state();

      if($state->active === false) {
        $description = "<p class='twbb_description'>" . $name . __(' plugin is not Active.', 'tenweb-builder') . "</p>";
        $description .= " <a class='twbb_activate_button one_click_action' id='activate_plugin' data-id='" . $manager->id . "' data-slug='" . $slug . "'>" . __('Activate', 'tenweb-builder') . "<span class='spinner'></span></a>";
      } else {
        $description = "<p class='twbb_description'>" . $name . __(' plugin should be updated.', 'tenweb-builder') . "</p>";
        $description .= " <a class='twbb_update_button one_click_action' id='update_plugin' data-id='" . $manager->id . "' data-slug='" . $slug . "' data-name='" . $name . "'>" . __('Update', 'tenweb-builder') . "<span class='spinner'></span></a>";
      }

    } else {  // case when plugin is not installed
      $data = \Tenweb_Manager\Manager::get_product_by('slug', $slug);
      $is_paid = 0;
      if (  isset($data) ){
        $is_paid = \Tenweb_Manager\Manager::get_product_by('slug', $slug)->is_paid;
      }

      $description = "<p class='twbb_description'>" . $name . __(' plugin is missing.', 'tenweb-builder') . "</p>";
      $description .= " <a class='twbb_install_button one_click_action' id='install_plugin' data-id='" . $manager->id . "' data-is_paid='" . $is_paid . "' data-slug='" . $slug . "'>" . __('Install', 'tenweb-builder') . "<span class='spinner'></span></a>";
    }
    return $description;
  }


  /**
   * Get widget name.
   *
   * @return string Widget name.
   */
  public function get_name(){
    return $this->prefix . '-elementor';
  }

  /**
   * Get widget title.
   *
   * @return string Widget title.
   */
  public function get_title(){
    return $this->title;
  }

  /**
   * Get widget icon.
   *
   * @return string Widget icon.
   */
  public function get_icon(){
    return $this->icon;
  }

  /**
   * Get widget categories.
   *
   * @return array Widget categories.
   */

  public function get_categories(){
    return ['tenweb-plugins-widgets'];
  }

  /**
   * Register widget controls.
   */
  protected function register_controls() {
    $this->start_controls_section('general', [
                                             'label' => $this->title,
                                           ]);
    $this->add_control('msg', [
                              'type' => \Elementor\Controls_Manager::RAW_HTML,
                              'raw' => $this->description,
                            ]);
    $this->end_controls_section();
  }

  protected function render(){
      ?>
    <p><?php esc_html__('The plugin is missing.', 'tenweb-builder');?> </p>
<?php
  }
}
