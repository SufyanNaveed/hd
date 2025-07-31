<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bio_model extends CI_Model
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
    public function checkBio($bio_id)
    {
        $this->db->select('id');
        $this->db->from('user_bio');
        $this->db->where('bio_id',$bio_id);
        $query=$this->db->get();
        if($query->num_rows() > 0){
            return $bio_id;
        }else{
            $bioData=array(
                'bio_id'=>$bio_id
            );
            if($this->db->insert('user_bio',$bioData)){
                return $this->db->insert_id();
            }
        }

    }

    public function savedTimeStatus($bio_id,$push_time)
    {
        $date = new DateTime("now");
        $curr_date = date('Y-m-d ');
        
        $this->db->select('time_status');
        $this->db->from('staff_bio_attendance'); 
        $this->db->where('bio_id',$bio_id);
        $this->db->where('DATE(push_time)',$curr_date);//use date function
        $query=$this->db->get();
        $result = $query->row_array();
        if($result){
            $return['success']=0;
            if($result['time_status']=='in'){$time_status='out';}
            if($result['time_status']=='out'){$time_status='in';}
            $bioDataInfo=array(
                'bio_id'=>$bio_id,
                'time_status'=>$time_status,
                'push_time'=>$push_time,

            );
            if($this->db->insert('staff_bio_attendance',$bioDataInfo)){
                $return['success']=1;
                $return['message']="staff attendance saved successfully";
            }
            return $return;exit;
        }else{
            $return['success']=0;
            $bioData=array(
                'bio_id'=>$bio_id,
                'time_status'=>'in',
                'push_time'=>$push_time,

            );
            if($this->db->insert('staff_bio_attendance',$bioData)){
                $return['success']=1;
                $return['message']="staff attendance saved successfully";
                return $return;exit;
            }
        }

    }

    public function getBioIDs()
    {
        $query = $this->db->select("user_bio.*")->get("user_bio");
        return $query->result_array();
    }
    public function getReport($select = '', $join = array(), $table_name, $additional_where = array())
    {
        if (empty($additional_where)) {
            $additional_where = array(" 1 = 1");
        }

        if (!empty($join)) {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("and ", $additional_where);
        } else {
            $query = "select " . $select . " from " . $table_name . " where" . implode("and ", $additional_where);
        }

        $res = $this->db->query($query);
        return $res->result_array();
    }
   
    public function searchReport($select, $join = array(), $table_name, $search_type, $search_table, $search_column, $additional_where = array(), $where = array())
    {
        if ($search_type == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                echo form_error();
            } else {
                $from_date = $this->input->post('date_from');
                $to_date   = $this->input->post('date_to');
                $date_from = date("Y-m-d", $this->customlib->datetostrtotime($from_date));
                $date_to   = date("Y-m-d 23:59:59.993", $this->customlib->datetostrtotime($to_date));
                $where     = array($search_table . "." . $search_column . " >=  '" . $date_from . "' ", $search_table . "." . $search_column . " <=  '" . $date_to . "'");
            }
        } else if ($search_type == 'today') {
            $today        = strtotime('today 00:00:00');
            $first_date   = date('Y-m-d ', $today);
            $search_today = 'date(' . $search_table . '.' . $search_column . ')';
            $where        = array($search_today . " = '" . $first_date . "'");
        } else if ($search_type == 'this_week') {
            $this_week_start = strtotime('-1 week monday 00:00:00');
            $this_week_end   = strtotime('sunday 23:59:59');
            $first_date      = date('Y-m-d H:i:s', $this_week_start);
            $last_date       = date('Y-m-d H:i:s', $this_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_week') {
            $last_week_start = strtotime('-2 week monday 00:00:00');
            $last_week_end   = strtotime('-1 week sunday 23:59:59');
            $first_date      = date('Y-m-d H:i:s', $last_week_start);
            $last_date       = date('Y-m-d H:i:s', $last_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'this_month') {
            $first_date = date('Y-m-01');
            $last_date  = date('Y-m-t 23:59:59.993');
            $where      = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_month') {
            $month      = date("m", strtotime("-1 month"));
            $first_date = date('Y-' . $month . '-01');
            $last_date  = date('Y-' . $month . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_3_month') {
            $month      = date("m", strtotime("-2 month"));
            $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_6_month') {
            $month      = date("m", strtotime("-5 month"));
            $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_12_month') {
            $first_date = date('Y-m' . '-01', strtotime("-11 month"));
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_year') {
            $search_year = date('Y', strtotime("-1 year"));
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'this_year') {
            $search_year = date('Y');
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'all time') {
            $where = array();
        }
        if (empty($additional_where)) {
            $additional_where = array('1 = 1');
        }
        if (!empty($where)) {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " order by " . $search_table . "." . $search_column;
        } else {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " order by " . $search_table . "." . $search_column;
        }
        $res = $this->db->query($query);
        return $res->result_array();
    }

    

}
