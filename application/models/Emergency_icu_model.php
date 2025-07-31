<?php

class Emergency_icu_model extends CI_Model
{
    public function getAll($hospital_id)
    {
        return $this->db->where('hospital_id', $hospital_id)->get('emergency_icu')->result();
    }

    public function insert($data)
    {
        $this->db->insert('emergency_icu', $data);
        return $this->db->insert_id();
    }

    public function getById($id, $hospital_id)
    {
        return $this->db->where('id', $id)->where('hospital_id', $hospital_id)->get('emergency_icu')->row();
    }

    public function update($id, $data, $hospital_id)
    {
        return $this->db->where('id', $id)->where('hospital_id', $hospital_id)->update('emergency_icu', $data);
    }

    public function delete($id, $hospital_id)
    {
        return $this->db->where('id', $id)->where('hospital_id', $hospital_id)->delete('emergency_icu');
    }

    public function getICURecords($hospital_id)
    {
        return $this->db->select('*')
            ->from('emergency_icu')
            ->where('hospital_id', $hospital_id)
            ->where('department_type', 'ICU')
            ->get()
            ->result_array();
    }

    public function getEmergencyRecords($hospital_id)
    {
        return $this->db->select('*')
            ->from('emergency_icu')
            ->where('hospital_id', $hospital_id)
            ->where('department_type', 'Emergency')
            ->get()
            ->result_array();
    }
}
