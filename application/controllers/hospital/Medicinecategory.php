<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Medicinecategory extends Hospital_Controller
{
    protected $hospital_id;
    protected $store_id;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
        $this->load->model('user_model'); // Load the user model if not already loaded
        $this->load->model('supplier_type_model'); // Load Supplier Type Model

        // Get session data
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $this->store_id = isset($user_details->store_id) ? $user_details->store_id : null;
    }

    public function index()
    {
        $this->medicine();
    }

    public function medicine()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'hospital/medicinecategory/medicine');

        $medicinecategoryid = $this->input->post("medicinecategoryid");
        $data["title"] = "Add Medicine Category";

        // Fetch medicine categories filtered by hospital_id and store_id
        $medicineCategory = $this->medicine_category_model->getMedicineCategory(null,$this->hospital_id, $this->store_id);
        $data["medicineCategory"] = $medicineCategory;
        $this->form_validation->set_rules(
            'medicine_category',
            'Medicine Category',
            array('required',
                array('check_exists', array($this->medicine_category_model, 'valid_medicine_category')),
            )
        );

        if ($this->form_validation->run()) {
            $medicineCategory = $this->input->post("medicine_category");
            $medicinecategoryid = $this->input->post("id");

            // Prepare data with hospital_id and store_id
            $data = array(
                'medicine_category' => $medicineCategory,
                'hospital_id' => $this->hospital_id,
                'store_id' => $this->store_id,
            );

            if (!empty($medicinecategoryid)) {
                $data['id'] = $medicinecategoryid;
            }

            $this->medicine_category_model->addMedicineCategory($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("hospital/medicinecategory/medicine");
        } else {
            $this->load->view("layout/user/header");
            $this->load->view("store/pharmacy/medicine_category", $data);
            $this->load->view("layout/user/footer");
        }
    }
    

    public function supplier()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'hospital/medicinecategory/supplier');
    
        $data["title"] = "Add Supplier";
    
        // Fetch supplier categories filtered by hospital_id and store_id
        $supplierCategory = $this->medicine_category_model->getSupplierCategoryPat(null, $this->hospital_id, $this->store_id);
        $data["supplierCategory"] = $supplierCategory;
    
        // Fetch supplier types filtered by hospital_id and store_id
        $supplierTypes = $this->supplier_type_model->getSupplierTypes($this->hospital_id, $this->store_id);
        $data["supplierTypes"] = $supplierTypes; // Now supplier types are also available
        $this->form_validation->set_rules('supplier_category', $this->lang->line('supplier') . " " . $this->lang->line('name'), 'required|trim');

        $this->form_validation->set_rules('supplier_type_id', $this->lang->line('supplier') . " " . $this->lang->line('type'), 'required|integer');
    
        $this->form_validation->set_rules('contact', $this->lang->line('supplier') . " " . $this->lang->line('contact'), 'required|trim|min_length[10]|max_length[15]');
    
        $this->form_validation->set_rules('supplier_person', $this->lang->line('contact_person_name'), 'required|trim');
    
        $this->form_validation->set_rules('supplier_contact_person', $this->lang->line('contact_person_phone'), 'required|trim|min_length[10]|max_length[15]');
    
        $this->form_validation->set_rules('address', $this->lang->line('address'), 'required|trim|min_length[5]|max_length[255]');
    
        if ($this->form_validation->run()) {
            $supplierCategory = $this->input->post("supplier_category");
            $supplierTypeId = $this->input->post("supplier_type_id"); // Get selected supplier type
            $suppliercategoryid = $this->input->post("id");
    
            // Prepare data with hospital_id, store_id, and supplier_type_id
            $data = array(
                'supplier_category' => $supplierCategory,
                'hospital_id' => $this->hospital_id,
                'store_id' => $this->store_id,
                'contact' => $this->input->post('contact'),
                'supplier_person' => $this->input->post('supplier_person'),
                'address' => $this->input->post('address'),
                'supplier_contact_person' => $this->input->post('supplier_contact_person'),
        
                'supplier_type_id' => $supplierTypeId, // Store supplier type
            );
            echo "<pre>"; print_r($data);exit;
    
            if (!empty($suppliercategoryid)) {
                $data['id'] = $suppliercategoryid;
            }
    
            $this->medicine_category_model->addSupplierCategory($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("hospital/medicinecategory/supplier");
        } else {
            $this->load->view("layout/user/header");
            $this->load->view("store/pharmacy/supplier_category", $data);
            $this->load->view("layout/user/footer");
        }
    }
    

    public function add()
    {
        $medicinecategoryid = $this->input->post("medicinecategoryid");
    
        $this->form_validation->set_rules(
            'medicine_category',
            $this->lang->line('medicine') . " " . $this->lang->line('category'),
            array(
                'required',
                array('check_exists', array($this, 'valid_medicine_category')), // Call the callback function
            )
        );
    
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('medicine_category'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $medicineCategory = $this->input->post("medicine_category");
    
            $data = array(
                'medicine_category' => $medicineCategory,
                'hospital_id' => $this->hospital_id,
                'store_id' => $this->store_id,
            );
    
            if (!empty($medicinecategoryid)) {
                $data['id'] = $medicinecategoryid;
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
    
            $insert_id = $this->medicine_category_model->addMedicineCategory($data);
        }
    
        echo json_encode($array);
    }
    
    public function valid_medicine_category($medicine_category)
    {
        $medicinecategoryid = $this->input->post("medicinecategoryid");
        $hospital_id = $this->hospital_id; // Assuming this is set in the controller
        $store_id = $this->store_id;       // Assuming this is set in the controller
    
        if (!isset($medicinecategoryid)) {
            $medicinecategoryid = 0; // Default value if no ID is provided
        }
    
        $exists = $this->medicine_category_model->check_medicine_category_exists(
            $medicine_category,
            $medicinecategoryid,
            $hospital_id,
            $store_id
        );
    
        if ($exists) {
            $this->form_validation->set_message('check_exists', $this->lang->line('record_already_exists'));
            return false;
        }
    
        return true;
    }
    
public function addsupplier()
{
    $suppliercategoryid = $this->input->post("suppliercategoryid");
    $this->form_validation->set_rules(
        'supplier_category',
        $this->lang->line('supplier') . " " . $this->lang->line('name'),
        'required|callback_check_exists' // Use a callback for custom validation
    );

    if ($this->form_validation->run() == false) {
        $msg = array(
            'supplier_category'       => form_error('supplier_category'),
            'contact'                 => form_error('contact'),
            'supplier_person'         => form_error('supplier_person'),
            'supplier_person_contact' => form_error('supplier_person_contact'),
            'address'                 => form_error('address'),
        );
        $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    } else {
        $supplierCategory = $this->input->post("supplier_category");
        $contact = $this->input->post('contact');
        $supplierperson = $this->input->post('supplier_person');
        $supplierpersoncontact = $this->input->post('supplier_person_contact');
        $address = $this->input->post('address');
        $supplierTypeId = $this->input->post("supplier_type_id"); // Get selected supplier type

        $data = array(
            'supplier_category' => $supplierCategory,
            'contact' => $contact,
            'supplier_person' => $supplierperson,
            'supplier_person_contact' => $supplierpersoncontact,
            'address' => $address,
            'hospital_id' => $this->hospital_id,
            'store_id' => $this->store_id,
            'supplier_type_id' => $supplierTypeId, // Store supplier type
        );

        if (!empty($suppliercategoryid)) {
            $data['id'] = $suppliercategoryid;
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        } else {
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        $insert_id = $this->medicine_category_model->addSupplierCategory($data);
    }
    echo json_encode($array);
}

/**
 * Custom callback for checking if a supplier category exists.
 */
public function check_exists($supplier_category)
{
    $suppliercategoryid = $this->input->post('suppliercategoryid');
    $hospital_id = $this->hospital_id; // Assuming these are set in the controller
    $store_id = $this->store_id;

    if (!isset($suppliercategoryid)) {
        $suppliercategoryid = 0; // Default value if no ID is provided
    }

    $exists = $this->medicine_category_model->check_category_existssupplier(
        $supplier_category,
        $suppliercategoryid,
        $hospital_id,
        $store_id
    );

    if ($exists) {
        $this->form_validation->set_message('check_exists', $this->lang->line('record_already_exists'));
        return false;
    }

    return true;
}




public function get()
{
    header('Content-Type: application/json');
    echo json_encode($this->medicine_category_model->getall($this->hospital_id, $this->store_id));
}

public function edit($id)
{
    $result = $this->medicine_category_model->getMedicineCategory($id, $this->hospital_id, $this->store_id);
    $data["result"] = $result;
    $data["title"] = "Edit Category";
    $data["medicineCategory"] = $this->medicine_category_model->getMedicineCategory(null, $this->hospital_id, $this->store_id);

    $this->load->view("layout/header");
    $this->load->view("admin/pharmacy/medicine_category", $data);
    $this->load->view("layout/footer");
}

public function delete($id)
{
    $this->medicine_category_model->delete($id, $this->hospital_id);
    redirect('admin/medicinecategory/medicine');
}

public function deletesupplier($id)
{
    $this->medicine_category_model->deletesupplier($id, $this->hospital_id);
    redirect('admin/medicinecategory/supplier');
}

public function get_data($id)
{
    $result = $this->medicine_category_model->getMedicineCategory($id, $this->hospital_id, $this->store_id);
    echo json_encode($result);
}

public function get_datasupplier($id)
{
    $result = $this->medicine_category_model->getSupplierCategory($id, $this->hospital_id, $this->store_id);
    echo json_encode($result);
}

}
