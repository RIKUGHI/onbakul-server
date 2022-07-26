<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suppliers extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('SuppliersModel', 'PM');

    header('Access-Control-Allow-Origin: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}


	public function index()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$dataAmount = 50;
			$idOwner = $this->input->get('id_owner');

			// q is set without page
			if (isset($_GET['q']) && !isset($_GET['page'])) {
				$firstData = 0;
				$activePage = 1;
				// q and page are set
			} else if (isset($_GET['q']) && isset($_GET['page'])) {
				$activePage = $_GET['page'] <= 0 ? 1 : $_GET['page'];
				$firstData = ($dataAmount * $activePage) - $dataAmount;
				// q isn't set, page is settable
			} else {
				$activePage = isset($_GET['page']) ? ($_GET['page'] <= 0 ? 1 : $_GET['page']) : 1;
				$firstData = ($dataAmount * $activePage) - $dataAmount;
			}

			$customersList = $this->PM->getSuupliers($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result();


			$this->helper->sendResponse(200, null, array(
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => (int) $firstData,
				'active_page' => (int) $activePage,
				'total_pages' => (int) $this->PM->getTotalPages($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $dataAmount),
				'results' => empty($customersList) ? null : $customersList
			));
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function detail()
	{
		$idOwner = $this->input->get('id_owner');
		$idCustomer = $this->uri->segment(2);

		$customer = $this->isSupplierAvailableById($idOwner, $idCustomer);

		if (!empty($customer)) {
			$this->helper->sendResponse(200, null, $customer[0]);
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any Supplier'), null);
		}
	}

	public function create()
	{
		$data['id_owner'] = $this->input->post('id_owner');
		$data['id_outlet'] = $this->input->post('id_outlet');
		$data['supplier_name'] = $this->input->post('supplier_name');
		$data['city'] = $this->input->post('city');
		$data['address'] = $this->input->post('address');
		$data['telp'] = $this->input->post('telp');

		if ($this->PM->create('suppliers', $data)) {
			$this->helper->sendResponse(200, null, array('message' => 'Supplier berhasil ditambah'));
		} else {
			unlink('assets/img/' . $data['product_img']);
			$this->helper->sendResponse(406, null, array('message' => 'Supplier gagal ditambah'));
		}
	}

	public function edit()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
			$idSupplier = $this->uri->segment(3);
			$idOwner = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('id_owner') : $this->input->input_stream('id_owner');
	
			$data['supplier_name'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('supplier_name') : $this->input->input_stream('supplier_name');
			$data['city'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('city') : $this->input->input_stream('city');
			$data['address'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('address') : $this->input->input_stream('address');
			$data['telp'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('telp') : $this->input->input_stream('telp');
	
			$checkCustomer = $this->isSupplierAvailableById($idOwner, $idSupplier);
	
			if (!empty($checkCustomer)) {
				if ($this->PM->update('suppliers', $data, array('id_owner' => $idOwner, 'id_supplier' => $idSupplier))) {
					$this->helper->sendResponse(200, null, array('message' => 'Supplier berhasil diubah'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Supplier gagal diubah'));
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any Supplier'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function delete()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$idSupplier = $this->uri->segment(3);
			$idOwner = $this->input->get('id_owner');
	
			$checkSupplier = $this->isSupplierAvailableById($idOwner, $idSupplier);
	
			if (!empty($checkSupplier)) {
				if ($this->PM->delete('suppliers', array('id_owner' => $idOwner, 'id_supplier' => $idSupplier))) {
					$this->helper->sendResponse(200, null, array('message' => 'Supplier berhasil dihapus'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Supplier gagal dihapus'));
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any Supplier'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function isSupplierAvailableById($idOwner, $idSupplier)
	{
		return $this->PM->findByQuery('suppliers', array('id_owner' => $idOwner, 'id_supplier' => $idSupplier))->result();
	}

	private function updateData($idOwner, $idProduct, $data)
	{
		if ($this->PM->update('products', $data, array('id_owner' => $idOwner, 'id_product' => $idProduct))) {
			$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate'));
		} else {
			$this->helper->sendResponse(400, null, array('message' => 'Produk gagal diupdate'));
		}
	}
}
