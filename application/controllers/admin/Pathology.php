<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pathology extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->model('common_model');
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
        if (!$this->rbac->hasPrivilege('pathology test', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('parameter_name[]', $this->lang->line('parameter') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('test_name', $this->lang->line('test') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('short_name', $this->lang->line('short') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('test_type', $this->lang->line('test') . " " . $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('pathology_category_id', $this->lang->line('category') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard') . " " . $this->lang->line('charge'), 'required');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge') . " " . $this->lang->line('category'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'test_name'          => form_error('test_name'),
                'short_name'         => form_error('short_name'),
                'test_type'          => form_error('test_type'),
                'pathology_category_id'    => form_error('pathology_category_id'),
                'parameter_name[]'   => form_error('parameter_name[]'),
                'charge_category_id' => form_error('charge_category_id'),
                'code'               => form_error('code'),
                'standard_charge'    => form_error('standard_charge'),

            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        }
        else {
            $parameter_id = $this->input->post('parameter_name');
            $pathology    = array(
                'test_name'             => $this->input->post('test_name'),
                'short_name'            => $this->input->post('short_name'),
                'test_type'             => $this->input->post('test_type'),
                'pathology_category_id' => $this->input->post('pathology_category_id'),
                'sub_category'          => $this->input->post('sub_category'),
                'report_days'           => $this->input->post('report_days'),
                'method'                => $this->input->post('method'),
                'charge_id'             => $this->input->post('code'),
            );

            $insert_id = $this->pathology_model->add($pathology);

            $i = 0;
            foreach ($parameter_id as $key => $value) {
                $detail = array(
                    'pathology_id' => $insert_id,
                    'parameter_id' => $parameter_id[$i],
                );
                $data[] = $detail;

                $i++;
            }

            $this->pathology_model->addparameter($data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function addpatient()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_add')) {
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

        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pathology');
        $categoryName         = $this->pathology_category_model->getcategoryName();
        $data["categoryName"] = $categoryName;

        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;

        $data["title"]           = 'pathology';
        $data['charge_category'] = $this->pathology_model->getChargeCategory();
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $data["pathology_patients"] = $this->pathology_model->getPathologyList();
       // echo "<pre>";print_r($data["pathology_patients"]);exit;
        $result         = $this->pathology_model->getPathology();
        $data['organisation']   = $this->organisation_model->get();
        $data['result'] = $result;
        $this->load->view('layout/header');
        $this->load->view('admin/pathology/search',$data);
        $this->load->view('layout/footer');
    }

     public function report_search(){

        $draw = $_POST['draw'];
        $row = $_POST['start'];
        $rowperpage = $_POST['length']; // Rows display per page
        $columnIndex = $_POST['order'][0]['column']; // Column index
        $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition=array();
        if(!empty($_POST['search']['value'])) {
            $where_condition=array('search'=>$_POST['search']['value']);
        }
        $resultlist = $this->pathology_model->searchreport_datatable($where_condition);
        $total_result = $this->pathology_model->searchreport_datatable_count($where_condition);
        $data = array();

        foreach ($resultlist as $result_key => $result_value) {

            $convert_status=isset($result_value->status) && $result_value->status=="generated" ? "refund" : "generated";
            if (!empty($result_value->apply_charge)) {
                $charge = $result_value->apply_charge;
            } else {
                $charge = $detail->standard_charge;
            }
         $action ="<div class='rowoptionview'>";
            if (!empty($result_value->pathology_report)) {
               $action.="<a href=".base_url().'admin/pathology/download/'.$result_value->pathology_report." class='btn btn-default btn-xs'  data-toggle='tooltip' title='".$this->lang->line('download')."'><i class='fa fa-download' aria-hidden='true'></i></a>";
            }

            if ($this->rbac->hasPrivilege('add_patho_patient_test_report', 'can_edit')) {
            $action.="<a href='#' onclick='addParametervalue(".$result_value->id.",".$result_value->pathology_id.")' class='btn btn-default btn-xs '  data-toggle='tooltip' title='".$this->lang->line('add').'/'.$this->lang->line('edit').' '.$this->lang->line('parameter').' '.$this->lang->line('value')."'><i class='fa fa-pencil' aria-hidden='true'></i></a>";
            }

            if ($this->rbac->hasPrivilege('pathology_print_report', 'can_view')) {
            $action.="<a href='#' onclick='viewDetailReportAdvance(".$result_value->id.",".$result_value->pathology_id.")' class='btn btn-default btn-xs '  data-toggle='tooltip' title='"." Adavnced ".$this->lang->line('print').' '.$this->lang->line('report')."'><i class='fa fa-print' aria-hidden='true'></i></a>";

            }
            if ($this->rbac->hasPrivilege('pathology_print_report', 'can_view')) {
            $action.="<a href='#' onclick='viewDetailReport(".$result_value->id.",".$result_value->pathology_id.")' class='btn btn-default btn-xs '  data-toggle='tooltip' title='".$this->lang->line('print').' '.$this->lang->line('report')."'><i class='fa fa-print' aria-hidden='true'></i></a>";

            }

            if ($this->rbac->hasPrivilege('pathology_print_report', 'can_view')) {
            $action.="<a href='#' onclick='viewCommulativeReport(".$result_value->id.",".$result_value->pathology_id.",".$result_value->patient_id.")' class='btn btn-default btn-xs '  data-toggle='tooltip' title='".'Cumulative'.' '.$this->lang->line('report')."'><i class='fa fa-eye' aria-hidden='true'></i></a>";

            }

            if ($this->rbac->hasPrivilege('pathology_print_bill', 'can_view')) {
            $action.="<a href='#' onclick='viewDetailbill(".$result_value->id.",".$result_value->pathology_id.")' class='btn btn-default btn-xs '  data-toggle='tooltip' title='".$this->lang->line('print').' '.$this->lang->line('bill')."'><i class='fa fa-print' aria-hidden='true'></i></a>";

            }

            if ($this->rbac->hasPrivilege('add_patho_patient_test_report', 'can_delete')) {
            $action.="<a href='#' onclick='deleterecord(".$result_value->id.")' class='btn btn-default btn-xs '  data-toggle='tooltip' title='".$this->lang->line('delete')."'><i class='fa fa-trash' aria-hidden='true'></i></a>";
            }
            if ($this->rbac->hasPrivilege('add_patho_patient_test_report', 'can_delete')) {
            $action.="<a href='#' onclick=refundRecord('".$result_value->id."','".$convert_status."') class='btn btn-default btn-xs '  data-toggle='tooltip' title='Refund'><i class='fas fa-hand-holding-usd' aria-hidden='true'></i></a>";
            }

        $action.="</div'>";
        $nestedData=array();
        $charge=isset($result_value->pth_discount) && $result_value->pth_discount > 0  ? $charge - $result_value->pth_discount : $charge;
       //$nestedData[]= $result_value->id.$action;
        $nestedData[]= $result_value->bill_no.$action;
        $nestedData[]= $result_value->reporting_date;
        $nestedData[]= $result_value->patient_name;
        $nestedData[]= $result_value->test_name;
        $nestedData[]= $result_value->short_name;
        $nestedData[]= $result_value->name." ".$result_value->surname;
        $nestedData[]= $result_value->description;
        $nestedData[]= $result_value->status;
        $nestedData[]= $result_value->pth_discount;
        $nestedData[]= $charge;
        $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($draw),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result),  // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
            );

        echo json_encode($json_data);  // send data as json format

    }

    public function editparameter($id)
    {
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail         = $this->pathology_category_model->getparameterDetails($id);
        $data['detail'] = $detail;
        $this->load->view("admin/pathology/editparameter", $data);
    }

    public function parameterview($id, $value_id = '')
    {
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail         = $this->pathology_category_model->getparameterDetails($id, $value_id);
        $data['detail'] = $detail;
        //echo "<pre>";print_r($data);exit;
        $this->load->view("admin/pathology/parameterview", $data);
    }

    public function parameterdetails($id, $value_id = '')
    {
        // log_message('debug', 'PARAMETER ID: ' . $id);
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        // $detail = $this->pathology_category_model->getparameterDetailsforpatient($value_id);
        $detail = $this->pathology_category_model->getreportParams($id, $value_id);

        $data['detail'] = $detail;
        $this->load->view("admin/pathology/parameterdetails", $data);
    }

    public function getparameterdetails()
    {
        $id     = $this->input->get_post('id');
        $result = $this->pathology_category_model->getpathoparameter($id);
        echo json_encode($result);
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post("pathology_id");
        $result = $this->pathology_model->getDetails($id);
        echo json_encode($result);
    }

    public function update()
    {

        if (!$this->rbac->hasPrivilege('pathology test', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('test_name', $this->lang->line('test') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('short_name', $this->lang->line('short') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('test_type', $this->lang->line('test') . " " . $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('pathology_category_id', $this->lang->line('category') . " " . $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
       // $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge') . " " . $this->lang->line('category'), 'required');
        if ($this->form_validation->run() == false) {
            //echo "<pre>";print_r($_POST);exit;
            $msg = array(
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'test_type'             => form_error('test_type'),
                'pathology_category_id' => form_error('pathology_category_id'),
                'code'                  => form_error('code'),
                'test_type'                  => form_error('test_type'),
               // 'charge_category_id'    => form_error('charge_category_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            //echo "<pre>";print_r($_POST);exit;
            $id                         = $this->input->post('id');
            //$charge_category_id         = $this->input->post('charge_category_id');
            $pre_pathology_parameter_id = $this->input->post("previous_pathology_parameter_id[]");
           // $pre_pathology_id           = $this->input->post("previous_pathology_id");
            $pre_parameter_id = $this->input->post("previous_parameter_id[]");
            $new_parameter_id = $this->input->post("new_parameter_id[]");
            $parameter_id = $this->input->post("parameter_name[]");
            $insert_data = array();
            $pathology   = array(
                'id'                    => $id,
                'test_name'             => $this->input->post('test_name'),
                'short_name'            => $this->input->post('short_name'),
                'test_type'             => $this->input->post('test_type'),
                'pathology_category_id' => $this->input->post('pathology_category_id'),
                'sub_category'          => $this->input->post('sub_category'),
                'report_days'           => $this->input->post('report_days'),
                'method'                => $this->input->post('method'),
                'charge_id'             => $this->input->post('code'),
            );


            $i = 0;
            $j = 0;
            foreach ($parameter_id as $key => $value) {
                if (array_key_exists($i, $pre_pathology_parameter_id)) {
                    $detail = array(
                        'parameter_id' => $parameter_id[$i],
                        'id'           => $pre_pathology_parameter_id[$i],
                    );
                    $data[] = $detail;
                } else {
                    $j++;
                    $insert_detail = array(
                        'pathology_id' => $id,
                        'parameter_id' => $parameter_id[$i],
                    );
                    $insert_data[] = $insert_detail;
                }
                $i++;
            }

            $k         = $i - $j;
            $s         = 1;
            $condition = "";
            foreach ($data as $key => $value) {
                if ($s == $k) {
                    $coma = '';
                } else {
                    $coma = ',';
                }
                $condition .= "(" . $value['parameter_id'] . "," . $value['id'] . ")" . $coma;
                $s++;
            }

            $delete_arr = array();
            foreach ($pre_parameter_id as $pkey => $pvalue) {
                if (in_array($pvalue, $new_parameter_id)) {

                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $this->pathology_model->updateparameter($condition);

            if (!empty($insert_data)) {
                $this->pathology_model->addparameter($insert_data);
            }

            if (!empty($delete_arr)) {
                $this->pathology_model->delete_parameter($delete_arr);
            }

            $this->pathology_model->update($pathology);

            $patientInfo=$this->common_model->getSingleRow($column="id",$id,$select="test_name",$table="pathology");
            $comments="update pathology test where test name is ". $patientInfo['test_name'];
            $activityLog=$this->common_model->saveLog('pathology','update',$comments);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $Info=$this->common_model->getSingleRow($column='id',$id,$select="test_name",$table="pathology");
            $this->pathology_model->delete($id);
			$comments="delete pathology record where test name is ". $Info['test_name'];
			$activityLog=$this->common_model->saveLog('pathology','delete',$comments);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getPathology()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }

        $id     = $this->input->post('pathology_id');
        $result = $this->pathology_model->getPathology($id);
        $chargesID=$result['charge_id'];
        $result['getOrganiztionCharges']=$getOrganiztionCharges=$this->charge_model->getOrganisationCharges($chargesID);

        echo json_encode($result);
    }

    public function getPathologyReport()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id');
        $result                   = $this->pathology_model->getPathologyReport($id);
        $result['reporting_date'] = date($this->customlib->getSchoolDateFormat(), strtotime($result['reporting_date']));
        echo json_encode($result);
    }

    public function getPathologyparameterReport()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id');
        $result                   = $this->pathology_model->getPathologyparameterReport($id);
        $result['reporting_date'] = date($this->customlib->getSchoolDateFormat(), strtotime($result['reporting_date']));
        echo json_encode($result);
    }

    public function updateTestReport()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'required');
        $this->form_validation->set_rules('pathology_report', $this->lang->line('file'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'id'           => form_error('id'),
                'patient_name' => form_error('patient_name'),
                'apply_charge' => form_error('apply_charge'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $reporting_date = $this->input->post("reporting_date");

            $id           = $this->input->post('id');
            $report_batch = array(
                'id'                => $id,
                'patient_name'      => $this->input->post('patient_name'),
                'patient_id'        => $this->input->post('patient_id_patho'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => date('Y-m-d', $this->customlib->datetostrtotime($reporting_date)),
                'description'       => $this->input->post('description'),
                'apply_charge'      => $this->input->post('apply_charge'),
            );

            $this->pathology_model->updateTestReport($report_batch);

            if (!empty($_FILES['pathology_report']['name'])) {
                $config['upload_path']   = 'uploads/pathology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config['file_name']     = $_FILES['pathology_report']['name'];

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('pathology_report')) {
                    $uploadData = $this->upload->data();
                    $picture    = $uploadData['file_name'];
                } else {
                    $picture = "";
                }

                $data_img = array('id' => $id, 'pathology_report' => $picture);
                $this->pathology_model->updateTestReport($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function parameteraddvalue()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'id' => form_error('id'),

            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id = $this->input->post('id');
            $reporting_date = $this->input->post("reporting_date");
            $show_description=$this->input->post('show_dscp');
            $show_description=isset($show_description) && !empty($show_description) ? 1 :0;
            $show_clinical=$this->input->post('show_clinical');
            $show_clinical=isset($show_clinical) && !empty($show_clinical) ? 1 :0;
            $show_description_only=$this->input->post('show_description_only');
            $show_description_only=isset($show_description_only) && !empty($show_description_only) ? 1 :0;
            $report_batch   = array(
                'id'                => $id,
               // 'patient_id'        => $this->input->post('patient_id_patho'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => date('Y-m-d', $this->customlib->datetostrtotime($reporting_date)),
                'description'       => $this->input->post('description'),
                //'apply_charge'      => $this->input->post('apply_charge'),
                //'pth_discount'      => $this->input->post('pth_discount'),
                'show_description'      => $show_description,
                'show_clinical'      => $show_clinical,
                'show_description_only'      => $show_description_only,
                'status'      => 'updated',

            );
           // echo "<pre>";print_r($report_batch );exit;
            $parameter_id    = $this->input->post('parameter_id[]');
            $parameter_value = $this->input->post('parameter_value[]');
            $par_id          = $this->input->post('parid[]');
            $pathology_id    = $this->input->post('pathologyid');
            $update_id  = $this->input->post('update_id[]');
            $preport_id = $this->input->post('preport_id[]');

            $i               = 0;
            $parameter_array = array();
            foreach ($update_id as $pkey => $pvalue) {
                $parameter_value_arr = array(
                    'id'                     => $pvalue,
                    'pathology_report_id'    => $preport_id[$i],
                    'pathology_report_value' => $parameter_value[$i],
                );

                $this->pathology_model->addparametervalue($parameter_value_arr);
                $i++;
            }

            if (!empty($_FILES['pathology_report']['name'])) {
                $config['upload_path']   = 'uploads/pathology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config['file_name']     = $_FILES['pathology_report']['name'];
                $fileInfo                = pathinfo($_FILES["pathology_report"]["name"]);
                $img_name                = $id . '.' . $fileInfo['extension'];

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                move_uploaded_file($_FILES["pathology_report"]["tmp_name"], "./uploads/pathology_report/" . $img_name);

                $data_img = array('id' => $id, 'pathology_report' => $img_name);
                $this->pathology_model->updateTestReport($data_img);
            }

            $this->pathology_model->updateTestReport($report_batch);

            $Info=$this->common_model->getSingleRow($column='id',$id,$select="bill_no",$table="pathology_report");
			$comments="update pathology bill record where bill number is ". $Info['bill_no'];
			$activityLog=$this->common_model->saveLog('pathology bill','update',$comments,$Info['bill_no']);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function testReportBatch()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_add')) {
            access_denied();
        }
        //print_r($_POST);exit;
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'required');
        $this->form_validation->set_rules('pathology_id', $this->lang->line('pathology') . " " . $this->lang->line('id'), 'required');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'required');
        $this->form_validation->set_rules('pathology_report', $this->lang->line('file'), 'callback_handle_upload');
        $this->form_validation->set_rules('reporting_date', 'Reporting Date', 'required');
        $this->form_validation->set_rules('consultant_doctor', 'Referral Doctor', 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'       => form_error('patient_id'),
                'pathology_id'     => form_error('pathology_id'),
                'apply_charge'     => form_error('apply_charge'),
                'reporting_date'   => form_error('reporting_date'),
                'consultant_doctor'   => form_error('consultant_doctor'),
                'pathology_report' => form_error('pathology_report'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $this->load->model('common_model');
            $bill_no = $this->pathology_model->getMaxId();
            if (empty($bill_no)) {
                $bill_no = 0;
            }
            $bill           = $bill_no + 1;
            $id             = $this->input->post('pathology_id');
            $patient_id     = $this->input->post('patient_id');
            $reporting_date = $this->input->post("reporting_date");

            $report_batch = array(
                'bill_no'           => $bill,
                'pathology_id'      => $id,
                'patient_id'        => $patient_id,
                'customer_type'     => $this->input->post('customer_type'),
                'patient_name'      => $this->input->post('patient_name'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'organization_charge_id' => $this->input->post('organisation'),
                'reporting_date'    => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($reporting_date)),
                'description'       => $this->input->post('description'),
                'apply_charge'      => $this->input->post('apply_charge'),
                'generated_by'      => $this->session->userdata('hospitaladmin')['id'],
                'pathology_report'  => '',
            );

            $insert_id = $this->pathology_model->testReportBatch($report_batch);

            $patientInfo=$this->common_model->getRow($patient_id);
            $comments="assign pathology test where patient name is ". $patientInfo['patient_name']." Bill No is ".$bill;
            $activityLog=$this->common_model->saveLog('pathology','add',$comments,$bill);

            $paramet_details = $this->pathology_model->getparameterBypathology($id);
            foreach ($paramet_details as $pkey => $pvalue) {
                # code...

                $paramet_insert_array = array('pathology_report_id' => $insert_id,
                    'parameter_id'                                      => $pvalue["parameter_id"],

                );

                $insert_into_parameter = $this->pathology_model->addParameterforPatient($paramet_insert_array);
            }
            $append_prescription_lab='';
            if($insert_id && $this->input->post('append_prescription_lab')=='append_prescription_lab'){
                $this->load->model('common_model');
                $append_prescription_lab=$this->common_model->getSingleLabInvestigations($insert_id);
            }
            if (isset($_FILES["pathology_report"]) && !empty($_FILES['pathology_report']['name'])) {
                $fileInfo = pathinfo($_FILES["pathology_report"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["pathology_report"]["tmp_name"], "./uploads/pathology_report/" . $img_name);
                $data_img = array('id' => $insert_id, 'pathology_report' => $img_name);
                $this->pathology_model->testReportBatch($data_img);
            }

            $array = array('status' => 'success', 'id' => $insert_id,'append_prescription_lab'=>$append_prescription_lab, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['file']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Not Allowed");
                return false;
            }

            return true;
        }
        return true;
    }

    public function getTestReportBatch()
    {
        //echo "<pre>";print_r($this->session->all_userdata());exit;
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pathology');
        $id               = $this->input->post("id");
        $doctors          = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]  = $doctors;
        $patients         = $this->patient_model->getPatientListall();
        $data['patient_bills']         = $this->pathology_model->pathology_bill_report();
        //echo "<pre>";print_r($data['bill_no']);exit;
        $data["patients"] = $patients;

        $this->load->view('layout/header');
        $this->load->view('admin/pathology/reportDetail', $data);
        $this->load->view('layout/footer');
    }
    public function getTestsAndBills()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $id               = $this->input->post("id");
        $patients         = $this->pathology_model->pathology_test_and_bills_report($id);
        echo json_encode($patients);
    }
    public function getPatientBills()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $id               = $this->input->post("id");
        $patients         = $this->pathology_model->pathology_bill_report($id);
        echo json_encode($patients);
    }
    public function printTestReports($type, $p_name, $t_date, $r_date,$t_name='') {
        $data['details'] = $this->pathology_model->getPathoReports($type, $p_name, $t_date, $r_date,$t_name);
        $setting_result         = $this->setting_model->get();
        $data['settinglist']    = $setting_result;
        $this->load->view('admin/pathology/print_test_reports', $data);
    }

    public function generatePathologyPatientTest()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('patient_id_bulk', $this->lang->line('patient'), 'required');
        $this->form_validation->set_rules('pathology_id_bulk[]',"test Name ", 'required');
        $this->form_validation->set_rules('apply_charge_bulk[]', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'required');
        $this->form_validation->set_rules('pathology_report_bulk[]', $this->lang->line('file'), 'callback_handle_upload');
        $this->form_validation->set_rules('reporting_date_bulk[]', 'Reporting Date', 'required');
        $this->form_validation->set_rules('consultant_doctor_bulk[]', 'Consultant Doctor', 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id_bulk'       => form_error('patient_id_bulk'),
                'pathology_id_bulk'     => form_error('pathology_id_bulk[]'),
                'apply_charge_bulk'     => form_error('apply_charge_bulk[]'),
                'reporting_date_bulk'   => form_error('reporting_date_bulk[]'),
                'pathology_report_bulk' => form_error('pathology_report_bulk[]'),
                'consultant_doctor_bulk' => form_error('consultant_doctor_bulk[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $this->load->model('common_model');
            $newIDs = '';
            $bill_no = $this->pathology_model->getMaxId();
                if (empty($bill_no)) {
                    $bill_no = 0;
                }
                $bill           = $bill_no + 1;
            foreach($this->input->post('pathology_id_bulk') as $key=>$pathology){


                $id             = $this->input->post('pathology_id_bulk')[$key];
                $patient_id     = $this->input->post('patient_id_bulk');
                $reporting_date = $this->input->post("reporting_date_bulk")[$key];
                $discount_type = isset($this->input->post("discount_type")[$key]) ? $this->input->post("discount_type")[$key] : '';
                $pth_discount = isset($this->input->post("pth_discount")[$key]) ? $this->input->post("pth_discount")[$key] : '';

                if($discount_type=='percentage' && $pth_discount > 0 && $this->input->post('applied_total')[$key] > 0){
                    $pth_discount=($this->input->post('applied_total')[$key] * $pth_discount/100);
                    $pth_discount=number_format($pth_discount,2);

                }
                if($discount_type=='fixed' && $pth_discount > 0 && $this->input->post('applied_total')[$key] > 0){
                    $pth_discount=$pth_discount;
                }
                $report_batch = array(
                    'bill_no'           => $bill,
                    'pathology_id'      => $id,
                    'patient_id'        => $patient_id,
                    'customer_type'     => $this->input->post('customer_type'),
                    'patient_name'      => $this->input->post('patient_name'),
                    'organization_charge_id' => $this->input->post('organisation_bulk')[$key],
                    'reporting_date'    => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($reporting_date)),
                    'description'       => $this->input->post('description_bulk')[$key],
                    'consultant_doctor'       => $this->input->post('consultant_doctor_bulk')[$key],
                    'apply_charge'      => $this->input->post('applied_total')[$key],
                    'generated_by'      => $this->session->userdata('hospitaladmin')['id'],
                    'pth_discount'      =>  $pth_discount,
                    'pth_discount_type'      => $discount_type,
                    'pathology_report'  => '',
                    'created_at'  => date('Y-m-d H:i:s', strtotime($reporting_date)),
                );

                $insert_id = $this->pathology_model->testReportBatch($report_batch);

                $patientInfo=$this->common_model->getRow($patient_id);
                $comments="assign pathology test where patient name is ". $patientInfo['patient_name']." Bill No is ".$bill;
                $activityLog=$this->common_model->saveLog('pathology','add',$comments,$bill);

                $newIDs .= $insert_id.",";
                $paramet_details = $this->pathology_model->getparameterBypathology($id);
                foreach ($paramet_details as $pkey => $pvalue) {
                    # code...

                    $paramet_insert_array = array('pathology_report_id' => $insert_id,
                        'parameter_id'                                      => $pvalue["parameter_id"],

                    );

                    $insert_into_parameter = $this->pathology_model->addParameterforPatient($paramet_insert_array);
                }

                if (isset($_FILES["pathology_report_bulk"][$key]) && !empty($_FILES['pathology_report_bulk'][$key]['name'])) {
                    $fileInfo = pathinfo($_FILES["pathology_report_bulk"][$key]["name"]);
                    $img_name = $insert_id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["pathology_report_bulk"][$key]["tmp_name"], "./uploads/pathology_report/" . $img_name);
                    $data_img = array('id' => $insert_id, 'pathology_report' => $img_name);
                    $this->pathology_model->testReportBatch($data_img);
                }
                    $select="pathology_commission";
                    $chargesCommission=$this->staff_model->chargesStaffCommission($id,$this->input->post('consultant_doctor_bulk')[$key]);
                    if(!empty($chargesCommission)){
                        $commission_month=date('m',strtotime($reporting_date));
                        $commission_year=date('Y',strtotime($reporting_date));
                        if($chargesCommission['percentage_type']=='fixed'){
                            $comission_amount=$chargesCommission['staff_percentage'];
                        }else{
                            $comission_amount=($this->input->post('apply_charge_bulk')[$key] * $chargesCommission['staff_percentage'])/100;
                        }

                        $commission_data=array(
                            'bill_no'=>$insert_id,
                            'staff_id'=>$this->input->post('consultant_doctor_bulk')[$key],
                            'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date)),
                            'comission_month'=>$commission_month,
                            'comission_year'=>$commission_year,
                            'comission_amount'=>$comission_amount,
                            'commission_type'=>'PATHOLOGY',
                            'commission_percentage'=>$chargesCommission['staff_percentage'],
                            'total_amount'=>$this->input->post('apply_charge_bulk')[$key],

                        );
                        $this->db->insert('monthly_comission', $commission_data);
                    }
                    $getStaffCommissions=$this->staff_model->getPathologyStaffCharges($id);
                    if(!empty($getStaffCommissions)){
                        $allstaffCommission=array();
                        foreach($getStaffCommissions as $sc){
                            if($sc['percentage_type']=='fixed'){
                                $comission_amount=$sc['staff_percentage'];
                            }else{
                                $comission_amount=($this->input->post('apply_charge_bulk')[$key] * $sc['staff_percentage'])/100;
                            }
                                $allstaffCommission[]=array(
                                    'bill_no'=>$insert_id,
                                    'staff_id'=>$sc['staff_id'],
                                    'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date)),
                                    'comission_month'=>date('m',strtotime($reporting_date)),
                                    'comission_year'=>date('Y',strtotime($reporting_date)),
                                    'comission_amount'=>$comission_amount,
                                    'commission_type'=>'PATHOLOGY',
                                    'commission_percentage'=>$sc['staff_percentage'],
                                    'total_amount'=>$this->input->post('apply_charge_bulk')[$key],
                            );
                        }
                        $this->db->insert_batch('monthly_comission', $allstaffCommission);
                    }


            }


            $array = array('status' => 'success', 'id' => $insert_id,'new_ids'=>$newIDs, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function printToken($type, $p_name, $t_date, $r_date,$t_name='') {
        $data['details'] = $this->pathology_model->getPathoReports($type, $p_name, $t_date, $r_date,$t_name);
        $setting_result         = $this->setting_model->get();
        $data['settinglist']    = $setting_result;
        $this->load->view('admin/pathology/print_token', $data);
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

    public function printTestReceipts($type, $p_name, $t_date, $r_date) {
        $data['details'] = $this->pathology_model->getPathoTestReceipts($type, $p_name, $t_date, $r_date);
        $this->load->view('admin/pathology/print_test_receipts', $data);
    }

    public function getBillDetails($id, $parameter_id)
    {

        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result         = $this->pathology_model->getBillDetails($id);
        $data['result'] = $result;
        $detail         = $this->pathology_model->getAllBillDetails($id);
        $data['detail'] = $detail;
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;
        $this->load->view('admin/pathology/printBill', $data);
    }

    public function getCheckedBillDetails($patho_arr)
    {

        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result         = $this->pathology_model->getCheckedBillDetails($id);
        $data['result'] = $result;
        $this->load->view('admin/pathology/printCheckedBill', $data);
    }

    public function getReportDetails($id, $parameter_id)
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        $result                   = $this->pathology_model->getBillDetails($id);
        //echo "<pre>";print_r($result);exit;
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        // $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $parameterdetails = $this->pathology_category_model->getreportParams($parameter_id, $id);
        $data['parameterdetails'] = $parameterdetails;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
            /////////////////////Barcode/////////////////////////
            $code = '000'.$result["bill_no"];
            $this->load->library('zend');
            $this->zend->load('Zend/Barcode');
            $imageResource = Zend_Barcode::factory('code128', 'image', array('text'=>$code), array())->draw();
            imagepng($imageResource, 'uploads/barcode/'.$code.'.png');
            $data['barcode'] = 'uploads/barcode/'.$code.'.png';
            /////////////////////Qr Code//////////////////////////
            require_once(APPPATH.'libraries/phpqrcode/qrlib.php');
            $this->load->helper('url');
            //$SERVERFILEPATH=$_SERVER['DOCUMENT_ROOT'].'/rehmanhospital/hms3.1/uploads/qr_barcode/';
            $SERVERFILEPATH='uploads/qr_barcode/';
            $qrtext = base_url('userlogin');
            $text = $qrtext;
            $folder = $SERVERFILEPATH;
            $file_name1 = "Patient-Qrcod.png";
            $file_name = $folder.$file_name1;
            QRcode::png($qrtext,$file_name);
            $base_url=base_url('uploads/qr_barcode/');
            $data['qr_code']=$base_url.$file_name1;
        } else {
            $data["print"] = 'no';
        }
        $this->load->view('admin/pathology/printReport', $data);
    }
    public function getReportDetailsAdvance($id, $parameter_id)
    {
        $data['sch_setting'] = $this->setting_model->getSetting();
        //echo "<pre>";print_r($data['sch_setting']);exit;
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        $result                   = $this->pathology_model->getBillDetails($id);
        //echo "<pre>";print_r($result);exit;
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        // $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $parameterdetails = $this->pathology_category_model->getreportParams($parameter_id, $id);
        $data['parameterdetails'] = $parameterdetails;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
            /////////////////////Barcode/////////////////////////
            $code = '000'.$result["bill_no"];
            $this->load->library('zend');
            $this->zend->load('Zend/Barcode');
            $imageResource = Zend_Barcode::factory('code128', 'image', array('text'=>$code), array())->draw();
            imagepng($imageResource, 'uploads/barcode/'.$code.'.png');
            $data['barcode'] = 'uploads/barcode/'.$code.'.png';
            /////////////////////Qr Code//////////////////////////
            require_once(APPPATH.'libraries/phpqrcode/qrlib.php');
            $this->load->helper('url');
            //$SERVERFILEPATH=$_SERVER['DOCUMENT_ROOT'].'/rehmanhospital/hms3.1/uploads/qr_barcode/';
            $SERVERFILEPATH='uploads/qr_barcode/';
            $qrtext = base_url('userlogin');
            $text = $qrtext;
            $folder = $SERVERFILEPATH;
            $file_name1 = "Patient-Qrcod.png";
            $file_name = $folder.$file_name1;
            QRcode::png($qrtext,$file_name);
            $base_url=base_url('uploads/qr_barcode/');
            $data['qr_code']=$base_url.$file_name1;
        } else {
            $data["print"] = 'no';
        }
        $this->load->view('admin/pathology/printReportAdvance', $data);
    }

    public function getCommulativeReport($id, $parameter_id, $patient_id)
    {
        $data['sch_setting'] = $this->setting_model->getSetting();
        //echo "<pre>";print_r($data['sch_setting']);exit;
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        $result                   = $this->pathology_model->getBillDetails($id);
        //echo "<pre>";print_r($result);exit;
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        // $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $reportDetails = $this->pathology_category_model->getMultiReportParams($parameter_id, $id, $patient_id);
        $data['reportDetails'] = $reportDetails;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
            /////////////////////Barcode/////////////////////////
            $code = '000'.$result["bill_no"];
            $this->load->library('zend');
            $this->zend->load('Zend/Barcode');
            $imageResource = Zend_Barcode::factory('code128', 'image', array('text'=>$code), array())->draw();
            imagepng($imageResource, 'uploads/barcode/'.$code.'.png');
            $data['barcode'] = 'uploads/barcode/'.$code.'.png';
            /////////////////////Qr Code//////////////////////////
            require_once(APPPATH.'libraries/phpqrcode/qrlib.php');
            $this->load->helper('url');
            //$SERVERFILEPATH=$_SERVER['DOCUMENT_ROOT'].'/rehmanhospital/hms3.1/uploads/qr_barcode/';
            $SERVERFILEPATH='uploads/qr_barcode/';
            $qrtext = base_url('userlogin');
            $text = $qrtext;
            $folder = $SERVERFILEPATH;
            $file_name1 = "Patient-Qrcod.png";
            $file_name = $folder.$file_name1;
            QRcode::png($qrtext,$file_name);
            $base_url=base_url('uploads/qr_barcode/');
            $data['qr_code']=$base_url.$file_name1;
        } else {
            $data["print"] = 'no';
        }
        $this->load->view('admin/pathology/printCommulativeReport', $data);
    }

    public function printCumulativeTestReport($patient_id, $report_ids)
    {
        $data['sch_setting'] = $this->setting_model->getSetting();
        //echo "<pre>";print_r($data['sch_setting']);exit;
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        $result                   = $this->pathology_model->getFirstBillDetails($patient_id, $report_ids);
        //echo "<pre>";print_r($result);exit;
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getFirstAllBillDetails($patient_id, $report_ids);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        // $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $reportDetails = $this->pathology_category_model->getSelectedReportParams($patient_id, $report_ids);

        $data['reportDetails'] = $reportDetails;

        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $this->load->view('admin/pathology/printCommulativeTestReport', $data);
    }

    public function printSingleTestReport($patient_id, $report_ids)
    {
        $data['sch_setting'] = $this->setting_model->getSetting();
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        $result                   = $this->pathology_model->getFirstBillDetails($patient_id, $report_ids);
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getFirstAllBillDetails($patient_id, $report_ids);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
    
        // Get report details and extract only the first object
        $reportDetails = $this->pathology_category_model->getSelectedReportParams($patient_id, $report_ids);
  
        $data['reportDetails'] = $reportDetails;
    
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
    
        $this->load->view('admin/pathology/printSingleTestReport', $data);
    }
    
    public function download($doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/pathology_report/" . $doc;
        $data     = file_get_contents($filepath);
        force_download($doc, $data);
    }

    public function deleteTestReport($id)
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_delete')) {
            access_denied();
        }
        $Info=$this->common_model->getSingleRow($column='id',$id,$select="bill_no",$table="pathology_report");
		$comments="delete pathology bill record where bill number is ". $Info['bill_no'];
		$activityLog=$this->common_model->saveLog('pathology bill','delete',$comments,$Info['bill_no']);
        $this->pathology_model->deleteTestReport($id);
    }

    public function pathologyReport()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/pathology/pathologyreport');
        $select = 'pathology_report.*, pathology.id, pathology.short_name,staff.name,staff.surname,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name';
        $join   = array(
            'JOIN pathology ON pathology_report.pathology_id = pathology.id',
            'LEFT JOIN staff ON pathology_report.consultant_doctor = staff.id',
            'JOIN charges ON charges.id = pathology.charge_id', 'JOIN patients ON patients.id = pathology_report.patient_id',
        );
        $where_check=array('pathology_report.status="generated"');
        $table_name = "pathology_report";
        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        if (empty($search_type)) {
            $search_type = "";
            //echo $search_type;exit;
            $resultlist  = $this->report_model->getReport($select, $join, $table_name,$where_check);
            // echo $this->db->last_query();exit;
        } else {
            $search_table  = "pathology_report";
            $search_column = "reporting_date";
            $result_list    = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column,'',$where_check);
            $resultlist    = $result_list['main_data'];
        }

        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist;
        $this->load->view('layout/header');
        $this->load->view('admin/pathology/pathologyReport.php', $data);
        $this->load->view('layout/footer');
    }

    public function RefundTestReport($id,$status)
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_delete')) {
            access_denied();
        }
        $this->pathology_model->RefundTestReport($id,$status);
    }

    public function PatientTests()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $id               = $this->input->post("id");
        $patients         = $this->pathology_model->getPathologyPatientTest($id);
        echo json_encode($patients);
    }
    public function bulkGeneratePatientTest()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('patient_id_bulk', $this->lang->line('patient'), 'required');
        $this->form_validation->set_rules('pathology_id_bulk[]',"test Name ", 'required');
        $this->form_validation->set_rules('apply_charge_bulk[]', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'required');
        $this->form_validation->set_rules('pathology_report_bulk[]', $this->lang->line('file'), 'callback_handle_upload');
        $this->form_validation->set_rules('reporting_date_bulk[]', 'Reporting Date', 'required');
        $this->form_validation->set_rules('consultant_doctor_bulk[]', 'Consultant Doctor', 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id_bulk'       => form_error('patient_id_bulk'),
                'pathology_id_bulk'     => form_error('pathology_id_bulk[]'),
                'apply_charge_bulk'     => form_error('apply_charge_bulk[]'),
                'reporting_date_bulk'   => form_error('reporting_date_bulk[]'),
                'pathology_report_bulk' => form_error('pathology_report_bulk[]'),
                'consultant_doctor_bulk' => form_error('consultant_doctor_bulk[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $this->load->model('common_model');
            $newIDs = '';
            $bill_no = $this->pathology_model->getMaxId();
                if (empty($bill_no)) {
                    $bill_no = 0;
                }
                $bill           = $bill_no + 1;
            foreach($this->input->post('pathology_id_bulk') as $key=>$pathology){


                $id             = $this->input->post('pathology_id_bulk')[$key];
                $patient_id     = $this->input->post('patient_id_bulk');
                $reporting_date = $this->input->post("reporting_date_bulk")[$key];
                $discount_type = isset($this->input->post("discount_type")[$key]) ? $this->input->post("discount_type")[$key] : '';
                $pth_discount = isset($this->input->post("pth_discount")[$key]) ? $this->input->post("pth_discount")[$key] : '';

                if($discount_type=='percentage' && $pth_discount > 0 && $this->input->post('applied_total')[$key] > 0){
                    $pth_discount=($this->input->post('applied_total')[$key] * $pth_discount/100);
                    $pth_discount=number_format($pth_discount,2);

                }
                if($discount_type=='fixed' && $pth_discount > 0 && $this->input->post('applied_total')[$key] > 0){
                    $pth_discount=$pth_discount;
                }
                $report_batch = array(
                    'bill_no'           => $bill,
                    'pathology_id'      => $id,
                    'patient_id'        => $patient_id,
                    'customer_type'     => $this->input->post('customer_type'),
                    'patient_name'      => $this->input->post('patient_name'),
                    'organization_charge_id' => $this->input->post('organisation_bulk')[$key],
                    'reporting_date'    => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($reporting_date)),
                    'description'       => $this->input->post('description_bulk')[$key],
                    'consultant_doctor'       => $this->input->post('consultant_doctor_bulk')[$key],
                    'apply_charge'      => $this->input->post('applied_total')[$key],
                    'generated_by'      => $this->session->userdata('hospitaladmin')['id'],
                    'pth_discount'      =>  $pth_discount,
                    'pth_discount_type'      => $discount_type,
                    'pathology_report'  => '',
                );

                $insert_id = $this->pathology_model->testReportBatch($report_batch);

                $patientInfo=$this->common_model->getRow($patient_id);
                $comments="assign pathology test where patient name is ". $patientInfo['patient_name']." Bill No is ".$bill;
                $activityLog=$this->common_model->saveLog('pathology','add',$comments,$bill);

                $newIDs .= $insert_id.",";
                $paramet_details = $this->pathology_model->getparameterBypathology($id);
                foreach ($paramet_details as $pkey => $pvalue) {
                    # code...

                    $paramet_insert_array = array('pathology_report_id' => $insert_id,
                        'parameter_id'                                      => $pvalue["parameter_id"],

                    );

                    $insert_into_parameter = $this->pathology_model->addParameterforPatient($paramet_insert_array);
                }

                if (isset($_FILES["pathology_report_bulk"][$key]) && !empty($_FILES['pathology_report_bulk'][$key]['name'])) {
                    $fileInfo = pathinfo($_FILES["pathology_report_bulk"][$key]["name"]);
                    $img_name = $insert_id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["pathology_report_bulk"][$key]["tmp_name"], "./uploads/pathology_report/" . $img_name);
                    $data_img = array('id' => $insert_id, 'pathology_report' => $img_name);
                    $this->pathology_model->testReportBatch($data_img);
                }
                    $select="pathology_commission";
                    $chargesCommission=$this->staff_model->chargesStaffCommission($id,$this->input->post('consultant_doctor_bulk')[$key]);
                    if(!empty($chargesCommission)){
                        $commission_month=date('m',strtotime($reporting_date));
                        $commission_year=date('Y',strtotime($reporting_date));
                        if($chargesCommission['percentage_type']=='fixed'){
                            $comission_amount=$chargesCommission['staff_percentage'];
                        }else{
                            $comission_amount=($this->input->post('apply_charge_bulk')[$key] * $chargesCommission['staff_percentage'])/100;
                        }

                        $commission_data=array(
                            'bill_no'=>$insert_id,
                            'staff_id'=>$this->input->post('consultant_doctor_bulk')[$key],
                            'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date)),
                            'comission_month'=>$commission_month,
                            'comission_year'=>$commission_year,
                            'comission_amount'=>$comission_amount,
                            'commission_type'=>'PATHOLOGY',
                            'commission_percentage'=>$chargesCommission['staff_percentage'],
                            'total_amount'=>$this->input->post('apply_charge_bulk')[$key],

                        );
                        $this->db->insert('monthly_comission', $commission_data);
                        $getStaffCommissions=$this->staff_model->getstaffCharges($chargesCommission['charge_id']);
                        if(!empty($getStaffCommissions)){
                            $allstaffCommission=array();
                            foreach($getStaffCommissions as $sc){
                                if($sc['percentage_type']=='fixed'){
                                    $comission_amount=$sc['staff_percentage'];
                                }else{
                                    $comission_amount=($this->input->post('apply_charge_bulk')[$key] * $sc['staff_percentage'])/100;
                                }
                                    $allstaffCommission[]=array(
                                        'bill_no'=>$insert_id,
                                        'staff_id'=>$sc['staff_id'],
                                        'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date)),
                                        'comission_month'=>$commission_month,
                                        'comission_year'=>$commission_year,
                                        'comission_amount'=>$comission_amount,
                                        'commission_type'=>'PATHOLOGY',
                                        'commission_percentage'=>$sc['staff_percentage'],
                                        'total_amount'=>$this->input->post('apply_charge_bulk')[$key],
                                );
                            }
                            $this->db->insert_batch('monthly_comission', $allstaffCommission);
                        }
                    }else{
                        $staff_info=$this->staff_model->getStaffCommission($select,$this->input->post('consultant_doctor_bulk')[$key]);
                        if($this->input->post('apply_charge_bulk')[$key] > 0 && $staff_info['pathology_commission'] > 0){
                            $commission_month=date('m',strtotime($reporting_date));
                            $commission_year=date('Y',strtotime($reporting_date));
                            $comission_amount=($this->input->post('apply_charge_bulk')[$key] * $staff_info['pathology_commission'])/100;
                            $commission_data=array(
                                'staff_id'=>$this->input->post('consultant_doctor_bulk')[$key],
                                'bill_no'=>$bill,
                                'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date)),
                                'comission_month'=>$commission_month,
                                'comission_year'=>$commission_year,
                                'comission_amount'=>$comission_amount,
                                'commission_type'=>'PATHOLOGY',
                                'commission_percentage'=>$staff_info['pathology_commission'],
                                'total_amount'=>$this->input->post('apply_charge_bulk')[$key],

                            );
                            $this->db->insert('monthly_comission', $commission_data);
                        }
                    }


            }


            $array = array('status' => 'success', 'id' => $insert_id,'new_ids'=>$newIDs, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function printTestBill($p_name, $bill_no ) {

        $data['print_details']         = $this->printing_model->get('', 'pathology');
        $data['result']   = $this->pathology_model->getTestBillDetailsInfo($bill_no);
        $data['details']                   = $this->pathology_model->getTestBillDetails($bill_no);
        //echo "<pre>";print_r($data);exit;
            /////////////////////Barcode/////////////////////////
            $code = '000'.$bill_no;
            $this->load->library('zend');
            $this->zend->load('Zend/Barcode');
            $imageResource = Zend_Barcode::factory('code128', 'image', array('text'=>$code), array())->draw();
            imagepng($imageResource, 'uploads/barcode/'.$code.'.png');
            $data["print"] = 'yes';
            $data['barcode'] = 'uploads/barcode/'.$code.'.png';
            /////////////////////Qr Code//////////////////////////
            require_once(APPPATH.'libraries/phpqrcode/qrlib.php');
            $this->load->helper('url');
            //$SERVERFILEPATH=$_SERVER['DOCUMENT_ROOT'].'/rehmanhospital/hms3.1/uploads/qr_barcode/';
            $SERVERFILEPATH='uploads/qr_barcode/';
            $qrtext = base_url('userlogin');
            $text = $qrtext;
            $folder = $SERVERFILEPATH;
            $file_name1 = "Patient-Qrcod.png";
            $file_name = $folder.$file_name1;
            QRcode::png($qrtext,$file_name);
            $base_url=base_url('uploads/qr_barcode/');
            $data['qr_code']=$base_url.$file_name1;

        $this->load->view('admin/pathology/printTestReportBill', $data);
    }

    public function getorganizationCharge()
    {
        $org_id=$this->input->post('org_id');
        $testInfos=$this->input->post('testInfo');
        $data=$this->pathology_model->getPathologyCharges($org_id,$testInfos);
        $totalCharge=$data['totalCharge'];
        echo json_encode($totalCharge);
    }

    public function generateBulkTestPatient()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('patient_id_bulk', $this->lang->line('patient'), 'required');
        //$this->form_validation->set_rules('pathology_id_bulk',"test Name ", 'required');
       // $this->form_validation->set_rules('apply_charge_bulk[]', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'required');
        //$this->form_validation->set_rules('pathology_report_bulk[]', $this->lang->line('file'), 'callback_handle_upload');
        $this->form_validation->set_rules('reporting_date_test', 'Reporting Date', 'required');
        $this->form_validation->set_rules('consultant_doctor_test', 'Consultant Doctor', 'required');
        //$this->form_validation->set_rules('organisation_tpa', 'TPA', 'required');
        $this->form_validation->set_rules('total_charges', 'Total Charges', 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id_bulk'       => form_error('patient_id_bulk'),
               // 'pathology_id_bulk'     => form_error('pathology_id_bulk[]'),
                'reporting_date_test'     => form_error('reporting_date_test'),
                'organisation_tpa'   => form_error('organisation_tpa'),
                'total_charges' => form_error('total_charges'),
                'consultant_doctor_test' => form_error('consultant_doctor_test'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $newIDs = '';
            $bill_no = $this->pathology_model->getMaxId();
                if (empty($bill_no)) {
                    $bill_no = 0;
                }
                $bill           = $bill_no + 1;
            foreach($this->input->post('test_name') as $key=>$pathology){


                $id             = $this->input->post('test_name')[$key];
                //echo $id;exit;
                $patient_id     = $this->input->post('patient_id_bulk');
                $reporting_date = $this->input->post("reporting_date_test");
                $orgID=$this->input->post('organisation_tpa');
                if($orgID==''){
                    $apply_charges=$this->pathology_model->getSingleTest($pathology);
                }else{
                    $get_charges=$this->pathology_model->getPathologyCharges($orgID,$pathology);
                    $apply_charges=$get_charges['totalCharge'];
                }
                $report_batch = array(
                    'bill_no'           => $bill,
                    'pathology_id'      => $id,
                    'patient_id'        => $patient_id,
                    'customer_type'     => $this->input->post('customer_type'),
                    'patient_name'      => $this->input->post('patient_name'),
                    'organization_charge_id' => $this->input->post('organisation_tpa'),
                    'reporting_date'    => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($reporting_date)),
                    'description'       => $this->input->post('description_test'),
                    'consultant_doctor'       => $this->input->post('consultant_doctor_test'),
                    'apply_charge'      => $apply_charges,
                    'generated_by'      => $this->session->userdata('hospitaladmin')['id'],
                    'pathology_report'  => '',
                );

                $insert_id = $this->pathology_model->testReportBatch($report_batch);
                $newIDs .= $insert_id.",";
                $paramet_details = $this->pathology_model->getparameterBypathology($id);
                foreach ($paramet_details as $pkey => $pvalue) {
                    # code...

                    $paramet_insert_array = array('pathology_report_id' => $insert_id,
                        'parameter_id'                                      => $pvalue["parameter_id"],

                    );

                    $insert_into_parameter = $this->pathology_model->addParameterforPatient($paramet_insert_array);
                }

                // if (isset($_FILES["pathology_report_bulk"][$key]) && !empty($_FILES['pathology_report_bulk'][$key]['name'])) {
                //     $fileInfo = pathinfo($_FILES["pathology_report_bulk"][$key]["name"]);
                //     $img_name = $insert_id . '.' . $fileInfo['extension'];
                //     move_uploaded_file($_FILES["pathology_report_bulk"][$key]["tmp_name"], "./uploads/pathology_report/" . $img_name);
                //     $data_img = array('id' => $insert_id, 'pathology_report' => $img_name);
                //     $this->pathology_model->testReportBatch($data_img);
                // }
                    $select="pathology_commission";
                    $staff_info=$this->staff_model->getStaffCommission($select,$this->input->post('consultant_doctor_test'));
                    if($apply_charges > 0 && $staff_info['pathology_commission'] > 0){
                        $commission_month=date('m',strtotime($reporting_date));
                        $commission_year=date('Y',strtotime($reporting_date));
                        $comission_amount=($apply_charges * $staff_info['pathology_commission'])/100;
                        $commission_data=array(
                            'staff_id'=>$this->input->post('consultant_doctor_test'),
                            'appointment_date'=>date('Y-m-d H:i:s',strtotime($reporting_date)),
                            'comission_month'=>$commission_month,
                            'comission_year'=>$commission_year,
                            'comission_amount'=>$comission_amount,
                            'commission_type'=>'PATHOLOGY',
                            'commission_percentage'=>$staff_info['pathology_commission'],
                            'total_amount'=>$apply_charges,

                        );
                        $this->db->insert('monthly_comission', $commission_data);
                    }

            }


            $array = array('status' => 'success', 'id' => $insert_id,'new_ids'=>$newIDs, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_test_sample.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_test_sample.csv';

        force_download($name, $data);
    }

    public function import()
    {
        if (!$this->rbac->hasPrivilege('pathology test', 'can_view')) {
            access_denied();
        }
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_csv_upload');
        $fields                   = array('test_name', 'short_name', 'test_type', 'pathology_category_id', 'sub_category', 'method', 'report_days', 'charge_category_id', 'code', 'standard_charge','charge_type');
        $data["fields"]           = $fields;

        if ($this->form_validation->run() == false) {
            $msg = array(
                'file'                 => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            $this->load->view('layout/header');
            $this->load->view('admin/pathology/search', $data);
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
                            $test_name = $test_data[$i]["test_name"];
                            $pathology_category_id = $test_data[$i]["pathology_category_id"];
                            if (!empty($test_name)) {
                                if ($this->pathology_model->check_test_exists($test_name,$pathology_category_id)) {
                                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');

                                    $insert_id = "";
                                } else {
                                    $new_pathology_category_id=$this->pathology_model->checkPathologyCreated($pathology_category_id);
                                    $chargeID=$this->pathology_model->checkChargesID($test_data[$i]["charge_category_id"],$test_data[$i]["code"],$test_data[$i]["standard_charge"],$test_data[$i]["charge_type"]);
                                    $testPathologyData=array(
                                        'test_name'=>$test_name,
                                        'short_name'=>$test_data[$i]["short_name"],
                                        'test_type'=>$test_data[$i]["test_type"],
                                        'pathology_category_id'=>$new_pathology_category_id,
                                        'sub_category'=>isset($test_data[$i]["sub_category"]) ? $test_data[$i]["sub_category"] : '',
                                        'method'=>$test_data[$i]["method"],
                                        'report_days'=>$test_data[$i]["report_days"],
                                        'charge_id'=>$chargeID,
                                    );
                                    $insert_id = $this->db->insert('pathology',$testPathologyData);
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
                redirect('admin/pathology/search');
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
}
