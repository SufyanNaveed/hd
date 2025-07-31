<?php
class Supplier_type_model extends CI_Model
{
    public function getSupplierTypes($hospital_id = null, $store_id = null)
    {
        $this->db->from('supplier_type');
    
        // Apply conditions only if values are not null
        if (!is_null($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }
        if (!is_null($store_id)) {
            $this->db->where('store_id', $store_id);
        }
    
        return $this->db->get()->result();
    }
    
    public function getSupplierType($id, $hospital_id, $store_id)
    {
        return $this->db->get_where('supplier_type', [
            'id' => $id,
            'hospital_id' => $hospital_id,
            'store_id' => $store_id
        ])->row();
    }

    public function saveSupplierType($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('supplier_type', $data);
        } else {
            $this->db->insert('supplier_type', $data);
        }
    }

    public function deleteSupplierType($id, $hospital_id)
    {
        $this->db->delete('supplier_type', ['id' => $id, 'hospital_id' => $hospital_id]);
    }

    public function checkSupplierExists($name, $id, $hospital_id, $store_id)
    {
        $this->db->where('name', $name);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('store_id', $store_id);

        if ($id) {
            $this->db->where('id !=', $id);
        }

        return $this->db->count_all_results('supplier_type') > 0;
    }
}
