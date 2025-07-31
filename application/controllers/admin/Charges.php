<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Charges extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('common_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'charges/index');
        $this->config->load("payroll");
        $charge_type         = $this->customlib->getChargeMaster();
        $data["charge_type"] = $charge_type;
        $data['resultlist']  = $this->charge_model->searchFullText();
        $data['schedule']    = $this->organisation_model->get();
        $data['doctors']  = $this->staff_model->getStaffbyrole(3);
        $data['other_staff']  = $this->staff_model->getOtherStaff();
        //echo "<pre>";print_r($data['doctors']);exit;
        $this->load->view("layout/header");
        $this->load->view("admin/charges/charge", $data);
        $this->load->view("layout/footer");
    }

    public function add_charges()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('charge_type', $this->lang->line('charge') . " " . $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge') . " " . $this->lang->line('category'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard') . " " . $this->lang->line('charge'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'charge_type'     => form_error('charge_type'),
                'charge_category' => form_error('charge_category'),
                'code'            => form_error('code'),
                'charge'          => form_error('standard_charge'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'charge_type'     => $this->input->post('charge_type'),
                'charge_category' => $this->input->post('charge_category'),
                'code'            => $this->input->post('code'),
                'description'     => $this->input->post('description'),
                'standard_charge' => $this->input->post('standard_charge'),
                'is_package'      => $this->input->post('is_package'),
                // 'doctor_percentage_type' => $this->input->post('doctor_percentage_type'),
                // 'staff_percentage_type' => $this->input->post('staff_percentage_type'),
                // 'doctor_percentage' => $this->input->post('doctor_percentage'),
                // 'staff_percentage' => $this->input->post('staff_percentage'),
            );
            $insert_id       = $this->charge_model->add_charges($data);
            if($this->input->post('doctor')!=='' or $this->input->post('staff')!==''){

                $this->assignPercentage($insert_id);
            }
            $schedule_charge = $this->input->post('schedule_charge');
            $i               = 0;
            if (!empty($schedule_charge)) {
                foreach ($schedule_charge as $key => $value) {
                    $schedule_charge_id = $this->input->post("schedule_charge_id");
                    $schedule_data      = array(
                        'charge_type' => $this->input->post('charge_type'),
                        'charge_id'   => $insert_id,
                        'org_id'      => $schedule_charge_id[$i],
                        'org_charge'  => $value,
                    );
                    $i++;
                    $insert_data[] = $schedule_data;
                }
                $this->tpa_model->add($insert_data);
            }
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function get_charge_category()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_view')) {
            access_denied();
        }
        $charge_type = $this->input->post("charge_type");
        $data        = $this->charge_model->getChargeCategory($charge_type);
        echo json_encode($data);
    }

    public function get_pkg_charge_category()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_view')) {
            access_denied();
        }
        $charge_type = $this->input->post("package_id");
        $data        = $this->charge_model->getPkgChargeCategory($charge_type);
        echo json_encode($data);
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_view')) {
            access_denied();
        }
        $id           = $this->input->post("charges_id");
        $organisation = $this->input->post("organisation");
        $result       = $this->charge_model->getDetails($id, $organisation);
        $result['all_staff']       = $this->charge_model->getstaffCharges($id);
        echo json_encode($result);
    }

    public function getScheduleChargeBatch()
    {
        $id                = $this->input->post("charges_id");
        $result            = $this->charge_model->getScheduleChargeBatch($id);
        $data["result"]    = $result;
        $allCharge         = $this->charge_model->getOrganisationCharges($id);
        $data["allCharge"] = $allCharge;
        $this->load->view('admin/charges/schedulechargeDetail', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('hospital_charge', 'can_delete')) {
            access_denied();
        }
        $result = $this->charge_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/charges');
    }

    public function scheduleChargeBatchGet()
    {
        $id                = $this->input->post("charges_id");
        $result            = $this->charge_model->getScheduleChargeBatch($id);
        $data["result"]    = $result;
        $allCharge         = $this->charge_model->getOrganisationCharges($id);
        $data["allCharge"] = $allCharge;
        $this->load->view('admin/charges/schedulechargeEdit', $data);
    }

    public function update_charges()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('charge_type', $this->lang->line('charge') . " " . $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge') . " " . $this->lang->line('category'), 'required');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard') . " " . $this->lang->line('charge'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'charge_type'     => form_error('charge_type'),
                'charge_category' => form_error('charge_category'),
                'charge'          => form_error('standard_charge'),
                'code'            => form_error('code'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id   = $this->input->post('id');
            $data = array(
                'id'              => $id,
                'charge_type'     => $this->input->post('charge_type'),
                'charge_category' => $this->input->post('charge_category'),
                'code'            => $this->input->post('code'),
                'description'     => $this->input->post('description'),
                'standard_charge' => $this->input->post('standard_charge'),
                'is_package'      => $this->input->post('package'),
                // 'doctor_percentage_type' => $this->input->post('doctor_percentage_type'),
                // 'staff_percentage_type' => $this->input->post('staff_percentage_type'),
                // 'doctor_percentage' => $this->input->post('doctor_percentage'),
                // 'staff_percentage' => $this->input->post('staff_percentage'),
            );
            $update_data     = $this->charge_model->update_charges($data);
            if($this->input->post('doctor')!=='' or $this->input->post('staff')!==''){

                $this->assignPercentage($id,'update');
            }
            $schedule_charge = $this->input->post('schedule_charge');
            $i               = 0;
            if (!empty($schedule_charge)) {
                foreach ($schedule_charge as $key => $value) {
                    $schedule_charge_id = $this->input->post("schedule_charge_id");
                    $ids                = $this->input->post('sid');
                    if ($ids[$i] == 0) {
                        $insert_data = array(
                            'charge_type' => $this->input->post('charge_type'),
                            'charge_id'   => $id,
                            'org_id'      => $schedule_charge_id[$i],
                            'org_charge'  => $value,
                        );
                        $ins_data[] = $insert_data;
                    } else {
                        $schedule_data = array(
                            'id'         => $ids[$i],
                            'org_charge' => $value,
                        );
                        $this->tpa_model->edit_org($ids[$i], $schedule_data);
                    }
                    $i++;
                }
            }
            if (isset($ins_data)) {
                $this->tpa_model->add($ins_data);
            }

            $json_array = array('status' => 'success', 'error' => '', 'message' => 'Record Added Successfully');
        }
        echo json_encode($json_array);
    }

    public function add_ipdcharges()
    {
        //echo "<pre>";print_r($_POST);exit;
        $this->form_validation->set_rules('charge_type', $this->lang->line('charge') . " " . $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge') . " " . $this->lang->line('category'), 'required');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        // Conditionally set charge_type validation rule
        // if (empty($this->input->post('package_id'))) {
        //     $this->form_validation->set_rules('charge_type', $this->lang->line('charge') . " " . $this->lang->line('type'), 'required');
        // }

        // Run validation
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'            => form_error('date'),
                'charge_type'     => form_error('charge_type'),
                'charge_category' => form_error('charge_category'),
                'apply_charge'    => form_error('apply_charge'),
                'code'            => form_error('code'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $recurrning=$this->input->post('recurring');
            $recurrning=isset($recurrning) && !empty($recurrning) ? '1' : '0';
            $data = array('patient_id' => $this->input->post('patient_id'),
                'date'                     => date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('date'))),
                'charge_id'                => $this->input->post('charge_id'),
                'ipd_id'                   => $this->input->post('ipdid'),
                'org_charge_id'            => $this->input->post('org_id'),
                'apply_charge'             => $this->input->post('apply_charge'),
                'recurring'             => $recurrning,
                'created_at'               => date('Y-m-d'),
            );

            // $chargePkg = $this->charge_model->checkCharge($this->input->post('charge_id'));
            // if($chargePkg) {
            //     $data['package_id'] = $this->input->post('charge_id');
            // }
            
            $isPackage=$this->input->post('is_package');
            if(isset($isPackage) && !empty($isPackage)) {
                $data['package_id'] = $this->input->post('charge_id');
            }
            
            $package_id = $this->input->post('package_id');

            if (!empty($package_id)) {
                $data['package_id'] = $package_id;
            }

            $this->charge_model->add_ipdcharges($data);
            $json_array = array('status' => 'success', 'error' => '', 'message' => 'Record Saved Successfully');
        }
        echo json_encode($json_array);
    }

    public function add_opdcharges()
    {
        $this->form_validation->set_rules('charge_type', $this->lang->line('charge') . " " . $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge') . " " . $this->lang->line('category'), 'required');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'            => form_error('date'),
                'charge_type'     => form_error('charge_type'),
                'charge_category' => form_error('charge_category'),
                'apply_charge'    => form_error('apply_charge'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array('patient_id' => $this->input->post('patient_id'),
                'date'                     => date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('date'))),
                'charge_id'                => $this->input->post('charge_id'),
                'opd_id'                   => $this->input->post('opd_id'),
                'org_charge_id'            => $this->input->post('org_id'),
                'apply_charge'             => $this->input->post('apply_charge'),
                'created_at'               => date('Y-m-d'),
            );
            $this->charge_model->add_opdcharges($data);
            $json_array = array('status' => 'success', 'error' => '', 'message' => 'Record Saved Successfully');
        }
        echo json_encode($json_array);
    }

    public function getchargeDetails()
    {

        $charge_category = $this->input->post("charge_category");
        $result          = $this->charge_model->getchargeDetails($charge_category);
        echo json_encode($result);
    }

    public function deleteOpdPatientCharge($pateint_id, $id, $opdid)
    {
        if (!$this->rbac->hasPrivilege('charges', 'can_delete')) {
            access_denied();
        }
        $this->charge_model->deleteOpdPatientCharge($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Patient Charges deleted successfully</div>');
        redirect('admin/patient/visitDetails/' . $pateint_id . '/' . $opd_id . '#charges');
    }

    public function assignPercentage($chargesID,$update=NULL)
    {
        if($update=='update'){
            $this->db->delete('staff_charges_percentage', array('charges_id' => $chargesID));
        }
        $doctorData=array();
        $staffData=array();
        if($this->input->post('doctor')!==''){
            $doctorPercentageType=$this->input->post('doctor_percentage_type');
            $doctorPercentage=$this->input->post('doctor_percentage');
            foreach($this->input->post('doctor') as $dkey=>$dt){
                $doctorData[]=array(
                    'charges_id'=>$chargesID,
                    'staff_id'=>$dt,
                    'percentage_type'=>$doctorPercentageType[$dkey],
                    'staff_percentage'=>$doctorPercentage[$dkey],
                    'type'=>'doctor',

                );
                log_message('debug', 'Doctor ID: ' . json_encode($dt));
                if($dt!=='') {
                    $doctorInfo = $this->common_model->getStaffRow($dt);
                    $chargesInfo = $this->common_model->getChargesfRow($chargesID);
                    $comments="Charges Updated for ". $doctorInfo['name']." in ". $chargesInfo['charge_type']." -> ".$chargesInfo['charge_category']." -> ".$chargesInfo['code']." The Share Type is ".$doctorPercentageType[$dkey]." and Value is Set at $doctorPercentage[$dkey]";
                    $this->common_model->saveLog('Charges','Edit',$comments,$chargesID);
                }
            }
            $this->db->insert_batch('staff_charges_percentage',$doctorData);
        }
        if($this->input->post('staff')!==''){
            $staffPercentageType=$this->input->post('staff_percentage_type');
            $staffPercentage=$this->input->post('staff_percentage');
            foreach($this->input->post('staff') as $skey=>$st){
                $staffData[]=array(
                    'charges_id'=>$chargesID,
                    'staff_id'=>$st,
                    'percentage_type'=>$staffPercentageType[$skey],
                    'staff_percentage'=>$staffPercentage[$skey],
                    'type'=>'staff',

                );
                if($st!=='') {
                    $staffInfo = $this->common_model->getStaffRow($st);
                    $chargesInfo = $this->common_model->getChargesfRow($chargesID);
                    $comments="Charges Updated for ". $staffInfo['name']." in ". $chargesInfo['charge_type']." -> ".$chargesInfo['charge_category']." -> ".$chargesInfo['code']." The Share Type is ".$staffPercentageType[$skey]." and Value is Set at $staffPercentage[$skey]";
                    $this->common_model->saveLog('Charges','Edit',$comments,$chargesID);
                }
            }
            $this->db->insert_batch('staff_charges_percentage',$staffData);
        }
    }

}
