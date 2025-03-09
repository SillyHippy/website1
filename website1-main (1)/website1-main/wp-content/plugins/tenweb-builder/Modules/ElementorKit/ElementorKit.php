<?php

namespace Tenweb_Builder\Modules\ElementorKit;

class ElementorKit {
  public static $defaultColorPallet = 'han_blue';
  public static $defaultfontFamily = 'Montserrat';

  protected static $ultimateKitPath = TWBB_DIR . '/Modules/ElementorKit/kits/ultimate_kit.json';
  protected $savedUltimateKitStyles = null;

  public function applyUltimateKit($color_pallet, $font_family, $template_theme_kit){
    $keep_ultimate_kit_unique = apply_filters('twbb_keep_ultimate_kit_unique', true);

    if(self::isUltimateKitActive() && $keep_ultimate_kit_unique === true) {
      $this->repairUltimateKit();
      if( $this->checkUltimateKitStylesChange($color_pallet, $font_family) ) {
          $this->updateUltimateKit($color_pallet, $font_family, $template_theme_kit);
      }
      return null;
    }

    return $this->createNewUltimateKit($color_pallet, $font_family, $template_theme_kit);
  }

  public function applyDefaultUltimateKit(){
    return $this->createNewUltimateKit(self::$defaultColorPallet, self::$defaultfontFamily, null);
  }

  /**
   *
   * @param $kit_data array kit settings
   * @param $title string Title for new kit
   *
   * @return string created kit id
   * */
  public function createNewKit($kit_data, $title){
    unset($kit_data['post_title']);
    unset($kit_data['post_status']);

    return \Elementor\Plugin::instance()->kits_manager->create_new_kit($title, $kit_data);
  }

  public function createNewUltimateKit($color_pallet_id, $font_family, $template_theme_kit){
    $kit_settings = $this->getUltimateKitSettings($color_pallet_id, $font_family, $template_theme_kit);

    $kit_id = $this->createNewKit($kit_settings, "10Web kit");
    update_post_meta($kit_id, 'twbb_ultimate_kit', "1");

    $this->saveUltimateKitStyles($color_pallet_id, $font_family);
    return $kit_id;
  }

  public function updateUltimateKit($color_pallet_id, $font_family, $template_theme_kit, $clear_cache = false){
    if(!self::isUltimateKitActive()) {
      return false;
    }

    $kit_settings = $this->getUltimateKitSettings($color_pallet_id, $font_family, $template_theme_kit);

    self::getActiveKit()->update_settings($kit_settings);
    $this->saveUltimateKitStyles($color_pallet_id, $font_family);

    if($clear_cache) {
      \Tenweb_Builder\Modules\Helper::clear_site_cache();
    }
  }

  /**
   * Function adds missing kit params to ultimate kit. e.g. if user has deleted Background 1, function will add it.
   * Ultimate kit should be the active kit.
   *
   * @return bool
   */
  public function repairUltimateKit(){
    if(!self::isUltimateKitActive()) {
      return false;
    }

    $active_kit_settings = self::getActiveKitSettings();

    $ultimate_kit_settings = self::getDefaultUltimateKitSettings();
    $ultimate_kit_settings = $this->addFontFamilyInUltimateKit($ultimate_kit_settings, self::getUltimateKitStyle("font_family"));
    $ultimate_kit_settings = $this->addColorPalletInUltimateKit($ultimate_kit_settings, self::getUltimateKitStyle("color_pallet"));

    $globals = ['system_colors', 'custom_colors', 'system_typography', 'custom_typography'];
    $update_active_kit = false;
    foreach($globals as $global) {
      foreach($ultimate_kit_settings[$global] as $value) {

        $id = $value['_id'];
        $restore = true;

        foreach($active_kit_settings[$global] as $value2) {
          if($value2['_id'] === $id) {
            $restore = false;
            break;
          }
        }

        if($restore) {
          $active_kit_settings[$global][] = $value;
          $update_active_kit = true;
        }
      }
    }

    if($update_active_kit) {
      self::getActiveKit()->update_settings($active_kit_settings);
    }

    return true;
  }

  /**
   * Rules to merge current kit with the ultimate
   * 1. We take all settings from the existing kit
   * 2. We change system colors and system typographies titles (only titles, not values)
   * 3. We add all settings from ultimate kit which don't exist in existing kit
   * 4. We insert new kit with merged settings as an ultimate kit. Inserted new kit (not modifying existing), will allow
   * us to revert back user's kit, if needed
   * */
  public function mergeCurrentKitWithUltimateKit(){
    if(self::isUltimateKitActive()) {
      return false;
    }

    $existing_settings = self::getActiveKitSettings();
    $ultimate_settings = self::getUltimateKitSettings();

    $colors_to_make_pallet = [];
    foreach($existing_settings['system_colors'] as $color) {
      if($color['_id'] === 'accent') {
        $colors_to_make_pallet['primary_color'] = $color['color'];
      } else if($color['_id'] === 'secondary') {
        $colors_to_make_pallet['secondary_color'] = $color['color'];
      }
    }

    $color_pallet_id = ColorPallets::addColorPallet('original', 'Original', -1, $colors_to_make_pallet);

    $font_family = self::$defaultfontFamily;
    if(!empty($existing_settings['body_typography_font_family'])) {
      $font_family = $existing_settings['body_typography_font_family'];
    } else {
      foreach($existing_settings['system_typography'] as $typography) {
        if($typography['_id'] === 'primary') {
          if(!empty($typography['typography_font_family'])) {
            $font_family = $typography['typography_font_family'];
          }
          break;
        }
      }
    }

    foreach($ultimate_settings as $setting_name => $setting_value) {

      if(in_array($setting_name, ['system_colors', 'system_typography'], true)) {

        $id_title_mapping = [];
        foreach($setting_value as $val) {
          $id_title_mapping[$val['_id']] = $val['title'];
        }

        foreach($existing_settings[$setting_name] as $i => $existing_setting_value) {
          $existing_settings[$setting_name][$i]['title'] = $id_title_mapping[$existing_setting_value['_id']];
        }
        continue;
      }

      if(in_array($setting_name, ['custom_colors', 'custom_typography'], true)) {

        $existing_settings_ids = [];
        foreach($existing_settings[$setting_name] as $existing_setting_value) {
          $existing_settings_ids[] = $existing_setting_value['_id'];
        }

        foreach($setting_value as $val) {
          if(!in_array($val['_id'], $existing_settings_ids, true)) {
            if($setting_name === 'custom_typography'){
              $val['typography_font_family'] = $font_family;
            }
            $existing_settings[$setting_name][] = $val;

          }
        }
        continue;
      }

      if($setting_name === '__globals__') {
        foreach($setting_value as $key => $value) {
          if(!isset($existing_settings['__globals__'][$key])) {
            $existing_settings['__globals__'][$key] = $value;
          }
        }
        continue;
      }

      if(!isset($existing_settings[$setting_name])) {
        $existing_settings[$setting_name] = $setting_value;
      }
    }

    $existing_settings = $this->addColorPalletInUltimateKit($existing_settings, $color_pallet_id);

    $kit_id = $this->createNewKit($existing_settings, "10Web kit");
    update_post_meta($kit_id, 'twbb_ultimate_kit', "1");
    update_post_meta($kit_id, 'twbb_merged_with_user_kit', "1");

    $this->saveUltimateKitStyles($color_pallet_id, $font_family);
    return $kit_id;
  }

  public function getSavedUltimateKitStyles(){
    return $this->savedUltimateKitStyles;
  }

  private static function visibilityCheck(){
    return self::isUltimateKitActive();
  }

  private function getUltimateKitSettings($color_pallet_id = null, $font_family = null, $template_theme_kit = null){
    if($color_pallet_id === null) {
      $color_pallet_id = self::getUltimateKitStyle('color_pallet');
    }

    if($font_family === null) {
      $font_family = self::getUltimateKitStyle('font_family');
    }

    $keep_ultimate_kit_unique = apply_filters('twbb_keep_ultimate_kit_unique', true);
    if(self::isUltimateKitActive() && $keep_ultimate_kit_unique === true) {
      $ultimate_kit_settings = self::getActiveKitSettings();
    } else {
      $ultimate_kit_settings = self::getDefaultUltimateKitSettings();
    }

    if($font_family) {
      $ultimate_kit_settings = $this->addFontFamilyInUltimateKit($ultimate_kit_settings, $font_family);
    }

    if($color_pallet_id) {
      $ultimate_kit_settings = $this->addColorPalletInUltimateKit($ultimate_kit_settings, $color_pallet_id);
    }

    if($template_theme_kit) {
      $ultimate_kit_settings = $this->addThemeSettingsInUltimateKit($ultimate_kit_settings, $template_theme_kit);
    }

    return $ultimate_kit_settings;
  }

  private function addFontFamilyInUltimateKit($ultimate_kit_settings, $font_family){
    foreach(['system_typography', 'custom_typography'] as $global_type) {
      foreach($ultimate_kit_settings[$global_type] as $i => $typography) {
        $ultimate_kit_settings[$global_type][$i]['typography_font_family'] = $font_family;
      }
    }

    return $ultimate_kit_settings;
  }

  private function addColorPalletInUltimateKit($ultimate_kit_settings, $color_pallet_id){
    $pallet = \Tenweb_Builder\Modules\ElementorKit\ColorPallets::getColorPallet($color_pallet_id);

    foreach($pallet['kit'] as $key => $global_value) {
      if(!isset($ultimate_kit_settings[$key])) {
        continue;
      }

      foreach($global_value as $value_pallet) {

        foreach($ultimate_kit_settings[$key] as $i => $value_ultimate) {
          if($value_ultimate['_id'] === $value_pallet['_id']) {
            $ultimate_kit_settings[$key][$i]['color'] = $value_pallet['color'];
          }
        }
      }
    }

    return $ultimate_kit_settings;
  }

  private function addThemeSettingsInUltimateKit($ultimate_kit_settings, $template_theme_kit){
    foreach($template_theme_kit as $key => $value) {
      $ultimate_kit_settings[$key] = $value;
    }

    return $ultimate_kit_settings;
  }

  private function saveUltimateKitStyles($color_pallet, $font_family){
    $this->savedUltimateKitStyles = [
      'color_pallet' => $color_pallet,
      'font_family' => $font_family
    ];

    update_option('twbb_ultimate_kit_styles', $this->savedUltimateKitStyles);
  }

  private function checkUltimateKitStylesChange($color_pallet, $font_family) {
    $saved_styles = get_option('twbb_ultimate_kit_styles', []);
    if( empty($saved_styles) ) {
        return true;
    }
    if ( ( isset($saved_styles['color_pallet']) && $saved_styles['color_pallet'] !== $color_pallet )
    || ( isset($saved_styles['font_family']) && $saved_styles['font_family'] !== $font_family ) ) {
        return true;
    }
    return false;
  }

  public static function getActiveKitId(){
    return get_option('elementor_active_kit');
  }

  public static function isUltimateKitActive(){
    return get_post_meta(self::getActiveKitId(), 'twbb_ultimate_kit', true) === "1";
  }

  public static function getUltimateKitStyle($name){
    $ultimate_kit_styles = get_option('twbb_ultimate_kit_styles', []);

    $default_ultimate_kit_styles = [
      'color_pallet' => self::$defaultColorPallet,
      'font_family' => self::$defaultfontFamily
    ];

    foreach($default_ultimate_kit_styles as $key => $value) {
      if(empty($ultimate_kit_styles[$key])) {
        $ultimate_kit_styles[$key] = $value;
      }
    }

    return (isset($ultimate_kit_styles[$name])) ? $ultimate_kit_styles[$name] : null;
  }

  public static function getActiveKitSettings(){
    return self::getActiveKit()->get_settings();
  }

  public static function isElementorDefaultKitActive(){
    $active_settings = self::getActiveKitSettings();
     if(!empty($active_settings['custom_typography']) || !empty($active_settings['custom_colors'])){
       return false;
     }

    $default_colors = [
      'primary' => '#6EC1E4',
      'secondary' => '#54595F',
      'text' => '#7A7A7A',
      'accent' => '#61CE70',
    ];

    $id_value_mapping = [];
    foreach($active_settings['system_colors'] as $system_colors) {
      if($default_colors[$system_colors['_id']] !== $system_colors['color']){
        return false;
      }
    }

    return true;
  }

  public static function getDefaultUltimateKitSettings(){
    //phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
    return json_decode(file_get_contents(self::$ultimateKitPath), true);
  }

  public static function getActiveKit(){
    return \Elementor\Plugin::instance()->kits_manager->get_active_kit_for_frontend();
  }

}
