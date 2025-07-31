<?php

class Medicine_dosage_model extends CI_model
{

    public function addMedicineDosage($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medicine_dosage', $data);
        } else {
            $this->db->insert('medicine_dosage', $data);
            return $this->db->insert_id();
        }
    }
    public function addMedicineInstruction($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medicin_instruction', $data);
        } else {
            $this->db->insert('medicin_instruction', $data);
            return $this->db->insert_id();
        }
    }

    public function getMedicineDosage($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->select('medicine_dosage.*,medicine_category.medicine_category')
                ->join('medicine_category', 'medicine_dosage.medicine_category_id = medicine_category.id')
                ->where('medicine_dosage.id', $id)
                ->get('medicine_dosage');
            return $query->row_array();
        } else {
            $query = $this->db->select('medicine_dosage.*,medicine_category.medicine_category')
                ->join('medicine_category', 'medicine_dosage.medicine_category_id = medicine_category.id')
                ->get('medicine_dosage');
            return $query->result_array();
        }
    }
    public function getMedicineInstruction($id = null,$catgID=null)
    {
        if (!empty($id)) {
            $query = $this->db->select('medicin_instruction.*,medicine_category.medicine_category')
                ->join('medicine_category', 'medicin_instruction.medicine_category_id = medicine_category.id')
                ->where('medicin_instruction.id', $id)
                ->get('medicin_instruction');
            return $query->row_array();
        } 
        if (!empty($catgID)) {
            $query = $this->db->select('medicin_instruction.*,medicine_category.medicine_category')
                ->join('medicine_category', 'medicin_instruction.medicine_category_id = medicine_category.id')
                ->where('medicine_category.id', $catgID)
                ->get('medicin_instruction');
            return $query->result_array();
        } 
        else {
            $query = $this->db->select('medicin_instruction.*,medicine_category.medicine_category')
                ->join('medicine_category', 'medicin_instruction.medicine_category_id = medicine_category.id')
                ->get('medicin_instruction');
            return $query->result_array();
        }
    }

    public function getDosageByMedicine($medicine)
    {

    }

    public function delete($id)
    {
        $this->db->where("id", $id)->delete("medicine_dosage");
    }
    public function deleteInstruction($id)
    {
        $this->db->where("id", $id)->delete("medicin_instruction");
    }

    public function addMedicinePrecaution($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medicin_precaution', $data);
        } else {
            $this->db->insert('medicin_precaution', $data);
            return $this->db->insert_id();
        }
    }
    
    public function getPrecaution($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->select('medicin_precaution.*')
                ->where('medicin_precaution.id', $id)
                ->get('medicin_precaution');
            return $query->row_array();
        } else {
            $query = $this->db->select('medicin_precaution.*')
                ->get('medicin_precaution');
            return $query->result_array();
        }
    }
    
    public function deletePrecaution($id)
    {
        $this->db->where("id", $id)->delete("medicin_precaution");
    }

}
