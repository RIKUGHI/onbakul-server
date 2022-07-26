<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('ProductsModel', 'PM');
		
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

			$getProducts = $this->PM->getProduct($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result(); 

			$this->helper->sendResponse(200, null, array(
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => (int) $firstData,
				'active_page' => (int) $activePage,
				'total_pages' => (int) $this->PM->getTotalPages($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $dataAmount),
				'results' => sizeof($getProducts) == 0 ? null : $this->getData($idOwner, $getProducts)
			));
		} else {
			header("HTTP/1.1 400");
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}	

	public function detail() {
		$idOwner = $this->input->get('id_owner');
		$idProduct = $this->uri->segment(2);

		$getDetailProduct = $this->isProductAvailable($idOwner, $idProduct);

		if (!empty($getDetailProduct)) {
			$this->helper->sendResponse(200, null, $this->getData($idOwner, $getDetailProduct)[0]);
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any product'), null);
		}
	}

	public function create() {
		$config['upload_path'] = './assets/img/';
		$config['allowed_types'] = 'jpeg|jpg|png';
		$config['encrypt_name'] = true;
		$this->load->library('upload', $config);

		if (isset($_FILES['product_img'])) {
			if ($this->upload->do_upload('product_img')){
				$this->createFullAction(true);
			} else {
				$this->helper->sendResponse(406, null, array('message' => $this->upload->display_errors('', '')));
			}
		} else {
			$this->createFullAction(false);
		}
	}

	public function edit() {
		$idProduct = $this->uri->segment(3);
		$idOwner = $this->input->post('id_owner');

		$config['upload_path'] = './assets/img/';
		$config['allowed_types'] = 'jpeg|jpg|png';
		$config['encrypt_name'] = true;
		$this->load->library('upload', $config);

		if (!empty($this->isProductAvailable($idOwner, $idProduct))) {
			if (isset($_FILES['product_img'])) {
				if ($this->upload->do_upload('product_img')){
					$this->updateFullAction($idOwner, $idProduct, true);
				} else {
					$this->helper->sendResponse(406, null, array('message' => $this->upload->display_errors('', '')));
				}
			} else {
				$this->updateFullAction($idOwner, $idProduct, false);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any product'), null);
		}
	}

	public function delete() {
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$idProduct = $this->uri->segment(3);
			$idOwner = $this->input->get('id_owner');

			$isProductAvailable = $this->isProductAvailable($idOwner, $idProduct);

			if (!empty($isProductAvailable)) {
				if ($this->PM->delete('products', array('id_owner' => $idOwner, 'id_product' => $idProduct))) {
					$isProductAvailable[0]->product_img != null ? unlink('assets/img/'.$isProductAvailable[0]->product_img) : null;
					$this->PM->delete('variants', array('id_product' => $idProduct));
					$this->helper->sendResponse(200, null, array('message' => 'Produk telah dihapus'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Produk gagal dihapus'));
				}
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Bad request. Can not find any product'));
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function isProductAvailable($idOwner, $idProduct)	{
		return $this->PM->findByQuery('products', array('id_owner' => $idOwner,'id_product' => $idProduct))->result();
	}

	private function getData($idOwner, $datas) {
		foreach ($datas as $value) {
			$getCategory = $this->PM->findByQuery('categories', array('id_owner' => $idOwner, 'id_category' => $value->id_category))->result();
			$getUnit = $this->PM->findByQuery('units', array('id_owner' => $idOwner, 'id_unit' => $value->id_unit))->result();
			$getVariants = $this->PM->findByQuery('variants', array('id_product' => $value->id_product))->result();
			$availableStock = $value->available_stock ? true : false;

			$resultData[] = array(
				'id_owner' => (int) $value->id_owner,
				'id_product' => (int) $value->id_product,
				'product_img' => $value->product_img == null ? null : base_url('assets/img/'.$value->product_img),
				'product_name' => $value->product_name,
				'barcode' => $value->barcode,
				'id_category' => sizeof($getCategory) == 0 ? array(
					'id_owner' => 0,
					'id_category' => 0,
					'category_name' => 'Umum'
				) : $getCategory[0],
				'capital_price' => (int) $value->capital_price,
				'selling_price' => (int) $value->selling_price,
				'available_stock' => $availableStock,
				'id_unit' => sizeof($getUnit) == 0 ? null : $getUnit[0],
				'stock_quantity' => (int) $value->stock_quantity,
				'stock_min' => (int) $value->stock_min,
				'variants' => empty($getVariants) ? null : $this->variantManagement($idOwner, $getVariants)
			);
		}

		return $resultData;
	}

	private function variantManagement($idOwner, $datas) {
		foreach ($datas as $value) {
			$getUnit = $this->PM->findByQuery('units', array('id_owner' => $idOwner, 'id_unit' => $value->id_unit))->result();

			$resultData[] = array(
				'id_product' => (int) $value->id_product,
				'id_variant' => (int) $value->id_variant,
				'variant_name' => $value->variant_name,
				'capital_price' => (int) $value->capital_price,
				'selling_price' => (int) $value->selling_price,
				'available_stock' => $value->available_stock ? true : false,
				'id_unit' => empty($getUnit) ? null : $getUnit[0],
				'stock_quantity' => (int) $value->stock_quantity,
				'stock_min' => (int) $value->stock_min			
			);
		}

		return $resultData;
	}

	private	function createFullAction($isUpload) {
		$data['id_owner'] = $this->input->post('id_owner');
		$isUpload ? $data['product_img'] = $this->upload->data()['file_name'] : null;
		$data['product_name'] = $this->input->post('product_name');
		$data['barcode'] = $this->input->post('barcode');
		$data['id_category'] = $this->input->post('id_category');
		$data['capital_price'] = $this->input->post('capital_price');
		$data['selling_price'] = $this->input->post('selling_price');
		$data['available_stock'] = $this->input->post('available_stock');
		$data['id_unit'] = $this->input->post('id_unit');
		$data['stock_quantity'] = $this->input->post('stock_quantity');
		$data['stock_min'] =$this->input->post('stock_min');

		$checkDuplicateName = $this->PM->findByQuery('products', array(
			'id_owner' => $data['id_owner'],
			'product_name' => $data['product_name']))->result();
			
		$checkDuplicateBarcode = $this->PM->findByQuery('products', array(
			'id_owner' => $data['id_owner'],
			'barcode' => $data['barcode']))->result();

		// android
		$variant_name = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('variant_name'))) :	$this->input->post('variant_name');
		$capital_price = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('capital_price_v'))) :	$this->input->post('capital_price_v');
		$selling_price = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('selling_price_v'))) :	$this->input->post('selling_price_v');
		$available_stock = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('available_stock_v'))) :	$this->input->post('available_stock_v');
		$id_unit = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('id_unit_v'))) :	$this->input->post('id_unit_v');
		$stock_quantity = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('stock_quantity_v'))) :	$this->input->post('stock_quantity_v');
		$stock_min = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('stock_min_v'))) :	$this->input->post('stock_min_v');

		if (empty($checkDuplicateName) && ($data['barcode'] == '' || empty($checkDuplicateBarcode))) {

			if (is_null($variant_name) || (!is_null($variant_name) && count($variant_name) == 1)) {

				$data['capital_price'] = is_null($variant_name) ? $data['capital_price'] : $capital_price[0];
				$data['selling_price'] = is_null($variant_name) ? $data['selling_price'] : $selling_price[0];
				$data['available_stock'] = is_null($variant_name) ? $data['available_stock'] : $available_stock[0];
				$data['id_unit'] = is_null($variant_name) ? $data['id_unit'] : $id_unit[0];
				$data['stock_quantity'] = is_null($variant_name) ? $data['stock_quantity'] : $stock_quantity[0];
				$data['stock_min'] = is_null($variant_name) ? $data['stock_min'] : $stock_min[0];

				if ($this->PM->create('products', $data)) {
					$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil ditambah'));
				} else {
					$isUpload ? unlink('assets/img/'.$data['product_img']) : null;
					$this->helper->sendResponse(406, null, array('message' => 'Produk gagal ditambah'));
				}
			} else {
				if ($this->PM->create('products', $data)) {
					$maxIdProduct = $this->PM->getMaxIdProduct();
					foreach ($variant_name as $key => $value) {
						$variant['id_product'] = $maxIdProduct;
						$variant['variant_name'] = $value;
						$variant['capital_price'] = $capital_price[$key];
						$variant['selling_price'] = $selling_price[$key];
						$variant['available_stock'] = $available_stock[$key];
						$variant['id_unit'] = $id_unit[$key];
						$variant['stock_quantity'] = $stock_quantity[$key];
						$variant['stock_min'] = $stock_min[$key];
						$this->PM->create('variants', $variant);
					}
					$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil ditambah'));
				} else {
					$isUpload ? unlink('assets/img/'.$data['product_img']) : null;
					$this->helper->sendResponse(406, null, array('message' => 'Produk gagal ditambah'));
				}
			}
			
		} else {
			$isUpload ? unlink('assets/img/'.$data['product_img']) : null;
			
			$name = "nama: ".$data['product_name'];
			$barcode = "barcode: ".$data['barcode'];

			if (!empty($checkDuplicateName) && empty($checkDuplicateBarcode)) {
				$message = $name;
			} else if (!empty($checkDuplicateBarcode) && empty($checkDuplicateName)) {
				$message = $barcode;
			} else {
				$message = $name.($data['barcode'] == '' ? '' : " dan ".$barcode);
			}
			
			$this->helper->sendResponse(406, null, array('message' => 'Produk dengan '.$message.' sudah tersedia'));
		}
	}
	
	private function updateFullAction($idOwner, $idProduct, $isUpload) {
		$isUpload ? $data['product_img'] = $this->upload->data()['file_name'] : null;
		$data['product_name'] = $this->input->post('product_name');
		$data['barcode'] = $this->input->post('barcode');
		$data['id_category'] = $this->input->post('id_category');
		$data['capital_price'] = $this->input->post('capital_price');
		$data['selling_price'] = $this->input->post('selling_price');
		$data['available_stock'] = $this->input->post('available_stock');
		$data['id_unit'] = $this->input->post('id_unit');
		$data['stock_quantity'] = $this->input->post('stock_quantity');
		$data['stock_min'] = $this->input->post('stock_min');
		
		$checkProduct = $this->PM->findByQuery('products', array(
			'id_owner' => $idOwner, 
			'id_product' => $idProduct))->result();

		// if product_name and barcode from database == product_name and barcode from user
		if ($checkProduct[0]->product_name == $data['product_name'] && $checkProduct[0]->barcode == $data['barcode']) {
			$isUpload ? ($checkProduct[0]->product_img == null || $checkProduct[0]->id_product == '' ? null : unlink('assets/img/'.$checkProduct[0]->product_img)) : null;
			$this->updateData($idOwner, $idProduct, $data);
		} else {
			$checkDuplicateName = $this->PM->findByQuery('products', array(
				'id_owner' => $idOwner,
				'product_name' => $data['product_name']))->result();
				
			$checkDuplicateBarcode = $this->PM->findByQuery('products', array(
				'id_owner' => $idOwner,
				'barcode' => $data['barcode']))->result();
			
			// name or barcode can't be the same as one of owner's data products
			if (empty($checkDuplicateName) || ($data['barcode'] == '' || empty($checkDuplicateBarcode))) {
				$isUpload ? unlink('assets/img/'.$checkProduct[0]->product_img) : null;
				$this->updateData($idOwner, $idProduct, $data);
			} else {
				$isUpload ? unlink('assets/img/'.$data['product_img']) : null;
				$this->helper->sendResponse(400, null, array('message' => 'Nama atau barcode tersebut sudah tersedia'));
			}
		}
	}

	private function updateData($idOwner, $idProduct, $data) {
		// android
		$variant_name = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('variant_name'))) :	$this->input->post('variant_name');
		$capital_price_n = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('capital_price_n'))) :	$this->input->post('capital_price_n');
		$selling_price_n = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('selling_price_n'))) :	$this->input->post('selling_price_n');
		$available_stock_n = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('available_stock_n'))) :	$this->input->post('available_stock_n');
		$id_unit_n = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('id_unit_n'))) :	$this->input->post('id_unit_n');
		$stock_quantity_n = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('stock_quantity_n'))) :	$this->input->post('stock_quantity_n');
		$stock_min_n = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name') == '' ? null : explode(',', $this->input->post('stock_min_n'))) :	$this->input->post('stock_min_n');
		
		$id_variant_e = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name_e') == '' ? null : explode(',', $this->input->post('id_variant_e'))) :	$this->input->post('id_variant_e');
		$variant_name_e = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name_e') == '' ? null : explode(',', $this->input->post('variant_name_e'))) :	$this->input->post('variant_name_e');
		$capital_price_e = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name_e') == '' ? null : explode(',', $this->input->post('capital_price_e'))) :	$this->input->post('capital_price_e');
		$selling_price_e = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name_e') == '' ? null : explode(',', $this->input->post('selling_price_e'))) :	$this->input->post('selling_price_e');
		$available_stock_e = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name_e') == '' ? null : explode(',', $this->input->post('available_stock_e'))) :	$this->input->post('available_stock_e');
		$id_unit_e = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name_e') == '' ? null : explode(',', $this->input->post('id_unit_e'))) :	$this->input->post('id_unit_e');
		$stock_quantity_e = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name_e') == '' ? null : explode(',', $this->input->post('stock_quantity_e'))) :	$this->input->post('stock_quantity_e');
		$stock_min_e = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('variant_name_e') == '' ? null : explode(',', $this->input->post('stock_min_e'))) :	$this->input->post('stock_min_e');
		
		$id_variant_d = $this->input->post('platform') != null && $this->input->post('platform') == 'android' ? ($this->input->post('id_variant_d') == '' ? null : explode(',', $this->input->post('id_variant_d'))) :	$this->input->post('id_variant_d');

		if ((!is_null($variant_name) && count($variant_name) == 1) && is_null($id_variant_e)) {
			// echo "only one by create";
			$data['capital_price'] = $capital_price_n[0];
			$data['selling_price'] = $selling_price_n[0];
			$data['available_stock'] = $available_stock_n[0];
			$data['id_unit'] = $id_unit_n[0];
			$data['stock_quantity'] = $stock_quantity_n[0];
			$data['stock_min'] = $stock_min_n[0];

			// delete main variant
			if (!empty($id_variant_d)) {
				$this->PM->delete('variants', array('id_product' => $idProduct));
			}

			if ($this->PM->update('products', $data, array('id_owner' => $idOwner, 'id_product' => $idProduct))) {
				$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate'));
			} else {
				$this->helper->sendResponse(400, null, array('message' => 'Produk gagal diupdate'));
			}
		} else if ((!is_null($id_variant_e) && count($id_variant_e) == 1) && is_null($variant_name)) {
			// echo "only one by edit";
			$data['capital_price'] = $capital_price_e[0];
			$data['selling_price'] = $selling_price_e[0];
			$data['available_stock'] = $available_stock_e[0];
			$data['id_unit'] = $id_unit_e[0];
			$data['stock_quantity'] = $stock_quantity_e[0];
			$data['stock_min'] = $stock_min_e[0];

			// delete main variant
			if (!empty($id_variant_d)) {
				$this->PM->delete('variants', array('id_product' => $idProduct));
			}

			if ($this->PM->update('products', $data, array('id_owner' => $idOwner, 'id_product' => $idProduct))) {
				$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate'));
			} else {
				$this->helper->sendResponse(400, null, array('message' => 'Produk gagal diupdate'));
			}
		} else {
			// echo "multiple";

			// create a new variant
			if (!empty($variant_name)) {
				foreach ($variant_name as $key => $value) {
					$variant['id_product'] = $idProduct;
					$variant['variant_name'] = $value;
					$variant['capital_price'] = $capital_price_n[$key];
					$variant['selling_price'] = $selling_price_n[$key];
					$variant['available_stock'] = $available_stock_n[$key];
					$variant['id_unit'] = $id_unit_n[$key];
					$variant['stock_quantity'] = $stock_quantity_n[$key];
					$variant['stock_min'] = $stock_min_n[$key];

					$this->PM->create('variants', $variant);
				};
			}

			// edit main variant
			if (!empty($id_variant_e)) {
				foreach ($id_variant_e as $key => $value) {
					$variant['variant_name'] = $variant_name_e[$key];
					$variant['capital_price'] = $capital_price_e[$key];
					$variant['selling_price'] = $selling_price_e[$key];
					$variant['available_stock'] = $available_stock_e[$key];
					$variant['id_unit'] = $id_unit_e[$key];
					$variant['stock_quantity'] = $stock_quantity_e[$key];
					$variant['stock_min'] = $stock_min_e[$key];

					$this->PM->update('variants', $variant, array('id_variant' => $value));
				};
			}

			// delete main variant
			if (!empty($id_variant_d)) {
				foreach ($id_variant_d as $key => $value) {
					$this->PM->delete('variants', array('id_variant' => $value));
				}
			}

			if ($this->PM->update('products', $data, array('id_owner' => $idOwner, 'id_product' => $idProduct))) {
				$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate'));
			} else {
				$this->helper->sendResponse(400, null, array('message' => 'Produk gagal diupdate'));
			}
		}
	}
}
