<?php

class Medicine_company_model extends CI_Model
{
    public function check_company_exists($name, $id = 0)
    {
        $this->db->where('name', $name);
        if ($id != 0) {
            $this->db->where('id !=', $id); // Exclude the current record when updating
        }

        $query = $this->db->get('medicine_company');
        return $query->num_rows() > 0; // Return true if a record exists
    }

    public function valid_company_name($str)
    {
        $company_name = $this->input->post('name');
        $id = $this->input->post('id');

        if (!isset($id)) {
            $id = 0;
        }

        if ($this->check_company_exists($company_name, $id)) {
            $this->form_validation->set_message('valid_company_name', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function addCompany($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medicine_company', $data);
        } else {
            $this->db->insert('medicine_company', $data);
            return $this->db->insert_id();
        }
    }

    public function getCompany($id = null)
    {
        $this->db->from('medicine_company');

        if (!empty($id)) {
            $this->db->where('id', $id);
        }

        $query = $this->db->get();

        if (!empty($id)) {
            return $query->row_array(); // Return a single record if ID is provided
        } else {
            return $query->result_array(); // Return all records otherwise
        }
    }

    public function deleteCompany($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('medicine_company');
    }

    public function getMedicineCompanies()
{
    $query = $this->db->get('medicine_company');
    return $query->result_array();
}

}
