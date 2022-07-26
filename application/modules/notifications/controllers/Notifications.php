<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('NotificationsModel', 'PM');

    header('Access-Control-Allow-Origin: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}
		

	public function index(){	
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$idOwner = $this->input->get('id_owner');

			$productList = $this->PM->getProducts($idOwner)->result(); 

			$this->helper->sendResponse(200, null, array(
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => 0,
				'active_page' => 0,
				'total_pages' => 0,
				'results' => empty($productList) ? null : $this->productManagement($productList)
			));
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}	

	private function productManagement($data) {
		foreach ($data as $key => $value) {
			$resultData[] = array(
				"id_owner" => (int) $value->id_owner,
				"id_product" => (int) $value->id_product,
				"product_name" => $value->product_name,
				"stock_quantity" => (int) $value->stock_quantity,
			);
		}

		return $resultData;
	}
}
