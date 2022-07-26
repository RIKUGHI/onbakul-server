<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashier extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('CashierModel', 'PM');
		$this->load->library('cart');

    header('Access-Control-Allow-Origin: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}
		

	public function index(){	
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$idOwner = $this->input->get('id_owner');
			$idOutlet = $this->input->get('id_outlet');
			$getCart = $this->PM->findByQuery('cart', array(
				'id_owner' => $idOwner,
				'id_outlet' => $idOutlet
			))->result();

			$this->helper->sendResponse(200, null, empty($getCart) ? null : $this->cartManagement($getCart));
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function scan() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$barcode = $this->input->post('barcode');

			$isAvailableProduct = $this->PM->findByQuery('products', array(
				'id_owner' => $this->input->post('id_owner'),
				'barcode' => $barcode
			))->result();

			if (empty($isAvailableProduct)) {
				$this->helper->sendResponse(406, null, array('message' => 'Produk dengan barcode '.$barcode.' belum tersedia'));
			} else {
				$isAnyVariant = $this->PM->findByQuery('variants', array(
					'id_product' => $isAvailableProduct[0]->id_product
				))->result();

				if (empty($isAnyVariant)) {
					$data['id_owner'] = $this->input->post('id_owner');
					$data['id_outlet'] = $this->input->post('id_outlet');
					$data['is_variant'] = 0;
					$data['id_product'] = $isAvailableProduct[0]->id_product;
					$data['product_name'] = $isAvailableProduct[0]->product_name;
					$data['quantity'] = 1;
					$data['selling_price'] = $isAvailableProduct[0]->selling_price;
					$data['capital_price'] = $isAvailableProduct[0]->capital_price;

					$isAvailableProductFromCart = $this->PM->findByQuery('cart', array(
						'id_owner' => $data['id_owner'],
						'id_outlet' => $data['id_outlet'],
						'id_product' => $data['id_product']
					))->result();

					if (empty($isAvailableProductFromCart)) {
						if ($isAvailableProduct[0]->available_stock) {
							if ((int) $data['quantity'] <= (int) $isAvailableProduct[0]->stock_quantity) {
								if ($this->PM->create($data)) {
									$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil ditambahkan ke keranjang'));
								} else {
									$this->helper->sendResponse(406, null, array('message' => 'Produk gagal ditambahkan ke keranjang'));
								}
							} else {
								$this->helper->sendResponse(406, null, array('message' => 'Stok barang ini sisa ' .$isAvailableProduct[0]->stock_quantity));
							}
						} else {					
							if ($this->PM->create($data)) {
								$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil ditambahkan ke keranjang'));
							} else {
								$this->helper->sendResponse(406, null, array('message' => 'Produk gagal ditambahkan ke keranjang'));
							}
						}
					} else {
						if ($isAvailableProduct[0]->available_stock) {
							if (((int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity) <= (int) $isAvailableProduct[0]->stock_quantity) {
								$data['quantity'] = (int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity;
								if ($this->PM->update($data, array(
									'id_owner' => $data['id_owner'],
									'id_outlet' => $data['id_outlet'],
									'id_product' => $data['id_product']))) {
										$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate ke keranjang'));
									} else {
									$this->helper->sendResponse(406, null, array('message' => 'Produk gagal diupdate ke keranjang'));
								}
							} else {
								$this->helper->sendResponse(406, null, array('message' => 'Stok barang ini sisa '.$isAvailableProduct[0]->stock_quantity.' dan kamu sudah punya '.$isAvailableProductFromCart[0]->quantity.' di keranjangmu'));
							}
						} else {
							$data['quantity'] = (int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity;
							if ($this->PM->update($data, array(
								'id_owner' => $data['id_owner'],
								'id_outlet' => $data['id_outlet'],
								'id_product' => $data['id_product']))) {
									$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate ke keranjang'));
								} else {
								$this->helper->sendResponse(406, null, array('message' => 'Produk gagal diupdate ke keranjang'));
							}
						}
					}
				} else {
					$this->helper->sendResponse(204, null, array(
						'message' => 'Produk mempunyai variasi',
						'results' => array(
							"id_owner" => (int) $isAvailableProduct[0]->id_owner,
							"id_product" => (int) $isAvailableProduct[0]->id_product,
							"product_img" => $isAvailableProduct[0]->product_img,
							"product_name" => $isAvailableProduct[0]->product_name,
							"barcode" => $isAvailableProduct[0]->barcode,
							"id_category" => (int) $isAvailableProduct[0]->id_category,
							"capital_price" => (int) $isAvailableProduct[0]->capital_price,
							"selling_price" => (int) $isAvailableProduct[0]->selling_price,
							"available_stock" => (bool) $isAvailableProduct[0]->available_stock,
							"id_unit" => (int) $isAvailableProduct[0]->id_unit,
							"stock_quantity" => (int) $isAvailableProduct[0]->stock_quantity,
							"stock_min" => (int) $isAvailableProduct[0]->stock_min,
							"variants" => $this->variantManagement($isAnyVariant)
						)
					));
				}
			}
			

		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function add() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data['id_owner'] = $this->input->post('id_owner');
			$data['id_outlet'] = $this->input->post('id_outlet');
			$data['is_variant'] = 0;
			$data['id_product'] = $this->input->post('id_product');
			$data['product_name'] = $this->input->post('product_name');
			$data['quantity'] = $this->input->post('quantity');
			$data['selling_price'] = $this->input->post('selling_price');
			$data['capital_price'] = $this->input->post('capital_price');

			$isAvailableProductFromCart = $this->PM->findByQuery('cart', array(
				'id_owner' => $data['id_owner'],
				'id_outlet' => $data['id_outlet'],
				'id_product' => $data['id_product']
			))->result();

			$checkStock = $this->PM->findByQuery('products', array(
				'id_owner' => $data['id_owner'],
				'id_product' => $data['id_product']
			))->result();

			if (empty($isAvailableProductFromCart)) {
				if ($checkStock[0]->available_stock) {
					if ((int) $data['quantity'] <= (int) $checkStock[0]->stock_quantity) {
						if ($this->PM->create($data)) {
							$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil ditambahkan ke keranjang'));
						} else {
							$this->helper->sendResponse(406, null, array('message' => 'Produk gagal ditambahkan ke keranjang'));
						}
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Stok barang ini sisa ' .$checkStock[0]->stock_quantity));
					}
				} else {					
					if ($this->PM->create($data)) {
						$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil ditambahkan ke keranjang'));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Produk gagal ditambahkan ke keranjang'));
					}
				}
			} else {
				if ($checkStock[0]->available_stock) {
					if (((int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity) <= (int) $checkStock[0]->stock_quantity) {
						$data['quantity'] = (int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity;
						if ($this->PM->update($data, array(
							'id_owner' => $data['id_owner'],
							'id_outlet' => $data['id_outlet'],
							'id_product' => $data['id_product']))) {
								$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate ke keranjang'));
							} else {
							$this->helper->sendResponse(406, null, array('message' => 'Produk gagal diupdate ke keranjang'));
						}
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Stok barang ini sisa '.$checkStock[0]->stock_quantity.' dan kamu sudah punya '.$isAvailableProductFromCart[0]->quantity.' di keranjangmu'));
					}
				} else {
					$data['quantity'] = (int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity;
					if ($this->PM->update($data, array(
						'id_owner' => $data['id_owner'],
						'id_outlet' => $data['id_outlet'],
						'id_product' => $data['id_product']))) {
							$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate ke keranjang'));
						} else {
						$this->helper->sendResponse(406, null, array('message' => 'Produk gagal diupdate ke keranjang'));
					}
				}
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function addvariant() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data['id_owner'] = $this->input->post('id_owner');
			$data['id_outlet'] = $this->input->post('id_outlet');
			$data['is_variant'] = 1;
			$data['id_product'] = $this->input->post('id_product');
			$data['product_name'] = $this->input->post('product_name');
			$data['quantity'] = $this->input->post('quantity');
			$data['selling_price'] = $this->input->post('selling_price');
			$data['capital_price'] = $this->input->post('capital_price');

			$isAvailableProductFromCart = $this->PM->findByQuery('cart', array(
				'id_owner' => $data['id_owner'],
				'id_outlet' => $data['id_outlet'],
				'id_product' => $data['id_product']
			))->result();

			$checkStock = $this->PM->findByQuery('variants', array(
				'id_variant' => $data['id_product']
			))->result();

			if (empty($isAvailableProductFromCart)) {
				if ($checkStock[0]->available_stock) {
					if ((int) $data['quantity'] <= (int) $checkStock[0]->stock_quantity) {
						if ($this->PM->create($data)) {
							$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil ditambahkan ke keranjang'));
						} else {
							$this->helper->sendResponse(406, null, array('message' => 'Produk gagal ditambahkan ke keranjang'));
						}
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Stok variasi ini sisa ' .$checkStock[0]->stock_quantity));
					}
				} else {					
					if ($this->PM->create($data)) {
						$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil ditambahkan ke keranjang'));
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Produk gagal ditambahkan ke keranjang'));
					}
				}
			} else {
				if ($checkStock[0]->available_stock) {
					if (((int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity) <= (int) $checkStock[0]->stock_quantity) {
						$data['quantity'] = (int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity;
						if ($this->PM->update($data, array(
							'id_owner' => $data['id_owner'],
							'id_outlet' => $data['id_outlet'],
							'id_product' => $data['id_product']))) {
								$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate ke keranjang'));
							} else {
							$this->helper->sendResponse(406, null, array('message' => 'Produk gagal diupdate ke keranjang'));
						}
					} else {
						$this->helper->sendResponse(406, null, array('message' => 'Stok variasi ini sisa '.$checkStock[0]->stock_quantity.' dan kamu sudah punya '.$isAvailableProductFromCart[0]->quantity.' di keranjangmu'));
					}
				} else {
					$data['quantity'] = (int) $data['quantity'] + (int) $isAvailableProductFromCart[0]->quantity;
					if ($this->PM->update($data, array(
						'id_owner' => $data['id_owner'],
						'id_outlet' => $data['id_outlet'],
						'id_product' => $data['id_product']))) {
							$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil diupdate ke keranjang'));
						} else {
						$this->helper->sendResponse(406, null, array('message' => 'Produk gagal diupdate ke keranjang'));
					}
				}
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function addQuantity() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$where['id_owner'] = $this->input->post('id_owner');
			$where['id_outlet'] = $this->input->post('id_outlet');
			$where['is_variant'] = $this->input->post('is_variant');
			$where['id_product'] = $this->input->post('id_product');
			$data['quantity'] = $this->input->post('qty');

			if ($this->PM->update($data, $where)) {
				$this->helper->sendResponse(200, null, array('message' => 'Qty berhasil ditambahkan'));
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Qty gagal ditambahkan'));
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function delete() {
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$data['id_owner'] = $this->input->get('id_owner');
			$data['id_outlet'] = $this->input->get('id_outlet');
			$data['is_variant'] = $this->input->get('is_variant');
			$data['id_product'] = $this->input->get('id_product');

			if ($this->PM->delete($data)) {
				$this->helper->sendResponse(200, null, array('message' => 'Produk berhasil didelete ke keranjang'));
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Produk gagal didelete ke keranjang'));
			}
			
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function cartManagement($data) {
		foreach ($data as $value) {
			$detail = $this->PM->findByQuery('products', array(
				'id_owner' => $value->id_owner,
				'id_product' => $value->id_product
			))->result();

			$detailVariant = $this->PM->findByQuery('variants', array(
				'id_variant' => $value->id_product
			))->result();

			$resultData[] = array(
				'id_owner' => (int) $value->id_owner,
				'id_outlet' => (int) $value->id_outlet,
				'is_variant' => (bool) $value->is_variant,
				'id_product' => (int) $value->id_product,
				'product_name' => $value->product_name,
				'quantity' => (int) $value->quantity,
				'selling_price' => (int) $value->selling_price,
				'capital_price' => (int) $value->capital_price,
				'detail' => $value->is_variant ? array(
					'id_product' => empty($detailVariant) ? null : (int) $detailVariant[0]->id_product,
					'id_variant' => empty($detailVariant) ? null : (int) $detailVariant[0]->id_variant,
					'variant_name' => empty($detailVariant) ? null : $detailVariant[0]->variant_name,
					'capital_price' => empty($detailVariant) ? null : (int) $detailVariant[0]->capital_price,
					'selling_price' => empty($detailVariant) ? null : (int) $detailVariant[0]->selling_price,
					'available_stock' => empty($detailVariant) ? null : (bool) $detailVariant[0]->available_stock,
					'id_unit' => empty($detailVariant) ? null : (int) $detailVariant[0]->id_unit,
					'stock_quantity' => empty($detailVariant) ? null : (int) $detailVariant[0]->stock_quantity,
					'stock_min' => empty($detailVariant) ? null : (int) $detailVariant[0]->stock_min
				) : array(
					'id_owner' => empty($detail) ? null : (int) $detail[0]->id_owner,
					'id_product' => empty($detail) ? null : (int) $detail[0]->id_product,
					'product_img' => empty($detail) ? null : $detail[0]->product_img,
					'product_name' => empty($detail) ? null : $detail[0]->product_name,
					'barcode' => empty($detail) ? null : $detail[0]->barcode,
					'id_category' => empty($detail) ? null : (int) $detail[0]->id_category,
					'capital_price' => empty($detail) ? null : (int) $detail[0]->capital_price,
					'selling_price' => empty($detail) ? null : (int) $detail[0]->selling_price,
					'available_stock' => empty($detail) ? null : ($detail[0]->available_stock ? true : false),
					'id_unit' => empty($detail) ? null : (int) $detail[0]->id_unit,
					'stock_quantity' => empty($detail) ? null : (int) $detail[0]->stock_quantity,
					'stock_min' => empty($detail) ? null : (int) $detail[0]->stock_min
				)
			);
		}

		return $resultData;
	}

	private function variantManagement($data) {
		foreach ($data as $value) {
			$unit = $this->PM->findByQuery('units', array(
				'id_unit' => $value->id_unit
			))->result();
			$resultData[] = array(
				"id_variant" => (int) $value->id_variant,
				"id_product" => (int) $value->id_product,
				"variant_name" => $value->variant_name,
				"selling_price" => (int) $value->selling_price,
				"capital_price" => (int) $value->capital_price,
				"available_stock" => (bool) $value->available_stock,
				"id_unit" => empty($unit) ? null : $unit[0],
				"stock_quantity" => (int) $value->stock_quantity,
				"stock_min" => (int) $value->stock_min
			);
		}

		return $resultData;
	}
}
