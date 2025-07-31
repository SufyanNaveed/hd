<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Common_model extends CI_Model
{

    public function getRecord($id = null,$table,$where='')
    {
        $this->db->select()->from($table);
        if ($where != null) {
            $this->db->where($where);
        }
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null or $where!='') {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getmultipleRecords($table,$where='')
    {
        $this->db->select()->from($table);
        if ($where != null) {
            $this->db->where($where);
        } 
        $query = $this->db->get();
        return $query->result_array(); 
    }

    public function fetch_symtopms($table,$symptopms)
    {
        $this->db->select()->from($table);
        $this->db->where_in('id',$symptopms);
        $query = $this->db->get();
        return $query->result_array();

    }
    public function fetch_precaution($table,$precaution)
    {
        $this->db->select()->from($table);
        $this->db->where_in('id',$precaution);
        $query = $this->db->get();
        return $query->result_array();

    }
    public function fetch_diagnosis($table,$diagnosis)
    {
        $this->db->select()->from($table);
        $this->db->where_in('id',$diagnosis);
        $query = $this->db->get();
        return $query->result_array();

    }
    public function getLabInvestigations($id)
    {
        //echo "yesss" ;exit;
        $query = $this->db->select('pathology_report.reporting_date as report_date, pathology_report.id,
            pathology_report.pathology_id, pathology_report.patient_id, pathology_report.pathology_report as document, pathology_report.description,
            pathology_report.apply_charge, pathology.test_name as report_type, staff.name, staff.surname, pathology_report_parameterdetails.pathology_report_value')
            ->join('pathology', 'pathology.id = pathology_report.pathology_id', "inner")
            ->join('patients', 'patients.id = pathology_report.patient_id', "inner")
            ->join('staff', 'staff.id = pathology_report.consultant_doctor', "left")
            ->join('pathology_report_parameterdetails', 'pathology_report_parameterdetails.pathology_report_id = pathology_report.id', "left")
            ->where_in("pathology_report.id", $id)
            ->group_by('pathology_report.id')
            ->get("pathology_report");
        $result = $query->result_array();
        //print_r($result);exit;
        return $result;
    }
    public function getSingleLabInvestigations($id)
    {
        $query = $this->db->select('pathology_report.reporting_date as report_date, pathology_report.id,
            pathology_report.pathology_id, pathology_report.patient_id, pathology_report.pathology_report as document, pathology_report.description,
            pathology_report.apply_charge, pathology.test_name as report_type, staff.name, staff.surname, pathology_report_parameterdetails.pathology_report_value')
            ->join('pathology', 'pathology.id = pathology_report.pathology_id', "inner")
            ->join('patients', 'patients.id = pathology_report.patient_id', "inner")
            ->join('staff', 'staff.id = pathology_report.consultant_doctor', "left")
            ->join('pathology_report_parameterdetails', 'pathology_report_parameterdetails.pathology_report_id = pathology_report.id', "left")
            ->where("pathology_report.id", $id)
            ->get("pathology_report");
        $result = $query->row_array();
        return $result;
    }

    public function saveLog($module_name,$module_type,$comments,$invoice_no=null)
    {
        $data=array(
            'module_name'=>$module_name,
            'module_type'=>$module_type,
            'comments'=>$comments,
            'invoice_no'=>$invoice_no,
            'action_by'=>$this->session->userdata['hospitaladmin']['id'],
        );
        $this->db->insert('activity_log',$data);
        return $this->db->insert_id();
    }

    public function getRow($id)
    {
        $this->db->select('patient_name,patient_unique_id');
        $this->db->from('patients');
        $this->db->where('id',$id);
        return $this->db->get()->row_array();
    }
    public function getPatientLogs()
    {
        $this->db->select('*');
        $this->db->from('activity_log');
        $this->db->order_by('id',"DESC");
        return $this->db->get()->result_array();

    }
    public function getRowOpd($id)
    {
        $this->db->select('opd_no,appointment_date');
        $this->db->from('opd_details');
        $this->db->where('id',$id);
        return $this->db->get()->row_array();

    }

    public function getSingleRow($column,$id,$select,$table)
    {
        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($column,$id);
        return $this->db->get()->row_array();

    }

    public function getStaffRow($id)
    {
        $this->db->select('name');
        $this->db->from('staff');
        $this->db->where('id',$id);
        return $this->db->get()->row_array();
    }

    public function getChargesfRow($id)
    {
        $this->db->select('charge_type,charge_category,code');
        $this->db->from('charges');
        $this->db->where('id',$id);
        return $this->db->get()->row_array();
    }

}