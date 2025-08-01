<?php

class Payment_model extends CI_Model
{

    public function addPayment($data)
    {
        $this->db->insert("payment", $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function deleteIpdPatientPayment($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('payment');
    }

    public function deleteOpdPatientPayment($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('opd_payment');
    }
    public function paymentDetails($id, $ipdid = '')
    {
        $query = $this->db->select('payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = payment.patient_id")->where("payment.patient_id", $id)->where("payment.ipd_id", $ipdid)
            ->get("payment");
        return $query->result_array();
    }

    public function getBillDetails($id, $ipdid)
    {
        $query = $this->db->select('patient_charges.package_id, charges.id, charges.charge_type, charges.code, charges.standard_charge, SUM(patient_charges.apply_charge) AS total_charge,
            IFNULL((SELECT SUM(paid_amount) FROM payment WHERE patient_id = ' . $id . ' AND ipd_id = ' . $ipdid . ' AND package_id = patient_charges.package_id), 
            (SELECT SUM(paid_amount) FROM payment WHERE patient_id = ' . $id . ' AND ipd_id = ' . $ipdid . ' AND package_id IS NULL)) AS total_paid')
            ->join('charges', 'charges.id = patient_charges.package_id', 'left')
            ->where('patient_charges.patient_id', $id)
            ->where('patient_charges.ipd_id', $ipdid)
            ->group_by('patient_charges.package_id')
            ->get('patient_charges');

        $billArray = $query->result_array();

        foreach ($billArray as &$charge) {
            $pkgQuery = $this->db->select('SUM(staff_percentage) AS pkg_charges')
                ->where('charges_id', $charge['package_id'])
                ->get('staff_charges_percentage');
            $pkgCharges = $pkgQuery->row_array();
            $charge['pkg_charges'] = $pkgCharges ? $pkgCharges['pkg_charges'] : null;
        
            $expenseQuery = $this->db->select('SUM(patient_charges.apply_charge) AS expense_charges')
                ->where('patient_charges.ipd_id', $ipdid)
                ->where('patient_charges.patient_id', $id)
                ->group_start()
                    ->where('patient_charges.package_id', $charge['package_id'])
                    ->or_where('patient_charges.package_id IS NULL', null, false) // Include null values
                ->group_end()
                ->get('patient_charges');
        
            $expenseData = $expenseQuery->row_array();
            if ($expenseData && isset($expenseData['expense_charges'])) {
                $expense_charges = $expenseData['expense_charges'];
                $standard_charge = $charge['standard_charge'];
            
                if ($expense_charges > $standard_charge) {
                    $charge['total_expense'] = $expense_charges - $standard_charge;
                } else {
                    $charge['total_expense'] = null;
                }
        
                // Calculate total expense combined
                $charge['total_expense_combined'] = $charge['total_expense'] + $charge['pkg_charges'];
            } else {
                $charge['total_expense'] = null;
                $charge['total_expense_combined'] = null;
            }
        }
        
        
        log_message('debug', 'BILL DETAILS: ' . json_encode($billArray));

        return $billArray;
    }


    public function opdpaymentDetails($id, $ipdid = '')
    {
        $query = $this->db->select('opd_payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = opd_payment.patient_id")->where("opd_payment.patient_id", $id)->where("opd_payment.opd_id", $ipdid)
            ->get("opd_payment");
        return $query->result_array();
    }

    public function opdPaymentDetailspat($id)
    {
        $query = $this->db->select('opd_payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = opd_payment.patient_id")->where("opd_payment.patient_id", $id)
            ->get("opd_payment");
        return $query->result_array();
    }

    public function paymentByID($id)
    {
        $query = $this->db->select('payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = payment.patient_id")->where("payment.id", $id)
            ->get("payment");
        return $query->row();
    }

    public function opdpaymentByID($id)
    {
        $query = $this->db->select('opd_payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = opd_payment.patient_id")->where("opd_payment.id", $id)
            ->get("opd_payment");
        return $query->row();
    }

    public function getBalanceTotal($id, $ipdid = '')
    {
        $query = $this->db->select("IFNULL(sum(balance_amount),'0') as balance_amount")->where("payment.patient_id", $id)->where("payment.ipd_id", $ipdid)->get("payment");
        return $query->row_array();
    }

    public function getOPDBalanceTotal($id)
    {
        $query = $this->db->select("IFNULL(sum(balance_amount),'0') as balance_amount")->where("opd_payment.patient_id", $id)->get("opd_payment");
        return $query->row_array();
    }

    public function getPaidTotal($id, $ipdid = '')
    {
        $query = $this->db->select("IFNULL(sum(paid_amount), '0') as paid_amount")->where("payment.patient_id", $id)->where("payment.ipd_id", $ipdid)->get("payment");
        return $query->row_array();
    }

     public function getopdbilling($id, $opdid = '')
    {
        $query = $this->db->select("IFNULL(sum(net_amount), '0') as billing_amount")->where("opd_billing.patient_id", $id)->where("opd_billing.opd_id", $opdid)->get("opd_billing");
        return $query->row_array();
    }

     public function getambulancepaidtotal($id, $ipdid = '')
    {
        $query = $this->db->select("IFNULL(sum(paid), '0') as paid_amount")->where("ambulance_billing.ambulancecall_id", $id)->get("ambulance_billing");
        return $query->row_array();
    }

      public function getotpaidtotal($id)
    {
        $query = $this->db->select("IFNULL(sum(paid), '0') as paid_amount")->where("operation_theatre_billing.operation_id", $id)->get("operation_theatre_billing");
        return $query->row_array();
    }

     public function getbloodissuepaidtotal($id, $ipdid = '')
    {
        $query = $this->db->select("IFNULL(sum(paid), '0') as paid_amount")->where("blood_issue_billing.bloodissue_id", $id)->get("blood_issue_billing");
        return $query->row_array();
    }

    public function getOPDPaidTotal($id, $visitid)
    {
        $query = $this->db->select("IFNULL(sum(paid_amount), '0') as paid_amount")->where("opd_payment.patient_id", $id)->where("opd_payment.opd_id", $visitid)->get("opd_payment");
        return $query->row_array();
    }

    public function getOPDbillpaid($id, $visitid)
    {
        $query = $this->db->select("IFNULL(sum(net_amount), '0') as billpaid_amount")->where("opd_billing.patient_id", $id)->where("opd_billing.opd_id", $visitid)->get("opd_billing");
        return $query->row_array();
    }

    public function getOPDPaidTotalPat($id)
    {
        $query = $this->db->select("IFNULL(sum(paid_amount), '0') as paid_amount")->where("opd_payment.patient_id", $id)->get("opd_payment");
        return $query->row_array();
    }

    public function getChargeTotal($id, $ipdid)
    {
        $query = $this->db->select("IFNULL(sum(apply_charge), '0') as apply_charge")
            ->join('patients', 'patient_charges.patient_id = patients.id', 'inner')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
            ->join('organisations_charges', 'patient_charges.org_charge_id = organisations_charges.id', 'left')
            ->where('patient_charges.patient_id', $id)
            ->where('patient_charges.ipd_id', $ipdid)
            ->get('patient_charges');
        return $query->row_array();
    }

    public function add_bill($data)
    {
        $this->db->insert("ipd_billing", $data);
    }

    public function add_opdbill($data)
    {
        $this->db->insert("opd_billing", $data);
    }

    public function revertBill($patient_id, $bill_id)
    {
        $this->db->where("id", $bill_id)->delete("ipd_billing");
    }

    public function valid_amount($amount)
    {
        if ($amount <= 0) {
            $this->form_validation->set_message('check_exists', 'The payment amount must be greater than 0');
            return false;
        } else {
            return true;
        }
    }

    public function addOPDPayment($data)
    {

        if (isset($data["id"])) {

            $this->db->where("id", $data["id"])->update("opd_payment", $data);

        } else {
            $this->db->insert("opd_payment", $data);
            $insert_id = $this->db->insert_id();
            return $insert_id;

        }

    }

    public function amount_validation()
    {
        $amount = $this->input->post('amount');
        if (!empty($amount)) {
           if($amount == 0.0){
                $this->form_validation->set_message('check_validation', $this->lang->line('enter').' '.$this->lang->line('valid').' '.$this->lang->line('amount'));
                return false; 
           }else{
                return true; 
           }
        } else {
            $this->form_validation->set_message('check_validation', $this->lang->line('amount').' '.$this->lang->line('field_is_required'));
            return false;
        }
    }

    public function dischargeCalculation($data)
    {
        $this->db->insert("discharge_calculations", $data);
    }

    public function removeDischarge($p_id)
    {
        $this->db->where('patient_id', $p_id);
        $this->db->delete('discharge_calculations');
    }


}
