<?php

class Medicine_category_model extends CI_model
{
    public function valid_medicine_category($str)
    {
        $medicine_category = $this->input->post('medicine_category');
        $id = $this->input->post('id');
        $hospital_id = $this->input->post('hospital_id');
        $store_id = $this->input->post('store_id');

        if (!isset($id)) {
            $id = 0;
        }

        if ($this->check_category_exists($medicine_category, $id, $hospital_id, $store_id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function valid_medicine_name($str)
    {
        $medicine_name = $this->input->post('medicine_name');
        $id = $this->input->post('id');
        $hospital_id = $this->input->post('hospital_id');
        $store_id = $this->input->post('store_id');

        if (!isset($id)) {
            $id = 0;
        }

        if ($this->check_name_exists($medicine_name, $id, $hospital_id, $store_id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_name_exists($name, $id, $hospital_id, $store_id)
    {
        $this->db->where('medicine_name', $name);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('store_id', $store_id);

        if ($id != 0) {
            $this->db->where('id !=', $id);
        }

        $query = $this->db->get('pharmacy');
        return $query->num_rows() > 0;
    }

    public function check_category_exists($name, $id, $hospital_id, $store_id)
    {
        $this->db->where('medicine_category', $name);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('store_id', $store_id);

        if ($id != 0) {
            $this->db->where('id !=', $id);
        }

        $query = $this->db->get('medicine_category');
        return $query->num_rows() > 0;
    }
    public function check_medicine_category_exists($medicine_category, $id, $hospital_id, $store_id)
{
    $this->db->where('medicine_category', $medicine_category);
    $this->db->where('hospital_id', $hospital_id);
    $this->db->where('store_id', $store_id);

    if ($id != 0) {
        $this->db->where('id !=', $id); // Exclude the current record when updating
    }

    $query = $this->db->get('medicine_category');
    return $query->num_rows() > 0; // Return true if a record exists
}


    public function check_category_existssupplier($name, $id, $hospital_id, $store_id)
    {
        $this->db->where('supplier_category', $name);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('store_id', $store_id);

        if ($id != 0) {
            $this->db->where('id !=', $id);
        }

        $query = $this->db->get('supplier_category');
        return $query->num_rows() > 0;
    }

    public function getMedicineCategory($id = null, $hospital_id = null, $store_id = null)
    {
        $this->db->from('medicine_category');

        if (!empty($id)) {
            $this->db->where('id', $id);
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
    public function getSupplierCategoryPat($id = null, $hospital_id = null, $store_id = null, $type_id = null)
    {
        $this->db->select("supplier_category.*, supplier_type.name AS supplier_type_name");
        $this->db->from("supplier_category");
        $this->db->join("supplier_type", "supplier_type.id = supplier_category.supplier_type_id", "left");
    
        if (!empty($id)) {
            $this->db->where("supplier_category.id", $id);
        }
    
        if (!empty($hospital_id)) {
            $this->db->where("supplier_category.hospital_id", $hospital_id);
        }
    
        
        if (!empty($type_id)) {
            $this->db->where("supplier_category.supplier_type_id", $type_id);
        }
        if (!empty($store_id)) {
            $this->db->where("supplier_category.store_id", $store_id);
        }
    
        $query = $this->db->get();
    
        if (!empty($id)) {
            return $query->row_array(); // Return a single record if ID is provided
        } else {
            return $query->result_array(); // Return all records otherwise
        }
    }
    
    public function valid_supplier_category($str,$hospital_id=null,$store_id=null)
    {
        $supplier_category = $this->input->post('supplier_category');
        $id = $this->input->post('suppliercategoryid');
    
        if (!isset($id)) {
            $id = 0;
        }
    
        if ($this->check_category_existssupplier($supplier_category, $id, $hospital_id, $store_id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }
    
    public function addMedicineCategory($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medicine_category', $data);
        } else {
            $this->db->insert('medicine_category', $data);
            return $this->db->insert_id();
        }
    }

    public function addSupplierCategory($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('supplier_category', $data);
        } else {
            $this->db->insert('supplier_category', $data);
            return $this->db->insert_id();
        }
    }

    public function getSupplierCategory($id = null, $hospital_id = null, $store_id = null)
    {
        $this->db->from('supplier_category');

        if (!empty($id)) {
            $this->db->where('id', $id);
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

    public function delete($id, $hospital_id)
    {
        $this->db->where('id', $id);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->delete('medicine_category');
    }

    public function deletesupplier($id, $hospital_id)
    {
        $this->db->where('id', $id);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->delete('supplier_category');
    }
    public function supplier()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'supplier/index');
        $this->session->set_userdata('sub_sidebar_menu', 'hospital/medicinecategory/supplier');

        $data["title"] = "Supplier Types";
        $data["supplierTypes"] = $this->supplier_type_model->getSupplierTypes($this->hospital_id, $this->store_id);

        $this->load->view("layout/user/header");
        $this->load->view("store/pharmacy/supplier_type", $data);
        $this->load->view("layout/user/footer");
    }

    // Add or update a supplier type
    public function addSupplierType()
    {
        $this->form_validation->set_rules('name', 'Supplier Type Name', 'required|callback_check_supplier_exists');

        if ($this->form_validation->run() == false) {
            $msg = array('name' => form_error('name'));
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $supplierTypeId = $this->input->post("id");
            $name = $this->input->post("name");

            $data = array(
                'name' => $name,
                'hospital_id' => $this->hospital_id,
                'store_id' => $this->store_id,
            );

            if (!empty($supplierTypeId)) {
                $data['id'] = $supplierTypeId;
                $message = $this->lang->line('update_message');
            } else {
                $message = $this->lang->line('success_message');
            }

            $this->supplier_type_model->saveSupplierType($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $message);
        }

        echo json_encode($array);
    }

    // Delete a supplier type
    public function deleteSupplierType($id)
    {
        $this->supplier_type_model->deleteSupplierType($id, $this->hospital_id);
        redirect('hospital/medicinecategory/supplier');
    }

    // Get a single supplier type by ID
    public function getSupplierType($id)
    {
        $result = $this->supplier_type_model->getSupplierType($id, $this->hospital_id, $this->store_id);
        echo json_encode($result);
    }

    // Get all supplier types
    public function getAllSupplierTypes()
    {
        header('Content-Type: application/json');
        echo json_encode($this->supplier_type_model->getSupplierTypes($this->hospital_id, $this->store_id));
    }

    // Custom validation callback for checking duplicate supplier type
    public function check_supplier_exists($name)
    {
        $id = $this->input->post('id');
        $exists = $this->supplier_type_model->checkSupplierExists($name, $id, $this->hospital_id, $this->store_id);

        if ($exists) {
            $this->form_validation->set_message('check_supplier_exists', $this->lang->line('record_already_exists'));
            return false;
        }
        return true;
    }

    public function getSupplierByType($id = null, $hospital_id = null, $store_id = null,$typeId)
    {
        $this->db->from('supplier_category');

        if (!empty($id)) {
            $this->db->where('id', $id);
        }

        if (!empty($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }
        if($typeId){
            $this->db->where('supplier_type_id', $typeId);
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
}
