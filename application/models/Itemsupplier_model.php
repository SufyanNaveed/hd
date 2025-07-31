<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Itemsupplier_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null, $hospital_id, $store_id = null)
    {
        $this->db->select()->from('item_supplier');
        $this->db->where('hospital_id', $hospital_id);

        if ($store_id !== null) {
            $this->db->where('store_id', $store_id);
        }

        if ($id !== null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }

        $query = $this->db->get();

        if ($id !== null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->where('hospital_id', $data['hospital_id']); // Ensure hospital context
            $this->db->update('item_supplier', $data);
        } else {
            $this->db->insert('item_supplier', $data);
        }
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('item_supplier');
    }

}
