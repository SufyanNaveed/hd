<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Suppliercategory extends Hospital_Controller
{
    protected $hospital_id;
    protected $store_id;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
        $this->load->model('user_model'); // Load the user model if not already loaded

        // Get session data
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $this->store_id = isset($user_details->store_id) ? $user_details->store_id : null;
    }

    public function supplier()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'supplier/index');

        $data["title"] = "Add Supplier";

        // Fetch suppliers filtered by hospital_id and store_id
        $data["supplier"] = $this->supplier_category_model->getSupplier($this->hospital_id, $this->store_id);

        $this->form_validation->set_rules(
            'supplier',
            'Supplier',
            array('required',
                array('check_exists', array($this->supplier_category_model, 'valid_supplier')),
            )
        );

        if ($this->form_validation->run()) {
            $supplier = $this->input->post("supplier");
            $supplier_id = $this->input->post("id");

            

            // Prepare data with hospital_id and store_id
            $data = array(
                'supplier' => $supplier,
                'hospital_id' => $this->hospital_id,
                'store_id' => $this->store_id,
            );

            if (!empty($supplier_id)) {
                $data['id'] = $supplier_id;
            }

            $this->supplier_category_model->addsupplier($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/Sustorercategory/supplier");
        } else {
            $this->load->view("layout/user/header");
            $this->load->view("store/pharmacy/supplier", $data);
            $this->load->view("layout/footer/footer");
        }
    }

    public function add()
    {
       

        $this->form_validation->set_rules(
            'supplier',
            $this->lang->line('medicine') . " " . $this->lang->line('category'),
            array('required',
                array('check_exists', array($this->supplier_category_model, 'valid_supplier')),
            )
        );

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('supplier'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $supplier = $this->input->post("supplier");
            $supplier_id = $this->input->post("id");

            // Prepare data with hospital_id and store_id
            $data = array(
                'supplier' => $supplier,
                'hospital_id' => $this->hospital_id,
                'store_id' => $this->store_id,
            );

            if (!empty($supplier_id)) {
                $data['id'] = $supplier_id;
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }

            $this->supplier_category_model->addsupplier($data);
        }
        echo json_encode($array);
    }

    public function get()
    {
        // Get supplier data filtered by hospital_id and store_id and encode to JSON
        header('Content-Type: application/json');
        echo json_encode($this->supplier_category_model->getall($this->hospital_id, $this->store_id));
    }

    public function edit($id)
    {

        // Fetch supplier data filtered by hospital_id and store_id
        $result = $this->supplier_category_model->getSupplier($id, $this->hospital_id, $this->store_id);
        if (empty($result)) {
            show_error("Invalid supplier ID or access denied.");
        }

        $data["result"] = $result;
        $data["title"] = "Edit Category";
        $data["supplier"] = $this->supplier_category_model->getSupplier($this->hospital_id, $this->store_id);

        $this->load->view("layout/user/header");
        $this->load->view("store/pharmacy/supplier", $data);
        $this->load->view("layout/footer/footer");
    }

    public function delete($id)
    {
       
        // Delete supplier with hospital_id check
        $this->supplier_category_model->delete($id, $this->hospital_id);
        redirect('admin/Sustorercategory/supplier');
    }

    public function get_data($id)
    {
        

        // Fetch supplier data filtered by hospital_id and store_id
        $result = $this->supplier_category_model->getSupplier($id, $this->hospital_id, $this->store_id);
        echo json_encode($result);
    }
}
