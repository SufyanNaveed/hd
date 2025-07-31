<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Machine_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function checkTestParameter($bill_no,$parameterName,$value)
    {
        
        $this->db->select('pathology_report.id');
        $this->db->from('pathology_report');
        $this->db->where('pathology_report.bill_no',$bill_no);
        $records=$this->db->get()->result_array();
        $reportIDs = array_column($records, 'id');

        $this->db->select('pathology_parameter.id');
        $this->db->from('pathology_report_parameterdetails');
        $this->db->join('pathology_parameter','pathology_parameter.id=pathology_report_parameterdetails.parameter_id');
        $this->db->where_in('pathology_report_parameterdetails.pathology_report_id',$reportIDs);
        $this->db->where('pathology_parameter.parameter_name',$parameterName);
        $query=$this->db->get();

        if($query->num_rows() > 0){
            $result=$query->row_array();
            $parameterID=$result['id'];

            $machineData=array(
                'pathology_report_value'=>$value
            );
            $this->db->where_in('pathology_report_id',$reportIDs);
            $this->db->where('parameter_id',$parameterID);
            if($this->db->update('pathology_report_parameterdetails',$machineData)){
                return $parameterName;
            }
        }

    }

    

   


}
