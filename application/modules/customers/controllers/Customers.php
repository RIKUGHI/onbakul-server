<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customers extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('CustomersModel', 'PM');

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

			$customersList = $this->PM->getCustomers($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result();


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

		$customer = $this->isCustomerAvailableById($idOwner, $idCustomer);

		if (!empty($customer)) {
			$this->helper->sendResponse(200, null, $customer[0]);
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any Customer'), null);
		}
	}

	public function create()
	{
		$data['id_owner'] = $this->input->post('id_owner');
		$data['id_outlet'] = $this->input->post('id_outlet');
		$data['customer_name'] = $this->input->post('customer_name');
		$data['city'] = $this->input->post('city');
		$data['address'] = $this->input->post('address');
		$data['telp'] = $this->input->post('telp');

		if ($this->PM->create('customers', $data)) {
			$this->helper->sendResponse(200, null, array('message' => 'Pelanggan berhasil ditambah'));
		} else {
			unlink('assets/img/' . $data['product_img']);
			$this->helper->sendResponse(400, null, array('message' => 'Pelanggan gagal ditambah'));
		}
	}

	public function edit()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
			$idCustomer = $this->uri->segment(3);
			$idOwner = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('id_owner') : $this->input->input_stream('id_owner');
	
			$data['customer_name'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('customer_name') : $this->input->input_stream('customer_name');
			$data['city'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('city') : $this->input->input_stream('city');
			$data['address'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('address') : $this->input->input_stream('address');
			$data['telp'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('telp') : $this->input->input_stream('telp');

			$checkCustomer = $this->isCustomerAvailableById($idOwner, $idCustomer);
	
			if (!empty($checkCustomer)) {
				if ($this->PM->update('customers', $data, array('id_owner' => $idOwner, 'id_customer' => $idCustomer))) {
					$this->helper->sendResponse(200, null, array('message' => 'Pelanggan berhasil diubah'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Pelanggan gagal diubah'));
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any Customer'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function delete()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$idCustomer = $this->uri->segment(3);
			$idOwner = $this->input->get('id_owner');
	
			$checkCustomer = $this->isCustomerAvailableById($idOwner, $idCustomer);
	
			if (!empty($checkCustomer)) {
				if ($this->PM->delete('customers', array('id_owner' => $idOwner, 'id_customer' => $idCustomer))) {
					$this->helper->sendResponse(200, null, array('message' => 'Pelanggan berhasil dihapus'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Pelanggan gagal dihapus'));
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any Customer'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function isCustomerAvailableById($idOwner, $idCustomer)
	{
		return $this->PM->findByQuery('customers', array('id_owner' => $idOwner, 'id_customer' => $idCustomer))->result();
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
