<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('AuthModel', 'AM');
		
    header('Access-Control-Allow-Origin: *');		
    header('Access-Control-Allow-Headers: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}
		

	public function index(){	
		$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
	}

	public function checkemail() {
		$email = $this->input->get('email');

		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			if (empty($this->AM->checkDuplicateByEmail($email))) {
				$this->helper->sendResponse(200, null, array('message' => 'Email tersedia'));
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Email sudah terdaftar'));
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function login() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$level = $this->input->post('level');
			$email = $this->input->post('email');
			$ownerCode = $this->input->post('owner_code');
			$password = $this->input->post('password');

			if ($level == 0) {
				$isAvailableEmail = $this->AM->findByQuery('owners', array(
					'email' => $email
				))->result();

				if (empty($isAvailableEmail)) {
					$this->helper->sendResponse(406, null, array('message' => 'Email belum terdaftar'));
				} else {
					if (password_verify($password, $isAvailableEmail[0]->password)) {
						$outlet = $this->AM->findByQuery('outlets', array(
							'owner_code' => $isAvailableEmail[0]->owner_code,
							'outlet_name' => 'Pusat'
						))->result();

						$this->helper->sendResponse(200, null, array(
							'message' => 'Berhasil login',
							'level' => 0,
							'id_owner' => (int) $isAvailableEmail[0]->id_owner,
							'owner_name' => $isAvailableEmail[0]->owner_name,
							'business_name' => $isAvailableEmail[0]->business_name,
							'id_category' => 0,
							'id_outlet' => $outlet[0]->id_outlet,
							'outlet_name' => $outlet[0]->outlet_name,
							'owner_code' => $isAvailableEmail[0]->owner_code,
							'products_ro' => 0,
							'units_ro' => 0,
							'categories_ro' => 0,
							'customers_ro' => 0,
							'suppliers_ro' => 0,
							'outlets_ro' => 0,
							'transactions_ro' => 0,
							'purchases_ro' => 0
						));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Password salah'));
					}
				}
			} else {
				$isAvailableOutlet = $this->AM->findByQuery('outlets', array(
					'owner_code' => $ownerCode,
					'loginable' => 1,
					'pin' => $password
				))->result();

				if (empty($isAvailableOutlet)) {
					$this->helper->sendResponse(406, null, array('message' => 'Outlet belum terdaftar atau password salah'));
				} else {
					$owner = $this->AM->findByQuery('owners', array(
						'owner_code' => $isAvailableOutlet[0]->owner_code
					))->result();

					$this->helper->sendResponse(200, null, array(
						'message' => 'Berhasil login',
						'level' => 1,
						'id_owner' => (int) $owner[0]->id_owner,
						'owner_name' => $owner[0]->owner_name,
						'business_name' => $owner[0]->business_name,
						'id_category' => $isAvailableOutlet[0]->id_category,
						'id_outlet' => $isAvailableOutlet[0]->id_outlet,
						'outlet_name' => $isAvailableOutlet[0]->outlet_name,
						'owner_code' => $isAvailableOutlet[0]->owner_code,
						'products_ro' => (int) $isAvailableOutlet[0]->products_ro ,
						'units_ro' => (int) $isAvailableOutlet[0]->units_ro,
						'categories_ro' => (int) $isAvailableOutlet[0]->categories_ro,
						'customers_ro' => (int) $isAvailableOutlet[0]->customers_ro,
						'suppliers_ro' => (int) $isAvailableOutlet[0]->suppliers_ro,
						'outlets_ro' => (int) $isAvailableOutlet[0]->outlets_ro,
						'transactions_ro' => (int) $isAvailableOutlet[0]->transactions_ro,
						'purchases_ro' => (int) $isAvailableOutlet[0]->purchases_ro
					));
				}
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function signup() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data['created_at'] = date('Y-m-d');
			$data['business_name'] = $this->input->post('business_name');
			$data['owner_name'] = $this->input->post('owner_name');

			$split = explode(' ', $data['business_name']);
			$code = count($split) >= 2 ? strtoupper($split[0][0].$split[1][0]) : strtoupper($split[0][0].$split[0][1]);

			$data['owner_code'] = "OB".$code."-".$this->AM->getMaxIdOwner();
			$data['telp'] = 0;
			$data['email'] = $this->input->post('email');
			$data['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
			
			$getIdOwner = explode('-', $data['owner_code']);

			if ($this->AM->create('owners', $data)) {
				$outlet['id_owner'] = (int) $getIdOwner[1];
				$outlet['loginable'] = 0; 
				$outlet['owner_code'] = $data['owner_code']; 
				$outlet['pin'] = 0; 
				$outlet['outlet_name'] = 'Pusat'; 
				$outlet['city'] = ''; 
				$outlet['address'] = ''; 
				$outlet['telp'] = 0; 
				$outlet['products_ro'] = 0; 
				$outlet['units_ro'] = 0; 
				$outlet['categories_ro'] = 0; 
				$outlet['customers_ro'] = 0; 
				$outlet['suppliers_ro'] = 0; 
				$outlet['outlets_ro'] = 0; 
				$outlet['transactions_ro'] = 0; 
				$outlet['purchases_ro'] = 0; 

				$this->AM->create('outlets', $outlet);

				$getItOutlet = $this->AM->findByQuery('outlets', array(
					'owner_code' => $outlet['owner_code']
				))->result();

				$this->helper->sendResponse(200, null, array(
					'message' => 'Akun berhasil dibuat',
					'id_owner' => (int) $getIdOwner[1],
					'owner_name' => $data['owner_name'],
					'business_name'=> $data['business_name'],
					'id_outlet' => empty($getItOutlet) ? 0 : $getItOutlet[0]->id_outlet,
					'outlet_name' => $outlet['outlet_name'],
					'owner_code' => $outlet['owner_code']
				));
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Akun gagal dibuat'));
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function loginadmin() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data['email'] = $this->input->post('email');
			$data['password'] = $this->input->post('password');

			$isAvailableEmail = $this->AM->findByQuery('admins', array(
				'email' => $data['email']
			))->result();

			if (empty($isAvailableEmail)) {
				$this->helper->sendResponse(406, null, array('message' => 'Email belum terdaftar'));
			} else {
				if (password_verify($data['password'], $isAvailableEmail[0]->password)) {
					$this->helper->sendResponse(200, null, array(
						'message' => 'Berhasil login',
						'name' => $isAvailableEmail[0]->name,
						'id_admin' => $isAvailableEmail[0]->id_admin
					));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Password salah'));
				}
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function signupadmin() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data['name'] = $this->input->post('name');
			$data['email'] = $this->input->post('email');
			$data['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);

			$isAvailableEmail = $this->AM->findByQuery('admins', array(
				'email' => $data['email']
			))->result();

			if (empty($isAvailableEmail)) {
				if ($this->AM->create('admins', $data)) {
					$this->helper->sendResponse(200, null, array(
						'message' => 'Akun berhasil dibuat',
						'name' => $data['name'],
						'id_admin' => $this->AM->getMaxIdAdmin()[0]->max_id
					));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Akun gagal dibuat'));
				}
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Email tersebut sudah digunakan'));
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}
}
