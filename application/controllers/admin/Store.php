<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Store extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the Store Model
        $this->load->model('store_model');
        $this->load->model('hospital_model');
        $this->load->model('department_model');
    }

    // Store List Page
    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/store');
        $data['hospitals'] = $this->hospital_model->getAllHospitals();
        $data['title'] = 'Stores';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/store/index', $data);
        $this->load->view('layout/footer', $data);
    }

    // Fetch Stores for DataTable
    public function store_list()
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

        // Fetch data
        $resultlist   = $this->store_model->search_store_datatable($where_condition, $columnName, $columnSortOrder, $row, $rowperpage);
        $total_result = $this->store_model->search_store_datatable_count($where_condition);

        $data = array();
        // print_r($resultlist);exit;

        foreach ($resultlist as $result_value) {
            $entityTypeLabel = ucfirst($result_value->entity_type); // Capitalize the entity type (Hospital/Department)
            $entityName      = $result_value->entity_name;         // Fetch the associated entity name

            $action = "<div class='btn-group' style='margin-left:2px;'>";
            $action .= "<a href='#' style='width: 20px;border-radius: 2px;' class='btn btn-default btn-xs'  data-toggle='dropdown' title='Options'><i class='fa fa-ellipsis-v'></i></a>";
            $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";
            $action .= "<li><a href='#' onclick='editRecord(" . $result_value->id . ")'>Edit</a></li>";
            $action .= "<li><a href='#' onclick='deleteRecord(" . $result_value->id . ")'>Delete</a></li>";
            $action .= "</ul>";
            $action .= "</div>";

            // Build associative array
            $nestedData = array(
                'store_unique_id' => $result_value->store_unique_id,
                'store_name'      => "<a href='#' onclick='getStoreData(" . $result_value->id . ")'>" . $result_value->store_name . "</a>",
                'entity_type'     => $entityTypeLabel, // Display entity type (Hospital/Department)
                'entity_name'     => $entityName,     // Display the associated entity name
                'action'          => $action,
            );

            $data[] = $nestedData;
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



    // Add Store
    public function addStore()
    {
        // print_r($_POST);exit;
        $this->form_validation->set_rules('store_name', 'Store Name', 'required|trim');
        $this->form_validation->set_rules('entity_type', 'Type', 'required|in_list[hospital,department]');
        $this->form_validation->set_rules('entity_id', 'Entity ID', 'required|numeric'); // Updated label for clarity
    
        // Validate department_id only if entity_type is department
        $entity_type = $this->input->post('entity_type');
        if ($entity_type === 'department') {
            $this->form_validation->set_rules('department_id', 'Department', 'required|numeric');
        }
    
        if ($this->form_validation->run() == false) {
            $response = array(
                'status' => 'fail',
                'error'  => array(
                    'store_name'  => form_error('store_name'),
                    'entity_type' => form_error('entity_type'),
                    'entity_id'   => form_error('entity_id'),
                    'department_id' => form_error('department_id'), // Include department_id error
                ),
            );
        } else {
            // Get the last store and generate a new unique ID
            $last_store = $this->store_model->getLastStore();
            $new_id = 'S' . str_pad(($last_store ? intval(substr($last_store->store_unique_id, 1)) + 1 : 1), 3, '0', STR_PAD_LEFT);
    
            // Prepare data for insertion
            $data = array(
                'store_name'      => $this->input->post('store_name'),
                'entity_type'     => $this->input->post('entity_type'),
                'entity_id'       => $this->input->post('entity_id'),
                'store_unique_id' => $new_id,
            );
    
            // Add department_id only if entity_type is department
            if ($entity_type === 'department') {
                $data['department_id'] = $this->input->post('department_id');
            }
    
            $this->store_model->addStore($data);
    
            $response = array(
                'status'  => 'success',
                'message' => 'Store added successfully.',
            );
        }
    
        echo json_encode($response);
    }
    


    // Get Store Details
    public function getStoreDetails()
    {
        $id = $this->input->post("id");
        $result = $this->store_model->getStoreDetails($id);
        echo json_encode($result);
    }

    // Delete Store
    public function deleteStore()
    {
        $id = $this->input->post("id");
        $deleted = $this->store_model->deleteStoreById($id);

        if ($deleted) {
            $response = array(
                'status'  => 'success',
                'message' => 'Store deleted successfully.',
            );
        } else {
            $response = array(
                'status'  => 'fail',
                'message' => 'Failed to delete store.',
            );
        }

        echo json_encode($response);
    }

    public function updateStore()
    {
        $this->form_validation->set_rules('store_name', 'Store Name', 'required|trim');
        $this->form_validation->set_rules('entity_type', 'Entity Type', 'required|in_list[hospital,department]');
        $this->form_validation->set_rules('entity_id', 'Entity', 'required|numeric');
        $this->form_validation->set_rules('store_id', 'Store ID', 'required|numeric');
    
        // Validate department_id if entity_type is department
        $entity_type = $this->input->post('entity_type');
        if ($entity_type === 'department') {
            $this->form_validation->set_rules('department_id', 'Department', 'required|numeric');
        }
    
        if ($this->form_validation->run() == false) {
            // Validation failed, return errors
            $response = array(
                'status' => 'fail',
                'error'  => array(
                    'store_name'    => form_error('store_name'),
                    'entity_type'   => form_error('entity_type'),
                    'entity_id'     => form_error('entity_id'),
                    'store_id'      => form_error('store_id'),
                    'department_id' => form_error('department_id'), // Include department_id errors
                ),
            );
        } else {
            // Prepare data for updating
            $data = array(
                'store_name'  => $this->input->post('store_name'),
                'entity_type' => $this->input->post('entity_type'),
                'entity_id'   => $this->input->post('entity_id'),
            );
    
            // Add department_id only if entity_type is department
            if ($entity_type === 'department') {
                $data['department_id'] = $this->input->post('department_id');
            }
    
            $id = $this->input->post('store_id'); // ID of the store to update
    
            // Call the model to update the store
            $updated = $this->store_model->updateStore($id, $data);
    
            if ($updated) {
                $response = array(
                    'status'  => 'success',
                    'message' => 'Store updated successfully.',
                );
            } else {
                $response = array(
                    'status'  => 'fail',
                    'message' => 'Failed to update the store.',
                );
            }
        }
    
        echo json_encode($response);
    }
    

    public function getEntitiesByType()
    {
        $hospital_id = $this->input->post('hospital_id');
        $data = [];
        $data = $this->department_model->getAllDepartments($hospital_id);

        echo json_encode(['status' => 'success', 'data' => $data]);
    }
}
