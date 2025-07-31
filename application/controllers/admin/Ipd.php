<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ipd extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type');
    }

    public function commission()
    {
        $this->session->set_userdata('top_menu', 'IPD Commission');
        $this->session->set_userdata('sub_menu', 'admin/ipd/commission');

        $doctorlist           = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']   = $doctorlist;
        $data['organisation'] = $this->organisation_model->get();
        $search_type          = $this->input->post("search_type");
        $doctorid             = $this->input->post("doctor");
        $organisation_id      = $this->input->post("organisation");
        $type_select          = $this->input->post("type");

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
        

        $this->db->select('tpa_adjustments.*, tpa_adjustments.id as adjusted_id, discharge_calculations.*, patients.patient_name, staff.name as consultant, charges.code, organisation.organisation_name, organisation_payments.*, discharge_calculations.doctor_share as doctor_commission, IFNULL(commission_payments.total_paid, 0) as total_paid');

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

        // Apply doctor filter
        if (!empty($doctorid)) {
            $this->db->where('discharge_calculations.doctor_id', $doctorid);
        }

        // Apply organisation filter
        if (!empty($organisation_id)) {
            $this->db->where('discharge_calculations.org_id', $organisation_id);
        }

        // Apply share type filter
        if (!empty($type_select)) {
            $this->db->where('tpa_adjustments.deduction_from', $type_select);
        }

        $query = $this->db->get();


        // log_message('debug', 'IPD Commission Report: ' . json_encode($query->result_array()));

        $data["resultlist"] = $query->result_array();
        $data["searchlist"] = $this->search_type;
        $data["search_type"] = $search_type;
        $data["doctor_select"] = $doctorid;
        $data["tpa_select"] = $organisation_id;
        $data["type_select"] = $type_select;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/ipd/commission', $data);
        $this->load->view('layout/footer', $data);
    }

    public function commissionreport()
    {
        $this->session->set_userdata('top_menu', 'IPD Commission');
        $this->session->set_userdata('sub_menu', 'admin/ipd/commission-report');

        $doctorlist           = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']   = $doctorlist;
        $data['organisation'] = $this->organisation_model->get();
        $search_type          = $this->input->post("search_type");
        $doctorid             = $this->input->post("doctor");

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
        

        $this->db->select('discharge_calculations.doctor_id, MAX(staff.name) as doctor_name, 
            SUM(discharge_calculations.doctor_share - tpa_adjustments.deduction_amount) as total_doctor_share, 
            COALESCE(commission_payments.total_paid, 0) as total_paid');

        $this->db->from('discharge_calculations');
        $this->db->join('tpa_adjustments', 'tpa_adjustments.discharge_id = discharge_calculations.id', 'left');
        $this->db->join('staff', 'discharge_calculations.doctor_id = staff.id', 'left');

        // Left join with commission_payments subquery to get total_paid
        $this->db->join('(SELECT payee_id, SUM(paid_amount) as total_paid 
            FROM commission_payments GROUP BY payee_id) as commission_payments', 
            'discharge_calculations.doctor_id = commission_payments.payee_id', 'left');

        // Apply WHERE conditions
        $this->db->where('tpa_adjustments.deduction_from', 'doctor');
        // Apply date filters if necessary
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
        // Apply doctor filter
        if (!empty($doctorid)) {
            $this->db->where('discharge_calculations.doctor_id', $doctorid);
        }

        $this->db->group_by('discharge_calculations.doctor_id');
        $this->db->order_by('discharge_calculations.doctor_id', 'ASC');

        $query = $this->db->get();



        // log_message('debug', 'IPD Commission Report: ' . json_encode($query->result_array()));

        $data["resultlist"] = $query->result_array();
        $data["searchlist"] = $this->search_type;
        $data["search_type"] = $search_type;
        $data["doctor_select"] = $doctorid;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/ipd/commission-report', $data);
        $this->load->view('layout/footer', $data);
    }

    public function pay_commission()
    {
        // var_dump($_POST);
        // die;
        if (!$this->rbac->hasPrivilege('organisation', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('pay_amount', 'Payment Amount', 'required');
        $this->form_validation->set_rules('cheque_no', 'Cheque No', 'required');
        $this->form_validation->set_rules('bank_name', 'Bank Name', 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pay_amount'           => form_error('pay_amount'),
                'cheque_no'           => form_error('cheque_no'),
                'bank_name'           => form_error('bank_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            if (!empty($_POST['pay_amount']) && $_POST['pay_amount'] > 0) {

                $payeeId = $this->input->post('pay_doctor_id');
                $commission_amount = $this->input->post('commission_amount');
                $paid_amount = $this->input->post('pay_amount');
                $balance_amount = $this->input->post('arrears');
                $cheque_no = $this->input->post('cheque_no');
                $bank_name = $this->input->post('bank_name');
                $payment_date = $this->input->post('pay_date');
                $org_id = $this->input->post('tpa_id');
                $org_pay_id = $this->input->post('org_pay_id');
                $adjusted_id = $this->input->post('adjusted_id');

            

                    $data = array(
                        'payee_id' => $payeeId,
                        'org_id' => $org_id,
                        'org_pay_id' => $org_pay_id,
                        'adjusted_id' => $adjusted_id,
                        'commission_amount' => $commission_amount,
                        'paid_amount' => $paid_amount,
                        'balance_amount' => $balance_amount,
                        'cheque_no' => $cheque_no,
                        'bank' => $bank_name,
                        'payment_date' => $payment_date,
                    );

                    $this->organisation_model->addCommissionPayment($data);
            }
            
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function commissionpayments($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'IPD Commission');
        $data['title']      = 'Commission Payments';
        $data['resultlist'] = $this->organisation_model->getPaidCommisssionDetail($id);
        $this->load->view('layout/header');
        $this->load->view('admin/ipd/commission-payments', $data);
        $this->load->view('layout/footer');
    }

}
