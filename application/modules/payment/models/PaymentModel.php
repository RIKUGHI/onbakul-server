<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class PaymentModel extends CI_Model{
    public function getPaymentGroup(){
      return $this->db->group_by(array('year', 'month', 'day'))->order_by('year DESC, month DESC, day DESC')->get('history_payment');
    }

    public function getPayment($year, $month, $day){
      return $this->db->where("year = $year AND month = $month AND day = $day")->order_by('id_history_payment', 'DESC')->get('history_payment');
    }

    public function getOwnerById($id){
      return $this->db->get_where('owners', array('id_owner' => $id));
    }

    public function create($table, $data) {
      return $this->db->insert($table, $data);
    }

    public function update($table, $data, $where) {
      return $this->db->update($table, $data, $where);
    }
  } 
?>