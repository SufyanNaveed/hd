<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class RefundPharmacy extends Hospital_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');

        $this->load->library('CSVReader');
        $this->load->helper('url');
        $this->load->model('user_model'); // Load the user model if not already loaded
        $this->load->model('patient_model'); // Load the user model if not already loaded
        $this->load->model('medicine_company_model'); // Load the user model if not already loaded
        $this->load->model('supplier_type_model'); // Load the user model if not already loaded

    
        $this->load->model('store_model'); // Load the user model if not already loaded
        $this->marital_status       = $this->config->item('marital_status');
        $this->blood_group          = $this->config->item('bloodgroup');

        // Get session data
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $this->store_id = isset($user_details->store_id) ? $user_details->store_id : null;
        $this->user_id = $user_id;
    }

    public function index()
    {
        // if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
        //     access_denied();
        // }
        // if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
        //     access_denied();
        // }
        $resultlist = $this->pharmacy_model->getReturnOpeningStockBill($this->hospital_id, $this->store_id);


        $data['resultlist'] = $resultlist;
        $this->load->view('layout/user/header');
        $this->load->view('store/refundPharmacy/index.php', $data);
        $this->load->view('layout/user/footer');
    }

    public function addReturnStock(){
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['hospitalStores'] = $this->store_model->getHospitalDepartmentStores('Hospital', $this->hospital_id);
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $this->load->view('layout/user/header');
        $this->load->view('store/refundPharmacy/returnStock.php', $data);
        $this->load->view('layout/user/footer'); 
    }

    public function returnStock(){
        $resultlist = $this->pharmacy_model->getMainStoreReturnOpeningStockBill($this->hospital_id, $this->store_id);

        $data['resultlist'] = $resultlist;
        $this->load->view('layout/user/header');
        $this->load->view('store/refundPharmacy/returnStockList.php', $data);
        $this->load->view('layout/user/footer');
    }

     public function returnSupplierStock(){
        $resultlist = $this->pharmacy_model->getMainStoreSupplierReturnOpeningStockBill($this->hospital_id, $this->store_id);

        $data['resultlist'] = $resultlist;
        $this->load->view('layout/user/header');
        $this->load->view('store/refundSupplierPharmacy/returnStockList.php', $data);
        $this->load->view('layout/user/footer');
    }

        public function addSupplierReturnStock(){
            $data['supplierTypes'] = $this->supplier_type_model->getSupplierTypes($this->hospital_id, $this->store_id);

        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory(null, $this->hospital_id, $this->store_id);
        $data["supplierCategory"] = $supplierCategory;
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['hospitalStores'] = $this->store_model->getHospitalDepartmentStores('Hospital', $this->hospital_id);
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $this->load->view('layout/user/header');
        $this->load->view('store/refundSupplierPharmacy/returnStock.php', $data);
        $this->load->view('layout/user/footer'); 
    }
}