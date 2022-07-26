<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class PurchasesModel extends CI_Model{

    public function getPurchases($idOwner, $q, $firstData, $limit) {
      $idOutlet = $this->input->get('id_outlet');
      if ($idOutlet) { 
        return $this->db->where("id_owner = '$idOwner' AND id_outlet = '$idOutlet' AND product_name LIKE '%$q%'")->order_by('product_name', 'ASC')->get('purchases', $limit, $firstData);
      } else {
        return $this->db->where("id_owner = '$idOwner' AND product_name LIKE '%$q%'")->order_by('product_name', 'ASC')->get('purchases', $limit, $firstData);
      }
    }

    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    public function getTotalPages($idOwner, $q, $dataAmount){
      $idOutlet = $this->input->get('id_outlet');
      if ($idOutlet) { 
        return ceil($this->db->where("id_owner = '$idOwner' AND id_outlet = '$idOutlet' AND product_name LIKE '%$q%'")->order_by('product_name', 'ASC')->get('purchases')->num_rows() / $dataAmount);
      } else {
        return ceil($this->db->where("id_owner = '$idOwner' AND product_name LIKE '%$q%'")->order_by('product_name', 'ASC')->get('purchases')->num_rows() / $dataAmount);
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

  } 
?>