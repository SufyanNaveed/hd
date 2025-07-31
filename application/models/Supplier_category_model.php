<?php

class Supplier_category_model extends CI_model
{
    public function valid_supplier($str)
    {
        $supplier = $this->input->post('supplier_name');
        $id = $this->input->post('supplierid');
        $hospital_id = $this->input->post('hospital_id');
        $store_id = $this->input->post('store_id');

        if (!isset($id)) {
            $id = 0;
        }

        if ($this->check_supplier_exists($supplier, $id, $hospital_id, $store_id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function getSupplier($id = null, $hospital_id = null, $store_id = null)
    {
        $this->db->from('supplier');

        if (!empty($id)) {
            $this->db->where("id", $id);
        }

        if (!empty($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->db->where('store_id', $store_id);
        }

        $query = $this->db->get();

        if (!empty($id)) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getSupplierPat($id = null, $hospital_id = null, $store_id = null)
    {
        return $this->getSupplier($id, $hospital_id, $store_id);
    }

    public function check_supplier_exists($name, $id, $hospital_id, $store_id)
    {
        $this->db->where('supplier', $name);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('store_id', $store_id);

        if ($id != 0) {
            $this->db->where('id !=', $id);
        }

        $query = $this->db->get('supplier');
        return $query->num_rows() > 0;
    }

    public function addsupplier($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('supplier', $data);
        } else {
            $this->db->insert('supplier', $data);
            return $this->db->insert_id();
        }
    }

    public function getall($hospital_id = null, $store_id = null)
    {
        $this->datatables->select('id,supplier');
        $this->datatables->from('supplier');

        if (!empty($hospital_id)) {
            $this->datatables->where('hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->datatables->where('store_id', $store_id);
        }

        $this->datatables->add_column('view', '<a href="' . site_url('admin/medicinecategory/edit/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"> <i class="fa fa-pencil"></i></a><a href="' . site_url('admin/medicinecategory/delete/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Delete">
                                                        <i class="fa fa-remove"></i>
                                                    </a>', 'id');
        return $this->datatables->generate();
    }

    public function delete($id, $hospital_id = null)
    {
        $this->db->where("id", $id);

        if (!empty($hospital_id)) {
            $this->db->where("hospital_id", $hospital_id);
        }

        $this->db->delete("supplier");
    }
}
