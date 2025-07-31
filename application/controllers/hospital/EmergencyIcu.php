<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class EmergencyIcu extends Hospital_Controller
{
    protected $hospital_id;
    protected $user_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('emergency_icu_model');

        // Get session data
        $session_data = $this->session->userdata('hospital');
        $this->user_id = isset($session_data['id']) ? $session_data['id'] : null;
        $user_details = $this->user_model->getUserById($this->user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
    }

    // List Emergency & ICU Entries
    public function index()
    {
        $data['departments'] = $this->emergency_icu_model->getAll($this->hospital_id);
        $this->load->view('layout/user/header');
        $this->load->view('store/emergency_icu/index', $data);
        $this->load->view('layout/user/footer');
    }

    // Add Emergency/ICU
    public function add()
    {
        $this->form_validation->set_rules('name', 'Department Name', 'required');
        $this->form_validation->set_rules('department_type', 'Department Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'fail', 'error' => validation_errors()]);
            return;
        }

        $data = [
            'hospital_id' => $this->hospital_id,
            'user_id' => $this->user_id,
            'department_type' => $this->input->post('department_type'),
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'status' => 'Active',
        ];

        $insert_id = $this->emergency_icu_model->insert($data);

        if ($insert_id) {
            echo json_encode(['status' => 'success', 'message' => 'Department added successfully']);
        } else {
            echo json_encode(['status' => 'fail', 'message' => 'Failed to add department']);
        }
    }

    // Edit Department
    public function edit($id)
    {
        $data = $this->emergency_icu_model->getById($id, $this->hospital_id);
        echo json_encode($data);
    }

    // Update Department
    public function update()
    {
        $id = $this->input->post('id');
        $this->form_validation->set_rules('name', 'Department Name', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'fail', 'error' => validation_errors()]);
            return;
        }

        $data = [
            'name' => $this->input->post('name'),
            'department_type' => $this->input->post('department_type'),
            'description' => $this->input->post('description'),
            'status' => $this->input->post('status'),
        ];

        $updated = $this->emergency_icu_model->update($id, $data, $this->hospital_id);

        if ($updated) {
            echo json_encode(['status' => 'success', 'message' => 'Department updated successfully']);
        } else {
            echo json_encode(['status' => 'fail', 'message' => 'Failed to update department']);
        }
    }

    // Delete Department
    public function delete($id)
    {
        $deleted = $this->emergency_icu_model->delete($id, $this->hospital_id);

        if ($deleted) {
            echo json_encode(['status' => 'success', 'message' => 'Department deleted successfully']);
        } else {
            echo json_encode(['status' => 'fail', 'message' => 'Failed to delete department']);
        }
    }
}
