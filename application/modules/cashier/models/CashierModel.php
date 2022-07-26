<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class CashierModel extends CI_Model{
    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    public function create($data) {
      return $this->db->insert('cart', $data);
    }

    public function update($data, $where) {
      return $this->db->update('cart', $data, $where);
    }

    public function delete($data) {
      return $this->db->delete('cart', $data);
    }
  } 
?>