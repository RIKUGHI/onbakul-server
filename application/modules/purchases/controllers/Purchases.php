<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('PurchasesModel', 'PM');
		
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

			$purchaseList = $this->PM->getPurchases($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result(); 

			$this->helper->sendResponse(200, null, array(
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => (int) $firstData,
				'active_page' => (int) $activePage,
				'total_pages' => (int) $this->PM->getTotalPages($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $dataAmount),
				'results' => empty($purchaseList) ? null : $this->purchasesManagement($purchaseList)
			));
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}	

	public function detail() {
		$idOwner = 1;
		$idOutlet = 1;
		$idPurchase = $this->uri->segment(2);

		$outlet = $this->isPurchaseAvailableById($idOwner, $idOutlet, $idPurchase);

		if (!empty($outlet)) {
			$this->helper->sendResponse(200, null, $outlet[0]);
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any purchase'), null);
		}
	}

	public function create() {
		$data['id_owner'] = $this->input->post('id_owner');
		$data['id_outlet'] = $this->input->post('id_outlet');
		$data['status'] = 0;
		$data['id_product'] = $this->input->post('id_product');
		$data['product_name'] = $this->input->post('product_name');
		$data['quantity'] = $this->input->post('quantity');
		$data['price'] = $this->input->post('price');
		$data['id_supplier'] = $this->input->post('id_supplier');
		$data['date'] = date('Y-m-d');
		$data['time'] = date('H:i:s');
		$data['note'] = $this->input->post('note');

		if ($this->PM->create('purchases', $data)) {
			$this->helper->sendResponse(200, null, array('message' => 'Pembelian berhasil ditambah'));
		} else {
			$this->helper->sendResponse(406, null, array('message' => 'Pembelian gagal ditambah'));
		}
	}

	public function edit() {
		if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
			$idOwner = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('id_owner') : $this->input->input_stream('id_owner');
			$idOutlet = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('id_outlet') : $this->input->input_stream('id_outlet');
			$idPurchase = $this->uri->segment(3);
			$data['status'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('status') : $this->input->input_stream('status');
			// $data['product_name'] = $this->input->input_stream('product_name');
			// $data['quantity'] = $this->input->input_stream('quantity');
			// $data['price'] = $this->input->input_stream('price');
			// $data['id_unit'] = $this->input->input_stream('id_unit');
			// $data['id_supplier'] = $this->input->input_stream('id_supplier');
			// $data['date'] = date('Y-m-d');
			// $data['time'] = date('H:i:s');
			// $data['note'] = $this->input->input_stream('note');
			$getPurchase = $this->isPurchaseAvailableById($idOwner, $idOutlet, $idPurchase);
	
			if (!empty($getPurchase)) {
				if ($this->PM->update('purchases', $data, array('id_owner' => $idOwner, 'id_outlet' => $idOutlet, 'id_purchase' => $idPurchase))) {
					$getProduct = $this->PM->findByQuery('products', array(
						'id_product' => $getPurchase[0]->id_product
					))->result();

					$this->PM->update('products', array(
						'stock_quantity' => (int) $getPurchase[0]->quantity + (int) $getProduct[0]->stock_quantity
					), array(
						'id_product' => $getProduct[0]->id_product
					));

					$this->helper->sendResponse(200, null, array('message' => 'Pembelian berhasil diubah'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Pembelian gagal diubah'));
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any purchase'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function delete() {
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$idOwner = $this->input->get('id_owner');
			$idOutlet = $this->input->get('id_outlet');
			$idPurchase = $this->uri->segment(3);
	
			if (!empty($this->isPurchaseAvailableById($idOwner, $idOutlet, $idPurchase))) {
				if ($this->PM->delete('purchases', array('id_owner' => $idOwner, 'id_outlet' => $idOutlet, 'id_purchase' => $idPurchase))) {
					$this->helper->sendResponse(200, null, array('message' => 'Pembelian berhasil dihapus'));
				} else {
					$this->helper->sendResponse(400, null, array('message' => 'Pembelian gagal dihapus'));
				}
			} else {
				$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any purchase'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function isPurchaseAvailableById($idOwner, $idOutlet, $idPurchase)	{
		return $this->PM->findByQuery('purchases', array('id_owner' => $idOwner,'id_outlet' => $idOutlet, 'id_purchase' => $idPurchase))->result();
	}

	private function purchasesManagement($datas) {
		foreach ($datas as $value) {
			$getProduct = $this->PM->findByQuery('products', array(
																												'id_owner' => $value->id_owner,
																												'id_product' => $value->id_product
																											))->result();
			$getSupplier = $this->PM->findByQuery('suppliers', array(
																													'id_owner' => $value->id_owner,
																													'id_supplier' => $value->id_supplier
																												))->result();
			$management[] = array(
				"id_owner" => (int) $value->id_owner,
				"id_outlet" => (int) $value->id_outlet,
				"id_purchase" => (int) $value->id_purchase,
				"status" => $value->status ? true : false,
				"id_product" => empty($getProduct) ? null : array(
					"id_owner" => (int) $getProduct[0]->id_owner,
					"id_product" => (int) $getProduct[0]->id_product,
					"product_img" => $getProduct[0]->product_img,
					"product_name" => $getProduct[0]->product_name,
					"barcode" => $getProduct[0]->barcode,
					"id_category" => (int) $getProduct[0]->id_category,
					"capital_price" => (int) $getProduct[0]->capital_price,
					"selling_price" => (int) $getProduct[0]->selling_price,
					"available_stock" => $getProduct[0]->available_stock ? true : false, 
					"id_unit" => sizeof($this->PM->findByQuery('units', array(
						'id_owner' => $getProduct[0]->id_owner,
						'id_unit' => $getProduct[0]->id_unit
					))->result()) == 0 ? array(
						'id_owner' => 0,
						'id_unit' => 0,
						'unit_name' => ''
					) : $this->PM->findByQuery('units', array(
						'id_owner' => $getProduct[0]->id_owner,
						'id_unit' => $getProduct[0]->id_unit
					))->result()[0],
					"stock_quantity" => (int) $getProduct[0]->stock_quantity,
					"stock_min" => (int) $getProduct[0]->stock_min
				),
				"product_name" => $value->product_name,
				"quantity" => $value->quantity,
				"price" => (int) $value->price,
				"id_supplier" => empty($getSupplier) ? null : $getSupplier[0],
				"date" => $value->date,
				"time" => $value->time,
				"note" => $value->note
			);
		}

		return $management;
	}
}
