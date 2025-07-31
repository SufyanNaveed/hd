<?php

class Report_model extends CI_Model
{

    public function getReport($select = '', $join = array(), $table_name, $additional_where = array())
    {
        if (empty($additional_where)) {
            $additional_where = array("1 = 1");
        }

        if (!empty($join)) {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("and ", $additional_where);
        } else {
            $query = "select " . $select . " from " . $table_name . " where" . implode("and ", $additional_where);
        }

        $res = $this->db->query($query);
        return $res->result_array();
    }
    public function getReportMedicine($select = '', $join = array(), $table_name, $additional_where = array())
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

    public function getReportbalance($select = '', $join = array(), $table_name, $additional_where = array(), $group_by)
    {
        if (empty($additional_where)) {
            $additional_where = array("1 = 1");
        }

        if (!empty($join)) {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("and ", $additional_where) . "group by" . ' ' . $group_by;
        } else {
            $query = "select " . $select . " from " . $table_name . " where" . implode("and ", $additional_where) . "group by" . ' ' . $group_by;
        }

        $res = $this->db->query($query);
        return $res->result_array();
    }

    public function searchReport($select, $join = array(), $table_name, $search_type, $search_table, $search_column, $additional_where = array(), $where = array(), $where_in = array(), $group_by = '')
    {
        //echo $search_type;exit;
        if ($search_type == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                echo form_error();
            } else {
                $data['from_date'] = $from_date = $this->input->post('date_from');
                $data['to_date'] = $to_date   = $this->input->post('date_to');
                $date_from = date("Y-m-d", $this->customlib->datetostrtotime($from_date));
                $date_to   = date("Y-m-d 23:59:59.993", $this->customlib->datetostrtotime($to_date));
                $where     = array($search_table . "." . $search_column . " >=  '" . $date_from . "' ", $search_table . "." . $search_column . " <=  '" . $date_to . "'");
            }
        } else if ($search_type == 'today') {
            $today        = strtotime('today 00:00:00');
            $data['from_date'] = $first_date   = date('Y-m-d ', $today);
            $search_today = 'date(' . $search_table . '.' . $search_column . ')';
            $where        = array($search_today . " = '" . $first_date . "'");
        } else if ($search_type == 'this_week') {
            $this_week_start = strtotime('-1 week monday 00:00:00');
            $this_week_end   = strtotime('sunday 23:59:59');
            $data['from_date'] = $first_date      = date('Y-m-d H:i:s', $this_week_start);
            $data['to_date'] = $last_date       = date('Y-m-d H:i:s', $this_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_week') {
            $last_week_start = strtotime('-2 week monday 00:00:00');
            $last_week_end   = strtotime('-1 week sunday 23:59:59');
            $data['from_date'] = $first_date      = date('Y-m-d H:i:s', $last_week_start);
            $data['to_date'] = $last_date       = date('Y-m-d H:i:s', $last_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'this_month') {
            $data['from_date'] = $first_date = date('Y-m-01');
            $data['to_date'] = $last_date  = date('Y-m-t 23:59:59.993');
            $where      = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_month') {
            $month      = date("m", strtotime("-1 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $data['to_date'] = $last_date  = date('Y-' . $month . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_3_month') {
            $month      = date("m", strtotime("-2 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_6_month') {
            $month      = date("m", strtotime("-5 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_12_month') {
            $data['from_date'] = $first_date = date('Y-m' . '-01', strtotime("-11 month"));
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_year') {
            $data['from_date'] = $search_year = date('Y', strtotime("-1 year"));
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'this_year') {
            $data['from_date'] = $search_year = date('Y');
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'all_time') {
            $data['from_date'] = 'all_time';
            $where = array();
        }
        if (empty($additional_where)) {
            $additional_where = array('1 = 1');
        }
        if (!empty($where) && empty($where_in)) {
            if (!empty($group_by)) {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " group by " . $group_by . " order by " . $search_table . "." . $search_column;
            } else {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " order by " . $search_table . "." . $search_column;
            }
        } elseif (!empty($where_in)) {
            $where_in = implode(', ', $where_in);
            if (empty($where)) {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " AND opd_details.department IN (" . $where_in . ")" . " group by " . $group_by . " order by " . $search_table . "." . $search_column;
            } else {
                if (!empty($group_by)) {
                    $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode("  and ", $additional_where) . " AND opd_details.department IN (" . $where_in . ")" . " order by " . $search_table . "." . $search_column;
                } else {
                    $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode("  and ", $additional_where) . " AND opd_details.department IN (" . $where_in . ")" . " group by " . $group_by . " order by " . $search_table . "." . $search_column;
                }
            }
        } else {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " group by" . $group_by . " order by " . $search_table . "." . $search_column;
        }
        $res = $this->db->query($query);
        //echo $this->db->last_query();exit;
        $allData['main_data'] = $res->result_array();
        // echo "<pre>";print_r($allData['main_data']);exit;
        $allData['fillter_data'] = $data;
        return $allData;
    }

    public function searchReportbalance($select, $join = array(), $table_name, $search_type, $search_table, $search_column, $additional_where = array(), $group_by)
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
            $today      = strtotime('today 00:00:00');
            $first_date = date('Y-m-d H:i:s', $today);
            $where      = array($search_table . "." . $search_column . " = '" . $first_date . "'");
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
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " group by " . $group_by . " order by " . $search_table . "." . $search_column;
        } else {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " group by " . $group_by . " order by " . $search_table . "." . $search_column;
        }

        $res = $this->db->query($query);

        return $res->result_array();
    }

    public function searchReportexpiry($select, $join = array(), $table_name, $search_type, $search_table, $search_column, $additional_where = array())
    {
        $this_mnt = $first_date = date('M/Y');
        for ($i = 1; $i <= 11; $i++) {
            $last_year[] = $search_table . "." . $search_column . "='" . date('M/Y', strtotime("-" . $i . "  month")) . "'";
            $this_year[] = $search_table . "." . $search_column . "='" . date('M/Y', strtotime("+" . $i . "  month")) . "'";
        }

        if ($search_type == 'this_month') {
            $where = array($search_table . "." . $search_column . " = '" . $this_mnt . "'", $search_table . "." . $search_column . " = '" . $this_mnt . "'");
        } else if ($search_type == 'last_month') {
            $where = array($last_year[0]);
        } else if ($search_type == 'last_3_month') {
            $where = array($last_year[0], $last_year[1], $last_year[2]);
        } else if ($search_type == 'last_6_month') {
            $where = array($last_year[0], $last_year[1], $last_year[3], $last_year[4], $last_year[5], $last_year[6]);
        } else if ($search_type == 'last_year') {
            $where = $last_year;
        } else if ($search_type == 'this_year') {
            $where = $this_year;
        } else if ($search_type == 'all time') {
            $where = array();
        }
        if (empty($additional_where)) {
            $additional_where = array('1 = 1');
        }

        if (!empty($where)) {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" or ", $where) . " order by " . $search_table . "." . $search_column;
        } else {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " order by " . $search_table . "." . $search_column;
        }

        $res = $this->db->query($query);
        return $res->result_array();
    }

    public function transactionReport($select = '', $join = array(), $table_name, $additional_where = array())
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
    public function searchReportMedicine($select, $join = array(), $table_name, $search_type, $search_table, $search_column, $additional_where = array(), $where = array())
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
        //echo $this->db->last_query();exit;
        return $res->result_array();
    }

    public function getKopName()
    {
        $this->db->select('staff.id,staff.name as kop_name');
        $this->db->from('opd_details');
        $this->db->join('staff', 'staff.id=opd_details.generated_by');
        $this->db->group_by('opd_details.generated_by');
        return $this->db->get()->result_array();
    }

    public function searchReportKPO($select, $join = array(), $table_name, $search_type, $search_table, $search_column, $additional_where = array(), $where = array(), $where_in = array(), $kpo_test = null)
    {
        //echo "<pre>";print_r($additional_where);exit;
        if ($search_type == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                echo form_error();
            } else {

                $data['from_date'] = $from_date = $this->input->post('date_from');
                $data['to_date'] = $to_date   = $this->input->post('date_to');
                $date_from = date("Y-m-d", $this->customlib->datetostrtotime($from_date));
                $date_to   = date("Y-m-d 23:59:59.993", $this->customlib->datetostrtotime($to_date));
                $where     = array($search_table . "." . $search_column . " >=  '" . $date_from . "' ", $search_table . "." . $search_column . " <=  '" . $date_to . "'");
            }
        } else if ($search_type == 'today') {
            $today        = strtotime('today 00:00:00');
            $data['from_date'] = $first_date   = date('Y-m-d ', $today);
            $search_today = 'date(' . $search_table . '.' . $search_column . ')';
            $where        = array($search_today . " = '" . $first_date . "'");
        } else if ($search_type == 'this_week') {
            $this_week_start = strtotime('-1 week monday 00:00:00');
            $this_week_end   = strtotime('sunday 23:59:59');
            $data['from_date'] = $first_date      = date('Y-m-d H:i:s', $this_week_start);
            $data['to_date'] = $last_date       = date('Y-m-d H:i:s', $this_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_week') {
            $last_week_start = strtotime('-2 week monday 00:00:00');
            $last_week_end   = strtotime('-1 week sunday 23:59:59');
            $data['from_date'] = $first_date      = date('Y-m-d H:i:s', $last_week_start);
            $data['to_date'] = $last_date       = date('Y-m-d H:i:s', $last_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'this_month') {
            $data['from_date'] = $first_date = date('Y-m-01');
            $data['to_date'] = $last_date  = date('Y-m-t 23:59:59.993');
            $where      = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_month') {
            $month      = date("m", strtotime("-1 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $data['to_date'] = $last_date  = date('Y-' . $month . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_3_month') {
            $month      = date("m", strtotime("-2 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_6_month') {
            $month      = date("m", strtotime("-5 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_12_month') {
            $data['from_date'] = $first_date = date('Y-m' . '-01', strtotime("-11 month"));
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_year') {
            $data['from_date'] = $search_year = date('Y', strtotime("-1 year"));
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'this_year') {
            $data['from_date'] = $search_year = date('Y');
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'all_time') {
            $data['from_date'] = 'all_time';
            $where = array();
        }
        if (empty($additional_where)) {
            $additional_where = array('1 = 1');
            $additional_where_checker = array('1 = 1');
        }
        if (!empty($where) && empty($where_in)) {
            if (isset($additional_where['opd_details.generated_by']) && is_array($additional_where['opd_details.generated_by'])) {
                $where_new = implode(', ', $additional_where['opd_details.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND opd_details.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where['ipd_details.generated_by']) && is_array($additional_where['ipd_details.generated_by'])) {
                $where_new = implode(', ', $additional_where['ipd_details.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND ipd_details.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where['pharmacy_bill_basic.generated_by']) && is_array($additional_where['pharmacy_bill_basic.generated_by'])) {
                $where_new = implode(', ', $additional_where['pharmacy_bill_basic.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND pharmacy_bill_basic.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where['pathology_report.generated_by']) && is_array($additional_where['pathology_report.generated_by'])) {
                $where_new = implode(', ', $additional_where['pathology_report.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND pathology_report.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where['radiology_report.generated_by']) && is_array($additional_where['radiology_report.generated_by'])) {
                $where_new = implode(', ', $additional_where['radiology_report.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND radiology_report.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where['operation_theatre.generated_by']) && is_array($additional_where['operation_theatre.generated_by'])) {
                $where_new = implode(', ', $additional_where['operation_theatre.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND operation_theatre.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where['blood_issue.generated_by']) && is_array($additional_where['blood_issue.generated_by'])) {
                $where_new = implode(', ', $additional_where['blood_issue.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND blood_issue.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where['ambulance_call.generated_by']) && is_array($additional_where['ambulance_call.generated_by'])) {
                $where_new = implode(', ', $additional_where['ambulance_call.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND ambulance_call.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where['ipd_billing.generated_by']) && is_array($additional_where['ipd_billing.generated_by'])) {
                $where_new = implode(', ', $additional_where['ipd_billing.generated_by']);
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " AND ipd_billing.generated_by IN (" . $where_new . ")" . " order by " . $search_table . "." . $search_column;
            }
            if (isset($additional_where_checker)) {
                //echo "he;ll";exit;
                //$query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " order by " . $search_table . "." . $search_column;
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " order by " . $search_table . "." . $search_column;
            }
            if ($kpo_test == null) {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " order by " . $search_table . "." . $search_column;
            }
        } elseif (!empty($where_in)) {
            $where_in = implode(', ', $where_in);
            if (empty($where)) {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " AND opd_details.department IN (" . $where_in . ")" . " order by " . $search_table . "." . $search_column;
            } else {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode("  and ", $additional_where) . " AND opd_details.department IN (" . $where_in . ")" . " order by " . $search_table . "." . $search_column;
            }
        } else {
            //echo "na kr";exit;
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where_in " . implode("  and ", $additional_where) . " order by " . $search_table . "." . $search_column;
        }
        if (isset($query)) {

            $res = $this->db->query($query);
            //echo $this->db->last_query();exit;
            $allData['main_data'] = $res->result_array();
            // echo "<pre>";print_r($allData['main_data']);exit;
            $allData['fillter_data'] = $data;
            return $allData;
        } else {
            $allData['fillter_data'] = $data;
            return $allData;
        }
    }

    public function searchReportStoreConsumption($select, $join = array(), $table_name, $search_type, $search_table, $search_column, $additional_where = array(), $where = array(), $where_in = array(), $group_by = '')
    {
        //echo $search_type;exit;
        if ($search_type == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                echo form_error();
            } else {
                $data['from_date'] = $from_date = $this->input->post('date_from');
                $data['to_date'] = $to_date   = $this->input->post('date_to');
                $date_from = date("Y-m-d", $this->customlib->datetostrtotime($from_date));
                $date_to   = date("Y-m-d 23:59:59.993", $this->customlib->datetostrtotime($to_date));
                $where     = array($search_table . "." . $search_column . " >=  '" . $date_from . "' ", $search_table . "." . $search_column . " <=  '" . $date_to . "'");
            }
        } else if ($search_type == 'today') {
            $today        = strtotime('today 00:00:00');
            $data['from_date'] = $first_date   = date('Y-m-d ', $today);
            $search_today = 'date(' . $search_table . '.' . $search_column . ')';
            $where        = array($search_today . " = '" . $first_date . "'");
        } else if ($search_type == 'this_week') {
            $this_week_start = strtotime('-1 week monday 00:00:00');
            $this_week_end   = strtotime('sunday 23:59:59');
            $data['from_date'] = $first_date      = date('Y-m-d H:i:s', $this_week_start);
            $data['to_date'] = $last_date       = date('Y-m-d H:i:s', $this_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_week') {
            $last_week_start = strtotime('-2 week monday 00:00:00');
            $last_week_end   = strtotime('-1 week sunday 23:59:59');
            $data['from_date'] = $first_date      = date('Y-m-d H:i:s', $last_week_start);
            $data['to_date'] = $last_date       = date('Y-m-d H:i:s', $last_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'this_month') {
            $data['from_date'] = $first_date = date('Y-m-01');
            $data['to_date'] = $last_date  = date('Y-m-t 23:59:59.993');
            $where      = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_month') {
            $month      = date("m", strtotime("-1 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $data['to_date'] = $last_date  = date('Y-' . $month . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_3_month') {
            $month      = date("m", strtotime("-2 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_6_month') {
            $month      = date("m", strtotime("-5 month"));
            $data['from_date'] = $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_12_month') {
            $data['from_date'] = $first_date = date('Y-m' . '-01', strtotime("-11 month"));
            $firstday   = date('Y-' . 'm' . '-01');
            $data['to_date'] = $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_year') {
            $data['from_date'] = $search_year = date('Y', strtotime("-1 year"));
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'this_year') {
            $data['from_date'] = $search_year = date('Y');
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'all_time') {
            $data['from_date'] = 'all_time';
            $where = array();
        }
        if (empty($additional_where)) {
            $additional_where = array('1 = 1');
        }
        if (!empty($where) && empty($where_in)) {
            if (!empty($group_by)) {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " group by " . $group_by . " order by " . $search_table . "." . $search_column;
            } else {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " order by " . $search_table . "." . $search_column;
            }
        } elseif (!empty($where_in)) {
            $where_in = implode(', ', $where_in);
            if (empty($where)) {
                $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " group by " . $group_by . " order by " . $search_table . "." . $search_column;
            } else {
                if (!empty($group_by)) {
                    $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode("  and ", $additional_where) . " group by " . $group_by . " order by " . $search_table . "." . $search_column;
                } else {
                    $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode("  and ", $additional_where) . " order by " . $search_table . "." . $search_column;
                }
            }
        } else {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " order by " . $search_table . "." . $search_column;
        }
        // echo "<pre>";print_r($query);exit;
        $res = $this->db->query($query);
        $allData['main_data'] = $res->result_array();
        //$allData['fillter_data']=$data;
        return $allData;
    }


    public function searchReportStoreConsumptionWithHaving(
        $select,
        $join = array(),
        $table_name,
        $search_type,
        $search_table,
        $search_column,
        $additional_where = array(),
        $where = array(),
        $where_in = array(),
        $group_by = '',
        $having = ''
    ) {
        if ($search_type == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                echo form_error();
            } else {
                $from_date = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('date_from')));
                $to_date = date("Y-m-d 23:59:59.993", $this->customlib->datetostrtotime($this->input->post('date_to')));
                $where = array(
                    "$search_table.$search_column >= '$from_date'",
                    "$search_table.$search_column <= '$to_date'"
                );
            }
        } else if ($search_type == 'all_time') {
            $where = array();  // skip time filters
        } else {
            // existing search_type logic (if needed)
            $where = array();
        }

        if (empty($additional_where)) {
            $additional_where = array('1 = 1');
        }

        if (!empty($where_in)) {
            $where_in = implode(', ', $where_in);
        }

        // Build query
        $query = "SELECT $select FROM $table_name " . implode(" ", $join) .
            " WHERE " . implode(" AND ", array_merge($where, $additional_where));

        if (!empty($group_by)) {
            $query .= " GROUP BY $group_by";
        }

        if (!empty($having)) {
            $query .= " HAVING $having";
        }

        $query .= " ORDER BY $search_table.$search_column";

        $res = $this->db->query($query);
        $allData['main_data'] = $res->result_array();
        // echo '<pre>'; print_r(expression)
        return $allData;
    }
}
