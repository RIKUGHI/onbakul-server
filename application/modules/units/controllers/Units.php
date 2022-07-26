<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Units extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('UnitsModel', 'PM');
		
    header('Access-Control-Allow-Origin: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}


	public function index()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$dataAmount = 100;
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

			$unitsList = $this->PM->getUnits($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result();

			$this->helper->sendResponse(200, null, array(
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => (int) $firstData,
				'active_page' => (int) $activePage,
				'total_pages' => (int) $this->PM->getTotalPages($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $dataAmount),
				'results' => empty($unitsList) ? null : $unitsList
			));
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function create()
	{
		$data['id_owner'] = $this->input->post('id_owner');
		$data['unit_name'] = $this->input->post('unit_name');

		// check duplicate outlet name
		if (empty($this->PM->findByQuery('units', array('id_owner' => $data['id_owner'], 'unit_name' => $data['unit_name']))->result())) {
			if ($this->PM->create('units', $data)) {
				$this->helper->sendResponse(200, null, array('message' => 'Satuan berhasil ditambah'));
			} else {
				unlink('assets/img/' . $data['product_img']);
				$this->helper->sendResponse(406, null, array('message' => 'Satuan gagal ditambah'));
			}
		} else {
			$this->helper->sendResponse(406, null, array('message' => 'Satuan dengan nama '.$data['unit_name'].' sudah tersedia'));
		}
	}

	public function edit()
	{
		// post for android
		if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'POST') {
			$idUnit = $this->uri->segment(3);
			$idOwner = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('id_owner') :$this->input->input_stream('id_owner');

			$data['unit_name'] = $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->input->post('unit_name') : $this->input->input_stream('unit_name');

			$checkUnit = $this->isUnitAvailableById($idOwner, $idUnit);
	
			if (!empty($checkUnit)) {
				// if outlet_name from database == outlet name from user
				if ($checkUnit[0]->unit_name == $data['unit_name']) {
					if ($this->PM->update('units', $data, array('id_owner' => $idOwner, 'id_unit' => $idUnit))) {
						$this->helper->sendResponse(200, null, array('message' => 'Satuan berhasil diubah'));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Satuan gagal diubah'));
					}
				} else {
					$checkDuplicateName = $this->PM->findByQuery('units', array(
						'id_owner' => $idOwner,
						'unit_name' => $data['unit_name']
					))->result();
	
					if (empty($checkDuplicateName)) {
						if ($this->PM->update('units', $data, array('id_owner' => $idOwner, 'id_unit' => $idUnit))) {
							$this->helper->sendResponse(200, null, array('message' => 'Satuan berhasil diubah'));
						} else {
							$this->helper->sendResponse(406, null, array('message' => 'Satuan gagal diubah'));
						}
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Satuan dengan nama '.$data['unit_name'].' sudah tersedia'));
					}
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any unit'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function delete()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$idUnit = $this->uri->segment(3);
			$idOwner = $this->input->get('id_owner');
	
			$checkOutlet = $this->isUnitAvailableById($idOwner, $idUnit);

			if (!empty($checkOutlet)) {
				if ($this->PM->delete('units', array('id_owner' => $idOwner, 'id_unit' => $idUnit))) {
					$this->helper->sendResponse(200, null, array('message' => 'Satuan berhasil dihapus'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Satuan gagal dihapus'));
				}
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Bad request. Can not find any unit'));
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function isUnitAvailableById($idOwner, $idUnit)
	{
		return $this->PM->findByQuery('units', array('id_owner' => $idOwner, 'id_unit' => $idUnit))->result();
	}

	private function updateData($idOwner, $idProduct, $data)
	{
		if ($this->PM->update('products', $data, array('id_owner' => $idOwner, 'id_product' => $idProduct))) {
			$this->helper->sendResponse(200, null, array('message' => 'Satuan berhasil diupdate'));
		} else {
			$this->helper->sendResponse(400, null, array('message' => 'Satuan gagal diupdate'));
		}
	}

	public function test(){
		echo 'haloo';
	}
}
