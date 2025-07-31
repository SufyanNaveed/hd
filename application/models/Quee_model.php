<?php

class Quee_model extends CI_Model
{
    public $column_order            = array('patients.patient_name', 'patients.patient_unique_id', 'patients.guardian_name', 'patients.gender', 'patients.mobileno', 'staff.name', 'opd_details.appointment_date', 'opd_details.patient_id'); //set column field database for datatable orderable
    public $column_search           = array('patient_name', 'patient_unique_id', 'guardian_name', 'mobileno');
    public $columncredential_order  = array('patient_name', 'patient_unique_id', 'username', 'password'); //set column field database for datatable orderable
    public $columncredential_search = array('patient_name', 'patient_unique_id', 'username', 'password');
    public $columnipd_order         = array('patients.patient_name', 'ipd_details.ipd_no', 'patients.patient_unique_id', 'patients.gender', 'patients.mobileno', 'staff.name', 'bed.name', 'charges', 'payment', 'amountdue', 'ipd_details.credit_limit'); //set column field database for datatable orderable
    public $columnipd_search        = array('patients.patient_name', 'ipd_details.ipd_no', 'patients.patient_unique_id', 'patients.gender', 'patients.mobileno', 'staff.name', 'bed.name', 'ipd_details.credit_limit');

    public $columndis_order         = array('patients.patient_name', 'patients.patient_unique_id', 'patients.gender', 'patients.mobileno', 'staff.name', 'ipd_details.date','ipd_details.date','charges','other_charge','tax','discount','net_amount'); //set column field database for datatable orderable
    
    public $columndis_search        = array('patients.patient_name', 'patients.patient_unique_id','patients.mobileno', 'staff.name', 'ipd_details.date','ipd_details.date');

    public $columnpatient_order     = array('patient_unique_id', 'patient_name', 'age', 'gender', 'mobileno', 'guardian_name', 'address', 'action'); //set column field database for datatable orderable
    public $columnpatient_search    = array('patient_unique_id', 'patient_name', 'age', 'gender', 'mobileno', 'guardian_name', 'address');

    public function search_datatable()
    {
        $date=date('Y-m-d');
        //echo "date".$date;exit;
        $setting            = $this->setting_model->get();
        $opd_month          = $setting[0]['opd_record_month'];
        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->db->where('opd_details.cons_doctor', $userdata['id']);
            }
        }
        $this->db->select('opd_details.*,patients.id as pid,patients.patient_name,patients.patient_cnic,patients.patient_unique_id,patients.guardian_name,patients.gender,patients.mobileno,patients.is_ipd,staff.name,staff.surname')->from('opd_details');
        $this->db->join('patients', "patients.id=opd_details.patient_id", "LEFT");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "LEFT");
        $this->db->where('patients.is_active', 'yes');
        $this->db->where('DATE(opd_details.appointment_date)', $date);
        if (!isset($_POST['order'])) {
            // $this->db->order_by('max(opd_details.appointment_date)', 'desc');
            $this->db->order_by('opd_details.id', 'desc');
        }
        $this->db->group_by('opd_details.patient_id');
        if (!empty($_POST['search']['value'])) {
            // if there is a search parameter
            $counter = true;
            $this->db->group_start();
            foreach ($this->column_search as $colomn_key => $colomn_value) {
                if ($counter) {
                    $this->db->like($colomn_value, $_POST['search']['value']);
                    $counter = false;
                }
                $this->db->or_like($colomn_value, $_POST['search']['value']);
            }
            $this->db->group_end();

        }
        $this->db->limit($_POST['length'], $_POST['start']);
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }

        $query  = $this->db->get();
        $result = $query->result();
        $i      = 0;
        foreach ($result as $key => $value) {
            $generated_by = $value->generated_by;
            $staff_query  = $this->db->select("staff.name,staff.surname")
                ->where("staff.id", $generated_by)
                ->get("staff");
            $staff_result                   = $staff_query->row_array();
            $result[$key]->generated_byname = $staff_result["name"] . $staff_result["surname"];
            $patient_id                     = $value->pid;
            // $total_visit                    = $this->totalVisit($patient_id);
            // $last_visit                     = $this->lastVisit($patient_id);
            // $opdno                          = $this->lastVisitopdno($patient_id);
            // $consultant                     = $this->getConsultant($patient_id, $opd_month);
            // $result[$i]->total_visit        = $total_visit["total_visit"];
            $result[$i]->appointment_date         = $value->appointment_date;
            $result[$i]->visit_status         = $value->visit_status;
            $result[$i]->opdno              = $opdno['opdno'];
            $i++;
        }

        return $result;
    }

    public function search_datatable_count()
    {
        $date=date('Y-m-d');
        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->db->where('opd_details.cons_doctor', $userdata['id']);
            }
        }
        $this->db->select('opd_details.*,patients.id as pid,patients.patient_name,patients.patient_cnic,patients.patient_unique_id,patients.guardian_name,patients.gender,patients.mobileno,patients.is_ipd,staff.name,staff.surname');
        $this->db->join('patients', "patients.id=opd_details.patient_id", "LEFT");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "LEFT");
        $this->db->where('patients.is_active', 'yes');
        $this->db->where('DATE(opd_details.appointment_date)', $date);
        $this->db->order_by('max(opd_details.appointment_date)', 'desc');
        $this->db->group_by('opd_details.patient_id');
        if (!empty($_POST['search']['value'])) {
            // if there is a search parameter
            $counter = true;
            $this->db->group_start();
            foreach ($this->column_search as $colomn_key => $colomn_value) {
                if ($counter) {
                    $this->db->like($colomn_value, $_POST['search']['value']);
                    $counter = false;
                }
                $this->db->or_like($colomn_value, $_POST['search']['value']);
            }
            $this->db->group_end();

        }
        $query        = $this->db->from('opd_details');
        $total_result = $query->count_all_results();
        return $total_result;
    }
}    