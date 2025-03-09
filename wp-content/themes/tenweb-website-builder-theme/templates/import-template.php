<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 11/13/18
 * Time: 11:18 AM
 */

namespace TWBT;


class ImportTemplate {

  private $file_path;
  private $upload_path;
  private $errors = [];
  private $export_data = [];

  public function __construct($file_path){
    $this->file_path = $file_path;
    $this->upload_path = wp_upload_dir()['basedir'] . '/twbb-import/' . uniqid() . '/';
  }

  public function import(){

    if($this->upload_file() === false) {
      return false;
    }


    $this->create_thumbnails();

    return true;
  }

  private function upload_file(){
    //    if(mkdir($this->upload_path, 0777, true) === false) {
    //      $this->add_error('mkdir', "Failed to create dir " . $this->upload_path);
    //      return false;
    //    }
    //
    //    $content = file_get_contents($this->file_path);
    //    if($content === false) {
    //      $this->add_error('read_file', 'Failed to read file.');
    //      return false;
    //    }
    //
    //    file_put_contents($this->upload_path . 'template.zip', $content);
    $this->upload_path = "/var/www/builder.loc/wp-content/uploads/twbb-import/5bed194d39189/";
    return true;
    $zip = new \ZipArchive();
    $res = $zip->open($this->upload_path . 'template.zip');

    if($res === false) {
      $this->add_error('failed_to_open_zip', 'Failed to open zip.');
      return false;
    }

    $zip->extractTo($this->upload_path);
    $zip->close();

    if($this->check_files() === false) {
      return false;
    }

    return true;
  }

  private function create_thumbnails(){
    $this->upload_images();
  }

  private function upload_images(){
    $images = json_decode(file_get_contents($this->upload_path . 'images.json'));
    echo '<pre>';
    var_dump($images);
    die;
  }

  private function check_files(){
    $export_json = $this->upload_path . 'export.json';
    $images_json = $this->upload_path . 'images.json';
    $terms_json = $this->upload_path . 'terms.json';

    if(!is_file($export_json) || !is_file($images_json) || !is_file($terms_json)) {
      $this->add_error('file_not_exists', 'File not exists.');
      return false;
    }

    $this->export_data = json_decode(file_get_contents($export_json));
    if($this->export_data === null) {
      $this->add_error('export_failed_to_parse_json', 'Failed to parse export.json');
      return false;
    }

    foreach($this->export_data->pt_files as $pt_file) {
      if(!is_file($this->upload_path . $pt_file)) {
        $this->add_error('pt_file_not_exists', 'File not exists.');
        return false;
      }
    }

    return true;
  }

  private function add_error($key, $msg){
    var_dump($key);
    $this->errors[$key] = $msg;
  }

  public function get_errors(){
    return $this->errors;
  }

}