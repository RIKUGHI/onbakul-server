<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require('./application/libraries/excel/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Transactions extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('TransactionsModel', 'TM');
		date_default_timezone_set("Asia/Jakarta");

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

			$start = '';
			$end = '';

			if (strtolower($this->input->get('type')) == 'periode') {
				$periode = true;
				$start = $this->input->get('start');
				$end = $this->input->get('end');
				$transactionList = $this->TM->getTransactionsByDateRange($idOwner, $start, $end, $firstData, $dataAmount)->result(); 
			} else {
				$periode = false;
				$transactionList = $this->TM->getTransactions($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $firstData, $dataAmount)->result(); 
			}
			


			$this->helper->sendResponse(200, null, array(
				'type' => $periode ? 'periode' : 'single',
				'start' => $start,
				'end' => $end,
				'key_search' => $this->input->get('q') == null ? 'Semua' : $this->input->get('q'),
				'first_data' => (int) $firstData,
				'active_page' => (int) $activePage,
				'total_pages' => (int) $periode ? $this->TM->getTotalPagesByDateRange($idOwner, $start, $end, $dataAmount) : $this->TM->getTotalPages($idOwner, $this->input->get('q') == null ? '' : $this->input->get('q'), $dataAmount),
				'results' => empty($transactionList) ? null : $this->transactionManagement($transactionList)
			));
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}	

	public function detail() {
		$idOwner = $this->input->get('id_owner');
		$idOutlet = $this->input->get('id_outlet');
		$inv = $this->uri->segment(2);
		$idTransaction = $this->input->get('id_transaction');

		$transaction = $this->TM->findByQuery('transactions', array(
			// 'id_owner' => $idOwner,
			// 'id_outlet' => $idOutlet,
			'id_transaction' => $idTransaction,
			'invoice' => $inv))->result();

		if (!empty($transaction)) {
			$transactionDetails= $this->TM->findByQuery('transaction_details', array(
				'id_transaction' => $idTransaction,
				'invoice' => $transaction[0]->invoice
			))->result();

			$this->helper->sendResponse(200, null, array(
				'id_owner' => (int) $transaction[0]->id_owner,
				'id_outlet' => (int) $transaction[0]->id_outlet,
				'id_transaction' => (int) $transaction[0]->id_transaction,
				'invoice' => $transaction[0]->invoice,
				'date' => $transaction[0]->date,
				'time' => $transaction[0]->time,
				'method' => (int) $transaction[0]->method,
				'grand_total' => (int) $transaction[0]->grand_total,
				'paid_off' => (int) $transaction[0]->paid_off,
				'details' => $this->transactionDetailManagement($transactionDetails)
			));
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any transaction'), null);
		}
	}

	public function dashboard() {
		$idOwner = $this->input->get('id_owner');
		$idOutlet = $this->input->get('id_outlet');
		$monthly = date('Y-m');
		// $monthly = "2021-01";
		$profit = 0;
		$totalOmset = 0;
		$omsets = [];
		$dataOmset = [];

		if ($idOutlet) {
			$transactions = $this->TM->query("SELECT * FROM transactions WHERE id_owner = $idOwner AND id_outlet = '$idOutlet' AND date LIKE '%$monthly%'")->result();
		} else {
			$transactions = $this->TM->query("SELECT * FROM transactions WHERE id_owner = $idOwner AND date LIKE '%$monthly%'")->result();
		}



		if (empty($transactions)) {
			$totalTransactions = 0;
		} else {
			foreach ($transactions as $key => $value) {
				$totalTransactions = $key + 1;
				
				$transactionDetails = $this->TM->findByQuery('transaction_details', array(
					// 'invoice' => $value->invoice
					'id_transaction' => $value->id_transaction
				))->result();
				$getOutlet = $this->TM->findByQuery('outlets', array('id_outlet' => $value->id_outlet))->result()[0];

				$totalOmset += (int) $value->grand_total;
				$omsets[] = [
					'id_transaction' => (int) $value->id_transaction,
					'id_outlet' => $getOutlet,
					'invoice' => $value->invoice,
					'date' => $value->date,
					'time' => $value->time,
					'grand_total' => (int) $value->grand_total
				];

				foreach ($transactionDetails as $key => $valueD) {
					$profit += ((int) $valueD->selling_price - (int) $valueD->capital_price) * (int) $valueD->quantity;
					$dataOmset[] = [
						'id_outlet' => $getOutlet,
						'id_transaction' => (int) $valueD->id_transaction,
						'product_name' => $valueD->product_name,
						'selling_price' => (int) $valueD->selling_price,
						'capital_price' => (int) $valueD->capital_price,
						'quantity' => (int) $valueD->quantity
					];
				}
			}
		}

		if ($idOutlet) {
			$purchase = $this->TM->query("SELECT * FROM purchases WHERE id_owner = $idOwner AND id_outlet = '$idOutlet' AND status = 1 AND date LIKE '%$monthly%'")->result();
		} else {
			$purchase = $this->TM->query("SELECT * FROM purchases WHERE id_owner = $idOwner AND status = 1 AND date LIKE '%$monthly%'")->result();
		}
		$expenditure = 0;

		if (empty($purchase)) {
			$expenditure = 0;
		} else {
			foreach ($purchase as $key => $value) {
				$expenditure = $expenditure + ((int) $value->price * (int) $value->quantity);
			}
		}

		if ($idOutlet) {	
			$orderPlan = $this->TM->query("SELECT * FROM purchases WHERE id_owner = $idOwner AND id_outlet = '$idOutlet' AND status = 0 AND date LIKE '%$monthly%'")->result();
		} else {
			$orderPlan = $this->TM->query("SELECT * FROM purchases WHERE id_owner = $idOwner AND status = 0 AND date LIKE '%$monthly%'")->result();
		}
		
		if (empty($orderPlan)) {
			$totalOrderPlan = 0;
		} else {
			foreach ($orderPlan as $key => $value) {
				$totalOrderPlan = $key + 1;
			}
		}
		
		if ($idOutlet) {
			$getGroupByDays = $this->TM->query("SELECT * FROM transactions, transaction_details 
			WHERE transactions.id_transaction = transaction_details.id_transaction AND transactions.id_owner = $idOwner AND transactions.id_outlet = $idOutlet AND transactions.date LIKE '%$monthly%' GROUP BY transactions.date ORDER BY transactions.date ASC")->result();
		} else {
			$getGroupByDays = $this->TM->query("SELECT * FROM transactions, transaction_details 
			WHERE transactions.id_transaction = transaction_details.id_transaction AND transactions.id_owner = $idOwner AND transactions.date LIKE '%$monthly%' GROUP BY transactions.date ORDER BY transactions.date ASC")->result();
		}

		if (empty($getGroupByDays)) {
			$dailyProfit = [];
		} else {
			foreach ($getGroupByDays as $key => $value) {
				if ($idOutlet) {
					$getTransactionByDays = $this->TM->query("SELECT * FROM transactions, transaction_details 
					WHERE transactions.id_transaction = transaction_details.id_transaction AND transactions.id_owner = $idOwner AND transactions.id_outlet = $idOutlet AND transactions.date = '$value->date'")->result();
				} else {
					$getTransactionByDays = $this->TM->query("SELECT * FROM transactions, transaction_details 
					WHERE transactions.id_transaction = transaction_details.id_transaction AND transactions.id_owner = $idOwner AND transactions.date = '$value->date'")->result();
				}
				

				$dProfit = 0;
				foreach ($getTransactionByDays as $key => $v) {
					$dProfit = $dProfit + ((int) $v->selling_price * (int) $v->quantity - (int) $v->capital_price * (int) $v->quantity);
				}
	
				$dailyProfit[] = array(
					'date' => $value->date,
					'profit' => $dProfit
				);
			}
		}

		if ($idOutlet) {
			$getTop10Products = $this->TM->query("SELECT transaction_details.product_name, SUM(quantity)AS total FROM transactions, transaction_details 
			WHERE transactions.id_transaction = transaction_details.id_transaction AND transactions.id_owner = $idOwner AND transactions.id_outlet = $idOutlet AND transactions.date LIKE '%$monthly%' 
			GROUP BY transaction_details.id_product, transaction_details.is_variant ORDER BY total DESC LIMIT 0,10")->result();
		} else {
			$getTop10Products = $this->TM->query("SELECT transaction_details.product_name, SUM(quantity)AS total FROM transactions, transaction_details 
			WHERE transactions.id_transaction = transaction_details.id_transaction AND transactions.id_owner = $idOwner AND transactions.date LIKE '%$monthly%' 
			GROUP BY transaction_details.id_product, transaction_details.is_variant ORDER BY total DESC LIMIT 0,10")->result();
		}

		if (empty($getTop10Products)) {
			$top10 = [];
		} else {
			foreach ($getTop10Products as $key => $value) {
				$top10[] = array(
					"product_name" => $value->product_name,
					"total" => (int) $value->total
				);
			}
		}

		if ($idOutlet) {
			$getLast7DaysTransaction = $this->TM->query("SELECT COUNT(*) AS total, date FROM transactions 
			WHERE id_owner = $idOwner AND id_outlet = $idOutlet GROUP BY date ORDER BY date ASC LIMIT 0,7")->result();
		} else {
			$getLast7DaysTransaction = $this->TM->query("SELECT COUNT(*) AS total, date FROM transactions 
			WHERE id_owner = $idOwner GROUP BY date ORDER BY date ASC LIMIT 0,7")->result();
		}

		if (empty($getLast7DaysTransaction)) {
		$getLast7Days = [];
		} else {
			foreach ($getLast7DaysTransaction as $key => $value) {
				$getLast7Days[] = array(
					'date' => $value->date,
					'total' => (int) $value->total
				);
			}
		}

		if ($idOutlet) {
			$getMonthlyTransactions = $this->TM->query("SELECT COUNT(*) AS total, date FROM transactions 
			WHERE id_owner = $idOwner AND id_outlet = $idOutlet AND date LIKE '%$monthly%' GROUP BY date ORDER BY date ASC")->result();
		} else {
			$getMonthlyTransactions = $this->TM->query("SELECT COUNT(*) AS total, date FROM transactions 
			WHERE id_owner = $idOwner AND date LIKE '%$monthly%' GROUP BY date ORDER BY date ASC")->result();
		}

		if (empty($getMonthlyTransactions)) {
			$getMonthly = [];
		} else {
			foreach ($getMonthlyTransactions as $key => $value) {
				$getMonthly[] = array(
					'date' => $value->date,
					'total' => (int) $value->total
				);
			}
		}

		$year = date('Y');
		// $year = "2022";

		if ($idOutlet) {
			$getYearlyTransactions = $this->TM->query("SELECT date FROM transactions 
			WHERE id_owner = $idOwner AND id_outlet = $idOutlet AND date LIKE '%$year%'")->result();
		} else {
			$getYearlyTransactions = $this->TM->query("SELECT date FROM transactions 
			WHERE id_owner = $idOwner AND date LIKE '%$year%'")->result();
		}

		$group = array();
		foreach ($getYearlyTransactions as $value) {
			$yearMonth = explode('-', $value->date);
			if (in_array(array(
				"date" => 	$yearMonth[0].'-'.$yearMonth[1]
			), $group)) {

			} else {
				array_push($group, array(
					"date" => 	$yearMonth[0].'-'.$yearMonth[1]
				));
			}
		}

		foreach ($group as $key => $value) {
			$date = $value['date'];
			$getYearly[] = array(
				'date' => $value['date'],
				'total' => (int) $this->TM->query("SELECT COUNT(*) AS total from transactions WHERE id_owner = $idOwner AND date LIKE '%$date%'")->result()[0]->total
			);
		}

		if (empty($getYearly)) {
			$getYearly = [];
		} else {
			
		}
		

		$this->helper->sendResponse(200, null, array(
			'today' => date('Y-m-d'),
			'total_transactions' => $totalTransactions,
			'omset' => [
				'month' => (int) date('m'),
				'total' => $totalOmset,
				'omsets' => $omsets,
				'data' => $dataOmset
			],
			'profit' => $profit,
			'expenditure' => $expenditure,
			'total_order_plan' => $totalOrderPlan,
			'daily_profit' => array(
				'month' => (int) date('m'),
				'data' => $dailyProfit
			),
			'top_10_products' => array(
				'month' => (int) date('m'),
				'data' => $top10
			),
			'last_7_days_trasaction' => array(
				'month' => (int) date('m'),
				'data' => $getLast7Days
			),
			'monthly_transactions' => array(
				'month' => (int) date('m'),
				'data' => $getMonthly
			),
			'yearly_transactions' => array(
				'year' => (int) date('Y'),
				'data' => $getYearly
			)
		));	
	}

	public function create() {
		$data['id_owner'] = $this->input->post('id_owner');
		$data['id_outlet'] = $this->input->post('id_outlet');
		$data['id_transaction'] = 0;

		$indexOfOutlet = 0;
		$listOutlet = $this->TM->findByQuery('outlets', array(
			'id_owner' => $data['id_owner']
		))->result();

		if (!empty($listOutlet)) {
			foreach ($listOutlet as $key => $value) {
				if ((int) $data['id_outlet'] == (int) $value->id_outlet) {
					$indexOfOutlet = $key + 1;
				}
			}
		}

		$data['invoice'] = $this->input->post('owner_code').'-'.sprintf('%03s', $indexOfOutlet).'-'.$this->TM->getInvoiceById($data['id_outlet']);
		$data['customer_name'] = $this->input->post('customer_name');
		$data['date'] = date('Y-m-d');
		$data['time'] = date('H:i:s');
		$data['method'] = $this->input->post('method');
		$data['discount'] = $this->input->post('discount');
		$data['grand_total'] = (int) $data['discount'] == 0 ? $this->input->post('grand_total') : (int) $this->input->post('grand_total') - (int) $data['discount'];
		$data['paid_off'] = $this->input->post('paid_off');

		if ($this->TM->create('transactions', $data)) {
			$data['id_transaction'] = $this->TM->getMaxIdTransaction($data['id_outlet'])[0]->max_id;
			$this->helper->sendResponse(200, null, array(
				'message' => 'transaksi berhasil ditambah',
				'details' => array(
					'id_transaction' => $data['id_transaction'],
					'invoice' => $data['invoice'],
					'date' => $data['date'],
					'time' => $data['time'],
					'payment' => $data['method'],
					'discount' => (int) $data['discount'],
					'grand_total' => (int) $data['grand_total'],
					'paid_off' => (int) $data['paid_off']
				)	
			));
		} else {
			$this->helper->sendResponse(406, null, array('message' => 'transaksi gagal ditambah'));
		}

		// transaction details
		foreach ($this->input->post('product_name') as $key => $value) {
			$detail['id_transaction'] = $data['id_transaction'];
			$detail['is_variant'] = $this->input->post('is_variant')[$key];
			$detail['id_product'] = $this->input->post('id_product')[$key];
			$detail['invoice'] = $data['invoice'];
			$detail['product_name'] = $value;
			$detail['capital_price'] = $this->input->post('capital_price')[$key];
			$detail['selling_price'] = $this->input->post('selling_price')[$key];
			$detail['quantity'] = $this->input->post('quantity')[$key];

			$this->TM->create('transaction_details', $detail);

			// update stocks
			if ($detail['is_variant']) {
				$checkVariant = $this->TM->findByQuery('variants', array(
					'id_variant' => $detail['id_product']
				))->result();

				if ($checkVariant[0]->available_stock) {
					$this->TM->update('variants', array(
						'stock_quantity' => (int) $checkVariant[0]->stock_quantity - (int) $detail['quantity']
					), array(
						'id_variant' => $detail['id_product']
					));
				}
			} else {
				$checkProduct = $this->TM->findByQuery('products', array(
					'id_product' => $detail['id_product']
				))->result();
				
				if ($checkProduct[0]->available_stock) {
					$this->TM->update('products', array(
						'stock_quantity' => (int) $checkProduct[0]->stock_quantity - (int) $detail['quantity']
					), array(
						'id_product' => $detail['id_product']
					));
				} 
			}
		}

		// delete transactions
		$this->TM->delete('cart', array(
			'id_outlet' => $data['id_outlet']
		));
	}

	public function delete() {
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$idOwner = $this->input->get('id_owner');
			$idTransaction = $this->input->get('id_transaction');
			$inv = $this->uri->segment(3);
			
			$checkTransaction = $this->TM->findByQuery('transactions', array(
				'id_transaction' => $idTransaction
			))->result();
	
			if (!empty($checkTransaction)) {
				if ($this->TM->delete('transactions', array('id_owner' => $idOwner, 'id_transaction' => $idTransaction))) {
					$this->TM->delete('transaction_details', array('id_transaction' => $idTransaction));
					
					$this->helper->sendResponse(200, null, array('message' => 'Transaksi berhasil dihapus'));
				} else {
					$this->helper->sendResponse(406, null, array('message' => 'Transaksi gagal dihapus'));
				}
			} else {
				$this->helper->sendResponse(406, array('message' => 'Bad request. Can not find any transaction'), null);
			}
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	public function export() {
		$spreadsheet = new Spreadsheet();
		$spreadsheet->
		getProperties()->
		setCreator('OnBakul System')->
		setLastModifiedBy('OnBakul System')->
		setTitle('Office 2007 XLSX Document')->
		setSubject('Office 2007 XLSX Document')->
		setDescription('Laporan Transaksi')->
		setKeywords('office 2007 openxml php')->
		setCategory('Result File');

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'No')->getStyle('A1')->getFont()->setBold(true);
		$sheet->getCell('A1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('B1', 'Invoice')->getStyle('B1')->getFont()->setBold(true);
		$sheet->getCell('B1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('C1', 'Pembeli')->getStyle('C1')->getFont()->setBold(true);
		$sheet->getCell('C1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('D1', 'Tanggal')->getStyle('D1')->getFont()->setBold(true);
		$sheet->getCell('D1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('E1', 'Waktu')->getStyle('E1')->getFont()->setBold(true);
		$sheet->getCell('E1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('F1', 'Metode')->getStyle('F1')->getFont()->setBold(true);
		$sheet->getCell('F1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('G1', 'Total')->getStyle('G1')->getFont()->setBold(true);
		$sheet->getCell('G1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('H1', 'Bayar')->getStyle('H1')->getFont()->setBold(true);
		$sheet->getCell('H1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('I1', 'Kembalian')->getStyle('I1')->getFont()->setBold(true);
		$sheet->getCell('I1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		$idOwner = $this->input->get('id_owner');
		$q = $this->input->get('q') ? $this->input->get('q') : '';
		$start = $this->input->get('start');
		$end = $this->input->get('end');
		$lastIndex = 0;
		$total = 0;
		$paidOff = 0;

		if ($this->input->get('id_owner') != null && ($this->input->get('start') == null || $this->input->get('end') == null)) {
			foreach ($this->TM->getTransactionsNoLimit($idOwner, $q)->result() as $key => $value) {
				$total += (int) $value->grand_total;
				$paidOff += (int) $value->paid_off;

				$sheet->setCellValue('A'.($key + 2), $key + 1)->getStyle('A'.($key + 2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('B'.($key + 2), $value->invoice)->getStyle('B'.($key + 2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('C'.($key + 2), $value->customer_name)->getStyle('C'.($key + 2));
				$sheet->setCellValue('D'.($key + 2), $value->date)->getStyle('D'.($key + 2));
				$sheet->setCellValue('E'.($key + 2), $value->time)->getStyle('E'.($key + 2));
				$sheet->setCellValue('F'.($key + 2), 'Tunai')->getStyle('F'.($key + 2));
				$sheet->setCellValue('G'.($key + 2), 'Rp. '.number_format($value->grand_total,0,',','.'))->getStyle('G'.($key + 2));
				$sheet->setCellValue('H'.($key + 2), 'Rp. '.number_format($value->paid_off,0,',','.'))->getStyle('H'.($key + 2));
				$sheet->setCellValue('I'.($key + 2), 'Rp. '.number_format((int) $value->paid_off - (int) $value->grand_total,0,',','.'))->getStyle('I'.($key + 2));
				$lastIndex = $key + 3;
			}
			$sheet->setCellValue('G'.$lastIndex, 'Rp. '.number_format($total,0,',','.'))->getStyle('G'.$lastIndex)->getFont()->setBold(true);
			$sheet->setCellValue('H'.$lastIndex, 'Rp. '.number_format($paidOff,0,',','.'))->getStyle('H'.$lastIndex)->getFont()->setBold(true);
			$sheet->setCellValue('I'.$lastIndex, 'Rp. '.number_format($paidOff - $total,0,',','.'))->getStyle('I'.$lastIndex)->getFont()->setBold(true);

		} else if ($this->input->get('id_owner') != null && $this->input->get('start') != null && $this->input->get('end') != null) {
			foreach ($this->TM->getTransactionsByDateRangeNoLimit($idOwner, $start, $end)->result() as $key => $value) {
				$total += (int) $value->grand_total;
				$paidOff += (int) $value->paid_off;

				$sheet->setCellValue('A'.($key + 2), $key + 1)->getStyle('A'.($key + 2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('B'.($key + 2), $value->invoice)->getStyle('B'.($key + 2))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$sheet->setCellValue('C'.($key + 2), $value->customer_name)->getStyle('C'.($key + 2));
				$sheet->setCellValue('D'.($key + 2), $value->date)->getStyle('D'.($key + 2));
				$sheet->setCellValue('E'.($key + 2), $value->time)->getStyle('E'.($key + 2));
				$sheet->setCellValue('F'.($key + 2), 'Tunai')->getStyle('F'.($key + 2));
				$sheet->setCellValue('G'.($key + 2), 'Rp. '.number_format($value->grand_total,0,',','.'))->getStyle('G'.($key + 2));
				$sheet->setCellValue('H'.($key + 2), 'Rp. '.number_format($value->paid_off,0,',','.'))->getStyle('H'.($key + 2));
				$sheet->setCellValue('I'.($key + 2), 'Rp. '.number_format((int) $value->paid_off - (int) $value->grand_total,0,',','.'))->getStyle('I'.($key + 2));
				$lastIndex = $key + 3;
			}
			$sheet->setCellValue('G'.$lastIndex, 'Rp. '.number_format($total,0,',','.'))->getStyle('G'.$lastIndex)->getFont()->setBold(true);
			$sheet->setCellValue('H'.$lastIndex, 'Rp. '.number_format($paidOff,0,',','.'))->getStyle('H'.$lastIndex)->getFont()->setBold(true);
			$sheet->setCellValue('I'.$lastIndex, 'Rp. '.number_format($paidOff - $total,0,',','.'))->getStyle('I'.$lastIndex)->getFont()->setBold(true);
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
		
		if ($this->input->get('id_owner') != null) {
			$fileName = '';
			if ($this->input->get('start') != null && $this->input->get('end') != null) {
				$fileName = ' '.$start.' - '.$end;
			}
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Laporan Transaksi'.$fileName.'.xlsx');
			header('Cache-Control: max-age=0');
	
			$write = IOFactory::createWriter($spreadsheet,'Xlsx');
			$write->save('php://output');
			exit;	
		}
	}

	private function isTransactionAvailableByInvoice($idOwner, $inv)	{
		return $this->TM->findByQuery('transactions', array('id_owner' => $idOwner,'invoice' => $inv))->result();
	}

	private function transactionManagement($datas) {
		$group = array();
		$no = 0;
		foreach ($datas as $value) {
			if (in_array(array("date" => $value->date), $group)) {

			} else {
				$no++;
				array_push($group, array("date" => $value->date));
			}
			
			// $getTransactionDetails = $this->TM->getTransactionDetailsByInvoice($value->invoice)->result();
			
			// $management[] = array(
			// 	"id_owner" => (int) $value->id_owner,
			// 	"id_outlet" => (int) $value->id_outlet,
			// 	"id_transaction" => (int) $value->id_transaction,
			// 	"invoice" => $value->invoice,
			// 	"date" => $value->date,
			// 	"time" => $value->time,
			// 	"method" => $value->method,
			// 	"grand_total" => (int) $value->grand_total,
			// 	"paid_off" => (int) $value->paid_off,
			// 	"details" => empty($getTransactionDetails) ? null : $getTransactionDetails
			// );
		}
	
		foreach ($group as $key => $value) {
			$getTotal = $this->TM->getTotalByDateGroup($this->input->get('id_owner'), $value['date'])->result();

			foreach ($datas as $v) {
				if ($v->date == $value['date']) {
					$getOutlet = $this->TM->findByQuery('outlets', array('id_owner' => $v->id_owner,'id_outlet' => $v->id_outlet))->result();
					$getTransactionDetails = $this->TM->getTransactionDetailsByIdTransaction($v->id_transaction)->result();

					$x[] = array(
						"id_owner" => (int) $v->id_owner,
						"id_outlet" => count($getOutlet) == 0 ? null : $getOutlet[0],
						"id_transaction" => (int) $v->id_transaction,
						"invoice" => $v->invoice,
						"date" => $v->date,
						"time" => $v->time,
						"method" => (int) $v->method,
						"customer_name" => $v->customer_name,
						"discount" => (int) $v->discount,
						"grand_total" => (int) $v->grand_total,
						"paid_off" => (int) $v->paid_off,
						"details" => empty($getTransactionDetails) ? null : $this->transactionDetailManagement($getTransactionDetails)
					);

					$group[$key] = array(
						"date" => $value['date'],
						"total" => (int) $getTotal[0]->grand_total,
						"results" => $x
					);
				}else{
					$x = [];
				}
			}
		}

		return $group;
	}

	private function transactionDetailManagement($data) {
		foreach ($data as $value) {
			$management[] = array(
				"invoice" => $value->invoice,
				"product_name" => $value->product_name,
				"capital_price" => (int) $value->capital_price,
				"selling_price" => (int) $value->selling_price,
				"quantity" => (int) $value->quantity
			);
		}
		
		return $management;
	}
}
