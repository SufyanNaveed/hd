<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Organisation_model extends CI_Model
{

    public function add($data)
    {
        $this->db->insert('organisation', $data);
        return $this->db->insert_id(); // Return the last inserted ID
    }

    public function edit($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('organisation', $data);
        }
    }

    public function get($id = null)
    {
        $this->db->select()->from('organisation');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id', 'asc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('organisation');

        $this->db->where('org_id', $id);
        $this->db->delete('organisation_payments');

        $this->db->where('cheque_no', $id);
        $this->db->delete('payment');
    }

    public function deletePayment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('organisation_payments');

        $this->db->where('org_pay_id', $id);
        $this->db->delete('tpa_adjustments');

        $this->db->where('cheque_no', $id);
        $this->db->delete('payment');

        // $this->db->where('org_pay_id', $id);
        // $this->db->delete('commission_payments');
    }

    public function deleteDeduction($id)
    {
        $this->db->where('org_pay_id', $id);
        $this->db->delete('tpa_adjustments');
    }

    public function Charge($ch_type)
    {
        $this->db->select(' charges.id , charges.standard_charge, schedule_charge_category.schedule');
        $this->db->join('schedule_charge_category', 'schedule_charges.schedule_charge_id = schedule_charge_category.id', 'left');
        $this->db->join('charges', 'schedule_charges.charge_id = charges.id', 'left');
        $this->db->where('charges.charge_type', $ch_type);
        $query = $this->db->get('schedule_charges');
        return $query->result_array();
    }

    public function addPayments($data)
    {
        $this->db->insert('organisation_payments', $data);
    }

    public function getPayments($id)
    {
        $this->db->select('organisation_payments.*, organisation.organisation_name, organisation.code, tpa_adjustments.balance_amount');
        $this->db->from('organisation_payments');
        $this->db->join('organisation', 'organisation_payments.org_id = organisation.id', 'left');
        $this->db->join('(SELECT org_pay_id, MIN(balance_amount) AS min_balance FROM tpa_adjustments GROUP BY org_pay_id) AS min_adjustments', 'organisation_payments.id = min_adjustments.org_pay_id', 'left');
        $this->db->join('tpa_adjustments', 'min_adjustments.org_pay_id = tpa_adjustments.org_pay_id AND min_adjustments.min_balance = tpa_adjustments.balance_amount', 'left');
        $this->db->where('org_id', $id);
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getPaymentsData($id)
    {
        // Query to get released amount from organisation_payments table
        $releasedQuery = "SELECT SUM(amount) AS released_amount FROM organisation_payments WHERE org_id = ?";
        $releasedResult = $this->db->query($releasedQuery, array($id));
        $releasedAmount = $releasedResult->row_array()['released_amount'];

        // Query to get lodged amount from discharge_calculations table
        $lodgedQuery = "SELECT SUM(total_amount + tax) AS lodged_amount FROM discharge_calculations WHERE org_id = ?";
        $lodgedResult = $this->db->query($lodgedQuery, array($id));
        $lodgedAmount = $lodgedResult->row_array()['lodged_amount'];

        // Query to get approved amount from discharge_calculations table
        $approvedQuery = "SELECT SUM(gross_amount) AS approved_amount FROM discharge_calculations WHERE org_id = ?";
        $approvedResult = $this->db->query($approvedQuery, array($id));
        $approvedAmount = $approvedResult->row_array()['approved_amount'];

        // Query to get organisation name and id as org_id from organisation table
        $orgQuery = "SELECT organisation_name, id AS org_id FROM organisation WHERE id = ?";
        $orgResult = $this->db->query($orgQuery, array($id));
        $orgData = $orgResult->row_array();

        // Combine all results into one array
        $resultArray = array(
            'released_amount' => number_format($releasedAmount, 2, '.', ','),
            'lodged_amount' => number_format($lodgedAmount, 2, '.', ','),
            'approved_amount' => number_format($approvedAmount, 2, '.', ','),
            'organisation_name' => $orgData['organisation_name'],
            'org_id' => $orgData['org_id']
        );

        return $resultArray;
    }




    public function getDischargePatients()
    {
        $this->db->select('dc.*, p.patient_name, p.patient_unique_id, p.mobileno, p.patient_cnic, s.name as consultant, c.code');
        $this->db->from('discharge_calculations dc');
        $this->db->join('patients p', 'dc.patient_id = p.id', 'left');
        $this->db->join('staff s', 'dc.doctor_id = s.id', 'left');
        $this->db->join('charges c', 'dc.package_id = c.id', 'left');
        $this->db->join('tpa_adjustments ta', 'dc.id = ta.discharge_id', 'left');
        $this->db->where('ta.discharge_id IS NULL');

        $query = $this->db->get();
        // log_message('debug', 'DETAILS: ' . json_encode($query->result_array()));
        return $query->result_array();
    }

    public function getPatients()
    {
        $this->db->select('dc.*, dc.patient_id as p_id, p.patient_name, p.patient_unique_id, p.mobileno, p.patient_cnic, s.name as consultant, c.code');
        $this->db->from('discharge_calculations dc');
        $this->db->join('patients p', 'dc.patient_id = p.id', 'left');
        $this->db->join('staff s', 'dc.doctor_id = s.id', 'left');
        $this->db->join('charges c', 'dc.package_id = c.id', 'left');
        $this->db->join('tpa_adjustments ta', 'dc.id = ta.discharge_id', 'left');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function addTPABill($data)
    {
        $this->db->insert('tpa_adjustments', $data);
    }

    public function addTPAPayment($data)
    {
        // Step 1: Select data from discharge_calculations
        $dischargeData = $this->db->select('patient_id, ipd_id, package_id, org_id')
            ->from('discharge_calculations')
            ->where('id', $data['discharge_id'])
            ->get()
            ->row_array();

        // Step 2: Select last payment record
        $lastPayment = $this->db->select('*')
            ->from('payment')
            ->where('patient_id', $dischargeData['patient_id'])
            ->where('ipd_id', $dischargeData['ipd_id'])
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        // Step 3: Create data array for insertion into payments table

            // Check if balance_amount is not null and not zero
            if ($lastPayment['balance_amount'] !== null && $lastPayment['balance_amount'] !== 0) {
                $balanceAmount = $lastPayment['balance_amount'] - ($data['approved_amount']+$data['deduction_amount']);
            } else {
                // Set balance amount to negative deduction amount if null or zero
                $balanceAmount = -($data['approved_amount']+$data['deduction_amount']);
            }

            // Check if total_amount is not null and not zero
            if ($lastPayment['total_amount'] !== null && $lastPayment['total_amount'] !== 0) {
                $totalAmount = $lastPayment['total_amount'];
            } else {
                // Set total amount to zero if null or zero
                $totalAmount = 0;
            }

        $pay_data = array(
            'patient_id' => $dischargeData['patient_id'],
            'ipd_id' => $dischargeData['ipd_id'],
            'paid_amount' => ($data['approved_amount']+$data['deduction_amount']),
            'balance_amount' => $balanceAmount,
            'total_amount' => $totalAmount,
            'payment_mode' => 'Cheque',
            'date' => date('Y-m-d'), // Current date
            'package_id' => $dischargeData['package_id'],
            'org_id' => $dischargeData['org_id'],
            'cheque_no' => $data['org_pay_id'],
        );

        // Insert data into payments table
        $this->db->insert('payment', $pay_data);

        log_message('debug', 'TPA Payment Data: ' . json_encode($pay_data));
    }


    public function getDeductionsDetail($id) {
        $this->db->select('tpa_adjustments.*, discharge_calculations.*, patients.patient_name, staff.name as consultant, charges.code, organisation.organisation_name, organisation_payments.*');
        $this->db->from('tpa_adjustments');
        $this->db->join('discharge_calculations', 'tpa_adjustments.discharge_id = discharge_calculations.id', 'left');
        $this->db->join('patients', 'discharge_calculations.patient_id = patients.id', 'left');
        $this->db->join('staff', 'discharge_calculations.doctor_id = staff.id', 'left');
        $this->db->join('charges', 'discharge_calculations.package_id = charges.id', 'left');
        $this->db->join('organisation', 'discharge_calculations.org_id = organisation.id', 'left');
        $this->db->join('organisation_payments', 'organisation_payments.id = tpa_adjustments.org_pay_id', 'left');
        $this->db->where('tpa_adjustments.org_pay_id', $id);
        $query = $this->db->get();
        
        // log_message('debug', 'Deductions: ' . json_encode($query->result_array()));
        return $query->result_array();
    }

    public function addCommissionPayment($data)
    {
        $this->db->insert('commission_payments', $data);
    }

    public function getPaidCommisssionDetail($id) {

        $this->db->select('commission_payments.*, staff.name as doctor_name');
        $this->db->from('commission_payments');
        $this->db->join('staff', 'commission_payments.payee_id = staff.id', 'left');
        $this->db->where('commission_payments.payee_id', $id);
        $query = $this->db->get();

        return $query->result_array();

    }

    public function getChequesList() {

        $this->db->select('organisation_payments.*');
        $this->db->from('organisation_payments');
        $query = $this->db->get();

        return $query->result_array();

    }

}
