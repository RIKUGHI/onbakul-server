<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accounts extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('AccountsModel', 'PM');

    header('Access-Control-Allow-Origin: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}


	public function index()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$dataAmount = 50;

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

			$accountList = $this->PM->getAccounts($this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result();
		

			$this->helper->sendResponse(200, null, array(
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => (int) $firstData,
				'active_page' => (int) $activePage,
				'total_pages' => (int) $this->PM->getTotalPages($this->input->get('q') == null ? '' : $this->input->get('q'), $dataAmount),
				'results' => empty($accountList) ? null : $this->accountManagement($accountList)
			));
		} else {
			header("HTTP/1.1 400");
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function detail() {
		$idOwner = $this->uri->segment(2);
	
		$getOwner = $this->PM->findByQuery('owners', array(
			'id_owner' => $idOwner
		))->result();
		
		if (empty($getOwner)) {
			$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any owner'), null);
		} else {
			$getOutlets = $this->PM->findByQuery('outlets', array(
				'owner_code' => $getOwner[0]->owner_code
			))->result();
			
			$this->helper->sendResponse(200, null, array(
				"id_owner" => (int) $getOwner[0]->id_owner,
				"created_at" => $getOwner[0]->created_at,
				"business_name" => $getOwner[0]->business_name,
				"owner_name" => $getOwner[0]->owner_name,
				"owner_code" => $getOwner[0]->owner_code,
				"telp" => $getOwner[0]->telp,
				"email" => $getOwner[0]->email,
				"today" => date('Y-m-d'),
				"is_pro" => $getOwner[0]->is_pro ? ((int) $getOwner[0]->is_pro ? true : false) : false,
				"start" => $getOwner[0]->start,
				"end" => $getOwner[0]->end,
				"outlets" => empty($getOutlets) ? null : $this->outletManagement($getOutlets)
			));
		}
		
	}

	public function detailadmin() {
		$idAdmin = $this->uri->segment(3);

		$isAvailableAdmin = $this->PM->findByQuery('admins', array(
			'id_admin' => $idAdmin
		))->result();

		if (empty($isAvailableAdmin)) {
			$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any admin'), null);
		} else {
			$this->helper->sendResponse(200, null, $isAvailableAdmin);
		}
		
	}

	public function dashboard() {
		$getNewUsers = $this->PM->getNewUsers()->result();
		$this->helper->sendResponse(200, null, array(
			'total_users' => $this->PM->getTotalUsers()->num_rows(),
			'new_users' => empty($getNewUsers) ? [] : $this->usersManagement($getNewUsers)
		));
	}

	public function create()
	{
		$data['id_owner'] = $this->input->post('id_owner');
		$data['category_name'] = $this->input->post('category_name');

		// check duplicate outlet name
		if (empty($this->PM->findByQuery('categories', array('id_owner' => $data['id_owner'], 'category_name' => $data['category_name']))->result())) {
			if ($this->PM->create('categories', $data)) {
				$this->helper->sendResponse(200, null, array('message' => 'Kategori berhasil ditambah'));
			} else {
				unlink('assets/img/' . $data['product_img']);
				$this->helper->sendResponse(406, null, array('message' => 'Kategori gagal ditambah'));
			}
		} else {
			$this->helper->sendResponse(406, null, array('message' => 'Kategori dengan nama '.$data['category_name'].' sudah tersedia'));
		}
	}

	public function edit()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
			$idOwner = $this->uri->segment(3);
	
			$data['owner_name'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('owner_name') : $this->input->input_stream('owner_name');
			$data['business_name'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('business_name') : $this->input->input_stream('business_name');
			$data['telp'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('telp') : $this->input->input_stream('telp');
			$data['email'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('email') : $this->input->input_stream('email');
			$password = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('password') : $this->input->input_stream('password');
	
			$checkAccount = $this->PM->findByQuery('owners', array(
				'email' => $data['email']
			))->result();

			if (!empty($checkAccount)) {
				if ((int) $idOwner == (int) $checkAccount[0]->id_owner) {
					if (empty($password)) {
						if ($this->PM->update('owners', $data, array('id_owner' => $idOwner))) {
							$this->helper->sendResponse(200, null, array('message' => 'Akun berhasil diubah'));
						} else {
							$this->helper->sendResponse(406, null, array('message' => 'Akun berhasil diubah'));
						}
					} else {
						$data['password'] = password_hash($password, PASSWORD_BCRYPT);
	
						if ($this->PM->update('owners', $data, array('id_owner' => $idOwner))) {
							$this->helper->sendResponse(200, null, array('message' => 'Akun berhasil diubah'));
						} else {
							$this->helper->sendResponse(406, null, array('message' => 'Akun berhasil diubah'));
						}
					}
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Email sudah terdaftar'));
				}
			} else {
				if (empty($password)) {
					if ($this->PM->update('owners', $data, array('id_owner' => $idOwner))) {
						$this->helper->sendResponse(200, null, array('message' => 'Akun berhasil diubah'));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Akun berhasil diubah'));
					}
				} else {
					$data['password'] = password_hash($password, PASSWORD_BCRYPT);

					if ($this->PM->update('owners', $data, array('id_owner' => $idOwner))) {
						$this->helper->sendResponse(200, null, array('message' => 'Akun berhasil diubah'));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Akun berhasil diubah'));
					}
				}
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function editadmin() {
		if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
			$idAdmin = $this->uri->segment(3);
			$data['name'] = $this->input->input_stream('name');
			$data['email'] = $this->input->input_stream('email');
			$password = $this->input->input_stream('password');

			$checkAdmin = $this->PM->findByQuery('admins', array(
				'id_admin' => $idAdmin
			))->result();

			if (empty($checkAdmin)) {
				$this->helper->sendResponse(406, null, array('message' => 'Bad request. Can not find any admin'));
			} else {
				if (empty($password)) {
					if ($this->PM->update('admins', $data, array('id_admin' => $idAdmin))) {
						$this->helper->sendResponse(200, null, array('message' => 'Admin berhasil diubah'));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Admin berhasil diubah'));
					}
				} else {
					$data['password'] = password_hash($password, PASSWORD_BCRYPT);
					
					if ($this->PM->update('admins', $data, array('id_admin' => $idAdmin))) {
						$this->helper->sendResponse(200, null, array('message' => 'Admin berhasil diubah'));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Admin berhasil diubah'));
					}
				}	
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function delete()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$idKategori = $this->uri->segment(3);
			$idOwner = $this->input->get('id_owner');
	
			$checkOutlet = $this->isCategoriesAvailableById($idOwner, $idKategori);
	
			if (!empty($checkOutlet)) {
				if ($this->PM->delete('categories', array('id_owner' => $idOwner, 'id_category' => $idKategori))) {
					$this->helper->sendResponse(200, null, array('message' => 'Kategori berhasil dihapus'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Kategori gagal dihapus'));
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any category'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function isCategoriesAvailableById($idOwner, $idKategori)
	{
		return $this->PM->findByQuery('categories', array('id_owner' => $idOwner, 'id_category' => $idKategori))->result();
	}

	private function updateData($idOwner, $idProduct, $data)
	{
		if ($this->PM->update('products', $data, array('id_owner' => $idOwner, 'id_product' => $idProduct))) {
			$this->helper->sendResponse(200, null, array('message' => 'Kategori berhasil diupdate'));
		} else {
			$this->helper->sendResponse(400, null, array('message' => 'Kategori gagal diupdate'));
		}
	}

	private function accountManagement($data) {
		foreach ($data as $value) {
			$getOutlets = $this->PM->findByQuery('outlets', array(
				'owner_code' => $value->owner_code
			))->result();

			$resultData[] = array(
				"id_owner" => (int) $value->id_owner,
				"created_at" => $value->created_at,
				"business_name" => $value->business_name,
				"owner_name" => $value->owner_name,
				"owner_code" => $value->owner_code,
				"telp" => $value->telp,
				"email" => $value->email,
				"today" => date('Y-m-d'),
				"is_pro" => $value->is_pro ? ((int) $value->is_pro ? true : false) : false,
				"start" => $value->start,
				"end" => $value->end,
				"total_products" => $this->PM->getTotalProducts($value->id_owner)->num_rows(),
				"outlets" => empty($getOutlets) ? null : $this->outletManagement($getOutlets)
			);
		}

		return $resultData;
	}

	private function outletManagement($data) {
		foreach ($data as $key => $value) {
			$resultData[] = array(
				"id_owner" => (int) $value->id_owner,
				"id_outlet" => (int) $value->id_outlet,
				"owner_code" => $value->owner_code,
				"outlet_name" => $value->outlet_name,
				"city" => $value->city,
				"address" => $value->address,
				"telp" => $value->telp,
			);
		}

		return $resultData;
	}

	private function usersManagement($data) {
		foreach ($data as $key => $value) {
			$resultData[] = array(
				"created_at" => $value->created_at,
				"total" => (int) $value->total
			);
		}

		return $resultData;
	}
	public function keuntungan(){
		$getUntung = $this->PM->getUntung();
		$this->helper->sendResponse(200, null, array(
			'paid_off' => $this->PM->getUntung($getUntung)->result()
		));
	}
}
