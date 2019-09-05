<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * File Upload Class
 *
 * @author    Sandeep Kumar <ki.sandeep11@gmail.com>
 */
class File
{

    private $APP;
    private $files;
    public $file_table;

    public function __construct()
    {
        $this->APP = &getInstance();
    }

    public function compress($source, $destination, $quality)
    {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        imagejpeg($image, $destination, $quality);

        return $destination;
    }

    public function init($file)
    {
        $this->files = $file;
    }

    public function upload(array $data)
    {

        //debug(1);
        //debug($data['index']);
        $data['file'] = $this->files;
        $status = true;
        $result = NULL;
        $config = NULL;

        if (empty($data['file'])) {
            $status = false;
            return array('status' => $status, 'data' => "file data is not initialized");
        }

        if ($data['file']) {

            if (isset($data['index'])) {
                if (!empty($data['file'][$data['key']]['name'][$data['index']])) {
                    $_FILES[$data['key']]['name'] = $data['file'][$data['key']]['name'][$data['index']];
                    $_FILES[$data['key']]['type'] = $data['file'][$data['key']]['type'][$data['index']];
                    $_FILES[$data['key']]['tmp_name'] = $data['file'][$data['key']]['tmp_name'][$data['index']];
                    $_FILES[$data['key']]['error'] = $data['file'][$data['key']]['error'][$data['index']];
                    $_FILES[$data['key']]['size'] = $data['file'][$data['key']]['size'][$data['index']];
                } else {

                    return array('status' => false, 'data' => 'file not found');
                }
            } else {
                if (!empty($data['file'][$data['key']]['name'])) {
                    $_FILES[$data['key']]['name'] = $data['file'][$data['key']]['name'];
                    $_FILES[$data['key']]['type'] = $data['file'][$data['key']]['type'];
                    $_FILES[$data['key']]['tmp_name'] = $data['file'][$data['key']]['tmp_name'];
                    $_FILES[$data['key']]['error'] = $data['file'][$data['key']]['error'];
                    $_FILES[$data['key']]['size'] = $data['file'][$data['key']]['size'];
                } else {
                    return array('status' => false, 'data' => 'file not found');
                }
            }

            if (isset($data['upload_path']) && !empty(trim($data['upload_path']))) {
                $config['upload_path'] = $data['upload_path'];
            } else {
                $config['upload_path'] = './data';
            }

            if (isset($data['allowed_types']) && !empty(trim($data['allowed_types']))) {
                $config['allowed_types'] = $data['allowed_types'];
            } else {
                $config['allowed_types'] = 'gif|jpg|png';
            }

            if (isset($data['max_size']) && !empty(trim($data['max_size']))) {
                $config['max_size'] = $data['max_size'];
            } else {
                $config['max_size'] = '2048';
            }

            if (isset($data['max_width']) && !empty(trim($data['max_width']))) {
                $config['max_height'] = $data['max_width'];
            } else {
                // $config['max_width'] = '2048';
            }

            if (isset($data['max_height']) && !empty(trim($data['max_height']))) {
                $config['max_width'] = $data['max_height'];
            } else {
                //$config['max_height'] = '2048';
            }

            if (isset($data['file_name']) && !empty(trim($data['file_name']))) {
                $config['file_name'] = $data['file_name'];
            } else {
                $ext = pathinfo($_FILES[$data['key']]['name'], PATHINFO_EXTENSION);
                $config['file_name'] = "FILE-" . time() . rand(1111, 9999) . "." . $ext;
            }

            $url = (isset($data['remote_upload_url'])) ? $data['remote_upload_url'] : $this->APP->config['remote_upload_url'];

            if ($url && isset($this->APP->config['remote_upload']) && $this->APP->config['remote_upload'] == TRUE) {
                
                $files = $_FILES[$data['key']]['tmp_name'];
                $postfields = array();
                if (function_exists('curl_file_create')) { // For PHP 5.5+
                    $file = curl_file_create($files, $_FILES[$data['key']]['type'], $config['file_name']);
                } else {
                    $file = '@' . realpath($files);
                }

                $postfields['file'] = $file;
                $postfields['upload_path'] = $config['upload_path'];
                $postfields['allowed_types'] = $config['allowed_types'];
                $postfields['max_size'] = $config['max_size'];
                $postfields['max_height'] = @$config['max_height'];
                $postfields['max_width'] = @$config['max_width'];

                $headers = array("Content-Type" => "multipart/form-data");

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                $response = curl_exec($ch);
                if (!curl_errno($ch)) {
                    $info = curl_getinfo($ch);
                    if ($info['http_code'] == 200) {
                        // Files uploaded successfully.
                        $status = TRUE;
                        $result = array('upload_data' => array(
                            'file_name' => $config['file_name']
                        ));
                    }
                } else {
                    // Error happened
                    $error_message = curl_error($ch);
                    $status = FALSE;
                    $result = array('error' => $error_message);
                }
                curl_close($ch);
            } else {
                if (is_dir($config['upload_path']) == false) {
                    // Create directory if it does not exist
                    mkdir($config['upload_path'], 0755);
                }

                /*$this->APP->load->library('upload', $config);
						if (!$this->APP->upload->do_upload($data['key'])) {
						
						$status = FALSE;
						$result = $errors = array('error' => $this->APP->upload->display_errors());
						} else {
						
						$status = TRUE;
						$result = $file_data = array('upload_data' => $this->APP->upload->data());
						if ($result['upload_data']['image_type'] == 'jpeg' || $result['upload_data']['image_type'] == 'png' || $result['upload_data']['image_type'] == 'gif') {
                        $this->compress($result['upload_data']['full_path'], $result['upload_data']['full_path'], get_config()['image_quality']);
						}
						
						}
					$this->APP->upload = NULL; */

                $this->APP->load->library('upload');
                $file_upload = new CI_Upload($config);
                if (!$file_upload->do_upload($data['key'])) {

                    $status = FALSE;
                    $result = $errors = array('error' => $file_upload->display_errors());
                } else {

                    $status = TRUE;
                    $result = $file_data = array('upload_data' => $file_upload->data());
                    if ($result['upload_data']['image_type'] == 'jpeg' || $result['upload_data']['image_type'] == 'png' || $result['upload_data']['image_type'] == 'gif') {
                        $this->compress($result['upload_data']['full_path'], $result['upload_data']['full_path'], get_config()['image_quality']);
                    }
                }
            }
        }
        return array('status' => $status, 'data' => $result);
    }

    public function all_files($path = NULL, $depth = NULL)
    {
        //$this->APP->load->helper('function_helper');

        if ($path) {
            return directory_info($path, $depth);
        } else {
            return directory_info('./data/', 0);
        }
    }
}
