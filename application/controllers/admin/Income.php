<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->search_type = $this->config->item('search_type');
    }

    public function index()
    {

        if (!$this->module_lib->hasActive('income')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'finance');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title']       = 'Add Income';
        $data['title_list']  = 'Recent Incomes';
        $income_result       = $this->income_model->get();
        $data['incomelist']  = $income_result;
        $incomeHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $incomeHead;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        $this->session->set_userdata('top_menu', 'Income');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title']      = 'Add Income';
        $data['title_list'] = 'Recent Incomes';
        $this->form_validation->set_rules('inc_head_id[]', $this->lang->line('income_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'inc_head_id[]' => form_error('inc_head_id[]'),
                'name'          => form_error('name'),
                'date'          => form_error('date'),
                'amount'        => form_error('amount'),
                'documents'     => form_error('documents'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'inc_head_id' => $this->input->post('inc_head_id'),
                'name'        => $this->input->post('name'),
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'amount'      => $this->input->post('amount'),
                'invoice_no'  => $this->input->post('invoice_no'),
                'note'        => $this->input->post('description'),
                'documents'   => $this->input->post('documents'),
            );
            $insert_id = $this->income_model->add($data);
            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $fileInfo = pathinfo($_FILES["documents"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["documents"]["tmp_name"], "./uploads/hospital_income/" . $img_name);
                $data_img = array('id' => $insert_id, 'documents' => 'uploads/hospital_income/' . $img_name);
                $this->income_model->add($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function download($documents)
    {
        $this->load->helper('download');
        $filepath = "./uploads/hospital_income/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('income', 'can_view')) {
            access_denied();
        }
        $data['title']  = 'Fees Master List';
        $income         = $this->income_model->get($id);
        $data['income'] = $income;
        $this->load->view('layout/header', $data);
        $this->load->view('income/incomeShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('income', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Fees Master List';
        $this->income_model->remove($id);
        redirect('admin/income/index');
    }

    public function create()
    {
        $data['title'] = 'Add Fees Master';
        $this->form_validation->set_rules('income', 'Fees Master', 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('income/incomeCreate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'income' => $this->input->post('income'),
            );
            $this->income_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">income added successfully</div>');
            redirect('income/index');
        }
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
            $file_type         = $_FILES["documents"]['type'];
            $file_size         = $_FILES["documents"]["size"];
            $file_name         = $_FILES["documents"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @filesize($_FILES['documents']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Extension Not Allowed');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "Error File Uploading");
                return false;
            }

            return true;
        }
        return true;
    }

    public function getDataByid($id)
    {
        $data['title']       = 'Edit Fees Master';
        $data['id']          = $id;
        $income              = $this->income_model->get($id);
        $data['income']      = $income;
        $expnseHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $expnseHead;
        $this->load->view('admin/income/editModal', $data);
    }

    public function edit($id)
    {
        $data['title']       = 'Edit Fees Master';
        $data['id']          = $id;
        $income              = $this->income_model->get($id);
        $data['income']      = $income;
        $data['title_list']  = 'Fees Master List';
        $income_result       = $this->income_model->get();
        $data['incomelist']  = $income_result;
        $expnseHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $expnseHead;
        $this->form_validation->set_rules('inc_head_id', $this->lang->line('income_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('inc_head_id[]', $this->lang->line('income_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'inc_head_id[]' => form_error('inc_head_id[]'),
                'amount'        => form_error('amount'),
                'name'          => form_error('name'),
                'date'          => form_error('date'),
                'documents'     => form_error('documents'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'id'          => $id,
                'inc_head_id' => $this->input->post('inc_head_id'),
                'name'        => $this->input->post('name'),
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'amount'      => $this->input->post('amount'),
                'invoice_no'  => $this->input->post('invoice_no'),
                'note'        => $this->input->post('description'),
            );
            $insert_id = $this->income_model->add($data);
            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $fileInfo = pathinfo($_FILES["documents"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["documents"]["tmp_name"], "./uploads/hospital_income/" . $img_name);
                $data_img = array('id' => $id, 'documents' => 'uploads/hospital_income/' . $img_name);
                $this->income_model->add($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }

        echo json_encode($array);
    }

    public function incomeSearch()
    {
        if (!$this->rbac->hasPrivilege('income_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/income/incomesearch');
        $select     = 'income.id,income.date,income.name,income.invoice_no,income.amount,income.documents,income.note,income_head.income_category,income.inc_head_id';
        $join       = array('JOIN income_head ON income.inc_head_id = income_head.id');
        $table_name = "income";

        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        if (empty($search_type)) {
            $search_type = "";
            $listMessage = $this->report_model->getReport($select, $join, $table_name);
        } else {
            $search_table     = "income";
            $search_column    = "date";
            $additional       = array();
            $additional_where = array();
            $listMessage      = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column);
        }
        $data['resultList']  = $listMessage;
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeSearch', $data);
        //echo "<pre>";print_r($data);exit;
        $this->load->view('layout/footer', $data);
    }

    public function transactionreport($value = '')
    {
        if (!$this->rbac->hasPrivilege('transaction_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/income/transactionreport');
        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        $data['kpo_type']=$kpo_type = $this->input->post("kpoid");
        if (!empty($kpo_type)) {
            $additional_whereopd = array(
                "opd_details.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereipd = array(
                "ipd_details.generated_by = '" . $kpo_type . "'",
            );
            $additional_wherepharma = array(
                "pharmacy_bill_basic.generated_by = '" . $kpo_type . "'",
            );
            $additional_wherepath = array(
                "pathology_report.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereradio = array(
                "radiology_report.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereot = array(
                "operation_theatre.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereblood_issue = array(
                "blood_issue.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereamb = array(
                "ambulance_call.generated_by = '" . $kpo_type . "'",
            );
        } else {
            $additional_whereopd = array('1 = 1');
        }
        $parameter = array('OPD' => array('label' => 'OPD', 'table'               => 'opd_details', 'search_table' => 'opd_details',
            'search_column'                           => 'appointment_date', 'select' => 'opd_details.*,kpo.id as kpo_id,kpo.name as kpo_name,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,staff.name, staff.surname',
            'join'                                    => array('LEFT JOIN staff ON opd_details.cons_doctor = staff.id',
                'LEFT JOIN patients ON opd_details.patient_id = patients.id',
                'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
            ),
            'where'=>$additional_whereopd
            ),
            'IPD'                    => array('label' => 'IPD', 'table' => 'ipd_details', 'search_table' => 'payment',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,ipd_details.ipd_no,payment.date,payment.paid_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id',
                'join'                                    => array(
                    'JOIN staff ON ipd_details.cons_doctor = staff.id',
                    'JOIN patients ON ipd_details.patient_id = patients.id',
                    'JOIN payment ON payment.ipd_id = ipd_details.id',
                    'LEFT JOIN staff as kpo ON kpo.id = ipd_details.generated_by',
                ),
                'where'=>$additional_whereipd,
            ),
            'Pharmacy'               => array('label' => 'Pharmacy', 'table' => 'pharmacy_bill_basic', 'search_table' => 'pharmacy_bill_basic',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,pharmacy_bill_basic.*,patients.patient_name as patient_name,pharmacy_bill_basic.bill_no as reff,pharmacy_bill_basic.net_amount as amount,patients.patient_unique_id',
                'join'                                    => array('JOIN patients ON patients.id = pharmacy_bill_basic.patient_id', 'LEFT JOIN staff as kpo ON kpo.id = pharmacy_bill_basic.generated_by'),
                'where'=>$additional_wherepharma,
            ),
            'Pathology'              => array('label' => 'Pathology', 'table' => 'pathology_report', 'search_table' => 'pathology_report',
                'search_column'                           => 'reporting_date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,pathology_report.*, pathology_report.apply_charge as amount,pathology_report.id as reff,pathology_report.reporting_date as date,pathology.id, pathology.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name,patients.patient_unique_id',
                'join'                                    => array(
                    'JOIN pathology ON pathology_report.pathology_id = pathology.id',
                    'LEFT JOIN staff ON pathology_report.consultant_doctor = staff.id',
                    'JOIN charges ON charges.id = pathology.charge_id', 'JOIN patients ON pathology_report.patient_id=patients.id','LEFT JOIN staff as kpo ON kpo.id = pathology_report.generated_by'),
                    'where'=>$additional_wherepath,
            ),
            'Radiology'              => array('label' => 'Radiology', 'table' => 'radiology_report', 'search_table' => 'radiology_report',
                'search_column'                           => 'reporting_date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,radiology_report.*,radiology_report.apply_charge as amount,radiology_report.reporting_date as date, radiology_report.id as reff,radio.id, radio.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name,patients.patient_unique_id',
                'join'                                    => array(
                    'JOIN radio ON radiology_report.radiology_id = radio.id',
                    'JOIN staff ON radiology_report.consultant_doctor = staff.id',
                    'JOIN charges ON charges.id = radio.charge_id', 'JOIN patients ON radiology_report.patient_id=patients.id',
                    'LEFT JOIN staff as kpo ON kpo.id = radiology_report.generated_by',
                ),
                'where'=>$additional_whereradio,

            ),
            'Operation_Theatre'      => array('label' => 'Operation Theatre', 'table' => 'operation_theatre', 'search_table' => 'operation_theatre',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,operation_theatre.*,operation_theatre.id as reff,patients.id as pid,patients.patient_unique_id,patients.patient_name,charges.id as cid,charges.charge_category,charges.code,charges.description,charges.standard_charge, operation_theatre.apply_charge as amount',
                'join'                                    => array(
                    'JOIN patients ON operation_theatre.patient_id=patients.id',
                    'JOIN staff ON staff.id = operation_theatre.consultant_doctor',
                    'JOIN charges ON operation_theatre.charge_id = charges.id',
                    'LEFT JOIN staff as kpo ON kpo.id = operation_theatre.generated_by',
                ),
                'where'=>$additional_whereot,
            ),

            'Blood_Bank'             => array('label' => 'Blood Bank', 'table'        => 'blood_issue',
                'search_column'                           => 'created_at', 'search_table' => 'blood_issue',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,blood_issue.*,blood_issue.id as reff,blood_issue.created_at as date,patients.patient_name',
                'join'                                    => array('JOIN patients ON blood_issue.recieve_to=patients.id','LEFT JOIN staff as kpo ON kpo.id = blood_issue.generated_by'),
                'where'=>$additional_whereblood_issue,
            ),

            'ambulance'              => array('label' => 'Ambulance', 'table' => 'ambulance_call', 'search_table' => 'ambulance_call',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,ambulance_call.*,ambulance_call.id as reff,patients.patient_name',
                'join'                                    => array('JOIN patients ON ambulance_call.patient_name=patients.id','LEFT JOIN staff as kpo ON kpo.id = ambulance_call.generated_by'),
                'where'=>$additional_whereamb,
            ),
            'income'                 => array('label' => 'General Income', 'table' => 'income', 'search_table' => 'income',
                'search_column'                           => 'date',
                'select'                                  => 'income.*,income.name as patient_name,income.invoice_no as reff',
                'join'                                    => array('JOIN income_head ON income.inc_head_id = income_head.id')),

                'expense'                => array('label' => 'Expenses', 'table' => 'expenses', 'search_table' => 'expenses',
                'search_column'                           => 'date',
                'select'                                  => 'expenses.*,expenses.name as patient_name,expenses.invoice_no as reff',
                'join'                                    => array('JOIN expense_head ON expenses.exp_head_id = expense_head.id')),
            'payroll'                => array('label' => 'Payroll', 'table' => 'staff_payslip', 'search_table' => 'staff_payslip',
                'search_column'                           => 'payment_date',
                'select'                                  => 'staff_payslip.*,staff.name as patient_name,staff.surname,staff.employee_id as patient_unique_id,staff_payslip.payment_date as date,staff_payslip.net_salary as amount,staff_payslip.id as reff',
                'join'                                    => array('JOIN staff ON staff_payslip.staff_id = staff.id')),
        );

        $i                 = 0;
        $data["parameter"] = $parameter;
        foreach ($parameter as $key => $value) {
            # code...

            $select     = $parameter[$key]['select'];
            $join       = $parameter[$key]['join'];
            $table_name = $parameter[$key]['table'];
            $where = isset($parameter[$key]['where']) ? $parameter[$key]['where'] : [];

            if (empty($search_type)) {

                $search_type = "";
                $resultList  = $this->report_model->getReport($select, $join, $table_name);
            } else {

                $search_table     = $parameter[$key]['search_table'];
                $search_column    = $parameter[$key]['search_column'];
                $result_List       = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column,$where);
                $resultList       = isset($result_List['main_data']) && !empty($result_List['main_data']) ? $result_List['main_data'] : '';
            }

            $rd[$parameter[$key]['label']]         = $resultList;
            $data['parameter'][$key]['resultList'] = $resultList;
            if(!empty($resultList)){
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($resultList, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($resultList, 'kpo_name'));
            }

            $i++;
        }
        if (!empty($kpo_type)) {
            $additional_where2 = array(
                "ipd_billing.generated_by = '" . $kpo_type . "'",
            );
        } else {
            $additional_where2 = array('1 = 1');
        }
        $resultList2 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,kpo.name as kpo_name,ipd_details.ipd_no,ipd_billing.date,ipd_billing.net_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id',
        $join = array('JOIN staff ON ipd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON ipd_details.patient_id = patients.id',
            //'LEFT  JOIN payment ON payment.ipd_id = ipd_details.id',
            'LEFT JOIN ipd_billing ON ipd_billing.ipd_id = ipd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = ipd_billing.generated_by',
        ), $table_name = 'ipd_details', $search_type, $search_table = 'ipd_billing', $search_column = 'date',$additional_where2);

        if (!empty($resultList2['main_data'])) {
            foreach ($resultList2['main_data'] as $key => $value) {
                array_push($rd["IPD"], $value);
                array_push($data['parameter']["IPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }

        if (!empty($kpo_type)) {
            $additional_where3 = array(
                "opd_details.generated_by = '" . $kpo_type . "'",
            );
        } else {
            $additional_where3 = array('1 = 1');
        }

        $resultList3 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_billing.date,opd_billing.net_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            //'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_billing', $search_column = 'date',$additional_where3);

        if (!empty($resultList3['main_data'])) {
            foreach ($resultList3['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }
        }

        if (!empty($kpo_type)) {
            $additional_where4 = array(
                "opd_details.generated_by = '" . $kpo_type . "'",
            );
        } else {
            $additional_where4 = array('1 = 1');
        }

        $resultList4 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_payment.date,opd_payment.paid_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            //'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_payment', $search_column = 'date',$additional_where4);

        if (!empty($resultList4['main_data'])) {
            foreach ($resultList4['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }

        $data["resultlist"]  = $rd;
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;

        $data['kpo_filterData']=$this->getAllKpos();
        $this->load->view('layout/header', $data);
        //echo "<pre>";print_r($data);exit;
        $this->load->view('admin/income/transactionReport', $data);
        $this->load->view('layout/footer', $data);
    }
    public function incomereport($value = '')
    {
        if (!$this->rbac->hasPrivilege('transaction_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/income/incomereport');
        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        $data['kpo_type']=$kpo_type = $this->input->post("kpoid");
        if (!empty($kpo_type)) {
            $additional_whereopd = array(
                "opd_details.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereipd = array(
                "ipd_details.generated_by = '" . $kpo_type . "'",
            );
            $additional_wherepharma = array(
                "pharmacy_bill_basic.generated_by = '" . $kpo_type . "'",
            );
            $additional_wherepath = array(
                "pathology_report.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereradio = array(
                "radiology_report.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereot = array(
                "operation_theatre.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereblood_issue = array(
                "blood_issue.generated_by = '" . $kpo_type . "'",
            );
            $additional_whereamb = array(
                "ambulance_call.generated_by = '" . $kpo_type . "'",
            );
        } else {
            $additional_whereopd = array('1 = 1');
        }
        $parameter =
         array(
            'OPD' => array('label' => 'OPD', 'table'               => 'opd_details', 'search_table' => 'opd_details',
            'search_column'                           => 'created_at', 'select' => 'opd_details.*,patients.mrno,opd_details.opd_discount as discount,SUM(smc.comission_amount) as staff_share,mc.comission_amount as doctor_share,dpt.department_name,kpo.id as kpo_id,kpo.name as kpo_name,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.address,staff.name,org.organisation_name,staff.surname',
            // 'search_column'                           => 'appointment_date', 'select' => 'opd_details.*,mc.comission_amount as doctor_share,dpt.department_name,kpo.id as kpo_id,kpo.name as kpo_name,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.address,staff.name,org.organisation_name,staff.surname',
            'join'                                    => array('LEFT JOIN staff ON opd_details.cons_doctor = staff.id',
                'LEFT JOIN patients ON opd_details.patient_id = patients.id',
                'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
                'LEFT JOIN organisation as org ON org.id = opd_details.organization_charge_id',
                'LEFT JOIN department as dpt ON dpt.id = opd_details.department',
                "LEFT JOIN monthly_comission as mc ON mc.bill_no = opd_details.id AND mc.staff_id=opd_details.cons_doctor AND mc.commission_type='OPD'",
               "LEFT JOIN monthly_comission as smc ON smc.bill_no = opd_details.id AND smc.staff_id != opd_details.cons_doctor AND smc.commission_type='OPD'",
            ),
            'where'=>$additional_whereopd,
            'group_by' => 'opd_details.id, mc.comission_amount, dpt.department_name, kpo.id, kpo.name, patients.id, staff.name, org.organisation_name, staff.surname'
            ),
            'Pathology'              => array('label' => 'Pathology', 'table' => 'pathology_report', 'search_table' => 'pathology_report',
                //'search_column'                           => 'reporting_date',
                'search_column'                           => 'created_at',
                'select'                                  => 'organisation.organisation_name,patients.mrno,mc.comission_amount as doctor_share,SUM(smc.comission_amount) as staff_share,pathology_report.pth_discount as discount,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.address,kpo.id as kpo_id,kpo.name as kpo_name,pathology_report.*, pathology_report.apply_charge as amount,pathology_report.id as reff,pathology_report.reporting_date as date,pathology.id, pathology.short_name,pathology.test_name as department_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name,patients.patient_unique_id,staff.surname,staff.name,patients.address',
                'join'                                    => array(
                    'JOIN pathology ON pathology_report.pathology_id = pathology.id',
                    'LEFT JOIN staff ON pathology_report.consultant_doctor = staff.id',
                    'LEFT JOIN charges ON charges.id = pathology.charge_id',
                    'LEFT JOIN patients ON pathology_report.patient_id=patients.id',
                    'LEFT JOIN organisations_charges ON organisations_charges.id=pathology_report.organization_charge_id',
                    'LEFT JOIN organisation ON organisation.id=organisations_charges.org_id',
                    'LEFT JOIN staff as kpo ON kpo.id = pathology_report.generated_by',
                    "LEFT JOIN monthly_comission as mc ON mc.bill_no = pathology_report.id AND mc.staff_id=pathology_report.consultant_doctor AND mc.commission_type='PATHOLOGY'",
                    "LEFT JOIN monthly_comission as smc ON smc.bill_no = pathology_report.id AND smc.staff_id != pathology_report.consultant_doctor AND smc.commission_type='PATHOLOGY'",

                ),
                    'where'=>$additional_wherepath,
                    'group_by' => 'pathology_report.id, mc.comission_amount, kpo.id, kpo.name, patients.id, staff.name, staff.surname'
            ),
            'Radiology'              => array('label' => 'Radiology', 'table' => 'radiology_report', 'search_table' => 'radiology_report',
               // 'search_column'                           => 'reporting_date',
                'search_column'                           => 'created_at',
                'select'                                  => 'charges.charge_category,organisation.organisation_name,patients.mrno,radio.test_name as department_name,mc.comission_amount as doctor_share,SUM(smc.comission_amount) as staff_share,radiology_report.radio_discount as discount,kpo.id as kpo_id,kpo.name as kpo_name,radiology_report.*,radiology_report.apply_charge as amount,radiology_report.apply_charge as amount,radiology_report.reporting_date as date, radiology_report.id as reff,radio.id, radio.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name,patients.patient_unique_id,staff.surname,staff.name,patients.address',
                'join'                                    => array(
                    'LEFT JOIN radio ON radiology_report.radiology_id = radio.id',
                    'LEFT JOIN staff ON radiology_report.consultant_doctor = staff.id',
                    'LEFT JOIN charges ON charges.id = radio.charge_id',
                    'LEFT JOIN patients ON radiology_report.patient_id=patients.id',
                    'LEFT JOIN organisations_charges ON organisations_charges.id=radiology_report.organization_charge_id',
                    'LEFT JOIN organisation ON organisation.id=organisations_charges.org_id',

                    'LEFT JOIN staff as kpo ON kpo.id = radiology_report.generated_by',
                    "LEFT JOIN monthly_comission as mc ON mc.bill_no = radiology_report.id AND mc.staff_id=radiology_report.consultant_doctor AND mc.commission_type='RADIOLOGY'",
                    "LEFT JOIN monthly_comission as smc ON smc.bill_no = radiology_report.id AND smc.staff_id != radiology_report.consultant_doctor AND smc.commission_type='RADIOLOGY'",
                ),
                'where'=>$additional_whereradio,
                'group_by' => 'radiology_report.id, mc.comission_amount, kpo.id, kpo.name, patients.id, staff.name, staff.surname'

            ),
        );

        $i                 = 0;
        $data["parameter"] = $parameter;
        foreach ($parameter as $key => $value) {
            # code...

            $select     = $parameter[$key]['select'];
            $join       = $parameter[$key]['join'];
            $table_name = $parameter[$key]['table'];
            $where = isset($parameter[$key]['where']) ? $parameter[$key]['where'] : [];
            $group_by = isset($parameter[$key]['group_by']) ? $parameter[$key]['group_by'] : [];

            if (empty($search_type)) {

                $search_type = "";
                $resultList  = $this->report_model->getReport($select, $join, $table_name);
            } else {

                $search_table     = $parameter[$key]['search_table'];
                $search_column    = $parameter[$key]['search_column'];
                $result_List       = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column,$where,$ab='',$bs='',$group_by);
                //echo $this->db->last_query();
                $resultList       = isset($result_List['main_data']) && !empty($result_List['main_data']) ? $result_List['main_data'] : '';
            }

            $rd[$parameter[$key]['label']]         = $resultList;
            $data['parameter'][$key]['resultList'] = $resultList;
            if(!empty($resultList)){
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($resultList, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($resultList, 'kpo_name'));
            }

            $i++;
        }
        if (!empty($kpo_type)) {
            $additional_where2 = array(
                "ipd_billing.generated_by = '" . $kpo_type . "'",
            );
        } else {
            $additional_where2 = array('1 = 1');
        }

        if (!empty($kpo_type)) {
            $additional_where3 = array(
                "opd_details.generated_by = '" . $kpo_type . "'",
            );
        } else {
            $additional_where3 = array('1 = 1');
        }

        $resultList3 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,patients.mrno,kpo.name as kpo_name,opd_details.opd_no,opd_billing.date,opd_billing.net_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            //'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_billing', $search_column = 'date',$additional_where3);

        if (!empty($resultList3['main_data'])) {
            foreach ($resultList3['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }
        }

        if (!empty($kpo_type)) {
            $additional_where4 = array(
                "opd_details.generated_by = '" . $kpo_type . "'",
            );
        } else {
            $additional_where4 = array('1 = 1');
        }

        $resultList4 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_payment.date,opd_payment.paid_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            //'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_payment', $search_column = 'date',$additional_where4);

        if (!empty($resultList4['main_data'])) {
            foreach ($resultList4['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }
        $data["resultlist"]  = $rd;
        // echo "<pre>";print_r($rd);exit;
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;

        $data['kpo_filterData']=$this->getAllKpos();
        $this->load->view('layout/header', $data);
        if($this->input->post('search')=='export_pdf'){
            $this->exportIncomeReportPdf($data);
        }
        if($this->input->post('search')=='export_summary_pdf'){
            $data["kpo_id"] = $this->input->post("kpoid");
            if($search_type == 'period') {
                $data["from_date"] = $this->input->post("date_from");
                $data["to_date"] = $this->input->post("date_to");
            }
            $this->exportIncomeReportSummaryPdf($data);

        }
        if($this->input->post('search')=='export_summary_new_pdf'){
            $data["kpo_id"] = $this->input->post("kpoid");
            if($search_type == 'period') {
                $data["from_date"] = $this->input->post("date_from");
                $data["to_date"] = $this->input->post("date_to");
            }
            $this->exportIncomeReportSummaryNewPdf($data);

        }
        else{
            $this->load->view('admin/income/incomeReport', $data);
        }
        $this->load->view('layout/footer', $data);
    }

    public function exportIncomeReportPdf($data)
    {
        $html  = $this->load->view('admin/income/export_income_report_pdf',$data, true);
        $this->load->library('pdf');
        $this->dompdf->set_paper("letter", "landscape");
        $customPaper = array(0,0,360,360);
        $this->dompdf->loadHtml($html);
        ini_set('display_errors', 1);
        // Render the HTML as PDF
        $this->dompdf->render();
        $canvas =  $this->dompdf->getCanvas();
        $date=date('d-M-Y h:i A',strtotime(date('Y-m-d H:i:s')));
        $totalPages = $this->dompdf->getCanvas()->get_page_count();

        // $canvas->page_text(270, 780, "Page : {PAGE_NUM}", null, 10, [0, 0, 0]);
        // $canvas->page_text(270, 760, $date, null, 10, [0, 0, 0]);
        $canvas->page_text(510, 5, "Page {PAGE_NUM} of {$totalPages}", null, 10, [0, 0, 0]);
        $canvas->page_text(30, 760, $date, null, 10, [0, 0, 0]);
        $this->dompdf->stream("Income Report.pdf", array("Attachment"=>1));
    }

    public function exportIncomeReportSummaryPdf($data)
    {
        $html  = $this->load->view('admin/income/export_income_report_summary_pdf',$data, true);
        $this->load->library('pdf');
        $this->dompdf->set_paper("letter", "landscape");
        $customPaper = array(0,0,360,360);
        $this->dompdf->loadHtml($html);
        ini_set('display_errors', 1);
        // Render the HTML as PDF
        $this->dompdf->render();
        $canvas =  $this->dompdf->getCanvas();
        $date=date('d-M-Y h:i A',strtotime(date('Y-m-d H:i:s')));
        $totalPages = $this->dompdf->getCanvas()->get_page_count();

        // $canvas->page_text(270, 780, "Page : {PAGE_NUM}", null, 10, [0, 0, 0]);
        // $canvas->page_text(270, 760, $date, null, 10, [0, 0, 0]);
        $canvas->page_text(510, 5, "Page {PAGE_NUM} of {$totalPages}", null, 10, [0, 0, 0]);
        $canvas->page_text(30, 760, $date, null, 10, [0, 0, 0]);
        $this->dompdf->stream("Income Summary Report.pdf", array("Attachment"=>1));
    }
    public function exportIncomeReportSummaryNewPdf($data)
    {
        $html  = $this->load->view('admin/income/export_income_report_summary_new_pdf',$data, true);
       // exit;
        $this->load->library('pdf');
        $this->dompdf->set_paper("letter", "landscape");
        $customPaper = array(0,0,360,360);
        $this->dompdf->loadHtml($html);
        ini_set('display_errors', 1);
        // Render the HTML as PDF
        $this->dompdf->render();
        $canvas =  $this->dompdf->getCanvas();
        $date=date('d-M-Y h:i A',strtotime(date('Y-m-d H:i:s')));
        $totalPages = $this->dompdf->getCanvas()->get_page_count();

        // $canvas->page_text(270, 780, "Page : {PAGE_NUM}", null, 10, [0, 0, 0]);
        // $canvas->page_text(270, 760, $date, null, 10, [0, 0, 0]);
        $canvas->page_text(510, 5, "Page {PAGE_NUM} of {$totalPages}", null, 10, [0, 0, 0]);
        $canvas->page_text(30, 760, $date, null, 10, [0, 0, 0]);
        $this->dompdf->stream("Income Summary Report.pdf", array("Attachment"=>1));
    }

    public function incomegroup()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/incomegroup');
        if (isset($_POST['search_type'])) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }
        $data['head_id'] = $head_id = "";
        if (isset($_POST['head']) && $_POST['head'] != '') {
            $data['head_id'] = $head_id = $_POST['head'];
        }
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $incomeList          = $this->income_model->searchincomegroup($search_type, $head_id);
        $data['headlist']    = $this->incomehead_model->get();
        $data['incomeList']  = $incomeList;
        //echo "<pre>";print_r($data['incomeList']);exit;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/groupincomeReport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getAllKpos()
    {
        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        $parameter = array('OPD' => array('label' => 'OPD', 'table'               => 'opd_details', 'search_table' => 'opd_details',
            'search_column'                           => 'appointment_date', 'select' => 'opd_details.*,kpo.id as kpo_id,kpo.name as kpo_name,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,staff.name, staff.surname',
            'join'                                    => array('LEFT JOIN staff ON opd_details.cons_doctor = staff.id',
                'LEFT JOIN patients ON opd_details.patient_id = patients.id',
                'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
            ),

            ),
            'IPD'                    => array('label' => 'IPD', 'table' => 'ipd_details', 'search_table' => 'payment',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,ipd_details.ipd_no,payment.date,payment.paid_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id',
                'join'                                    => array(
                    'JOIN staff ON ipd_details.cons_doctor = staff.id',
                    'JOIN patients ON ipd_details.patient_id = patients.id',
                    'JOIN payment ON payment.ipd_id = ipd_details.id',
                    'LEFT JOIN staff as kpo ON kpo.id = ipd_details.generated_by',
                ),

            ),
            'Pharmacy'               => array('label' => 'Pharmacy', 'table' => 'pharmacy_bill_basic', 'search_table' => 'pharmacy_bill_basic',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,pharmacy_bill_basic.*,patients.patient_name as patient_name,pharmacy_bill_basic.bill_no as reff,pharmacy_bill_basic.net_amount as amount',
                'join'                                    => array('JOIN patients ON patients.id = pharmacy_bill_basic.patient_id', 'LEFT JOIN staff as kpo ON kpo.id = pharmacy_bill_basic.generated_by'),

            ),
            'Pathology'              => array('label' => 'Pathology', 'table' => 'pathology_report', 'search_table' => 'pathology_report',
                'search_column'                           => 'reporting_date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,pathology_report.*, pathology_report.apply_charge as amount,pathology_report.id as reff,pathology_report.reporting_date as date,pathology.id, pathology.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name',
                'join'                                    => array(
                    'JOIN pathology ON pathology_report.pathology_id = pathology.id',
                    'LEFT JOIN staff ON pathology_report.consultant_doctor = staff.id',
                    'JOIN charges ON charges.id = pathology.charge_id', 'JOIN patients ON pathology_report.patient_id=patients.id','LEFT JOIN staff as kpo ON kpo.id = pathology_report.generated_by'),
            ),
            'Radiology'              => array('label' => 'Radiology', 'table' => 'radiology_report', 'search_table' => 'radiology_report',
                'search_column'                           => 'reporting_date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,radiology_report.*,radiology_report.apply_charge as amount,radiology_report.reporting_date as date, radiology_report.id as reff,radio.id, radio.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name',
                'join'                                    => array(
                    'JOIN radio ON radiology_report.radiology_id = radio.id',
                    'JOIN staff ON radiology_report.consultant_doctor = staff.id',
                    'JOIN charges ON charges.id = radio.charge_id', 'JOIN patients ON radiology_report.patient_id=patients.id',
                    'LEFT JOIN staff as kpo ON kpo.id = radiology_report.generated_by',
                ),

            ),
            'Operation_Theatre'      => array('label' => 'Operation Theatre', 'table' => 'operation_theatre', 'search_table' => 'operation_theatre',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,operation_theatre.*,operation_theatre.id as reff,patients.id as pid,patients.patient_unique_id,patients.patient_name,charges.id as cid,charges.charge_category,charges.code,charges.description,charges.standard_charge, operation_theatre.apply_charge as amount',
                'join'                                    => array(
                    'JOIN patients ON operation_theatre.patient_id=patients.id',
                    'JOIN staff ON staff.id = operation_theatre.consultant_doctor',
                    'JOIN charges ON operation_theatre.charge_id = charges.id',
                    'LEFT JOIN staff as kpo ON kpo.id = operation_theatre.generated_by',
                ),

            ),

            'Blood_Bank'             => array('label' => 'Blood Bank', 'table'        => 'blood_issue',
                'search_column'                           => 'created_at', 'search_table' => 'blood_issue',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,blood_issue.*,blood_issue.id as reff,blood_issue.created_at as date,patients.patient_name',
                'join'                                    => array('JOIN patients ON blood_issue.recieve_to=patients.id','LEFT JOIN staff as kpo ON kpo.id = blood_issue.generated_by'),

            ),

            'ambulance'              => array('label' => 'Ambulance', 'table' => 'ambulance_call', 'search_table' => 'ambulance_call',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,ambulance_call.*,ambulance_call.id as reff,patients.patient_name',
                'join'                                    => array('JOIN patients ON ambulance_call.patient_name=patients.id','LEFT JOIN staff as kpo ON kpo.id = ambulance_call.generated_by'),

            ),
            'income'                 => array('label' => 'General Income', 'table' => 'income', 'search_table' => 'income',
                'search_column'                           => 'date',
                'select'                                  => 'income.*,income.name as patient_name,income.invoice_no as reff',
                'join'                                    => array('JOIN income_head ON income.inc_head_id = income_head.id')),

                'expense'                => array('label' => 'Expenses', 'table' => 'expenses', 'search_table' => 'expenses',
                'search_column'                           => 'date',
                'select'                                  => 'expenses.*,expenses.name as patient_name,expenses.invoice_no as reff',
                'join'                                    => array('JOIN expense_head ON expenses.exp_head_id = expense_head.id')),
            'payroll'                => array('label' => 'Payroll', 'table' => 'staff_payslip', 'search_table' => 'staff_payslip',
                'search_column'                           => 'payment_date',
                'select'                                  => 'staff_payslip.*,staff.name as patient_name,staff.surname,staff.employee_id as patient_unique_id,staff_payslip.payment_date as date,staff_payslip.net_salary as amount,staff_payslip.id as reff',
                'join'                                    => array('JOIN staff ON staff_payslip.staff_id = staff.id')),
        );

        $i                 = 0;
        $data["parameter"] = $parameter;
        foreach ($parameter as $key => $value) {
            # code...

            $select     = $parameter[$key]['select'];
            $join       = $parameter[$key]['join'];
            $table_name = $parameter[$key]['table'];


            if (empty($search_type)) {

                $search_type = "";
                $resultList  = $this->report_model->getReport($select, $join, $table_name);
            } else {

                $search_table     = $parameter[$key]['search_table'];
                $search_column    = $parameter[$key]['search_column'];
                $result_List       = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column);
                $resultList       = isset($result_List['main_data']) && !empty($result_List['main_data']) ? $result_List['main_data'] : '';
            }

            $rd[$parameter[$key]['label']]         = $resultList;
            $data['parameter'][$key]['resultList'] = $resultList;
            if(!empty($resultList)){
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($resultList, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($resultList, 'kpo_name'));
            }

            $i++;
        }

        $resultList2 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,kpo.name as kpo_name,ipd_details.ipd_no,ipd_billing.date,ipd_billing.net_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id',
        $join = array('JOIN staff ON ipd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON ipd_details.patient_id = patients.id',
            //'LEFT  JOIN payment ON payment.ipd_id = ipd_details.id',
            'LEFT JOIN ipd_billing ON ipd_billing.ipd_id = ipd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = ipd_billing.generated_by',
        ), $table_name = 'ipd_details', $search_type, $search_table = 'ipd_billing', $search_column = 'date');

        if (!empty($resultList2['main_data'])) {
            foreach ($resultList2['main_data'] as $key => $value) {
                array_push($rd["IPD"], $value);
                array_push($data['parameter']["IPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }
        $resultList3 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_billing.date,opd_billing.net_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            //'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_billing', $search_column = 'date');

        if (!empty($resultList3['main_data'])) {
            foreach ($resultList3['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }
        }

        $resultList4 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_payment.date,opd_payment.paid_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            //'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_payment', $search_column = 'date');

        if (!empty($resultList4['main_data'])) {
            foreach ($resultList4['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }

        $data["resultlist"]  = $rd;
        $kpoData=array();
        foreach ($data["resultlist"] as $key1 => $result2) {

            foreach ($result2 as $key4 => $result) {
                $kpoData[$result['kpo_id']]=$result['kpo_name'];
            }
        }
        $kpo_filterData=array_unique($kpoData);
        return $kpo_filterData;
    }
    public function incomeSummary($value = '')
    {
        if (!$this->rbac->hasPrivilege('income_summary', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/income/incomeSummary');
        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        $data['kpo_type']=$kpo_type = $this->input->post("kpoid");
        $data['head_select']= $this->input->post("head_select");
        if (!empty($kpo_type)) {
            $additional_whereopd = array("opd_details.generated_by" => $kpo_type);
            $additional_whereipd = array("ipd_details.generated_by" => $kpo_type);
            $additional_wherepharma = array("pharmacy_bill_basic.generated_by" => $kpo_type);
            $additional_wherepath = array("pathology_report.generated_by" => $kpo_type);
            $additional_whereradio = array("radiology_report.generated_by" => $kpo_type);
            $additional_whereot = array("operation_theatre.generated_by" => $kpo_type);
            $additional_whereblood_issue = array("blood_issue.generated_by" => $kpo_type);
            $additional_whereamb = array("ambulance_call.generated_by" => $kpo_type);

        } else {
            //echo "whyyy";exit;
            $additional_whereopd = array('1 = 1');
        }
        $parameter = array(
            'OPD' => array('label' => 'OPD', 'table' => 'opd_details', 'search_table' => 'opd_details',
            'search_column' => 'appointment_date', 'select' => 'opd_details.*,kpo.id as kpo_id,kpo.name as kpo_name,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,staff.name, staff.surname',
            'join' => array('LEFT JOIN staff ON opd_details.cons_doctor = staff.id',
                'INNER JOIN patients ON opd_details.patient_id = patients.id ',
                'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
            ),
            'where_in'=>$additional_whereopd
            ),
            'IPD' => array('label' => 'IPD', 'table' => 'ipd_details', 'search_table' => 'payment',
                'search_column' => 'date',
                'select'=> 'kpo.id as kpo_id,kpo.name as kpo_name,ipd_details.ipd_no,payment.date,payment.paid_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id',
                'join'=> array(
                    'JOIN staff ON ipd_details.cons_doctor = staff.id',
                    'JOIN patients ON ipd_details.patient_id = patients.id',
                    'JOIN payment ON payment.ipd_id = ipd_details.id',
                    'LEFT JOIN staff as kpo ON kpo.id = ipd_details.generated_by',
                ),
                'where_in'=>$additional_whereipd,
            ),
            'Pharmacy'=> array('label' => 'Pharmacy', 'table' => 'pharmacy_bill_basic', 'search_table' => 'pharmacy_bill_basic',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,pharmacy_bill_basic.*,patients.patient_name as patient_name,pharmacy_bill_basic.bill_no as reff,pharmacy_bill_basic.net_amount as amount',
                'join'                                    => array('JOIN patients ON patients.id = pharmacy_bill_basic.patient_id', 'LEFT JOIN staff as kpo ON kpo.id = pharmacy_bill_basic.generated_by'),
                'where_in'=>$additional_wherepharma,
            ),
            'Pathology'              => array('label' => 'Pathology', 'table' => 'pathology_report', 'search_table' => 'pathology_report',
                'search_column'                           => 'reporting_date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,pathology_report.*, pathology_report.apply_charge as amount,pathology_report.id as reff,pathology_report.reporting_date as date,pathology.id, pathology.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name',
                'join'                                    => array(
                    'JOIN pathology ON pathology_report.pathology_id = pathology.id',
                    'LEFT JOIN staff ON pathology_report.consultant_doctor = staff.id',
                    'JOIN charges ON charges.id = pathology.charge_id', 'JOIN patients ON pathology_report.patient_id=patients.id','LEFT JOIN staff as kpo ON kpo.id = pathology_report.generated_by'),
                    'where_in'=>$additional_wherepath,
            ),
            'Radiology'              => array('label' => 'Radiology', 'table' => 'radiology_report', 'search_table' => 'radiology_report',
                'search_column'                           => 'reporting_date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,radiology_report.*,radiology_report.apply_charge as amount,radiology_report.reporting_date as date, radiology_report.id as reff,radio.id, radio.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name',
                'join'                                    => array(
                    'JOIN radio ON radiology_report.radiology_id = radio.id',
                    'JOIN staff ON radiology_report.consultant_doctor = staff.id',
                    'JOIN charges ON charges.id = radio.charge_id', 'JOIN patients ON radiology_report.patient_id=patients.id',
                    'LEFT JOIN staff as kpo ON kpo.id = radiology_report.generated_by',
                ),
                'where_in'=>$additional_whereradio,

            ),
            'Operation_Theatre'      => array('label' => 'Operation Theatre', 'table' => 'operation_theatre', 'search_table' => 'operation_theatre',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,operation_theatre.*,operation_theatre.id as reff,patients.id as pid,patients.patient_unique_id,patients.patient_name,charges.id as cid,charges.charge_category,charges.code,charges.description,charges.standard_charge, operation_theatre.apply_charge as amount',
                'join'                                    => array(
                    'JOIN patients ON operation_theatre.patient_id=patients.id',
                    'JOIN staff ON staff.id = operation_theatre.consultant_doctor',
                    'JOIN charges ON operation_theatre.charge_id = charges.id',
                    'LEFT JOIN staff as kpo ON kpo.id = operation_theatre.generated_by',
                ),
                'where_in'=>$additional_whereot,
            ),

            'Blood_Bank'             => array('label' => 'Blood Bank', 'table'        => 'blood_issue',
                'search_column'                           => 'created_at', 'search_table' => 'blood_issue',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,blood_issue.*,blood_issue.id as reff,blood_issue.created_at as date,patients.patient_name',
                'join'                                    => array('JOIN patients ON blood_issue.recieve_to=patients.id','LEFT JOIN staff as kpo ON kpo.id = blood_issue.generated_by'),
                'where_in'=>$additional_whereblood_issue,
            ),

            'ambulance'              => array('label' => 'Ambulance', 'table' => 'ambulance_call', 'search_table' => 'ambulance_call',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,ambulance_call.*,ambulance_call.id as reff,patients.patient_name',
                'join'                                    => array('JOIN patients ON ambulance_call.patient_name=patients.id','LEFT JOIN staff as kpo ON kpo.id = ambulance_call.generated_by'),
                'where_in'=>$additional_whereamb,
            ),
            'income'                 => array('label' => 'General Income', 'table' => 'income', 'search_table' => 'income',
                'search_column'                           => 'date',
                'select'                                  => 'income.*,income.name as patient_name,income.invoice_no as reff',
                'join'                                    => array('JOIN income_head ON income.inc_head_id = income_head.id')),

                'expense'                => array('label' => 'Expenses', 'table' => 'expenses', 'search_table' => 'expenses',
                'search_column'                           => 'date',
                'select'                                  => 'expenses.*,expenses.name as patient_name,expenses.invoice_no as reff',
                'join'                                    => array('JOIN expense_head ON expenses.exp_head_id = expense_head.id')),
            'payroll'                => array('label' => 'Payroll', 'table' => 'staff_payslip', 'search_table' => 'staff_payslip',
                'search_column'                           => 'payment_date',
                'select'                                  => 'staff_payslip.*,staff.name as patient_name,staff.surname,staff.employee_id as patient_unique_id,staff_payslip.payment_date as date,staff_payslip.net_salary as amount,staff_payslip.id as reff',
                'join'                                    => array('JOIN staff ON staff_payslip.staff_id = staff.id')),
        );
        $kpo_test=$this->input->post("kpoid");
        $kpo_test=isset($kpo_test) && !empty($kpo_test) ? 'kpo_av' : '';

        $i                 = 0;
        $data["parameter"] = $parameter;
        foreach ($parameter as $key => $value) {
            # code...
           //echo "fffi".$i;
            $select     = $parameter[$key]['select'];
            $join       = $parameter[$key]['join'];
            $table_name = $parameter[$key]['table'];
            //echo $table_name;exit;
            $where = isset($parameter[$key]['where_in']) ? $parameter[$key]['where_in'] : [];
            if (empty($search_type)) {
               // echo "whyyyy";exit;
                $search_type = "";
                $resultList  = $this->report_model->getReport($select, $join, $table_name);
            } else {

                $search_table     = $parameter[$key]['search_table'];
                $search_column    = $parameter[$key]['search_column'];
                $result_List       = $this->report_model->searchReportKPO($select, $join, $table_name, $search_type, $search_table, $search_column,$where,$whereeee = array(),$where_innnnnn=array(),$kpo_test);
                $resultList       = isset($result_List['main_data']) && !empty($result_List['main_data']) ? $result_List['main_data'] : '';
                //echo "<pre>";print_r($result_List);exit;
            }

            $rd[$parameter[$key]['label']]         = $resultList;
            $data['parameter'][$key]['resultList'] = $resultList;
            if(!empty($resultList)){
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($resultList, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($resultList, 'kpo_name'));
            }

            $i++;
        }
        //echo "<pre>";print_r($data);exit;
        if (!empty($kpo_type)) {
            $additional_where2['where_in'] = array('ipd_billing.generated_by' => $kpo_type);
        } else {
            $additional_where2 = array('1 = 1');
        }
        $resultList2 = $this->report_model->searchReportKPO($select = 'kpo.id as kpo_id,kpo.name as kpo_name,ipd_details.ipd_no,ipd_billing.date,ipd_billing.net_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id',
        $join = array('JOIN staff ON ipd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON ipd_details.patient_id = patients.id',
            //'LEFT  JOIN payment ON payment.ipd_id = ipd_details.id',
            'LEFT JOIN ipd_billing ON ipd_billing.ipd_id = ipd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = ipd_billing.generated_by',
        ), $table_name = 'ipd_details', $search_type, $search_table = 'ipd_billing', $search_column = 'date',$additional_where2,$whereeee = array(),$where_innnnnn=array(),$kpo_test);

        if (!empty($resultList2['main_data'])) {
            foreach ($resultList2['main_data'] as $key => $value) {
                array_push($rd["IPD"], $value);
                array_push($data['parameter']["IPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }

        if (!empty($kpo_type)) {
            $additional_where3['where_in'] = array('opd_details.generated_by' => $kpo_type);
        } else {
            $additional_where3 = array('1 = 1');
        }

        $resultList3 = $this->report_model->searchReportKPO($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_billing.date,opd_billing.net_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            //'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_billing', $search_column = 'date',$additional_where3,$whereeee = array(),$where_innnnnn=array(),$kpo_test);

        if (!empty($resultList3['main_data'])) {
            foreach ($resultList3['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }
        }

        if (!empty($kpo_type)) {
            $additional_where4['where_in'] = array('opd_details.generated_by' => $kpo_type);
        } else {
            $additional_where4 = array('1 = 1');
        }

        $resultList4 = $this->report_model->searchReportKPO($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_payment.date,opd_payment.paid_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            //'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_payment', $search_column = 'date',$additional_where4,$whereeee = array(),$where_innnnnn=array(),$kpo_test);

        if (!empty($resultList4['main_data'])) {
            foreach ($resultList4['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }

        //echo "<pre>";print_r($resultList4);exit;
        $data["resultlist"]  = $rd;
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data['fillter_data']=$resultList4['fillter_data'];
        $data['kpo_filterData']=$this->getAllKposFillter();
        //$data['kpo_filterData']=$this->getAllKposFillter();

        if($this->input->post('search')=='search_excel'){
        $this->headExcelReport($data);
        }else{
            $this->load->view('layout/header', $data);
            //echo "<pre>";print_r($data);exit;
            $this->load->view('admin/income/incomeSummary', $data);
            $this->load->view('layout/footer', $data);
        }


    }
    public function filltertransaction($value = '')
    {
        if (!$this->rbac->hasPrivilege('transaction_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/income/filltertransaction');
        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        $data['kpo_type']=$kpo_type = $this->input->post("kpoid");
        $data['head_select']= $this->input->post("head_select");
        $kpo_type = array_filter($kpo_type, function ($value) {
            return !empty($value);
        });
        $kpo_type = array_values($kpo_type);
        //echo "<pre>";print_r($this->input->post("kpoid"));exit;
        if (!empty($kpo_type)) {
            $additional_whereopd = array("opd_details.generated_by" => $kpo_type);
            $additional_whereipd = array("ipd_details.generated_by" => $kpo_type);
            $additional_wherepharma = array("pharmacy_bill_basic.generated_by" => $kpo_type);
            $additional_wherepath = array("pathology_report.generated_by" => $kpo_type);
            $additional_whereradio = array("radiology_report.generated_by" => $kpo_type);
            $additional_whereot = array("operation_theatre.generated_by" => $kpo_type);
            $additional_whereblood_issue = array("blood_issue.generated_by" => $kpo_type);
            $additional_whereamb = array("ambulance_call.generated_by" => $kpo_type);

        } else {
            //echo "whyyy";exit;
            $additional_whereopd = array('1 = 1');
        }
        $parameter = array(
            'OPD' => array('label' => 'OPD', 'table' => 'opd_details', 'search_table' => 'opd_details',
            'search_column' => 'appointment_date', 'select' => 'opd_details.*,kpo.id as kpo_id,kpo.name as kpo_name,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,staff.name, staff.surname',
            'join' => array('LEFT JOIN staff ON opd_details.cons_doctor = staff.id',
                'INNER JOIN patients ON opd_details.patient_id = patients.id',
                'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
            ),
            'where_in'=>$additional_whereopd
            ),
            'IPD' => array('label' => 'IPD', 'table' => 'ipd_details', 'search_table' => 'payment',
                'search_column' => 'date',
                'select'=> 'kpo.id as kpo_id,kpo.name as kpo_name,ipd_details.ipd_no,payment.date,payment.paid_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id',
                'join'=> array(
                    'JOIN staff ON ipd_details.cons_doctor = staff.id',
                    'JOIN patients ON ipd_details.patient_id = patients.id',
                    'JOIN payment ON payment.ipd_id = ipd_details.id',
                    'LEFT JOIN staff as kpo ON kpo.id = ipd_details.generated_by',
                ),
                'where_in'=>$additional_whereipd,
            ),
            'Pharmacy'=> array('label' => 'Pharmacy', 'table' => 'pharmacy_bill_basic', 'search_table' => 'pharmacy_bill_basic',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,pharmacy_bill_basic.*,patients.patient_name as patient_name,pharmacy_bill_basic.bill_no as reff,pharmacy_bill_basic.net_amount as amount',
                'join'                                    => array('JOIN patients ON patients.id = pharmacy_bill_basic.patient_id', 'LEFT JOIN staff as kpo ON kpo.id = pharmacy_bill_basic.generated_by'),
                'where_in'=>$additional_wherepharma,
            ),
            'Pathology'              => array('label' => 'Pathology', 'table' => 'pathology_report', 'search_table' => 'pathology_report',
                'search_column'                           => 'reporting_date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,pathology_report.*, pathology_report.apply_charge as amount,pathology_report.id as reff,pathology_report.reporting_date as date,pathology.id, pathology.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name',
                'join'                                    => array(
                    'JOIN pathology ON pathology_report.pathology_id = pathology.id',
                    'LEFT JOIN staff ON pathology_report.consultant_doctor = staff.id',
                    'JOIN charges ON charges.id = pathology.charge_id', 'JOIN patients ON pathology_report.patient_id=patients.id','LEFT JOIN staff as kpo ON kpo.id = pathology_report.generated_by'),
                    'where_in'=>$additional_wherepath,
            ),
            'Radiology'              => array('label' => 'Radiology', 'table' => 'radiology_report', 'search_table' => 'radiology_report',
                'search_column'                           => 'reporting_date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,radiology_report.*,radiology_report.apply_charge as amount,radiology_report.reporting_date as date, radiology_report.id as reff,radio.id, radio.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name',
                'join'                                    => array(
                    'JOIN radio ON radiology_report.radiology_id = radio.id',
                    'JOIN staff ON radiology_report.consultant_doctor = staff.id',
                    'JOIN charges ON charges.id = radio.charge_id', 'JOIN patients ON radiology_report.patient_id=patients.id',
                    'LEFT JOIN staff as kpo ON kpo.id = radiology_report.generated_by',
                ),
                'where_in'=>$additional_whereradio,

            ),
            'Operation_Theatre'      => array('label' => 'Operation Theatre', 'table' => 'operation_theatre', 'search_table' => 'operation_theatre',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,operation_theatre.*,operation_theatre.id as reff,patients.id as pid,patients.patient_unique_id,patients.patient_name,charges.id as cid,charges.charge_category,charges.code,charges.description,charges.standard_charge, operation_theatre.apply_charge as amount',
                'join'                                    => array(
                    'JOIN patients ON operation_theatre.patient_id=patients.id',
                    'JOIN staff ON staff.id = operation_theatre.consultant_doctor',
                    'JOIN charges ON operation_theatre.charge_id = charges.id',
                    'LEFT JOIN staff as kpo ON kpo.id = operation_theatre.generated_by',
                ),
                'where_in'=>$additional_whereot,
            ),

            'Blood_Bank'             => array('label' => 'Blood Bank', 'table'        => 'blood_issue',
                'search_column'                           => 'created_at', 'search_table' => 'blood_issue',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,blood_issue.*,blood_issue.id as reff,blood_issue.created_at as date,patients.patient_name',
                'join'                                    => array('JOIN patients ON blood_issue.recieve_to=patients.id','LEFT JOIN staff as kpo ON kpo.id = blood_issue.generated_by'),
                'where_in'=>$additional_whereblood_issue,
            ),

            'ambulance'              => array('label' => 'Ambulance', 'table' => 'ambulance_call', 'search_table' => 'ambulance_call',
                'search_column'                           => 'date',
                'select'                                  => 'kpo.id as kpo_id,kpo.name as kpo_name,ambulance_call.*,ambulance_call.id as reff,patients.patient_name',
                'join'                                    => array('JOIN patients ON ambulance_call.patient_name=patients.id','LEFT JOIN staff as kpo ON kpo.id = ambulance_call.generated_by'),
                'where_in'=>$additional_whereamb,
            ),
            'income'                 => array('label' => 'General Income', 'table' => 'income', 'search_table' => 'income',
                'search_column'                           => 'date',
                'select'                                  => 'income.*,income.name as patient_name,income.invoice_no as reff',
                'join'                                    => array('JOIN income_head ON income.inc_head_id = income_head.id')),

                'expense'                => array('label' => 'Expenses', 'table' => 'expenses', 'search_table' => 'expenses',
                'search_column'                           => 'date',
                'select'                                  => 'expenses.*,expenses.name as patient_name,expenses.invoice_no as reff',
                'join'                                    => array('JOIN expense_head ON expenses.exp_head_id = expense_head.id')),
            'payroll'                => array('label' => 'Payroll', 'table' => 'staff_payslip', 'search_table' => 'staff_payslip',
                'search_column'                           => 'payment_date',
                'select'                                  => 'staff_payslip.*,staff.name as patient_name,staff.surname,staff.employee_id as patient_unique_id,staff_payslip.payment_date as date,staff_payslip.net_salary as amount,staff_payslip.id as reff',
                'join'                                    => array('JOIN staff ON staff_payslip.staff_id = staff.id')),
        );
        $kpo_test=$this->input->post("kpoid");
        $kpo_test=isset($kpo_test) && !empty($kpo_test) ? 'kpo_av' : '';

        $i                 = 0;
        $data["parameter"] = $parameter;
        foreach ($parameter as $key => $value) {
            # code...
           //echo "fffi".$i;
            $select     = $parameter[$key]['select'];
            $join       = $parameter[$key]['join'];
            $table_name = $parameter[$key]['table'];
            //echo $table_name;exit;
            $where = isset($parameter[$key]['where_in']) ? $parameter[$key]['where_in'] : [];
            if (empty($search_type)) {
               // echo "whyyyy";exit;
                $search_type = "";
                $resultList  = $this->report_model->getReport($select, $join, $table_name);
            } else {

                $search_table     = $parameter[$key]['search_table'];
                $search_column    = $parameter[$key]['search_column'];
                $result_List       = $this->report_model->searchReportKPO($select, $join, $table_name, $search_type, $search_table, $search_column,$where,$whereeee = array(),$where_innnnnn=array(),$kpo_test);
                $resultList       = isset($result_List['main_data']) && !empty($result_List['main_data']) ? $result_List['main_data'] : '';
                //echo "<pre>";print_r($result_List);exit;
            }

            $rd[$parameter[$key]['label']]         = $resultList;
            $data['parameter'][$key]['resultList'] = $resultList;
            if(!empty($resultList)){
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($resultList, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($resultList, 'kpo_name'));
            }

            $i++;
        }
        //echo "<pre>";print_r($data);exit;
        if (!empty($kpo_type)) {
            $additional_where2['where_in'] = array('ipd_billing.generated_by' => $kpo_type);
        } else {
            $additional_where2 = array('1 = 1');
        }
        $resultList2 = $this->report_model->searchReportKPO($select = 'kpo.id as kpo_id,kpo.name as kpo_name,ipd_details.ipd_no,ipd_billing.date,ipd_billing.net_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id',
        $join = array('JOIN staff ON ipd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON ipd_details.patient_id = patients.id',
            //'LEFT  JOIN payment ON payment.ipd_id = ipd_details.id',
            'LEFT JOIN ipd_billing ON ipd_billing.ipd_id = ipd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = ipd_billing.generated_by',
        ), $table_name = 'ipd_details', $search_type, $search_table = 'ipd_billing', $search_column = 'date',$additional_where2,$whereeee = array(),$where_innnnnn=array(),$kpo_test);

        if (!empty($resultList2['main_data'])) {
            foreach ($resultList2['main_data'] as $key => $value) {
                array_push($rd["IPD"], $value);
                array_push($data['parameter']["IPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }

        if (!empty($kpo_type)) {
            $additional_where3['where_in'] = array('opd_details.generated_by' => $kpo_type);
        } else {
            $additional_where3 = array('1 = 1');
        }

        $resultList3 = $this->report_model->searchReportKPO($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_billing.date,opd_billing.net_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            //'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_billing', $search_column = 'date',$additional_where3,$whereeee = array(),$where_innnnnn=array(),$kpo_test);

        if (!empty($resultList3['main_data'])) {
            foreach ($resultList3['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }
        }

        if (!empty($kpo_type)) {
            $additional_where4['where_in'] = array('opd_details.generated_by' => $kpo_type);
        } else {
            $additional_where4 = array('1 = 1');
        }

        $resultList4 = $this->report_model->searchReportKPO($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_payment.date,opd_payment.paid_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            //'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_payment', $search_column = 'date',$additional_where4,$whereeee = array(),$where_innnnnn=array(),$kpo_test);

        if (!empty($resultList4['main_data'])) {
            foreach ($resultList4['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }

        //echo "<pre>";print_r($resultList4);exit;
        $data["resultlist"]  = $rd;
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data['fillter_data']=$resultList4['fillter_data'];
        $data['kpo_filterData']=$this->getAllKposFillter();
        //$data['kpo_filterData']=$this->getAllKposFillter();

        if($this->input->post('search')=='search_excel'){
        $this->kpoExcelReport($data);
        }else{
            $this->load->view('layout/header', $data);
            //echo "<pre>";print_r($data);exit;
            $this->load->view('admin/income/transactionFillterReport', $data);
            $this->load->view('layout/footer', $data);
        }


    }


    // public function exportReportInExcel($data){
    //     if($this->input->post('head_select')==''){
    //         $this->kpoExcelReport($data);
    //     }
    //     else{
    //         $this->headExcelReport($data);
    //     }

    // }

    public function kpoExcelReport($data)
    {
        require_once APPPATH . 'third_party/PHPExcel.php';
        $this->load->library('phpexcel');
        // Create a new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Add data to the Excel sheet
        $objPHPExcel->setActiveSheetIndex(0);


        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFont()->setBold(true); // Make the text bold
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'D3D3D3'), // Gray background color
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Adjust the width as needed
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
        // Set font and font size for cell A2
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
        // Set top and bottom padding for the cell
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        // Write the header text
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Al Nafees Medical College & Hospital');

        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFont()->setBold(true); // Make the text bold
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'D3D3D3'), // Gray background color
        ));

        $objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
        // Set font and font size for cell A2
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
        // Set top and bottom padding for the cell
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'KPO Cash Collection Summary');


        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getFont()->setBold(true); // Make the text bold
        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'D3D3D3'), // Gray background color
        ));

        $objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
        // Set font and font size for cell A2
        $objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14);

        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(30);
        // Set top and bottom padding for the cell
        $objPHPExcel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        // Write the header text
        if(empty($data['fillter_data']['to_date']) && empty($data['fillter_data']['from_date'])) {$setDate="All Record";}
        elseif($data['search_type']=='all_time') {$setDate="All Record";}
        elseif(!empty($data['fillter_data']['to_date'])) {
            $setDate='From : '.date('d-M-Y',strtotime($data['fillter_data']['from_date'])) .' To : '.date('d-M-Y',strtotime($data['fillter_data']['to_date']));
        }else{
            $setDate='From : '.date('d-M-Y',strtotime($data['fillter_data']['from_date']));
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A4', $setDate);
        $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

        for ($row = 7; $row <= 8; $row++) {
            for ($col = 'A'; $col <= 'D'; $col++) {
                $objPHPExcel->getActiveSheet()->getStyle($col.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
            }
        }

        // Set cell styles for the header (A7:D7)
        $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getFont()->setBold(true); // Make the text bold

       // $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(20); // Adjust the height as needed
        $objPHPExcel->getActiveSheet()->getStyle('A7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setSize(12);

        $objPHPExcel->getActiveSheet()->setCellValue('A7', 'Sr.No');
        $objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('B7', 'KPO');
        $objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('C7', 'Patients');
        $objPHPExcel->getActiveSheet()->getStyle('D7')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('D7')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('D7', 'Amount');

        $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
        //$objPHPExcel->getActiveSheet()->getStyle('C8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C8')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('C8')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('C8', 'No.');

        $objPHPExcel->getActiveSheet()->getStyle('D8')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('D8')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('D8', 'Pak Rupees');
        $objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $testArray=array();
        foreach($this->input->post('kpoid') as $kpoids){
            foreach($data['resultlist'] as $km=>$kmData){
                    $Sr=1;
                    foreach($kmData as $k=>$v){
                        $amount_patient=0;
                            if($v['kpo_id']==$kpoids){
                                if(array_key_exists($v['kpo_name'], $testArray)){
                                $testArray[$v['kpo_name']] += floor($v['amount']);
                                $testArray[$v['kpo_id']] += count($v['kpo_id']);
                            }else{
                                $testArray[$v['kpo_name']] = floor($v['amount']);
                                $testArray[$v['kpo_id']] = count($v['kpo_id']);
                            }


                        }
                    }
            }
        }
        $get_Array=$testArray;
        //echo "<pre>";print_r($get_Array);exit;
        $newArray = array();

                $index = 0;
                foreach ($get_Array as $key => $value) {
                    if ($index % 2 == 0) {
                        $newArray[] = array(
                            'Sr' => $index / 2 + 1,
                            'kpo_name' => $key,
                            'total_Amount' => $value
                        );

                        for ($col = 'A'; $col <= 'D'; $col++) {
                            $objPHPExcel->getActiveSheet()->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
                        }
                        $row++;

                    } else {
                        $newArray[$index / 2]['kpo_id'] = $key;
                        $newArray[$index / 2]['total_patient'] = $value;
                    }
                    $index++;

                    // Set vertical and horizontal dotted borders for each cell in the range A9 to D9

                }



        $roww = 9;
        $column_num=0;
        $total_amount=0;
        $nopatient=0;
        //echo "<pre>";print_r($newArray);exit;
        // foreach ($newArray as $row_Data) {
        //     $column = 'A';
        //     foreach ($row_Data as $keyee=>$value) {
        //         $value=isset($value) && !empty($value) ? $value : '0';
        //         if($keyee=='Sr'){
        //             $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //             $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setName('Cambria');
        //             $objPHPExcel->getActiveSheet()->getStyle($column . $row)->getFont()->setSize(11);
        //             $objPHPExcel->getActiveSheet()->setCellValue($column . $roww, $value);
        //         }
        //         if($keyee=='kpo_name'){
        //             $objPHPExcel->getActiveSheet()->getStyle('B'. $row)->getFont()->setName('Cambria');
        //             $objPHPExcel->getActiveSheet()->getStyle('B'. $row)->getFont()->setSize(11);
        //             $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $value);

        //         }
        //         if($keyee=='total_patient'){
        //             $objPHPExcel->getActiveSheet()->getStyle('C'. $row)->getFont()->setName('Cambria');
        //             $objPHPExcel->getActiveSheet()->getStyle('C'. $row)->getFont()->setSize(11);
        //             $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $value);
        //         }
        //         if($keyee=='total_Amount'){
        //             $total_amount+=$value;
        //             $objPHPExcel->getActiveSheet()->getStyle('D'. $row)->getFont()->setName('Cambria');
        //             $objPHPExcel->getActiveSheet()->getStyle('D'. $row)->getFont()->setSize(11);
        //             $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $value);
        //         }
        //         //$column_num++;
        //     }
        //     $row++;
        // }
        foreach ($newArray as $row_Data) {
            $column = 'A';
            foreach ($row_Data as $keyee=>$value) {
                $value=isset($value) && !empty($value) ? $value : '0';
                if($keyee=='Sr'){
                    $objPHPExcel->getActiveSheet()->getStyle($column . $roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle($column . $roww)->getFont()->setName('Cambria');
                    $objPHPExcel->getActiveSheet()->getStyle($column . $roww)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->setCellValue($column . $roww, $value);
                }
                if($keyee=='kpo_name'){
                    $objPHPExcel->getActiveSheet()->getStyle('B'. $roww)->getFont()->setName('Cambria');
                    $objPHPExcel->getActiveSheet()->getStyle('B'. $roww)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$roww, $value);
                }
                if($keyee=='total_patient'){
                    $nopatient+=$value;
                    $objPHPExcel->getActiveSheet()->getStyle('C'. $roww)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('C'. $roww)->getFont()->setName('Cambria');
                    $objPHPExcel->getActiveSheet()->getStyle('C'. $roww)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$roww, $value);
                }
                if($keyee=='total_Amount'){
                    $total_amount+=$value;
                    $objPHPExcel->getActiveSheet()->getStyle('D'. $roww)->getFont()->setName('Cambria');
                    $objPHPExcel->getActiveSheet()->getStyle('D'. $roww)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$roww, number_format($value));
                    //$objPHPExcel->getActiveSheet()->setCellValue('D'.$roww, $value);
                }
            }
            $roww++;
        }

            // $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, 'Total');
            // $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $total_amount);

            $rowDown=$roww-1;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowDown.':D'.$rowDown)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$roww)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('B'. $roww)->getFont()->setName('Cambria');
            $objPHPExcel->getActiveSheet()->getStyle('B'. $roww)->getFont()->setSize(11);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$roww, 'Total');
            $objPHPExcel->getActiveSheet()->getStyle('C'.$roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$roww, number_format($nopatient));
            $objPHPExcel->getActiveSheet()->getStyle('D'.$roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$roww, number_format($total_amount));

            $filename = 'KPO Cash Collection Summary.xls';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Output the Excel file to the browser
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
    }

    public function headExcelReport($data){
        require_once APPPATH . 'third_party/PHPExcel.php';
        $this->load->library('phpexcel');
        // Create a new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Add data to the Excel sheet
        $objPHPExcel->setActiveSheetIndex(0);


        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFont()->setBold(true); // Make the text bold
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'D3D3D3'), // Gray background color
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Adjust the width as needed
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
        // Set font and font size for cell A2
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
        // Set top and bottom padding for the cell
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        // Write the header text
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Al Nafees Medical College & Hospital');

        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFont()->setBold(true); // Make the text bold
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'D3D3D3'), // Gray background color
        ));

        $objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
        // Set font and font size for cell A2
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
        // Set top and bottom padding for the cell
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Income Summary');


        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getFont()->setBold(true); // Make the text bold
        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => 'D3D3D3'), // Gray background color
        ));

        $objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
        // Set font and font size for cell A2
        $objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setSize(14);

        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(30);
        // Set top and bottom padding for the cell
        $objPHPExcel->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        // Write the header text
        if(empty($data['fillter_data']['to_date']) && empty($data['fillter_data']['from_date'])) {$setDate="All Record";}
        elseif($data['search_type']=='all_time') {$setDate="All Record";}
        elseif(!empty($data['fillter_data']['to_date'])) {
            $setDate='From : '.date('d-M-Y',strtotime($data['fillter_data']['from_date'])) .' To : '.date('d-M-Y',strtotime($data['fillter_data']['to_date']));
        }else{
            $setDate='From : '.date('d-M-Y',strtotime($data['fillter_data']['from_date']));
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A4', $setDate);
        $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

        for ($row = 7; $row <= 8; $row++) {
            for ($col = 'A'; $col <= 'D'; $col++) {
                $objPHPExcel->getActiveSheet()->getStyle($col.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
            }
        }

        // Set cell styles for the header (A7:D7)
        $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getFont()->setBold(true); // Make the text bold

       // $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(20); // Adjust the height as needed
        $objPHPExcel->getActiveSheet()->getStyle('A7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setSize(12);

        $objPHPExcel->getActiveSheet()->setCellValue('A7', 'Sr.No');
        $objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('B7', 'Particulars');
        $objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('C7')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('C7', 'No.of Patients');
        $objPHPExcel->getActiveSheet()->getStyle('D7')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('D7')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('D7', 'Amount');

        $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle('D8')->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('D8')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->setCellValue('D8', 'Pak Rupees');
        $objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $prepareArray=array();
        $Sr = 1;

        foreach ($this->input->post('head_select') as $kmData) {
            $total_count = count($data['resultlist'][$kmData]);
            $prepareArray[] = array(
                'Sr' => $Sr,
                'particulars' => $kmData,
                'no_patients' => isset($total_count) && $total_count > 1 ? $total_count : '0',
                'amount' => array_sum(array_column($data['resultlist'][$kmData], 'amount')),
            );

            // Set vertical and horizontal dotted borders for each cell in the range A9 to D9
            for ($col = 'A'; $col <= 'D'; $col++) {
                $objPHPExcel->getActiveSheet()->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
            }

            $row++;
            $Sr++;
        }
        $roww = 9;
        $column_num=0;
        $total_amount=0;
        foreach ($prepareArray as $row_Data) {
            $column = 'A';
            $r=1;
            foreach ($row_Data as $keyee=>$value) {
                if($keyee=='Sr'){

                    $objPHPExcel->getActiveSheet()->getStyle($column . $roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle($column . $roww)->getFont()->setName('Cambria');
                    $objPHPExcel->getActiveSheet()->getStyle($column . $roww)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->setCellValue($column . $roww, $value);
                }
                if($keyee=='particulars'){
                    $objPHPExcel->getActiveSheet()->getStyle('B'. $roww)->getFont()->setName('Cambria');
                    $objPHPExcel->getActiveSheet()->getStyle('B'. $roww)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$roww, $value);

                }
                if($keyee=='no_patients'){
                    $objPHPExcel->getActiveSheet()->getStyle('C'. $roww)->getFont()->setName('Cambria');
                    $objPHPExcel->getActiveSheet()->getStyle('C'. $roww)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$roww, $value);

                }
                if($keyee=='amount'){
                    $total_amount+=$value;
                    $objPHPExcel->getActiveSheet()->getStyle('D'. $roww)->getFont()->setName('Cambria');
                    $objPHPExcel->getActiveSheet()->getStyle('D'. $roww)->getFont()->setSize(11);
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$roww, number_format($value));
                    //$objPHPExcel->getActiveSheet()->setCellValue('D'.$roww, $value);

                }
            }
            $roww++;
        }

        $rowDown=$roww-1;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowDown.':D'.$rowDown)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$roww)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C'. $roww)->getFont()->setName('Cambria');
        $objPHPExcel->getActiveSheet()->getStyle('C'. $roww)->getFont()->setSize(11);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$roww, 'Total');
        $objPHPExcel->getActiveSheet()->getStyle('D'.$roww)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$roww, number_format($total_amount));


        $rowCashier=$row+5;
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCashier)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $rowCashierLabel=$rowCashier+2;
        $objPHPExcel->getActiveSheet()->getStyle('B'. $rowCashierLabel)->getFont()->setName('Calibri');
        $objPHPExcel->getActiveSheet()->getStyle('B'. $rowCashierLabel)->getFont()->setSize(11);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowCashierLabel, 'Cashier');

        $cashCounter=$rowCashierLabel+5;
        $objPHPExcel->getActiveSheet()->getStyle('B'.$cashCounter)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $rowcashCounter=$cashCounter+2;
        $objPHPExcel->getActiveSheet()->getStyle('B'. $rowcashCounter)->getFont()->setName('Calibri');
        $objPHPExcel->getActiveSheet()->getStyle('B'. $rowcashCounter)->getFont()->setSize(11);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rowcashCounter, 'Cash Counter Incharge');

        $directorHospital=$rowcashCounter+5;
        $objPHPExcel->getActiveSheet()->getStyle('B'.$directorHospital)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
        $directorHospitalLabel=$directorHospital+2;
        $objPHPExcel->getActiveSheet()->getStyle('B'. $directorHospitalLabel)->getFont()->setName('Calibri');
        $objPHPExcel->getActiveSheet()->getStyle('B'. $directorHospitalLabel)->getFont()->setSize(11);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$directorHospitalLabel, 'Director Hospital');



        // Add more data as needed
        // Set the header for the Excel file
        $filename ='Income Summary.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the Excel file to the browser
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function getAllKposFillter()
    {
        $this->db->select('staff.name as kpo_name,staff.id as kpo_id');
        $this->db->from('staff');
        $this->db->join('staff_roles','staff_roles.staff_id=staff.id');
        $this->db->where('staff_roles.role_id',8);
        $this->db->order_by('staff.id','DES');
        return $this->db->get()->result_array();

    }
   public function doctorIncomereport($value = '')
    {
        if (!$this->rbac->hasPrivilege('transaction_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/income/doctorIncomeReport');
        $search_type = $this->input->post("search_type");
        $search_department =$this->input->post("department");

        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
            $search_doctor_id =$this->input->post("doctorId");
        } else {
            $search_type = "this_month";
        }
        
    
        // Get doctor_id from the form post
         $doctor_id = $this->input->post("doctorId");
         if (!empty($doctor_id)) {
            $additional_whereopd = array(
                "opd_details.cons_doctor = '" . $doctor_id . "'",
            );
            $additional_whereipd = array(
                "ipd_details.cons_doctor = '" . $doctor_id . "'",
            );
            $additional_wherepharma = array(
                "pharmacy_bill_basic.cons_doctor = '" . $doctor_id . "'",
            );
            $additional_wherepath = array(
                "pathology_report.consultant_doctor = '" . $doctor_id . "'",
            );
            $additional_whereradio = array(
                "radiology_report.consultant_doctor = '" . $doctor_id . "'",
            );
            $additional_whereot = array(
                "operation_theatre.cons_doctor = '" . $doctor_id . "'",
            );
            $additional_whereblood_issue = array(
                "blood_issue.cons_doctor = '" . $doctor_id . "'",
            );
            $additional_whereamb = array(
                "ambulance_call.cons_doctor = '" . $doctor_id . "'",
            );
        } else {
            $additional_whereopd = array('1 = 1');
        }
        $parameter =
         array(
            'OPD' => array('label' => 'OPD', 'table'               => 'opd_details', 'search_table' => 'opd_details',
            'search_column'                           => 'created_at', 'select' => 'opd_details.*,patients.mrno,opd_details.opd_discount as discount,SUM(smc.comission_amount) as staff_share,mc.comission_amount as doctor_share,dpt.department_name,kpo.id as kpo_id,kpo.name as kpo_name,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.address,staff.name,org.organisation_name,staff.surname',
            // 'search_column'                           => 'appointment_date', 'select' => 'opd_details.*,mc.comission_amount as doctor_share,dpt.department_name,kpo.id as kpo_id,kpo.name as kpo_name,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.address,staff.name,org.organisation_name,staff.surname',
            'join'                                    => array('LEFT JOIN staff ON opd_details.cons_doctor = staff.id',
                'LEFT JOIN patients ON opd_details.patient_id = patients.id',
                'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
                'LEFT JOIN organisation as org ON org.id = opd_details.organization_charge_id',
                'LEFT JOIN department as dpt ON dpt.id = opd_details.department',
                "LEFT JOIN monthly_comission as mc ON mc.bill_no = opd_details.id AND mc.staff_id=opd_details.cons_doctor AND mc.commission_type='OPD'",
               "LEFT JOIN monthly_comission as smc ON smc.bill_no = opd_details.id AND smc.staff_id != opd_details.cons_doctor AND smc.commission_type='OPD'",
            ),
            'where'=>$additional_whereopd,
            'group_by' => 'opd_details.id, mc.comission_amount, dpt.department_name, kpo.id, kpo.name, patients.id, staff.name, org.organisation_name, staff.surname'
            ),
            'Pathology'              => array('label' => 'Pathology', 'table' => 'pathology_report', 'search_table' => 'pathology_report',
                //'search_column'                           => 'reporting_date',
                'search_column'                           => 'created_at',
                'select'                                  => 'organisation.organisation_name,patients.mrno,mc.comission_amount as doctor_share,SUM(smc.comission_amount) as staff_share,pathology_report.pth_discount as discount,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.address,kpo.id as kpo_id,kpo.name as kpo_name,pathology_report.*, pathology_report.apply_charge as amount,pathology_report.id as reff,pathology_report.reporting_date as date,pathology.id, pathology.short_name,pathology.test_name as department_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name,patients.patient_unique_id,staff.surname,staff.name,patients.address',
                'join'                                    => array(
                    'JOIN pathology ON pathology_report.pathology_id = pathology.id',
                    'LEFT JOIN staff ON pathology_report.consultant_doctor = staff.id',
                    'LEFT JOIN charges ON charges.id = pathology.charge_id',
                    'LEFT JOIN patients ON pathology_report.patient_id=patients.id',
                    'LEFT JOIN organisations_charges ON organisations_charges.id=pathology_report.organization_charge_id',
                    'LEFT JOIN organisation ON organisation.id=organisations_charges.org_id',
                    'LEFT JOIN staff as kpo ON kpo.id = pathology_report.generated_by',
                    "LEFT JOIN monthly_comission as mc ON mc.bill_no = pathology_report.id AND mc.staff_id=pathology_report.consultant_doctor AND mc.commission_type='PATHOLOGY'",
                    "LEFT JOIN monthly_comission as smc ON smc.bill_no = pathology_report.id AND smc.staff_id != pathology_report.consultant_doctor AND smc.commission_type='PATHOLOGY'",

                ),
                    'where'=>$additional_wherepath,
                    'group_by' => 'pathology_report.id, mc.comission_amount, kpo.id, kpo.name, patients.id, staff.name, staff.surname'
            ),
            'Radiology'              => array('label' => 'Radiology', 'table' => 'radiology_report', 'search_table' => 'radiology_report',
               // 'search_column'                           => 'reporting_date',
                'search_column'                           => 'created_at',
                'select'                                  => 'charges.charge_category,organisation.organisation_name,patients.mrno,radio.test_name as department_name,mc.comission_amount as doctor_share,SUM(smc.comission_amount) as staff_share,radiology_report.radio_discount as discount,kpo.id as kpo_id,kpo.name as kpo_name,radiology_report.*,radiology_report.apply_charge as amount,radiology_report.apply_charge as amount,radiology_report.reporting_date as date, radiology_report.id as reff,radio.id, radio.short_name,charges.id as cid,charges.charge_category,charges.standard_charge,patients.patient_name,patients.patient_unique_id,staff.surname,staff.name,patients.address',
                'join'                                    => array(
                    'LEFT JOIN radio ON radiology_report.radiology_id = radio.id',
                    'LEFT JOIN staff ON radiology_report.consultant_doctor = staff.id',
                    'LEFT JOIN charges ON charges.id = radio.charge_id',
                    'LEFT JOIN patients ON radiology_report.patient_id=patients.id',
                    'LEFT JOIN organisations_charges ON organisations_charges.id=radiology_report.organization_charge_id',
                    'LEFT JOIN organisation ON organisation.id=organisations_charges.org_id',

                    'LEFT JOIN staff as kpo ON kpo.id = radiology_report.generated_by',
                    "LEFT JOIN monthly_comission as mc ON mc.bill_no = radiology_report.id AND mc.staff_id=radiology_report.consultant_doctor AND mc.commission_type='RADIOLOGY'",
                    "LEFT JOIN monthly_comission as smc ON smc.bill_no = radiology_report.id AND smc.staff_id != radiology_report.consultant_doctor AND smc.commission_type='RADIOLOGY'",
                ),
                'where'=>$additional_whereradio,
                'group_by' => 'radiology_report.id, mc.comission_amount, kpo.id, kpo.name, patients.id, staff.name, staff.surname'

            ),
        );

        $i                 = 0;
       
        if (array_key_exists($search_department, $parameter)) {
            $data['parameter'][$search_department] = $parameter[$search_department];
        } else {
            $data['parameter'] = []; // Or handle this scenario appropriately
        }
        // echo "<pre>"; print_r($data);exit;
        foreach ($data['parameter'] as $key => $value) {

                # code...
    
                $select     = $parameter[$key]['select'];
                $join       = $parameter[$key]['join'];
                $table_name = $parameter[$key]['table'];
                $where = isset($parameter[$key]['where']) ? $parameter[$key]['where'] : [];
                $group_by = isset($parameter[$key]['group_by']) ? $parameter[$key]['group_by'] : [];
    
                if (empty($search_type)) {
    
                    $search_type = "";
                    $resultList  = $this->report_model->getReport($select, $join, $table_name);
                } else {
    
                    $search_table     = $parameter[$key]['search_table'];
                    $search_column    = $parameter[$key]['search_column'];
                    $result_List       = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column,$where,$ab='',$bs='',$group_by);
                    //echo $this->db->last_query();
                    $resultList       = isset($result_List['main_data']) && !empty($result_List['main_data']) ? $result_List['main_data'] : '';
                }
    
                $rd[$parameter[$key]['label']]         = $resultList;
                $data['parameter'][$key]['resultList'] = $resultList;
                if(!empty($resultList)){
                    $data['parameter'][$key]['kpoID'] = array_unique(array_column($resultList, 'kpo_id'));
                    $data['parameter'][$key]['kpoName'] = array_unique(array_column($resultList, 'kpo_name'));
                }
    
                $i++;

        }
        if (!empty($doctor_id)) {
            $additional_where2 = array(
                "ipd_billing.cons_doctor = '" . $doctor_id . "'",
            );
        } else {
            $additional_where2 = array('1 = 1');
        }

        if (!empty($doctor_id)) {
            $additional_where3 = array(
                "opd_details.cons_doctor = '" . $doctor_id . "'",
            );
        } else {
            $additional_where3 = array('1 = 1');
        }

        $resultList3 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,patients.mrno,kpo.name as kpo_name,opd_details.opd_no,opd_billing.date,opd_billing.net_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            //'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_billing', $search_column = 'date',$additional_where3);

        if (!empty($resultList3['main_data'])) {
            foreach ($resultList3['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }
        }

        if (!empty($doctor_id)) {
            $additional_where4 = array(
                "opd_details.cons_doctor = '" . $doctor_id . "'",
            );
        } else {
            $additional_where4 = array('1 = 1');
        }

        $resultList4 = $this->report_model->searchReport($select = 'kpo.id as kpo_id,kpo.name as kpo_name,opd_details.opd_no,opd_payment.date,opd_payment.paid_amount as amount,patients.id as pid,patients.patient_name,opd_details.opd_no as reff,patients.patient_unique_id', $join = array('JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
            'LEFT JOIN opd_payment ON opd_payment.opd_id = opd_details.id',
            //'LEFT JOIN opd_billing ON opd_billing.opd_id = opd_details.id',
            'LEFT JOIN staff as kpo ON kpo.id = opd_details.generated_by',
        ), $table_name = 'opd_details', $search_type, $search_table = 'opd_payment', $search_column = 'date',$additional_where4);

        if (!empty($resultList4['main_data'])) {
            foreach ($resultList4['main_data'] as $key => $value) {
                array_push($rd["OPD"], $value);
                array_push($data['parameter']["OPD"]['resultList'], $value);
                $data['parameter'][$key]['kpoID'] = array_unique(array_column($value, 'kpo_id'));
                $data['parameter'][$key]['kpoName'] = array_unique(array_column($value, 'kpo_name'));
            }

        }
        $data["resultlist"]  = $rd;
        // echo "<pre>";print_r($rd);exit;
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data['doctors'] = $this->staff_model->getStaffbyrole(3);
        $data['search_doctor_id'] = $search_doctor_id;
        $data['kpo_filterData']=$this->getAllKpos();
        $data['search_department'] = $search_department;
        $this->load->view('layout/header', $data);
        if($this->input->post('search')=='export_pdf'){
            $this->exportIncomeReportPdf($data);
        }
        if($this->input->post('search')=='export_summary_pdf'){
            $data["kpo_id"] = $this->input->post("kpoid");
            if($search_type == 'period') {
                $data["from_date"] = $this->input->post("date_from");
                $data["to_date"] = $this->input->post("date_to");
            }
            $this->exportIncomeReportSummaryPdf($data);

        }
        if($this->input->post('search')=='export_summary_new_pdf'){
            $data["kpo_id"] = $this->input->post("kpoid");
            if($search_type == 'period') {
                $data["from_date"] = $this->input->post("date_from");
                $data["to_date"] = $this->input->post("date_to");
            }
            $this->exportDoctorIncomeReportSummaryNewPdf($data);

        }
        else{
            $this->load->view('admin/income/doctorIncomeReport', $data);
        }
        $this->load->view('layout/footer', $data);
    }

    public function exportDoctorIncomeReportSummaryNewPdf($data)
    {
        $html  = $this->load->view('admin/income/export_doctor_income_report_summary_new_pdf',$data, true);
       // exit;
        $this->load->library('pdf');
        $this->dompdf->set_paper("letter", "landscape");
        $customPaper = array(0,0,360,360);
        $this->dompdf->loadHtml($html);
        ini_set('display_errors', 1);
        // Render the HTML as PDF
        $this->dompdf->render();
        $canvas =  $this->dompdf->getCanvas();
        $date=date('d-M-Y h:i A',strtotime(date('Y-m-d H:i:s')));
        $totalPages = $this->dompdf->getCanvas()->get_page_count();

        // $canvas->page_text(270, 780, "Page : {PAGE_NUM}", null, 10, [0, 0, 0]);
        // $canvas->page_text(270, 760, $date, null, 10, [0, 0, 0]);
        $canvas->page_text(510, 5, "Page {PAGE_NUM} of {$totalPages}", null, 10, [0, 0, 0]);
        $canvas->page_text(30, 760, $date, null, 10, [0, 0, 0]);
        $this->dompdf->stream("Income Summary Report.pdf", array("Attachment"=>1));
    }

}
