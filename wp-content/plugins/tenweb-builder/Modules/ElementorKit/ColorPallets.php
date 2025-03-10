<?php

namespace Tenweb_Builder\Modules\ElementorKit;

class ColorPallets {

  protected static $palletsDir = TWBB_DIR . '/Modules/ElementorKit/kits/color_palettes/';

  protected static $colorPallets = null;
  protected static $colorPalletsForPreview = null;

  public static function getColorPallets(){
    if(self::$colorPallets === null) {
      self::initColorPallets();
    }

    return self::$colorPallets;
  }

  public static function getColorPalletsForPreview(){
    if(self::$colorPalletsForPreview === null) {
      self::initColorPalletsForPreview();
    }

    return self::$colorPalletsForPreview;
  }

  public static function getColorPallet($pallet_id){
    $pallet = null;
    foreach(self::getColorPallets() as $color_pallet) {
      if($color_pallet['id'] === $pallet_id) {
        $pallet = $color_pallet;
        break;
      }
    }

    return $pallet;
  }

  public static function addColorPallet($id, $title, $order, $colors){
    return self::storeColorPallet([
      'id' => $id,
      'title' => $title,
      'order' => $order,
      'kit' => self::makeColorPallet($colors)
    ]);
  }

  public static function addAIGeneratedColorPallet($colors, $palletId='ai_generated', $palletTitle="AI generated"){
    return self::addColorPallet($palletId, $palletTitle, -1, $colors);
  }

  public static function makeColorPallet($colors){

    $accentColor = strval($colors['primary_color']);
    $secondaryColor = strval($colors['secondary_color']);
    $darkBackground = !empty($colors['background_dark']) ? strval($colors['background_dark']) : "#000000";

    $bgColor1 = $accentColor . "26";
    $bgColor2 = $secondaryColor . "26";

    return [
      "system_colors" => [
        ["_id" => "accent", "color" => $accentColor],
        ["_id" => "secondary", "color" => "$secondaryColor"]
      ],
      "custom_colors" => [
        ["_id" => "twbb_bg_1", "color" => $bgColor1],
        ["_id" => "twbb_bg_2", "color" => $bgColor2],
        ["_id" => "twbb_bg_inv", "color" => $darkBackground],
        ["_id" => "twbb_link", "color" => $accentColor],
        ["_id" => "twbb_accent_hover", "color" => $accentColor."CC"],
        ["_id" => "twbb_link_hover", "color" => $accentColor."80"],
      ]
    ];
  }

  protected static function storeColorPallet($pallet){
    $accentColor = null;
    $secondaryColor = null;

    foreach($pallet['kit']['system_colors'] as $color) {
      if($color['_id'] === 'accent') {
        $accentColor = $color['color'];
      } else if($color['_id'] === 'secondary') {
        $secondaryColor = $color['color'];
      }
    }

    $palletId = self::findColorPalletIdWithColors($accentColor, $secondaryColor);
    if($palletId === null) {
      $pallets = get_option('twbb_color_pallets', []);
      $pallets[] = $pallet;
      update_option('twbb_color_pallets', $pallets);
      self::clearCache();
      return $pallet['id'];
    }

    $palletWithId = self::findColorPalletWithId($palletId);

    if($palletWithId !== null && $palletWithId['default'] === true) {
      // can't add pallet with given id
      return $palletWithId['id'];
    }


    if($palletWithId !== null) {
      $customPallets = self::deletePalletWithId($palletWithId['id']);
    } else {
      $customPallets = get_option('twbb_color_pallets', []);
    }

    $customPallets[] = $pallet;
    update_option('twbb_color_pallets', $customPallets);
    self::clearCache();

    return $pallet['id'];
  }

  protected static function initColorPallets(){
    $colorPallets = [];
    foreach(glob(self::$palletsDir . '*.json') as $filename) {
	  //phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
      $pallet = json_decode(file_get_contents($filename), true);
      $pallet['default'] = true;
      $colorPallets[] = $pallet;
    }

    $customPallets = get_option('twbb_color_pallets', []);
    foreach($customPallets as $pallet) {
      $pallet['default'] = false;
      $colorPallets[] = $pallet;
    }

    usort($colorPallets, ['\Tenweb_Builder\Modules\ElementorKit\ColorPallets', 'cmpCallback']);
    self::$colorPallets = $colorPallets;
  }

  protected static function initColorPalletsForPreview(){
    $colorPallets = self::getColorPallets();
    self::$colorPalletsForPreview = [];
    foreach($colorPallets as $pallet) {

      $accentColor = null;
      $secondaryColor = null;

      foreach($pallet['kit']['system_colors'] as $color) {
        if($color['_id'] === 'accent') {
          $accentColor = $color['color'];
        } else if($color['_id'] === 'secondary') {
          $secondaryColor = $color['color'];
        }
      }

      $pallet['accent_color'] = $accentColor;
      $pallet['secondary_color'] = $secondaryColor;

      self::$colorPalletsForPreview[] = $pallet;
    }
  }

  protected static function findColorPalletIdWithColors($accentColor, $secondaryColor){
    foreach(self::getColorPallets() as $pallet) {

      $hasAccent = false;
      $hasSecondary = false;

      foreach($pallet['kit']['system_colors'] as $color) {
        if($color['_id'] === 'accent' && $color['color'] === $accentColor) {
          $hasAccent = true;
        } else if($color['_id'] === 'secondary' && $color['color'] === $secondaryColor) {
          $hasSecondary = true;
        }
      }

      if($hasAccent && $hasSecondary) {
        return $pallet['id'];
      }
    }

    return null;
  }

  protected static function findColorPalletWithId($id){
    foreach(self::getColorPallets() as $pallet) {
      if($pallet['id'] === $id) {
        return $pallet;
      }
    }

    return null;
  }

  protected static function deletePalletWithId($id){
    $customPallets = get_option('twbb_color_pallets', []);
    $filteredPallets = [];
    foreach($customPallets as $pallet) {
      if($pallet['id'] !== $id) {
        $filteredPallets[] = $pallet;
      }
    }

    update_option('twbb_color_pallets', $filteredPallets);
    self::clearCache();
    return $filteredPallets;
  }

  protected static function clearCache(){
    self::$colorPallets = null;
    self::$colorPalletsForPreview = null;
  }

  public static function cmpCallback($c1, $c2){
    return $c1['order'] <=> $c2['order'];
  }
}