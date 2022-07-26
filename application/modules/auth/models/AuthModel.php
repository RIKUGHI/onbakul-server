<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class AuthModel extends CI_Model{

    public function getMaxIdOwner() {
      $result = $this->db->select_max('id_owner', 'max_id')->get('owners')->result();

      return empty($result[0]->max_id) ? sprintf('%05s',1) : sprintf('%05s', $result[0]->max_id + 1);
    }

    public function getMaxIdAdmin() {
      return $this->db->select_max('id_admin', 'max_id')->get('admins')->result();
    }

    public function findByQuery($table, $data) {
      return $this->db->get_where($table, $data);
    }

    public function checkDuplicateByEmail($email) {
      return $this->db->get_where('owners', array('email' => $email))->result();
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