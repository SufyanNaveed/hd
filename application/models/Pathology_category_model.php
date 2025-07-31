<?php

class Pathology_category_model extends CI_model
{

    public function valid_category_name($str)
    {
        $category_name = $this->input->post('category_name');
        $id            = $this->input->post('pathology_category_id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_category_exists($category_name, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function valid_unit_name($str)
    {
        $unit_name = $this->input->post('unit_name');
        $id        = $this->input->post('unit_id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_unit_exists($unit_name, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function valid_parameter_name($str)
    {
        $parameter_name = $this->input->post('parameter_name');
        $id             = $this->input->post('pathology_parameter_id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_parameter_exists($parameter_name, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function getCategoryName($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('pathology_category');
            return $query->row_array();
        } else {
            $query = $this->db->get("pathology_category");
            return $query->result_array();
        }
    }

    public function getreportParams($patho_id, $report_id) {
        $subquery = $this->db->select('parameter_id')
            ->from('pathology_parameterdetails')
            ->where('pathology_id', $patho_id)
            ->get_compiled_select();
    
        $query = $this->db->select('pathology_report_parameterdetails.*, pathology_parameter.parameter_name, pathology_parameter.reference_range, pathology_parameter.unit, unit.unit_name')
            ->from('pathology_report_parameterdetails')
            ->join('pathology_parameter', 'pathology_parameter.id = pathology_report_parameterdetails.parameter_id')
            ->join('unit', 'unit.id = pathology_parameter.unit', 'left')
            ->join('pathology_report', 'pathology_report.id = pathology_report_parameterdetails.pathology_report_id')
            ->where('pathology_report_parameterdetails.pathology_report_id', $report_id)
            ->where_in('pathology_report_parameterdetails.parameter_id', $subquery, false)
            ->get();
    
        return $query->result_array();
    }

    public function getMultiReportParams($patho_id, $report_id, $patient_id) {

        $reports = $this->db
            ->select('id as report_id, created_at, updated_at')
            ->from('pathology_report')
            ->where('pathology_id', $patho_id)
            ->where('patient_id', $patient_id)
            ->order_by('created_at', 'DESC')
            ->limit(4)
            ->get()
            ->result_array();

        // Fetch latest 4 report IDs
        $reportIds = $this->db
            ->select('id as pathology_report_id')
            ->from('pathology_report')
            ->where('pathology_id', $patho_id)
            ->where('patient_id', $patient_id)
            ->order_by('created_at', 'DESC')
            ->limit(4)
            ->get()
            ->result_array();
    
        // Fetch parameter IDs for the given pathology and report IDs
        $parameterIds = $this->db
        ->select('prpd.parameter_id')
        ->from('pathology_report_parameterdetails prpd')
        ->join('pathology_report pr', 'pr.id = prpd.pathology_report_id')
        ->join('pathology_parameterdetails ppd', 'ppd.parameter_id = prpd.parameter_id') // Join with pathology_parameterdetails
        ->where_in('prpd.pathology_report_id', array_column($reportIds, 'pathology_report_id'))
        ->where('prpd.pathology_report_value !=', '') // Filter by non-empty values
        ->where('ppd.pathology_id', $patho_id) // Match pathology_id
        ->get()
        ->result_array();
    
        // Fetch parameter details using parameter IDs
        $paramterDetails = $this->db
            ->select('pathology_parameter.id, pathology_parameter.parameter_name, pathology_parameter.reference_range, pathology_parameter.unit, unit.unit_name')
            ->from('pathology_parameter')
            ->join('unit', 'unit.id = pathology_parameter.unit', 'left')
            ->where_in('pathology_parameter.id', array_column($parameterIds, 'parameter_id'))
            ->order_by('pathology_parameter.id', 'ASC')
            ->get()
            ->result_array();
    
        // Fetch report details for the given report ID and parameter IDs
        $reportsResults = $this->db
            ->select('pathology_report_parameterdetails.*, pathology_report.created_at')
            ->from('pathology_report_parameterdetails')
            ->join('pathology_report', 'pathology_report.id = pathology_report_parameterdetails.pathology_report_id')
            ->where_in('pathology_report_parameterdetails.pathology_report_id', array_column($reportIds, 'pathology_report_id'))
            ->where_in('pathology_report_parameterdetails.parameter_id', array_column($parameterIds, 'parameter_id'))
            ->get()
            ->result_array();
    
        $resultArray = [
            'paramterDetails' => $paramterDetails,
            'reportsResult' => $reportsResults,
            'reports' => $reports,
        ];
    
        return $resultArray;
    }
    

    // public function getSelectedReportParams($patient_id, $patho_ids) {
    //     $ids = explode(',', $patho_ids);
        
    //     // Fetch all reports for the given report IDs and patient ID along with parameter details and values
    //     $reportsResults = $this->db
    //         ->select('prpd.*, pr.created_at as report_created_at, pr.updated_at as report_updated_at, pr.bill_no, pp.id as parameter_id, pp.parameter_name, pp.reference_range, pp.unit, u.unit_name, prpd.pathology_report_value, p.test_name')
    //         ->from('pathology_report_parameterdetails prpd')
    //         ->join('pathology_report pr', 'pr.id = prpd.pathology_report_id')
    //         ->join('pathology_parameter pp', 'pp.id = prpd.parameter_id')
    //         ->join('unit u', 'u.id = pp.unit', 'left')
    //         ->join('pathology p', 'p.id = pr.pathology_id') // Join with pathology table to get test_name
    //         ->where_in('prpd.pathology_report_id', $ids)
    //         ->where('pr.patient_id', $patient_id)
    //         ->order_by('pr.created_at', 'DESC')
    //         ->order_by('pp.id', 'ASC')
    //         ->get()
    //         ->result_array();
        
    //     // Organize the results by report ID with parameter details, values, and test_name
    //     $reportsResultsGrouped = [];
    //     foreach ($reportsResults as $result) {
    //         $reportId = $result['pathology_report_id'];
    //         if (!isset($reportsResultsGrouped[$reportId])) {
    //             $reportsResultsGrouped[$reportId] = [
    //                 'report_id' => $reportId,
    //                 'created_at' => $result['report_created_at'],
    //                 'updated_at' => $result['report_updated_at'],
    //                 'test_name' => $result['test_name'],
    //                 'bill_no' => $result['bill_no'],
    //                 'parameters' => [],
    //             ];
    //         }
    
    //         // Add result value to parameters array
    //         $reportsResultsGrouped[$reportId]['parameters'][] = [
    //             'parameter_id' => $result['parameter_id'],
    //             'parameter_name' => $result['parameter_name'],
    //             'reference_range' => $result['reference_range'],
    //             'unit' => $result['unit'],
    //             'unit_name' => $result['unit_name'],
    //             'result_value' => $result['pathology_report_value'],
    //             'updated_at' => $result['report_updated_at'], // Include updated_at timestamp
    //         ];
    //     }
    
    //     // Convert the grouped array to a sequential array
    //     $finalReportsResults = array_values($reportsResultsGrouped);
    //     log_message('debug', 'RESULT: ' . json_encode($finalReportsResults));
    //     $resultArray = [
    //         'reportsResult' => $finalReportsResults,
    //     ];
    
    //     return $resultArray;
    // }
    public function getSelectedReportParams($patient_id, $patho_ids) {
        $ids = explode(',', $patho_ids);
        $resultArray = [];
    
        foreach ($ids as $patho_id) {
            // Fetch pathology details including test_name
            $pathologyInfo = $this->db
                ->select('id as pathology_id, test_name')
                ->from('pathology')
                ->where('id', $patho_id)
                ->get()
                ->row_array();
    
            if ($pathologyInfo) {
                $reports = $this->db
                    ->select('id as report_id, created_at, updated_at')
                    ->from('pathology_report')
                    ->where('pathology_id', $patho_id)
                    ->where('patient_id', $patient_id)
                    ->order_by('created_at', 'DESC')
                    ->limit(3)
                    ->get()
                    ->result_array();
                // Fetch report IDs for the given pathology
                $reportIds = $this->db
                    ->select('id as pathology_report_id')
                    ->from('pathology_report')
                    ->where('pathology_id', $patho_id)
                    ->where('patient_id', $patient_id)
                    ->order_by('created_at', 'DESC')
                    ->limit(3)
                    ->get()
                    ->result_array();
                // Fetch parameter IDs for the given pathology
                $parameterIds = $this->db
                ->select('prpd.parameter_id')
                ->from('pathology_report_parameterdetails prpd')
                ->join('pathology_report pr', 'pr.id = prpd.pathology_report_id')
                ->join('pathology_parameterdetails ppd', 'ppd.parameter_id = prpd.parameter_id') // Join with pathology_parameterdetails
                ->where_in('prpd.pathology_report_id', array_column($reportIds, 'pathology_report_id'))
                ->where('prpd.pathology_report_value !=', '') // Filter by non-empty values
                ->where('ppd.pathology_id', $patho_id) // Match pathology_id
                ->get()
                ->result_array();

                // log_message('debug', 'Report Params: ' . json_encode($parameterIds));

                // Fetch parameter details using filtered parameter IDs
                $paramterDetails = [];

                // Check if $parameterIds is not empty
                if (!empty($parameterIds)) {
                    // Fetch parameter details using filtered parameter IDs
                    $paramterDetails = $this->db
                        ->select('pp.id, pp.parameter_name, pp.reference_range, pp.unit, u.unit_name')
                        ->from('pathology_parameter pp')
                        ->join('unit u', 'u.id = pp.unit', 'left')
                        ->where_in('pp.id', array_column($parameterIds, 'parameter_id'))
                        ->order_by('pp.id', 'ASC')
                        ->get()
                        ->result_array();
                } else {
                    // Add a dummy value to $parameterIds to ensure the IN() clause has at least one value
                    $parameterIds[] = ['parameter_id' => 'dummy'];
                } 
                // Fetch report details for the given report ID and parameter IDs
                $reportsResults = $this->db
                    ->select('pathology_report_parameterdetails.*, pathology_report.created_at')
                    ->from('pathology_report_parameterdetails')
                    ->join('pathology_report', 'pathology_report.id = pathology_report_parameterdetails.pathology_report_id')
                    ->where_in('pathology_report_parameterdetails.pathology_report_id', array_column($reportIds, 'pathology_report_id'))
                    ->where_in('pathology_report_parameterdetails.parameter_id', array_column($parameterIds, 'parameter_id'))
                    ->get()
                    ->result_array();
    
                $resultArray[$patho_id] = [
                    'pathologyInfo' => $pathologyInfo,
                    'paramterDetails' => $paramterDetails,
                    'reportsResult' => $reportsResults,
                    'reports' => array_reverse($reports),
                ];
            }
        }
        // log_message('debug', 'RESULT DETAILS: ' . json_encode($resultArray));
        return $resultArray;
    }
    
    
    
    
    
    
    // public function getreportParams($patho_id, $report_id) {
    //     $subquery = $this->db->select('parameter_id')
    //         ->from('pathology_parameterdetails')
    //         ->where('pathology_id', $patho_id)
    //         ->get_compiled_select();
    
    //     $query = $this->db->select('pathology_report_parameterdetails.*, pathology_parameter.parameter_name, pathology_parameter.reference_range, pathology_parameter.unit, unit.unit_name')
    //         ->from('pathology_report_parameterdetails')
    //         ->join('pathology_parameter', 'pathology_parameter.id = pathology_report_parameterdetails.parameter_id')
    //         ->join('unit', 'unit.id = pathology_parameter.unit', 'left')
    //         ->join('pathology_report', 'pathology_report.id = pathology_report_parameterdetails.pathology_report_id')
    //         ->where('pathology_report_parameterdetails.pathology_report_id', $report_id)
    //         ->where_in('pathology_report_parameterdetails.parameter_id', $subquery, false)
    //         ->group_by('pathology_report_parameterdetails.id') // Group by the primary key
    //         ->having('pathology_report_value IS NOT NULL') // Filter out empty values
    //         ->get();
    
    //     return $query->result_array();
    // }
    
    
    
    public function getparameterDetailsforpatient($report_id)
    {
        // log_message('debug', 'PATHO VALUE: ' . $report_id);
        $query = $this->db->select('pathology_report_parameterdetails.*,pathology_parameter.parameter_name,pathology_parameter.reference_range,pathology_parameter.unit,unit.unit_name')
            ->join('pathology_parameter', 'pathology_parameter.id = pathology_report_parameterdetails.parameter_id')
            ->join('unit', 'unit.id = pathology_parameter.unit','left')
            ->where("pathology_report_parameterdetails.pathology_report_id", $report_id)
            ->get('pathology_report_parameterdetails');
            // log_message('debug', 'PATHO REPORTS: ' . json_encode($query->result_array()));
        return $query->result_array();

        // $query = $this->db->select('pathology_report_parameterdetails.*, pathology_parameter.parameter_name, pathology_parameter.reference_range, pathology_parameter.unit, unit.unit_name')
        //     ->join('pathology_parameter', 'pathology_parameter.id = pathology_report_parameterdetails.parameter_id')
        //     ->join('unit', 'unit.id = pathology_parameter.unit', 'left')
        //     ->where("pathology_report_parameterdetails.pathology_report_id", $report_id)
        //     ->where("pathology_report_parameterdetails.pathology_report_value IS NOT NULL")
        //     ->where("pathology_report_parameterdetails.pathology_report_value !=", "")
        //     ->get('pathology_report_parameterdetails');

        // // Logging the debug message
        // // log_message('debug', 'PATHO REPORTS: ' . json_encode($query->result_array()));

        // // Filter out null or empty values from the result array
        // $result_array = array_filter($query->result_array(), function($row) {
        //     return !empty($row['pathology_report_value']);
        // });

        // return $result_array;
    }

    public function getparameterDetails($id, $value_id = '')
    {
        $check_query = $this->db->select('pathology_parameterdetails.*')
            ->where('pathology_parameterdetails.pathology_report_id', $value_id)
            ->get('pathology_parameterdetails');
        $num_rows = $check_query->num_rows();

        if ($num_rows > 0) {
            $query = $this->db->select('pathology_parameterdetails.*,pathology_parameter.parameter_name,pathology_parameter.reference_range,pathology_parameter.unit,unit.unit_name')
                ->join('pathology_parameter', 'pathology_parameter.id = pathology_parameterdetails.parameter_id')
                ->join('unit', 'unit.id = pathology_parameter.unit')
                ->where('pathology_parameterdetails.pathology_id', $id)
                ->where("pathology_parameterdetails.pathology_report_id", $value_id)
                ->get('pathology_parameterdetails');
        } else {
            $query = $this->db->select('pathology_parameterdetails.pathology_id,pathology_parameterdetails.parameter_id,pathology_parameterdetails.created_id,pathology_parameterdetails.pathology_report_id,pathology_parameter.parameter_name,pathology_parameter.reference_range,pathology_parameter.id as parid,pathology_parameter.unit,unit.unit_name')
                ->join('pathology_parameter', 'pathology_parameter.id = pathology_parameterdetails.parameter_id')
                ->join('unit', 'unit.id = pathology_parameter.unit')
                ->where('pathology_parameterdetails.pathology_id', $id)
                ->get('pathology_parameterdetails');
        }
        return $query->result_array();
    }

    public function getpathoparameter($id = null)
    {
        if (!empty($id)) {
            $this->db->select('pathology_parameter.*,unit.unit_name');
            $this->db->from('pathology_parameter');
            $this->db->join('unit', 'pathology_parameter.unit = unit.id', 'left');
            $this->db->where("pathology_parameter.id", $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->select('pathology_parameter.*,unit.unit_name');
            $this->db->from('pathology_parameter');
            $this->db->join('unit', 'pathology_parameter.unit = unit.id', 'left');
            $this->db->join('pathology', 'pathology_parameter.id = pathology.pathology_parameter_id', 'left');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function getunit($id = null)
    {
        if (!empty($id)) {
            $this->db->select("unit.*");
            $this->db->where('id', $id);
            $this->db->where('unit_type', 'patho');
            $query = $this->db->get('unit');
            return $query->row_array();
        } else {
            $this->db->select("unit.*");
            $this->db->where('unit_type', 'patho');
            $query = $this->db->get('unit');
            return $query->result_array();
        }
    }

    public function check_category_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'category_name' => $name);
            $query = $this->db->where($data)->get('pathology_category');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('category_name', $name);
            $query = $this->db->get('pathology_category');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function check_unit_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'unit_name' => $name);
            $query = $this->db->where($data)->get('unit');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('unit_name', $name);
            $query = $this->db->get('unit');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function check_parameter_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'parameter_name' => $name);
            $query = $this->db->where($data)->get('pathology_parameter');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('parameter_name', $name);
            $query = $this->db->get('pathology_parameter');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function addCategoryName($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology_category', $data);
        } else {
            $this->db->insert('pathology_category', $data);
            return $this->db->insert_id();
        }
    }

    public function addunit($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('unit', $data);
        } else {
            $this->db->insert('unit', $data);
            return $this->db->insert_id();
        }
    }

    public function addparameter($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology_parameter', $data);
        } else {
            $this->db->insert('pathology_parameter', $data);
            return $this->db->insert_id();
        }
    }

    public function getAllDetails($id)
    {
        $query = $this->db->select('pathology_parameterdetails.*')
            ->where('pathology_id', $id)
            ->get('pathology_parameterdetails');
        return $query->result_array();
    }

    public function getall()
    {
        $this->datatables->select('id,category_name');
        $this->datatables->from('pathology_category');
        $this->datatables->add_column('view', '<a href="' . site_url('admin/pathologycategory/edit/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"> <i class="fa fa-pencil"></i></a><a href="' . site_url('admin/pathologycategory/delete/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Delete">
                                                        <i class="fa fa-remove"></i>
                                                    </a>', 'id');
        return $this->datatables->generate();
    }

    public function delete($id)
    {
        $this->db->where("id", $id)->delete("pathology_category");
    }

    public function deleteunit($id)
    {
        $this->db->where("id", $id)->delete("unit");
    }

    public function deleteparameter($id)
    {
        $this->db->where("id", $id)->delete("pathology_parameter");
    }

}
