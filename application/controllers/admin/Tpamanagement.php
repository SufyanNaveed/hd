<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tpamanagement extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'tpa_management');
        $data['title']      = 'TPA Management';
        $data['resultlist'] = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/tpamanagement/index', $data);
        $this->load->view('layout/footer');
    }

    public function payments($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'tpa_management');
        $data['title']      = 'TPA Payments Management';
        $data['resultlist'] = $this->organisation_model->getPayments($id);
        $data['discharged_patients'] = $this->organisation_model->getDischargePatients();
        $this->load->view('layout/header');
        $this->load->view('admin/tpamanagement/payments', $data);
        $this->load->view('layout/footer');
    }

    public function deductions($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'tpa_management');
        $data['title']      = 'TPA Deductions Management';
        $data['resultlist'] = $this->organisation_model->getDeductionsDetail($id);
        $this->load->view('layout/header');
        $this->load->view('admin/tpamanagement/deductions', $data);
        $this->load->view('layout/footer');
    }

    public function add_organisation()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('contact_number', $this->lang->line('contact') . " " . $this->lang->line('number'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'           => form_error('name'),
                'code'           => form_error('code'),
                'contact_number' => form_error('contact_number'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $organisation = array(
                'organisation_name'    => $this->input->post('name'),
                'code'                 => $this->input->post('code'),
                'contact_no'           => $this->input->post('contact_number'),
                'address'              => $this->input->post('address'),
                'contact_person_name'  => $this->input->post('contact_person_name'),
                'contact_person_phone' => $this->input->post('contact_person_phone'),
            );
            $org_id = $this->organisation_model->add($organisation);

            // Check if there are values in inputs cheque[], date[], amount[], bank[]
            if (!empty($_POST['cheque']) && !empty($_POST['date']) && !empty($_POST['amount']) && !empty($_POST['bank'])) {
                $cheques = $this->input->post('cheque');
                $dates = $this->input->post('date');
                $amounts = $this->input->post('amount');
                $banks = $this->input->post('bank');

                foreach ($cheques as $key => $cheque) {
                    $payment_data = array(
                        'cheque_no' => $cheque,
                        'date' => $dates[$key],
                        'amount' => $amounts[$key],
                        'bank' => $banks[$key],
                        'org_id' => $org_id,
                    );
                    $this->organisation_model->addPayments($payment_data);
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }


    public function get_data($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $org   = $this->organisation_model->get($id);
        $array = array(
            'id'                     => $org['id'],
            'ename'                  => $org['organisation_name'],
            'ecode'                  => $org['code'],
            'econtact_number'        => $org['contact_no'],
            'eaddress'               => $org['address'],
            'econtact_persion_name'  => $org['contact_person_name'],
            'econtact_persion_phone' => $org['contact_person_phone'],
        );
        echo json_encode($array);
    }

    public function pay_data($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $org   = $this->organisation_model->getPaymentsData($id);
        $array = array(
            'org_id'                => $org['org_id'],
            'org_name'              => $org['organisation_name'],
            'lodged_amount'         => $org['lodged_amount'],
            'approved_amount'       => $org['approved_amount'],
            'released_amount'       => $org['released_amount'],
        );
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('ename', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('ecode', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('econtact_number', $this->lang->line('contact') . " " . $this->lang->line('number'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('ename'),
                'e2' => form_error('ecode'),
                'e3' => form_error('econtact_number'),
                'e4' => form_error('eaddress'),
                'e5' => form_error('econtact_persion_name'),
                'e6' => form_error('econtact_persion_phone'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $organigation = array(
                'id'                   => $this->input->post('org_id'),
                'organisation_name'    => $this->input->post('ename'),
                'code'                 => $this->input->post('ecode'),
                'contact_no'           => $this->input->post('econtact_number'),
                'address'              => $this->input->post('eaddress'),
                'contact_person_name'  => $this->input->post('econtact_persion_name'),
                'contact_person_phone' => $this->input->post('econtact_persion_phone'),
            );
            $this->organisation_model->edit($organigation);

            // Check if there are values in inputs cheque[], date[], amount[], bank[]
            if (!empty($_POST['cheque']) && !empty($_POST['date']) && !empty($_POST['amount']) && !empty($_POST['bank'])) {
                $cheques = $this->input->post('cheque');
                $dates = $this->input->post('date');
                $amounts = $this->input->post('amount');
                $banks = $this->input->post('bank');

                foreach ($cheques as $key => $cheque) {
                    $payment_data = array(
                        'cheque_no' => $cheque,
                        'date' => $dates[$key],
                        'amount' => $amounts[$key],
                        'bank' => $banks[$key],
                        'org_id' => $this->input->post('org_id')
                    );
                    $this->organisation_model->addPayments($payment_data);
                }
            }

            $array = array('status' => 'suucess', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_delete')) {
            access_denied();
        }
        $this->organisation_model->delete($id);
        redirect('admin/tpamanagement');
    }

    public function deletePayment($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_delete')) {
            access_denied();
        }
        $this->organisation_model->deletePayment($id);
        redirect('admin/tpamanagement/payments/' . $id);
    }

    public function deleteDeduction($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_delete')) {
            access_denied();
        }
        $this->organisation_model->deleteDeduction($id);
        redirect('admin/tpamanagement/deductions/' . $id);
    }

    public function tpareport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/tpamanagement/tpareport');

        $doctorlist           = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']   = $doctorlist;
        $data['organisation'] = $this->organisation_model->get();
        $search_type          = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        $parameter = array('OPD' => array('label' => 'OPD', 'table'               => 'opd_details', 'search_table' => 'opd_details',
            'search_column'                           => 'appointment_date', 'select' => 'opd_details.*,opd_details.appointment_date as date,opd_details.opd_no as reff, patients.id as pid,patients.patient_name,patients.patient_unique_id,staff.name,staff.surname,organisation.organisation_name',
            'join'                                    => array('JOIN staff ON opd_details.cons_doctor = staff.id',
                'JOIN patients ON opd_details.patient_id = patients.id',
                'JOIN organisation ON organisation.id = patients.organisation',
            )),
            'IPD'                    => array('label' => 'IPD', 'table' => 'ipd_details', 'search_table' => 'payment',
                'search_column'                           => 'date',
                'select'                                  => 'ipd_details.ipd_no,payment.date,payment.paid_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id,staff.name,staff.surname,organisation.organisation_name',
                'join'                                    => array(
                    'JOIN staff ON ipd_details.cons_doctor = staff.id',
                    'JOIN patients ON ipd_details.patient_id = patients.id',
                    'JOIN payment ON payment.patient_id = patients.id',
                    'JOIN organisation ON organisation.id = patients.organisation',
                ),

            ),
            'Operation_Theatre'      => array('label' => 'Operation Theatre', 'table' => 'operation_theatre', 'search_table' => 'operation_theatre',
                'search_column'                           => 'date',
                'select'                                  => 'operation_theatre.*,patients.id as pid,patients.patient_unique_id,patients.patient_name,charges.id as cid,charges.charge_category,charges.code,charges.description,charges.standard_charge as standard_charges, operation_theatre.apply_charge as amount,staff.name,staff.surname,organisation.organisation_name',
                'join'                                    => array(
                    'JOIN patients ON operation_theatre.patient_id=patients.id',
                    'JOIN staff ON staff.id = operation_theatre.consultant_doctor',
                    'JOIN charges ON operation_theatre.charge_id = charges.id',
                    'JOIN organisation ON organisation.id = patients.organisation',
                )),
            'Pathology'      => array('label' => 'Pathology', 'table' => 'pathology_report', 'search_table' => 'pathology_report',
                'search_column'                           => 'reporting_date',
                //'select'                                  => 'pathology_report.*,patients.id as pid,patients.patient_unique_id,patients.patient_name,charges.id as cid,charges.charge_category,charges.code,charges.description,charges.standard_charge as standard_charges, operation_theatre.apply_charge as amount,staff.name,staff.surname,organisation.organisation_name',
                'select'                                  => 'pathology_report.*,pathology_report.reporting_date as date,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,charges.charge_category,charges.code,charges.standard_charge as standard_charges,patients.patient_name,pathology_report.apply_charge as amount,staff.name,staff.surname,organisation.organisation_name',
                'join'                                    => array(
                    'JOIN patients ON pathology_report.patient_id = patients.id',
                    'JOIN pathology ON pathology_report.pathology_id = pathology.id',
                    'JOIN charges ON pathology.charge_id = charges.id',
                    'JOIN organisations_charges ON organisations_charges.id = pathology_report.organization_charge_id',
                    'JOIN organisation ON organisation.id = organisations_charges.org_id',
                    'LEFT JOIN staff ON staff.id = pathology_report.consultant_doctor',

                )),
        );

        $i                 = 0;
        $data["parameter"] = $parameter;
        //echo "<pre>";print_r($data["parameter"]);exit;
        foreach ($parameter as $key => $value) {

            $select     = $parameter[$key]['select'];
            $join       = $parameter[$key]['join'];
            $table_name = $parameter[$key]['table'];

            //$additional_where   = array("patients.organisation !=' '");
            $additional_where   = [];
            $orgnid             = $this->input->post('organisation');
            $data['tpa_select'] = $orgnid;
            if (!empty($orgnid) && ($key != 'Pathology')) {
                $additional_where = array("patients.organisation = '" . $orgnid . "' ");
            }
            if (!empty($orgnid) && ($key == 'Pathology')) {
                $additional_where = array("organisation.id = '" . $orgnid . "' ");
            }
            $doctorid = $this->input->post('doctor');

            if ((!empty($doctorid)) && ($key == 'OPD')) {
                $additional_where[] = "opd_details.cons_doctor =" . $doctorid;
            }
            if ((!empty($doctorid)) && ($key == 'IPD')) {
                $additional_where[] = "ipd_details.cons_doctor =" . $doctorid;
            }

            if ((!empty($doctorid)) && ($key == 'Operation_Theatre')) {
                $additional_where[] = "operation_theatre.consultant_doctor =" . $doctorid;
            }
            if ((!empty($doctorid)) && ($key == 'Pathology')) {
                $additional_where[] = "pathology_report.consultant_doctor =" . $doctorid;
            }

            if (empty($search_type)) {
                $search_type = "";
                $resultList  = $this->report_model->getReport($select, $join, $table_name, $additional_where);
               // echo "yesss";
                //echo $this->db->last_query();
            } else {

                $search_table  = $parameter[$key]['search_table'];
                $search_column = $parameter[$key]['search_column'];
                $additional    = array();

                $resultList = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where);
                //echo $this->db->last_query();
            }

            $rd[$parameter[$key]['label']]         = $resultList;
            $data['parameter'][$key]['resultList'] = $resultList;

            $i++;
        }
        //echo "<pre>";print_r($data);exit;
        $ipd_additional_where = array();
        if (!empty($doctorid)) {
            $ipd_additional_where = array('ipd_details.cons_doctor =' . $doctorid);
        }

        if (!empty($orgnid)) {
            $ipd_additional_where = array("patients.organisation = '" . $orgnid . "' ");
        }

        $resultList2 = $this->report_model->searchReport($select = 'charges.standard_charge as standard_charges,ipd_details.ipd_no,ipd_billing.date,ipd_billing.net_amount as amount,patients.id as pid,patients.patient_name,ipd_details.ipd_no as reff,patients.patient_unique_id,staff.name,staff.surname,organisation.organisation_name',
        $join = array('JOIN staff ON ipd_details.cons_doctor = staff.id',
            'JOIN patients ON ipd_details.patient_id = patients.id',
            'JOIN ipd_billing ON ipd_billing.patient_id = patients.id',
            'JOIN organisation ON organisation.id = patients.organisation',
            'JOIN patient_charges ON patient_charges.patient_id=patients.id',
            'JOIN charges ON  charges.id=patient_charges.charge_id'

        ), $table_name = 'ipd_details', $search_type, $search_table = 'ipd_billing', $search_column = 'date', $ipd_additional_where);

        if (!empty($resultList2)) {

            $rd["IPD"]=$resultList2;
            //array_push($rd["IPD"], $resultList2);
            //array_push($data['parameter']["IPD"]['resultList'], $resultList2);
        }

        $data["resultlist"]    = $rd;
       /// echo "<pre>";print_r($data["resultlist"]);exit;
       // echo "<pre>";print_r($data["resultlist"]);exit;
        $data["searchlist"]    = $this->search_type;
        $data["search_type"]   = $search_type;
        $data["doctor_select"] = $doctorid;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/tpamanagement/tpareport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function adjust_bill()
    {
        // var_dump($_POST);
        // die;
        if (!$this->rbac->hasPrivilege('organisation', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('discharge_id[]', 'Visit No', 'required');
        $this->form_validation->set_rules('deduction_amount[]', 'Deduction Amount', 'required');
        $this->form_validation->set_rules('deduction_from[]', 'Deduction From', 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'discharge_id'           => form_error('discharge_id[]'),
                'deduction_amount'           => form_error('deduction_amount[]'),
                'deduction_from'           => form_error('deduction_from[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            // Check if there are values in inputs deduction_amount[], share_type[]
            if (!empty($_POST['cheque_no'])) {
                $cheque_no = $this->input->post('cheque_no');
                $cheque_amount = $this->input->post('cheque_amount');
                $org_pay_id = $this->input->post('org_pay_id');
            
                $billDeductions = $this->input->post('discharge_id');
                $procedures = $this->input->post('procedure');
                $lodged_amounts = $this->input->post('lodged_amount');
                $approved_amounts = $this->input->post('approved_amount');
                $deduction_amounts = $this->input->post('deduction_amount');
                $deduction_from = $this->input->post('deduction_from'); // Assuming deduction_from is available
            
                $previous_balance = 0; // Initialize previous balance
            
                foreach ($billDeductions as $key => $bill) {
                    // Calculate balance_amount based on previous_balance for the first record
                    if ($key == 0) {
                        $balance_amount = floatval($cheque_amount) - floatval($approved_amounts[$key] + $deduction_amounts[$key]);
                    } else {
                        $balance_amount = $previous_balance - floatval($approved_amounts[$key] + $deduction_amounts[$key]);
                    }
            
                    $previous_balance = $balance_amount; // Update previous_balance for next iteration
                    
                    if($balance_amount < 0) {
                        $balance_amount = 0;
                    }
                    
                    $data = array(
                        'discharge_id' => $bill,
                        'org_pay_id' => $org_pay_id,
                        'approved_amount' => $approved_amounts[$key],
                        'deduction_amount' => $deduction_amounts[$key],
                        'deduction_from' => $deduction_from[$key], // Assuming deduction_from is available
                        'balance_amount' => $balance_amount,
                        'set_by' => $this->customlib->getAdminSessionUserName(),
                    );

                    $this->organisation_model->addTPABill($data);

                    $this->organisation_model->addTPAPayment($data);
                    
                }
            }
            
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function deductionreport()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'tpa_management');

        $chequelist           = $this->organisation_model->getChequesList();
        $data['chequelist']   = $chequelist;
        $data['organisation'] = $this->organisation_model->get();
        $data['discharged_patients'] = $this->organisation_model->getPatients();
        $search_type          = $this->input->post("search_type");
        $chequeId             = $this->input->post("cheque");
        $organisation_id      = $this->input->post("organisation");
        $patientId       = $this->input->post("patient");

        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        if ($search_type == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                echo form_error();
            } else {
                $data['from_date'] = $from_date = $this->input->post('date_from');
                $data['to_date'] = $to_date = $this->input->post('date_to');
                $data['from_date'] = date("Y-m-d", $this->customlib->datetostrtotime($from_date));
                $data['to_date'] = date("Y-m-d 23:59:59.993", $this->customlib->datetostrtotime($to_date));
            }
        } else if ($search_type == 'today') {
            $today = strtotime('today 00:00:00');
            $data['from_date'] = $first_date = date('Y-m-d ', $today);
        } else if ($search_type == 'this_week') {
            $this_week_start = strtotime('-1 week monday 00:00:00');
            $this_week_end = strtotime('sunday 23:59:59');
            $data['from_date'] = $first_date = date('Y-m-d H:i:s', $this_week_start);
            $data['to_date'] = $last_date = date('Y-m-d H:i:s', $this_week_end);
        } else if ($search_type == 'last_week') {
            $last_week_start = strtotime('-2 week monday 00:00:00');
            $last_week_end = strtotime('-1 week sunday 23:59:59');
            $data['from_date'] = $first_date = date('Y-m-d H:i:s', $last_week_start);
            $data['to_date'] = $last_date = date('Y-m-d H:i:s', $last_week_end);
        } else if ($search_type == 'this_month') {
            $data['from_date'] = $first_date = date('Y-m-01');
            $data['to_date'] = $last_date = date('Y-m-t 23:59:59.993');
        } else if ($search_type == 'last_month') {
            $month = date("m", strtotime("-1 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $data['to_date'] = $last_date = date('Y-' . $month . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');
        } else if ($search_type == 'last_3_month') {
            $month = date("m", strtotime("-2 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $firstday = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
        } else if ($search_type == 'last_6_month') {
            $month = date("m", strtotime("-5 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $firstday = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
        } else if ($search_type == 'last_12_month') {
            $data['from_date'] = $first_date = date('Y-m' . '-01', strtotime("-11 month"));
            $firstday = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
        } else if ($search_type == 'last_year') {
            $data['from_date'] = $search_year = date('Y', strtotime("-1 year"));
        } else if ($search_type == 'this_year') {
            $data['from_date'] = $search_year = date('Y');
        } else if ($search_type == 'all_time') {
            $data['from_date'] = 'all_time';
        }
        

        $this->db->select('tpa_adjustments.*, tpa_adjustments.id as adjusted_id, tpa_adjustments.created_at as adjusted_date, discharge_calculations.*, patients.patient_name, patients.patient_unique_id, staff.name as consultant, charges.code, organisation.organisation_name, organisation_payments.*, discharge_calculations.doctor_share as doctor_commission, IFNULL(commission_payments.total_paid, 0) as total_paid');

        $this->db->from('tpa_adjustments');
        $this->db->join('discharge_calculations', 'tpa_adjustments.discharge_id = discharge_calculations.id', 'left');
        $this->db->join('patients', 'discharge_calculations.patient_id = patients.id', 'left');
        $this->db->join('staff', 'discharge_calculations.doctor_id = staff.id', 'left');
        $this->db->join('charges', 'discharge_calculations.package_id = charges.id', 'left');
        $this->db->join('organisation', 'discharge_calculations.org_id = organisation.id', 'left');
        $this->db->join('organisation_payments', 'organisation_payments.id = tpa_adjustments.org_pay_id', 'left');
        $this->db->join('(SELECT adjusted_id, SUM(paid_amount) as total_paid 
                        FROM commission_payments GROUP BY adjusted_id) as commission_payments', 
                        'tpa_adjustments.id = commission_payments.adjusted_id', 'left');

        // log_message('debug', 'IPD Commission Date: ' .  $data['from_date']);

        // Apply date filters
        if ($search_type != 'all_time') {
            if ($search_type == 'period') {
                $this->db->where('tpa_adjustments.created_at >=', $data['from_date']);
                $this->db->where('tpa_adjustments.created_at <=', $data['to_date']);
            } else if ($search_type == 'today') {
                $this->db->where('DATE(tpa_adjustments.created_at)', date('Y-m-d'));
            } else if ($search_type == 'this_week' || $search_type == 'last_week' || $search_type == 'this_month' || $search_type == 'last_month' || $search_type == 'last_3_month' || $search_type == 'last_6_month' || $search_type == 'last_12_month') {
                $this->db->where('tpa_adjustments.created_at >=', $data['from_date']);
                $this->db->where('tpa_adjustments.created_at <=', $last_date);
            } else if ($search_type == 'last_year' || $search_type == 'this_year') {
                $this->db->where('YEAR(tpa_adjustments.created_at)', $search_year);
            }
        }

        // Apply cheque filter
        if (!empty($chequeId)) {
            $this->db->where('tpa_adjustments.org_pay_id', $chequeId);
        }

        // Apply patient filter
        if (!empty($patientId)) {
            $this->db->where('discharge_calculations.patient_id', $patientId);
        }

        // Apply organisation filter
        if (!empty($organisation_id)) {
            $this->db->where('discharge_calculations.org_id', $organisation_id);
        }


        $query = $this->db->get();


        // log_message('debug', 'IPD Commission Report: ' . json_encode($query->result_array()));

        $data["resultlist"] = $query->result_array();
        $data["searchlist"] = $this->search_type;
        $data["search_type"] = $search_type;
        $data["cheque_select"] = $chequeId;
        $data["tpa_select"] = $organisation_id;
        $data["patient_select"] = $patientId;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/tpamanagement/deductionreport', $data);
        $this->load->view('layout/footer', $data);
    }

}
