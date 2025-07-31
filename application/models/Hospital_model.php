<?php

class Hospital_model extends CI_Model
{
    protected $column_search = ['name', 'hospital_unique_id', 'phone_number', 'address'];

    public function searchhospital_datatable()
    {
        $this->db->select("hospitals.*,'0' action")->from('hospitals');

        // Handle ordering
        if (!isset($_POST['order'])) {
            $this->db->order_by('hospitals.id', "desc");
        }

        // Handle search filter
        if (!empty($_POST['search']['value'])) {
            $this->db->group_start();
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $_POST['search']['value']);
            }
            $this->db->group_end();
        }

        // Handle pagination
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }

        // Execute query and fetch results
        $query = $this->db->get();
        return $query->result();
    }

    public function searchhospital_datatable_count()
    {
        $this->db->from('hospitals');

        // Handle search filter
        if (!empty($_POST['search']['value'])) {
            $this->db->group_start();
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $_POST['search']['value']);
            }
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }
    public function addHospital($data)
    {
        $this->db->insert('hospitals', $data);
        return $this->db->insert_id();
    }
    public function getLastHospital()
    {
        $this->db->select('id')->from('hospitals')->order_by('id', 'DESC')->limit(1);
        $query = $this->db->get();

        return $query->row(); // Returns the last inserted hospital row or null if none exist
    }

    public function getHospitalDetails($id)
    {
        $this->db->select('hospitals.*')->from('hospitals')->where('hospitals.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function updateHospital($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('hospitals', $data);
    }

    public function deleteHospitalById($id)
    {
        // Get the hospital record to delete its logo
        $this->db->select('logo');
        $this->db->where('id', $id);
        $hospital = $this->db->get('hospitals')->row();

        if ($hospital) {
            // Delete the logo file if it exists
            if (!empty($hospital->logo) && file_exists($hospital->logo)) {
                unlink($hospital->logo);
            }

            // Delete the hospital record from the database
            $this->db->where('id', $id);
            return $this->db->delete('hospitals');
        }

        return false;
    }

    public function getAllHospitals()
    {
        $this->db->select('id, name');
        $this->db->from('hospitals');
        $query = $this->db->get();
        return $query->result_array();
    }
}
