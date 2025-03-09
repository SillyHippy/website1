<?php

namespace Tenweb_Builder\Modules;

class UploadWidgetsAttachments {

  private $callbacks = [];
  private $attachmentPath;
  private $attachmentURL;
  private $cache = [];

  public function __construct(){
    $this->initCallbacks();

    $wp_upload_dir = wp_upload_dir();
    $this->attachmentPath = $wp_upload_dir['path'] . '/';
    $this->attachmentURL = $wp_upload_dir['url'] . '/';
  }

  public function upload(&$elementorData){
    $this->initCash();
    \Tenweb_Builder\Modules\Helper::elementorTreeWalker($elementorData, $this->callbacks);
  }


  /** Callbacks **/
  public function uploadGalleryAttachment($element){

    foreach($element['settings']['gallery'] as $i => $attachment) {
      if(empty($attachment['url'])) {
        continue;
      }

      $attachData = $this->getAttachmentData($attachment['url']);
      if(empty($attachData)) {
        continue;
      }

      $attachment['id'] = $attachData['id'];
      $attachment['url'] = $attachData['url'];

      $element['settings']['gallery'][$i] = $attachment;
    }

    return $element;
  }

  public function uploadSliderAttachment($element){
    foreach($element['settings']['slides'] as $i => $slide) {
      if(empty($slide['background_image'])) {
        continue;
      }

      $attachData = $this->getAttachmentData($slide['background_image']['url']);
      if(empty($attachData)) {
        continue;
      }

      $slide['background_image']['id'] = $attachData['id'];
      $slide['background_image']['url'] = $attachData['url'];
      $slide['background_image']['source'] = 'library';

      $element['settings']['slides'][$i] = $slide;
    }

    return $element;
  }

  public function uploadTestimonialCarouselAttachment($element){
    foreach($element['settings']['slides'] as $i => $slide) {
      if(empty($slide['image'])) {
        continue;
      }

      $attachData = $this->getAttachmentData($slide['image']['url']);
      if(empty($attachData)) {
        continue;
      }

      $slide['image']['id'] = $attachData['id'];
      $slide['image']['url'] = $attachData['url'];
      $slide['image']['source'] = 'library';

      $element['settings']['slides'][$i] = $slide;
    }

    return $element;
  }

  public function uploadContainerAttachment($element){
    if(empty($element['settings']['background_image'])) {
      return $element;
    }

    $attachData = $this->getAttachmentData($element['settings']['background_image']['url']);
    if(empty($attachData)) {
      return $element;
    }

    $element['settings']['background_image']['id'] = $attachData['id'];
    $element['settings']['background_image']['url'] = $attachData['url'];
    $element['settings']['background_image']['source'] = 'library';

    return $element;
  }

  // TODO: Implement this.
  public function uploadTeamAttachment($element) {}

  private function getAttachmentData($url){
    $attachData = $this->getFromCache($this->urlHash($url));

    if($attachData !== null) {
      return $attachData;
    }

    $data = $this->uploadAttachment($url);
    if($data === false) {
      return false;
    }

    $this->addToCache($data['hash'], $data['id']);
    return $this->getFromCache($data['hash']);
  }

  private function uploadAttachment($originalUrl){

    $attachID = \Tenweb_Builder\Modules\Helper::insertAttachmentFromUrl($originalUrl);

    if(!$attachID || is_wp_error($attachID)) {
      return false;
    }

    $attachURL = wp_get_attachment_url($attachID);
    $fileHash = $this->urlHash($originalUrl);

    update_post_meta($attachID, '_twbb_image_hash', $fileHash);
    update_post_meta($attachID, '_twbb_section_attachment', '1');
    update_post_meta($attachID, '_elementor_source_image_hash', $this->urlHash($attachURL));

    return ['id' => $attachID, 'url' => $attachURL, 'hash' => $fileHash];
  }

  /**
   * Function is partially copied from media_sideload_image() function. It downloads file and returns download file
   * path in tmp folder or false
   */
  private function download($url){
    $allowed_extensions = array('jpg', 'jpeg', 'jpe', 'png', 'gif', 'webp');
    $allowed_extensions = array_map('preg_quote', $allowed_extensions);

    // Set variables for storage, fix file filename for query strings.
    preg_match('/[^\?]+\.(' . implode('|', $allowed_extensions) . ')\b/i', $url, $matches);

    if(!$matches) {
      return false;
    }

    // Download file to temp location.
    $tmp_name = download_url($url);

    // If error storing temporarily, return the error.
    if(is_wp_error($tmp_name)) {
      return false;
    }

    return $tmp_name;
  }

  private function initCash(){
    $args = array(
      'numberposts' => -1,
      'posts_per_page' => -1,
      'post_type' => 'attachment',
      'post_status' => 'inherit',
	  //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
      'meta_query' => array(
        array(
          'key' => '_twbb_section_attachment',
          'value' => '1'
        )
      ),
    );
    $posts = get_posts($args);//phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts

    foreach($posts as $post) {
      $hash = get_post_meta($post->ID, '_twbb_image_hash', true);
      if(empty($hash)) {
        continue;
      }

      $this->addToCache($hash, $post->ID);
    }

  }

  private function addToCache($hash, $id){
    $this->cache[$hash] = ['id' => $id, 'url' => wp_get_attachment_url($id)];
  }

  private function getFromCache($url){
    if(!empty($this->cache[$url])) {
      return $this->cache[$url];
    }

    return null;
  }

  private function urlHash($url){
    return sha1($url);
  }

  private function initCallbacks(){
    $this->callbacks = [
      'twbb_gallery' => [$this, 'uploadGalleryAttachment'],
      'twbb_slides' => [$this, 'uploadSliderAttachment'],
      'twbb-testimonial-carousel' => [$this, 'uploadTestimonialCarouselAttachment'],
      'container' => [$this, 'uploadContainerAttachment'],
      'twbb-team' => [$this, 'uploadTeamAttachment'],
    ];
  }

}