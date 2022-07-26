<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('PaymentModel', 'PM');
		date_default_timezone_set("Asia/Jakarta");

    header('Access-Control-Allow-Origin: *');		
		header('Content-Type: application/json; charset=utf8');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	}
		

	public function index(){	
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$historyPaymentList = $this->PM->getPaymentGroup()->result();

			$this->helper->sendResponse(200, null, array(
				'key_search' => 'Semua',
				'first_data' => 0,
				'active_page' => 0,
				'total_pages' => 0,
				'results' => empty($historyPaymentList) ? null : $this->paymentGroupManagement($historyPaymentList)
			));
		} else {
			header("HTTP/1.1 400");
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}	

	public function edit(){
		if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
			$today = new DateTime();
			$formatedDate = 'Y-m-d H:i:s';
			$idOwner = $this->uri->segment(3);
			$data['is_pro'] = 1;
			$data['start'] = $today->format($formatedDate);
			$data['end'] = $today->modify('+1 month')->format($formatedDate);

			$payment['id_owner'] = $idOwner;
			$payment['year'] = date('Y');
			$payment['month'] = date('m');
			$payment['day'] = date('d');
			$payment['start'] = $data['start'];
			$payment['end'] = $data['end'];
			$payment['paid_off'] = 30000;
	
			if ($this->PM->update('owners', $data, array('id_owner' => $idOwner))) {
				$this->PM->create('history_payment', $payment);
				$this->helper->sendResponse(200, null, array('message' => 'Pembayaran berhasil'));
			} else {
				$this->helper->sendResponse(406, null, array('message' => 'Pembayaran gagal'));
			}

			// echo json_encode($data);
		} else {
			$this->helper->sendResponse(400, array('message' => 'Bad request. Can not find any query param'), null);
		}
	}

	private function paymentGroupManagement($data){
		foreach ($data as $key => $value) {
			$paymentList = $this->PM->getPayment($value->year, $value->month, $value->day)->result();
			$resultPayment = [];
			$total = 0;

			foreach ($paymentList as $k => $v) {
				$getOwner = $this->PM->getOwnerById($v->id_owner)->result();
				$total += (int) $v->paid_off;

				$resultPayment[] = [
					'id_history_payment' => (int) $v->id_history_payment,
					'id_owner' => empty($getOwner) ? null : [
						'id_owner' => (int) $getOwner[0]->id_owner,
						'created_at' => $getOwner[0]->created_at,
						'business_name' => $getOwner[0]->business_name,
						'owner_name' => $getOwner[0]->owner_name,
						'owner_code' => $getOwner[0]->owner_code,
						'telp' => $getOwner[0]->telp,
						'email' => $getOwner[0]->email,
						'is_pro' => (int) $getOwner[0]->is_pro ? true : false,
						'start' => $getOwner[0]->start,
						'end' => $getOwner[0]->end
					],
					'date' => $v->year.'-'.$v->month.'-'.$v->day,
					'start' => $v->start,
					'end' => $v->end,
					'paid_off' => (int) $v->paid_off
				];
			}

			$resultGroup[] = [
				'date' => $value->year.'-'.$value->month.'-'.$value->day,
				'total' => $total,
				'data' => $resultPayment
			];
		}

		return $resultGroup;
	}
}
