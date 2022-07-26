<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Categories extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('CategoriesModel', 'PM');

    header('Access-Control-Allow-Origin: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}


	public function index()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$dataAmount = 99;
			$idOwner = $this->input->get('id_owner');

			// q is set without page
			if (isset($_GET['q']) && !isset($_GET['page'])) {
				$firstData = 0;
				$activePage = 1;
				$search = true;
				// q and page are set
			} else if (isset($_GET['q']) && isset($_GET['page'])) {
				$activePage = $_GET['page'] <= 0 ? 1 : $_GET['page'];
				$firstData = ($dataAmount * $activePage) - $dataAmount;
				$search = true;
				// q isn't set, page is settable
			} else {
				$activePage = isset($_GET['page']) ? ($_GET['page'] <= 0 ? 1 : $_GET['page']) : 1;
				$firstData = ($dataAmount * $activePage) - $dataAmount;
				$search = false;
			}

			$categoriesList = $this->PM->getCategories($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result();

			$this->helper->sendResponse(200, null, array(
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => (int) $firstData,
				'active_page' => (int) $activePage,
				'total_pages' => (int) $this->PM->getTotalPages($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $dataAmount),
				'results' => empty($categoriesList) ? ($search ? null : [array(
					'id_owner' => 0,
					'id_category' => 0,
					'category_name' => 'Umum'
				)]) : $this->categoriesManagement($categoriesList)
			));
		} else {
			header("HTTP/1.1 400");
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
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
			$idKategori = $this->uri->segment(3);
			$idOwner = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('id_owner') : $this->input->input_stream('id_owner');
	
			$data['category_name'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('category_name') : $this->input->input_stream('category_name');
			
	
			$checkKategori = $this->isCategoriesAvailableById($idOwner, $idKategori);
	
			if (!empty($checkKategori)) {
				// if outlet_name from database == outlet name from user
				if ($checkKategori[0]->category_name == $data['category_name']) {
					if ($this->PM->update('categories', $data, array('id_owner' => $idOwner, 'id_category' => $idKategori))) {
						$this->helper->sendResponse(200, null, array('message' => 'Kategori berhasil diubah'));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Kategori gagal diubah'));
					}
				} else {
					$checkDuplicateName = $this->PM->findByQuery('categories', array(
						'id_owner' => $idOwner,
						'category_name' => $data['category_name']
					))->result();
	
					if (empty($checkDuplicateName)) {
						if ($this->PM->update('categories', $data, array('id_owner' => $idOwner, 'id_category' => $idKategori))) {
							$this->helper->sendResponse(200, null, array('message' => 'Kategori berhasil diubah'));
						} else {
							$this->helper->sendResponse(406, null, array('message' => 'Kategori gagal diubah'));
						}
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Kategori dengan nama '.$data['category_name'].' sudah tersedia'));
					}
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any category'), null);
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

	private function categoriesManagement($data) {
		$resultData = [array(
			'id_owner' => 0,
			'id_category' => 0,
			'category_name' => 'Umum'
		)];

		foreach ($data as $value) {
			$resultData[] = array(
				'id_owner' => (int) $value->id_owner,
				'id_category' => (int) $value->id_category,
				'category_name' => $value->category_name
			);
		}

		return $resultData;
	}
}
