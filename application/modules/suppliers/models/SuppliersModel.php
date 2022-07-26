<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class SuppliersModel extends CI_Model{

    public function getSuupliers($idOwner, $q, $firstData, $limit) {
      return $this->db->where("id_owner = '$idOwner' AND supplier_name LIKE '%$q%'")->order_by('supplier_name', 'ASC')->get('suppliers', $limit, $firstData);
    }

    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    public function getTotalPages($idOwner, $q, $dataAmount){
      return ceil($this->db->where("id_owner = '$idOwner' AND supplier_name LIKE '%$q%'")->order_by('supplier_name', 'ASC')->get('suppliers')->num_rows() / $dataAmount);
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