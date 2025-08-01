<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Printing extends Hospital_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        // Get session data
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
    }

    public function index()
    {

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'opdpre', $this->hospital_id);
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/opdpresprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function ipdprinting()
    {
        if (!$this->rbac->hasPrivilege('ipd_bill_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/ipdprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ipd');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/ipdprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function summaryprinting()
    {
        if (!$this->rbac->hasPrivilege('discharged_summary_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/summaryprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'summary');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/summaryprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function opdprinting()
    {
        if (!$this->rbac->hasPrivilege('opd_bill_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/opdprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'opd');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/opdprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function ipdpresprinting()
    {
        if (!$this->rbac->hasPrivilege('ipd_prescription_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/ipdpresprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ipdpres');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/ipdpresprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function birthprinting()
    {
        if (!$this->rbac->hasPrivilege('birth_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/birthprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'birth');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/birthprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function deathprinting()
    {
        if (!$this->rbac->hasPrivilege('death_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/deathprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'death');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/deathprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function pathologyprinting()
    {
        if (!$this->rbac->hasPrivilege('pathology_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/pathologyprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'pathology');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/pathologyprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function radiologyprinting()
    {
        if (!$this->rbac->hasPrivilege('radiology_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/radiologyprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'radiology');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/radiologyprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function otprinting()
    {
        if (!$this->rbac->hasPrivilege('ot_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/otprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ot');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/otprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function pharmacyprinting()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/pharmacyprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'pharmacy');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/pharmacyprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function bloodbankprinting()
    {
        if (!$this->rbac->hasPrivilege('bloodbank_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/bloodbankprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'bloodbank');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/bloodbankprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function ambulanceprinting()
    {
        if (!$this->rbac->hasPrivilege('ambulance_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/ambulanceprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ambulance');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/ambulanceprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function payslipprinting()
    {
        if (!$this->rbac->hasPrivilege('print_payslip_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/payslipprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'payslip');
        $this->load->view('layout/user/header');
        $this->load->view('admin/printing/payslipprinting', $data);
        $this->load->view('layout/user/footer');
    }

    public function getRecord($id)
    {
        $result = $this->printing_model->get($id, '');
        echo json_encode($result);
    }

    public function add()
    {
        $this->form_validation->set_rules(
            'categories',
            'Categories',
            'required|callback_validate_categories',
            array(
                'required' => 'The Categories field is required.',
                'validate_categories' => 'Invalid format for Categories. Please enter valid tags.'
            )
        );
    
        $this->form_validation->set_rules('print_header', 'Print Header', 'callback_handle_upload');
    
        if ($this->form_validation->run() == false) {
            $msg = array(
                'print_header' => form_error('print_header'),
                'categories'   => form_error('categories'),
            );
    
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            // Retrieve categories from input
            $categories = $this->input->post('categories');
    
            // Convert JSON array to string (Tagify sends JSON by default)
            if (!empty($categories)) {
                $categories = json_encode($categories); // Store as JSON in DB
            }
    
            $insertData = array(
                'print_footer' => $this->input->post('print_footer'),
                'setting_for'  => $this->input->post('setting_for'),
                'categories'   => $categories, // Save categories
                'is_active'    => 'yes',
                'hospital_id'  => $this->hospital_id,
            );
    
            $insert_id = $this->printing_model->add($insertData);
    
            // Handle file upload
            if (isset($_FILES["print_header"]) && !empty($_FILES['print_header']['name'])) {
                $fileInfo = pathinfo($_FILES["print_header"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["print_header"]["tmp_name"], "./uploads/printing/" . $img_name);
    
                $img_data = array('id' => $insert_id, 'print_header' => 'uploads/printing/' . $img_name);
                $this->printing_model->add($img_data);
            }
    
            $msg   = "Record Added Successfully";
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
    
        echo json_encode($array);
    }
    
    public function validate_categories($categories)
    {
        if (empty($categories)) {
            $this->form_validation->set_message('validate_categories', 'The Categories field is required.');
            return false;
        }
    
        // Check if it's valid JSON (Tagify sends JSON)
        json_decode($categories);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->form_validation->set_message('validate_categories', 'Invalid format for Categories. Please enter valid tags.');
            return false;
        }
    
        return true;
    }
    
    public function update()
    {
        $id = $this->input->post('printid');
        
        if (empty($id)) {
            $msg = array(
                'print_header' => form_error('print_header'),
            );
    
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id = $this->input->post('printid');
    
            // Retrieve categories from input
            $categories = $this->input->post('categories');
    
            // Convert JSON array to string (Tagify sends JSON by default)
            if (!empty($categories)) {
                $categories = json_encode($categories); // Store as JSON in DB
            }
    
            $updateData = array(
                'id'           => $id,
                'print_footer' => $this->input->post('print_footer'),
                'categories'   => $categories, // Save categories
                'is_active'    => 'yes',
                'hospital_id'  => $this->hospital_id
            );
    // print_r($updateData);exit;
            $this->printing_model->add($updateData);
    
            // Handle file upload
            if (isset($_FILES["print_header"]) && !empty($_FILES['print_header']['name'])) {
                $this->form_validation->set_rules('print_header', 'Print Header', 'callback_handle_upload');
                if ($this->form_validation->run() == false) {
                    $msg = array(
                        'print_header' => form_error('print_header'),
                    );
    
                    $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                } else {
                    $fileInfo = pathinfo($_FILES["print_header"]["name"]);
                    $img_name = $id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["print_header"]["tmp_name"], "./uploads/printing/" . $img_name);
    
                    $img_data = array('id' => $id, 'print_header' => 'uploads/printing/' . $img_name);
                    $this->printing_model->update($id, $img_data);
                }
            }
    
            $msg   = "Record Updated Successfully";
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
    
        echo json_encode($array);
    }
    

    public function delete($id)
    {
        if (!empty($id)) {
            $this->printing_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'success', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function handle_upload()
    {
        if (isset($_FILES["print_header"]) && !empty($_FILES['print_header']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png', "pdf", "doc", "docx", "rar", "zip");
            $temp        = explode(".", $_FILES["print_header"]["name"]);
            $extension   = end($temp);
            if ($_FILES["print_header"]["error"] > 0) {
                $error .= $this->lang->line('error_opening_the_file') . "<br />";
            }
            if (($_FILES["print_header"]["type"] != "application/pdf") && ($_FILES["print_header"]["type"] != "image/gif") && ($_FILES["print_header"]["type"] != "image/jpeg") && ($_FILES["print_header"]["type"] != "image/jpg") && ($_FILES["print_header"]["type"] != "application/vnd.openxmlformats-officedocument.wordprocessingml.document") && ($_FILES["print_header"]["type"] != "application/vnd.openxmlformats-officedocument.wordprocessingml.document") && ($_FILES["print_header"]["type"] != "image/pjpeg") && ($_FILES["print_header"]["type"] != "image/x-png") && ($_FILES["print_header"]["type"] != "application/x-rar-compressed") && ($_FILES["print_header"]["type"] != "application/octet-stream") && ($_FILES["print_header"]["type"] != "application/zip") && ($_FILES["print_header"]["type"] != "application/octet-stream") && ($_FILES["print_header"]["type"] != "image/png")) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            if (!in_array(strtolower($extension), $allowedExts)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            return true;
        } else {
            $this->form_validation->set_message('handle_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
    }

}
