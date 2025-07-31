<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class UserManagement extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the Store Model
        $this->load->model('store_model');
        $this->load->model('hospital_model');
    }

    // Store List Page
    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/store');
        $roles                  = $this->role_model->get();
        $data['roles'] = $roles;
        $data['hospitals'] = $this->hospital_model->getAllHospitals();
        $data['title'] = 'Stores';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/user/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function addUser()
    {
        // Validation Rules
        $departments = $this->input->post('department_id') ? implode(',', $this->input->post('department_id')) : null;

        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|callback_check_unique_username');
        $this->form_validation->set_rules('father_name', 'Father Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('role', 'Role', 'trim|required|numeric|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|xss_clean');
        $this->form_validation->set_rules('hospital_id', 'Hospital', 'trim|required');
        $this->form_validation->set_rules('employee_code', 'Employee Code', 'trim|required|xss_clean|callback_check_unique_employee_code', [
            'check_unique_employee_code' => 'The %s already exists.'
        ]);
        $this->form_validation->set_rules('user_cnic', 'CNIC', 'trim|required|regex_match[/^\d{5}-\d{7}-\d$/]|xss_clean|callback_check_unique_cnic', [
            'regex_match' => 'Invalid CNIC format. Expected format: XXXXX-XXXXXXX-X',
            'check_unique_cnic' => 'The CNIC already exists in the system.'
        ]);
        $this->form_validation->set_rules('mobileno', 'Phone', 'trim|required|regex_match[/^\d{4}-\d{7}$/]|xss_clean', [
            'regex_match' => 'Invalid Phone format. Expected format: XXXX-XXXXXXX'
        ]);
        $this->form_validation->set_rules('shift_start_time', 'Shift Start Time', 'trim|required|xss_clean');
        $this->form_validation->set_rules('shift_end_time', 'Shift End Time', 'trim|required|xss_clean');

        // Get User Role
        $role_id = $this->input->post('role');
        $userRole = $this->role_model->get($role_id); // Assuming this fetches role details
        $role_name = $userRole['name'] ?? ''; // Extract role name safely
        // Apply validation based on Role
        if ($role_name === "Store In-Charge") {
            $this->form_validation->set_rules('store_id', 'Store', 'trim|required', [
                'required' => 'The %s field is required for Store In-Charge role.'
            ]);
        }

        if ($role_name === "Department Pharmacist" || $role_name === "Nurse" ) {
            $this->form_validation->set_rules('store_id', 'Store', 'trim|required', [
                'required' => 'The %s field is required for Department Pharmacist role.'
            ]);

            if($departments == ""){
                $this->form_validation->set_rules('department_id', 'Department', 'trim|required', [
                    'required' => 'The %s field is required for Department Pharmacist role.'
                ]);
            }
        }

        // Check Validation
        if ($this->form_validation->run() == false) {
            $errors = array(
                'name' => form_error('name'),
                'father_name' => form_error('father_name'),
                'role' => form_error('role'),
                'password' => form_error('password'),
                'employee_code' => form_error('employee_code'),
                'user_cnic' => form_error('user_cnic'),
                'mobileno' => form_error('mobileno'),
                'shift_start_time' => form_error('shift_start_time'),
                'shift_end_time' => form_error('shift_end_time'),
                'hospital_id' => form_error('hospital_id'),
                'store_id' => form_error('store_id'),
                'department_id' => form_error('department_id')
            );
            $response = array('status' => 'fail', 'error' => $errors, 'message' => '');
        } else {
            // Prepare User Data
            $user_data = array(
                'username' => $this->input->post('name'),
                'father_name' => $this->input->post('father_name'),
                'role_id' => $role_id,
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'employee_code' => $this->input->post('employee_code'),
                'cnic' => $this->input->post('user_cnic'),
                'mobileno' => $this->input->post('mobileno'),
                'shift_start_time' => $this->input->post('shift_start_time'),
                'shift_end_time' => $this->input->post('shift_end_time'),
                'created_at' => date('Y-m-d H:i:s'),
                'hospital_id' => $this->input->post('hospital_id'),
                'department_id' => $this->input->post('department_id')[0],
                'store_id' => $this->input->post('store_id'),
            );

            // Insert User into DB
            $user = $this->user_model->add_user($user_data);

            if($user){
                $user_department_data=array();
                for($i = 0; $i < count($this->input->post('department_id')); $i++) {
                    $user_department_data[] = array(
                        'user_id' => $user,
                        'department_id' => $this->input->post('department_id')[$i],
                    ); 
                }
                $this->db->insert_batch('user_departments',$user_department_data); 
            }


            $response = array('status' => 'success', 'message' => 'User added successfully.');
        }

        echo json_encode($response);
    }


    // Callback function to check unique CNIC
    public function check_unique_cnic($cnic)
    {
        $this->load->model('user_model'); // Load the user model
        if ($this->user_model->is_cnic_exists($cnic)) {
            $this->form_validation->set_message('check_unique_cnic', 'The CNIC already exists in the system.');
            return false;
        }
        return true;
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

        // Get filtered and total user data
        $resultlist   = $this->user_model->searchuser_datatable($where_condition, $columnName, $columnSortOrder, $row, $rowperpage);
        $total_result = $this->user_model->searchuser_datatable_count($where_condition);

        $data = array();

        // Format data for DataTable
        foreach ($resultlist as $result_value) {
            $action = "";

            // Dropdown for additional actions (if any)
            $action .= "<div class='btn-group' style='margin-left:2px;'>";
            $action .= "<a href='#' style='width: 20px;border-radius: 2px;' class='btn btn-default btn-xs' data-toggle='dropdown' title='Options'><i class='fa fa-ellipsis-v'></i></a>";
            $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";
            // $action .= "<li><a href='" . base_url("admin/userManagement/getUserDetail?id=" . urlencode($result_value->id)) . "'>Edit</a></li>";
            $action .= "<li><a href='#' onclick='deleteRecord(" . $result_value->id . ")'>Delete</a></li>";
            $action .= "</ul>";
            $action .= "<a href='" .base_url("/site/userByPasslogin/" . urlencode($result_value->id)) . "'><i class='fa fa-sign-in'></i></a>";
            $action .= "</div>";

            // Create row data for DataTable
            $nestedData   = array();
            $nestedData[] = $result_value->id;
            $nestedData[] = $result_value->username;
            $nestedData[] = $result_value->cnic;
            $nestedData[] = $result_value->father_name;
            $nestedData[] = $result_value->role_name;
            $nestedData[] = $result_value->hospital_name;
            $nestedData[] = $result_value->department_name;
            $nestedData[] = $result_value->store_name;
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

    public function getUserDetail()
    {

        $id = $this->input->get('id');
        // Validate input
        if (empty($id)) {
            echo json_encode(['status' => 'fail', 'message' => 'User ID is required.']);
            return;
        }

        $data = [];
        $roles                  = $this->role_model->get();
        $data['roles'] = $roles;
        $data['hospitals'] = $this->hospital_model->getAllHospitals();
        // Load user details from the model
        $data['user'] = $this->user_model->getUserById($id);
        $data['userDepartments'] = $this->user_model->getUserDepartments($id);
        
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
        //    echo "<pre>"; print_r($data);exit;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/user/editModal', $data);
        $this->load->view('layout/footer', $data);
    }
    public function update()
    {
        $departments = $this->input->post('department_id') ? implode(',', $this->input->post('department_id')) : null;

        // Validation Rules
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('father_name', 'Father Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('role', 'Role', 'trim|required|integer|xss_clean');
        $this->form_validation->set_rules('user_cnic', 'CNIC', 'trim|required|regex_match[/^\d{5}-\d{7}-\d$/]|xss_clean');
        $this->form_validation->set_rules('mobileno', 'Mobile Number', 'trim|required|regex_match[/^\d{4}-\d{7}$/]|xss_clean');
        $this->form_validation->set_rules('shift_start_time', 'Shift Start Time', 'trim|required');
        $this->form_validation->set_rules('shift_end_time', 'Shift End Time', 'trim|required');
    
        // Get User Role
        $role_id = $this->input->post('role');
        $userRole = $this->role_model->get($role_id); // Fetch role details
        $role_name = $userRole['name'] ?? ''; // Extract role name safely
    
        // Apply validation based on Role
        if ($role_name === "Store In-Charge") {
            $this->form_validation->set_rules('store_id', 'Store', 'trim|required', [
                'required' => 'The %s field is required for Store In-Charge role.'
            ]);
        }
    
        if ($role_name === "Department Pharmacist") {
            $this->form_validation->set_rules('store_id', 'Store', 'trim|required', [
                'required' => 'The %s field is required for Department Pharmacist role.'
            ]);
            if($departments == ""){ 
                $this->form_validation->set_rules('department_id', 'Department', 'trim|required', [
                    'required' => 'The %s field is required for Department Pharmacist role.'
                ]);
            }
        }
    
        // Check Validation
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'father_name' => form_error('father_name'),
                'role' => form_error('role'),
                'user_cnic' => form_error('user_cnic'),
                'mobileno' => form_error('mobileno'),
                'shift_start_time' => form_error('shift_start_time'),
                'shift_end_time' => form_error('shift_end_time'),
                'store_id' => form_error('store_id'),
                'department_id' => form_error('department_id')
            );
            $array = array('status' => 'fail', 'error' => $msg);
        } else {
            $update_id = $this->input->post('updateid');
            $user_data = array(
                'username' => $this->input->post('name'),
                'father_name' => $this->input->post('father_name'),
                'role_id' => $role_id,
                'cnic' => $this->input->post('user_cnic'),
                'mobileno' => $this->input->post('mobileno'),
                'shift_start_time' => $this->input->post('shift_start_time'),
                'shift_end_time' => $this->input->post('shift_end_time'),
                'hospital_id' => $this->input->post('hospital_id'),
                'department_id' => $this->input->post('department_id')[0],
                'store_id' => $this->input->post('store_id'),
            );
    
            if ($this->user_model->update_user($update_id, $user_data)) {

                $user_department_data=array();
                for($i = 0; $i < count($this->input->post('department_id')); $i++) {
                    $user_department_data[] = array(
                        'user_id' => $update_id,
                        'department_id' => $this->input->post('department_id')[$i],
                    ); 
                }
                $this->db->where('user_id',$update_id);
                $this->db->delete('user_departments');
                $this->db->insert_batch('user_departments',$user_department_data);

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
        $this->load->view('layout/header', $data);
        $this->load->view('admin/user/assignStore', $data);
        $this->load->view('layout/footer', $data);
    }


    public function fetchDataByRole()
    {
        $hospitalId = $this->input->post('hospital_id');
        $role_id = $this->input->post('role_id');


        // Validate inputs
        if (empty($hospitalId)) {
            echo json_encode(['status' => 'fail', 'message' => 'User ID and Hospital ID are required.']);
            return;
        }

        $role = $this->role_model->get($role_id);
        $role = $role['name'];

        // Fetch data based on role
        switch ($role) {
            case 'Store In-Charge':
                // Fetch main stores for the hospital
                $stores = $this->store_model->getStoresByEntity('hospital', $hospitalId);
                $result['stores'] = $stores;
                break;

            case 'Chief Pharmacist':
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

                case 'Nurse':
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
                // echo json_encode(['status' => 'fail', 'message' => 'Invalid Role.']);
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

    public function fetchStores()
    {
        $departmentId = $this->input->post('department_id');

        if (empty($departmentId)) {
            echo json_encode(['status' => 'fail', 'message' => 'Department ID is required.']);
            return;
        }

        $this->load->model('Store_model');
        $stores = $this->Store_model->getStoresByEntity('department', $departmentId);

        if (!empty($stores)) {
            echo json_encode(['status' => 'success', 'data' => $stores]);
        } else {
            echo json_encode(['status' => 'fail', 'message' => 'No stores found for the selected department.']);
        }
    }

    public function create()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/store');
        $roles                  = $this->role_model->get();
        $data['roles'] = $roles;
        $data['hospitals'] = $this->hospital_model->getAllHospitals();
        $data['title'] = 'Stores';
        $id = $this->input->get('id'); // Get user ID for edit mode

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
        $this->load->view('layout/header', $data);
        $this->load->view('admin/user/userModal', $data);
        $this->load->view('layout/footer', $data);
    }
}
