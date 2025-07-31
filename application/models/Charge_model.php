<?php

class Charge_model extends CI_Model
{

    public function add_charges($data)
    {
        $this->db->insert("charges", $data);
        return $this->db->insert_id();
    }

    public function addconsultcharges($data)
    {
        $this->db->insert("consult_charges", $data);
        return $this->db->insert_id();
    }

    public function add_schedule_charge($data)
    {
        $this->db->insert_batch("schedule_charges", $data);
    }

    public function getChargeCategory($charge_type)
    {
        $query = $this->db->where("charge_type", $charge_type)->get("charge_categories");
        return $query->result_array();
    }

    public function getPkgChargeCategory($id)
    {
        // Fetch the charge type for the given ID
        $type_query = $this->db->select('charge_type')->where('id', $id)->get("charges");
        $type_result = $type_query->row(); // Fetch the single row result

        if ($type_result) {
            $charge_type = $type_result->charge_type;

            // Now use the fetched charge type to filter charge categories
            $query = $this->db->where("charge_type", $charge_type)->get("charge_categories");
            return $query->result_array();
        } else {
            return []; // Return empty array if no charge type is found for the given ID
        }
    }

    public function searchFullText()
    {
        $query = $this->db->order_by('id', 'desc')->get('charges');
        return $query->result_array();
    }

    public function getDetails($id, $organisation = "")
    {
        $this->db->select('charges.*,organisations_charges.org_charge');
        $this->db->join('organisations_charges', 'charges.id = organisations_charges.charge_id ', 'left');
        $this->db->where('charges.id', $id);
        if (!empty($organisation)) {
            $this->db->where('organisations_charges.org_id', $organisation);
        }
        $query = $this->db->get('charges');
        return $query->row_array();
    }

    public function getDetailsTpadoctor($id, $organisation = "")
    {
        $this->db->select('consult_charges.*,tpa_doctorcharges.org_charge,staff.name,staff.surname');
        $this->db->join('tpa_doctorcharges', 'consult_charges.id = tpa_doctorcharges.charge_id ', 'left');
        $this->db->join('staff', 'staff.id = consult_charges.doctor', "inner");
        $this->db->where('consult_charges.id', $id);
        if (!empty($organisation)) {
            $this->db->where('tpa_doctorcharges.org_id', $organisation);
        }
        $query = $this->db->get('consult_charges');
        return $query->row_array();
    }

    public function get_chargedoctorfee($charge_type='')
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('consult_charges.*,staff.name,staff.surname');
        $this->db->join('staff', 'consult_charges.doctor = staff.id ', 'INNER');
        if($charge_type!=''){
            $this->db->where('consult_charges.charges_type',$charge_type);
        }
        $query = $this->db->get("consult_charges");
        return $query->result_array();
    }

    public function getScheduleChargeBatch($charges_id)
    {
        $query = $this->db->where('id', $charges_id)->get('charges');
        return $query->row_array();
    }

    public function getScheduleChargeBatchTpadoctor($charges_id)
    {
        $query = $this->db->where('id', $charges_id)->get('consult_charges');
        return $query->row_array();
    }

    public function getAllScheduleCharges($charges_id)
    {
        $query = $this->db->select('schedule_charges.* , schedule_charge_category.id as schid, schedule_charge_category.schedule')
            ->join('schedule_charges', 'schedule_charge_category.id = schedule_charges.schedule_charge_id', 'left')
            ->get('schedule_charge_category');
        return $query->result_array();
    }

    public function getOrganisationCharges($charge_id)
    {
        $query = $this->db->query("SELECT organisations_charges.id,organisations_charges.org_charge,organisations_charges.org_charge,organisation.organisation_name,organisation.id as org_id
        FROM organisations_charges
        RIGHT OUTER JOIN organisation ON organisations_charges.org_id = organisation.id AND organisations_charges.charge_id = '$charge_id'
        ORDER BY organisation.id");
        return $query->result_array();
    }

    public function getOrganisationChargesTpadoctor($charge_id)
    {
        $query = $this->db->query("SELECT tpa_doctorcharges.id,tpa_doctorcharges.org_charge,organisation.organisation_name,organisation.id as org_id
FROM tpa_doctorcharges
RIGHT OUTER JOIN organisation ON tpa_doctorcharges.org_id = organisation.id AND tpa_doctorcharges.charge_id = '$charge_id'
ORDER BY organisation.id");
        return $query->result_array();
    }

    public function delete($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('charges');
        if ($id) {
            $this->db->where('charge_id', $id)
                ->delete('tpa_doctorcharges');
        }
    }

    public function deletedoctorcharge($id)
    {
        $queery = $this->db->where('id', $id)
            ->delete('consult_charges');
    }

    public function update_charges($data)
    {
        $query = $this->db->where('id', $data['id'])
            ->update('charges', $data);
    }

    public function update_consultcharges($data)
    {
        $query = $this->db->where('id', $data['id'])
            ->update('consult_charges', $data);
    }

    public function update_schedule_charge($schedule_data)
    {
        $query = $this->db->where('id', $schedule_data['id'])
            ->update('schedule_charges', $schedule_data);
    }

    public function add_ipdcharges($data)
    {
        $this->db->insert("patient_charges", $data);
    }

    public function checkCharge($chargeId)
    {
        $query = $this->db->select('is_package')
                      ->from('charges')
                      ->where('id', $chargeId)
                      ->get();

        if ($query->num_rows() == 1) {
            $result = $query->row();
            return $result->is_package == 1 ? true : false;
        } else {
            return false;
        }
    }

    public function add_opdcharges($data)
    {
        $this->db->insert("opd_patient_charges", $data);
    }

    public function getAssingedPackages($pid, $ipdid)
    {
        // Select charge_ids assigned to the patient and ipd
        $query = $this->db->select('charge_id')
                        ->from('patient_charges')
                        ->where('patient_id', $pid)
                        ->where('ipd_id', $ipdid)
                        ->get();

        // Check if the query result is empty
        if ($query->num_rows() == 0) {
            // If no packages are assigned, return an empty array
            return [];
        }

        // Fetch the result as an array of objects
        $chargeIds = $query->result();

        // Extract charge_id values into an array
        $chargeIdValues = array_column($chargeIds, 'charge_id');

        // Select packages details based on charge_ids
        $packagesQuery = $this->db->select('charges.id as package_id, charges.charge_type, charges.code')
                            ->from('charges')
                            ->where('charges.is_package', 1)
                            ->where_in('charges.id', $chargeIdValues)
                            ->get();

        // Check if no packages are found
        if ($packagesQuery->num_rows() == 0) {
            // If no packages are found, return an empty array
            return [];
        }

        // Fetch the result as an array
        $packages = $packagesQuery->result_array();

        // Logging the query result for debugging
        // log_message('debug', 'PACKAGES DETAILS: ' . json_encode($packages));

        return $packages;
    }


    public function getCharges($id, $ipdid = '') {
        $query = $this->db->select('patient_charges.*, patients.id as pid, charges.id as package_id, charges.code, charges.charge_type, charges.is_package, charges.charge_category, charges.standard_charge, organisations_charges.id as oid, organisations_charges.org_charge')
            ->join('patients', 'patient_charges.patient_id = patients.id', 'inner')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
            ->join('organisations_charges', 'patient_charges.org_charge_id = organisations_charges.id', 'left')
            ->where('patient_charges.patient_id', $id)
            ->where('patient_charges.ipd_id', $ipdid)
            ->order_by('ISNULL(patient_charges.package_id)', 'ASC') // Orders rows where package_id is null or not set at the bottom
            ->order_by('patient_charges.package_id', 'ASC') // Orders rows with the same package_id together
            ->get('patient_charges');
    
        return $query->result_array();
    }
    
    
    

    public function getPackageDetails($charges) {

        $result = [];
        foreach ($charges as $key => $charge) {
            $query = $this->db->select('staff_charges_percentage.*, staff.id as staff_id, staff.surname, staff.name')
                ->join('staff', 'staff_charges_percentage.staff_id = staff.id', 'inner')
                ->where('staff_charges_percentage.charges_id', $charge['package_id'])
                ->get('staff_charges_percentage');
    
            // Collect the results of each query
            $result[$key] = $query->result_array();
        }
    
        return $result;
    }

    public function getOPDCharges($id, $visitid)
    {
        $query = $this->db->select('opd_patient_charges.*,patients.id as pid,charges.charge_type,charges.charge_category,charges.standard_charge,charges.code,organisations_charges.id as oid,organisations_charges.org_charge')
            ->join('patients', 'opd_patient_charges.patient_id = patients.id', 'inner')
            ->join('charges', 'opd_patient_charges.charge_id = charges.id', 'inner')
            ->join('organisations_charges', 'opd_patient_charges.org_charge_id = organisations_charges.id', 'left')
            ->where('opd_patient_charges.patient_id', $id)
            ->where('opd_patient_charges.opd_id', $visitid)
            ->get('opd_patient_charges');
        return $query->result_array();
    }

    public function deleteIpdPatientCharge($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('patient_charges');
    }

    public function getchargeDetails($charge_category)
    {
        $query = $this->db->where("charge_category", $charge_category)->get("charges");
        return $query->result_array();
    }

    public function check_data_exists($standard_charge, $id, $staff_id,$type='')
    {
        if ($staff_id != 0) {
            $data  = array('id != ' => $staff_id, 'doctor' => $id);
            $query = $this->db->where($data)->get('consult_charges');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('doctor', $id);
            if($type!=''){
                $this->db->where('charges_type', $type);
            }

            $query = $this->db->get('consult_charges');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function valid_doctor_id($str)
    {
        $standard_charge = $this->input->post('standard_charge');
        $id              = $this->input->post('doctor');
        $staff_id        = $this->input->post('editid');
        $type        = $this->input->post('charges_type');
        if (!isset($id)) {
            $id = 0;
        }
        if (!isset($staff_id)) {
            $staff_id = 0;
        }
        if ($this->check_data_exists($standard_charge, $id, $staff_id,$type)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function deleteOpdPatientCharge($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('opd_patient_charges');
    }

    public function getRecurringCharges($patient_id,$ipd_id)
    {
        $qry=$this->db->query("SELECT * FROM patient_charges WHERE id IN (SELECT MAX(id) FROM patient_charges WHERE recurring=1 AND patient_id=$patient_id AND ipd_id=$ipd_id GROUP BY charge_id)");
        $query=$this->db->get();
        $data = array();
        if($query !== FALSE && $query->num_rows() > 0){
            return $query->result_array();
        }else{
            return $data;
        }
    }

    public function getstaffCharges($charge_id)
    {
        $query = $this->db->where("charges_id", $charge_id)->get("staff_charges_percentage");
        return $query->result_array();
    }

    public function getConsultantStaffCharges($charge_id)
    {
        $query = $this->db->where("charges_id", $charge_id)->get("consult_charges_percentage");
        return $query->result_array();
    }

}
