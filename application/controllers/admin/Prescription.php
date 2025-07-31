<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Prescription extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
    }

    public function getPrescription($id, $opdid, $visitid = '')
    {
        $this->load->model('common_model');
        if ($visitid > 0) {
            $result = $this->prescription_model->getvisit($visitid);
        } else {
            $result = $this->prescription_model->get($id);
        }

        $result['opd_id'];
        $prescription_list     = $this->prescription_model->getPrescriptionByOPD($result['opd_id'], $visitid);
        $data["print_details"] = $this->printing_model->get('', 'opdpre');
        $data["result"]        = $result;
       // echo "<pre>";print_r($result);exit;
        $data["id"]            = $id;
        $data["opdid"]         = $opdid;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $data['m_prescription']=$medical_prescription=$this->common_model->getRecord($id = null,' prescription_medical',$where=array('opd_id'=>$opdid));
        $symptom = $medical_prescription['symptom'];
        isset($symptom) && !empty($symptom) ? $symptom=explode(",", $symptom):NULL;
        $data['symptopms']=isset($symptom) && $symptom!=NULL ? $this->common_model->fetch_symtopms('symptoms_classification', $symptom) : NULL;
        $diagnosis = $medical_prescription['diagnosis'];
        isset($diagnosis) && !empty($diagnosis) ? $diagnosis=explode(",", $diagnosis):NULL;
        $data['diagnosis']=isset($diagnosis) && $diagnosis!=NULL ? $this->common_model->fetch_diagnosis('diagnosis', $diagnosis) : NULL;
        $lab_test = $medical_prescription['lab_test'];
        isset($lab_test) && !empty($lab_test) ? $lab_test=explode(",", $lab_test):NULL;
        $data['lab_test']=isset($lab_test) && $lab_test!=NULL ? $this->common_model->getLabInvestigations($lab_test) : NULL;
        $data['print_prescription']=$this->setting_model->getSetting();
        //echo "<pre>";print_r($data['print_prescription']);exit;
        $precaution = $medical_prescription['precaution'];
        isset($precaution) && !empty($precaution) ? $precaution=explode(",", $precaution):NULL;
        $data['precaution']=isset($precaution) && $precaution!=NULL ? $this->common_model->fetch_precaution('medicin_precaution', $precaution) : NULL;
        $data["prescription_list"] = $prescription_list;
        //echo "<pre>";print_r( $data);exit;
        $this->load->view("admin/patient/prescription", $data);
    }

    public function getPrescriptionmanual($id, $opdid,$emg='')
    {
        if($emg==''){
            $result = $this->prescription_model->getmanual($opdid);
        }
        else{
            $result = $this->prescription_model->getmanualEmg($opdid);
        }

        $data["print_details"] = $this->printing_model->get('', 'opdpre');
        $data["result"]        = $result;

        $data["id"]    = $id;
        $data["opdid"] = $opdid;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        if($emg=='emg'){
            $prescription_list     = $this->prescription_model->getPrescriptionByEMG($result['opd_id'], $visitid);
        }else{
            $prescription_list     = $this->prescription_model->getPrescriptionByOPD($result['opd_id'], $visitid);
        }

        $data["prescription_list"] = $prescription_list;
        //echo "<pre>";print_r($data);exit;
        // $this->load->view("admin/patient/prescriptionmanual", $data);
        $this->load->view("admin/patient/prescription_manual", $data);
    }

    public function getIPDPrescription($id, $ipdid, $visitid = '')
    {
        $result                = $this->prescription_model->getIPD($id);
        $prescription_list     = $this->prescription_model->getPrescriptionByIPD($id, $ipdid, $visitid);
        $data["print_details"] = $this->printing_model->get('', 'ipdpres');


        $data["result"]        = $result;
        $data["id"]            = $id;
        $data["ipdid"]         = $ipdid;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $data["prescription_list"] = $prescription_list;
        $this->load->view("admin/patient/ipdprescription", $data);
    }

    public function editPrescription($id, $opdid, $visitid = '',$patient_id='')
    {
        $this->load->model('common_model');
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $data['dosage']           = $this->medicine_dosage_model->getMedicineDosage();
        if ($visitid > 0) {

            $result = $this->prescription_model->getvisit($visitid);
        } else {

            $result = $this->prescription_model->get($id);
        }
        isset($visitid) && $visitid > 0 ? $visitid : $visitid='';
        $prescription_list         = $this->prescription_model->getPrescriptionByOPD($opdid, $visitid);
        $data['roles']             = $this->role_model->get();
        $data["result"]            = $result;
        //echo "<pre>";print_r($data["result"] );exit;
        $data["id"]                = $id;
        $data["opdid"]             = $opdid;
        $data["prescription_list"] = $prescription_list;
        $data["diagnosis_detail"] = $this->patient_model->getDiagnosisDetails($patient_id);
        $data['problems']=$this->common_model->getRecord($id = null,'symptoms_classification');
        $data['precautions']=$this->common_model->getRecord($id = null,'medicin_precaution');
        $data["lab_report"]  = $this->patient_model->getLabInvestigations($patient_id);

        $data['prescription_medical']=$prescription_medical=$this->common_model->getRecord($id = null,'prescription_medical',$where=array('opd_id'=>$opdid));
        $data['symptoms']=isset($prescription_medical['symptom']) && !empty($prescription_medical['symptom']) ? explode(',',$prescription_medical['symptom']) : '';
        $data['diagnosis']=isset($prescription_medical['diagnosis']) && !empty($prescription_medical['diagnosis']) ? explode(',',$prescription_medical['diagnosis']) : '';
        $data['lab_test']=isset($prescription_medical['lab_test']) && !empty($prescription_medical['lab_test']) ? explode(',',$prescription_medical['lab_test']) : '';
        $data['precaution']=isset($prescription_medical['precaution']) && !empty($prescription_medical['precaution']) ? explode(',',$prescription_medical['precaution']) : '';
        $this->load->view("admin/patient/edit_prescription", $data);

    }

    public function editipdPrescription($id, $ipdid, $visitid = '')
    {
        $data['medicineCategory']  = $this->medicine_category_model->getMedicineCategory();
        $data['medicineName']      = $this->pharmacy_model->getMedicineName();
        $data['dosage']            = $this->medicine_dosage_model->getMedicineDosage();
        $result                    = $this->prescription_model->getIPD($id);
        $prescription_list         = $this->prescription_model->getPrescriptionByIPD($id, $ipdid, $visitid);
        $data['roles']             = $this->role_model->get();
        $data["result"]            = $result;
        $data["id"]                = $id;
        $data["ipdid"]             = $ipdid;
        $data["prescription_list"] = $prescription_list;
        $this->load->view("admin/patient/edit_ipdprescription", $data);
    }

    public function deletePrescription($id, $opdid, $visitid = '')
    {
        if (!empty($opdid)) {
            if ($visitid > 0) {
                $this->prescription_model->deletePrescription($opdid, $visitid);
            } else {
                $this->prescription_model->deletePrescription($opdid);
            }
            $json = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('delete_message'));
            echo json_encode($json);
        }
    }

    public function deleteipdPrescription($id, $ipdid)
    {
        if (!empty($id)) {
            $this->prescription_model->deleteipdPrescription($id);
            $json = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('delete_message'));
            echo json_encode($json);
        }
    }

}
