<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Department extends Hospital_Controller
{

    function __construct()
    {

        parent::__construct();
        $this->load->library('datatables');
        // $this->load->helper('file');
        // $this->config->load("payroll");
        $this->load->model('hospital_model');

        // Get session data
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;
        $this->load->model('store_model'); // Load the user model if not already loaded
        $this->marital_status       = $this->config->item('marital_status');
        $this->blood_group          = $this->config->item('bloodgroup');
        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $this->store_id = isset($user_details->store_id) ? $user_details->store_id : null;
        $this->user_id = $user_id;

    }

    function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'hr/index');
        $this->form_validation->set_rules(
            'type',
            'Department Name',
            array(
                'required',
                array('check_exists', array($this->department_model, 'valid_department'))
            )
        );

        $data["title"] = "Add Department";
        if ($this->form_validation->run()) {
            $type = $this->input->post("type");
            $departmenttypeid = $this->input->post("departmenttypeid");
            $status = $this->input->post("status");
            if (empty($departmenttypeid)) {
                if (!$this->rbac->hasPrivilege('department', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('department', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($departmenttypeid)) {
                $data = array('department_name' => $type, 'is_active' => 'yes', 'id' => $departmenttypeid);
            } else {
                $data = array('department_name' => $type, 'is_active' => 'yes');
            }
            $insert_id = $this->department_model->addDepartmentType($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Record added Successfully</div>');
            redirect("admin/department");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/staff/departmentType", $data);
            $this->load->view("layout/footer");
        }
    }

    public function add()
    {
        $this->form_validation->set_rules(
            'type',
            $this->lang->line('department') . " " . $this->lang->line('name'),
            array(
                'required',
                array('check_exists', array($this->department_model, 'valid_department'))
            )
        );
        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'name' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $type = $this->input->post("type");
            $data = array('department_name' => $type, 'is_active' => 'yes');
            $insert_id = $this->department_model->addDepartmentType($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    function get()
    { //get product data and encode to be JSON object
        header('Content-Type: application/json');
        echo $this->department_model->getall();
    }

    function get_data($id)
    {
        $result = $this->department_model->getDepartmentType($id);
        echo json_encode($result);
    }

    function edit()
    {
        $this->form_validation->set_rules(
            'type',
            $this->lang->line('department') . " " . $this->lang->line('name'),
            array(
                'required',
                array('check_exists', array($this->department_model, 'valid_department'))
            )
        );
        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'name' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $departmenttypeid = $this->input->post("departmenttypeid");
            $type = $this->input->post("type");
            $data = array('department_name' => $type, 'is_active' => 'yes', 'id' => $departmenttypeid);
            $this->department_model->addDepartmentType($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    function departmentdelete($id)
    {
        if (!empty($id)) {
            $this->department_model->deleteDepartment($id);
        }
        redirect("admin/department");
    }

    public function deleteDepartment()
    {
        $id = $this->input->post("id");
        $deleted = $this->department_model->deleteDepartmentById($id);

        if ($deleted) {
            $response = array(
                'status'  => 'success',
                'message' => 'Department deleted successfully.',
            );
        } else {
            $response = array(
                'status'  => 'fail',
                'message' => 'Failed to delete department.',
            );
        }

        echo json_encode($response);
    }

    public function getDepartmentDetails()
    {
        $id = $this->input->post("id");
        $result = $this->department_model->getDepartmentDetails($id);
        echo json_encode($result);
    }

    public function addDepartment()
    {
        $this->form_validation->set_rules('department_name', 'Department Name', 'required|trim');
        $this->form_validation->set_rules('hospital_id', 'Hospital', 'required|numeric');

        if ($this->form_validation->run() == false) {
            $response = array(
                'status' => 'fail',
                'error'  => array(
                    'department_name' => form_error('department_name'),
                    'hospital_id'        => form_error('hospital_id'),
                ),
            );
        } else {
            $last_department = $this->department_model->getLastDepartment();
            $new_id = 'D' . str_pad(($last_department ? intval(substr($last_department->department_unique_id, 1)) + 1 : 1), 3, '0', STR_PAD_LEFT);

            $data = array(
                'department_name'      => $this->input->post('department_name'),
                'hospital_id'             => $this->input->post('hospital_id'),
                'department_unique_id' => $new_id,
            );

            $this->department_model->addDepartment($data);

            $response = array(
                'status'  => 'success',
                'message' => 'Department added successfully.',
            );
        }

        echo json_encode($response);
    }
    public function department_list()
    {
        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length'];
        $columnIndex     = $_POST['order'][0]['column'];
        $columnName      = $_POST['columns'][$columnIndex]['data'];
        $columnSortOrder = $_POST['order'][0]['dir'];

        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }

        $where_condition['hospital_id'] = $this->hospital_id;

        // Get filtered and total department data
        $resultlist   = $this->department_model->search_department_datatable($where_condition, $columnName, $columnSortOrder, $row, $rowperpage);
        $total_result = $this->department_model->search_department_datatable_count($where_condition);

        $data = array();

        // Format the response data
        foreach ($resultlist as $result_value) {
            $action = "<a href='#' onclick='getDepartmentData(" . $result_value->id . ")' class='btn btn-default btn-xs' data-toggle='modal' title='View Details'><i class='fa fa-reorder'></i></a>";
            // $action .= "<div class='btn-group' style='margin-left:2px;'>";
            // $action .= "<a href='#' style='width: 20px;border-radius: 2px;' class='btn btn-default btn-xs' data-toggle='dropdown' title='Options'><i class='fa fa-ellipsis-v'></i></a>";
            // $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";
            // $action .= "<li><a href='#' onclick='editRecord(" . $result_value->id . ")'>Edit</a></li>";
            // $action .= "<li><a href='#' onclick='deleteRecord(" . $result_value->id . ")'>Delete</a></li>";
            // $action .= "</ul>";
            // $action .= "</div>";

            $data[] = array(
                'department_unique_id' => $result_value->department_unique_id,
                'department_name' => $result_value->department_name,
                'hospital_name' => $result_value->hospital_name, // Updated field
                'action' => $action,
            );
        }

        // JSON response for DataTable
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_result),
            "recordsFiltered" => intval($total_result),
            "data"            => $data,
        );

        echo json_encode($json_data);
    }

    public function list()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/department');
        $data['title'] = 'Departments';

        // Fetch stores for dropdown
        $this->load->model('store_model');
        $data['stores'] = $this->store_model->getAllStores();
        $data['hospitals'] = $this->hospital_model->getAllHospitals();

        $this->load->view('layout/user/header', $data);
        $this->load->view('store/department/index', $data);
        $this->load->view('layout/user/footer', $data);
    }

    public function updateDepartment()
    {
        $this->form_validation->set_rules('department_name', 'Department Name', 'required|trim');
        $this->form_validation->set_rules('hospital_id', 'Hospital', 'required|numeric');

        if ($this->form_validation->run() == false) {
            // Validation failed
            $response = array(
                'status' => 'fail',
                'error'  => array(
                    'department_name' => form_error('department_name'),
                    'hospital_id'        => form_error('hospital_id'),
                ),
            );
        } else {
            // Prepare data for update
            $data = array(
                'id'             => $this->input->post('department_id'),
                'department_name' => $this->input->post('department_name'),
                'hospital_id'       => $this->input->post('hospital_id'),
            );

            // Update department using the model
            $updated = $this->department_model->updateDepartment($data);

            if ($updated) {
                $response = array(
                    'status'  => 'success',
                    'message' => 'Department updated successfully.',
                );
            } else {
                $response = array(
                    'status'  => 'fail',
                    'message' => 'Failed to update department. Please try again.',
                );
            }
        }

        echo json_encode($response);
    }
}
