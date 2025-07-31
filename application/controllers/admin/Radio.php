<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Radio extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->library('CSVReader');
        $this->marital_status       = $this->config->item('marital_status');
        $this->payment_mode         = $this->config->item('payment_mode');
        $this->search_type          = $this->config->item('search_type');
        $this->blood_group          = $this->config->item('bloodgroup');
        $this->charge_type          = $this->customlib->getChargeMaster();
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->config->load("image_valid");
        $this->load->model('common_model');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('radiology test', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('parameter_name[]', $this->lang->line('parameter') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('test_name', $this->lang->line('test') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('short_name', $this->lang->line('short') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('test_type', $this->lang->line('test') . " " . $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('radiology_category_id', $this->lang->line('category') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge') . " " . $this->lang->line('category'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard') . " " . $this->lang->line('charge'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'parameter_name[]'      => form_error('parameter_name[]'),
                'test_type'             => form_error('test_type'),
                'radiology_category_id' => form_error('radiology_category_id'),
                'charge_category_id'    => form_error('charge_category_id'),
                'code'                  => form_error('code'),
                'standard_charge'       => form_error('standard_charge'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $parameter_id = $this->input->post('parameter_name');
            // print_r($parameter_id);
            // exit();
            $radiology = array(
                'test_name'             => $this->input->post('test_name'),
                'short_name'            => $this->input->post('short_name'),
                'test_type'             => $this->input->post('test_type'),
                'radiology_category_id' => $this->input->post('radiology_category_id'),
                'sub_category'          => $this->input->post('sub_category'),
                'report_days'           => $this->input->post('report_days'),
                'charge_id'             => $this->input->post('code'),
                'test_description'             => $this->input->post('test_description'),
            );
            $insert_id = $this->radio_model->add($radiology);

            $i = 0;
            foreach ($parameter_id as $key => $value) {
                $detail = array(
                    'radiology_id' => $insert_id,
                    'parameter_id' => $parameter_id[$i],
                );
                $data[] = $detail;

                $i++;
            }

            $this->radio_model->addparameter($data);
            $Info=$this->common_model->getSingleRow($column='id',$insert_id,$select="test_name",$table="radio");
			$comments="add radiology test where test name is ". $Info['test_name'];
			$activityLog=$this->common_model->saveLog('radiology','add',$comments);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function parameterview($id)
    {
        $parametername         = $this->radio_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail         = $this->radio_model->getparameterDetails($id);
        $data['detail'] = $detail;
        $this->load->view("admin/radio/parameterview", $data);
    }

    public function parameterdetails($id, $valueid = '')
    {
        $parametername         = $this->radio_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail         = $this->radio_model->getparameterDetailsforpatient($valueid);
        $data['detail'] = $detail;
        $this->load->view("admin/radio/parameterdetails", $data);
    }

    public function editparameter($id)
    {
        $parametername         = $this->radio_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail         = $this->radio_model->getparameterDetails($id);
        $data['detail'] = $detail;
        $this->load->view("admin/radio/editparameter", $data);
    }

    public function patientDetails()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }
        $id   = $this->input->post("id");
        $data = $this->patient_model->patientDetails($id);
        echo json_encode($data);
    }

    public function addpatient()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $check_patient_id = $this->patient_model->getMaxId();

            if (empty($check_patient_id)) {
                $check_patient_id = 1000;
            }

            $patient_id = $check_patient_id + 1;

            $patient_data = array(
                'patient_name'      => $this->input->post('name'),
                'mobileno'          => $this->input->post('contact'),
                'marital_status'    => $this->input->post('marital_status'),
                'email'             => $this->input->post('email'),
                'gender'            => $this->input->post('gender'),
                'guardian_name'     => $this->input->post('guardian_name'),
                'blood_group'       => $this->input->post('blood_group'),
                'address'           => $this->input->post('address'),
                'known_allergies'   => $this->input->post('known_allergies'),
                'patient_unique_id' => $patient_id,
                'note'              => $this->input->post('note'),
                'age'               => $this->input->post('age'),
                'month'             => $this->input->post('month'),
                'is_active'         => 'yes',
            );
            $insert_id = $this->patient_model->add_patient($patient_data);

            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $data_patient_login = array(
                'username' => $this->patient_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'patient',
            );
            $this->user_model->add($data_patient_login);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }
        }
        echo json_encode($array);
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('radiology test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'radiology');
        $categoryName            = $this->lab_model->getlabName();
        $data["marital_status"]  = $this->marital_status;
        $data["categoryName"]    = $categoryName;
        $data['charge_category'] = $this->radio_model->getChargeCategory();
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $parametername           = $this->lab_model->getradioparameter();
        $data["parametername"]   = $parametername;
        $data['resultlist']      = $this->radio_model->searchFullText();
        $data['tests'] = $this->radio_model->getRadiology();
        $data['organisation']   = $this->organisation_model->get();
        //echo "<pre>";print_r($data['organisation']);exit;
        $this->load->view('layout/header');
        $this->load->view('admin/radio/search.php', $data);
        $this->load->view('layout/footer');
    }

    public function getparameterdetails()
    {
        $id     = $this->input->get_post('id');
        $result = $this->lab_model->getradioparameter($id);
        echo json_encode($result);
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('radiology test', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post("radiology_id");
        $result = $this->radio_model->getDetails($id);
        echo json_encode($result);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('radiology test', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('test_name', $this->lang->line('test') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('short_name', $this->lang->line('short') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('test_type', $this->lang->line('test') . " " . $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('radiology_category_id', $this->lang->line('category') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge') . " " . $this->lang->line('category'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'test_type'             => form_error('test_type'),
                'radiology_category_id' => form_error('radiology_category_id'),
                'charge_category_id'    => form_error('charge_category_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id                         = $this->input->post('id');
            $charge_category_id         = $this->input->post('charge_category_id');
            $id                         = $this->input->post('id');
            $charge_category_id         = $this->input->post('charge_category_id');
            $test_description         = $this->input->post('test_description');
            $pre_radiology_parameter_id = $this->input->post("previous_radiology_parameter_id[]");
            $pre_radiology_id           = $this->input->post("previous_radiology_id");
            $pre_parameter_id           = $this->input->post("pre_parameter_id[]");
            $previous_parameter_id = $this->input->post("previous_parameter_id[]");
            $new_parameter_id = $this->input->post("new_parameter_id[]");
            $parameter_id = $this->input->post("parameter_name[]");
            $insert_data  = array();
            $radiology    = array(
                'id'                    => $id,
                'test_name'             => $this->input->post('test_name'),
                'short_name'            => $this->input->post('short_name'),
                'test_type'             => $this->input->post('test_type'),
                'radiology_category_id' => $this->input->post('radiology_category_id'),
                'sub_category'          => $this->input->post('sub_category'),
                'report_days'           => $this->input->post('report_days'),
                'charge_id'             => $charge_category_id,
                'test_description'             => $test_description,
            );

            $delete_arr = array();
            foreach ($previous_parameter_id as $pkey => $pvalue) {
                if (in_array($pvalue, $new_parameter_id)) {

                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $i = 0;
            $j = 0;
            foreach ($parameter_id as $key => $value) {
                if (!empty($pre_radiology_parameter_id)) {
                    if (array_key_exists($i, $pre_radiology_parameter_id)) {
                        $detail = array(
                            'parameter_id' => $parameter_id[$i],
                            'id'           => $pre_radiology_parameter_id[$i],
                        );
                        $data[] = $detail;
                    } else {
                        $j++;
                        $insert_detail = array(
                            'radiology_id' => $pre_radiology_id,
                            'parameter_id' => $parameter_id[$i],
                        );
                        $insert_data[] = $insert_detail;
                    }} else {

                    $insert_detail = array(
                        'radiology_id' => $id,
                        'parameter_id' => $parameter_id[$i],
                    );
                    $insert_data[] = $insert_detail;

                }
                $i++;
            }

            $k         = $i - $j;
            $s         = 1;
            $condition = "";
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    if ($s == $k) {
                        $coma = '';
                    } else {
                        $coma = ',';
                    }
                    $condition .= "(" . $value['parameter_id'] . "," . $value['id'] . ")" . $coma;
                    $s++;
                }
            }

            if (!empty($data)) {
                $this->radio_model->updateparameter($condition);
            }
            if (!empty($insert_data)) {
                $this->radio_model->addparameter($insert_data);
            }

            if (!empty($delete_arr)) {
                $this->radio_model->delete_parameter($delete_arr);
            }

            $this->radio_model->update($radiology);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('radiology test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->radio_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getRadiology()
    {
        if (!$this->rbac->hasPrivilege('radiology test', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post('radiology_id');
        $result = $this->radio_model->getRadiology($id);
        $chargesID=$result['charge_id'];
        $result['getOrganiztionCharges']=$getOrganiztionCharges=$this->charge_model->getOrganisationCharges($chargesID);
        echo json_encode($result);
    }

    public function testReportBatch()
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('radiology_id', $this->lang->line('radiology') . " " . $this->lang->line('id'), 'required');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'required');
        $this->form_validation->set_rules('reporting_date', $this->lang->line('reporting').' '.$this->lang->line('date'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'radiology_id'   => form_error('radiology_id'),
                'patient_id'     => form_error('patient_id'),
                'reporting_date' => form_error('reporting_date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $this->load->model('common_model');
            $bill_no = $this->radio_model->getMaxId();
            if (empty($bill_no)) {
                $bill_no = 0;
            }
            $bill           = $bill_no + 1;
            $id             = $this->input->post('radiology_id');
            $patient_id     = $this->input->post('patient_id');
            $reporting_date = $this->input->post("reporting_date");

            if($this->input->post('discount_type')=='percentage' && $this->input->post('apply_charge') > 0){
                $radio_discount=($this->input->post('apply_charge') * $this->input->post('radio_discount')/100);
                $radio_discount=number_format($radio_discount,2);

            }
            if($this->input->post('discount_type')=='fixed' && $this->input->post('apply_charge') > 0){
                $radio_discount=$this->input->post('radio_discount');
            }
            $report_batch = array(
                'bill_no'           => $bill,
                'radiology_id'      => $id,
                'patient_id'        => $patient_id,
                'customer_type'     => $this->input->post('customer_type'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => date('Y-m-d', $this->customlib->datetostrtotime($reporting_date)),
                'description'       => $this->input->post('description'),
                'generated_by'      => $this->session->userdata('hospitaladmin')['id'],
                'apply_charge'      => $this->input->post('apply_charge'),
                'radio_discount'      => $radio_discount,
                'discount_type'      => $this->input->post('discount_type'),
            );
            if($this->input->post('description')==''){

                $report_batch['description']=$this->getTestDescp($id);
            }
            $insert_id = $this->radio_model->testReportBatch($report_batch);

            $patientInfo=$this->common_model->getRow($patient_id);
            $comments="assign radiology test where patient name is ". $patientInfo['patient_name']." Bill No is ".$bill;
            $activityLog=$this->common_model->saveLog('radiology','add',$comments,$bill);

            $paramet_details = $this->radio_model->getparameterBypathology($id);
            foreach ($paramet_details as $pkey => $pvalue) {
                # code...

                $paramet_insert_array = array('radiology_report_id' => $insert_id,
                    'parameter_id'       => $pvalue["parameter_id"],

                );

                $insert_into_parameter = $this->radio_model->addParameterforPatient($paramet_insert_array);

            }

            if (isset($_FILES["radiology_report"]) && !empty($_FILES['radiology_report']['name'])) {
                $fileInfo = pathinfo($_FILES["radiology_report"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["radiology_report"]["tmp_name"], "./uploads/radiology_report/" . $img_name);
                $data_img = array('id' => $insert_id, 'radiology_report' => $img_name);
                $this->radio_model->testReportBatch($data_img);
            }
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getReportDetails($id, $parameter_id = '')
    {

        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details            = $this->printing_model->get('', 'radiology');
        $data['print_details']    = $print_details;
        $result                   = $this->radio_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->radio_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parameterdetails         = $this->radio_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;
        //echo "<pre>";print_r($data);exit;
        $this->load->view('admin/radio/printReport', $data);
    }

    public function getBillDetails($id, $parameter_id = '')
    {

        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details            = $this->printing_model->get('', 'radiology');
        $data['print_details']    = $print_details;
        $result                   = $this->radio_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->radio_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parameterdetails         = $this->radio_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;
        //echo "<pre>";print_r($data);exit;
        $this->load->view('admin/radio/printBill', $data);
    }

    public function getTestReportBatch()
    {

        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'radiology');
        $id               = $this->input->post("radiology_id");
        $doctors          = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]  = $doctors;
        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        $this->load->view('layout/header');
        $this->load->view('admin/radio/reportDetail', $data);
        $this->load->view('layout/footer');
    }

    public function getRadiologyReport()
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id');
        $result                   = $this->radio_model->getRadiologyReport($id);
        $result['reporting_date'] = date($this->customlib->getSchoolDateFormat(), strtotime($result['reporting_date']));

        echo json_encode($result);
    }

    public function updateTestReport()
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('id', 'Id', 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'id' => form_error('id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id             = $this->input->post('id');
            $reporting_date = $this->input->post("reporting_date");

            $report_batch = array(
                'id'                => $id,
                'patient_name'      => $this->input->post('patient_name'),
                'patient_id'        => $this->input->post('patient_id_radio'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => date('Y-m-d', $this->customlib->datetostrtotime($reporting_date)),
                'description'       => $this->input->post('description'),
                'apply_charge'      => $this->input->post('apply_charge'),
            );
            $this->radio_model->updateTestReport($report_batch);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            if (!empty($_FILES['radiology_report']['name'])) {
                $config['upload_path']   = 'uploads/radiology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['file_name']     = $_FILES['radiology_report']['name'];
                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('radiology_report')) {
                    $uploadData = $this->upload->data();
                    $picture    = $uploadData['file_name'];
                    $data_img   = array('id' => $id, 'radiology_report' => $picture);
                    $this->radio_model->updateTestReport($data_img);
                }
            }
        }
        echo json_encode($array);
    }

    public function parameteraddvalue()
    {

        if (!$this->rbac->hasPrivilege('radiology test', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'id' => form_error('id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id             = $this->input->post('id');
            $reporting_date = $this->input->post("reporting_date");
            $show_description_only=$this->input->post('show_description_only');
            $show_description_only=isset($show_description_only) && !empty($show_description_only) ? 1 :0;
            $report_batch   = array(
                'id'                => $id,
                //'patient_id'        => $this->input->post('patient_id_radio'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => date('Y-m-d', $this->customlib->datetostrtotime($reporting_date)),
                'description'       => $this->input->post('description'),
                //'radio_discount'      => $this->input->post('radio_discount'),
                // not used befor old this one 'apply_charge'      => $this->input->post('apply_charge') +  $this->input->post('radio_discount'),
                //'apply_charge'      => $this->input->post('apply_charge') ,
                'show_description_only'      => $show_description_only,
                'status'      => 'updated',
            );
           // echo "<pre>";print_r($report_batch);exit;
            $parameter_id    = $this->input->post('parameter_id[]');
            $parameter_value = $this->input->post('parameter_value[]');
            $i               = 0;
            $parameter_array = array();
            if (!empty($parameter_id)) {
                foreach ($parameter_id as $pkey => $pvalue) {
                    $parameter_value_arr = array(
                        'id'                     => $pvalue,
                        'radiology_report_id'    => $id,
                        'radiology_report_value' => $parameter_value[$i],
                    );


                    $this->radio_model->addparametervalue($parameter_value_arr);
                    $i++;
                }
            }

            if (!empty($_FILES['radiology_report']['name'])) {
                $config['upload_path']   = 'uploads/radiology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['file_name']     = $_FILES['radiology_report']['name'];

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                $fileInfo = pathinfo($_FILES["radiology_report"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                $fileInfo = pathinfo($_FILES["radiology_report"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];

                $data_img = array('id' => $id, 'radiology_report' => $img_name);
                $this->radio_model->updateTestReport($data_img);

                move_uploaded_file($_FILES["radiology_report"]["tmp_name"], "./uploads/radiology_report/" . $img_name);

                // }
            }

            $this->radio_model->updateTestReport($report_batch);
            $Info=$this->common_model->getSingleRow($column='id',$id,$select="bill_no",$table="radiology_report");
			$comments="update radiology bill record where bill number is ". $Info['bill_no'];
			$activityLog=$this->common_model->saveLog('radiology bill','update',$comments,$Info['bill_no']);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function download($doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/radiology_report/" . $doc;
        $data     = file_get_contents($filepath);
        force_download($doc, $data);
    }

    public function deleteTestReport($id)
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_delete')) {
            access_denied();
        }
        $Info=$this->common_model->getSingleRow($column='id',$id,$select="bill_no",$table="radiology_report");
		$comments="delete radiology bill record where bill number is ". $Info['bill_no'];
		$activityLog=$this->common_model->saveLog('radiology bill','delete',$comments,$Info['bill_no']);
        $this->radio_model->deleteTestReport($id);
    }

    public function radiologyReport()
    {
        if (!$this->rbac->hasPrivilege('radiology test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/radio/radiologyreport');
        $select = 'radiology_report.*, radio.id, radio.short_name,staff.name,staff.surname,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name';
        $join   = array(
            'JOIN radio ON radiology_report.radiology_id = radio.id',
            'LEFT JOIN staff ON radiology_report.consultant_doctor = staff.id',
            'JOIN charges ON charges.id = radio.charge_id',
            'JOIN patients ON patients.id = radiology_report.patient_id',
        );
        $table_name  = "radiology_report";
        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }
        if (empty($search_type)) {
            $search_type = "";
            $resultlist  = $this->report_model->getReport($select, $join, $table_name);
        } else {

            $search_table  = "radiology_report";
            $search_column = "reporting_date";
            $result_list    = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column);
            $resultlist    = $result_list['main_data'];
        }
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist;
        $this->load->view('layout/header');
        //echo "<pre>";print_r($data);exit;
        $this->load->view('admin/radio/radiologyReport.php', $data);
        $this->load->view('layout/footer');
    }

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_radiology.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_radiology.csv';

        force_download($name, $data);
    }

    public function import()
    {
        if (!$this->rbac->hasPrivilege('radiology test', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_csv_upload');
        $fields                   = array('test_name', 'short_name', 'test_type', 'radiology_category', 'sub_category', 'report_days', 'charge_category_id', 'code', 'standard_charge','charge_type');
        $data["fields"]           = $fields;

        if ($this->form_validation->run() == false) {
            $msg = array(
                'file'                 => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            $this->load->view('layout/header');
            $this->load->view('admin/radio/search', $data);
            $this->load->view('layout/footer');
        } else {
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];

                    $result = $this->csvreader->parse_file($file);
                    if (!empty($result)) {
                        $count = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $test_data[$i] = array();
                            $n                 = 0;
                            foreach ($result[$i] as $key => $value) {

                                $test_data[$i][$fields[$n]]            = $this->encoding_lib->toUTF8($result[$i][$key]);
                                $test_data[$i]['is_active']            = 'yes';
                                //$test_data[$i]['medicine_category_id'] = $medicine_category_id;

                                $n++;
                            }
                            //echo "<pre>";print_r($test_data);exit;
                            $test_name = $test_data[$i]["test_name"];
                            $radiology_category_id = $test_data[$i]["radiology_category"];
                            if (!empty($test_name)) {
                                if ($this->radio_model->check_test_exists($test_name,$radiology_category_id)) {
                                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');

                                    $insert_id = "";
                                } else {
                                    $new_radiology_category_id=$this->radio_model->checkRadiologyCreated($radiology_category_id);
                                    $chargeID=$this->radio_model->checkChargesID($test_data[$i]["charge_category_id"],$test_data[$i]["code"],$test_data[$i]["standard_charge"],$test_data[$i]["charge_type"]);
                                    $testRadiologyData=array(
                                        'test_name'=>$test_name,
                                        'short_name'=>$test_data[$i]["short_name"],
                                        'test_type'=>$test_data[$i]["test_type"],
                                        'radiology_category_id'=>$new_radiology_category_id,
                                        'sub_category'=>$test_data[$i]["sub_category"],
                                        'report_days'=>$test_data[$i]["report_days"],
                                        'charge_id'=>$chargeID,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                    );
                                    $insert_id = $this->db->insert('radio',$testRadiologyData);
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
                redirect('admin/radio/search');
            }
        }

    }

    public function handle_csv_upload()
    {

        $image_validate = $this->config->item('filecsv_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = filesize($_FILES['file']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_csv_upload', "File Type / Extension Not Allowed");
                return false;
            }

            return true;
        } else {
            $this->form_validation->set_message('handle_csv_upload', "File field is required");
            return false;
        }
        return true;
    }

    public function testReportBatchBulk()
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_add')) {
            access_denied();
        }
        //echo "<pre>";print_r($_POST);exit;
        $this->form_validation->set_rules('test_id_bulk[]', $this->lang->line('test') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'required');
        $this->form_validation->set_rules('reporting_date_bulk[]', $this->lang->line('reporting').' '.$this->lang->line('date'), 'required');
        $this->form_validation->set_rules('consultant_doctor_bulk[]', $this->lang->line('consultant').' '.$this->lang->line('doctor'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'radiology_id'   => form_error('test_id_bulk[]'),
                'patient_id'     => form_error('patient_id'),
                'reporting_date_bulk' => form_error('reporting_date_bulk[]'),
                'consultant_doctor_bulk' => form_error('consultant_doctor_bulk[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $this->load->model('common_model');
            $reporting_date = $this->input->post("reporting_date_bulk");
            $discount_type=$this->input->post('discount_type');
            $apply_charge=$this->input->post('apply_charge');
            $radiodiscount=$this->input->post('radio_discount');
            $consultant_doctor_bulk=$this->input->post('consultant_doctor_bulk');
            $description_bulk=$this->input->post('description_bulk');
            $apply_charge_bulk=$this->input->post('apply_charge_bulk');
            $applied_total=$this->input->post('applied_total');
            $allBil=array();
            $report_bulk_id=array();
            foreach($this->input->post('test_id_bulk') as $key=>$id)
            {
                $bill_no = $this->radio_model->getMaxId();
                if (empty($bill_no)) {
                    $bill_no = 0;
                }
                $bill           = $bill_no + 1;
                $patient_id     = $this->input->post('patient_id');
                $allBil[]=$bill_no;
                if($discount_type[$key]=='percentage' && $applied_total[$key] > 0){
                    $radio_discount=($applied_total[$key] * $radiodiscount[$key]/100);
                    $radio_discount=number_format($radio_discount,2);

                }
                if($discount_type[$key]=='fixed' && $apply_charge_bulk[$key] > 0){
                    $radio_discount= $radiodiscount[$key];
                }

                $report_batch = array(
                    'bill_no'           => $bill,
                    'radiology_id'      => $id,
                    'patient_id'        => $patient_id,
                    'organization_charge_id' => $this->input->post('organisation_bulk')[$key],
                    'customer_type'     => $this->input->post('customer_type'),
                    'consultant_doctor' => $consultant_doctor_bulk[$key],
                    'reporting_date'    => date('Y-m-d', $this->customlib->datetostrtotime($reporting_date[$key])),
                    'description'       => $description_bulk[$key],
                    'generated_by'      => $this->session->userdata('hospitaladmin')['id'],
                    'apply_charge'      => $applied_total[$key],
                    'radio_discount'      => $radio_discount,
                    'discount_type'      => $discount_type[$key],
                );
                if($description_bulk[$key]==''){

                    $report_batch['description']=$this->getTestDescp($id);
                }
                $insert_id = $this->radio_model->testReportBatch($report_batch);

                $chargesCommission=$this->staff_model->radiologyStaffCommission($id,$consultant_doctor_bulk[$key]);
                if(!empty($chargesCommission)){
                    $commission_month=date('m',strtotime($reporting_date[$key]));
                    $commission_year=date('Y',strtotime($reporting_date[$key]));
                    if($chargesCommission['percentage_type']=='fixed'){
                        $comission_amount=$chargesCommission['staff_percentage'];
                    }else{
                        $comission_amount=($apply_charge_bulk[$key] * $chargesCommission['staff_percentage'])/100;
                    }

                    $commission_data=array(
                        'staff_id'=>$consultant_doctor_bulk[$key],
                        'bill_no'=>$insert_id,
                        'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date[$key])),
                        'comission_month'=>$commission_month,
                        'comission_year'=>$commission_year,
                        'comission_amount'=>$comission_amount,
                        'commission_type'=>'RADIOLOGY',
                        'commission_percentage'=>$chargesCommission['staff_percentage'],
                        'total_amount'=>$apply_charge_bulk[$key],

                    );
                $this->db->insert('monthly_comission', $commission_data);
                $getStaffCommissions=$this->staff_model->getstaffCharges($chargesCommission['charge_id']);
                if(!empty($getStaffCommissions)){
                    $allstaffCommission=array();
                    foreach($getStaffCommissions as $sc){
                        if($sc['percentage_type']=='fixed'){
                            $comission_amount=$sc['staff_percentage'];
                        }else{
                            $comission_amount=($apply_charge_bulk[$key] * $sc['staff_percentage'])/100;
                        }
                            $allstaffCommission[]=array(
                                'staff_id'=>$sc['staff_id'],
                                'bill_no'=>$insert_id,
                                'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date[$key])),
                                'comission_month'=>$commission_month,
                                'comission_year'=>$commission_year,
                                'comission_amount'=>$comission_amount,
                                'commission_type'=>'RADIOLOGY',
                                'commission_percentage'=>$sc['staff_percentage'],
                                'total_amount'=>$apply_charge_bulk[$key],
                        );
                    }
                    $this->db->insert_batch('monthly_comission', $allstaffCommission);
                }

            }

                $patientInfo=$this->common_model->getRow($patient_id);
                $comments="assign radiology test where patient name is ". $patientInfo['patient_name']." Bill No is ".$bill;
                $activityLog=$this->common_model->saveLog('radiology','add',$comments,$bill);

                $report_bulk_id[]=$insert_id;
                $paramet_details = $this->radio_model->getparameterBypathology($id);
                foreach ($paramet_details as $pkey => $pvalue) {
                    # code...

                    $paramet_insert_array = array('radiology_report_id' => $insert_id,
                        'parameter_id'       => $pvalue["parameter_id"],

                    );

                    $insert_into_parameter = $this->radio_model->addParameterforPatient($paramet_insert_array);

                }

                if (isset($_FILES["radiology_report"][$key]) && !empty($_FILES['radiology_report']['name'][$key])) {
                    $fileInfo = pathinfo($_FILES["radiology_report"]["name"][$key]);
                    $img_name = $insert_id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["radiology_report"]["tmp_name"][$key], "./uploads/radiology_report/" . $img_name);
                    $data_img = array('id' => $insert_id, 'radiology_report' => $img_name);
                    $this->radio_model->testReportBatch($data_img);
                }

            }
            $array = array('status' => 'success', 'id' => $report_bulk_id,'bil_no'=>$allBil,'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function generateRadioPatientTest()
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_add')) {
            access_denied();
        }
        //echo "<pre>";print_r($_POST);exit;
        $this->form_validation->set_rules('test_id_bulk[]', $this->lang->line('test') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'required');
        $this->form_validation->set_rules('reporting_date_bulk[]', $this->lang->line('reporting').' '.$this->lang->line('date'), 'required');
        $this->form_validation->set_rules('consultant_doctor_bulk[]', $this->lang->line('consultant').' '.$this->lang->line('doctor'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'radiology_id'   => form_error('test_id_bulk[]'),
                'patient_id'     => form_error('patient_id'),
                'reporting_date_bulk' => form_error('reporting_date_bulk[]'),
                'consultant_doctor_bulk' => form_error('consultant_doctor_bulk[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $this->load->model('common_model');
            $reporting_date = $this->input->post("reporting_date_bulk");
            $discount_type=$this->input->post('discount_type');
            $apply_charge=$this->input->post('apply_charge');
            $radiodiscount=$this->input->post('radio_discount');
            $consultant_doctor_bulk=$this->input->post('consultant_doctor_bulk');
            $description_bulk=$this->input->post('description_bulk');
            $apply_charge_bulk=$this->input->post('apply_charge_bulk');
            $applied_total=$this->input->post('applied_total');
            $allBil=array();
            $report_bulk_id=array();
            foreach($this->input->post('test_id_bulk') as $key=>$id)
            {
                $bill_no = $this->radio_model->getMaxId();
                if (empty($bill_no)) {
                    $bill_no = 0;
                }
                $bill           = $bill_no + 1;
                $patient_id     = $this->input->post('patient_id');
                $allBil[]=$bill_no;
                if($discount_type[$key]=='percentage' && $applied_total[$key] > 0){
                    $radio_discount=($applied_total[$key] * $radiodiscount[$key]/100);
                    $radio_discount=number_format($radio_discount,2);

                }
                if($discount_type[$key]=='fixed' && $apply_charge_bulk[$key] > 0){
                    $radio_discount= $radiodiscount[$key];
                }

                $report_batch = array(
                    'bill_no'           => $bill,
                    'radiology_id'      => $id,
                    'patient_id'        => $patient_id,
                    'organization_charge_id' => $this->input->post('organisation_bulk')[$key],
                    'customer_type'     => $this->input->post('customer_type'),
                    'consultant_doctor' => $consultant_doctor_bulk[$key],
                    'reporting_date'    => date('Y-m-d', $this->customlib->datetostrtotime($reporting_date[$key])),
                    'description'       => $description_bulk[$key],
                    'generated_by'      => $this->session->userdata('hospitaladmin')['id'],
                    'apply_charge'      => $applied_total[$key],
                    'radio_discount'      => $radio_discount,
                    'discount_type'      => $discount_type[$key],
                    'created_at'  => date('Y-m-d H:i:s', strtotime($reporting_date[$key])),
                );
                if($description_bulk[$key]==''){

                    $report_batch['description']=$this->getTestDescp($id);
                }
                $insert_id = $this->radio_model->testReportBatch($report_batch);

                $chargesCommission=$this->staff_model->radiologyStaffCommission($id,$consultant_doctor_bulk[$key]);
                if(!empty($chargesCommission)){
                    $commission_month=date('m',strtotime($reporting_date[$key]));
                    $commission_year=date('Y',strtotime($reporting_date[$key]));
                    if($chargesCommission['percentage_type']=='fixed'){
                        $comission_amount=$chargesCommission['staff_percentage'];
                    }else{
                        $comission_amount=($apply_charge_bulk[$key] * $chargesCommission['staff_percentage'])/100;
                    }

                    $commission_data=array(
                        'staff_id'=>$consultant_doctor_bulk[$key],
                        'bill_no'=>$insert_id,
                        'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date[$key])),
                        'comission_month'=>$commission_month,
                        'comission_year'=>$commission_year,
                        'comission_amount'=>$comission_amount,
                        'commission_type'=>'RADIOLOGY',
                        'commission_percentage'=>$chargesCommission['staff_percentage'],
                        'total_amount'=>$apply_charge_bulk[$key],

                    );
                    $this->db->insert('monthly_comission', $commission_data);
                }
                $getStaffCommissions=$this->staff_model->getRadioStaffCharges($id);
                if(!empty($getStaffCommissions)){
                    $allstaffCommission=array();
                    foreach($getStaffCommissions as $sc){
                        if($sc['percentage_type']=='fixed'){
                            $comission_amount=$sc['staff_percentage'];
                        }else{
                            $comission_amount=($apply_charge_bulk[$key] * $sc['staff_percentage'])/100;
                        }
                            $allstaffCommission[]=array(
                                'staff_id'=>$sc['staff_id'],
                                'bill_no'=>$insert_id,
                                'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date[$key])),
                                'comission_month'=>date('m',strtotime($reporting_date)),
                                'comission_year'=>date('Y',strtotime($reporting_date)),
                                'comission_amount'=>$comission_amount,
                                'commission_type'=>'RADIOLOGY',
                                'commission_percentage'=>$sc['staff_percentage'],
                                'total_amount'=>$apply_charge_bulk[$key],
                        );
                    }
                    $this->db->insert_batch('monthly_comission', $allstaffCommission);
                }   

                $patientInfo=$this->common_model->getRow($patient_id);
                $comments="assign radiology test where patient name is ". $patientInfo['patient_name']." Bill No is ".$bill;
                $activityLog=$this->common_model->saveLog('radiology','add',$comments,$bill);

                $report_bulk_id[]=$insert_id;
                $paramet_details = $this->radio_model->getparameterBypathology($id);
                foreach ($paramet_details as $pkey => $pvalue) {
                    # code...

                    $paramet_insert_array = array('radiology_report_id' => $insert_id,
                        'parameter_id'       => $pvalue["parameter_id"],

                    );

                    $insert_into_parameter = $this->radio_model->addParameterforPatient($paramet_insert_array);

                }

                if (isset($_FILES["radiology_report"][$key]) && !empty($_FILES['radiology_report']['name'][$key])) {
                    $fileInfo = pathinfo($_FILES["radiology_report"]["name"][$key]);
                    $img_name = $insert_id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["radiology_report"]["tmp_name"][$key], "./uploads/radiology_report/" . $img_name);
                    $data_img = array('id' => $insert_id, 'radiology_report' => $img_name);
                    $this->radio_model->testReportBatch($data_img);
                }

            }
            $array = array('status' => 'success', 'id' => $report_bulk_id,'bil_no'=>$allBil,'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function printToken($id, $report_id)
    {
        // $id = $this->input->post('id');
        // $report_id = json_decode($this->input->post('report_ids'));
        $reportIds = explode(",", $report_id);
        $log_message = 'REPORT IDS = ' . print_r($reportIds, true);
        log_message('debug', $log_message);
        
        $print_details            = $this->printing_model->get('', 'radiology');
        $data['print_details']    = $print_details;
        // $data['result']           = $this->radio_model->getBillDetails($id);

        $data['detail']           = $this->radio_model->getBillDetailsBulk($reportIds);

        $data['parameterdetails'] =$this->radio_model->getparameterDetailsforpatient(null, $reportIds);

        $this->load->view('admin/radio/print_token', $data);
        // // Load pdf library
        // $this->load->library('Pdf');
        // $customPaper = array(0,0,360,360);
        // $this->dompdf->set_paper($customPaper);
        // // Load HTML content
        // $this->dompdf->loadHtml($html);
        // ini_set('display_errors', 1);
        // // (Optional) Setup the paper size and orientation
        // // $this->dompdf->setPaper('A4', 'portrait');

        // // Render the HTML as PDF
        // $this->dompdf->render();

        // // Output the generated PDF (1 = download and 0 = preview)
        // $this->dompdf->stream("welcome.pdf", array("Attachment"=>0));
    }

    public function getBillDetailsBulk()
    {

        $id=$this->input->post('id');
        $report_id=$this->input->post('report_id');
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details            = $this->printing_model->get('', 'radiology');
        $data['print_details']    = $print_details;
       // $data['result']           = $this->radio_model->getBillDetails(null,$id);
        $data['detail']           = $this->radio_model->getBillDetailsBulk($report_id);
        $data['parameterdetails'] =$this->radio_model->getparameterDetailsforpatient(null,$report_id);
        $this->load->view('admin/radio/printBillBulk', $data);
    }

    public function getTestDescp($radioID)
    {
        $this->db->select('test_description');
        $this->db->from('radio');
        $this->db->where('id', $radioID);
        $qry=$this->db->get()->row_array();
        $result=$qry['test_description'];
        return $result;

    }

}
