<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hospital extends Hospital_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the Store Model
        $this->load->library('Enc_lib');

        $this->load->model('store_model');
        $this->load->model('hospital_model');
        $this->load->model('user_model');
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $this->store_id = isset($user_details->store_id) ? $user_details->store_id : null;
    }
    public function dashboard()
    {
        $data['total_patients'] = $this->user_model->total_patients(null, $this->hospital_id);
        $data['total_ipd_patients'] = $this->user_model->total_patients('ipd', $this->hospital_id);
        $data['total_opd_patients'] = $this->user_model->total_patients('opd', $this->hospital_id);
        $data['pharmacies_count'] = $this->user_model->get_pharmacies_count('hospital', $this->hospital_id);
        $data['department_store_count'] = $this->user_model->get_pharmacies_count('department', $this->hospital_id);
        $data['total_requested'] = $this->user_model->total_requested($this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role']);
        $data['total_approved'] = $this->user_model->total_approved('approved', $this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role']);
        $data['total_rejected'] = $this->user_model->total_approved('rejected', $this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role']);
        $where_condition['hospital_id'] = $this->hospital_id;
        $where_condition['store_id'] = $this->store_id;
        $where_condition['role'] = $this->session->userdata('hospital')['role'];
        $data['total_products'] = $this->user_model->get_medicine_count($where_condition);
        $data['total_in_stock_items'] =  $this->pharmacy_model->get_stock_summary($this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role']);
        $data['total_in_stock_supplier_items'] =  $this->pharmacy_model->get_supplier_stock_summary($this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role']);
        $data['total_issued_medicine'] =  $this->pharmacy_model->get_hospital_issued_medicine_stats($this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role']);
        // $today = "2025-07-02";
        $today = date('Y-m-d');
        $data['today_total_issued_medicine'] =  $this->pharmacy_model->get_hospital_issued_medicine_stats($this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role'],$today);
        $data['total_issued_medicine_wards'] =  $this->pharmacy_model->get_hospital_issued_medicine_wards_stats($this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role']);
        $data['total_requests'] = $data['total_requested'] + $data['total_approved'] + $data['total_rejected'];
        $data['total_expiry_count_this_week'] =  $this->pharmacy_model->get_expiry_count_this_week($this->hospital_id, $this->store_id, $this->session->userdata('hospital')['role']);

        // echo "<pre>";print_r($data);exit;
        // echo $this->store_id;exit;   
        $this->load->view("layout/user/header");
        $this->load->view("user/dashboard", $data);
        $this->load->view("layout/user/footer");
    }


    public function itemStock()
    {
        //  if (!$this->rbac->hasPrivilege('item_stock', 'can_view')) {
        //      access_denied();
        //  }
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Itemstock/index');
        $data['title']      = 'Add Item';
        $data['title_list'] = 'Recent Items';

        $this->form_validation->set_rules('item_id', $this->input->post('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity', $this->input->post('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->input->post('item') . " " . $this->input->post('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('photo'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
        } else {

            $store_id = ($this->input->post('store_id')) ? $this->input->post('store_id') : null;
            $data     = array(
                'item_id'     => $this->input->post('item_id'),
                'symbol'      => $this->input->post('symbol'),
                'supplier_id' => $this->input->post('supplier_id'),
                'store_id'    => $store_id,
                'quantity'    => $this->input->post('symbol') . $this->input->post('quantity'),
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description' => $this->input->post('description'),
            );

            $insert_id = $this->itemstock_model->add($data);
            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $fileInfo = pathinfo($_FILES["item_photo"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["item_photo"]["tmp_name"], "./uploads/inventory_items/" . $img_name);
                $data_img = array('id' => $insert_id, 'attachment' => 'uploads/inventory_items/' . $img_name);
                $this->itemstock_model->add($data_img);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('item_added_successfully') . '</div>');
            redirect('admin/itemstock/index');
        }
        $item_result          = $this->itemstock_model->get();
        $data['itemlist']     = $item_result;
        $itemcategory         = $this->itemcategory_model->get();
        $data['itemcatlist']  = $itemcategory;
        $itemsupplier         = $this->itemsupplier_model->get();
        $data['itemsupplier'] = $itemsupplier;
        $itemstore            = $this->itemstore_model->get();
        $data['itemstore']    = $itemstore;
        //   echo "<pre>"; print_r($data);exit;
        $this->load->view('layout/user/header', $data);
        $this->load->view('store/itemStock/itemList', $data);
        $this->load->view('layout/user/footer', $data);
    }
    public function item()
    {
        $session_data = $this->session->userdata('hospital'); // Get session data
        $user_id      = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object
        $hospital_id   = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $store_id      = isset($user_details->store_id) ? $user_details->store_id : null;
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Item/index');
        $data['title']       = 'Add Item';
        $data['title_list']  = 'Recent Items';
        $item_result         = $this->item_model->get(null, $hospital_id, $store_id);
        $data['itemlist']    = $item_result;
        $itemcategory        = $this->itemcategory_model->get();
        $data['itemcatlist'] = $itemcategory;
        $this->load->view('layout/user/header', $data);
        $this->load->view('store/item/itemList', $data);
        $this->load->view('layout/user/footer', $data);
    }


    public function itemAdd()
    {
        // Set validation rules
        $this->form_validation->set_rules('name', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|required|xss_clean');
        $this->form_validation->set_rules(
            'item_category_id',
            $this->lang->line('item') . " " . $this->lang->line('category'),
            array(
                'required',
                array('check_exists', array($this->item_model, 'valid_check_exists')),
            )
        );

        // Custom validation for quantities
        $this->form_validation->set_rules('opening_qty', 'Opening Quantity', 'trim|required|greater_than_equal_to[0]|xss_clean');
        $this->form_validation->set_rules('min_qty', 'Minimum Quantity', 'trim|required|greater_than_equal_to[0]|xss_clean');
        $this->form_validation->set_rules('reorder_qty', 'Reorder Quantity', 'trim|required|greater_than_equal_to[0]|xss_clean');

        // Check if expiry_is_optional is "Y", then expiry_date_min is required
        if ($this->input->post('expiry_is_optional') === 'y') {
            $this->form_validation->set_rules('expiry_date_min', 'Expiry Date', 'trim|required|xss_clean');
        }

        // Check validation
        if ($this->form_validation->run() == false) {
            // Collect validation error messages
            $msg = array(
                'name'               => form_error('name'),
                'unit'               => form_error('unit'),
                'item_category_id'   => form_error('item_category_id'),
                'opening_qty'        => form_error('opening_qty'),
                'min_qty'            => form_error('min_qty'),
                'reorder_qty'        => form_error('reorder_qty'),
                'expiry_date_min'    => form_error('expiry_date_min'),
                'expiry_is_optional' => form_error('expiry_is_optional'),
            );

            // Return error response
            $array = array('status' => 'fail', 'error' => $msg);
        } else {
            // Retrieve user data from session
            $session_data = $this->session->userdata('hospital'); // Get session data
            $user_id      = isset($session_data['id']) ? $session_data['id'] : null;

            // Fetch user details from the database
            $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

            // Validate user details and hospital linkage
            if (!empty($user_details) && isset($user_details->hospital_id)) {
                $hospital_id = $user_details->hospital_id; // Access object property
                $store_id    = $user_details->store_id; // Access object property
            } else {
                // Return error response if no hospital or store is linked
                $array = array(
                    'status'  => 'fail',
                    'error'   => '',
                    'message' => $this->lang->line('user_not_linked_to_hospital_or_store'),
                );
                echo json_encode($array);
                return;
            }

            // Collect data from the modal form
            $data = array(
                'item_category_id'   => $this->input->post('item_category_id', true), // Use true for XSS filtering
                'name'               => $this->input->post('name', true),
                'unit'               => $this->input->post('unit', true),
                'description'        => $this->input->post('description', true),
                'opening_qty'        => $this->input->post('opening_qty', true),
                'min_qty'            => $this->input->post('min_qty', true),
                'reorder_qty'        => $this->input->post('reorder_qty', true),
                'expiry_date_min'    => $this->input->post('expiry_date_min', true),
                'expiry_is_optional' => $this->input->post('expiry_is_optional', true),
                'hospital_id'        => $hospital_id, // Add hospital_id from user details
                'store_id'           => $store_id, // Add store_id from user details
                'user_id'            => $user_id, // Add user_id from session
            );

            // Insert the data into the database
            $insert_id = $this->item_model->add($data);

            // Return success response
            $array = array(
                'status'  => 'success',
                'error'   => '',
                'message' => $this->lang->line('new_item_successfully_inserted'),
            );
        }

        // Output the response as JSON
        echo json_encode($array);
    }

    public function itemUpdate()
    {
        // Set validation rules
        $this->form_validation->set_rules('name', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|required|xss_clean');
        $this->form_validation->set_rules(
            'item_category_id',
            $this->lang->line('item') . " " . $this->lang->line('category'),
            'required|xss_clean'
        );

        // Custom validation for quantities
        $this->form_validation->set_rules('opening_qty', 'Opening Quantity', 'trim|required|greater_than_equal_to[0]|xss_clean');
        $this->form_validation->set_rules('min_qty', 'Minimum Quantity', 'trim|required|greater_than_equal_to[0]|xss_clean');
        $this->form_validation->set_rules('reorder_qty', 'Reorder Quantity', 'trim|required|greater_than_equal_to[0]|xss_clean');

        // Check if expiry_is_optional is "Y", then expiry_date_min is required
        if ($this->input->post('expiry_is_optional') === 'y') {
            $this->form_validation->set_rules('expiry_date_min', 'Expiry Date', 'trim|required|xss_clean');
        }

        // Check validation
        if ($this->form_validation->run() == false) {
            // Collect validation error messages
            $msg = array(
                'name'               => form_error('name'),
                'unit'               => form_error('unit'),
                'item_category_id'   => form_error('item_category_id'),
                'opening_qty'        => form_error('opening_qty'),
                'min_qty'            => form_error('min_qty'),
                'reorder_qty'        => form_error('reorder_qty'),
                'expiry_date_min'    => form_error('expiry_date_min'),
                'expiry_is_optional' => form_error('expiry_is_optional'),
            );

            // Return error response
            $array = array('status' => 'fail', 'error' => $msg);
        } else {
            // Retrieve user data from session
            $session_data = $this->session->userdata('hospital'); // Get session data
            $user_id      = isset($session_data['id']) ? $session_data['id'] : null;

            // Fetch user details from the database
            $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

            // Validate user details and hospital linkage
            if (!empty($user_details) && isset($user_details->hospital_id)) {
                $hospital_id = $user_details->hospital_id; // Access object property
                $store_id    = $user_details->store_id; // Access object property
            } else {
                // Return error response if no hospital or store is linked
                $array = array(
                    'status'  => 'fail',
                    'error'   => '',
                    'message' => $this->lang->line('user_not_linked_to_hospital_or_store'),
                );
                echo json_encode($array);
                return;
            }

            // Collect data from the modal form
            $data = array(
                'id' => $this->input->post('id', true),
                'item_category_id'   => $this->input->post('item_category_id', true), // Use true for XSS filtering
                'name'               => $this->input->post('name', true),
                'unit'               => $this->input->post('unit', true),
                'description'        => $this->input->post('description', true),
                'opening_qty'        => $this->input->post('opening_qty', true),
                'min_qty'            => $this->input->post('min_qty', true),
                'reorder_qty'        => $this->input->post('reorder_qty', true),
                'expiry_date_min'    => $this->input->post('expiry_date_min', true),
                'expiry_is_optional' => $this->input->post('expiry_is_optional', true),
                'hospital_id'        => $hospital_id, // Add hospital_id from user details
                'store_id'           => $store_id, // Add store_id from user details
                'user_id'            => $user_id, // Add user_id from session
            );

            // Update item in the database
            $this->item_model->add($data);

            // Return success response
            $array = array(
                'status'  => 'success',
                'error'   => '',
                'message' => $this->lang->line('item_updated_successfully'),
            );
        }

        // Output the response as JSON
        echo json_encode($array);
    }

    public function getItem($id)
    {
        $item = $this->item_model->get($id, null, null);


        echo json_encode($item);
    }

    public function itemDelete($id)
    {
        // if (!$this->rbac->hasPrivilege('item', 'can_delete')) {
        //     access_denied();
        // }
        // $data['title'] = 'Fees Master List';
        $this->item_model->remove($id);
        redirect('hospital/item');
    }

    public function UserManagement()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/store');
        $roles                  = $this->role_model->get();
        $data['roles'] = $roles;
        $data['hospitals'] = $this->hospital_model->getAllHospitals();
        $data['title'] = 'Stores';
        $this->load->view('layout/user/header', $data);
        $this->load->view('store/user/list', $data);
        $this->load->view('layout/user/footer', $data);
    }

    public function user_list()
    {
        // Get DataTable request parameters
        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc

        // Search filter
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }

        $session_data = $this->session->userdata('hospital'); // Get session data
        $user_id      = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object
        $hospital_id   = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        // Get filtered and total user data
        $resultlist   = $this->user_model->searchuser_datatable($where_condition, $columnName, $columnSortOrder, $row, $rowperpage, $hospital_id, $user_id);
        $total_result = $this->user_model->searchuser_datatable_count($where_condition, $hospital_id, $user_id);

        $data = array();

        // Format data for DataTable
        foreach ($resultlist as $result_value) {
            $action = "<a href='" . base_url("hospital/hospital/assignStore?id=" . urlencode($result_value->id)) . "' class='btn btn-default btn-xs' data-toggle='modal' title='View Details'><i class='fa fa-reorder'></i></a>";

            // Dropdown for additional actions (if any)
            $action .= "<div class='btn-group' style='margin-left:2px;'>";
            $action .= "<a href='#' style='width: 20px;border-radius: 2px;' class='btn btn-default btn-xs' data-toggle='dropdown' title='Options'><i class='fa fa-ellipsis-v'></i></a>";
            $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";
            $action .= "<li><a href='#' onclick='editRecord(" . $result_value->id . ")'>Edit</a></li>";
            $action .= "<li><a href='#' onclick='deleteRecord(" . $result_value->id . ")'>Delete</a></li>";
            $action .= "</ul>";
            $action .= "</div>";

            // Create row data for DataTable
            $nestedData   = array();
            $nestedData[] = "<a href='#' onclick='getUserData(" . $result_value->id . ")'>" . $result_value->id . "</a>";
            $nestedData[] = $result_value->username;
            $nestedData[] = $result_value->father_name;
            $nestedData[] = $result_value->role_name; // Assuming roles are fetched in join query
            $nestedData[] = $action;
            $data[]       = $nestedData;
        }

        // Prepare JSON response
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_result),
            "recordsFiltered" => intval($total_result),
            "data"            => $data,
        );

        echo json_encode($json_data);
    }

    public function addUser()
    {
        // Validation Rules
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|callback_check_unique_username');
        $this->form_validation->set_rules('father_name', 'Father Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('role', 'Role', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|xss_clean');
        $this->form_validation->set_rules('employee_code', 'Employee Code', 'trim|required|xss_clean|callback_check_unique_employee_code', [
            'check_unique_employee_code' => 'The %s already exists.'
        ]);
        $this->form_validation->set_rules('user_cnic', 'CNIC', 'trim|required|regex_match[/^\d{5}-\d{7}-\d$/]|xss_clean', [
            'regex_match' => 'Invalid CNIC format. Expected format: XXXXX-XXXXXXX-X'
        ]);
        $this->form_validation->set_rules('mobileno', 'Phone', 'trim|required|regex_match[/^\d{4}-\d{7}$/]|xss_clean', [
            'regex_match' => 'Invalid Phone format. Expected format: XXXX-XXXXXXX'
        ]);
        $this->form_validation->set_rules('shift_start_time', 'Shift Start Time', 'trim|required|xss_clean');
        $this->form_validation->set_rules('shift_end_time', 'Shift End Time', 'trim|required|xss_clean');
        $session_data = $this->session->userdata('hospital'); // Get session data
        $user_id      = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object
        $hospital_id   = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        // Check Validation
        if ($this->form_validation->run() == false) {
            $errors = array(
                'name' => form_error('name'),
                'father_name' => form_error('father_name'),
                'role' => form_error('role'),
                'password' => form_error('password'),
                'employee_code' => form_error('employee_code'),
                'patient_cnic' => form_error('patient_cnic'),
                'mobileno' => form_error('mobileno'),
                'shift_start_time' => form_error('shift_start_time'),
                'shift_end_time' => form_error('shift_end_time'),
            );
            $response = array('status' => 'fail', 'error' => $errors, 'message' => '');
        } else {
            // Prepare User Data
            $user_data = array(
                'username' => $this->input->post('name'),
                'father_name' => $this->input->post('father_name'),
                'role_id' => $this->input->post('role'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'employee_code' => $this->input->post('employee_code'),
                'cnic' => $this->input->post('user_cnic'),
                'mobileno' => $this->input->post('mobileno'),
                'shift_start_time' => $this->input->post('shift_start_time'),
                'shift_end_time' => $this->input->post('shift_end_time'),
                'created_at' => date('Y-m-d H:i:s'),
                'hospital_id' => $hospital_id,
                'created_by' => $user_id
            );

            // Insert User into DB
            $this->user_model->add_user($user_data);

            $response = array('status' => 'success', 'message' => 'User added successfully.');
        }

        echo json_encode($response);
    }

    public function check_unique_username($username)
    {
        $this->load->model('user_model'); // Ensure the user model is loaded

        if ($this->user_model->is_username_exists($username)) {
            $this->form_validation->set_message('check_unique_username', 'The {field} already exists.');
            return false;
        }
        return true;
    }

    // Callback to Check Unique Employee Code
    public function check_unique_employee_code($employee_code)
    {
        $is_unique = $this->user_model->is_employee_code_unique($employee_code);
        if (!$is_unique) {
            $this->form_validation->set_message('check_unique_employee_code', 'The Employee Code is already in use.');
            return false;
        }
        return true;
    }

    public function getUserDetail()
    {
        $id = $this->input->post('id');

        // Validate input
        if (empty($id)) {
            echo json_encode(['status' => 'fail', 'message' => 'User ID is required.']);
            return;
        }

        // Load user details from the model
        $user = $this->user_model->getUserById($id);

        if ($user) {
            echo json_encode(['status' => 'success', 'data' => $user]);
        } else {
            echo json_encode(['status' => 'fail', 'message' => 'User not found.']);
        }
    }
    public function updateUser()
    {
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('father_name', 'Father Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('role', 'Role', 'trim|required|integer|xss_clean');
        $this->form_validation->set_rules('user_cnic', 'CNIC', 'trim|required|regex_match[/^\d{5}-\d{7}-\d$/]|xss_clean');
        $this->form_validation->set_rules('mobileno', 'Mobile Number', 'trim|required|regex_match[/^\d{4}-\d{7}$/]|xss_clean');
        $this->form_validation->set_rules('shift_start_time', 'Shift Start Time', 'trim|required');
        $this->form_validation->set_rules('shift_end_time', 'Shift End Time', 'trim|required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'father_name' => form_error('father_name'),
                'role' => form_error('role'),
                'user_cnic' => form_error('user_cnic'),
                'mobileno' => form_error('mobileno'),
                'shift_start_time' => form_error('shift_start_time'),
                'shift_end_time' => form_error('shift_end_time'),
            );
            $array = array('status' => 'fail', 'error' => $msg);
        } else {
            $update_id = $this->input->post('updateid');
            $user_data = array(
                'username' => $this->input->post('name'),
                'father_name' => $this->input->post('father_name'),
                'role_id' => $this->input->post('role'),
                'cnic' => $this->input->post('user_cnic'),
                'mobileno' => $this->input->post('mobileno'),
                'shift_start_time' => $this->input->post('shift_start_time'),
                'shift_end_time' => $this->input->post('shift_end_time'),
            );

            if ($this->user_model->update_user($update_id, $user_data)) {
                $array = array('status' => 'success', 'message' => 'User updated successfully.');
            } else {
                $array = array('status' => 'fail', 'message' => 'Failed to update user.');
            }
        }

        echo json_encode($array);
    }
    public function deleteUser()
    {
        $user_id = $this->input->post('delid');

        if (empty($user_id)) {
            $response = array(
                'status' => 'fail',
                'message' => 'Invalid User ID.'
            );
        } else {
            $this->load->model('user_model');
            $result = $this->user_model->delete_user($user_id);

            if ($result) {
                $response = array(
                    'status' => 'success',
                    'message' => 'User deleted successfully.'
                );
            } else {
                $response = array(
                    'status' => 'fail',
                    'message' => 'Failed to delete user. Please try again.'
                );
            }
        }

        echo json_encode($response);
    }

    public function assignStore()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/store');

        $id = $this->input->get('id'); // Get user ID for edit mode
        $data = [];

        if (!empty($id)) {
            // Fetch user details for edit
            $user = $this->user_model->getUserById($id);

            if (!$user) {
                $this->session->set_flashdata('error', 'User not found.');
                redirect('admin/userManagement'); // Redirect to the user management page
                return;
            }

            $data['user'] = $user; // Pass user data to the view

            // Fetch dropdown options based on saved values
            $data['departments'] = !empty($user->hospital_id)
                ? $this->department_model->getDepartmentsByHospital($user->hospital_id)
                : []; // Fetch departments for saved hospital, or set empty if not assigned

            $data['stores'] = !empty($user->department_id)
                ? $this->store_model->getStoresByEntity('department', $user->department_id)
                : $this->store_model->getStoresByEntity('hospital', $user->hospital_id); // Fetch stores for saved department, or set empty if not assigned
        } else {
            $data['user'] = null; // For "add" mode
            $data['departments'] = [];
            $data['stores'] = [];
        }

        // Fetch all hospitals for the dropdown
        $data['hospitals'] = $this->hospital_model->getAllHospitals();
        //   echo "<pre>";  print_r($data);exit;
        // Load views with data
        $this->load->view('layout/user/header', $data);
        $this->load->view('store/user/assignStore', $data);
        $this->load->view('layout/user/footer', $data);
    }

    public function fetchDataByRole()
    {
        $userId = $this->input->post('user_id');
        $hospitalId = $this->input->post('hospital_id');

        // Validate inputs
        if (empty($userId) || empty($hospitalId)) {
            echo json_encode(['status' => 'fail', 'message' => 'User ID and Hospital ID are required.']);
            return;
        }

        // Fetch user role
        $user = $this->user_model->getUserById($userId);
        if (!$user) {
            echo json_encode(['status' => 'fail', 'message' => 'Invalid User.']);
            return;
        }

        $role = $user->role_name;
        $result = [
            'departments' => [],
            'stores' => [],
        ];


        // Fetch data based on role
        switch ($role) {
            case 'Store In-Charge':
                // Fetch main stores for the hospital
                $stores = $this->store_model->getStoresByEntity('hospital', $hospitalId);
                $result['stores'] = $stores;
                break;

            case 'Department Pharmacist':
                // Fetch departments for the hospital
                $departments = $this->department_model->getDepartmentsByHospital($hospitalId);
                $result['departments'] = $departments;

                // If a department is selected, fetch its stores
                $departmentId = $this->input->post('department_id');
                if (!empty($departmentId)) {
                    $stores = $this->store_model->getStoresByEntity('department', $departmentId);
                    $result['stores'] = $stores;
                }
                break;

            default:
                echo json_encode(['status' => 'fail', 'message' => 'Invalid Role.']);
                return;
        }

        // Return result
        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    public function saveUserAssignments()
    {
        // Load validation library
        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules('user_id', 'User ID', 'required|integer');
        $this->form_validation->set_rules('hospital_id', 'Hospital', 'required|integer');
        $this->form_validation->set_rules('department_id', 'Department', 'integer');
        $this->form_validation->set_rules('store_id', 'Store', 'integer');

        // Validate input
        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'status' => 'fail',
                'error' => validation_errors()
            ]);
            return;
        }

        // Get POST data
        $userId = $this->input->post('user_id');
        $hospitalId = $this->input->post('hospital_id');
        $departmentId = $this->input->post('department_id') ?: NULL; // Allow NULL for optional fields
        $storeId = $this->input->post('store_id') ?: NULL;

        // Load the user model
        $this->load->model('User_model');

        // Save the data
        $updateStatus = $this->User_model->updateUserAssignments($userId, $hospitalId, $departmentId, $storeId);

        // Return response
        if ($updateStatus) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Assignments saved successfully.'
            ]);
        } else {
            echo json_encode([
                'status' => 'fail',
                'message' => 'Failed to save assignments. Please try again.'
            ]);
        }
    }


    public function changepass()
    {


        $data['title'] = 'Change Password';
        $this->form_validation->set_rules('current_pass', $this->lang->line('current_password'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_pass', $this->lang->line('new_password'), 'trim|required|xss_clean|matches[confirm_pass]');
        $this->form_validation->set_rules('confirm_pass', $this->lang->line('confirm_password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $sessionData = $this->session->userdata('hospital');
            $this->data['id']       = $sessionData['id'];
            $this->data['username'] = $sessionData['username'];
            $this->load->view('layout/user/header', $data);
            $this->load->view('user/change_password', $data);
            $this->load->view('layout/user/footer', $data);
        } else {
            $sessionData = $this->session->userdata('hospital');
            $data_array  = array(
                'current_pass' => ($this->input->post('current_pass')),
                'new_pass'     => ($this->input->post('new_pass')),
                'user_id'      => $sessionData['id'],
                'user_name'    => $sessionData['username'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'password' => password_hash($this->input->post('new_pass'), PASSWORD_DEFAULT),
            );
            // echo "<pre>"; print_r($newdata);exit;
            $query1 = $this->user_model->checkOldPass($data_array);

            if ($query1) {
                $query2 = $this->user_model->saveNewPass($newdata);
                if ($query2) {
                    $this->session->set_flashdata('success_msg', $this->lang->line('success_message'));
                    $this->load->view('layout/user/header', $data);
                    $this->load->view('user/change_password', $data);
                    $this->load->view('layout/user/footer', $data);
                }
            } else {
                $this->session->set_flashdata('error_msg', $this->lang->line('invalid_current_password'));
                $this->load->view('layout/user/header', $data);
                $this->load->view('user/change_password', $data);
                $this->load->view('layout/user/footer', $data);
            }
        }
    }

    public function getPatientStats()
    {
        $type = $this->input->get('type'); // Get filter type (monthly, weekly, today)
        $this->load->model('Patient_model');

        if ($type === 'weekly') {
            $data = $this->Patient_model->get_weekly_data($this->hospital_id);
        } elseif ($type === 'today') {
            $data = $this->Patient_model->get_today_data($this->hospital_id);
        } else {
            $data = $this->Patient_model->get_monthly_data($this->hospital_id);
        }

        // Set JSON response header
        echo json_encode($data);
    }

    public function supplierList()
    {
        $id = $this->input->post('id');
        $response         = $this->medicine_category_model->getSupplierByType(null, null, null, $id);
        echo json_encode($response);
    }
}
