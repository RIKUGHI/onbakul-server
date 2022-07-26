<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Helper {
  public static function sendResponse($code, $error, $data) {
    echo json_encode(array(
      'response_code' => $code,
      'error' => $error,
      'result' => $data
    ));
  }
}
?>