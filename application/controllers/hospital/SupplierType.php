<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SupplierType extends Hospital_Controller
{
    protected $hospital_id;
    protected $store_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('user_model'); 
        $this->load->model('supplier_type_model'); // Load Supplier Type Model

        // Get session data
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details
        $user_details = $this->user_model->getUserById($user_id);

        // Initialize properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $this->store_id = isset($user_details->store_id) ? $user_details->store_id : null;
    }

    // Display all supplier types
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
}
