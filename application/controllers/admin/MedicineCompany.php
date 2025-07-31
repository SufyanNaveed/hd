<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MedicineCompany extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('medicine_company_model'); // Load the user model if not already loaded

        // Get session data
       
    }
    public function index()
    {
     

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine_company/index');
        $data["title"] = "Manage Medicine Companies";

        $companies = $this->medicine_company_model->getCompany(); // Fetch all companies
        $data["companies"] = $companies;

        $this->load->view("layout/header");
        $this->load->view("admin/pharmacy/medicine_company", $data);
        $this->load->view("layout/footer");
    }

    public function add()
    {
       

        $company_id = $this->input->post("company_id");
        $this->form_validation->set_rules(
            'name',
            'Company Name',
            array(
                'required',
                array('check_exists', array($this->medicine_company_model, 'valid_company_name'))
            )
        );

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $name = $this->input->post("name");
            $address = $this->input->post("address");

            if (!empty($company_id)) {
                $data = array('name' => $name, 'address' => $address, 'id' => $company_id);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                $data = array('name' => $name, 'address' => $address);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }

            $this->medicine_company_model->addCompany($data);
        }

        echo json_encode($array);
    }

    public function edit($id)
    {
        

        $result = $this->medicine_company_model->getCompany($id);
        $data["result"] = $result;
        $data["title"] = "Edit Company";

        $this->load->view("layout/header");
        $this->load->view("admin/pharmacy/medicine_company", $data);
        $this->load->view("layout/footer");
    }

    public function delete($id)
    {
        

        $this->medicine_company_model->deleteCompany($id);
        redirect('admin/medicinecompany/index');
    }

    public function get_data($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_company', 'can_view')) {
            access_denied();
        }

        $result = $this->medicine_company_model->getCompany($id);
        echo json_encode($result);
    }
}
