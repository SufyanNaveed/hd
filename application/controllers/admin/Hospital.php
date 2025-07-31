<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hospital extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the hospital_model
        $this->load->model('hospital_model');
    }
    public function index()
    {
        // if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
        //     access_denied();
        // }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/hospital');
        $data['title']       = 'Hospital';
        $this->load->view('layout/header', $data);

        $this->load->view('admin/hospital/index', $data);
        $this->load->view('layout/footer', $data);
    }
    public function hospital_list()
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

        // Get filtered and total hospital data
        $resultlist   = $this->hospital_model->searchhospital_datatable($where_condition, $columnName, $columnSortOrder, $row, $rowperpage);
        $total_result = $this->hospital_model->searchhospital_datatable_count($where_condition);

        $data = array();

        // Format data for DataTable
        foreach ($resultlist as $result_value) {
            $action = "<a href='#' onclick='getHospitalData(" . $result_value->id . ")' class='btn btn-default btn-xs'  data-toggle='modal' title='View Details'><i class='fa fa-reorder'></i></a>";

            // Dropdown for additional actions (if any)
            $action .= "<div class='btn-group' style='margin-left:2px;'>";
            $action .= "<a href='#' style='width: 20px;border-radius: 2px;' class='btn btn-default btn-xs'  data-toggle='dropdown' title='Options'><i class='fa fa-ellipsis-v'></i></a>";
            $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";
            $action .= "<li><a href='#' onclick='editRecord(" . $result_value->id . ")'>Edit</a></li>";
            $action .= "<li><a href='#' onclick='deleteRecord(" . $result_value->id . ")'>Delete</a></li>";
            $action .= "</ul>";
            $action .= "</div>";

            // Create row data for DataTable
            $nestedData   = array();
            $nestedData[] = $result_value->hospital_unique_id;
            // $nestedData[] = "<a href='#' onclick='getHospitalData(" . $result_value->id . ")'>" . $result_value->name . "</a>";
            $nestedData[] = "<a href='". base_url('site/userByPasslogin/'.$result_value->id)."/".str_replace(" ", "-",trim($result_value->short_name)) ."'>" . $result_value->name . "</a>";
            
            // $nestedData[] = $result_value->phone_number;
            // $nestedData[] = $result_value->address;
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

    public function addHospital()
    {
        // Define validation rules
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('mobileno', 'Phone Number', 'required|regex_match[/^\d{4}-\d{7}$/]');
        $this->form_validation->set_rules('address', 'Address', 'required|trim');

        if ($this->form_validation->run() == false) {
            // Validation failed, return errors in JSON format
            $response = array(
                'status' => 'fail',
                'error'  => array(
                    'name'     => form_error('name'),
                    'mobileno' => form_error('mobileno'),
                    'address'  => form_error('address'),
                ),
            );
        } else {
            // Generate a hospital_unique_id starting from 001
            $last_hospital = $this->hospital_model->getLastHospital();
            $new_id        = str_pad(($last_hospital ? intval($last_hospital->id) + 1 : 1), 3, '0', STR_PAD_LEFT);
            $hospital_unique_id =  $new_id;

            // Prepare data for insertion

            if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES['file']['name']); // Get file information
                $file_extension = $fileInfo['extension']; // Extract file extension

                // Generate a unique file name (e.g., 1.png, 2.jpg, etc.)
                $img_name = uniqid() . '.' . $file_extension;

                // Define upload path
                $upload_path = "./uploads/hospitals/";

                // Ensure the directory exists
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true); // Create directory if it doesn't exist
                }

                // Move the uploaded file to the destination
                if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path . $img_name)) {
                    // File successfully uploaded, save the file path to the database
                    $data_img = array('image' => $upload_path . $img_name);
                } else {
                    // File upload failed
                    $data_img = array('image' => 'uploads/hospitals/no_image.png'); // Default placeholder image
                }
            } else {
                // No file uploaded, use a default image
                $data_img = array('image' => 'uploads/hospitals/no_image.png');
            }

            // Save $data_img['image'] in the database with other hospital details

            $data = array(
                'name'           => $this->input->post('name'),
                'phone_number'   => $this->input->post('mobileno'),
                'address'        => $this->input->post('address'),
                'hospital_unique_id' => $hospital_unique_id,
                'logo'           => $data_img['image'], // File path saved here

            );

            // Save hospital data using model
            $this->hospital_model->addHospital($data);

            // Return success response
            $response = array(
                'status'  => 'success',
                'message' => 'Hospital added successfully.',
            );
        }

        echo json_encode($response);
    }

    public function gethospitalDetails()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }

        $id = $this->input->post("id");

        $result = $this->hospital_model->getHospitalDetails($id);
        echo json_encode($result);
    }

    public function updateHospital()
    {
        // Define validation rules
        $this->form_validation->set_rules('updateid', 'Hospital ID', 'required|trim');
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('phone', 'Phone Number', 'required|regex_match[/^\d{4}-\d{7}$/]');
        $this->form_validation->set_rules('address', 'Address', 'required|trim');

        if ($this->form_validation->run() == false) {
            // Validation failed, return errors in JSON format
            $response = array(
                'status' => 'fail',
                'error'  => array(
                    'updateid' => form_error('updateid'),
                    'name'     => form_error('name'),
                    'phone'    => form_error('phone'),
                    'address'  => form_error('address'),
                ),
            );
        } else {
            // Get Hospital ID
            $hospital_id = $this->input->post('updateid');

            // Prepare data for updating
            $data = array(
                'name'         => $this->input->post('name'),
                'phone_number' => $this->input->post('phone'),
                'address'      => $this->input->post('address'),
            );

            // Handle file upload for hospital logo
            if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES['file']['name']);
                $file_extension = $fileInfo['extension'];

                // Generate a unique file name
                $img_name = uniqid() . '.' . $file_extension;

                // Define upload path
                $upload_path = "./uploads/hospitals/";

                // Ensure the directory exists
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true); // Create directory if it doesn't exist
                }

                // Move the uploaded file to the destination
                if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path . $img_name)) {
                    // Update the file path in the data array
                    $data['logo'] = $upload_path . $img_name;
                }
            }

            // Update hospital data using the model
            $this->hospital_model->updateHospital($hospital_id, $data);

            // Return success response
            $response = array(
                'status'  => 'success',
                'message' => 'Hospital updated successfully.',
            );
        }

        echo json_encode($response);
    }

    public function deleteHospital()
    {
        // Validate that the request is POST
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed', 403);
        }

        // Get the hospital ID from the request
        $id = $this->input->post('delid');

        // Validate ID
        if (empty($id) || !is_numeric($id)) {
            $response = array(
                'status'  => 'fail',
                'message' => 'Invalid Hospital ID provided.',
            );
            echo json_encode($response);
            return;
        }

        // Delete the hospital using the model
        $deleted = $this->hospital_model->deleteHospitalById($id);

        // Respond based on the result
        if ($deleted) {
            $response = array(
                'status'  => 'success',
                'message' => $this->lang->line('delete_message'),
            );
        } else {
            $response = array(
                'status'  => 'fail',
                'message' => 'Failed to delete hospital. Please try again.',
            );
        }

        echo json_encode($response);
    }


    
    
}
