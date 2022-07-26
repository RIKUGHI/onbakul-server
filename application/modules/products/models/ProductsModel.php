<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class ProductsModel extends CI_Model{

    public function getProduct($id, $q, $firstData, $limit) {
      $idCategory = $this->input->get('id_category');
      if ($idCategory) {
        return $this->db->query("SELECT * FROM products WHERE products.id_owner = '$id' AND products.id_category = '$idCategory' AND (product_name LIKE '%$q%' OR barcode LIKE '%$q%') ORDER BY product_name ASC LIMIT $firstData, $limit");
      } else {
        return $this->db->where("id_owner = '$id' AND (product_name LIKE '%$q%' OR barcode LIKE '%$q%')")->order_by('product_name', 'ASC')->get('products', $limit, $firstData); 
      }
    }

    public function getMaxIdProduct() {
      $result = $this->db->select_max('id_product', 'max_id')->get('products')->result();

      return empty($result[0]->max_id) ? 1 : $result[0]->max_id;
    }
    
    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    public function getTotalPages($id, $q, $dataAmount){
      $idCategory = $this->input->get('id_category');
      if ($idCategory) {
        return ceil($this->db->query("SELECT * FROM products WHERE products.id_owner = '$id' AND products.id_category = '$idCategory' AND (product_name LIKE '%$q%' OR barcode LIKE '%$q%') ORDER BY product_name ASC")->num_rows() / $dataAmount);
      } else {
        return ceil($this->db->where("id_owner = '$id' AND (product_name LIKE '%$q%' OR barcode LIKE '%$q%')")->order_by('product_name', 'ASC')->get('products')->num_rows() / $dataAmount);
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