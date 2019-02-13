<?php

class PincUploadManager {
  /**
   * The processed files
   */
  protected $files;

  /**
   * Validator info
   */
  protected $validator;

  /**
   * The response of the upload process
   */
  protected $response;

  /**
   * An array of attachment ids
   */
  protected $attachments = [];

  /**
   * An array of thumbnails of the uploaded files
   */
  protected $thumbnails = [];

  /**
   *
   */
  protected $data;

  /**
   * The post ID
   */
  protected $post_id = false;

  /**
   * Sets the files for processing
   */
  public function addValidator($key, $data) {
    $this->validator[$key] = $data;
  }

  /**
   * Sets the files for processing
   */
  public function setFiles($files) {
    $this->files = $files;
    $this->initData();
    $this->response = $this->data;
  }

  /**
   * Executes the upload process
   */
  public function execute() {
    foreach ($this->data as $key => $data) {
      $file = array(
        'name' => $data['safe_filename'],
        'type' => $data['mime'],
        'tmp_name' => $data['tmp_name'],
        'error' => $data['error'],
        'size' => $data['size'],
      );

      $_FILES = array('pin_upload_file' => $file);
      foreach ($_FILES as $files_key => $array) {
        $attach_id = media_handle_upload($files_key, $this->post_id);

        if (is_wp_error($attach_id)) {
          @unlink($data['tmp_name']);
          echo 'error';
          die();
        }

        update_post_meta($attach_id, 'pinc_unattached', 'yes');
        $this->attachments[] = $attach_id;

        $attachment_meta = wp_get_attachment_metadata($attach_id, true);
        if (preg_match('/video\/.*/', $attachment_meta['mime_type'])) {
          $url = wp_get_attachment_url($attach_id);
          $attachment_meta['file'] = $url;



          $this->response['video'][] = array(
            'meta' => $attachment_meta,
            'id' => $attach_id,
            'code' => do_shortcode('[video src="' . $url . '"]')
          );
        } else {
          $image = wp_get_attachment_image_src($attach_id, 'full');
          $this->thumbnails[] = $image;

          $this->response['thumbnails'][] = array(
            'thumbnail' => $image[0],
            'id' => $attach_id,
          );
        }
      }

    }
  }

  /**
   * Sets the post id for the uploaded files
   */
  public function setPostID($post_id) {
    $this->post_id = $post_id;
  }

  /**
   * Executes the upload process
   */
  public function getAttachments() {
    return $this->attachments;
  }

  /**
   * Returns the response in JSON format
   */
  public function json() {
    return json_encode($this->response);
  }

  /**
   * Returns the extension of a file
   */
  protected function getFileExtension($filename, $with_dot = false) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return $with_dot ? '.' . $ext : $ext;
  }

  /**
   * Returns a safe name for the uploaded file
   */
  protected function getSafeFilename($filename) {
    $ext = $this->getFileExtension($filename, true);
    $shuffle = time() . str_shuffle('pcl48');
    $original_filename = preg_replace('/[^(\x20|\x61-\x7A)]*/', '', strtolower(str_ireplace($ext, '', $filename)));
    return strtolower(substr($original_filename, 0, 100)) . '-' . $shuffle . $ext;
  }

  /**
   * Initializes a more detailed information array for the uploaded files
   */
  protected function initData() {
    foreach ($this->files['tmp_name'] as $key => $file) {
      $imageinfo = getimagesize($file);

      $extension = $this->getFileExtension($this->files['name'][$key]);
      $name = str_replace('.' . $extension, '', $this->files['name'][$key]);
      $mime_info = explode('/', $imageinfo['mime']);

      $this->data[$key] = [
        'filename'  => $this->files['name'][$key],
        'safe_filename' => $this->getSafeFilename($this->files['name'][$key]),
        'name' => $name,
        'ext' => $extension,
        'tmp_name' => $file,
        'width' => @$imageinfo[0],
        'height' => @$imageinfo[1],
        'type' => isset($mime_info[0]) ? $mime_info[0] : false,
        'mime' => @$imageinfo['mime'],
        'file_key' => $key,
        'size' => $this->files['size'][$key],
        'error' => $this->files['error'][$key]
      ];

      $this->validateFile($this->data[$key]);
    }

  }

  /**
   * Runs the added validators towards the files.
   */
  protected function validateFile(&$file) {

    $file['valid'] = true;

    //Validate file types
    if (!empty($this->validator['file_types'])
      && !array_intersect(array($file['mime']), $this->validator['file_types'])) {
      $file['valid'] = false;
      $file['error_msg']['file_type'] = 'File type not valid: ' . $file['mime'];
    }

    //Validate image min width
    if ($file['type'] == 'image'
      && !empty($this->validator['width'])
      && $file['width'] < $this->validator['width']['min']) {
      $file['valid'] = false;
      $file['error_msg']['min_width'] = 'File width less than allowed: ' . $this->validator['width']['min'] . ' (' . $file['width'] . ')';
    }

    //Validate image max width
    if ($file['type'] == 'image'
      && !empty($this->validator['width'])
      && $file['width'] > $this->validator['width']['max']) {
      $file['valid'] = false;
      $file['error_msg']['max_width'] = 'File width more than allowed: ' . $this->validator['width']['max'] . ' (' . $file['width'] . ')';
    }

    //Validate image min height
    if ($file['type'] == 'image'
      && !empty($this->validator['height'])
      && $file['height'] < $this->validator['height']['min']) {
      $file['valid'] = false;
      $file['error_msg']['min_height'] = 'File height less than allowed: ' . $this->validator['height']['min'] . ' (' . $file['height'] . ')';
    }

    //Validate image max height
    if ($file['type'] == 'image'
      && !empty($this->validator['height'])
      && $file['height'] > $this->validator['height']['max']) {
      $file['valid'] = false;
      $file['error_msg']['max_height'] = 'File width more than allowed: ' . $this->validator['height']['max'] . ' (' . $file['height'] . ')';
    }
  }
}

