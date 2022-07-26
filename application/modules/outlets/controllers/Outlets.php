<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outlets extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('OutletsModel', 'PM');

    header('Access-Control-Allow-Origin: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}
		

	public function index(){	
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$dataAmount = 50;
			$idOwner = $this->input->get('id_owner');

			// q is set without page
			if (isset($_GET['q']) && !isset($_GET['page'])) {
				$firstData = 0;
				$activePage = 1;
			// q and page are set
			} else if (isset($_GET['q']) && isset($_GET['page'])) {
				$activePage = $_GET['page'] <= 0 ? 1: $_GET['page'];
				$firstData = ($dataAmount * $activePage) - $dataAmount;
			// q isn't set, page is settable
			} else {
				$activePage = isset($_GET['page']) ? ($_GET['page'] <= 0 ? 1 : $_GET['page']) : 1;
				$firstData = ($dataAmount * $activePage) - $dataAmount;
			}

			$outletsList = $this->PM->getOutlets($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result(); 

			$this->helper->sendResponse(200, null, array(
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => (int) $firstData,
				'active_page' => (int) $activePage,
				'total_pages' => (int) $this->PM->getTotalPages($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $dataAmount),
				'results' => empty($outletsList) ? null : $this->outletsManagement($outletsList)
			));
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}	

	public function detail() {
		$idOwner = $this->input->get('id_owner');
		$idOutlet = $this->uri->segment(2);

		$outlet = $this->isOutletAvailableById($idOwner, $idOutlet);

		if (!empty($outlet)) {
			$this->helper->sendResponse(200, null, $outlet[0]);
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any outlet'), null);
		}
	}

	public function create() {
		$data['id_owner'] = $this->input->post('id_owner');
		$data['id_category'] = $this->input->post('id_category');
		$data['loginable'] = 1;
		$data['owner_code'] = $this->input->post('owner_code');
		$data['pin'] = $this->input->post('pin');
		$data['outlet_name'] = $this->input->post('outlet_name');
		$data['city'] = $this->input->post('city');
		$data['address'] = $this->input->post('address');
		$data['telp'] = $this->input->post('telp');
		$data['products_ro'] = $this->input->post('products_ro');
    $data['units_ro'] = $this->input->post('units_ro');
    $data['categories_ro'] = $this->input->post('categories_ro');
    $data['customers_ro'] = $this->input->post('customers_ro');
    $data['suppliers_ro'] = $this->input->post('suppliers_ro');
    $data['outlets_ro'] = $this->input->post('outlets_ro');
    $data['transactions_ro'] = $this->input->post('transactions_ro');
    $data['purchases_ro'] = $this->input->post('purchases_ro');

		if ($this->PM->create('outlets', $data)) {
			$this->helper->sendResponse(200, null, array('message' => 'outlet berhasil ditambah'));
		} else {
			unlink('assets/img/'.$data['product_img']);
			$this->helper->sendResponse(406, null, array('message' => 'outlet gagal ditambah'));
		}
	}

	public function edit() {
		if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
			$idOutlet = $this->uri->segment(3);
			$idOwner = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('id_owner') : $this->input->input_stream('id_owner');
	
			$data['id_category'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('id_category') : $this->input->input_stream('id_category');
			$data['owner_code'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('owner_code') : $this->input->input_stream('owner_code');
			$data['pin'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('pin') : $this->input->input_stream('pin');
			$data['outlet_name'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('outlet_name') : $this->input->input_stream('outlet_name');
			$data['city'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('city') : $this->input->input_stream('city');
			$data['address'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('address') : $this->input->input_stream('address');
			$data['telp'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('telp') : $this->input->input_stream('telp');
			$data['products_ro'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('products_ro') : $this->input->input_stream('products_ro');
			$data['units_ro'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('units_ro') : $this->input->input_stream('units_ro');
			$data['categories_ro'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('categories_ro') : $this->input->input_stream('categories_ro');
			$data['customers_ro'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('customers_ro') : $this->input->input_stream('customers_ro');
			$data['suppliers_ro'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('suppliers_ro') : $this->input->input_stream('suppliers_ro');
			$data['outlets_ro'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('outlets_ro') : $this->input->input_stream('outlets_ro');
			$data['transactions_ro'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('transactions_ro') : $this->input->input_stream('transactions_ro');
			$data['purchases_ro'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('purchases_ro') : $this->input->input_stream('purchases_ro');
	
			$checkOutlet = $this->isOutletAvailableById($idOwner, $idOutlet);
		
			if (!empty($checkOutlet)) {
				if ($this->PM->update('outlets', $data, array('id_owner' => $idOwner, 'id_outlet' => $idOutlet))) {
					$this->helper->sendResponse(200, null, array('message' => 'Outlet berhasil diubah'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Outlet gagal diubah'));
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any outlet'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function delete() {
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$idOutlet = $this->uri->segment(3);
			$idOwner = $this->input->get('id_owner');
	
			$checkOutlet = $this->isOutletAvailableById($idOwner, $idOutlet);
	
			if (!empty($checkOutlet)) {
				if ($this->PM->delete('outlets', array('id_owner' => $idOwner, 'id_outlet' => $idOutlet))) {
					$this->helper->sendResponse(200, null, array('message' => 'Outlet berhasil dihapus'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Outlet gagal dihapus'));
				}
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Bad request. Can not find any outlet'));
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function isOutletAvailableById($idOwner, $idOutlet)	{
		return $this->PM->findByQuery('outlets', array('id_owner' => $idOwner,'id_outlet' => $idOutlet))->result();
	}

	private function updateData($idOwner, $idProduct, $data) {
		if ($this->PM->update('products', $data, array('id_owner' => $idOwner, 'id_product' => $idProduct))) {
			$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate'));
		} else {
			$this->helper->sendResponse(400, null, array('message' => 'Produk gagal diupdate'));
		}
	}

	private function outletsManagement($datas) {
		foreach ($datas as $value) {
			$getCategory = $this->PM->findByQuery('categories', array(
				'id_category' => $value->id_category
			))->result();

			$resultData[] = array(
				'id_owner' => $value->id_owner,
				'id_outlet' => $value->id_outlet,
				'id_category' => count($getCategory) == 0 ? array(
					"id_owner" => $value->id_owner,
					"id_category" => "0",
					"category_name" => "Umum"
				) : $getCategory[0],
				'loginable' => (bool) $value->loginable,
				'owner_code' => $value->owner_code,
				'pin' => $value->pin,
				'outlet_name' => $value->outlet_name,
				'city' => $value->city,
				'address' => $value->address,
				'telp' => $value->telp,
				'products_ro' => $value->products_ro ? true : false,
				'units_ro' => $value->units_ro ? true : false,
				'categories_ro' => $value->categories_ro ? true : false,
				'customers_ro' => $value->customers_ro ? true : false,
				'suppliers_ro' => $value->suppliers_ro ? true : false,
				'outlets_ro' => $value->outlets_ro ? true : false,
				'transactions_ro' => $value->transactions_ro ? true : false,
				'purchases_ro' => $value->purchases_ro ? true : false
			);
		}

		return $resultData;
	}
}
