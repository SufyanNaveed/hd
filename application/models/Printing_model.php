<?php

class Printing_model extends CI_Model
{

    public function add($data)
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("print_setting", $data);
        } else {
            $this->db->insert("print_setting", $data);
            return $this->db->insert_id();
        }
    }

    public function get($id = '', $setting_for = '', $hospital_id = null)
    {
        if (!empty($id)) {
            $this->db->where("id", $id);
        }

        if (!empty($hospital_id)) {
            $this->db->where("hospital_id", $hospital_id);
        }

        if (!empty($id)) {
            $query = $this->db->get("print_setting");
            return $query->row_array();
        } else {
            $this->db->where("setting_for", $setting_for);
            $query = $this->db->get("print_setting");
            return $query->result_array();
        }
    }
    public function get_latest($setting_for = '', $hospital_id = null)
{
    $this->db->select('*'); // Select all columns
    $this->db->from("print_setting");
    $this->db->order_by("id", "DESC"); // Order by latest ID

    if (!empty($hospital_id)) {
        $this->db->where("hospital_id", $hospital_id);
    }

    if (!empty($setting_for)) {
        $this->db->where("setting_for", $setting_for);
    }

    $this->db->limit(1); // Ensure only 1 record is fetched

    $query = $this->db->get();

    return $query->row_array(); // Return single latest record
}


    public function delete($id)
    {
        $this->db->where("id", $id)->delete('print_setting');
    }
}
