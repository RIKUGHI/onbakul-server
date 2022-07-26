<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PrintOn extends CI_Controller {
	function __construct(){
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");

    // header('Access-Control-Allow-Origin: *');		
		// header('Content-Type: application/json; charset=utf8');
		// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}
		

	public function index(){	
		$this->load->view('PrintOn');
	}	
}
