<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pharmacy extends Hospital_Controller
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
    public function index($stock = null)
    {
        // if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
        //     access_denied();
        // }
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $resultlist               = $this->pharmacy_model->searchFullText();
        $i                        = 0;
        foreach ($resultlist as $value) {
            $pharmacy_id                 = $value['id'];
            $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
            $totalAvailableQty           = $available_qty['total_qty'];
            $resultlist[$i]["total_qty"] = $totalAvailableQty;
            $i++;
        }
        $result             = $this->pharmacy_model->getPharmacy();
        $data['resultlist'] = $resultlist;
        $data['result']     = $result;
        $data['stock']     = $stock;
        //   echo "<pre>";  print_r($data);exit;
        $this->load->view('layout/user/header');
        $this->load->view('store/pharmacy/index', $data);
        $this->load->view('layout/user/footer');
    }

    public function add()
    {
        $session_data = $this->session->userdata('hospital'); // Get session data
        $user_id      = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object
        $hospital_id   = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $store_id      = isset($user_details->store_id) ? $user_details->store_id : null;
        // Validation Rules
        $this->form_validation->set_rules(
            'medicine_name',
            $this->lang->line('medicine') . " " . $this->lang->line('name'),
            array('required', array('check_exists', array($this->medicine_category_model, 'valid_medicine_name')))
        );
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'required');
        $this->form_validation->set_rules('medicine_company', $this->lang->line('medicine') . " " . $this->lang->line('company'), 'required');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');
        $this->form_validation->set_rules('unit_packing', $this->lang->line('unit') . "/" . $this->lang->line('packing'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('image'));

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_name'        => form_error('medicine_name'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'medicine_company'     => form_error('medicine_company'),
                'unit'                 => form_error('unit'),
                'unit_packing'         => form_error('unit_packing'),
                'file'                 => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            // Generate System Barcode
            $barcode = strtoupper(substr($this->input->post('medicine_name'), 0, 3)) . time();

            // Prepare Pharmacy Data
            $pharmacy = array(
                'medicine_name'        => $this->input->post('medicine_name'),
                'medicine_category_id' => $this->input->post('medicine_category_id'),
                'medicine_company'     => $this->input->post('medicine_company'),
                'medicine_composition' => $this->input->post('medicine_composition'),
                'medicine_group'       => $this->input->post('medicine_group'),
                'unit'                 => $this->input->post('unit'),
                'min_level'            => $this->input->post('min_level'),
                'reorder_level'        => $this->input->post('reorder_level'),
                'vat'                  => $this->input->post('vat'),
                'unit_packing'         => $this->input->post('unit_packing'),
                'supplier'             => $this->input->post('supplier'),
                'note'                 => $this->input->post('note'),
                'vat_ac'               => $this->input->post('vat_ac'),
                'expiry_is_optional'   => $this->input->post('expiry_is_optional') ?? 'n', // Default to 'n'
                'expiry_date_min'      => $this->input->post('expiry_date_min'),
                'open_stock'           => $this->input->post('open_stock') ?? 0, // Default to 0
                'barcode'              => $barcode, // Generated barcode
                'hospital_id' => $hospital_id,
                'store_id' => $store_id,
                'user_id' => $user_id
            );

            // Insert Data into Database
            $insert_id = $this->pharmacy_model->add($pharmacy);

            // Handle File Upload
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'medicine_image' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->update($data_img);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            // Validate if required keys exist
            if (!isset($_FILES['file']['tmp_name']) || empty($_FILES['file']['tmp_name'])) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            $file_type         = $_FILES["file"]['type'] ?? '';
            $file_size         = $_FILES["file"]["size"] ?? 0;
            $file_name         = $_FILES["file"]["name"] ?? '';
            $allowed_extension = $image_validate['allowed_extension'] ?? [];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'] ?? [];

            if ($files = @getimagesize($_FILES['file']['tmp_name'])) {
                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if ($file_size > ($image_validate['upload_size'] ?? 0)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format(($image_validate['upload_size'] ?? 0) / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }

        return true; // No file uploaded, consider it valid
    }

    public function dt_search($stock = null)
    {

        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $where_condition['hospital_id'] = $this->hospital_id; // Assuming $this->hospital_id holds the value for hospital ID
        $where_condition['store_id'] = $this->store_id; // Assuming $this->store_id holds the value for store ID

        // Calling the model function with the updated where_condition
        $resultlist = $this->pharmacy_model->search_datatable($where_condition,$stock);
        // echo $this->db->last_query();exit;
        $total_result = $this->pharmacy_model->search_datatable_count($where_condition,$stock);
        $data         = array();
        $total_qty = 0;
        $total_purchase = 0;
        $total_sale = 0;

        foreach ($resultlist as $result_key => $result_value) {
            $status = "";


            $nestedData = array();
            $action     = "<div class='rowoptionview'>";
            // $action .= "<a href='#' onclick='viewDetail(" . $result_value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
            // if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_add')) {
            //     $action .= "<a href='#' class='btn btn-default btn-xs' onclick='addbadstock(" . $result_value->id . ")' data-toggle='tooltip' title='" . $this->lang->line('add') . ' ' . $this->lang->line('bad') . ' ' . $this->lang->line('stock') . "' > <i class='fas fa-minus-square'></i> </a>";
            // }


            $action .= "<div'>";

            $nestedData[] = $result_value->medicine_name . $action;
            $nestedData[] = $result_value->medicine_company;
            $nestedData[] = $result_value->medicine_category;
            $nestedData[] = $result_value->total_qty;


            $data[]       = $nestedData;
        }





        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }
    public function getDetails()
    {
        // if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
        //     access_denied();
        // }
        $id     = $this->input->post("pharmacy_id");
        $result = $this->pharmacy_model->getDetails($id);
        echo json_encode($result);
    }

    public function update()
    {
        $session_data = $this->session->userdata('hospital'); // Get session data
        $user_id      = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object
        $hospital_id   = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $store_id      = isset($user_details->store_id) ? $user_details->store_id : null;

        // Validation Rules
        $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine') . " " . $this->lang->line('category') . " " . $this->lang->line('id'), 'required');
        $this->form_validation->set_rules('medicine_company', $this->lang->line('medicine') . " " . $this->lang->line('company'), 'required');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');
        $this->form_validation->set_rules('unit_packing', $this->lang->line('unit') . "/" . $this->lang->line('packing'), 'required');
        $this->form_validation->set_rules('medicine_image', $this->lang->line('image'));

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_name'        => form_error('medicine_name'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'medicine_company'     => form_error('medicine_company'),
                'unit'                 => form_error('unit'),
                'unit_packing'         => form_error('unit_packing'),
                'medicine_image'       => form_error('medicine_image'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id = $this->input->post('id');

            // Prepare Pharmacy Data
            $pharmacy = array(
                'id'                   => $id,
                'medicine_name'        => $this->input->post('medicine_name'),
                'medicine_category_id' => $this->input->post('medicine_category_id'),
                'medicine_company'     => $this->input->post('medicine_company'),
                'medicine_composition' => $this->input->post('medicine_composition'),
                'medicine_group'       => $this->input->post('medicine_group'),
                'unit'                 => $this->input->post('unit'),
                'min_level'            => $this->input->post('min_level'),
                'reorder_level'        => $this->input->post('reorder_level'),
                'vat'                  => $this->input->post('vat'),
                'unit_packing'         => $this->input->post('unit_packing'),
                'note'                 => $this->input->post('edit_note'),
                'vat_ac'               => $this->input->post('vat_ac'),
                'expiry_is_optional'   => $this->input->post('expiry_is_optional') ?? 'n', // Default to 'n'
                'expiry_date_min'      => $this->input->post('expiry_date_min'),
                'open_stock'           => $this->input->post('open_stock') ?? 0, // Default to 0
                'barcode'              => $this->input->post('barcode'), // Preserve existing barcode
                'hospital_id'          => $hospital_id,
                'store_id'             => $store_id,
                'user_id'              => $user_id
            );

            // Update Data in Database
            $this->pharmacy_model->update($pharmacy);

            // Handle File Upload
            if (isset($_FILES["medicine_image"]) && !empty($_FILES['medicine_image']['name'])) {
                $fileInfo = pathinfo($_FILES["medicine_image"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["medicine_image"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $id, 'medicine_image' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->update($data_img);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }


    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_medicine_sample_file.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_medicine_sample_file.csv';

        force_download($name, $data);
    }

    public function import()
    {
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('file'));

        $medicineCategory = $this->medicine_category_model->getMedicineCategory(null, $this->hospital_id, $this->store_id);
        $data["medicineCategory"] = $medicineCategory;
        $fields = array('medicine_name', 'medicine_company', 'medicine_composition', 'medicine_group', 'unit', 'min_level', 'reorder_level', 'vat', 'unit_packing', 'note');
        $data["fields"] = $fields;

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_category_id' => form_error('medicine_category_id'),
                'file' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            $this->load->view('layout/user/header');
            $this->load->view('store/pharmacy/import', $data);
            $this->load->view('layout/user/footer');
        } else {
            $medicine_category_id = $this->input->post('medicine_category_id');
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];

                    $result = $this->csvreader->parse_file($file);
                    if (!empty($result)) {
                        $count = 0;
                        for ($i = 1; $i <= count($result); $i++) {
                            $medicine_data[$i] = array();
                            $n = 0;
                            foreach ($result[$i] as $key => $value) {
                                $medicine_data[$i][$fields[$n]] = mb_convert_encoding($result[$i][$key], 'UTF-8', 'auto');
                                $medicine_data[$i]['is_active'] = 'yes';
                                $medicine_data[$i]['medicine_category_id'] = $medicine_category_id;
                                $medicine_data[$i]['hospital_id'] = $this->hospital_id;
                                $medicine_data[$i]['store_id'] = $this->store_id;
                                $n++;
                            }

                            $medicine_name = $medicine_data[$i]["medicine_name"];

                            // Check if barcode exists, otherwise generate one
                            if (!isset($medicine_data[$i]['barcode']) || empty($medicine_data[$i]['barcode'])) {
                                $medicine_data[$i]['barcode'] = strtoupper(substr($medicine_name, 0, 3)) . time() . rand(100, 999);
                            }

                            $barcode = $medicine_data[$i]['barcode']; // Use the barcode field

                            if (!empty($medicine_name)) {
                                if ($this->pharmacy_model->check_medicine_exists($medicine_name, $medicine_category_id, $this->hospital_id, $this->store_id)) {
                                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');
                                    $insert_id = "";
                                } else {
                                    $insert_id = $this->pharmacy_model->addImport($medicine_data[$i]);
                                }
                            }

                            if (!empty($insert_id)) {
                                $data['csvData'] = $result;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');
                                $count++;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">Total ' . count($result) . " records found in CSV file. Total " . $count . ' records imported successfully.</div>');
                            } else {
                                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');
                            }
                        }
                    }
                }
                redirect('hospital/pharmacy/import');
            }
        }
    }

    public function purchase()
    {
        // if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
        //     access_denied();
        // }
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"] = $supplierCategory;

        $resultlist = $this->pharmacy_model->getSupplier($this->hospital_id, $this->store_id);
        $i          = 0;
        foreach ($resultlist as $value) {
            $pharmacy_id                 = $value['id'];
            $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
            $totalAvailableQty           = $available_qty['total_qty'];
            $resultlist[$i]["total_qty"] = $totalAvailableQty;
            $i++;
        }
        $result             = $this->pharmacy_model->getPharmacy(null, $this->hospital_id, $this->store_id);
        $data['resultlist'] = $resultlist;
        $data['result']     = $result;
        $this->load->view('layout/user/header');
        $this->load->view('store/pharmacy/purchase.php', $data);
        $this->load->view('layout/user/footer');
    }

    public function purchaseMedicine()
    {
        $data['supplierTypes'] = $this->supplier_type_model->getSupplierTypes($this->hospital_id, $this->store_id);

        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory(null, $this->hospital_id, $this->store_id);
        $data["supplierCategory"] = $supplierCategory;

        $resultlist = $this->pharmacy_model->getSupplier();
        $i          = 0;
        foreach ($resultlist as $value) {
            $pharmacy_id                 = $value['id'];
            $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
            $totalAvailableQty           = $available_qty['total_qty'];
            $resultlist[$i]["total_qty"] = $totalAvailableQty;
            $i++;
        }
        $result             = $this->pharmacy_model->getPharmacy();
        // $data['resultlist'] = $resultlist;
        $data['result']     = $result;
        $this->load->view('layout/user/header');
        $this->load->view('store/pharmacy/purchaseMedicine.php', $data);
        $this->load->view('layout/user/footer');
    }

    public function supplierDetails()
    {

        // if (!$this->rbac->hasPrivilege('medicine_supplier', 'can_view')) {
        //     access_denied();
        // }
        $id   = $this->input->post("id");
        $data = $this->patient_model->supplierDetails($id);
        echo json_encode($data);
    }
    public function get_medicine_name()
    {
        // if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
        //     access_denied();
        // }
        $medicine_category_id = $this->input->post("medicine_category_id");
        $data                 = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        echo json_encode($data);
    }
    public function getBatchNoList()
    {
        $medicine = $this->input->get_post('medicine');
        $result   = $this->pharmacy_model->getBatchNoList($medicine, $this->hospital_id, $this->store_id);
        echo json_encode($result);
    }

    public function getStoreBatchNoList()
    {
        $medicine = $this->input->get_post('medicine');
        $role = $this->session->userdata('hospital')['role'];

        // Step 1: Get role_id for "Chief Pharmacist"
        $this->db->select('id');
        $this->db->from('roles');
        $this->db->where('name', 'Chief Pharmacist');
        $roleData = $this->db->get()->row();

        $store_id = $this->store_id; // default

        if ($roleData) {
            $chiefPharmacistRoleId = $roleData->id;

            // Step 2: Get store_id from users table
            $this->db->select('store_id');
            $this->db->from('users');
            $this->db->where('hospital_id', $this->hospital_id);
            $this->db->where('role_id', $chiefPharmacistRoleId);
            $chiefPharmacist = $this->db->get()->row();

            if ($chiefPharmacist && $chiefPharmacist->store_id) {
                $store_id = $chiefPharmacist->store_id;
            }
        }

        // Step 3: Call model function with the resolved store_id
        $result = $this->pharmacy_model->getStoreBatchNoList($medicine, $this->hospital_id, $store_id, $role);
        echo json_encode($result);
    }

    public function addBillSupplier()
    {

        // if (!$this->rbac->hasPrivilege('medicine_supplier', 'can_add')) {
        //     access_denied();
        // }
        $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
        $status = $isDraft ? 'draft' : 'final';

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expiry_date[]', $this->lang->line('expiry') . " " . $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase') . " " . $this->lang->line('price'), 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'supplier_id'          => form_error('supplier_id'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'batch_no'             => form_error('batch_no[]'),
                'expiry_date'          => form_error('expiry_date[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'total'                => form_error('total'),
                'amount'               => form_error('amount[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $supplier_id   = $this->input->post('supplier_id');
            $supplier_name = $this->input->post('supplier_name');
            $bill_date     = $this->input->post("date");
            $purchase_no   = $this->pharmacy_model->getMaxId();
            if (empty($purchase_no)) {
                $purchase_no = 0;
            }
            $purchase = $purchase_no + 1;

            $data = array(
                'date'          => date('Y-m-d H:i:s', strtotime($bill_date)),
                'supplier_id'   => $supplier_id,
                'supplier_type_id' => $this->input->post('supplier_type_id'),
                'supplier_name' => $supplier_name,
                'invoice_no'    => $this->input->post('invoiceno'),
                'purchase_no'   => $purchase,
                'total'         => $this->input->post('total'),
                'net_amount'    => 0,
                'note'          => $this->input->post('note'),
                'hospital_id'   => $this->hospital_id,
                'store_id'      => $this->store_id,
                'user_id'       => $this->user_id,
                'bill_status' => $status
            );

            $insert_id = $this->pharmacy_model->addBillSupplier($data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'file' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->addBillSupplier($data_img);
            }

            if ($insert_id) {
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $expiry_date          = $this->input->post('expiry_date');
                $batch_no             = $this->input->post('batch_no');
                $batch_amount         = $this->input->post('batch_amount');
                $mrp                  = $this->input->post('mrp');
                $sale_rate            = $this->input->post('sale_rate');
                $packing_qty          = $this->input->post('packing_qty');
                $quantity             = $this->input->post('quantity');
                $purchase_price       = $this->input->post('purchase_price');
                $amount               = $this->input->post('amount');

                $data1 = array();
                $j     = 0;
                foreach ($medicine_name as $key => $mvalue) {

                    $expdate = $expiry_date[$j];

                    $explore = explode("/", $expdate);

                    $monthary = $explore[0];
                    $yearary  = $explore[1];
                    $month    = $monthary;

                    $month_number = $this->convertMonthToNumber($month);
                    $insert_date  = $yearary . "-" . $month_number . "-01";

                    $details = array(
                        'inward_date'            => date('Y-m-d H:i:s', strtotime($bill_date)),
                        'pharmacy_id'            => $medicine_name[$j],
                        'supplier_bill_basic_id' => $insert_id,
                        'medicine_category_id'   => $medicine_category_id[$j],
                        'expiry_date'            => $expiry_date[$j],
                        'expiry_date_format'     => $insert_date,
                        'batch_no'               => $batch_no[$j],
                        'quantity'               => $quantity[$j],
                        'purchase_price'         => $purchase_price[$j],
                        'available_quantity'     => $quantity[$j],
                        'amount'                 => $amount[$j],
                        'hospital_id'   => $this->hospital_id,
                        'store_id'      => $this->store_id,
                        'user_id'       => $this->user_id,
                        'bill_status' => $status

                    );
                    $data1[] = $details;

                    $j++;
                }

                $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
            } else {
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
        }
        echo json_encode($array);
    }
    public function getExpiryDate()
    {
        $batch_no = $this->input->get_post('batch_no');
        $med_id   = $this->input->get_post('med_id');
        $result   = $this->pharmacy_model->getExpiryDate($batch_no, $med_id);
        echo json_encode($result);
    }
    public function convertMonthToNumber($monthName)
    {
        return date('m', strtotime($monthName));
    }

    public function getSupplierDetails($id)
    {
        // if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
        //     access_denied();
        // }
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result         = $this->pharmacy_model->getSupplierDetails($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail'] = $detail;
        $this->load->view('store/pharmacy/printPurchase', $data);
    }

    public function getStockDetails($id)
    {
        // if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
        //     access_denied();
        // }
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result         = $this->pharmacy_model->getBillDetail($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail'] = $detail;
        // print_r($data);exit;
        $this->load->view('store/pharmacy/printStockPurchase', $data);
    }

    public function stockTransfer()
    {
        // if (!$this->rbac->hasPrivilege('pharmacy bill', 'can_view')) {
        //     access_denied();
        // }
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $this->load->view('layout/user/header');
        $this->load->view('store/pharmacy/stockTransfer.php', $data);
        $this->load->view('layout/user/footer');
    }
    public function getindate()
    {
        $purchase_id = $this->input->post("purchase_id");
        $result      = $this->pharmacy_model->getindate($purchase_id);
        echo json_encode($result);
    }
    public function editSupplierBillPage($id)
    {
        $data['supplierTypes'] = $this->supplier_type_model->getSupplierTypes($this->hospital_id, $this->store_id);

        // Fetch necessary data
        $medicineCategory = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"]     = $medicineCategory;
        $medicine_category_id         = $this->input->post("medicine_category_id");
        $data['medicine_category_id'] = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        $data['medicine_category_id'] = $medicine_category_id;


        $supplier_category_id         = $this->input->post("supplier_category_id");
        $data['supplier_category_id'] = $this->pharmacy_model->get_supplier_name($supplier_category_id);
        $data['supplier_category_id'] = $supplier_category_id;

        $result         = $this->pharmacy_model->getSupplierDetails($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['supplier_select'] = $data['result']['supplier_id'];
        $data['detail'] = $detail;
        $supplier_type_id = isset($data['result']['supplier_type_id']) ? $data['result']['supplier_type_id'] : null;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory(null, $this->hospital_id, $this->store_id, $supplier_type_id);
        $data["supplierCategory"]     = $supplierCategory;
        $data['supplier_type_id'] = $supplier_type_id;
        // Load the edit view (full page)
        $this->load->view('layout/user/header');
        $this->load->view("store/pharmacy/editSupplierBill", $data);
        $this->load->view('layout/user/footer');
    }
    public function getExpireDate()
    {
        $batch_no = $this->input->get_post('batch_no');
        $result = $this->pharmacy_model->getExpireDate($batch_no);
        echo json_encode($result);
    }
    public function getBatchExpireDate()
    {
        $batch_no = $this->input->get_post('batch_no');
        $result = $this->pharmacy_model->getBatchExpireDate($batch_no);
        echo json_encode($result);
    }
     public function getBatchQuantity()
    {

        $batch_no = "";
        $batch_id = $this->input->get('batch_no');
        $med_id   = $this->input->get('med_id');
        $data     = $this->pharmacy_model->getQuantity($batch_no, $med_id, $this->hospital_id, $this->store_id,$batch_id);
        echo json_encode($data);
    }

    public function getQuantityedit()
    {


        $batch_no = $this->input->get('batch_no');
        $data = $this->pharmacy_model->getQuantityedit($batch_no, $this->hospital_id, $this->store_id);
        echo json_encode($data);
    }

    public function getStoreQuantityedit()
    {

        $batch_no = $this->input->get('med_id');
        $store_id = $this->input->get('store_id');
        $data = $this->pharmacy_model->getStoreQuantityedit($batch_no, $store_id);
        echo json_encode($data);
    }
    public function updateSupplierBill()
    {

        $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
        $status = $isDraft ? 'draft' : 'final';


        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expiry_date[]', $this->lang->line('expiry') . " " . $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase_price'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'supplier_id'          => form_error('supplier_id'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'expiry_date'          => form_error('expiry_date[]'),
                'batch_no'             => form_error('batch_no[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'total'                => form_error('total'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id               = $this->input->post('bill_basic_id');
            $bill_id          = $this->input->post("bill_detail_id[]");
            $previous_bill_id = $this->input->post("previous_bill_id[]");
            $supplier_id      = $this->input->post('supplier_id');
            $purchase_no      = $this->input->post('purchase_no');
            $data_array       = array();
            $delete_arr       = array();

            $bill_date = $this->input->post("date");
            $data      = array(
                'id'          => $id,
                'date'        => date('Y-m-d H:i:s', strtotime($bill_date)),
                'supplier_id' => $supplier_id,
                'supplier_type_id' => $this->input->post('supplier_type_id'),
                'purchase_no' => $purchase_no,
                'invoice_no'  => $this->input->post('invoice_no'),
                'total'       => $this->input->post('total'),
                'tax'         => $this->input->post('tax'),
                'note'        => $this->input->post('note'),
                'bill_status' => $status
            );

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $id, 'file' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->addBillSupplier($data_img);
            }
            $this->pharmacy_model->addBillSupplier($data);

            if (!empty($id)) {

                $bill_detail_id       = $this->input->post('bill_detail_id');
                $medicine_batch_id    = $this->input->post('medicine_batch_id');
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $expiry_date          = $this->input->post('expiry_date');
                $batch_no             = $this->input->post('batch_no');
                $quantity             = $this->input->post('quantity');
                $total_quantity       = $this->input->post('available_quantity');
                $amount               = $this->input->post('amount');
                $purchase_price       = $this->input->post('purchase_price');
                $data_array1          = array();
                $bill_date1           = $this->input->post("date");
                $j                    = 0;
                foreach ($medicine_category_id as $key => $value) {

                    if ($bill_id[$j] == 0) {
                        $add_data = array(
                            'supplier_bill_basic_id' => $id,
                            'medicine_category_id'   => $medicine_category_id[$j],
                            'pharmacy_id'            => $medicine_name[$j],
                            'inward_date'            => date('Y-m-d H:i:s', strtotime($bill_date1)),
                            'expiry_date'            => $expiry_date[$j],
                            'batch_no'               => $batch_no[$j],
                            'quantity'               => $quantity[$j],
                            'available_quantity'     => $quantity[$j],
                            'purchase_price'         => $purchase_price[$j],
                            'amount'                 => $amount[$j],
                            'hospital_id'   => $this->hospital_id,
                            'store_id'      => $this->store_id,
                            'user_id'       => $this->user_id,
                            'bill_status' => $status

                        );
                        $data_array[] = $add_data;
                    } else {

                        $detail = array(
                            'id'                     => $bill_detail_id[$j],
                            'supplier_bill_basic_id' => $id,
                            'medicine_category_id'   => $medicine_category_id[$j],
                            'pharmacy_id'            => $medicine_name[$j],
                            'inward_date'            => date('Y-m-d H:i:s', strtotime($bill_date1)),
                            'expiry_date'            => $expiry_date[$j],
                            'batch_no'               => $batch_no[$j],
                            'quantity'               => $quantity[$j],
                            'available_quantity'     => $quantity[$j],
                            'purchase_price'         => $purchase_price[$j],
                            'amount'                 => $amount[$j],
                            'hospital_id'   => $this->hospital_id,
                            'store_id'      => $this->store_id,
                            'user_id'       => $this->user_id,
                            'bill_status' => $status

                        );

                        $this->pharmacy_model->updateMedicineBatchDetail($detail);
                    }

                    $j++;
                }
            } else {
            }
            if (!empty($data_array)) {
                $this->pharmacy_model->addBillMedicineBatchSupplier($data_array);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function stockTransferAdd()
    {
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $role = $this->session->userdata('hospital')['role'];
        $entity = $role == 'Chief Pharmacist' ? '' : 'Department';
        $data['hospitalStores'] = $this->store_model->getHospitalDepartmentStores($entity, $this->hospital_id);
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $this->load->view('layout/user/header');
        $this->load->view('store/pharmacy/stockTransferAdd.php', $data);
        $this->load->view('layout/user/footer');
    }

    public function addBill()
    {
        // print_r($_POST);exit;
        $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
        $status = $isDraft ? 'draft' : 'final';

        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase_price'), 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        $this->form_validation->set_rules('store_id', $this->lang->line('store'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required');
        $this->form_validation->set_rules(
            'bill_no',
            $this->lang->line('bill') . " " . $this->lang->line('no'),
            'trim|required|callback_bill_no_unique_check'
        );

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'batch_no'             => form_error('batch_no[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'           => form_error('purchase_price[]'),
                'amount'               => form_error('amount[]'),
                'total'                => form_error('total'),
                'store_id'             => form_error('store_id'),
                'date'                 => form_error('date'),
                'indent_no' => form_error('bill_no'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id               = $this->input->post('bill_basic_id');
            $store_id   = $this->input->post('store_id'); // Target store ID
            $store_name = $this->input->post('store_name');
            $bill_date  = $this->input->post('date');
            $purchase_no = $this->pharmacy_model->getMaxId() ?? 0;
            $purchase = $purchase_no + 1;
            $role = $this->session->userdata('hospital')['role'];
            $store_details = $this->store_model->getStoreEntityType($store_id);

            $data = array(
                'date'             => date('Y-m-d H:i:s', strtotime($bill_date)),
                'store_name'       => $store_name,
                'invoice_no'       => $this->input->post('bill_no'),
                'purchase_no'      => $purchase,
                'total'            => $this->input->post('total'),
                'net_amount'       => 0,
                'note'             => $this->input->post('note'),
                'hospital_id'      => $this->hospital_id,
                'user_id'          => $this->user_id,
                'transfer_store_id' => $this->store_id, // Source store ID
                'bill_status' => $status

            );
            if ($store_details && $store_details['entity_type'] === 'hospital') {
                $data['store_id'] = $store_id;
            } else {
                $data['target_store_id'] = $store_id;
            }
            $insert_id = $this->pharmacy_model->addBillSupplier($data);

            if ($insert_id) {
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $expiry_date          = $this->input->post('expire_date');
                $batch_no             = $this->input->post('batch_no');
                $quantity             = $this->input->post('quantity');
                $amount               = $this->input->post('amount');
                $purchase_price               = $this->input->post('purchase_price');
                $total_quantity       = $this->input->post('available_quantity');
                $medicine_batch_details_id = $this->input->post('id');
                $data1 = array();
                $bill_detail_id       = $this->input->post('bill_detail_id');

                $j     = 0;
                foreach ($medicine_name as $key => $mvalue) {
                    if (!isset($expiry_date[$j]) || empty($expiry_date[$j])) {
                        $msg = 'Expiry date is missing for medicine: ' . $mvalue;
                        $array = array('status' => 'fail', 'error' => '', 'message' => $msg);
                        echo json_encode($array);
                        return; // Stop further processing
                    }

                    $expdate = $expiry_date[$j];
                    $explore = explode("/", $expdate);

                    if (count($explore) !== 2) {
                        $msg = 'Invalid expiry date format for medicine: ' . $mvalue;
                        $array = array('status' => 'fail', 'error' => '', 'message' => $msg);
                        echo json_encode($array);
                        return; // Stop further processing
                    }

                    $month_number = $this->convertMonthToNumber($explore[0]);
                    $insert_date = $explore[1] . "-" . $month_number . "-01";


                    $details = array(
                        'inward_date'         => date('Y-m-d H:i:s', strtotime($bill_date)),
                        'pharmacy_id'         => $medicine_name[$j],
                        'supplier_bill_basic_id' => $insert_id,
                        'medicine_category_id' => $medicine_category_id[$j],
                        'expiry_date'         => $expiry_date[$j],
                        'expiry_date_format'  => $insert_date,
                        'batch_no'            => $batch_no[$j],
                        'quantity'            => $quantity[$j],
                        'available_quantity'  => $quantity[$j],
                        'purchase_price'  => $purchase_price[$j],
                        'amount'              => $amount[$j],
                        'hospital_id'         => $this->hospital_id,
                        'user_id'             => $this->user_id,
                        'transfer_store_id'   => $this->store_id,
                        'bill_status' => $status
                    );
                    if ($store_details && $store_details['entity_type'] === 'hospital') {
                        $details['store_id'] = $store_id;
                    } else {
                        $details['target_store_id'] = $store_id;
                    }
                    if ($status == 'final') {
                        $available_quantity[$j] = $total_quantity[$j] - $quantity[$j];
                        $update_quantity = array(
                            'id'                 => $medicine_batch_details_id[$j],
                            'available_quantity' => $available_quantity[$j],
                        );
                        $this->pharmacy_model->availableQty($update_quantity);
                    }


                    $data1[] = $details;
                    $j++;
                }

                $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
        }
        echo json_encode($array);
    }

    public function editPharmacyBill($id)
    {

        $medicineCategory             = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"]     = $medicineCategory;
        $medicine_category_id         = $this->input->post("medicine_category_id");
        $data['medicine_category_id'] = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        $data['medicine_category_id'] = $medicine_category_id;
        $doctors                      = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]              = $doctors;
        $result                       = $this->pharmacy_model->getBillDetails($id);
        $data['result']               = $result;
        $detail                       = $this->pharmacy_model->getAllBillDetails($id);
        $data['detail']               = $detail;
        $this->load->view("admin/pharmacy/editPharmacyBill", $data);
    }

    public function editTransferBillPage($id)
    {
        $role = $this->session->userdata('hospital')['role'];
        $entity = $role == 'Chief Pharmacist' ? '' : 'Department';
        $data['hospitalStores'] = $this->store_model->getHospitalDepartmentStores($entity, $this->hospital_id);
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $result                       = $this->pharmacy_model->getBillDetail($id);
        $data['result']               = $result;
        $detail                       = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail']               = $detail;
        // echo "<pre>"; print_r($data);exit;
        $this->load->view('layout/user/header');
        $this->load->view("store/pharmacy/editTransferBillPage", $data);
        $this->load->view('layout/user/footer');
    }

    public function getQuantity()
    {

        $batch_no = $this->input->get('batch_no');
        $med_id   = $this->input->get('med_id');
        $data     = $this->pharmacy_model->getQuantity($batch_no, $med_id, $this->hospital_id, $this->store_id);
        echo json_encode($data);
    }

    public function getDepPharQuantity()
    {

        $batch_no = $this->input->get('batch_no');
        $med_id   = $this->input->get('med_id');
        $batch_id   = $this->input->get('batch_id');
        $role = $this->session->userdata('hospital')['role'];
        $data     = $this->pharmacy_model->getDepPharQuantity($batch_no, $med_id, $this->hospital_id, $this->store_id, $batch_id, $role);
        echo json_encode($data);
    }

    public function storeDetails()
    {
        $id   = $this->input->post("id");
        $data = $this->store_model->getStoreDetails($id);
        echo json_encode($data);
    }
    // public function dt_search()
    // {

    //     $draw            = $_POST['draw'];
    //     $row             = $_POST['start'];
    //     $rowperpage      = $_POST['length']; // Rows display per page
    //     $columnIndex     = $_POST['order'][0]['column']; // Column index
    //     $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
    //     $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    //     $where_condition = array();
    //     if (!empty($_POST['search']['value'])) {
    //         $where_condition = array('search' => $_POST['search']['value']);
    //     }
    //     $resultlist   = $this->pharmacy_model->search_datatable($where_condition);
    //     // echo $this->db->last_query();exit;
    //     $total_result = $this->pharmacy_model->search_datatable_count($where_condition);
    //     $data         = array();
    //     $total_qty = 0;
    //     $total_purchase = 0;
    //     $total_sale = 0;
    //     foreach ($resultlist as $result_key => $result_value) {
    //         $status = "";


    //         $nestedData = array();
    //         $action     = "<div class='rowoptionview'>";
    //         // $action .= "<a href='#' onclick='viewDetail(" . $result_value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
    //         // if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_add')) {
    //         //     $action .= "<a href='#' class='btn btn-default btn-xs' onclick='addbadstock(" . $result_value->id . ")' data-toggle='tooltip' title='" . $this->lang->line('add') . ' ' . $this->lang->line('bad') . ' ' . $this->lang->line('stock') . "' > <i class='fas fa-minus-square'></i> </a>";
    //         // }


    //         $action .= "<div'>";

    //         $nestedData[] = $result_value->medicine_name . $action;
    //         $nestedData[] = $result_value->medicine_company;
    //         $nestedData[] = $result_value->medicine_category;

    //         $data[]       = $nestedData;
    //     }





    //     $json_data = array(
    //         "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    //         "recordsTotal"    => intval($total_result), // total number of records
    //         "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
    //         "data"            => $data, // total data array
    //     );

    //     echo json_encode($json_data); // send data as json format

    // }

    public function bill_search()
    {
        // Get parameters from the request
        $draw            = isset($_POST['draw']) ? $_POST['draw'] : 0;
        $row             = isset($_POST['start']) ? $_POST['start'] : 0;
        $rowperpage      = isset($_POST['length']) ? $_POST['length'] : 10; // Rows display per page
        $columnIndex     = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0; // Column index
        $columnName      = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : 'id'; // Column name
        $columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc'; // asc or desc

        // Prepare search condition
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition['search'] = $_POST['search']['value'];
        }

        // Use internally managed hospital_id and store_id
        $hospital_id = $this->hospital_id;
        $store_id = null;
        $target_store_id = null;
        $session_data = $this->session->userdata('hospital');
        if ($session_data['role'] != 'Department Pharmacist') {
            $store_id    = $this->store_id;
        } else {
            $target_store_id = $this->store_id;
        }
        // print_r($session_data['role']);exit;

        // Fetch data and count
        $resultlist = $this->pharmacy_model->getStoreTransferStock(
            $where_condition,
            $hospital_id,
            $store_id,
            $target_store_id

        );
        $total_result = $this->pharmacy_model->getStoreTransferStockCount(
            $hospital_id,
            $store_id,
            $target_store_id
        );
        $data = array();
        // print_r($resultlist);exit;
        foreach ($resultlist as $result) {
            $action = '';
            $nestedData   = array();
            // $action       = "<div class='rowoptionview'>";
            $action .= "<a href='#' onclick='viewDetail(" . $result["id"] . ", \"" . $result["bill_status"] . "\")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

            // $action      .= "<a href='#' onclick='printDetail(" . $result->id . ",\"" . $result->bill_no . "\",\"" . $result->patient_id . "\")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
            // $action      .= "</div>";

            // Add row data
            $nestedData[] = $result['purchase_no'];
            $nestedData[] = $result['invoice_no'];
            $nestedData[] = $result['date'];
            $nestedData[] = $result['store_name'];
            $nestedData[] = number_format($result['total'], 2);

            $nestedData[] = $action;
            $data[]       = $nestedData;
        }

        // Prepare JSON response
        $json_data = array(
            "draw"            => intval($draw), // Draw number for DataTables
            "recordsTotal"    => intval($total_result), // Total number of records
            "recordsFiltered" => intval($total_result), // Total number of filtered records
            "data"            => $data, // Data for the table
        );

        echo json_encode($json_data); // Return response in JSON format
    }


    public function get_medicine_qty()
    {
        $id = $this->input->post('medicine_id');
        $resultlist = $this->pharmacy_model->store_medicine_qty($id, $this->hospital_id, $this->store_id);

        // Check if the resultlist is not empty
        if (!empty($resultlist)) {
            // Get total_qty from the first object
            $total_qty = $resultlist[0]->total_qty;

            // Send response with total_qty
            echo json_encode(['status' => true, 'total_qty' => $total_qty]);
        } else {
            // Handle the case where no data is found
            echo json_encode(['status' => false, 'error' => 'No data found']);
        }
    }

    public function getBillDetails($id)
    {

        $print_details         = $this->printing_model->get('', 'pharmacy');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result         = $this->pharmacy_model->getBillDetails($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllBillDetails($id);
        $data['detail'] = $detail;
        print_r($data);
        exit;
        $this->load->view('admin/pharmacy/printBill', $data);
    }

    public function patientDetails()
    {


        $id   = $this->input->post("id");
        $data = $this->patient_model->patientDetails($id);
        echo json_encode($data);
    }
    public function addOpeningStock()
    {

        // if (!$this->rbac->hasPrivilege('medicine_supplier', 'can_add')) {
        //     access_denied();
        // }
        $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
        $status = $isDraft ? 'draft' : 'final';


        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expiry_date[]', $this->lang->line('expiry') . " " . $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase') . " " . $this->lang->line('price'), 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'batch_no'             => form_error('batch_no[]'),
                'expiry_date'          => form_error('expiry_date[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'total'                => form_error('total'),
                'amount'               => form_error('amount[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $supplier_id   = $this->input->post('supplier_id');
            $supplier_name = $this->input->post('supplier_name');
            $bill_date     = $this->input->post("date");
            $purchase_no   = $this->pharmacy_model->getMaxId();
            if (empty($purchase_no)) {
                $purchase_no = 0;
            }
            $purchase = $purchase_no + 1;

            $data = array(
                'date'          => date('Y-m-d H:i:s', strtotime($bill_date)),
                'invoice_no'    => $this->input->post('invoiceno'),
                'purchase_no'   => $purchase,
                'total'         => $this->input->post('total'),
                'net_amount'    => 0,
                'note'          => $this->input->post('note'),
                'hospital_id'   => $this->hospital_id,
                'target_store_id'      => $this->store_id,
                'user_id'       => $this->user_id,
                'bill_status' => $status
            );

            $insert_id = $this->pharmacy_model->addBillSupplier($data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'file' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->addBillSupplier($data_img);
            }

            if ($insert_id) {
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $expiry_date          = $this->input->post('expiry_date');
                $batch_no             = $this->input->post('batch_no');
                $batch_amount         = $this->input->post('batch_amount');
                $mrp                  = $this->input->post('mrp');
                $sale_rate            = $this->input->post('sale_rate');
                $packing_qty          = $this->input->post('packing_qty');
                $quantity             = $this->input->post('quantity');
                $purchase_price       = $this->input->post('purchase_price');
                $amount               = $this->input->post('amount');

                $data1 = array();
                $j     = 0;
                foreach ($medicine_name as $key => $mvalue) {
                    $expdate = $expiry_date[$j];
                    $explore = explode("/", $expdate);

                    if (count($explore) === 2) {
                        $monthary = $explore[0];
                        $yearary  = $explore[1];
                        $month    = $monthary;

                        $month_number = $this->convertMonthToNumber($month);
                        $insert_date  = $yearary . "-" . $month_number . "-01";
                    } else {
                        // Handle the error case: maybe log it, assign a default, or skip this entry
                        $insert_date = null; // or some default value like date('Y-m-d')
                    }

                    // $expdate = $expiry_date[$j];

                    // $explore = explode("/", $expdate);

                    // $monthary = $explore[0];
                    // $yearary  = $explore[1];
                    // $month    = $monthary;

                    // $month_number = $this->convertMonthToNumber($month);
                    // $insert_date  = $yearary . "-" . $month_number . "-01";

                    $details = array(
                        'inward_date'            => date('Y-m-d H:i:s', strtotime($bill_date)),
                        'pharmacy_id'            => $medicine_name[$j],
                        'supplier_bill_basic_id' => $insert_id,
                        'medicine_category_id'   => $medicine_category_id[$j],
                        'expiry_date'            => $expiry_date[$j],
                        'expiry_date_format'     => $insert_date,
                        'batch_no'               => $batch_no[$j],
                        'quantity'               => $quantity[$j],
                        'purchase_price'         => $purchase_price[$j],
                        'available_quantity'     => $quantity[$j],
                        'amount'                 => $amount[$j],
                        'hospital_id'   => $this->hospital_id,
                        'target_store_id'      => $this->store_id,
                        'user_id'       => $this->user_id,
                        'bill_status' => $status
                    );
                    $data1[] = $details;

                    $j++;
                }

                $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
            } else {
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
        }
        echo json_encode($array);
    }

    public function updateOpeningStock()
    {

        // if (!$this->rbac->hasPrivilege('medicine_supplier', 'can_add')) {
        //     access_denied();
        // }
        $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
        // echo "<pre>";print_r($_POST);exit;
        $status = $isDraft ? 'draft' : 'final';


        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expiry_date[]', $this->lang->line('expiry') . " " . $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase') . " " . $this->lang->line('price'), 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'batch_no'             => form_error('batch_no[]'),
                'expiry_date'          => form_error('expiry_date[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'total'                => form_error('total'),
                'amount'               => form_error('amount[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id               = $this->input->post('bill_basic_id');
            $bill_id          = $this->input->post("bill_detail_id[]");
            $supplier_id   = $this->input->post('supplier_id');
            $supplier_name = $this->input->post('supplier_name');
            $bill_date     = $this->input->post("date");
            $purchase_no   = $this->pharmacy_model->getMaxId();
            if (empty($purchase_no)) {
                $purchase_no = 0;
            }
            $purchase = $purchase_no + 1;

            $data = array(
                'id' => $id,
                'date'          => date('Y-m-d H:i:s', strtotime($bill_date)),
                'invoice_no'    => $this->input->post('invoiceno'),
                'purchase_no'   => $purchase,
                'total'         => $this->input->post('total'),
                'net_amount'    => 0,
                'note'          => $this->input->post('note'),
                'hospital_id'   => $this->hospital_id,
                'target_store_id'      => $this->store_id,
                'user_id'       => $this->user_id,
                'bill_status' => $status
            );

            $insert_id = $this->pharmacy_model->addBillSupplier($data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'file' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->addBillSupplier($data_img);
            }
            $data_array = array();
            $insert_id = $insert_id ? $insert_id : $id;
            if ($insert_id) {
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $expiry_date          = $this->input->post('expiry_date');
                $batch_no             = $this->input->post('batch_no');
                $batch_amount         = $this->input->post('batch_amount');
                $mrp                  = $this->input->post('mrp');
                $sale_rate            = $this->input->post('sale_rate');
                $packing_qty          = $this->input->post('packing_qty');
                $quantity             = $this->input->post('quantity');
                $purchase_price       = $this->input->post('purchase_price');
                $amount               = $this->input->post('amount');

                $j     = 0;
                $this->pharmacy_model->deleteBillMedicineBatchSupplier($insert_id);                // Add medicine details to `medicine_batch_details`
                foreach ($medicine_name as $key => $mvalue) {
    $expdate = trim($expiry_date[$j]);

    // Normalize separator to "/"
    $expdate = str_replace(['-', '\\'], '/', $expdate);
    $parts = explode('/', $expdate);

    if (count($parts) !== 2) {
        throw new Exception("Invalid expiry date format: $expdate");
    }

    [$monthPart, $yearPart] = $parts;

    // Normalize year
    $yearPart = trim($yearPart);
    if (strlen($yearPart) === 2) {
        $yearPart = '20' . $yearPart;
    }

    // Normalize month (either number or string like Apr)
    $monthPart = trim($monthPart);
    if (is_numeric($monthPart)) {
        $month_number = str_pad($monthPart, 2, '0', STR_PAD_LEFT);
    } else {
        // Convert short/full month name to number
        $month_number = date('m', strtotime("1 $monthPart"));
    }

    $insert_date = "$yearPart-$month_number-01";

    $add_data = array(
        'inward_date'            => date('Y-m-d H:i:s', strtotime($bill_date)),
        'pharmacy_id'            => $medicine_name[$j],
        'supplier_bill_basic_id' => $insert_id,
        'medicine_category_id'   => $medicine_category_id[$j],
        'expiry_date'            => $expiry_date[$j],
        'expiry_date_format'     => $insert_date,
        'batch_no'               => $batch_no[$j],
        'quantity'               => $quantity[$j],
        'purchase_price'         => $purchase_price[$j],
        'available_quantity'     => $quantity[$j],
        'amount'                 => $amount[$j],
        'hospital_id'            => $this->hospital_id,
        'target_store_id'        => $this->store_id,
        'user_id'                => $this->user_id,
        'bill_status'            => $status
    );

    $data_array[] = $add_data;
    $j++;
}

            } else {
            }
            if (!empty($data_array)) {
                $this->pharmacy_model->addBillMedicineBatchSupplier($data_array);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
        }
        echo json_encode($array);
    }

    public function getOpeningStockDetail($id)
    {
        // if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
        //     access_denied();
        // }
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result         = $this->pharmacy_model->getSingleOpeningStockDetail($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail'] = $detail;
        $this->load->view('store/storeStock/printPurchase', $data);
    }

    public function getReturnOpeningStockDetail($id)
    {
        // if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
        //     access_denied();
        // }
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $result         = $this->pharmacy_model->getSingleReturnOpeningStockDetail($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getReturnAllSupplierDetails($id);
        $data['detail'] = $detail;
        //   echo "<pre>";  print_r($data);exit;
        $this->load->view('store/refundPharmacy/printPurchase', $data);
    }

    public function getDepartmentPharmasistBatchNoList()
    {
        $medicine = $this->input->get_post('medicine');
        $role = $this->session->userdata('hospital')['role'];
        $result   = $this->pharmacy_model->getDepartmentPharmasistBatchNoList($medicine, $this->hospital_id, $this->store_id, $role);
        echo json_encode($result);
    }

    public function getStorePharmasistBatchNoList()
    {
        $medicine = $this->input->get_post('medicine');
        $transfer_store_id = $this->input->post('transfer_store_id');
        $result   = $this->pharmacy_model->getStorePharmasistBatchNoList($medicine, $this->hospital_id, $this->store_id, $transfer_store_id);
        echo json_encode($result);
    }


    // public function updateAddBill()
    // {
    //     $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
    //     $status = $isDraft ? 'draft' : 'final';

    //     $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
    //     $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
    //     $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase_price'), 'required|numeric');
    //     $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
    //     $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
    //     $this->form_validation->set_rules('store_id', $this->lang->line('store'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required');

    //     if ($this->form_validation->run() == false) {
    //         $msg = array(
    //             'medicine_category_id' => form_error('medicine_category_id[]'),
    //             'medicine_name'        => form_error('medicine_name[]'),
    //             'batch_no'             => form_error('batch_no[]'),
    //             'quantity'             => form_error('quantity[]'),
    //             'purchase_price'           => form_error('purchase_price[]'),
    //             'amount'               => form_error('amount[]'),
    //             'total'                => form_error('total'),
    //             'store_id'             => form_error('store_id'),
    //             'date'                 => form_error('date'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {
    //         $store_id   = $this->input->post('store_id'); // Target store ID
    //         $store_name = $this->input->post('store_name');
    //         $bill_date  = $this->input->post('date');
    //         $purchase_no = $this->pharmacy_model->getMaxId() ?? 0;
    //         $purchase = $purchase_no + 1;

    //         $data = array(
    //             'date'             => date('Y-m-d H:i:s', strtotime($bill_date)),
    //             'target_store_id'  => $store_id,
    //             'store_name'       => $store_name,
    //             'invoice_no'       => $this->input->post('bill_no'),
    //             'purchase_no'      => $purchase,
    //             'total'            => $this->input->post('total'),
    //             'net_amount'       => 0,
    //             'note'             => $this->input->post('note'),
    //             'hospital_id'      => $this->hospital_id,
    //             'user_id'          => $this->user_id,
    //             'transfer_store_id'=> $this->store_id, // Source store ID
    //             'bill_status' =>$status
    //         );
    //         $insert_id = $this->pharmacy_model->addBillSupplier($data);

    //         if ($insert_id) {
    //             $medicine_category_id = $this->input->post('medicine_category_id');
    //             $medicine_name        = $this->input->post('medicine_name');
    //             $expiry_date          = $this->input->post('expire_date');
    //             $batch_no             = $this->input->post('batch_no');
    //             $quantity             = $this->input->post('quantity');
    //             $amount               = $this->input->post('amount');
    //             $purchase_price               = $this->input->post('purchase_price');
    //             $total_quantity       = $this->input->post('available_quantity');
    //             $medicine_batch_details_id = $this->input->post('id');
    //             $data1 = array();
    //             $j     = 0;
    //             foreach ($medicine_name as $key => $mvalue) {
    //                 if (!isset($expiry_date[$j]) || empty($expiry_date[$j])) {
    //                     $msg = 'Expiry date is missing for medicine: ' . $mvalue;
    //                     $array = array('status' => 'fail', 'error' => '', 'message' => $msg);
    //                     echo json_encode($array);
    //                     return; // Stop further processing
    //                 }

    //                 $expdate = $expiry_date[$j];
    //                 $explore = explode("/", $expdate);

    //                 if (count($explore) !== 2) {
    //                     $msg = 'Invalid expiry date format for medicine: ' . $mvalue;
    //                     $array = array('status' => 'fail', 'error' => '', 'message' => $msg);
    //                     echo json_encode($array);
    //                     return; // Stop further processing
    //                 }

    //                 $month_number = $this->convertMonthToNumber($explore[0]);
    //                 $insert_date = $explore[1] . "-" . $month_number . "-01";


    //                 $details = array(
    //                     'inward_date'         => date('Y-m-d H:i:s', strtotime($bill_date)),
    //                     'pharmacy_id'         => $medicine_name[$j],
    //                     'supplier_bill_basic_id' => $insert_id,
    //                     'medicine_category_id'=> $medicine_category_id[$j],
    //                     'expiry_date'         => $expiry_date[$j],
    //                     'expiry_date_format'  => $insert_date,
    //                     'batch_no'            => $batch_no[$j],
    //                     'quantity'            => $quantity[$j],
    //                     'available_quantity'  => $quantity[$j],
    //                     'purchase_price'  => $purchase_price[$j],
    //                     'amount'              => $amount[$j],
    //                     'hospital_id'         => $this->hospital_id,
    //                     'user_id'             => $this->user_id,
    //                     'target_store_id'     => $store_id,
    //                     'transfer_store_id'   => $this->store_id,
    //                     'bill_status' =>$status
    //                 );
    //                 if($status == 'final'){
    //                     $available_quantity[$j] = $total_quantity[$j] - $quantity[$j];
    //                     $update_quantity = array(
    //                         'id'                 => $medicine_batch_details_id[$j],
    //                         'available_quantity' => $available_quantity[$j],
    //                     );
    //                     $this->pharmacy_model->availableQty($update_quantity);
    //                 }


    //                 $data1[] = $details;
    //                 $j++;
    //             }

    //             $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
    //         }

    //         $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
    //     }
    //     echo json_encode($array);
    // }

    public function updateAddBill()
    {
        $bill_date = $this->input->post("date");
        $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
        // echo "<pre>";print_r($_POST);exit;
        $status = $isDraft ? 'draft' : 'final';


        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        $this->form_validation->set_rules('store_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expire_date[]', $this->lang->line('expiry') . " " . $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase_price'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'store_id'          => form_error('store_id'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'expire_date'          => form_error('expire_date[]'),
                'batch_no'             => form_error('batch_no[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'total'                => form_error('total'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $store_id = $this->input->post('store_id');
            $store_details = $this->store_model->getStoreEntityType($store_id);

            $store_name = $this->input->post('store_name');
            $id               = $this->input->post('bill_basic_id');
            $bill_id          = $this->input->post("bill_detail_id[]");
            $previous_bill_id = $this->input->post("previous_bill_id[]");
            $supplier_id      = $this->input->post('supplier_id');
            $purchase_no      = $this->input->post('purchase_no');
            $data_array       = array();
            $delete_arr       = array();
            $bill_date = $this->input->post("date");
            $role = $this->session->userdata('hospital')['role'];
            $data      = array(
                'id'          => $id,
                'date'             => date('Y-m-d H:i:s', strtotime($bill_date)),
                'store_name'       => $store_name,
                'total'            => $this->input->post('total'),
                'net_amount'       => 0,
                'note'             => $this->input->post('note'),
                'hospital_id'      => $this->hospital_id,
                'user_id'          => $this->user_id,
                'transfer_store_id' => $this->store_id, // Source store ID
                'bill_status' => $status
            );
            if ($store_details && $store_details['entity_type'] === 'hospital') {
                $data['store_id'] = $store_id;
            } else {
                $data['target_store_id'] = $store_id;
            }

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $id, 'file' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->addBillSupplier($data_img);
            }
            $this->pharmacy_model->addBillSupplier($data);

            if (!empty($id)) {

                $bill_detail_id       = $this->input->post('bill_detail_id');
                $medicine_batch_id    = $this->input->post('id');
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $expiry_date          = $this->input->post('expire_date');
                $batch_no             = $this->input->post('batch_no');
                $quantity             = $this->input->post('quantity');
                $total_quantity       = $this->input->post('available_quantity');
                $amount               = $this->input->post('amount');
                $purchase_price       = $this->input->post('purchase_price');
                $data_array1          = array();
                $bill_date1           = $this->input->post("date");
                $j                    = 0;
                foreach ($medicine_category_id as $key => $value) {
                    if ($status == 'final') {
                        $available_quantity[$j] = $total_quantity[$j] - $quantity[$j];
                        $update_quantity = array(
                            'id'                 => $medicine_batch_id[$j],
                            'available_quantity' => $available_quantity[$j],
                        );
                        $this->pharmacy_model->availableQty($update_quantity);
                    }
                    if ($bill_id[$j] == 0) {
                        $add_data = array(
                            'supplier_bill_basic_id' => $id,
                            'medicine_category_id'   => $medicine_category_id[$j],
                            'pharmacy_id'            => $medicine_name[$j],
                            'inward_date'            => date('Y-m-d H:i:s', strtotime($bill_date1)),
                            'expiry_date'            => $expiry_date[$j],
                            'batch_no'               => $batch_no[$j],
                            'quantity'               => $quantity[$j],
                            'available_quantity'     => $quantity[$j],
                            'purchase_price'         => $purchase_price[$j],
                            'amount'                 => $amount[$j],
                            'hospital_id'   => $this->hospital_id,
                            'target_store_id'     => $store_id,
                            'transfer_store_id'   => $this->store_id,
                            'user_id'       => $this->user_id,
                            'bill_status' => $status
                        );
                        if ($store_details && $store_details['entity_type'] === 'hospital') {
                            $add_data['store_id'] = $store_id;
                        } else {
                            $add_data['target_store_id'] = $store_id;
                        }

                        $data_array[] = $add_data;
                    } else {

                        $detail = array(
                            'id'                     => $bill_id[$j],
                            'supplier_bill_basic_id' => $id,
                            'medicine_category_id'   => $medicine_category_id[$j],
                            'pharmacy_id'            => $medicine_name[$j],
                            'inward_date'            => date('Y-m-d H:i:s', strtotime($bill_date1)),
                            'expiry_date'            => $expiry_date[$j],
                            'batch_no'               => $batch_no[$j],
                            'quantity'               => $quantity[$j],
                            'available_quantity'     => $quantity[$j],
                            'purchase_price'         => $purchase_price[$j],
                            'amount'                 => $amount[$j],
                            'hospital_id'   => $this->hospital_id,
                            'target_store_id'     => $store_id,
                            'transfer_store_id'   => $this->store_id,
                            'bill_status' => $status

                        );

                        if ($store_details && $store_details['entity_type'] === 'hospital') {
                            $detail['store_id'] = $store_id;
                        } else {
                            $detail['target_store_id'] = $store_id;
                        }

                        $this->pharmacy_model->updateMedicineBatchDetail($detail);
                    }

                    $j++;
                }
            } else {
            }
            if (!empty($data_array)) {
                $this->pharmacy_model->addBillMedicineBatchSupplier($data_array);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }


    public function deleteSupplierBillDetail()
    {
        $bill_detail_id = $this->input->post('bill_detail_id');

        // Validate input
        if (!$bill_detail_id) {
            echo json_encode(["status" => "error", "message" => "Invalid bill detail ID"]);
            return;
        }


        // Call model function to delete the record
        $delete = $this->pharmacy_model->deleteBillDetail($bill_detail_id);

        if ($delete) {
            echo json_encode(["status" => "success", "message" => "Deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete the record"]);
        }
    }

    public function medicineList()
    {

        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $resultlist               = $this->pharmacy_model->searchFullText();
        $i                        = 0;
        $medicineCompanies         = $this->medicine_company_model->getMedicineCompanies();
        $data["medicineCompanies"] = $medicineCompanies;

        $result             = $this->pharmacy_model->getPharmacy();
        $data['resultlist'] = $resultlist;
        $data['result']     = $result;
        $this->load->view('layout/user/header');
        $this->load->view('store/product/search', $data);
        $this->load->view('layout/user/footer');
    }
    public function product_search()
    {

        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->pharmacy_model->search_datatable($where_condition);
        // echo $this->db->last_query();exit;
        $total_result = $this->pharmacy_model->search_datatable_count($where_condition);
        $data         = array();
        $total_qty = 0;
        $total_purchase = 0;
        $total_sale = 0;
        foreach ($resultlist as $result_key => $result_value) {
            $status = "";


            $nestedData = array();
            $action     = "<div class='rowoptionview'>";
            $action .= "<a href='#' onclick='viewDetail(" . $result_value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
            // if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_add')) {
            //     $action .= "<a href='#' class='btn btn-default btn-xs' onclick='addbadstock(" . $result_value->id . ")' data-toggle='tooltip' title='" . $this->lang->line('add') . ' ' . $this->lang->line('bad') . ' ' . $this->lang->line('stock') . "' > <i class='fas fa-minus-square'></i> </a>";
            // }


            $action .= "<div'>";
            $nestedData[] = $result_value->id;
            $nestedData[] = $result_value->medicine_name . $action;
            $nestedData[] = $result_value->medicine_company;
            $nestedData[] = $result_value->medicine_category;
            $nestedData[] = $result_value->barcode;



            $data[]       = $nestedData;
        }





        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }
    public function addProduct()
    {


        $this->form_validation->set_rules(
            'medicine_name',
            $this->lang->line('medicine') . " " . $this->lang->line('name'),
            array(
                'required',
                array('check_exists', array($this->medicine_category_model, 'valid_medicine_name')),
            )
        );
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'required');
        $this->form_validation->set_rules('medicine_company_id', $this->lang->line('medicine') . " " . $this->lang->line('company'), 'required');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');
        $this->form_validation->set_rules('formula', 'Formula', 'required');
        $this->form_validation->set_rules('unit_packing', $this->lang->line('unit') . "/" . $this->lang->line('packing'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload', 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_name'        => form_error('medicine_name'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'medicine_company_id'     => form_error('medicine_company_id'),
                'medicine_composition' => form_error('medicine_composition'),
                'medicine_group'       => form_error('medicine_group'),
                'unit'                 => form_error('unit'),
                'unit_packing'         => form_error('unit_packing'),
                'file'                 => form_error('file'),
                'formula'                 => form_error('formula'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $barcode = strtoupper(substr($this->input->post('medicine_name'), 0, 3)) . time();

            $pharmacy = array(
                'medicine_name' => $this->input->post('medicine_name'),
                'medicine_category_id'            => $this->input->post('medicine_category_id'),
                'medicine_company_id'                => $this->input->post('medicine_company_id'),
                'medicine_composition'            => $this->input->post('medicine_composition'),
                'medicine_group'                  => $this->input->post('medicine_group'),
                'unit'                            => $this->input->post('unit'),
                'min_level'                       => $this->input->post('min_level'),
                'reorder_level'                   => $this->input->post('reorder_level'),
                'vat'                             => $this->input->post('vat'),
                'unit_packing'                    => $this->input->post('unit_packing'),
                'supplier'                        => $this->input->post('supplier'),
                'note'                            => $this->input->post('note'),
                'vat_ac'                          => $this->input->post('vat_ac'),
                'formula'                          => $this->input->post('formula'),
                'barcode'              => $barcode,
            );
            $insert_id = $this->pharmacy_model->add($pharmacy);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'medicine_image' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->update($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }
    public function updateProduct()
    {

        $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine') . " " . $this->lang->line('category') . " " . $this->lang->line('id'), 'required');
        $this->form_validation->set_rules('medicine_company_id', $this->lang->line('medicine') . " " . $this->lang->line('company'), 'required');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');
        $this->form_validation->set_rules('unit_packing', $this->lang->line('unit') . "/" . $this->lang->line('packing'), 'required');
        $this->form_validation->set_rules('medicine_image', $this->lang->line('image'));
        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_name'        => form_error('medicine_name'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'medicine_company_id'     => form_error('medicine_company'),
                'medicine_composition' => form_error('medicine_composition'),
                'medicine_group'       => form_error('medicine_group'),
                'unit'                 => form_error('unit'),
                'unit_packing'         => form_error('unit_packing'),
                'medicine_image'       => form_error('medicine_image'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id       = $this->input->post('id');
            $pharmacy = array(
                'id'                   => $id,
                'medicine_name'        => $this->input->post('medicine_name'),
                'medicine_category_id' => $this->input->post('medicine_category_id'),
                'medicine_company'     => $this->input->post('medicine_company'),
                'medicine_composition' => $this->input->post('medicine_composition'),
                'medicine_group'       => $this->input->post('medicine_group'),
                'unit'                 => $this->input->post('unit'),
                'min_level'            => $this->input->post('min_level'),
                'reorder_level'        => $this->input->post('reorder_level'),
                'vat'                  => $this->input->post('vat'),
                'unit_packing'         => $this->input->post('unit_packing'),
                'note'                 => $this->input->post('edit_note'),
                'vat_ac'               => $this->input->post('vat_ac'),
                'medicine_company_id' => $this->input->post('medicine_company_id')
            );
            $this->pharmacy_model->update($pharmacy);
            if (isset($_FILES["medicine_image"]) && !empty($_FILES['medicine_image']['name'])) {
                $fileInfo = pathinfo($_FILES["medicine_image"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["medicine_image"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $id, 'medicine_image' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->update($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function supplierList()
    {
        $id = $this->input->post('id');
        $response         = $this->medicine_category_model->getSupplierByType(null, $this->hospital_id, $this->store_id, $id);
        echo json_encode($response);
    }

    public function closeRequest()
    {
        $this->load->model("Pharmacy_model");

        $bill_id = $this->input->post("bill_id");
        $remarks = $this->input->post("remarks");

        if (empty($bill_id) || empty($remarks)) {
            echo json_encode(["status" => "error", "message" => "Bill ID and remarks are required."]);
            return;
        }

        // Update bill status to 'closed' with remarks
        $update_data = [
            "status" => "rejected",
            "remarks" => $remarks
        ];

        $this->Pharmacy_model->updateBillStatus($bill_id, $update_data);

        echo json_encode(["status" => "success", "message" => "Request closed successfully."]);
    }

    public function get_store_medicine_name()
    {

        $medicine_category_id = $this->input->post("medicine_category_id");
        $role = $this->session->userdata('hospital')['role'];
        $target_store_id = $role == 'Store In-Charge' ? '' : $this->store_id;
        $transfer_store_id = $this->input->post('transfer_store_id');
        $supplier_id = $role == 'Store In-Charge' ? $transfer_store_id : '';
        $store_id = $role == 'Store In-Charge' ? $this->store_id : '';
        $transfer_store_id = $role == 'Store In-Charge' ? '' : $transfer_store_id;
        $data     =  $role == 'Store In-Charge' ? $this->pharmacy_model->get_main_store_medicine_name($medicine_category_id, $target_store_id, $transfer_store_id, $supplier_id, $store_id) : $this->pharmacy_model->get_store_medicine_name($medicine_category_id, $target_store_id, $transfer_store_id, $supplier_id, $store_id);
        echo json_encode($data);
    }

    public function get_store_medicine_qty()
    {
        $id = $this->input->post('medicine_id');
        $batch_id = $this->input->post('batch_id');
        $transfer_store_id = $this->input->post('transfer_store_id');
        $resultlist = $this->pharmacy_model->store_pharmacy_medicine_qty($id, $this->hospital_id, $this->store_id, $transfer_store_id, $batch_id);

        // Check if the resultlist is not empty
        if (!empty($resultlist)) {
            // Get total_qty from the first object
            $total_qty = $resultlist[0]->total_qty;

            // Send response with total_qty
            echo json_encode(['status' => true, 'total_qty' => $total_qty]);
        } else {
            // Handle the case where no data is found
            echo json_encode(['status' => false, 'error' => 'No data found']);
        }
    }

    public function addReturnStock()
    {
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'batch_no'             => form_error('batch_no[]'),
                'quantity'             => form_error('quantity[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $this->db->trans_begin(); // Start Transaction

            $transfer_store_id = $this->input->post('store_id');
            $target_store_id   = $this->store_id;
            $bill_date         = $this->input->post("date");
            $purchase_no       = $this->pharmacy_model->getMaxId();
            $purchase          = empty($purchase_no) ? 1 : $purchase_no + 1;

            $data = array(
                'date'              => date('Y-m-d H:i:s', strtotime($bill_date)),
                'invoice_no'        => $purchase,
                'hospital_id'       => $this->hospital_id,
                'target_store_id'   => $target_store_id,
                'transfer_store_id' => $transfer_store_id,
                'user_id'           => $this->user_id
            );

            $insert_id = $this->pharmacy_model->addReturbBill($data);

            if (!$insert_id) {
                $this->db->trans_rollback();
                $array = array(
                    'status' => 'fail',
                    'error' => 'Failed to insert return bill.',
                    'message' => ''
                );
                echo json_encode($array);
                return;
            }

            $medicine_category_id = $this->input->post('medicine_category_id');
            $medicine_name        = $this->input->post('medicine_name');
            $batch_no             = $this->input->post('batch_no');
            $quantity             = $this->input->post('quantity');
            $batch_id             = $this->input->post('batch_id');
            $return_type             = $this->input->post('return_type');


            $data1 = array();
            $error_flag = false;
            $error_messages = [];

            $j = 0;
            foreach ($medicine_name as $key => $mvalue) {
                $details = array(
                    'inward_date'                 => date('Y-m-d H:i:s', strtotime($bill_date)),
                    'pharmacy_id'                 => $medicine_name[$j],
                    'return_supplier_bill_basic_id' => $insert_id,
                    'medicine_category_id'        => $medicine_category_id[$j],
                    'batch_no'                    => $batch_no[$j],
                    'returned_quantity'           => $quantity[$j],
                    'hospital_id'                 => $this->hospital_id,
                    'target_store_id'             => $this->store_id,
                    'transfer_store_id'           => $transfer_store_id,
                    'user_id'                     => $this->user_id,
                    'return_type'               => isset($return_type[$j]) ? $return_type[$j] : null,
                );
                $data1[] = $details;

                // Update stock
                $where = ['id' => $batch_id[$j]];
                $stock_where = ['batch_no' => $batch_no[$j], 'store_id' => $transfer_store_id, 'target_store_id' => null, 'transfer_store_id' => null];

                $result = $this->pharmacy_model->updateStoreStock($where, $quantity[$j]);
                $this->pharmacy_model->updateMainStoreStock($stock_where, $quantity[$j]);

                // If updateStoreStock fails, rollback and stop execution
                if (!$result['status']) {
                    $error_flag = true;
                    $error_messages[] = "Batch " . $batch_no[$j] . ": " . $result['message'];
                    break; // Stop processing further records
                }

                $j++;
            }

            if ($error_flag) {
                $this->db->trans_rollback(); // Revert all changes
                $array = array(
                    'status' => 'fail',
                    'error' => implode(', ', $error_messages),
                    'message' => 'Some stock updates failed due to insufficient quantity.'
                );
                echo json_encode($array);
                return;
            }

            // If no errors, commit the transaction
            $this->pharmacy_model->addBillReturnMedicineBatchSupplier($data1);
            $this->db->trans_commit(); // Commit Transaction

            $array = array(
                'status' => 'success',
                'error' => '',
                'message' => $this->lang->line('success_message'),
                'insert_id' => $insert_id
            );
        }
        echo json_encode($array);
    }

    public function transferStockReport()
    {
        // if (!$this->rbac->hasPrivilege('pharmacy bill', 'can_view')) {
        //     access_denied();
        // }
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $this->load->view('layout/user/header');
        $this->load->view('store/pharmacy/transferStockReport.php', $data);
        $this->load->view('layout/user/footer');
    }

    public function report_bill_search()
    {
        $draw            = isset($_POST['draw']) ? $_POST['draw'] : 0;
        $row             = isset($_POST['start']) ? $_POST['start'] : 0;
        $rowperpage      = isset($_POST['length']) ? $_POST['length'] : 10;
        $columnIndex     = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
        $columnName      = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : 'id';
        $columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition['search'] = $_POST['search']['value'];
        }

        $hospital_id = $this->hospital_id;
        $store_id = null;
        $session_data = $this->session->userdata('hospital');
        $store_id = $this->store_id;

        // Fetch records
        $resultlist = $this->pharmacy_model->getStoreTransferStockReport(
            $where_condition,
            $hospital_id,
            $store_id
        );

        // Fetch count
        $total_result = $this->pharmacy_model->getStoreTransferStockReportCount(
            $hospital_id,
            $store_id,
            $where_condition
        );

        $data = array();
        foreach ($resultlist as $result) {

            $nestedData   = array();
            $nestedData[] = $result['purchase_no'];
            $nestedData[] = $result['date'];
            $nestedData[] = $result['transfer_store_name'];
            $nestedData[] = $result['username'];

            // If you want to include medicine-level details, you can add them here as needed
            $nestedData[] = $result['medicine_name'];
            $nestedData[] = $result['batch_no'];
            $nestedData[] = $result['expiry_date'];
            $nestedData[] = $result['quantity'];
            $nestedData[] = $result['medicine_category'];

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_result),
            "recordsFiltered" => intval($total_result),
            "data"            => $data,
        );

        echo json_encode($json_data);
    }

    public function addSupplierReturnStock()
    {
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'batch_no'             => form_error('batch_no[]'),
                'quantity'             => form_error('quantity[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $this->db->trans_begin(); // Start Transaction
            $supplier_id = $this->input->post('supplier_id');
            $transfer_store_id = $this->input->post('store_id');
            $target_store_id   = $this->store_id;
            $bill_date         = $this->input->post("date");
            $purchase_no       = $this->pharmacy_model->getMaxId();
            $purchase          = empty($purchase_no) ? 1 : $purchase_no + 1;

            $data = array(
                'date'              => date('Y-m-d H:i:s', strtotime($bill_date)),
                'invoice_no'        => $purchase,
                'hospital_id'       => $this->hospital_id,
                'store_id' => $this->store_id,
                'user_id'           => $this->user_id,
                'supplier_id' => $supplier_id
            );

            $insert_id = $this->pharmacy_model->addReturbBill($data);

            if (!$insert_id) {
                $this->db->trans_rollback();
                $array = array(
                    'status' => 'fail',
                    'error' => 'Failed to insert return bill.',
                    'message' => ''
                );
                echo json_encode($array);
                return;
            }

            $medicine_category_id = $this->input->post('medicine_category_id');
            $medicine_name        = $this->input->post('medicine_name');
            $batch_no             = $this->input->post('batch_no');
            $quantity             = $this->input->post('quantity');
            $batch_id             = $this->input->post('batch_id');
            $return_type             = $this->input->post('return_type');


            $data1 = array();
            $error_flag = false;
            $error_messages = [];

            $j = 0;
            foreach ($medicine_name as $key => $mvalue) {
                $details = array(
                    'inward_date'                 => date('Y-m-d H:i:s', strtotime($bill_date)),
                    'pharmacy_id'                 => $medicine_name[$j],
                    'return_supplier_bill_basic_id' => $insert_id,
                    'medicine_category_id'        => $medicine_category_id[$j],
                    'batch_no'                    => $batch_no[$j],
                    'returned_quantity'           => $quantity[$j],
                    'hospital_id'                 => $this->hospital_id,
                    'store_id'             => $this->store_id,
                    'user_id'                     => $this->user_id,
                    'return_type'               => isset($return_type[$j]) ? $return_type[$j] : null,
                );
                $data1[] = $details;

                // Update stock
                $where = ['id' => $batch_id[$j]];
                $stock_where = ['batch_no' => $batch_no[$j], 'store_id' => $this->store_id, 'supplier_id' => $supplier_id];

                $result = $this->pharmacy_model->updateStoreStock($where, $quantity[$j]);
                // $this->pharmacy_model->updateMainStoreStock($stock_where, $quantity[$j]);

                // If updateStoreStock fails, rollback and stop execution
                if (!$result['status']) {
                    $error_flag = true;
                    $error_messages[] = "Batch " . $batch_no[$j] . ": " . $result['message'];
                    break; // Stop processing further records
                }

                $j++;
            }

            if ($error_flag) {
                $this->db->trans_rollback(); // Revert all changes
                $array = array(
                    'status' => 'fail',
                    'error' => implode(', ', $error_messages),
                    'message' => 'Some stock updates failed due to insufficient quantity.'
                );
                echo json_encode($array);
                return;
            }

            // If no errors, commit the transaction
            $this->pharmacy_model->addBillReturnMedicineBatchSupplier($data1);
            $this->db->trans_commit(); // Commit Transaction

            $array = array(
                'status' => 'success',
                'error' => '',
                'message' => $this->lang->line('success_message'),
                'insert_id' => $insert_id
            );
        }
        echo json_encode($array);
    }
    public function bill_no_unique_check($bill_no)
    {
        $exists = $this->pharmacy_model->checkBillNoExists($bill_no, $this->hospital_id);

        if ($exists) {
            $this->form_validation->set_message('bill_no_unique_check', 'The Bill No already exists.');
            return false;
        }

        return true;
    }
}
