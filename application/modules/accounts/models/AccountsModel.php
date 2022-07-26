<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class AccountsModel extends CI_Model{

    public function getAccounts($q, $firstData, $limit) {
      return $this->db->where("owner_name LIKE '%$q%'")->order_by('owner_name', 'ASC')->get('owners', $limit, $firstData);
    }
    
    public function getUntung(){
      return $this->db->query("SELECT SUM(paid_off) as untung FROM history_payment");
    }


    public function getTotalUsers() {
      return $this->db->get('owners');
    }

    public function getTotalProducts($idOwner) {
      return $this->db->get_where('products', array('id_owner' => $idOwner));
    }

    public function getNewUsers() {
      return $this->db->query("SELECT created_at, COUNT(*) AS total FROM owners GROUP BY created_at ORDER BY created_at DESC LIMIT 0, 30");
    }

    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    public function getTotalPages($q, $dataAmount){
      return ceil($this->db->where("owner_name LIKE '%$q%'")->order_by('owner_name', 'ASC')->get('owners')->num_rows() / $dataAmount);
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