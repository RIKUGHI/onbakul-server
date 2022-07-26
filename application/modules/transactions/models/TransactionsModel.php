<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class TransactionsModel extends CI_Model{

    public function getTransactions($idOwner, $q, $firstData, $limit) {
      $idOutlet = $this->input->get('id_outlet'); 
      if ($idOutlet) {
        return $this->db->where("id_owner = '$idOwner' AND id_outlet = '$idOutlet' AND invoice LIKE '%$q%'")->order_by('date', 'ASC')->get('transactions', $limit, $firstData);
      } else {
        return $this->db->where("id_owner = '$idOwner' AND invoice LIKE '%$q%'")->order_by('date', 'ASC')->get('transactions', $limit, $firstData);
      }
    }
    
    public function getTransactionsNoLimit($idOwner, $q) {
      $idOutlet = $this->input->get('id_outlet'); 
      if ($idOutlet) {
        return $this->db->where("id_owner = '$idOwner' AND id_outlet = '$idOutlet' AND invoice LIKE '%$q%'")->order_by('date', 'ASC')->get('transactions');
      } else {
        return $this->db->where("id_owner = '$idOwner' AND invoice LIKE '%$q%'")->order_by('date', 'ASC')->get('transactions');
      }
    }
    
    public function getTransactionsByDateRange($idOwner, $start, $end, $firstData, $limit) {
      $idOutlet = $this->input->get('id_outlet'); 
      if ($idOutlet) {
        return $this->db->where("id_owner = '$idOwner' AND id_outlet = '$idOutlet' AND date BETWEEN '$start' AND '$end'")->order_by('date', 'ASC')->get('transactions', $limit, $firstData);
      } else {
        return $this->db->where("id_owner = '$idOwner' AND date BETWEEN '$start' AND '$end'")->order_by('date', 'ASC')->get('transactions', $limit, $firstData);
      }
    }
    
    public function getTransactionsByDateRangeNoLimit($idOwner, $start, $end) {
      $idOutlet = $this->input->get('id_outlet'); 
      if ($idOutlet) {
        return $this->db->where("id_owner = '$idOwner' AND id_outlet = '$idOutlet' AND date BETWEEN '$start' AND '$end'")->order_by('date', 'ASC')->get('transactions');
      } else {
        return $this->db->where("id_owner = '$idOwner' AND date BETWEEN '$start' AND '$end'")->order_by('date', 'ASC')->get('transactions');
      }
    }

    public function getTransactionDetailsByIdTransaction($id) {
      return $this->db->get_where('transaction_details', array('id_transaction' => $id));
    }

    public function getTotalByDateGroup($idOwner, $date) {
      return $this->db->where("id_owner = '$idOwner' AND date = '$date'")->select_sum('grand_total')->get('transactions');
    }

    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    public function query($query) {
      return $this->db->query($query);
    }

    public function getTotalPages($idOwner, $q, $dataAmount){
      $idOutlet = $this->input->get('id_outlet'); 
      if ($idOutlet) {
        return ceil($this->db->where("id_owner = '$idOwner' AND id_outlet = '$idOutlet' AND invoice LIKE '%$q%'")->get('transactions')->num_rows() / $dataAmount);
      } else {
        return ceil($this->db->where("id_owner = '$idOwner' AND invoice LIKE '%$q%'")->get('transactions')->num_rows() / $dataAmount);
      }
    }
    
    public function getTotalPagesByDateRange($idOwner, $start, $end, $dataAmount) {
      $idOutlet = $this->input->get('id_outlet'); 
      if ($idOutlet) {
        return ceil($this->db->where("id_owner = '$idOwner' AND id_outlet = '$idOutlet' AND date BETWEEN '$start' AND '$end'")->get('transactions')->num_rows() / $dataAmount);
      } else {
        return ceil($this->db->where("id_owner = '$idOwner' AND date BETWEEN '$start' AND '$end'")->get('transactions')->num_rows() / $dataAmount);
      }
    }

    public function create($table, $data) {
      return $this->db->insert($table, $data);
    }

    public function update($table, $data, $where) {
      return $this->db->update($table, $data, $where);
    }


    public function delete($table, $data) {
      return $this->db->delete($table, $data);
    }

    function getInvoiceById($idOutlet){
      $today = date('Y-m-d');

      $result = $this->db->select_max('id_transaction', 'max_id')->where("id_outlet = '$idOutlet' AND date = '$today'")->get('transactions')->result();

      if (empty($result[0]->max_id)) {
        return sprintf('%07s',1);
      } else {
        $getLastNumber = $this->db->get_where('transactions', array('id_transaction' => $result[0]->max_id))->result()[0];
        $split = explode('-', $getLastNumber->invoice);
        $getNo = end($split) + 1;
        return sprintf('%07s', $getNo);
      }
    }

    public function getMaxIdTransaction($idOutlet) {
      $today = date('Y-m-d');

      return $this->db->select_max('id_transaction', 'max_id')->where("id_outlet = '$idOutlet' AND date = '$today'")->get('transactions')->result();
    }
  } 
?>