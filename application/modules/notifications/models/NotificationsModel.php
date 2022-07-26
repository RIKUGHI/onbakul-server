<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class NotificationsModel extends CI_Model{

    // public function getProduct($q, $firstData, $limit) {
    //   return  $this->db->query("SELECT * FROM products WHERE product_name LIKE '%$q%' OR barcode LIKE '%$q%' ORDER BY product_name ASC LIMIT $firstData,$limit");
    // }

    public function getProducts($idOwner) {
      return $this->db->where("id_owner = '$idOwner' AND available_stock = 1 AND stock_quantity <= stock_min")->get('products');
    }

    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    // public function getTotalPages($idOwner, $q, $dataAmount){
    //   return ceil($this->db->where("id_owner = '$idOwner' AND outlet_name LIKE '%$q%'")->order_by('outlet_name', 'ASC')->get('outlets')->num_rows() / $dataAmount);
    // }

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