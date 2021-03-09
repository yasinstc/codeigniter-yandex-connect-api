<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Yandex_connect_model extends CI_Model
{
    protected $table = 'yandex_connect';

    public function get_all()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->order_by('id ASC');

        $sql = $this->db->get();

        if ($sql->num_rows()) {
            return $sql->result();
        } else {
            return false;
        }
    }

    public function get_ID($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $this->db->order_by('id ASC');

        $sql = $this->db->get();

        if ($sql->num_rows()) {
            return $sql->row();
        } else {
            return false;
        }
    }

    public function add($data)
    {
        $this->db->set($data);
        $add = $this->db->insert($this->table);

        if ($add) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->set($data);

        $update = $this->db->update($this->table);

        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_ID($id)
    {
        $this->db->where('id', $id);

        $delete = $this->db->delete($this->table);

        if ($delete) {
            return true;
        } else {
            return false;
        }
    }
}
