<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class CustomersModel extends CI_Model{

    public function getCustomers($idOwner, $q, $firstData, $limit) {
      return $this->db->where("id_owner = '$idOwner' AND customer_name LIKE '%$q%'")->order_by('customer_name', 'ASC')->get('customers', $limit, $firstData);
    }

    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    public function getTotalPages($idOwner, $q, $dataAmount){
      return ceil($this->db->where("id_owner = '$idOwner' AND customer_name LIKE '%$q%'")->order_by('customer_name', 'ASC')->get('customers')->num_rows() / $dataAmount);
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

  } 
?>