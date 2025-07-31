<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class patient extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->config->load("mailsms");
        $this->notification            = $this->config->item('notification');
        $this->notificationurl         = $this->config->item('notification_url');
        $this->patient_notificationurl = $this->config->item('patient_notification_url');
        $this->load->library('Enc_lib');
        // $this->load->library('encoding_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('CSVReader');
        $this->load->library('Customlib');
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->search_type    = $this->config->item('search_type');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->load->model('conference_model');
        $this->load->model('common_model');
        $this->load->model('quee_model');
        $this->charge_type          = $this->customlib->getChargeMaster();
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_add')) {
            access_denied();
        }
        $patient_type = $this->customlib->getPatienttype();

        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant') . " " . $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('casualty', "Select counter", 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'patient_id'        => form_error('patient_id'),
                'amount'            => form_error('amount'),
                'casualty'            => form_error('casualty'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $check_opd_id     = $this->patient_model->getMaxOPDId();
            $opdnoid          = $check_opd_id + 1;
            $doctor_id        = $this->input->post('consultant_doctor');
            $insert_id        = $this->input->post('patient_id');
            $password         = $this->input->post('password');
            $email            = $this->input->post('email');
            $mobileno         = $this->input->post('mobileno');
            $patient_name     = $this->input->post('patient_name');
            $appointment_date = $this->input->post('appointment_date');
            $isopd            = $this->input->post('is_opd');
            $appointmentid    = $this->input->post('appointment_id');
            $consult     = $this->input->post('live_consult');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            }else{
                $live_consult = $this->lang->line('no');
            }

            $date             = date('Y-m-d H:i:s', $this->customlib->datetostrtotime($appointment_date));
            $next_visit='';
            if($this->input->post('next_visit')!==''){
                $next_visit= date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('next_visit')));

            }
            if($this->input->post('discount_type')=='percentage' && $this->input->post('amount') > 0){
                $opd_discount=($this->input->post('applied_total') * $this->input->post('opd_discount')/100);
                $opd_discount=number_format($opd_discount,2);

            }
            if($this->input->post('discount_type')=='fixed' && $this->input->post('amount') > 0){
                $opd_discount=$this->input->post('opd_discount');
            }
            $setOPD=$this->formatNumber($opdnoid,$type='opd');
            $opd_data         = array(
                'appointment_date' => $date,
                'case_type'        => $this->input->post('case'),
                'opd_no'           => 'OPDN-'.$setOPD,
                'symptoms'         => $this->input->post('symptoms'),
                'refference'       => $this->input->post('refference'),
                'cons_doctor'      => $doctor_id,
                'spo2'             => $this->input->post('spo2'),
                'height'           => $this->input->post('height'),
                'weight'           => $this->input->post('weight'),
                'bp'               => $this->input->post('bp'),
                'pulse'            => $this->input->post('pulse'),
                'temperature'      => $this->input->post('temperature'),
                'respiration'      => $this->input->post('respiration'),
                'patient_id'       => $insert_id,
                'casualty'         => $this->input->post('casualty'),
                'set_casuality'         => $this->input->post('set_casuality'),
                'payment_mode'     => $this->input->post('payment_mode'),
                'note_remark'      => $this->input->post('note'),
                'amount'           => $this->input->post('applied_total'),
                'standard_charges'           => $this->input->post('standard_charge'),
                'opd_discount'           => $opd_discount,
                'opd_discount_type'           => $this->input->post('discount_type'),
                'department'           => $this->input->post('department'),
                'next_visit'           => $next_visit,
                'live_consult'     => $live_consult,
                'generated_by'     => $this->session->userdata('hospitaladmin')['id'],
                'discharged'       => 'no',
            );

            $patient_data = array(
                'id'           => $insert_id,
                'old_patient'  => $this->input->post('old_patient'),
                'organisation' => $this->input->post('organisation'),

            );
            $select="opd_commission";
            $staff_info=$this->staff_model->getStaffCommission($select,$doctor_id);
            if($this->input->post('amount') > 0 && $staff_info['opd_commission'] > 0){
                $commission_month=date('m',strtotime($this->input->post('appointment_date')));
                $commission_year=date('Y',strtotime($this->input->post('appointment_date')));
                $comission_amount=($this->input->post('amount') * $staff_info['opd_commission'])/100;
                $commission_data=array(
                    'staff_id'=>$doctor_id,
                    'appointment_date'=>date('Y-m-d H:i:s',strtotime($this->input->post('appointment_date'))),
                    'comission_month'=>$commission_month,
                    'comission_year'=>$commission_year,
                    'comission_amount'=>$comission_amount,
                    'commission_type'=>'OPD',
                    'commission_percentage'=>$staff_info['opd_commission'],
                    'total_amount'=>$this->input->post('amount'),

                );
                $this->db->insert('monthly_comission', $commission_data);
            }

            $p_id            = $this->patient_model->add_patient($patient_data);
            $opdn_id         = $this->patient_model->add_opd($opd_data);
            $setOPD=$this->formatNumber($opdn_id,$type='opd');
            $opd_no          = 'OPDN-'.$setOPD;
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["opd"];
            $setting_result  = $this->setting_model->getzoomsetting();
            $opdduration     = $setting_result->opd_duration;
            $status_live     = $this->lang->line('yes');

            $patientInfo=$this->common_model->getRow($p_id);
            $comments="new add opd patient where patient name is ". $patientInfo['patient_name']." MR No is ".$patientInfo['patient_unique_id']." and OPD No is ".$opd_no." and appintment date is ".date('d,M Y h:i:s A',strtotime($date));
            $activityLog=$this->common_model->saveLog('opd visit','add',$comments,$opd_no);

            if ($live_consult == $status_live) {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );

                $title = 'Online consult for OPDN' . $opdnoid;
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'     => $doctor_id,
                    'patient_id'   => $insert_id,
                    'opd_id'       => $opdn_id,
                    'title'        => $title,
                    'date'         => $date,
                    'duration'     => $opdduration,
                    'created_id'   => $this->customlib->getStaffID(),
                    'password'     => $password,
                    'api_type'     => $api_type,
                    'host_video'   => 1,
                    'client_video' => 1,
                    'purpose'      => 'consult',
                    'timezone'     => $this->customlib->getTimeZone(),
                );

                $response = $this->zoom_api->createAMeeting($insert_array);

                if (!empty($response)) {
                    if (isset($response->id)) {
                        $insert_array['return_response'] = json_encode($response);
                        $conferenceid                    = $this->conference_model->add($insert_array);

                        $sender_details = array('patient_id' => $insert_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);

                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

            $url = base_url() . $url_link . '/' . $insert_id . '/' . $opdn_id;
            //$url =  $url_link . '/' . $insert_id . '/' . $opdn_id;

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $insert_id, 'opd_id' => $opdn_id);

            if ($this->session->has_userdata("appointment_id")) {
                $appointment_id = $this->session->userdata("appointment_id");
                $updateData     = array('id' => $appointment_id, 'is_opd' => 'yes');
                $this->appointment_model->update($updateData);
                $this->session->unset_userdata('appointment_id');
            }

            $this->opdNotification($insert_id, $doctor_id, $opd_no, $url, $date);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }

            $sender_details = array('patient_id' => $insert_id, 'patient_name' => $patient_name, 'opd_no' => $opd_no, 'contact_no' => $mobileno, 'email' => $email);
            $result = $this->mailsmsconf->mailsms('opd_patient_registration', $sender_details);

        }
        echo json_encode($array);
    }



    public function patientDetails()
    {

        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }
        $id   = $this->input->post("id");
        $data = $this->patient_model->patientDetails($id);
        if (($data['dob'] == '') || ($data['dob'] == '0000-00-00') || ($data['dob'] == '1970-01-01')) {
            $data['dob'] = "";
        } else {
            $data['dob'] = date($this->customlib->getSchoolDateFormat(true, false), strtotime($data['dob']));
        }

        echo json_encode($data);
    }

    public function getPatientType()
    {
        $opd_ipd_patient_type = $this->input->post('opd_ipd_patient_type');
        $opd_ipd_no           = $this->input->post('opd_ipd_no');
        if ($opd_ipd_patient_type == 'opd') {
            if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
                access_denied();
            }
            $result = $this->patient_model->getOpdPatient($opd_ipd_no);
        } elseif ($opd_ipd_patient_type == 'ipd') {
            if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
                access_denied();
            }
            $result = $this->patient_model->getIpdPatient($opd_ipd_no);
        }
        echo json_encode($result);
    }

    public function add_revisit()
    {
        if (!$this->rbac->hasPrivilege('revisit', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant') . " " . $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('revisit_casualty', "Counter", 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'amount'            => form_error('amount'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'revisit_casualty' => form_error('revisit_casualty'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $check_patient_id = $this->patient_model->getMaxOPDId();
            if (empty($check_patient_id)) {
                $check_patient_id = 0;
            }
            $patient_id = $this->input->post('id');
            $password   = $this->input->post('password');
            $email      = $this->input->post('email');
            $mobileno   = $this->input->post('mobileno');
            $opdn_id    = $check_patient_id + 1;

            $patient_data = array(
                'id'              => $this->input->post('id'),
                'old_patient'     => $this->input->post('old_patient'),
                'known_allergies' => $this->input->post('known_allergies'),
                'organisation'    => $this->input->post('organisation_name'),
            );
            $this->patient_model->add($patient_data);
            $appointment_date = $this->input->post('appointment_date');
            $consult     = $this->input->post('live_consult');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            }else{
                $live_consult = $this->lang->line('no');
            }
            $doctor_id        = $this->input->post("consultant_doctor");
            $date             = date('Y-m-d H:i:s', $this->customlib->datetostrtotime($appointment_date));
            $next_visit='';
            if($this->input->post('next_visit')!==''){
                $next_visit= date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('next_visit')));

            }
            $setOPD=$this->formatNumber($opdn_id,$type='opd');
            $opd_data         = array(
                'patient_id'       => $this->input->post('id'),
                'appointment_date' => $date,
                'opd_no'           => 'OPDN-'.$setOPD,
                'height'           => $this->input->post('height'),
                'weight'           => $this->input->post('weight'),
                'bp'               => $this->input->post('bp'),
                'pulse'            => $this->input->post('pulse'),
                'temperature'      => $this->input->post('temperature'),
                'respiration'      => $this->input->post('respiration'),
                'case_type'        => $this->input->post('revisit_case'),
                'symptoms'         => $this->input->post('symptoms'),
                'known_allergies'  => $this->input->post('known_allergies'),
                'refference'       => $this->input->post('refference'),
                'cons_doctor'      => $this->input->post('consultant_doctor'),
                'standard_charges' => $this->input->post('standard_charge'),
                'amount'           => $this->input->post('amount'),
                'department'           => $this->input->post('department'),
                'casualty'         => $this->input->post('revisit_casualty'),
                'set_casuality'         => $this->input->post('revisit_set_casuality'),
                'payment_mode'     => $this->input->post('payment_mode'),
                'note_remark'      => $this->input->post('note_remark'),
                'next_visit'      =>  $next_visit,
                'live_consult'     => $live_consult,
                'generated_by'     => $this->session->userdata('hospitaladmin')['id'],
                'discharged'       => 'no',
            );
            $opd_id          = $this->patient_model->add_opd($opd_data);
            $patientInfo=$this->common_model->getRow($this->input->post('id'));
            $comments="revisit opd patient where patient name is ". $patientInfo['patient_name']." MR No is ".$patientInfo['patient_unique_id']." and OPD No is ".'OPDN' . $opdn_id." and appintment date is ".date('d,M Y h:i:s A',strtotime($date));
            $activityLog=$this->common_model->saveLog('revisit opd visit','add',$comments,$opdn_id);
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["opd"];
            $url             = base_url() . $url_link . '/' . $patient_id . '/' . $opd_id;
            //$url             = $url_link . '/' . $patient_id . '/' . $opd_id;
            $setting_result = $this->setting_model->getzoomsetting();
            $opdduration    = $setting_result->opd_duration;
            $status_live    = $this->lang->line('yes');
            if ($live_consult == $status_live) {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'     => $doctor_id,
                    'patient_id'   => $patient_id,
                    'opd_id'       => $opd_id,
                    'title'        => 'Online consult for Revisit OPDN' . $opdn_id,
                    'date'         => $date,
                    'duration'     => $opdduration,
                    'created_id'   => $this->customlib->getStaffID(),
                    'password'     => $password,
                    'api_type'     => $api_type,
                    'host_video'   => 1,
                    'client_video' => 1,
                    'purpose'      => 'consult',
                    'timezone'     => $this->customlib->getTimeZone(),
                );
                $response = $this->zoom_api->createAMeeting($insert_array);

                if ($response) {
                    if (isset($response->id)) {
                        $insert_array['return_response'] = json_encode($response);

                        $conferenceid   = $this->conference_model->add($insert_array);
                        $sender_details = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);

                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

            $this->opdNotification($this->input->post("id"), $this->input->post("consultant_doctor"), 'OPDN' . $opdn_id, $url, $date);

            $sender_details = array('patient_id' => $patient_id, 'opd_no' => 'OPDN' . $opdn_id, 'contact_no' => $mobileno, 'email' => $email);
            $this->mailsmsconf->mailsms('opd_patient_revisit', $sender_details);

            $array = array('status' => 'success', 'error' => '', 'id' => $opd_id, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getPatientId()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $result         = $this->patient_model->getPatientId();
        $data["result"] = $result;
        echo json_encode($result);
    }

    public function get_symptoms()
    {

        $result         = $this->symptoms_model->get();
        $data["result"] = $result;
        echo json_encode($result);
    }

    public function doctCharge()
    {

        if (!$this->rbac->hasPrivilege('doctor_charges', 'can_view')) {
            access_denied();
        }
        $doctor       = $this->input->post("doctor");
        $organisation = $this->input->post("organisation");
        $data         = $this->patient_model->doctortpaCharge($doctor, $organisation);

        echo json_encode($data);
    }

    public function doctortpaCharge()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }

        $doctor         = $this->input->post("doctor");
        $organisation   = $this->input->post("organisation");
        $charges_type   = $this->input->post("charges_type");
        $result         = $this->patient_model->doctortpaCharge($doctor, $organisation,$charges_type);
        $data['result'] = $result;
        echo json_encode($result);
    }

    public function doctName()
    {

        $doctor = $this->input->post("doctor");
        $data   = $this->patient_model->doctName($doctor);
        echo json_encode($data);
    }

    public function opdNotification($patient_id = '', $doctor_id, $opd_no = '', $url, $date)
    {

        $notification      = $this->notification;
        $notification_desc = $notification["opd_created"];
        $desc              = str_replace(array('<opdno>', '<url>'), array($opd_no, $url), $notification_desc);
        $patient_url       = $this->patient_notificationurl['opd'];
        $patient_desc      = str_replace(array('<opdno>', '<url>'), array($opd_no, base_url() . $patient_url), $notification_desc);

        if (!empty($patient_id)) {
            $notification_data = array('notification_title' => $this->lang->line('notification_opd_visit_created'),
                'notification_desc'                             => $patient_desc,
                'notification_for'                              => 'Patient',
                'notification_type'                             => 'opd',
                'receiver_id'                                   => $patient_id,
                'date'                                          => $date,
                'is_active'                                     => 'yes',
            );
            $admin_notification_data = array('notification_title' => $this->lang->line('notification_opd_visit_created'),
                'notification_desc'                                   => $desc,
                'notification_for'                                    => 'Super Admin',
                'notification_type'                                   => 'opd',
                'receiver_id'                                         => '',
                'date'                                                => $date,
                'is_active'                                           => 'yes',
            );
            $this->notification_model->addSystemNotification($notification_data);
            $this->notification_model->addSystemNotification($admin_notification_data);
        }

        if (!empty($doctor_id)) {

            $notification_data = array('notification_title' => $this->lang->line('notification_opd_visit_created'),
                'notification_desc'                             => $desc,
                'notification_for'                              => 'Doctor',
                'notification_type'                             => 'opd',
                'receiver_id'                                   => $doctor_id,
                'date'                                          => $date,
                'is_active'                                     => 'yes',
            );
            $this->notification_model->addSystemNotification($notification_data);
        }
    }

    public function opdpresNotification($patient_id = '', $doctor_id, $opd_no = '', $opd_no_value = '', $url, $visible_module)
    {

        $notification      = $this->notification;
        $notification_desc = $notification["opdpres_created"];

        $desc = str_replace(array('<opdno>', '<url>'), array($opd_no_value, $url), $notification_desc);

        $patient_url = $this->patient_notificationurl['opdpres'];
        $patient_desc      = str_replace(array('<opdno>', '<url>'), array($opd_no_value, base_url() . $patient_url), $notification_desc);

        if (!empty($patient_id)) {
            $notification_data = array('notification_title' => $this->lang->line('notification_opd_prescription_created'),
                'notification_desc'                             => $patient_desc,
                'notification_for'                              => 'Patient',
                'notification_type'                             => 'opd',
                'receiver_id'                                   => $patient_id,
                'date'                                          => date("Y-m-d H:i:s"),
                'is_active'                                     => 'yes',
            );
            $admin_notification_data = array('notification_title' => $this->lang->line('notification_opd_prescription_created'),
                'notification_desc'                                   => $desc,
                'notification_for'                                    => 'Super Admin',
                'notification_type'                                   => 'opd',
                'receiver_id'                                         => '',
                'date'                                                => date("Y-m-d H:i:s"),
                'is_active'                                           => 'yes',
            );
            $this->notification_model->addSystemNotification($notification_data);

            $this->notification_model->addSystemNotification($admin_notification_data);

            foreach ($visible_module as $key => $visible_value) {
                $role_id = $visible_value;

                $role_data = $this->role_model->getRolefromid($role_id);
                foreach ($role_data as $key => $role_value) {
                    # code...
                    $role_notification_data = array('notification_title' => $this->lang->line('notification_opd_prescription_created'),
                        'notification_desc'                                  => $desc,
                        'notification_for'                                   => $role_value["name"],
                        'notification_type'                                  => 'opd',
                        'receiver_id'                                        => $role_value["staff_id"],
                        'date'                                               => date("Y-m-d H:i:s"),
                        'is_active'                                          => 'yes',
                    );

                    $this->notification_model->addSystemNotification($role_notification_data);
                }

            }
        }

        if (!empty($doctor_id)) {
            $notification_data = array('notification_title' => $this->lang->line('notification_opd_prescription_created'),
                'notification_desc'                             => $desc,
                'notification_for'                              => 'Doctor',
                'notification_type'                             => 'opd',
                'receiver_id'                                   => $doctor_id,
                'date'                                          => date("Y-m-d H:i:s"),
                'is_active'                                     => 'yes',
            );
            $this->notification_model->addSystemNotification($notification_data);
        }
    }

    public function ipdpresNotification($patient_id = '', $doctor_id, $ipd_no = '', $ipd_no_value = '', $url, $visible_module, $pres_id = '')
    {

        $notification      = $this->notification;
        $notification_desc = $notification["ipdpres_created"];

        $desc = str_replace(array('<ipdno>', '<url>'), array($ipd_no_value, $url), $notification_desc);

        $patient_url = $this->patient_notificationurl['ipdpres'];

        $patient_desc = str_replace(array('<ipdno>', '<url>'), array($ipd_no_value, base_url() . $patient_url . '/' . $ipd_no . '/' . $pres_id), $notification_desc);

        if (!empty($patient_id)) {
            $notification_data = array('notification_title' => $this->lang->line('notification_ipd_prescription_created'),
                'notification_desc'                             => $patient_desc,
                'notification_for'                              => 'Patient',
                'notification_type'                             => 'ipd',
                'receiver_id'                                   => $patient_id,
                'date'                                          => date("Y-m-d H:i:s"),
                'is_active'                                     => 'yes',
            );

            $admin_notification_data = array('notification_title' => $this->lang->line('notification_ipd_prescription_created'),
                'notification_desc'                                   => $desc,
                'notification_for'                                    => 'Super Admin',
                'notification_type'                                   => 'ipd',
                'receiver_id'                                         => '',
                'date'                                                => date("Y-m-d H:i:s"),
                'is_active'                                           => 'yes',
            );

            $this->notification_model->addSystemNotification($notification_data);

            $this->notification_model->addSystemNotification($admin_notification_data);

            foreach ($visible_module as $key => $visible_value) {
                $role_id = $visible_value;

                $role_data = $this->role_model->getRolefromid($role_id);
                foreach ($role_data as $key => $role_value) {
                    # code...
                    $role_notification_data = array('notification_title' => $this->lang->line('notification_ipd_prescription_created'),
                        'notification_desc'                                  => $desc,
                        'notification_for'                                   => $role_value["name"],
                        'notification_type'                                  => 'ipd',
                        'receiver_id'                                        => $role_value["staff_id"],
                        'date'                                               => date("Y-m-d H:i:s"),
                        'is_active'                                          => 'yes',
                    );

                    $this->notification_model->addSystemNotification($role_notification_data);
                }

            }
        }

        if (!empty($doctor_id)) {
            $notification_data = array('notification_title' => $this->lang->line('notification_ipd_prescription_created'),
                'notification_desc'                             => $desc,
                'notification_for'                              => 'Doctor',
                'notification_type'                             => 'ipd',
                'receiver_id'                                   => $doctor_id,
                'date'                                          => date("Y-m-d H:i:s"),
                'is_active'                                     => 'yes',
            );
            $this->notification_model->addSystemNotification($notification_data);
        }
    }

    public function ipdNotification($patient_id = '', $doctor_id, $ipdno = '', $url, $date)
    {

        $notification      = $this->notification;
        $notification_desc = $notification["ipd_created"];
        $desc              = str_replace(array('<ipdno>', '<url>'), array($ipdno, $url), $notification_desc);
        $patient_url       = $this->patient_notificationurl['ipd'];
        $patient_desc      = str_replace(array('<ipdno>', '<url>'), array($ipdno, base_url() . $patient_url), $notification_desc);

        if (!empty($patient_id)) {
            $notification_data = array('notification_title' => $this->lang->line('notification_ipd_visit_created'),
                'notification_desc'                             => $patient_desc,
                'notification_for'                              => 'Patient',
                'notification_type'                             => 'ipd',
                'receiver_id'                                   => $patient_id,
                'date'                                          => $date,
                'is_active'                                     => 'yes',
            );

            $admin_notification_data = array('notification_title' =>  $this->lang->line('notification_ipd_visit_created'),
                'notification_desc'                                   => $desc,
                'notification_for'                                    => 'Super Admin',
                'notification_type'                                   => 'ipd',
                'receiver_id'                                         => '',
                'date'                                                => $date,
                'is_active'                                           => 'yes',
            );
            $this->notification_model->addSystemNotification($notification_data);
            $this->notification_model->addSystemNotification($admin_notification_data);
        }

        if (!empty($doctor_id)) {

            $notification_data = array('notification_title' =>  $this->lang->line('notification_ipd_visit_created'),
                'notification_desc'                             => $desc,
                'notification_for'                              => 'Doctor',
                'notification_type'                             => 'ipd',
                'receiver_id'                                   => $doctor_id,
                'date'                                          => $date,
                'is_active'                                     => 'yes',
            );
            $this->notification_model->addSystemNotification($notification_data);
        }
    }

    public function addpatient()
    {

        //echo "<pre>";print_r($_POST);exit;
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('mobileno', $this->lang->line('phone'), 'trim|required|regex_match[/^\d{4}-\d{7}/]');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('age', $this->lang->line('age'), 'trim|numeric|xss_clean');
        //$this->form_validation->set_rules('month', $this->lang->line('month'), 'trim|numeric|xss_clean');
        //$this->form_validation->set_rules('day', " Days ", 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('patient_cnic', 'Patient Cnic', 'trim|regex_match[/^\d{5}-\d{7}-\d$/]');

        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if($this->input->post('mobileno')=='' && $this->input->post('patient_cnic')=='')
        {
            $msg=array(
                'age_error'=>'add at least one field from phone,cnic'
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            echo json_encode($array);exit;
        }
        if($this->input->post('age')=='' && $this->input->post('month')=='' && $this->input->post('day')=='')
        {
            $msg=array(
                'age_error'=>'add at least one field from age,month and day'
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            echo json_encode($array);exit;
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                //'mobileno' => form_error('mobileno'),
                'file' => form_error('file'),
                'gender' => form_error('gender'),
                //'patient_cnic' => form_error('patient_cnic'),
                //'age' => form_error('age'),
                //'month' => form_error('month'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {


            $checkPatient=$this->patient_model->checkPatient();
            if(is_numeric($checkPatient)){
                $array = array('status' => 'fail', 'error' => $msg=array("patient"=>"patient name already exist!"), 'message' => '','patientID'=>$checkPatient);
                echo json_encode($array);exit;
            }
            $check_patient_id = $this->patient_model->getMaxId();
            if (empty($check_patient_id)) {
                $check_patient_id = 0;
            }
            $patient_id = $check_patient_id + 1;
            $dobdate    = $this->input->post('dob');
            if ($dobdate == "") {
                $dob = "";
            } else {
                $dob = date('Y-m-d', $this->customlib->datetostrtotime($dobdate));
            }
            $cnic = $this->input->post('patient_cnic');
            if ($cnic == "") {
                $p_cnic = null;
            } else {
                $p_cnic = $this->input->post('patient_cnic');
            }
            $paed=$this->input->post('paed');
            $paed=isset($paed) && !empty($paed) ? 1 :0;
            $setpatient_unique_id=$this->formatNumber($patient_id,$type='mr');
            $patient_data = array(
                'patient_name'      => $this->input->post('name'),
                'patient_cnic'      => $p_cnic,
                'mobileno'          => $this->input->post('mobileno'),
                'marital_status'    => $this->input->post('marital_status'),
                'email'             => $this->input->post('email'),
                'gender'            => $this->input->post('gender'),
                'guardian_name'     => $this->input->post('guardian_name'),
                'blood_group'       => $this->input->post('blood_group'),
                'address'           => $this->input->post('address'),
                'known_allergies'   => $this->input->post('known_allergies'),
                'patient_unique_id' => $setpatient_unique_id,
                'note'              => $this->input->post('note'),
                'age'               => $this->input->post('age'),
                'month'             => $this->input->post('month'),
                'day'             => $this->input->post('day'),
                'dob'               => $dob,
                'is_active'         => 'yes',
                'discharged'        => 'no',
                'paed'        => $paed,
            );

            $insert_id = $this->patient_model->add_patient($patient_data);
            $comments="new patient add where patient name is ".$this->input->post('name')." and Mr No is ".$setpatient_unique_id;
            $activityLog=$this->common_model->saveLog('patient','add',$comments);
            if ($this->session->has_userdata("appointment_id")) {
                $appointment_id = $this->session->userdata("appointment_id");
                $updateData     = array('id' => $appointment_id, 'patient_id' => $insert_id);
                $this->appointment_model->update($updateData);
                $this->session->unset_userdata('appointment_id');
            }
            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $data_patient_login = array(
                'username' => $this->patient_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'patient',
            );
            $this->user_model->add($data_patient_login);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $insert_id);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
            } else {
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/no_image.png');
            }
            $this->patient_model->add($data_img);

            $sender_details = array('id' => $insert_id, 'credential_for' => 'patient', 'username' => $this->patient_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $this->input->post('mobileno'), 'email' => $this->input->post('email'));

            $this->mailsmsconf->mailsms('login_credential', $sender_details);

        }
        echo json_encode($array);
    }

    public function handle_upload()
    {

        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['file']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Not Allowed");
                return false;
            }

            return true;
        }
        return true;
    }

    public function handle_csv_upload()
    {

        $image_validate = $this->config->item('filecsv_validate');

        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = filesize($_FILES['file']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_csv_upload', "File Type / Extension Not Allowed");
                return false;
            }

            return true;
        } else {
            $this->form_validation->set_message('handle_csv_upload', "File field is required");
            return false;
        }
        return true;
    }

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_patient_sample_file.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_patient_sample_file.csv';

        force_download($name, $data);
    }

    public function import()
    {
        if (!$this->rbac->hasPrivilege('patient_import', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'patient/import');

        $fields         = array('patient_name', 'guardian_name', 'gender', 'age', 'month', 'blood_group', 'marital_status', 'mobileno', 'email', 'address', 'note', 'known_allergies');
        $data["fields"] = $fields;
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_csv_upload');

        if ($this->form_validation->run() == false) {

            $this->load->view('layout/header');
            $this->load->view('admin/patient/import', $data);
            $this->load->view('layout/footer');

        } else {

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

                if ($ext == 'csv') {
                    $file   = $_FILES['file']['tmp_name'];
                    $result = $this->csvreader->parse_file($file);

                    if (!empty($result)) {
                        $check_patient_id = $this->patient_model->getMaxId();
                        if (empty($check_patient_id)) {
                            $check_patient_id = 0;
                        }
                        $patient_id = $check_patient_id + 1;

                        $count = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $patient_data[$i] = array();
                            $n                = 0;
                            foreach ($result[$i] as $key => $value) {

                                $patient_data[$i][$fields[$n]]  = $this->encoding_lib->toUTF8($result[$i][$key]);
                                $patient_data[$i]['is_active']  = 'yes';
                                $patient_data[$i]['discharged'] = 'no';
                                $patient_data[$i]['image']      = 'uploads/patient_images/no_image.png';
                                if ($i == 0) {
                                    $uniqueid = $patient_id;
                                } else {
                                    $uniqueid = $patient_id + $i;
                                }

                                $patient_data[$i]['patient_unique_id'] = '00000'.$uniqueid;
                                $n++;
                            }

                            $patient_name = $patient_data[$i]["patient_name"];

                            if (!empty($patient_name)) {
                                $insert_id = $this->patient_model->addImport($patient_data[$i]);
                            }

                            if (!empty($insert_id)) {
                                $data['csvData'] = $result;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('patients_imported_successfully') . '</div>');
                                $count++;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">Total ' . count($result) . " records found in CSV file. Total " . $count . ' records imported successfully.</div>');
                            } else {

                                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');
                            }

                            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
                            $data_patient_login = array(
                                'username' => $this->patient_login_prefix . $insert_id,
                                'password' => $user_password,
                                'user_id'  => $insert_id,
                                'role'     => 'patient',
                            );
                            $this->user_model->add($data_patient_login);

                        }
                    }
                }
                redirect('admin/patient/import');
            }
        }
    }

    public function check_medicine_exists($medicine_name, $medicine_category_id)
    {

        $this->db->where(array('medicine_category_id' => $medicine_category_id, 'medicine_name' => $medicine_name));
        $query = $this->db->join("medicine_category", "medicine_category.id = pharmacy.medicine_category_id")->get('pharmacy');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        //echo "<pre>";print_r($this->session->all_userdata());exit;
        $opd_data         = $this->session->flashdata('opd_data');
        $data['opd_data'] = $opd_data;
        $data["title"]    = 'opd_patient';
        $this->session->set_userdata('top_menu', 'OPD_Out_Patient');
        $setting                    = $this->setting_model->get();
        $data['setting']            = $setting;
        $opd_month                  = $setting[0]['opd_record_month'];
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["bloodgroup"]         = $this->blood_group;
        $data['department']             = $this->staff_model->getDepartment();
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;

        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data["doctor_select"]  = $doctorid;
        $data["dpt_select"]  = "";
        $data["disable_option"] = $disable_option;
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/search.php', $data);
        $this->load->view('layout/footer');
    }

    public function opd_search()
    {

        $draw       = $_POST['draw'];
        $row        = $_POST['start'];
        $rowperpage = $_POST['length']; // Rows display per page
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->patient_model->search_datatable($where_condition);
        $total_result = $this->patient_model->search_datatable_count($where_condition);
        $data         = array();
        $i=$row+1;
        foreach ($resultlist as $result_key => $result_value) {
            $action = "<div class='rowoptionview'>";
            if ($this->rbac->hasPrivilege('revisit', 'can_add')) {

                $action .= "<a href='#' onclick='getRevisitRecord(" . $result_value->pid . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('revisit') . "'><i class='fas fa-exchange-alt'></i></a>";
            }

            $action .= "<a href=" . base_url() . 'admin/patient/profile/' . $result_value->pid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";

            if ($result_value->is_ipd != 'yes') {
                if ($this->rbac->hasPrivilege('opd_move _patient_in_ipd', 'can_view')) {
                    $action .= "<a href=" . base_url() . 'admin/patient/moveipd/' . $result_value->pid . " data-toggle='tooltip' onclick='return confirm('" . $this->lang->line('move') . " " . $this->lang->line('patient') . " " . $this->lang->line('in') . " " . $this->lang->line('ipd') . ")' data-original-title='" . $this->lang->line('move') . " " . $this->lang->line('in') . " " . $this->lang->line('ipd') . "' class='btn btn-default btn-xs' ><i class='fas fa-share-square'></i></a>";

                }
            }
            $action .= "</div'>";
            $first_action = "<a href=" . base_url() . 'admin/patient/profile/' . $result_value->pid . ">";
            $nestedData   = array();
            $nestedData[] = $i;
            $nestedData[] = date($this->customlib->getSchoolDateFormat(true, true), strtotime($result_value->last_visit));
            $nestedData[] = $first_action . $result_value->patient_name . "</a>" . $action;
            $nestedData[] = $result_value->patient_unique_id;
            $nestedData[] = $result_value->opdno;
            $nestedData[] = $result_value->patient_cnic;
            $nestedData[] = $result_value->gender;
            $nestedData[] = $result_value->mobileno;
            $nestedData[] = $result_value->name . " " . $result_value->surname;
            $nestedData[] = $result_value->kpo_name;
            $nestedData[] = isset($result_value->opddiscount) && $result_value->opddiscount > 0 ? floor($result_value->apply_charge - $result_value->opddiscount) : floor($result_value->apply_charge);
            $nestedData[] = $result_value->total_visit;

            $data[]       = $nestedData;
            $i++;
        }

        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }

    public function getPartialsymptoms()
    {

        $sys_id              = $this->input->post('sys_id');
        $row_id              = $this->input->post('row_id');
        $sectionList         = $this->symptoms_model->getbysys($sys_id);
        $data['sectionList'] = $sectionList;
        $data['row_id']      = $row_id;
        $section_page        = $this->load->view('admin/patient/_getPartialsymptoms', $data, true);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(array(
                'status' => 1,
                'record' => $section_page,
            )));

    }

    public function getPatientList()
    {
        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        echo json_encode($patients);
    }

    public function getsymptoms()
    {
        $id               = $this->input->post('id');
        $symptoms         = $this->patient_model->getsymptoms($id);
        $data["symptoms"] = $symptoms;
        echo json_encode($symptoms);
    }

    public function ipdsearch($bedid = '', $bedgroupid = '')
    {
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
            access_denied();
        }

        $ipd_data         = $this->session->flashdata('ipd_data');
        $data['ipd_data'] = $ipd_data;

        if (!empty($bedgroupid)) {
            $data["bedid"]      = $bedid;
            $data["bedgroupid"] = $bedgroupid;
        }
        $this->session->set_userdata('top_menu', 'IPD_in_patient');
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["bloodgroup"]         = $this->blood_group;
        $data['bed_list']           = $this->bed_model->bedNoType();
        $data['floor_list']         = $this->floor_model->floor_list();
        $data['bedlist']            = $this->bed_model->bed_list();
        $data['bedgroup_list']      = $this->bedgroup_model->bedGroupFloor();
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $patients                   = $this->patient_model->getPatientListall();
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $data["patients"]           = $patients;
        $data["doctors"]            = $doctors;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $setting                = $this->setting_model->get();
        $data['setting']        = $setting;
        $data['resultlist']     = $this->patient_model->search_ipd_patients('');
        $i                      = 0;
        foreach ($data['resultlist'] as $key => $value) {
            $charges                           = $this->patient_model->getCharges($value["id"], $value["ipdid"]);
            $data['resultlist'][$i]["charges"] = $charges['charge'];
            $payment                           = $this->patient_model->getPayment($value["id"], $value["ipdid"]);
            $data['resultlist'][$i]["payment"] = $payment['payment'];
            $i++;
        }
        $data['organisation'] = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/ipdsearch.php', $data);
        $this->load->view('layout/footer');
    }

    public function ipd_search()
    {

        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->patient_model->searchipd_datatable($where_condition);
        $total_result = $this->patient_model->searchipd_datatable_count($where_condition);
        $data         = array();

        foreach ($resultlist as $result_key => $result_value) {
            $action = "<div class='rowoptionview'>";
            if ($this->rbac->hasPrivilege('consultant register', 'can_add')) {
                $action .= "<a href='#' onclick='add_instruction(" . $result_value->id . ',' . $result_value->ipdid . "),refreshmodal()' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('instruction') . "'><i class='fa fa-user-md'></i></a>";
            }

            if ($this->rbac->hasPrivilege('ipd_patient', 'can_view')) {

                $action .= "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $result_value->id . '/' . $result_value->ipdid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
            }

            $action .= "</div'>";
            $first_action = "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $result_value->id . '/' . $result_value->ipdid . ">";
            $nestedData   = array();
            $nestedData[] = $first_action . $result_value->patient_name . "</a>" . $action;
            $nestedData[] = $result_value->ipd_no;
            $nestedData[] = $result_value->patient_unique_id;
            $nestedData[] = $result_value->patient_cnic;

            $nestedData[] = $result_value->gender;
            $nestedData[] = $result_value->mobileno;
            $nestedData[] = $result_value->name . " " . $result_value->surname;
            $nestedData[] = $result_value->bed_name . "-" . $result_value->bedgroup_name . "-" . $result_value->floor_name;
            $nestedData[] = $result_value->kpo_name;
            $nestedData[] = number_format($result_value->charges, 2, '.', '');
            $nestedData[] = $result_value->payment;
            $nestedData[] = number_format($result_value->amountdue, 2, '.', '');
            $nestedData[] = $result_value->ipdcredit_limit;
            $data[]       = $nestedData;

        }
        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }

     public function discharged_search(){

            $draw = $_POST['draw'];
            $row = $_POST['start'];
            $rowperpage = $_POST['length']; // Rows display per page
            $columnIndex = $_POST['order'][0]['column']; // Column index
            $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
            $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
            $where_condition=array();
            if(!empty($_POST['search']['value'])) {
                $where_condition=array('search'=>$_POST['search']['value']);
            }
            $resultlist = $this->patient_model->searchdischarged_datatable($where_condition);
            $total_result = $this->patient_model->searchdischarged_datatable_count($where_condition);
            $data = array();

            foreach ($resultlist as $result_key => $result_value) {
                $action ="<div class='rowoptionview'>";

                $action.="<a href=".base_url().'admin/patient/ipdprofile/'.$result_value->id.'/'.$result_value->ipdid." class='btn btn-default btn-xs'  data-toggle='tooltip' title='".$this->lang->line('show')."'><i class='fa fa-reorder' aria-hidden='true'></i></a>";

            $action.="</div'>";
            $first_action ="<a href=".base_url().'admin/patient/ipdprofile/'.$result_value->id.'/'.$result_value->ipdid.">" ;
            $nestedData=array();
            $nestedData[]= $first_action.$result_value->patient_name."</a>".$action;
            $nestedData[]=$result_value->patient_unique_id;
            $nestedData[]=$result_value->gender;
            $nestedData[]=$result_value->mobileno;
            $nestedData[]=$result_value->name." ".$result_value->surname;
            $nestedData[]=date($this->customlib->getSchoolDateFormat(true, true), strtotime($result_value->date));
            $nestedData[]=date($this->customlib->getSchoolDateFormat(true, false), strtotime($result_value->discharge_date));
            $nestedData[]=number_format($result_value->charges, 2, '.', '');
            $nestedData[]=$result_value->other_charge;
            $nestedData[]=$result_value->tax;
            $nestedData[]=$result_value->discount;
            $nestedData[]=$result_value->net_amount + $result_value->payment;
            $data[] = $nestedData;

            }

            $json_data = array(
                "draw"            => intval($draw),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => intval($total_result),  // total number of records
                "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data"            => $data   // total data array
                );

    echo json_encode($json_data);  // send data as json format

    }

    public function discharged_patients()
    {
        if (!$this->rbac->hasPrivilege('discharged patients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'IPD_in_patient');
        $this->load->view('layout/header');
        $this->load->view('admin/patient/dischargedPatients.php');
        $this->load->view('layout/footer');
    }

    public function visitDetails($id, $visitid)
    {

        if (!empty($id)) {

            $result                = $this->patient_model->getDetails($id, $visitid);
            $data['result']        = $result;
            $data["id"]            = $id;
            $billstatus            = $this->patient_model->getBillstatus($result["id"], $visitid);
            $data["billstatus"]    = $billstatus;
            $data['visit_id']      = $visitid;
            $opd_details           = $this->patient_model->getOPDetails($id);
            $visit_details         = $this->patient_model->getVisitDetails($id, $visitid);
            $data['visit_details'] = $visit_details;
            $revisit_details       = $this->patient_model->getVisitDetailsByOPD($id, $visitid);
            $data['revisit_details']    = $revisit_details;
            $symptoms_resulttype        = $this->symptoms_model->getsymtype();
            $data['symptomsresulttype'] = $symptoms_resulttype;
            $doctors                    = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]            = $doctors;
            $userdata                   = $this->customlib->getUserData();
            $role_id                    = $userdata['role_id'];
            $doctorid                   = "";
            $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            $disable_option             = false;
            if ($doctor_restriction == 'enabled') {
                if ($role_id == 3) {
                    $disable_option = true;
                    $doctorid       = $userdata['id'];
                }
            }
            $staff_id                = $this->customlib->getStaffID();
            $data['logged_staff_id'] = $staff_id;
            $data['organisation']    = $this->organisation_model->get();
            $data["doctor_select"]   = $doctorid;
            $data["disable_option"]  = $disable_option;
            $data["marital_status"]  = $this->marital_status;
            $data["payment_mode"]    = $this->payment_mode;
            $data["bloodgroup"]      = $this->blood_group;
            $data["charge_type"]     = $this->charge_type;
            $diagnosis_details     = $this->patient_model->getDiagnosisDetails($id);
            $timeline_list         = $this->timeline_model->getPatientTimeline($id, $timeline_status = '');
            $data["timeline_list"] = $timeline_list;
            $data['opd_details']   = $opd_details;
            $data['diagnosis_details'] = $diagnosis_details;
            $data['medicineCategory']  = $this->medicine_category_model->getMedicineCategory();
            $data['dosage']            = $this->medicine_dosage_model->getMedicineDosage();
            $data['medicineName']      = $this->pharmacy_model->getMedicineName();
            $charges                   = $this->charge_model->getOPDCharges($id, $visitid);
            $paymentDetails            = $this->payment_model->opdPaymentDetails($id, $visitid);
            $data["charges_detail"]    = $charges;
            $data["payment_details"]   = $paymentDetails;
            $paid_amount               = $this->payment_model->getOPDPaidTotal($id, $visitid);
            $data["paid_amount"]       = $paid_amount["paid_amount"];
            $data['roles']             = $this->role_model->get();
            $data['visitconferences']  = $this->conference_model->getconfrencebyvisitopd($doctorid, $id, $visitid);
            if ($result['status'] == 'paid') {
                $generate          = $this->patient_model->getopdBillInfo($result["id"], $visitid);
                $data["bill_info"] = $generate;
            }

            $this->load->view("layout/header");
            $this->load->view("admin/patient/visitDetails", $data);
            $this->load->view("layout/footer");
        }
    }

    public function addvisitDetails()
    {
        if (!$this->rbac->hasPrivilege('revisit', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'firstname'        => form_error('name'),
                'appointment_date' => form_error('appointment_date'),
                'amount'           => form_error('amount'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $check_patient_id = $this->patient_model->getMaxOPDId();
            if (empty($check_patient_id)) {
                $check_patient_id = 0;
            }
            $opdn_id          = $check_patient_id + 1;
            $patient_id       = $this->input->post('id');
            $password         = $this->input->post('password');
            $appointment_date = $this->input->post('appointment_date');
            $consult     = $this->input->post('live_consult');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            }else{
                $live_consult = $this->lang->line('no');
            }
            $setOPD=$this->formatNumber($opdn_id,$type='opd');
            $opd_data         = array(
                'patient_id'       => $this->input->post('id'),
                'appointment_date' => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($appointment_date)),
                //'opd_no'           => $this->input->post('opd_no'),
                'opd_no'           => 'OPDN-'.$setOPD,
                'opd_id'           => $this->input->post('opd_id'),
                'height'           => $this->input->post('height'),
                'weight'           => $this->input->post('weight'),
                'bp'               => $this->input->post('bp'),
                'pulse'            => $this->input->post('pulse'),
                'temperature'      => $this->input->post('temperature'),
                'respiration'      => $this->input->post('respiration'),
                'case_type'        => $this->input->post('revisit_case'),
                'symptoms'         => $this->input->post('symptoms'),
                'known_allergies'  => $this->input->post('known_allergies'),
                'refference'       => $this->input->post('refference'),
                'cons_doctor'      => $this->input->post('consultant_doctor'),
                'amount'           => $this->input->post('amount'),
                'casualty'         => $this->input->post('casualty'),
                'payment_mode'     => $this->input->post('payment_mode'),
                'note'             => $this->input->post('note_remark'),
                'live_consult'     => $live_consult,
                'generated_by'     => $this->session->userdata('hospitaladmin')['id'],
            );
            $opd_id         = $this->patient_model->addvisitDetails($opd_data);
            $live_consult   = $this->input->post('live_consult');
            $doctor_id      = $this->input->post('consultant_doctor');
            $setting_result = $this->setting_model->getzoomsetting();
            $opdduration    = $setting_result->opd_duration;
            $status_live    = $this->lang->line('yes');
            if ($live_consult == $status_live) {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'     => $doctor_id,
                    'patient_id'   => $this->input->post('id'),
                    'opd_id'       => $this->input->post('opd_id'),
                    'title'        => 'Online consult for Recheckup ' . $this->input->post('opd_no'),
                    'date'         => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($this->input->post('appointment_date'))),
                    'duration'     => $opdduration,
                    'created_id'   => $this->customlib->getStaffID(),
                    'password'     => $password,
                    'api_type'     => $api_type,
                    'host_video'   => 1,
                    'client_video' => 1,
                    'purpose'      => 'consult',
                    //'description'  => $this->input->post('description'),
                    'timezone'     => $this->customlib->getTimeZone(),
                );
                $response = $this->zoom_api->createAMeeting($insert_array);

                if (!empty($response)) {
                    if (isset($response->id)) {
                        $insert_array['return_response'] = json_encode($response);

                        $conferenceid   = $this->conference_model->add($insert_array);
                        $sender_details = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $this->input->post('contact'), 'email' => $this->input->post('email'));

                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

            $sender_details = array('patient_id' => $patient_id, 'opd_no' => 'OPDN' . $opdn_id, 'contact_no' => $this->input->post('contact'), 'email' => $this->input->post('email'));

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function profile($id)
    {

        //echo "<pre>";print_r($this->session->all_userdata());exit;
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'OPD_Out_Patient');

        $opd_data                   = $this->session->flashdata('opd_data');
        $data['opd_data']           = $opd_data;
        $opdn_data                  = $this->session->flashdata('opdn_data');
        $data['opdn_data']          = $opdn_data;
        $opdnpres_data              = $this->session->flashdata('opdnpres_data');
        $data['opdnpres_data']      = $opdnpres_data;
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["bloodgroup"]         = $this->blood_group;
        $data['medicineCategory']   = $this->medicine_category_model->getMedicineCategory();
        $data['dosage']             = $this->medicine_dosage_model->getMedicineDosage();
        $data['medicineName']       = $this->pharmacy_model->getMedicineName();
        $data["charge_type"]        = $this->charge_type;
        $charges                    = $this->charge_model->getOPDCharges($id, '');
        $paymentDetails             = $this->payment_model->paymentDetails($id, '');
        $data["charges_detail"]     = $charges;
        $data["payment_details"]    = $paymentDetails;
        $paid_amount                = $this->payment_model->getPaidTotal($id, '');
        $data["paid_amount"]        = $paid_amount["paid_amount"];
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $data["id"]                 = $id;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['roles']          = $this->role_model->get();
        $result                 = array();
        $diagnosis_details      = array();
        $lab_reports            = array();
        $opd_details            = array();
        $timeline_list          = array();
        if (!empty($id)) {
            $result = $this->patient_model->getDetails($id);
            if ($result['status'] == 'paid') {
                $generate          = $this->patient_model->getBillInfo($result["id"]);
                $data["bill_info"] = $generate;
            }
            $opd_details       = $this->patient_model->getOPDetails($id);
            $diagnosis_details = $this->patient_model->getDiagnosisDetails($id);
            $lab_reports        = $this->patient_model->getLabInvestigations($id);
            $timeline_list     = $this->timeline_model->getPatientTimeline($id, $timeline_status = '');
        }
        $data["result"]           = $result;
        $data["diagnosis_detail"] = $diagnosis_details;
        //echo "<pre>";print_r($data["diagnosis_detail"]);exit;

        $data["lab_report"]       = $lab_reports;
        //echo "<pre>";print_r($data["lab_report"]);exit;
        $staff_id                 = $this->customlib->getStaffID();
        $data['logged_staff_id']  = $staff_id;
        $data['opdconferences']   = $this->conference_model->getconfrencebyopd($doctorid, $id);
        $data["opd_details"]      = $opd_details;
        $data["timeline_list"]    = $timeline_list;
        $data['organisation']     = $this->organisation_model->get();
        $categoryName           = $this->pathology_category_model->getcategoryName();
        $data["categoryName"] = $categoryName;
        $data['problems']=$this->common_model->getRecord($id = null,'symptoms_classification');
        $data['precautions']=$this->common_model->getRecord($id = null,'medicin_precaution');
        $this->load->view("layout/header");
        $this->load->view("admin/patient/profile", $data);
        $this->load->view("layout/footer");
    }
    public function emgprofile($id)
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'EMG_Patient');

        $opd_data                   = $this->session->flashdata('opd_data');
        $data['opd_data']           = $opd_data;
        $opdn_data                  = $this->session->flashdata('opdn_data');
        $data['opdn_data']          = $opdn_data;
        $opdnpres_data              = $this->session->flashdata('opdnpres_data');
        $data['opdnpres_data']      = $opdnpres_data;
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["bloodgroup"]         = $this->blood_group;
        $data['medicineCategory']   = $this->medicine_category_model->getMedicineCategory();
        $data['dosage']             = $this->medicine_dosage_model->getMedicineDosage();
        $data['medicineName']       = $this->pharmacy_model->getMedicineName();
        $data["charge_type"]        = $this->charge_type;
        $charges                    = $this->charge_model->getOPDCharges($id, '');
        $paymentDetails             = $this->payment_model->paymentDetails($id, '');
        $data["charges_detail"]     = $charges;
        $data["payment_details"]    = $paymentDetails;
        $paid_amount                = $this->payment_model->getPaidTotal($id, '');
        $data["paid_amount"]        = $paid_amount["paid_amount"];
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $data["id"]                 = $id;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['roles']          = $this->role_model->get();
        $result                 = array();
        $diagnosis_details      = array();
        $lab_reports            = array();
        $opd_details            = array();
        $timeline_list          = array();
        if (!empty($id)) {
            $result = $this->patient_model->getDetails($id);
            if ($result['status'] == 'paid') {
                $generate          = $this->patient_model->getBillInfo($result["id"]);
                $data["bill_info"] = $generate;
            }
            $opd_details       = $this->patient_model->getEMGDetails($id);
            $diagnosis_details = $this->patient_model->getDiagnosisDetails($id);
            $lab_reports        = $this->patient_model->getLabInvestigations($id);
            $timeline_list     = $this->timeline_model->getPatientTimeline($id, $timeline_status = '');
        }
        $data["result"]           = $result;
        $data["diagnosis_detail"] = $diagnosis_details;
        $data["lab_report"]       = $lab_reports;
        $staff_id                 = $this->customlib->getStaffID();
        $data['logged_staff_id']  = $staff_id;
        $data['opdconferences']   = $this->conference_model->getconfrencebyopd($doctorid, $id);
        $data["opd_details"]      = $opd_details;
        $data["timeline_list"]    = $timeline_list;
        $data['organisation']     = $this->organisation_model->get();
        $categoryName           = $this->pathology_category_model->getcategoryName();
        $data["categoryName"] = $categoryName;
        $this->load->view("layout/header");
        $this->load->view("admin/patient/emg_profile", $data);
        $this->load->view("layout/footer");
    }
    public function printLabInvestigations()
    {
        $id       = $this->uri->segment('4');
        $data['patient_detail']   = $this->patient_model->patientProfile($id);
        $data['lab_report']   = $this->patient_model->getLabInvestigations($id);
        $html  = $this->load->view('admin/patient/print_lab_investigations',$data, true);

        //echo '<pre>'; print_r($data);//exit;
        //echo $html;exit;
        //echo '<pre>'; print_r($data['invoice_detail']);exit;
        // Get output html
        // $html = $this->output->get_output();

        // Load pdf library
        $this->load->library('pdf');
        $customPaper = array(0,0,360,360);
        $this->dompdf->set_paper($customPaper);
        // Load HTML content
        $this->dompdf->loadHtml($html);
        ini_set('display_errors', 1);
        // Render the HTML as PDF
        $this->dompdf->render();

        // Output the generated PDF (1 = download and 0 = preview)
        $this->dompdf->stream("welcome.pdf", array("Attachment"=>0));
    }

    public function ipdprofile($id, $ipdid = '', $active = 'yes')
    {
       // echo "<pre>";print_r($this->session->all_userdata());exit;
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
            access_denied();
        }

        if ($ipdid == '') {
            $ipdresult = $this->patient_model->search_ipd_patients($searchterm = '', $active = 'yes', $discharged = 'no', $id);
            $ipdid     = $ipdresult["ipdid"];
        }

        $this->session->set_userdata('top_menu', 'IPD_in_patient');
        $ipdnpres_data              = $this->session->flashdata('ipdnpres_data');
        $setRecurring=$this->setRecurrning($id,$ipdid);

        $data['ipdnpres_data']      = $ipdnpres_data;
        $data['bed_list']           = $this->bed_model->bedNoType();
        $data['bedgroup_list']      = $this->bedgroup_model->bedGroupFloor();
        $data['medicineCategory']   = $this->medicine_category_model->getMedicineCategory();
        $data['dosage']             = $this->medicine_dosage_model->getMedicineDosage();
        $data['medicineName']       = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["bloodgroup"]         = $this->blood_group;
        $patients                   = $this->patient_model->getPatientListall();
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $data["patients"]           = $patients;
        $data['organisation']       = $this->organisation_model->get();
        $data["id"]                 = $id;
        $data["ipdid"]              = $ipdid;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $result                 = array();
        $diagnosis_details      = array();
        $opd_details            = array();
        $timeline_list          = array();
        $charges                = array();
        if (!empty($id)) {
            $result = $this->patient_model->getIpdDetails($id, $ipdid, $active);

            if ($result['status'] == 'paid') {
                $generate          = $this->patient_model->getBillInfo($result["id"]);
                $data["bill_info"] = $generate;
            }
            $diagnosis_details           = $this->patient_model->getDiagnosisDetails($id);
            $timeline_list               = $this->timeline_model->getPatientTimeline($id, $timeline_status = '');
            $prescription_details        = $this->prescription_model->getIpdPrescription($ipdid);
            $consultant_register         = $this->patient_model->getPatientConsultant($id, $ipdid);
            $charges                     = $this->charge_model->getCharges($id, $ipdid);
            $paymentDetails              = $this->payment_model->paymentDetails($id, $ipdid);
            $paid_amount                 = $this->payment_model->getPaidTotal($id, $ipdid);
            $data["paid_amount"]         = $paid_amount["paid_amount"];
            $balance_amount              = $this->payment_model->getBalanceTotal($id, $ipdid);
            $data["balance_amount"]      = $balance_amount["balance_amount"];
            $data["payment_details"]     = $paymentDetails;
            $data["consultant_register"] = $consultant_register;
            $data["result"]              = $result;
            $data["diagnosis_detail"]    = $diagnosis_details;
            $data["prescription_detail"] = $prescription_details;
            $data["opd_details"]         = $opd_details;
            $data["timeline_list"]       = $timeline_list;
            $data["charge_type"]         = $this->charge_type;
            $data["charges"]             = $charges;
            $data['roles']               = $this->role_model->get();
            $data['medicine_prescriptions']=$this->patient_model->getPateintIpdMedicine($id,'ipd');
            //echo "<pre>";print_r($data['medicine_prescription']);exit;
        }
        $staff_id                = $this->customlib->getStaffID();
        $data['logged_staff_id'] = $staff_id;
        $data['ipdconferences']  = $this->conference_model->getconfrencebyipd($doctorid, $id, $ipdid);
        $this->load->view("layout/header");
        $this->load->view("admin/patient/ipdprofile", $data);
        $this->load->view("layout/footer");
    }

    public function getsummaryDetails($id)
    {
        if (!$this->rbac->hasPrivilege('discharge_summary', 'can_view')) {
            access_denied();
        }
        $print_details         = $this->printing_model->get('', 'summary');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $result = $this->patient_model->getsummaryDetails($id);

        $data['result'] = $result;

        $this->load->view('admin/patient/printsummary', $data);
    }

     public function getopdsummaryDetails($id)
    {

        $print_details         = $this->printing_model->get('', 'summary');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $result = $this->patient_model->getsummaryopdDetails($id);

        $data['result'] = $result;

        $this->load->view('admin/patient/printopdsummary', $data);
    }

    public function patientipddetails($patient_id)
    {

        $data['resultlist'] = $this->patient_model->patientipddetails($patient_id);
        $i                  = 0;
        foreach ($data['resultlist'] as $key => $value) {
            $charges                           = $this->patient_model->getCharges($value["id"]);
            $data['resultlist'][$i]["charges"] = $charges['charge'];
            $payment                           = $this->patient_model->getPayment($value["id"]);
            $data['resultlist'][$i]["payment"] = $payment['payment'];
            $i++;
        }
        $data['organisation'] = $this->organisation_model->get();

        $this->load->view('layout/header');
        $this->load->view('admin/patient/patientipddetails.php', $data);
        $this->load->view('layout/footer');
    }

    public function deleteIpdPatientCharge($pateint_id, $id)
    {
        if (!$this->rbac->hasPrivilege('charges', 'can_delete')) {
            access_denied();
        }
        $this->charge_model->deleteIpdPatientCharge($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Patient Charges deleted successfully</div>');
        redirect('admin/patient/ipdprofile/' . $pateint_id . '#charges');
    }

    public function deleteOpdPatientCharge($pateint_id, $opdid, $id)
    {

        if (!$this->rbac->hasPrivilege('charges', 'can_delete')) {
            access_denied();
        }
        $this->charge_model->deleteOpdPatientCharge($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Patient Charges deleted successfully</div>');
        redirect('admin/patient/visitDetails/' . $pateint_id . '/' . $opdid . '#charges');
    }

    public function deleteIpdPatientConsultant($pateint_id, $id)
    {
        if (!$this->rbac->hasPrivilege('consultant register', 'can_add')) {
            access_denied();
        }
        $this->patient_model->deleteIpdPatientConsultant($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Patient Consultant deleted successfully</div>');
    }

    public function deleteIpdPatientDiagnosis($pateint_id, $id)
    {
        if (!$this->rbac->hasPrivilege('ipd diagnosis', 'can_delete')) {
            access_denied();
        }
        $this->patient_model->deleteIpdPatientDiagnosis($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Patient Diagnosis deleted successfully</div>');
        redirect('admin/patient/ipdprofile/' . $pateint_id . '#diagnosis');
    }

    public function deleteIpdPatientPayment($pateint_id, $id)
    {
        if (!$this->rbac->hasPrivilege('payment', 'can_delete')) {
            access_denied();
        }
        $this->payment_model->deleteIpdPatientPayment($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Patient Payment deleted successfully</div>');
        redirect('admin/patient/ipdprofile/' . $pateint_id . '#payment');
    }

    public function deleteOpdPatientPayment($pateint_id, $id, $opd_id)
    {
        if (!$this->rbac->hasPrivilege('payment', 'can_delete')) {
            access_denied();
        }
        $this->payment_model->deleteopdPatientPayment($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Patient Payment deleted successfully</div>');
        redirect('admin/patient/visitdetails/' . $pateint_id . '/' . $opd_id . '#payment');
    }

    public function deleteOpdPatientDiagnosis($pateint_id, $id)
    {
        if (!$this->rbac->hasPrivilege('opd diagnosis', 'can_delete')) {
            access_denied();
        }
        $this->patient_model->deleteIpdPatientDiagnosis($id);
    }

    public function report_download($doc)
    {
        $this->load->helper('download');
        $filepath = "./" . $this->uri->segment(4) . "/" . $this->uri->segment(5) . "/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $id = $this->input->post("patient_id");

        $opdid = $this->input->post("opd_id");

        $visitid = $this->input->post("visitid");

        $result = $this->patient_model->getDetails($id, $opdid);

        if ($result['symptoms']) {
            $result['symptoms'] = nl2br($result['symptoms']);
        }

        if ((!empty($visitid))) {

            $result = $this->patient_model->getpatientDetailsByVisitId($id, $visitid);
        }

        $appointment_date = date($this->customlib->getSchoolDateFormat(true, true), strtotime($result['appointment_date']));

        $result["appointment_date"] = $appointment_date;

        echo json_encode($result);
    }

      public function getopdDetailsSummary()
    {
        // if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
        //     access_denied();
        // }
        $id = $this->input->post("patient_id");
        $opdid = $this->input->post("opd_id");
        $visitid = $this->input->post("visitid");
        $result = $this->patient_model->getDetails($id, $opdid);
        $appointment_date = date($this->customlib->getSchoolDateFormat(true, true), strtotime($result['appointment_date']));
        $discharge_date = date($this->customlib->getSchoolDateFormat(true, false), strtotime($result['discharge_date']));
        $result["appointment_date"] = $appointment_date;
        $result["discharge_date"] = $discharge_date;

        echo json_encode($result);
    }

    public function getpatientDetails()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }

        $id = $this->input->post("id");

        $result = $this->patient_model->getpatientDetails($id);
        if (($result['dob'] == '') || ($result['dob'] == '0000-00-00') || ($result['dob'] == '1970-01-01')) {
            $result['dob'] = "";
        } else {
            $result['dob'] = date($this->customlib->getSchoolDateFormat(true, false), strtotime($result['dob']));
        }

        echo json_encode($result);
    }

    public function getIpdDetails()
    {
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post("recordid");
        $ipdid  = $this->input->post("ipdid");
        $active = $this->input->post("active");
        $result = $this->patient_model->getIpdDetails($id, $ipdid, $active);
        if ($result['symptoms']) {
            $result['symptoms']  = $result['symptoms'];
            $result['vsymptoms'] = nl2br($result['symptoms']);
        }
        $result['dob']            = date($this->customlib->getSchoolDateFormat(true, false), strtotime($result['dob']));
        $result['date']           = date($this->customlib->getSchoolDateFormat(true, true), strtotime($result['date']));
        $result['discharge_date'] = date($this->customlib->getSchoolDateFormat(true, false), strtotime($result['discharge_date']));
        echo json_encode($result);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_edit')) {
            access_denied();
        }
        $patient_type = $this->customlib->getPatienttype();
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'file' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            if($this->input->post('mobileno')=='' && $this->input->post('patient_cnic')=='')
            {
                $msg=array(
                    'age_error'=>'add at least one field from phone,cnic'
                );
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                echo json_encode($array);exit;
            }
            if($this->input->post('age')=='' && $this->input->post('month')=='' && $this->input->post('day')=='')
            {
                $msg=array(
                    'age_error'=>'add at least one field from age,month and day'
                );
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                echo json_encode($array);exit;
            }


            $id           = $this->input->post('updateid');
            $dobdate      = $this->input->post('dob');
            $dob          = date("Y-m-d", $this->customlib->datetostrtotime($dobdate));
            $paed=$this->input->post('paed');
            $paed=isset($paed) && !empty($paed) ? 1 :0;
            $patient_data = array(
                'id'              => $this->input->post('updateid'),
                'patient_name'    => $this->input->post('name'),
                'patient_cnic'    => $this->input->post('patient_cnic'),
                'mobileno'        => $this->input->post('contact'),
                'marital_status'  => $this->input->post('marital_status'),
                'blood_group'     => $this->input->post('blood_group'),
                'email'           => $this->input->post('email'),
                'dob'             => $dob,
                'gender'          => $this->input->post('gender'),
                'guardian_name'   => $this->input->post('guardian_name'),
                'address'         => $this->input->post('address'),
                'note'            => $this->input->post('note'),
                'age'             => $this->input->post('age'),
                'month'           => $this->input->post('month'),
                'day'           => $this->input->post('day'),
                'organisation'    => $this->input->post('organisation'),
                'known_allergies' => $this->input->post('known_allergies'),
                'credit_limit'    => $this->input->post('credit_limit'),
                'paed'    => $paed,
                //'is_active' => 'yes',
            );

            $this->patient_model->add($patient_data);
            // String of all alphanumeric character
            $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            // Shufle the $str_result and returns substring
            // of specified length
            $alfa_no = substr(str_shuffle($str_result), 0, 5);
            $array   = array('status' => 'success', 'error' => '', 'message' => "Record Updated Successfully");
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $alfa_no . "_" . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/patient_images/' . $img_name);

                $this->patient_model->add($data_img);
            }
        }

        echo json_encode($array);
    }

    public function deactivePatient()
    {

        if (!$this->rbac->hasPrivilege('patient_deactive', 'can_edit')) {
            access_denied();
        }

        $id           = $this->input->post('id');

        $patient_data = array(
            'id'        => $id,
            'is_active' => 'no',
        );
        $chekpatient = $this->patient_model->checkpatientipddis($id);

      if ($chekpatient) {
         $msg = $this->lang->line('patient_already_in_ipd');
         $sts = 'fail';
      }else{
        $this->patient_model->add($patient_data);
        $this->user_model->updateUser($id, 'no');
        $sts = 'success';
        $msg = "Record Deactive";
      }

        $array = array('status' =>  $sts, 'error' => '', 'message' => $msg);
        echo json_encode($array);
    }

    public function activePatient()
    {
        if (!$this->rbac->hasPrivilege('patient_active', 'can_edit')) {
            access_denied();
        }
        $id = $this->input->post('activeid');

        $patientact_data = array(
            'id'        => $id,
            'is_active' => 'yes',
        );

        $this->patient_model->add_patient($patientact_data);
        $this->user_model->updateUser($id, 'yes');
        $array = array('status' => 'success', 'error' => '', 'message' => "Record Active");
        echo json_encode($array);
    }

    public function ipd_update()
    {
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_edit')) {
            access_denied();
        }
        $patient_type = $this->customlib->getPatienttype();

        $this->form_validation->set_rules('cons_doctor', $this->lang->line('consultant') . " " . $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('admission') . " " . $this->lang->line('date'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patients_id'      => form_error('patients_id'),
                'cons_doctor'      => form_error('cons_doctor'),
                'appointment_date' => form_error('appointment_date'),
                'file'             => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id               = $this->input->post('updateid');
            $appointment_date = $this->input->post('appointment_date');
            $patientid        = $this->input->post('patient_id');
            $previous_bed_id  = $this->input->post('previous_bed_id');
            $current_bed_id   = $this->input->post('bed_no');
            if ($previous_bed_id != $current_bed_id) {
                $beddata = array('id' => $previous_bed_id, 'is_active' => 'yes');
                $this->bed_model->savebed($beddata);
            }
            $ipd_data = array(
                'id'              => $this->input->post('ipdid'),
                'patient_id'      => $patientid,
                'date'            => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($appointment_date)),
                'bed'             => $this->input->post('bed_no'),
                'bed_group_id'    => $this->input->post('bed_group_id'),
                'height'          => $this->input->post('height'),
                'bp'              => $this->input->post('bp'),
                'weight'          => $this->input->post('weight'),
                'pulse'           => $this->input->post('pulse'),
                'temperature'     => $this->input->post('temperature'),
                'respiration'     => $this->input->post('respiration'),
                'case_type'       => $this->input->post('case_type'),
                'symptoms'        => $this->input->post('symptoms'),
                'known_allergies' => $this->input->post('known_allergies'),
                'refference'      => $this->input->post('refference'),
                'cons_doctor'     => $this->input->post('cons_doctor'),
                'casualty'        => $this->input->post('casualty'),
                'note'            => $this->input->post('note'),
                'credit_limit'    => $this->input->post('credit_limit'),
            );
            $bed_data = array('id' => $this->input->post('bed_no'), 'is_active' => 'no');
            $this->bed_model->savebed($bed_data);
            $ipd_id = $this->patient_model->add_ipd($ipd_data);

            $patient_data = array('id' => $id, 'organisation' => $this->input->post('organisation'), 'old_patient' => $this->input->post('old_patient'));
            $this->patient_model->add($patient_data);

            $array = array('status' => 'success', 'error' => '', 'message' => "Patient Updated Successfully");
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }
        }
        echo json_encode($array);
    }

    public function add_discharged_summary()
    {

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient') . " " . $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id' => form_error('patients_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $patientid  = $this->input->post('patient_id');
            $updated_id = $this->input->post('updateid');
            $ipd_id     = $this->input->post('ipdid');
            if (!empty($updated_id)) {
                $summary_dataupdate = array(
                    'id'             => $updated_id,
                    'ipd_id'         => $ipd_id,
                    'patient_id'     => $patientid,
                    'note'           => $this->input->post('note'),
                    'diagnosis'      => $this->input->post('diagnosis'),
                    'operation'      => $this->input->post('operation'),
                    'investigations' => $this->input->post('investigations'),
                    'treatment_home' => $this->input->post('treatment_at_home'),
                );
                $summary_id = $this->patient_model->add_disch_summary($summary_dataupdate);
            } else {
                $summary_data = array(
                    'ipd_id'         => $ipd_id,
                    'patient_id'     => $patientid,
                    'note'           => $this->input->post('note'),
                    'diagnosis'      => $this->input->post('diagnosis'),
                    'operation'      => $this->input->post('operation'),
                    'investigations' => $this->input->post('investigations'),
                    'treatment_home' => $this->input->post('treatment_at_home'),
                );
                $summary_id = $this->patient_model->add_disch_summary($summary_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => "Patient Updated Successfully");

        }
        echo json_encode($array);
    }

    public function add_opddischarged_summary()
    {

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient') . " " . $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id' => form_error('patient_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $patientid  = $this->input->post('patient_id');
            $updated_id = $this->input->post('updateid');
            $opd_id     = $this->input->post('opdid');

            if (!empty($updated_id)) {
                $summary_dataupdate = array(
                    'id'             => $updated_id,
                    'opd_id'         => $opd_id,
                    'patient_id'     => $patientid,
                    'note'           => $this->input->post('note'),
                    'diagnosis'      => $this->input->post('diagnosis'),
                    'operation'      => $this->input->post('operation'),
                    'investigations' => $this->input->post('investigations'),
                    'treatment_home' => $this->input->post('treatment_at_home'),
                );
                $summary_id = $this->patient_model->add_dischopd_summary($summary_dataupdate);
            } else {
                $summary_data = array(

                    'opd_id'         => $opd_id,
                    'patient_id'     => $patientid,
                    'note'           => $this->input->post('note'),
                    'diagnosis'      => $this->input->post('diagnosis'),
                    'operation'      => $this->input->post('operation'),
                    'investigations' => $this->input->post('investigations'),
                    'treatment_home' => $this->input->post('treatment_at_home'),
                );

                $summary_id = $this->patient_model->add_dischopd_summary($summary_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => "Patient Updated Successfully");

        }
        echo json_encode($array);
    }


    public function opd_detail_update()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_edit')) {
            access_denied();
        }
        $id = $this->input->post('opdid');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant') . " " . $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('opdid', $this->lang->line('opd') . " " . $this->lang->line('id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == true) {
            $appointment_date = $this->input->post('appointment_date');
            $next_visit='';
            if($this->input->post('next_visit')!==''){
                $next_visit= date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('next_visit')));

            }
            $visitid      = $this->input->post("visitid");
            $patient_data = array('id' => $this->input->post('patientid'),
                'organisation'             => $this->input->post('organisation'),
                'old_patient'              => $this->input->post('old_patient'),
            );
            if (!empty($visitid)) {
                $opd_data = array(
                    'id'               => $this->input->post('visitid'),
                    'appointment_date' => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($appointment_date)),
                    'case_type'        => $this->input->post('case'),
                    'symptoms'         => $this->input->post('symptoms'),
                    'refference'       => $this->input->post('refference'),
                    'cons_doctor'      => $this->input->post('consultant_doctor'),
                    'amount'           => $this->input->post('amount'),
                    'bp'               => $this->input->post('bp'),
                    'height'           => $this->input->post('height'),
                    'weight'           => $this->input->post('weight'),
                    'pulse'            => $this->input->post('pulse'),
                    'temperature'      => $this->input->post('temperature'),
                    'respiration'      => $this->input->post('respiration'),
                    'tax'              => $this->input->post('tax'),
                    'casualty'         => $this->input->post('casualty'),
                    'payment_mode'     => $this->input->post('payment_mode'),
                    'note_remark'      => $this->input->post('revisit_note'),
                    //'discharged'       => 'no'
                );
                $opd_id = $this->patient_model->addvisitDetails($opd_data);
            } else {
                $opd_data = array(
                    'id'               => $this->input->post('opdid'),
                    'appointment_date' => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($appointment_date)),
                    'case_type'        => $this->input->post('case'),
                    'symptoms'         => $this->input->post('symptoms'),
                    'refference'       => $this->input->post('refference'),
                    'cons_doctor'      => $this->input->post('consultant_doctor'),
                    'amount'           => $this->input->post('amount'),
                    'bp'               => $this->input->post('bp'),
                    'height'           => $this->input->post('height'),
                    'weight'           => $this->input->post('weight'),
                    'pulse'            => $this->input->post('pulse'),
                    'temperature'      => $this->input->post('temperature'),
                    'respiration'      => $this->input->post('respiration'),
                    'tax'              => $this->input->post('tax'),
                    'casualty'         => $this->input->post('casualty'),
                    'payment_mode'     => $this->input->post('payment_mode'),
                    'note_remark'      => $this->input->post('revisit_note'),
                    'next_visit'      =>  $next_visit,
                    //'discharged'       => 'no',
                );

                $opd_id = $this->patient_model->add_opd($opd_data);

                $patientInfo=$this->common_model->getRowOpd($opd_id);
                $comments="update opd visit from opd where opd number is ". $patientInfo['opd_no']." and appointment date is ".date('d,M Y h:i:s A', $this->customlib->datetostrtotime($appointment_date));
                $activityLog=$this->common_model->saveLog('opd','update',$comments,$patientInfo['opd_no']);

            }

            $this->patient_model->add($patient_data);

            $array = array('status' => 'success', 'error' => '', 'message' => "Record Updated Successfully");
        } else {

            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'opdid'             => form_error('opdid'),
                'amount'            => form_error('amount'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        }
        echo json_encode($array);
    }

    public function opd_details()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post("recordid");
        $opdid  = $this->input->post("opdid");
        $result = $this->patient_model->getOPDetails($id, $opdid);

        if (!empty($result['appointment_date'])) {
            $appointment_date           = date($this->customlib->getSchoolDateFormat(true, true), strtotime($result['appointment_date']));
            $result["appointment_date"] = $appointment_date;
        }

        echo json_encode($result);
    }

    public function editvisitdetails()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $id      = $this->input->post("recordid");
        $visitid = $this->input->post("visitid");
        if ((!empty($visitid))) {

            $result = $this->patient_model->getpatientDetailsByVisitId($id, $visitid);
        }

        if (!empty($result['appointment_date'])) {
            $appointment_date           = date($this->customlib->getSchoolDateFormat(true, true), strtotime($result['appointment_date']));
            $result["appointment_date"] = $appointment_date;
        }
        echo json_encode($result);
    }

    public function editDiagnosis()
    {

        if (!$this->rbac->hasPrivilege('opd diagnosis', 'can_edit')) {
            access_denied();
        }
        $id                    = $this->input->post("id");
        $result                = $this->patient_model->geteditDiagnosis($id);
        $result["report_date"] = date($this->customlib->getSchoolDateFormat(true, false), strtotime($result['report_date']));
        echo json_encode($result);
    }

    public function editTimeline()
    {

        if (!$this->rbac->hasPrivilege('opd timeline', 'can_edit')) {
            access_denied();
        }
        $id     = $this->input->post("id");
        $result = $this->timeline_model->geteditTimeline($id);

        echo json_encode($result);
    }

    public function editstaffTimeline()
    {

        if (!$this->rbac->hasPrivilege('staff_timeline', 'can_view')) {
            access_denied();
        }
        $id = $this->input->post("id");

        $result = $this->timeline_model->geteditstaffTimeline($id);

        echo json_encode($result);
    }

    public function add_diagnosis()
    {
        $this->form_validation->set_rules('report_type', $this->lang->line('report') . " " . $this->lang->line('type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'report_type' => form_error('report_type'),
                'description' => form_error('description'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $report_date = $this->input->post('report_date');
            $data        = array(
                'report_type' => $this->input->post("report_type"),
                'report_date' => date('Y-m-d', $this->customlib->datetostrtotime($report_date)),
                'patient_id'  => $this->input->post("patient"),
                'description' => $this->input->post("description"),
            );
            $insert_id = $this->patient_model->add_diagnosis($data);
            if (isset($_FILES["report_document"]) && !empty($_FILES['report_document']['name'])) {
                $fileInfo = pathinfo($_FILES["report_document"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["report_document"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'document' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add_diagnosis($data_img);
            }
            $append_prescription_diagnosis=array();
            if($insert_id && $this->input->post('append_prescription_diagnosis')=='append_prescription_diagnosis'){
                $append_prescription_diagnosis=array(
                    'id'=>$insert_id,
                    'append_prescription_diagnosis'=>$this->input->post("report_type"),
                );
            }

            $array = array('status' => 'success', 'error' => '', 'message' => 'Record Added Successfully.','append_prescription_diagnosis'=>$append_prescription_diagnosis);
        }
        echo json_encode($array);
    }

    public function update_diagnosis()
    {
        $this->form_validation->set_rules('report_type', $this->lang->line('report') . " " . $this->lang->line('type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'report_type' => form_error('report_type'),
                'description' => form_error('description'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $report_date = $this->input->post('report_date');
            $id          = $this->input->post('diagnosis_id');
            $patientid   = $this->input->post("diagnosispatient_id");
            $this->load->library('Customlib');
            $data = array(
                'id'          => $id,
                'report_type' => $this->input->post("report_type"),
                'report_date' => date('Y-m-d', $this->customlib->datetostrtotime($report_date)),
                'patient_id'  => $patientid,
                'description' => $this->input->post("description"),
            );
            $insert_id = $this->patient_model->add_diagnosis($data);
            if (isset($_FILES["report_document"]) && !empty($_FILES['report_document']['name'])) {
                $fileInfo = pathinfo($_FILES["report_document"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["report_document"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $id, 'document' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add_diagnosis($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => 'Record Added Successfully.');
        }
        echo json_encode($array);
    }

    public function add_prescription()
    {

        if (!$this->rbac->hasPrivilege('prescription', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('medicine_cat[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('opd_no', $this->lang->line('opd_no'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('symptom[]', $this->lang->line('symptom'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('diagnosis[]', $this->lang->line('diagnosis'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('lab_test[]', $this->lang->line('lab_test'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('precaution[]', "PRecaution", 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(

                'medicine_cat' => form_error('medicine_cat[]'),
                'opd_no'       => form_error('opd_no'),
                //'symptom'       => form_error('symptom[]'),
                //'diagnosis'       => form_error('diagnosis[]'),
                //'lab_test'       => form_error('lab_test[]'),
               // 'precaution'       => form_error('precaution[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $opd_id       = $this->input->post('opd_no');
            $opd_no_value = $this->input->post('opd_no_value');

            if (!empty($opd_id)) {
                $opd_details = $this->patient_model->getopddetailspres($opd_id);
            }
            $next_visit='';
            if($this->input->post('next_visit_prescription')!==''){
                $next_visit= date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('next_visit_prescription')));
                $updateOPd=array(
                    'next_visit'=>$next_visit
                );
                $this->db->where('id',$opd_id );
                $this->db->update('opd_details',$updateOPd);

            }

            $symptom = $this->input->post("symptom");
            isset($symptom) && !empty($symptom) ? $symptom=implode(",", $symptom):NULL;
            $diagnosis = $this->input->post("diagnosis");
            isset($diagnosis) && !empty($diagnosis) ? $diagnosis=implode(",", $diagnosis):NULL;
            $lab_test = $this->input->post("lab_test");
            isset($lab_test) && !empty($lab_test) ? $lab_test=implode(",", $lab_test):NULL;
            $precaution = $this->input->post("precaution");
            isset($precaution) && !empty($precaution) ? $precaution=implode(",", $precaution):NULL;
            $visible_module = $this->input->post("visible[]");
            $visit_id       = $this->input->post('visit_id');
            $medicine       = $this->input->post("medicine[]");
            $medicine_cat   = $this->input->post("medicine_cat[]");
            $dosage         = $this->input->post("dosage[]");
            $instruction    = $this->input->post("instruction[]");
            //$header_note    = $this->input->post("header_note");
            //$footer_note    = $this->input->post("footer_note");
            $data_array     = array();
            $i              = 0;
            foreach ($medicine as $key => $value) {
                $inst               = '';
                $do                 = '';
                $medicine_cat_value = '';
                if (!empty($dosage[$i])) {
                    $do = $dosage[$i];
                }
                if (!empty($instruction[$i])) {
                    $inst = $instruction[$i];
                }

                if (!empty($medicine_cat[$i])) {
                    $medicine_cat_value = $medicine_cat[$i];
                }
                $data         = array('opd_id' => $opd_id,'visit_id' => $visit_id, 'medicine' => $value, 'medicine_category_id' => $medicine_cat_value, 'dosage' => $do, 'instruction' => $inst);
                $data_array[] = $data;
                $i++;
            }

            if ($visit_id > 0) {
                $opdvisit_array = array('id' => $visit_id, 'header_note' => $header_note, 'footer_note' => $footer_note);
                $this->patient_model->add_opdvisit($opdvisit_array);

            }
            //  else {
            //     $opd_array = array('id' => $opd_id, 'header_note' => $header_note, 'footer_note' => $footer_note);
            //     $this->patient_model->add_opd($opd_array);
            // }
            //$updated_opd_id=$this->common_model->getRecords();

            $this->patient_model->add_prescription($data_array);
            $prescription_medical=array('opd_id'=>$opd_id,'lab_test'=>$lab_test,'precaution'=>$precaution,'diagnosis'=> $diagnosis,'symptom'=>$symptom,'prescription_note'=>$this->input->post('note_instruction'));
            $this->db->insert('prescription_medical', $prescription_medical);

            $insert_id       = $opd_details['patient_id'];
            $doctor_id       = $opd_details['staff_id'];
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["opdpres"];
            $url             = base_url() . $url_link . '/' . $insert_id . '/' . $opd_id;
           // $url             = $url_link . '/' . $insert_id . '/' . $opd_id;

            $this->opdpresNotification($insert_id, $doctor_id, $opd_id, $opd_no_value, $url, $visible_module);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);

    }

    public function add_ipdprescription()
    {
        if (!$this->rbac->hasPrivilege('ipd_prescription', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('medicine_cat[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('ipd_no', $this->lang->line('ipd_no') . " " . $this->lang->line('category'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(

                'medicine_cat' => form_error('medicine_cat[]'),
                'ipd_no'       => form_error('ipd_no'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $ipd_id       = $this->input->post('ipd_no');
            $ipd_no_value = $this->input->post('ipd_no_value');

            if (!empty($ipd_id)) {
                $ipd_details = $this->patient_model->getipddetailspres($ipd_id);
            }

            $visit_id        = 1;
            $medicine        = $this->input->post("medicine[]");
            $medicine_cat    = $this->input->post("medicine_cat[]");
            $dosage          = $this->input->post("dosage[]");
            $instruction     = $this->input->post("instruction[]");
            $header_note     = $this->input->post("header_note");
            $footer_note     = $this->input->post("footer_note");
            $visible_module  = $this->input->post("visible[]");
            $data_array      = array();
            $ipd_basic_array = array('ipd_id' => $ipd_id, 'header_note' => $header_note, 'footer_note' => $footer_note, 'date' => date("Y-m-d"));
            $basic_id        = $this->prescription_model->add_ipdprescriptionbasic($ipd_basic_array);

            $i = 0;
            foreach ($medicine as $key => $value) {
                $inst               = '';
                $do                 = '';
                $medicine_cat_value = '';
                if (!empty($dosage[$i])) {
                    $do = $dosage[$i];
                }
                if (!empty($instruction[$i])) {
                    $inst = $instruction[$i];
                }

                if (!empty($medicine_cat[$i])) {
                    $medicine_cat_value = $medicine_cat[$i];
                }

                $data = array('basic_id' => $basic_id, 'ipd_id' => $ipd_id, 'medicine' => $value, 'dosage' => $do, 'medicine_category_id' => $medicine_cat_value, 'instruction' => $inst);

                $insert_id       = $ipd_details['patient_id'];
                $doctor_id       = $ipd_details['staff_id'];
                $notificationurl = $this->notificationurl;
                $url_link        = $notificationurl["ipdpres"];
                $url             = base_url() . $url_link . '/' . $insert_id . '/' . $ipd_id . '/' . $basic_id;
                //$url             =  $url_link . '/' . $insert_id . '/' . $ipd_id . '/' . $basic_id;
                $this->ipdpresNotification($insert_id, $doctor_id, $ipd_id, $ipd_no_value, $url, $visible_module, $basic_id);

                $data_array[] = $data;
                $i++;
            }

            $this->prescription_model->add_ipdprescriptiondetail($data_array);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function update_prescription()
    {
        if (!$this->rbac->hasPrivilege('prescription', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('opd_id', $this->lang->line('opd_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('symptom[]', $this->lang->line('symptom'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('diagnosis[]', $this->lang->line('diagnosis'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('lab_test[]', $this->lang->line('lab_test'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('precaution[]', "Precaution", 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'opd_id' => form_error('opd_id'),
                'symptom'       => form_error('symptom[]'),
                'diagnosis'       => form_error('diagnosis[]'),
                'lab_test'       => form_error('lab_test[]'),
                'precaution'       => form_error('precaution[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $opd_id           = $this->input->post('opd_id');
            $visit_id         = $this->input->post('visit_id');
            $medicine         = $this->input->post("medicine[]");
            $medicine_cat     = $this->input->post("medicine_cat[]");
            $prescription_id  = $this->input->post("prescription_id[]");
            $previous_pres_id = $this->input->post("previous_pres_id[]");

            $dosage      = $this->input->post("dosage[]");
            $instruction = $this->input->post("instruction[]");

            $next_visit='';
            if($this->input->post('next_visit_prescription')!==''){
                $next_visit= date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('next_visit_prescription')));
                $updateOPd=array(
                    'next_visit'=>$next_visit
                );
                $this->db->where('id',$opd_id );
                $this->db->update('opd_details',$updateOPd);

            }
            $symptom = $this->input->post("symptom");
            isset($symptom) && !empty($symptom) ? $symptom=implode(",", $symptom):NULL;
            $diagnosis = $this->input->post("diagnosis");
            isset($diagnosis) && !empty($diagnosis) ? $diagnosis=implode(",", $diagnosis):NULL;
            $lab_test = $this->input->post("lab_test");
            isset($lab_test) && !empty($lab_test) ? $lab_test=implode(",", $lab_test):NULL;
            $precaution = $this->input->post("precaution");
            isset($precaution) && !empty($precaution) ? $precaution=implode(",", $precaution):NULL;
            $visible_module = $this->input->post("visible[]");
            $visit_id       = $this->input->post('visit_id');
            $medicine       = $this->input->post("medicine[]");
            $medicine_cat   = $this->input->post("medicine_cat[]");
            $dosage         = $this->input->post("dosage[]");
            $instruction    = $this->input->post("instruction[]");

            $data_array = array();
            $delete_arr = array();

            if (!empty($previous_pres_id)) {
                foreach ($previous_pres_id as $pkey => $pvalue) {
                    if (in_array($pvalue, $prescription_id)) {

                    } else {
                        $delete_arr[] = array('id' => $pvalue);
                    }
                }
            }

            $visible_module = $this->input->post("visible");

            $opd_no_value = $this->input->post('opd_no_value');

            if (!empty($opd_id)) {
                $opd_details = $this->patient_model->getopddetailspres($opd_id);
            }

            $insert_id = $opd_details["patient_id"];
            $doctor_id = $opd_details["staff_id"];

            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["opdpres"];
            $url             = base_url() . $url_link . '/' . $insert_id . '/' . $opd_id;
            $this->opdpresNotification($insert_id, $doctor_id, $opd_id, $opd_no_value, $url, $visible_module);



            $i = 0;
            foreach ($medicine as $key => $value) {
                $inst               = '';
                $do                 = '';
                $medicine_cat_value = '';
                if (!empty($dosage[$i])) {
                    $do = $dosage[$i];
                }

                if (!empty($instruction[$i])) {
                    $inst = $instruction[$i];
                }
                if (!empty($medicine_cat[$i])) {
                    $medicine_cat_value = $medicine_cat[$i];
                }
                if ($prescription_id[$i] == 0) {
                    $add_data = array('opd_id' => $opd_id, 'visit_id' => $visit_id, 'medicine' => $value, 'medicine_category_id' => $medicine_cat_value, 'dosage' => $do, 'instruction' => $inst);

                    $data_array[] = $add_data;
                } else {

                    $update_data = array('id' => $prescription_id[$i], 'medicine_category_id' => $medicine_cat_value, 'opd_id' => $opd_id, 'medicine' => $value, 'dosage' => $do, 'instruction' => $inst);

                    $this->prescription_model->update_prescription($update_data);
                }

                $i++;
            }
            if ($visit_id > 0) {
                $opdvisit_array = array('id' => $visit_id, 'header_note' => $header_note, 'footer_note' => $footer_note);
                $this->patient_model->add_opdvisit($opdvisit_array);
            } else {
                $opd_array = array('id' => $opd_id, 'header_note' => $header_note, 'footer_note' => $footer_note);
                $this->patient_model->add_opd($opd_array);
            }

            if (!empty($data_array)) {
                $this->patient_model->add_prescription($data_array);
            }
            if (!empty($delete_arr)) {

                $this->prescription_model->delete_prescription($delete_arr);
            }
            $prescription_medical=array('lab_test'=>$lab_test,'precaution'=>$precaution,'diagnosis'=> $diagnosis,'symptom'=>$symptom);
            $this->db->where('opd_id',$opd_id);
            $this->db->update('prescription_medical', $prescription_medical);
            $array = array('status' => 'success', 'error' => '', 'message' => 'Prescription Added Successfully');
        }
        echo json_encode($array);
    }

    public function update_ipdprescription()
    {
        if (!$this->rbac->hasPrivilege('prescription', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('ipd_id', $this->lang->line('ipd_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_cat[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'ipd_id'       => form_error('ipd_id'),
                'medicine_cat' => form_error('medicine_cat[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $ipd_id   = $this->input->post('ipd_id');
            $visit_id = $this->input->post('visit_id');

            $visible_module = $this->input->post("visible");

            $ipd_no_value = $this->input->post('ipd_no_value');

            if (!empty($ipd_id)) {
                $ipd_details = $this->patient_model->getipddetailspres($ipd_id);
            }

            $insert_id = $ipd_details["patient_id"];
            $doctor_id = $ipd_details["staff_id"];

            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["ipdpres"];
            $url             = base_url() . $url_link . '/' . $insert_id . '/' . $ipd_id;
            // $url             = $url_link . '/' . $insert_id . '/' . $ipd_id;

            $this->ipdpresNotification($insert_id, $doctor_id, $ipd_id, $ipd_no_value, $url, $visible_module);

            $medicine         = $this->input->post("medicine[]");
            $medicine_cat     = $this->input->post("medicine_cat[]");
            $prescription_id  = $this->input->post("prescription_id[]");
            $previous_pres_id = $this->input->post("previous_pres_id[]");

            $dosage = $this->input->post("dosage[]");

            $instruction = $this->input->post("instruction[]");
            $header_note = $this->input->post("header_note");
            $footer_note = $this->input->post("footer_note");

            $data_array = array();
            $delete_arr = array();
            foreach ($previous_pres_id as $pkey => $pvalue) {
                if (in_array($pvalue, $prescription_id)) {

                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $i = 0;
            foreach ($medicine as $key => $value) {
                $inst               = '';
                $do                 = '';
                $medicine_cat_value = '';
                if (!empty($dosage[$i])) {
                    $do = $dosage[$i];
                }
                if (!empty($instruction[$i])) {
                    $inst = $instruction[$i];
                }
                if (!empty($medicine_cat[$i])) {
                    $medicine_cat_value = $medicine_cat[$i];
                }
                if ($prescription_id[$i] == 0) {
                    $add_data = array('ipd_id' => $ipd_id, 'basic_id' => $visit_id, 'medicine' => $value, 'medicine_category_id' => $medicine_cat_value, 'dosage' => $do, 'instruction' => $inst);

                    $data_array[] = $add_data;
                } else {

                    $update_data = array('id' => $prescription_id[$i], 'medicine_category_id' => $medicine_cat_value, 'ipd_id' => $ipd_id, 'medicine' => $value, 'dosage' => $do, 'instruction' => $inst);
                    $this->prescription_model->update_ipdprescription($update_data);
                }
                $i++;
            }

            $ipd_array = array('id' => $visit_id, 'header_note' => $header_note, 'footer_note' => $footer_note);

            if (!empty($data_array)) {
                $this->patient_model->add_ipdprescription($data_array);
            }
            if (!empty($delete_arr)) {

                $this->prescription_model->delete_ipdprescription($delete_arr);
            }
            $this->patient_model->addipd($ipd_array);

            $array = array('status' => 'success', 'error' => '', 'message' => 'Prescription Added Successfully');
        }
        echo json_encode($array);
    }

    public function add_inpatient()
    {

        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_add')) {
            access_denied();
        }
        $patient_type = $this->customlib->getPatienttype();

        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment') . " " . $this->lang->line('date'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant') . " " . $this->lang->line('doctor'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('bed_no', $this->lang->line('bed') . " " . $this->lang->line('no'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'callback_valid_patient');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'bed_no'            => form_error('bed_no'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'patient_id'        => form_error('patient_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $check_ipd_id     = $this->patient_model->getMaxIPDId();
            $ipdnoid          = $check_ipd_id + 1;
            $ipdno            = 'IPDN' . $ipdnoid;
            $appointment_date = $this->input->post('appointment_date');
            $insert_id        = $this->input->post('patient_id');
            $password         = $this->input->post('password');
            $email            = $this->input->post('email');
            $mobileno         = $this->input->post('mobileno');
            $patient_name     = $this->input->post('patient_name');
            $consult     = $this->input->post('live_consult');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            }else{
                $live_consult = $this->lang->line('no');
            }

            $doctor_id        = $this->input->post('consultant_doctor');
            $date             = date('Y-m-d H:i:s', $this->customlib->datetostrtotime($appointment_date));
            $ipd_data         = array(
                'date'         => $date,
                'ipd_no'       => $ipdno,
                'bed'          => $this->input->post('bed_no'),
                'bed_group_id' => $this->input->post('bed_group_id'),
                'height'       => $this->input->post('height'),
                'weight'       => $this->input->post('weight'),
                'bp'           => $this->input->post('bp'),
                'pulse'        => $this->input->post('pulse'),
                'temperature'  => $this->input->post('temperature'),
                'respiration'  => $this->input->post('respiration'),
                'case_type'    => $this->input->post('case'),
                'symptoms'     => $this->input->post('symptoms'),
                'refference'   => $this->input->post('refference'),
                'cons_doctor'  => $this->input->post('consultant_doctor'),
                'patient_id'   => $insert_id,
                'credit_limit' => $this->input->post('credit_limit'),
                'casualty'     => $this->input->post('casualty'),
                'discharged'   => 'no',
                'live_consult' => $live_consult,
                'generated_by' => $this->session->userdata('hospitaladmin')['id'],
            );

            $ipdpatient_data = array(
                'id'           => $insert_id,
                'organisation' => $this->input->post('organisation'),
            );
            $ipd_id = $this->patient_model->add_ipd($ipd_data);

            $patientInfo=$this->common_model->getRow($insert_id);
            $comments="new add patient in ipd where patient name is ". $patientInfo['patient_name']." IPD No is ".$ipdno;
            $activityLog=$this->common_model->saveLog('ipd','add',$comments,$ipdno);

            $bed_data = array('id' => $this->input->post('bed_no'), 'is_active' => 'no');
            $this->bed_model->savebed($bed_data);
            $updateData = array('id' => $insert_id, 'is_ipd' => 'yes', 'discharged' => 'no');
            $this->patient_model->add($updateData);
            $ipdid = $this->patient_model->add($ipdpatient_data);
            //$date = date('Y-m-d H:i:s', $this->customlib->datetostrtotime($this->input->post('appointment_date')));
            $setting_result = $this->setting_model->getzoomsetting();
            $ipdduration    = $setting_result->ipd_duration;
            $status_live    = $this->lang->line('yes');

            if ($live_consult == $status_live) {

                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'     => $doctor_id,
                    'patient_id'   => $insert_id,
                    'ipd_id'       => $ipd_id,
                    'title'        => 'Online consult for ' . $ipdno,
                    'date'         => $date,
                    'duration'     => $ipdduration,
                    'created_id'   => $this->customlib->getStaffID(),
                    'api_type'     => $api_type,
                    'host_video'   => 1,
                    'client_video' => 1,
                    'purpose'      => 'consult',
                    'password'     => $password,
                    //'description'  => $this->input->post('description'),
                    'timezone'     => $this->customlib->getTimeZone(),
                );
                $response = $this->zoom_api->createAMeeting($insert_array);

                if ($response) {
                    if (isset($response->id)) {
                        $insert_array['return_response'] = json_encode($response);
                        $conferenceid                    = $this->conference_model->add($insert_array);
                        $sender_details                  = array('patient_id' => $insert_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);

                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => "Patient Added Successfully");
            if ($this->session->has_userdata("appointment_id")) {

                $appointment_id = $this->session->userdata("appointment_id");
                $updateData     = array('id' => $appointment_id, 'is_ipd' => 'yes');
                $this->appointment_model->update($updateData);
                $this->session->unset_userdata('appointment_id');
            }

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }

            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["ipd"];
             $url             = base_url() . $url_link . '/' . $insert_id . '/' . $ipd_id;
            //$url             = $url_link . '/' . $insert_id . '/' . $ipd_id;
            $this->ipdNotification($insert_id, $this->input->post('consultant_doctor'), $ipdno, $url, $date);

            $sender_details = array('patient_id' => $insert_id, 'patient_name' => $patient_name, 'ipd_no' => $ipdno, 'contact_no' => $mobileno, 'email' => $email);
            $this->mailsmsconf->mailsms('ipd_patient_registration', $sender_details);
        }

        echo json_encode($array);
    }

    public function valid_patient()
    {
        $id = $this->input->post('patient_id');

        if ($id > 0) {
            $check_exists = $this->patient_model->valid_patient($id);
            if ($check_exists == true) {
                $this->form_validation->set_message('valid_patient', 'Record already exists');
                return false;
            }
        }
    }

    public function add_consultant_instruction()
    {
        if (!$this->rbac->hasPrivilege('consultant register', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('date[]', $this->lang->line('applied') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctor[]', $this->lang->line('consultant'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('instruction[]', $this->lang->line('instruction'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('insdate[]', $this->lang->line('instruction') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'        => form_error('date[]'),
                'doctor'      => form_error('doctor[]'),
                'instruction' => form_error('instruction[]'),
                'datee'       => form_error('insdate[]'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->input->post('date[]');
            $ins_date = $this->input->post('insdate[]');
            //$ins_time = $this->input->post('instime[]');
            $patient_id  = $this->input->post('patient_id');
            $ipd_id      = $this->input->post('ipdid');
            $doctor      = $this->input->post('doctor[]');
            $instruction = $this->input->post('instruction[]');
            $data        = array();
            $i           = 0;
            foreach ($date as $key => $value) {
                $details = array(
                    'date'        => date('Y-m-d H:i:s', $this->customlib->datetostrtotime($date[$i])),
                    'patient_id'  => $patient_id,
                    'ipd_id'      => $ipd_id,
                    'ins_date'    => date('Y-m-d', $this->customlib->datetostrtotime($ins_date[$i])),
                    'cons_doctor' => $doctor[$i],
                    'instruction' => $instruction[$i],
                );
                $data[] = $details;
                $i++;
            }
            $this->patient_model->add_consultantInstruction($data);
            $array = array('status' => 'success', 'error' => '', 'message' => 'Record Added Successfully');
        }
        echo json_encode($array);
    }

    public function ipdCharge()
    {
        $code   = $this->input->post('code');
        $org_id = $this->input->post('organisation_id');

        $patient_charge         = $this->patient_model->ipdCharge($code, $org_id);
        $data['patient_charge'] = $patient_charge;
        echo json_encode($patient_charge);
    }

    public function opd_report()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
       // $additional_where=array();
        $additional_where_in=array();
        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;
        $doctors            = $this->staff_model->getStaffbyrole(3);
        $data['doctors']    = $doctors;
        $data['dept_select']=$dept_select= $this->input->post("department");
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/patient/opd_report');
        $select = 'opd.*,muser.name as kop_name,opd_details.opd_no,opd_details.set_casuality,opd_details.cons_doctor,opd_details.casualty,opd_details.refference,staff.name,staff.surname,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.address,patients.admission_date,patients.gender,patients.mobileno,patients.age,patients.month,patients.paed';
        $join   = array(
            'LEFT JOIN patients ON opd.patient_id = patients.id',
            'LEFT JOIN opd_details ON opd_details.id = opd.id',
            'LEFT JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN staff muser ON opd_details.generated_by = muser.id',
        );
        $where = array();
        $patient_status         = $this->input->post("patient_status");
        $data["patient_status"] = $patient_status;
        $doctorid = $this->input->post('doctor');
        $counter = $this->input->post('counter');

        $data['kpo']=$kpo = $this->input->post('kpo');
        $data['gender']=$gender = $this->input->post('gender');
        $data['paed']=$paed = $this->input->post('paed');
        $data['departments']= $this->staff_model->getDepartment();

        if (!empty($doctorid)) {
            $additional_where = array('opd_details.cons_doctor =' . $doctorid);
            $selected_doctor=$this->getStaff($doctorid);
            $data['selected_doctor']=ucfirst($selected_doctor['name']);
        }
        if (!empty($kpo)) {
            if(!empty($additional_where)){
                $additionalwhere = array('muser.id =' . $kpo);

                $additional_where = array_merge($additional_where,$additionalwhere);
            }else{

                $additional_where = array('muser.id =' . $kpo);
            }
            $selected_kpo=$this->getStaff($kpo);
            $data['selected_kpo']=ucfirst($selected_kpo['name']);
        }
        if (!empty($gender)) {
            if(!empty($additional_where)){
                $additional_gender = array('patients.gender ='."'$gender'");
                $additional_where = array_merge($additional_where,$additional_gender);
            }else{
                $additional_where = array('patients.gender ='."'$gender'");
            }
            $data['selected_gender']=$gender;
        }
        if (!empty($paed)) {

            if(!empty($additional_where)){
                $additional_paed = array('patients.paed =' . $paed);
                $additional_where = array_merge($additional_where,$additional_paed);
            }else{
                $additional_where = array('patients.paed =' . $paed);
            }
            $data['selected_paeds']="Paeds";
        }
        if (!empty($counter)) {

            if(!empty($additional_where)){
                $additional_counter = array('opd_details.casualty =' . "'$counter'");
                $additional_where = array_merge($additional_where,$additional_counter);
            }else{
                $additional_where = array('opd_details.casualty =' . "'$counter'");
            }
            $data['selected_counter']=$counter;
        }
        if (!empty($patient_status)) {

            if(!empty($additional_where)){
                $additional_where = array_merge($additional_where,$additional_paytype);
            }else{
                $additional_where = array('opd.paytype =' . $patient_status);
            }
        }
        if (!empty($dept_select)) {
            if($dept_select==1){
                $where_in=['1'];
            }else{

                $where_in=$this->getotherOpd();
            }


        }

        $table_name = "(SELECT `id`, `patient_id`,`amount`,`appointment_date`,`payment_mode`,'visit' as paytype
        FROM `opd_details` UNION ALL SELECT `opd_id`,`patient_id`,`amount`,`appointment_date`,`payment_mode`,'rechekup' as paytype
        FROM `visit_details` UNION ALL SELECT `opd_id`,`patient_id`,`paid_amount`,`date`,`payment_mode`,'payment' as paytype
        FROM `opd_payment` UNION ALL SELECT `opd_id`,`patient_id`,`net_amount`,`date`,`status`,'bill' as paytype FROM `opd_billing`) AS opd";

        $disable_option     = false;
        $userdata           = $this->customlib->getUserData();
        $role_id            = $userdata['role_id'];
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {

                $user_id          = $userdata["id"];
                $doctorid         = $user_id;
                $additional_where = array(
                    "opd_details.cons_doctor = " . $user_id,
                );
                $disable_option = true;
            }
        }
        $data['disable_option'] = $disable_option;
        $data['doctor_select']  = $doctorid;
        $search_type            = $this->input->post("search_type");

        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }
        $search_table        = "opd";
        $search_column       = "appointment_date";
        $resultlist          = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where, $where,$where_in);
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist['main_data'];
        $data["date_data"]  = $resultlist['fillter_data'];

        $i = 0;
        if (!empty($resultlist)) {
            foreach ($data['resultlist'] as $key => $value) {
                $charges                           = $this->patient_model->getOPDCharges($value["pid"], $value["id"]);
                $data['resultlist'][$i]["charges"] = $charges['charge'];
                $vamount                           = $this->patient_model->getOPDvisitCharges($value["pid"], $value["id"]);
                $data['resultlist'][$i]["vamount"] = $vamount['vamount'];
                $payment                           = $this->patient_model->getopdPayment($value["pid"], $value["id"]);
                $data['resultlist'][$i]["payment"] = $payment['opdpayment'];
                $i++;
            }
        }
        //echo "<pre>";print_r($data['resultlist']);exit;
        $data['all_kops']=$this->report_model->getKopName();
        if($this->input->post('search')=='export_filter')
        {
            //echo "<pre>";print_r($data);exit;
            $html  = $this->load->view('admin/patient/opdReportPdf',$data, true);
            // Load pdf library
            $this->load->library('pdf');
            $this->dompdf->set_paper("letter", "portrait");
            $customPaper = array(0,0,360,360);
            // $this->dompdf->set_paper('A4','portrait');

            // Load HTML content
            $this->dompdf->loadHtml($html);
            ini_set('display_errors', 1);
            // Render the HTML as PDF
            $this->dompdf->render();
            $canvas =  $this->dompdf->getCanvas();
            $date=date('d-M-Y h:i:s A',strtotime(date('Y-m-d H:i:s')));
            //$date=strtotime('d-m-y H:i:s');
            //$canvas->page_text(420, 540, "Page: {PAGE_NUM}", null, 10);
            $canvas->page_text(270, 780, "Page : {PAGE_NUM}", null, 10, [0, 0, 0]);
            $canvas->page_text(270, 760, $date, null, 10, [0, 0, 0]);


            // Output the generated PDF (1 = download and 0 = preview)
            $this->dompdf->stream("OPDREPORT.pdf", array("Attachment"=>1));

        }else{
            $this->load->view('layout/header');
            $this->load->view('admin/patient/opdReport.php', $data);
            $this->load->view('layout/footer');
        }

    }

    public function opdreportbalance()
    {
        if (!$this->rbac->hasPrivilege('opd_balance_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/patient/opdreportbalance');
        $doctorlist             = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']     = $doctorlist;
        $doctors                = $this->staff_model->getStaffbyrole(3);
        $data['doctors']        = $doctors;
        $patient_status         = $this->input->post("patient_status");
        $data["patient_status"] = $patient_status;

        $status = 'yes';
        if (empty($patient_status)) {
            $patient_status = 'on_opd';
        }
        if ($patient_status == 'all') {
            $status = '';
        } else if ($patient_status == 'on_opd') {
            $status = 'yes';
        } else if ($patient_status == 'discharged') {
            $status = 'no';
        }

        $select = 'opd_details.*,staff.name,staff.surname,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.address,patients.admission_date,patients.gender,patients.mobileno,patients.age,patients.month';
        $join   = array(
            'LEFT JOIN staff ON opd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON opd_details.patient_id = patients.id',
        );
        $where = array();
        $additional_where = array("patients.is_active = 'yes'", "opd_details.discharged != '" . $status . "'");
        $doctorid         = $this->input->post('doctor');

        if (!empty($doctorid)) {
            $additional_where = array("patients.is_active = 'yes' ", "opd_details.cons_doctor ='" . $doctorid . "'", "opd_details.discharged != '" . $status . "'");
        }

        $table_name         = "opd_details";
        $disable_option     = false;
        $userdata           = $this->customlib->getUserData();
        $role_id            = $userdata['role_id'];
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {

                $user_id  = $userdata["id"];
                $doctorid = $user_id;
                $where    = array(
                    "opd_details.cons_doctor = " . $user_id,
                );
                $disable_option = true;
            }
        }
        $data['disable_option'] = $disable_option;
        $data['doctor_select']  = $doctorid;
        $search_type            = $this->input->post("search_type");

        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        if (empty($search_type)) {

            $search_type = "";
            $resultlist  = $this->report_model->getReport($select, $join, $table_name, $where, $additional_where);
        } else {

            $search_table  = "opd_details";
            $search_column = "appointment_date";
            $resultlist    = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where, $where);
        }

        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist;

        $i = 0;
        if (!empty($resultlist)) {
            foreach ($data['resultlist'] as $key => $value) {
                $charges                            = $this->patient_model->getOPDCharges($value["pid"], $value["id"]);
                $data['resultlist'][$i]["charges"]  = $charges['charge'];
                $vamount                            = $this->patient_model->getOPDvisitCharges($value["pid"], $value["id"]);
                $data['resultlist'][$i]["vamount"]  = $vamount['vamount'];
                $billpaid                           = $this->patient_model->getOPDbill($value["pid"], $value["id"]);
                $data['resultlist'][$i]["billpaid"] = $billpaid['billamount'];
                $payment                            = $this->patient_model->getopdPayment($value["pid"], $value["id"]);
                $data['resultlist'][$i]["payment"]  = $payment['opdpayment'];
                $i++;
            }
        }
        if (!empty($patient_status)) {
            $data['patient_status'] = $patient_status;
        } else {
            $data['patient_status'] = 'on_opd';
        }
        $this->load->view('layout/header');
        $this->load->view('admin/patient/opdReportbalance.php', $data);
        $this->load->view('layout/footer');
    }

    public function ipdReport()
    {
        if (!$this->rbac->hasPrivilege('ipd_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/patient/ipdreport');

        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;
        $status             = 'no';
        $patient_status     = $this->input->post("patient_status");
        if (empty($patient_status)) {
            $patient_status = 'on_bed';
        }
        if ($patient_status == 'all') {
            $status = '';
        } else if ($patient_status == 'on_bed') {
            $status = 'yes';
        } else if ($patient_status == 'discharged') {
            $status = 'no';
        }

        $select = 'ipd_details.*,ipd_details.discharged as ipd_discharge,payment.paid_amount,payment.payment_mode, payment.date as payment_date, staff.name,staff.surname,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.address,patients.admission_date,patients.gender,patients.mobileno,patients.age,patients.month';
        $join   = array(
            'JOIN staff ON ipd_details.cons_doctor = staff.id',
            'JOIN patients ON ipd_details.patient_id = patients.id',
            'LEFT JOIN payment ON payment.ipd_id = ipd_details.id',
        );
        $table_name = "ipd_details";

        $additional_where = array("patients.is_active = 'yes'", "ipd_details.discharged != '" . $status . "'");
        $doctorid         = $this->input->post('doctor');

        if (!empty($doctorid)) {
            $additional_where = array("patients.is_active = 'yes' ", "ipd_details.cons_doctor ='" . $doctorid . "' ", "ipd_details.discharged != '" . $status . "'");
        }
        $disable_option = false;
        $userdata       = $this->customlib->getUserData();
        $role_id        = $userdata['role_id'];

        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {

                $user_id          = $userdata["id"];
                $doctorid         = $user_id;
                $additional_where = array(
                    "ipd_details.cons_doctor = " . $user_id . " ",
                    "ipd_details.discharged != '" . $status . "' ",
                );
                $disable_option = true;
            }
        }
        $data['disable_option'] = $disable_option;
        $data['doctor_select']  = $doctorid;

        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        if (empty($search_type)) {
            $search_type = "";
            $resultlist  = $this->report_model->getReport($select, $join, $table_name, $additional_where);
        } else {

            $search_table  = "ipd_details";
            $search_column = "date";
            $resultlist    = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where);

        }
        $resultList2 = $this->report_model->searchReport($select = 'ipd_details.*,ipd_details.discharged as ipd_discharge,ipd_billing.net_amount as paid_amount, ipd_billing.date as payment_date,staff.name,staff.surname,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.address,patients.admission_date,patients.gender,patients.mobileno,patients.age,patients.month', $join = array(
            'JOIN staff ON ipd_details.cons_doctor = staff.id',
            'JOIN patients ON ipd_details.patient_id = patients.id',
            'LEFT JOIN payment ON payment.patient_id = patients.id',
            'JOIN ipd_billing ON ipd_billing.ipd_id = ipd_details.id',
        ), $table_name = 'ipd_details', $search_type, $search_table = 'ipd_billing', $search_column = 'date', $additional_where);

        if (!empty($resultList2)) {
            array_push($resultlist, $resultList2[0]);
        }

        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist;
        $i                   = 0;
        if (!empty($resultlist)) {
            foreach ($data['resultlist'] as $key => $value) {
                $charges                           = $this->patient_model->getCharges($value["pid"], $value["id"]);
                $data['resultlist'][$i]["charges"] = $charges['charge'];
                $i++;
            }
        }
        if (!empty($patient_status)) {
            $data['patient_status'] = $patient_status;
        } else {
            $data['patient_status'] = 'on_bed';
        }
        $this->load->view('layout/header');
        $this->load->view('admin/patient/ipdReport.php', $data);
        $this->load->view('layout/footer');
    }

    public function ipdReportbalance()
    {
        if (!$this->rbac->hasPrivilege('ipd_balance_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/patient/ipdreportbalance');

        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;
        $status             = 'no';
        $patient_status     = $this->input->post("patient_status");
        if (empty($patient_status)) {
            $patient_status = 'on_bed';
        }
        if ($patient_status == 'all') {
            $status = '';
        } else if ($patient_status == 'on_bed') {
            $status = 'yes';
        } else if ($patient_status == 'discharged') {
            $status = 'no';
        }

        $select = 'ipd_details.*,ipd_details.discharged as ipd_discharge,payment.paid_amount, payment.date as payment_date,IFNULL(ipd_billing.total_amount,0) as totalpaid_amount,staff.name,staff.surname,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.address,patients.admission_date,patients.gender,patients.mobileno,patients.age,patients.month';
        $join   = array(
            'LEFT JOIN staff ON ipd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON ipd_details.patient_id = patients.id',
            'LEFT JOIN payment ON payment.ipd_id = ipd_details.id',
            'LEFT JOIN ipd_billing ON ipd_billing.ipd_id = ipd_details.id',
        );
        $table_name = "ipd_details";
        $group_by         = "ipd_details.ipd_no";
        $additional_where = array("patients.is_active = 'yes'", "ipd_details.discharged != '" . $status . "'");
        $doctorid         = $this->input->post('doctor');

        if (!empty($doctorid)) {
            $additional_where = array("patients.is_active = 'yes' ", "ipd_details.cons_doctor ='" . $doctorid . "'", "ipd_details.discharged != '" . $status . "'");
        }
        $disable_option = false;
        $userdata       = $this->customlib->getUserData();
        $role_id        = $userdata['role_id'];

        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {

                $user_id          = $userdata["id"];
                $doctorid         = $user_id;
                $additional_where = array(
                    "ipd_details.cons_doctor = " . $user_id,
                    "patients.discharged != 'yes'",
                );
                $disable_option = true;
            }
        }
        $data['disable_option'] = $disable_option;
        $data['doctor_select']  = $doctorid;

        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        if (empty($search_type)) {
            $search_type = "";
            $resultlist  = $this->report_model->getReportbalance($select, $join, $table_name, $additional_where, $group_by);
        } else {

            $search_table  = "ipd_details";
            $search_column = "date";
            $resultlist    = $this->report_model->searchReportbalance($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where, $group_by);

        }
        $resultList2 = $this->report_model->searchReportbalance($select = 'ipd_details.*,ipd_details.discharged as ipd_discharge,ipd_billing.net_amount as paid_amount,IFNULL(ipd_billing.total_amount,0) as totalpaid_amount, ipd_billing.date as payment_date,staff.name,staff.surname,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.address,patients.admission_date,patients.gender,patients.mobileno,patients.age,patients.month', $join = array(
            'LEFT JOIN staff ON ipd_details.cons_doctor = staff.id',
            'LEFT JOIN patients ON ipd_details.patient_id = patients.id',
            'LEFT JOIN payment ON payment.patient_id = patients.id',
            'LEFT JOIN ipd_billing ON ipd_billing.ipd_id = ipd_details.id',
        ), $table_name = 'ipd_details', $search_type, $search_table = 'ipd_billing', $search_column = 'date', $additional_where, $group_by);

        if (!empty($resultList2)) {
            array_push($resultlist, $resultList2[0]);
        }
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist;
        $i                   = 0;
        if (!empty($resultlist)) {
            foreach ($data['resultlist'] as $key => $value) {
                $charges                           = $this->patient_model->getCharges($value["pid"], $value["id"]);
                $data['resultlist'][$i]["charges"] = $charges['charge'];
                $payment                           = $this->patient_model->getPayment($value["pid"], $value["id"]);
                $data['resultlist'][$i]["payment"] = $payment['payment'];
                $i++;
            }
        }
        if (!empty($patient_status)) {
            $data['patient_status'] = $patient_status;

        } else {
            $data['patient_status'] = 'on_bed';

        }
        $this->load->view('layout/header');
        $this->load->view('admin/patient/ipdReportbalance.php', $data);
        $this->load->view('layout/footer');
    }

    public function dischargepatientReport()
    {
        if (!$this->rbac->hasPrivilege('ipd_report', 'can_view')) {
            access_denied();
        }
        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/patient/dischargepatientReport');
        $select = 'ipd_details.*,payment.paid_amount, payment.date as payment_date,payment.payment_mode,staff.name,staff.surname,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.address,patients.admission_date,patients.gender,patients.mobileno,patients.age';
        $join   = array(
            'JOIN staff ON ipd_details.cons_doctor = staff.id',
            'JOIN patients ON ipd_details.patient_id = patients.id',
            'LEFT JOIN payment ON payment.ipd_id = ipd_details.id',
        );
        $table_name       = "ipd_details";
        $additional_where = array("ipd_details.discharged = 'yes'");
        $doctorid         = $this->input->post('doctor');
        $disable_option   = false;
        $userdata         = $this->customlib->getUserData();
        $role_id          = $userdata['role_id'];

        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {

                $user_id          = $userdata["id"];
                $doctorid         = $user_id;
                $additional_where = array(
                    "ipd_details.discharged = 'yes' ",
                    "ipd_details.cons_doctor = " . $user_id,
                );
                $disable_option = true;
            }
        }
        $data['doctor_select']  = $doctorid;
        $data['disable_option'] = $disable_option;

        if (!empty($doctorid)) {
            $additional_where = array("ipd_details.discharged = 'yes'", "ipd_details.cons_doctor ='" . $doctorid . "'");
        }
        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        if (empty($search_type)) {
            $search_type = "";
            $resultlist  = $this->report_model->getReport($select, $join, $table_name, $additional_where);
        } else {
            $search_table  = "ipd_details";
            $search_column = "date";
            $resultlist    = $this->report_model->searchReport($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where);
        }

        $resultList2 = $this->report_model->searchReport($select = 'ipd_details.*,ipd_billing.net_amount as paid_amount, ipd_billing.date as payment_date,staff.name,staff.surname,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.address,patients.admission_date,patients.gender,patients.mobileno,patients.age', $join = array(
            'JOIN staff ON ipd_details.cons_doctor = staff.id',
            'JOIN patients ON ipd_details.patient_id = patients.id',
            //'LEFT JOIN payment ON payment.ipd_id = ipd_details.id',
            'JOIN ipd_billing ON ipd_billing.ipd_id = ipd_details.id',
        ), $table_name = 'ipd_details', $search_type, $search_table = 'ipd_billing', $search_column = 'date', $additional_where);
        $resultlist3 = array();
        if (!empty($resultList2)) {
            //   array_push($resultlist, $resultList2[0]);
            $resultlist3 = $resultlist + $resultList2;
            $resultlist3 = array_merge($resultlist, $resultList2);
        }

        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist3;

        $i = 0;
        if (!empty($resultlist3)) {
            foreach ($resultlist3 as $key => $value) {

                $charges                                  = $this->patient_model->getCharges($value["pid"], $value["id"]);
                $data['resultlist'][$i]["charges"]        = $charges['charge'];
                $discharge_details                        = $this->patient_model->getIpdBillDetails($value["pid"], $value["id"]);
                $payment                                  = $this->patient_model->getPayment($value["pid"], $value["id"]);
                $data['resultlist'][$i]["discharge_date"] = $discharge_details['date'];
                $data['resultlist'][$i]["other_charge"]   = $discharge_details['other_charge'];
                $data['resultlist'][$i]["tax"]            = $discharge_details['tax'];
                $data['resultlist'][$i]["discount"]       = $discharge_details['discount'];
                $data['resultlist'][$i]["net_amount"]     = $discharge_details['net_amount'] + $payment['payment'];
                $i++;
            }
        }

        $this->load->view('layout/header');
        $this->load->view('admin/patient/dischargePatientReport.php', $data);
        $this->load->view('layout/footer');
    }

    public function revertBill()
    {
        $patient_id = $this->input->post('patient_id');
        $bill_id    = $this->input->post('bill_id');
        $bed_id     = $this->input->post('bed_id');
        $ipd_id     = $this->input->post('ipdid');

        if ((!empty($patient_id)) && (!empty($bill_id))) {
            $patient_data = array('id' => $patient_id, 'discharged' => 'no');
            $this->patient_model->add($patient_data);

            $ipd_data = array('id' => $ipd_id, 'discharged' => 'no');
            $this->patient_model->add_ipd($ipd_data);

            $bed_data = array('id' => $bed_id, 'is_active' => 'no');
            $this->bed_model->savebed($bed_data);
            $revert = $this->payment_model->revertBill($patient_id, $bill_id);
            $array  = array('status' => 'success', 'error' => '', 'message' => 'Record Updated Successfully.');
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => 'Record Not Updated.');
        }
        echo json_encode($array);
    }

    public function deleteOPD()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_delete')) {
            access_denied();
        }
        $opdid = $this->input->post('opdid');
        if (!empty($opdid)) {
            $patientInfo=$this->common_model->getRowOpd($opdid);
            $comments="delete visit from opd where opd number is ". $patientInfo['opd_no']." and appointment date is ".date('d,M Y h:i:s A',strtotime($patientInfo['appointment_date']));
            $activityLog=$this->common_model->saveLog('opd','delete',$comments,$patientInfo['opd_no']);

            $this->patient_model->deleteOPD($opdid);
            $array = array('status' => 'success', 'error' => '', 'message' => 'Record Deleted Successfully.');
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function deletePatient()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_delete')) {
            access_denied();
        }
        $id = $this->input->post('delid');
        if (!empty($id)) {
            $patientInfo=$this->common_model->getRow($id);
			$comments="delete patient where patient name is ". $patientInfo['patient_name']." and MR NO is ".$patientInfo['patient_unique_id'];
			$activityLog=$this->common_model->saveLog('patient','delete',$comments);
            $this->patient_model->deletePatient($id);
            $array = array('status' => 'success', 'error' => '', 'message' => 'Record Deleted Successfully.');
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function deleteOPDPatient()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_delete')) {
            access_denied();
        }
        $id = $this->input->post('id');
        if (!empty($id)) {
            $this->patient_model->deleteOPDPatient($id);
            $array = array('status' => 'success', 'error' => '', 'message' => 'Record Deleted Successfully.');
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function patientCredentialReport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/patient/patientcredentialreport');
        $credential         = $this->patient_model->patientCredentialReport();
        $data["credential"] = $credential;
        $this->load->view("layout/header");
        $this->load->view("admin/patient/patientcredentialreport", $data);
        $this->load->view("layout/footer");
    }

    public function patientcredential_search()
    {
        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->patient_model->search_datatablecredential($where_condition);
        $total_result = $this->patient_model->search_datatablecredential_count($where_condition);
        $data         = array();

        foreach ($resultlist as $result_key => $result_value) {

            $nestedData   = array();
            $nestedData[] = $result_value->patient_unique_id;
            $nestedData[] = $result_value->patient_name;
            $nestedData[] = $result_value->username;
            $nestedData[] = $result_value->password;
            $data[]       = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }

    public function deleteIpdPatient($id)
    {
        if (!empty($id)) {
            $this->patient_model->deleteIpdPatient($id);
            $array = array('status' => 'success', 'error' => '', 'message' => 'Record Deleted Successfully.');
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getBedStatus()
    {

        $floor_list            = $this->floor_model->floor_list();
        $bedlist               = $this->bed_model->bed_list();
        $bedactive             = $this->bed_model->bed_active();
        $bedgroup_list         = $this->bedgroup_model->bedGroupFloor();
        $data["floor_list"]    = $floor_list;
        $data["bedlist"]       = $bedlist;
        $data["bedgroup_list"] = $bedgroup_list;
        $data['bedactive']     = $bedactive;

        $this->load->view("layout/bedstatusmodal", $data);
    }

    public function updateBed()
    {
        $this->form_validation->set_rules('bedgroup', $this->lang->line('bed') . " " . $this->lang->line('group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bedno', $this->lang->line('bed'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'bedgroup' => form_error('bedgroup'),
                'bedno'    => form_error('bedno'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'ipd_no'       => $this->input->post('ipd_no'),
                'bed_group_id' => $this->input->post('bedgroup'),
                'bed'          => $this->input->post('bedno'),
            );

            $this->patient_model->updatebed($data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function moveipd($id)
    {

        $appointment_details = $this->patient_model->getDetails($id);
        $patient_name        = $appointment_details['patient_name'];
        $patient_id          = $appointment_details['id'];
        $gender              = $appointment_details['gender'];
        $email               = $appointment_details['email'];
        $phone               = $appointment_details['mobileno'];
        $doctor              = $appointment_details['cons_doctor'];
        $note                = $appointment_details['note'];
        $orgid               = $appointment_details['orgid'];
        $live_consult        = $appointment_details['live_consult'];
        //$appointment_date = $appointment_details['appointment_date'];
        $appointment_date = date($this->customlib->getSchoolDateFormat(true, true), strtotime($appointment_details['appointment_date']));
        $amount           = $appointment_details['amount'];
        $allergies        = $appointment_details['opdknown_allergies'];
        $symptoms         = strip_tags($appointment_details['symptoms']);

        $patient_data = array(
            'patient_id'       => $patient_id,
            'patient_name'     => $patient_name,
            'gender'           => $gender,
            'email'            => $email,
            'phone'            => $phone,
            'appointment_date' => $appointment_date,
            'known_allergies'  => $allergies,
            'cons_doctor'      => $doctor,
            'orgid'            => $orgid,
            'live_consult'     => $live_consult,
        );

        $data['ipd_data'] = $patient_data;
        $updateData       = array('id' => $patient_id, 'is_ipd' => 'yes');
        $this->appointment_model->update($updateData);
        $this->session->set_flashdata('ipd_data', $data);
        redirect("admin/patient/ipdsearch/");
    }

    public function deleteVisit($id)
    {
        $this->patient_model->deleteVisit($id);
        $json_array = array('status' => 'success');
        echo json_encode($json_array);
    }

    public function getTestList()
    {

        $patho_category = $this->input->post("patho_category");
        $result          = $this->patient_model->getTestList($patho_category);
        echo json_encode($result);
    }
    public function getTestDetails()
    {

        $charge_id = $this->input->post("charge_id");
        $result          = $this->patient_model->getTestDetails($charge_id);
        echo json_encode($result);
    }


    public function getorganizationCharge()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }

        $doctor         = $this->input->post("doctor");
        $organisation   = $this->input->post("organisation");
        $result         = $this->patient_model->getorganizationCharge($organisation);
        $data['result'] = $result;
        echo json_encode($result);
    }

    public function emgpatients()
    {
        // if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
        //     access_denied();
        // }
        $opd_data         = $this->session->flashdata('opd_data');
        $data['opd_data'] = $opd_data;
        $data["title"]    = 'opd_patient';
        $this->session->set_userdata('top_menu', 'OPD_Out_Patient');
        $setting                    = $this->setting_model->get();
        $data['setting']            = $setting;
        $opd_month                  = $setting[0]['opd_record_month'];
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["bloodgroup"]         = $this->blood_group;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;

        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/emg_patients.php', $data);
        $this->load->view('layout/footer');
    }
    public function save_emg_patient()
    {
        // if (!$this->rbac->hasPrivilege('opd_patient', 'can_add')) {
        //     access_denied();
        // }
        $patient_type = $this->customlib->getPatienttype();

        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment') . " " . $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant') . " " . $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('applied') . " " . $this->lang->line('charge'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'patient_id'        => form_error('patient_id'),
                'amount'            => form_error('amount'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $check_opd_id     = $this->patient_model->getMaxEMGId();
            $opdnoid          = $check_opd_id + 1;
            $doctor_id        = $this->input->post('consultant_doctor');
            $insert_id        = $this->input->post('patient_id');
            $password         = $this->input->post('password');
            $email            = $this->input->post('email');
            $mobileno         = $this->input->post('mobileno');
            $patient_name     = $this->input->post('patient_name');
            $appointment_date = $this->input->post('appointment_date');
            $isopd            = $this->input->post('is_opd');
            $appointmentid    = $this->input->post('appointment_id');
            $consult     = $this->input->post('live_consult');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            }else{
                $live_consult = $this->lang->line('no');
            }

            $date             = date('Y-m-d H:i:s', $this->customlib->datetostrtotime($appointment_date));
            $opd_data         = array(
                'appointment_date' => $date,
                'case_type'        => $this->input->post('case'),
                'emg_no'           => 'EMGNO' . $opdnoid,
                'symptoms'         => $this->input->post('symptoms'),
                'refference'       => $this->input->post('refference'),
                'cons_doctor'      => $doctor_id,
                'spo2'             => $this->input->post('spo2'),
                'height'           => $this->input->post('height'),
                'weight'           => $this->input->post('weight'),
                'bp'               => $this->input->post('bp'),
                'pulse'            => $this->input->post('pulse'),
                'temperature'      => $this->input->post('temperature'),
                'respiration'      => $this->input->post('respiration'),
                'patient_id'       => $insert_id,
                'casualty'         => $this->input->post('casualty'),
                'payment_mode'     => $this->input->post('payment_mode'),
                'note_remark'      => $this->input->post('note'),
                'amount'           => $this->input->post('amount'),
                'live_consult'     => $live_consult,
                'generated_by'     => $this->session->userdata('hospitaladmin')['id'],
                'discharged'       => 'no',
            );

            $patient_data = array(
                'id'           => $insert_id,
                'old_patient'  => $this->input->post('old_patient'),
                'organisation' => $this->input->post('organisation'),

            );

            $p_id            = $this->patient_model->add_patient($patient_data);
            $opdn_id         = $this->patient_model->add_emg($opd_data);
            $setOPD=$this->formatNumber($opdn_id,$type='opd');
            $opd_no          = 'OPDN-'.$setOPD;
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["opd"];
            $setting_result  = $this->setting_model->getzoomsetting();
            $opdduration     = $setting_result->opd_duration;
            $status_live     = $this->lang->line('yes');

            $select="emg_commission";
            $staff_info=$this->staff_model->getStaffCommission($select,$doctor_id);
            if($this->input->post('amount') > 0 && $staff_info['emg_commission'] > 0){
                $commission_month=date('m',strtotime($this->input->post('appointment_date')));
                $commission_year=date('Y',strtotime($this->input->post('appointment_date')));
                $comission_amount=($this->input->post('amount') * $staff_info['emg_commission'])/100;
                $commission_data=array(
                    'staff_id'=>$doctor_id,
                    'appointment_date'=>date('Y-m-d H:i:s',strtotime($this->input->post('appointment_date'))),
                    'comission_month'=>$commission_month,
                    'comission_year'=>$commission_year,
                    'comission_amount'=>$comission_amount,
                    'commission_type'=>'EMERGENCY',
                    'commission_percentage'=>$staff_info['emg_commission'],
                    'total_amount'=>$this->input->post('amount'),

                );
                $this->db->insert('monthly_comission', $commission_data);
            }
            if ($live_consult == $status_live) {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );

                $title = 'Online consult for OPDN' . $opdnoid;
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'     => $doctor_id,
                    'patient_id'   => $insert_id,
                    'opd_id'       => $opdn_id,
                    'title'        => $title,
                    'date'         => $date,
                    'duration'     => $opdduration,
                    'created_id'   => $this->customlib->getStaffID(),
                    'password'     => $password,
                    'api_type'     => $api_type,
                    'host_video'   => 1,
                    'client_video' => 1,
                    'purpose'      => 'consult',
                    'timezone'     => $this->customlib->getTimeZone(),
                );

                $response = $this->zoom_api->createAMeeting($insert_array);

                if (!empty($response)) {
                    if (isset($response->id)) {
                        $insert_array['return_response'] = json_encode($response);
                        $conferenceid                    = $this->conference_model->add($insert_array);

                        $sender_details = array('patient_id' => $insert_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);

                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

            $url = base_url() . $url_link . '/' . $insert_id . '/' . $opdn_id;
            //$url =  $url_link . '/' . $insert_id . '/' . $opdn_id;

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $insert_id, 'opd_id' => $opdn_id);

            if ($this->session->has_userdata("appointment_id")) {
                $appointment_id = $this->session->userdata("appointment_id");
                $updateData     = array('id' => $appointment_id, 'is_opd' => 'yes');
                $this->appointment_model->update($updateData);
                $this->session->unset_userdata('appointment_id');
            }

            $this->opdNotification($insert_id, $doctor_id, $opd_no, $url, $date);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }

            $sender_details = array('patient_id' => $insert_id, 'patient_name' => $patient_name, 'opd_no' => $opd_no, 'contact_no' => $mobileno, 'email' => $email);
            $result = $this->mailsmsconf->mailsms('opd_patient_registration', $sender_details);

        }
        echo json_encode($array);
    }
    public function emg_search()
    {
        $draw       = $_POST['draw'];
        $row        = $_POST['start'];
        $rowperpage = $_POST['length']; // Rows display per page
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->patient_model->search_datatable_emg($where_condition);
        // echo "<pre>";print_r($resultlist);exit;
        $total_result = $this->patient_model->search_datatable_count_emg($where_condition);
        $data         = array();
        foreach ($resultlist as $result_key => $result_value) {
            $action = "<div class='rowoptionview'>";
            if ($this->rbac->hasPrivilege('revisit', 'can_add')) {

                $action .= "<a href='#' onclick='getRevisitRecord(" . $result_value->pid . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('revisit') . "'><i class='fas fa-exchange-alt'></i></a>";
            }

            $action .= "<a href=" . base_url() . 'admin/patient/profile/' . $result_value->pid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";

            if ($result_value->is_ipd != 'yes') {
                if ($this->rbac->hasPrivilege('opd_move _patient_in_ipd', 'can_view')) {
                    $action .= "<a href=" . base_url() . 'admin/patient/moveipd/' . $result_value->pid . " data-toggle='tooltip' onclick='return confirm('" . $this->lang->line('move') . " " . $this->lang->line('patient') . " " . $this->lang->line('in') . " " . $this->lang->line('ipd') . ")' data-original-title='" . $this->lang->line('move') . " " . $this->lang->line('in') . " " . $this->lang->line('ipd') . "' class='btn btn-default btn-xs' ><i class='fas fa-share-square'></i></a>";

                }
            }
            $action .= "</div'>";
            $first_action = "<a href=" . base_url() . 'admin/patient/emgprofile/' . $result_value->pid. ">";
            $nestedData   = array();
            $nestedData[] = $first_action . $result_value->patient_name . "</a>" . $action;
            $nestedData[] = $result_value->patient_unique_id;
            $nestedData[] = $result_value->patient_cnic;
            $nestedData[] = $result_value->guardian_name;
            $nestedData[] = $result_value->gender;
            $nestedData[] = $result_value->mobileno;
            $nestedData[] = $result_value->name . " " . $result_value->surname;
            $nestedData[] = date($this->customlib->getSchoolDateFormat(true, true), strtotime($result_value->last_visit));
            $nestedData[] = $result_value->total_visit;
            $data[]       = $nestedData;

        }

        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format
    }
    public function setRecurrning($id,$ipdid)
    {
        $recurringCharges=$this->charge_model->getRecurringCharges($id,$ipdid);
        if(!empty($recurringCharges)){
            $recurr_data=array();
            foreach($recurringCharges as $recurrning)
            {
                $today = date("Y-m-d");
                $today_dt = new DateTime($today);
                $expire_dt = new DateTime($recurrning['date']);
                if ($expire_dt < $today_dt)
                {
                    $expire_dt = date('Y-m-d', strtotime($recurrning['date'] . ' +1 day'));
                    $dates=$this->displayDates($expire_dt, $today);
                    foreach($dates as $datecheck)
                    {
                        $recurr_data[]=array(
                            'date'=>$datecheck,
                            'patient_id'=>$recurrning['patient_id'],
                            'ipd_id'=>$recurrning['ipd_id'],
                            'charge_id'=>$recurrning['charge_id'],
                            'org_charge_id'=>$recurrning['org_charge_id'],
                            'apply_charge'=>$recurrning['apply_charge'],
                            'recurring'=>$recurrning['recurring'],
                            'created_at'=>$datecheck,
                        );
                    }
                }
            }
            if(!empty($recurr_data)){
                $this->db->insert_batch('patient_charges',$recurr_data);
            }
        }

        return 1;
    }

    public function displayDates($date1, $date2, $format = 'Y-m-d' )
    {
        $dates = array();
        $current = strtotime($date1);
        $date2 = strtotime($date2);
        $stepVal = '+1 day';
        while( $current <= $date2 ) {
           $dates[] = date($format, $current);
           $current = strtotime($stepVal, $current);
        }
        return $dates;
    }
    public function deleteEmgVisits($id,$pid)
    {
        if (!$this->rbac->hasPrivilege('hospital_charge', 'can_delete')) {
            access_denied();
        }
        $result = $this->patient_model->deleteEmgVisits($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/patient/emgprofile/'.$pid);
    }

    public function queeList()
    {
        if (!$this->rbac->hasPrivilege('quee_list', 'can_view')) {
            access_denied();
        }
        $opd_data         = $this->session->flashdata('opd_data');
        $data['opd_data'] = $opd_data;
        $data["title"]    = 'opd_patient';
        $this->session->set_userdata('top_menu', 'OPD_Out_Patient');
        $setting                    = $this->setting_model->get();
        $data['setting']            = $setting;
        $opd_month                  = $setting[0]['opd_record_month'];
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["bloodgroup"]         = $this->blood_group;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;

        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/quee_patients.php', $data);
        $this->load->view('layout/footer');
    }

    public function quee_search()
    {
        $draw       = $_POST['draw'];
        $row        = $_POST['start'];
        $rowperpage = $_POST['length']; // Rows display per page
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->quee_model->search_datatable($where_condition);
        $total_result = $this->quee_model->search_datatable_count($where_condition);
        $data         = array();
        $optionArray=array('pending','in progress','completed');
        foreach ($resultlist as $result_key => $result_value) {
            $selected_visit=$result_value->visit_status;
            $option="<div class='form-group'><select class='form-control' id='visit_status' onchange='updateVisitStatus(".$result_value->id.",this.value)' ><option value=''>Select Status</option>";
            foreach($optionArray as $optiondata){
                $selectedOPtion=isset($selected_visit) && $selected_visit==$optiondata ? 'selected' : '';
                $option.='<option value="'.$optiondata.'" '.$selectedOPtion.' >'.ucfirst($optiondata).'</option>';
            }
            $doctorName=$result_value->name . " " . $result_value->surname;
            //$patientname=$result_value->patient_name;
            $option.='</select>';
            $option.="</div>";
            $option.='<a class="btn btn-primary btn-sm" onclick="getAnnouncement(\''.$result_value->patient_name.'\',\''.$doctorName.'\',\''.$result_value->id.'\')"> Announcement </a>';
            $action = "<div class='rowoptionview'>";
            if ($this->rbac->hasPrivilege('revisit', 'can_add')) {

                $action .= "<a href='#' onclick='getRevisitRecord(" . $result_value->pid . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('revisit') . "'><i class='fas fa-exchange-alt'></i></a>";
            }

            $action .= "<a href=" . base_url() . 'admin/patient/profile/' . $result_value->pid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";

            if ($result_value->is_ipd != 'yes') {
                if ($this->rbac->hasPrivilege('opd_move _patient_in_ipd', 'can_view')) {
                    $action .= "<a href=" . base_url() . 'admin/patient/moveipd/' . $result_value->pid . " data-toggle='tooltip' onclick='return confirm('" . $this->lang->line('move') . " " . $this->lang->line('patient') . " " . $this->lang->line('in') . " " . $this->lang->line('ipd') . ")' data-original-title='" . $this->lang->line('move') . " " . $this->lang->line('in') . " " . $this->lang->line('ipd') . "' class='btn btn-default btn-xs' ><i class='fas fa-share-square'></i></a>";

                }
            }
            $action .= "</div'>";
            $first_action = "<a href=" . base_url() . 'admin/patient/profile/' . $result_value->pid . ">";
            $nestedData   = array();
            $nestedData[] = $first_action . $result_value->patient_name . "</a>" . $action;
            $nestedData[] = $result_value->patient_unique_id;
            $nestedData[] = $result_value->patient_cnic;
            $nestedData[] = $result_value->guardian_name;
            $nestedData[] = $result_value->gender;
            $nestedData[] = $result_value->mobileno;
            $nestedData[] = $result_value->name . " " . $result_value->surname;
            $nestedData[] = date($this->customlib->getSchoolDateFormat(true, true), strtotime($result_value->appointment_date));
            $nestedData[] = $option;
            $data[]       = $nestedData;

        }

        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format
    }

    public function updateVisitStatus()
    {
        $updataData=array(
            'visit_status'=>$this->input->post('visit_status')
        );

        $this->db->where('id',$this->input->post('opd_id'));
        if($this->db->update('opd_details',$updataData))
        {
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }

    }

    public function doctortDepartment()
    {
        $depID=$this->input->post('depid');
        $depMultiID=$this->input->post('depid_multi');

        $this->db->select('staff.id,staff.name');
        $this->db->from('staff');
        $this->db->join('staff_roles','staff_roles.staff_id=staff.id');
        $this->db->where('staff.is_active',1);
        if($depID!=''){
            $this->db->where('department',$depID);
        }
        if($depMultiID!=''){
            $this->db->where_in('department',$depMultiID);
        }

        $result=$this->db->get()->result_array();
        echo json_encode($result);
    }

    public function checkStaffDoctor()
    {
        $depMultiID=$this->input->post('depid_multi');
        $this->db->select('staff.id,staff.name');
        $this->db->from('staff');
        $this->db->join('staff_roles','staff_roles.staff_id=staff.id');
        if($depMultiID==''){
            $this->db->where('staff_roles.role_id','3');
        }
        if($depMultiID!=''){
            $this->db->where_in('department',$depMultiID);
        }

        $result=$this->db->get()->result_array();
        echo json_encode($result);

    }

    public function getotherOpd()
    {
        $this->db->select('department.*');
        $this->db->from('department');
        $this->db->where('id !=','1');
        $result=$this->db->get()->result_array();
        $deptIDs=array_column($result, 'id');
        return $deptIDs;
    }

    public function patientLog()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/patient/patient_log');
        $data["logs"] = $this->common_model->getPatientLogs();
        $this->load->view("layout/header");
        $this->load->view("admin/patient/patient_log", $data);
        $this->load->view("layout/footer");
    }

    public function patientlog_search()
    {
        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->patient_model->search_datatablelog($where_condition);
        $total_result = $this->patient_model->search_datatablelog_count($where_condition);
        $data         = array();
        $i=1;
        foreach ($resultlist as $result_key => $result_value) {
            $dateTime = new DateTime($result_value->created_at);
            // Add 5 hours to the DateTime object
            $dateTime->add(new DateInterval('PT5H'));
            $setTime = $dateTime->format('d,M Y h:i:s A');
            $nestedData   = array();
            $nestedData[] = $i;
            $nestedData[] = ucfirst($result_value->module_name);
            $nestedData[] = $result_value->module_type;
            $nestedData[] = $result_value->invoice_no;
            //$nestedData[] = date('d,M Y h:i:s A', $this->customlib->datetostrtotime($result_value->created_at));
            $nestedData[] = $setTime;
            $nestedData[] = $result_value->name;
            $nestedData[] = $result_value->comments;
            $data[]       = $nestedData;
            $i++;
        }

        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }

    public function opd_list()
    {
        $data['resultlist']   = $this->patient_model->search_datatable($where_condition);
        $html  = $this->load->view('admin/patient/opdPatientListPdf',$data, true);
            // Load pdf library
        $this->load->library('pdf');
        $this->dompdf->set_paper("letter", "portrait");
        $customPaper = array(0,0,360,360);
        // Load HTML content
        $this->dompdf->loadHtml($html);
        ini_set('display_errors', 1);
            // Render the HTML as PDF
        $this->dompdf->render();
        $canvas =  $this->dompdf->getCanvas();
        $date=date('d-M-Y h:i:s A',strtotime(date('Y-m-d H:i:s')));
        $canvas->page_text(270, 780, "Page : {PAGE_NUM}", null, 10, [0, 0, 0]);
        $canvas->page_text(270, 760, $date, null, 10, [0, 0, 0]);
        // Output the generated PDF (1 = download and 0 = preview)
        $this->dompdf->stream("OPDLIST.pdf", array("Attachment"=>1));
    }

    public function getStaff($id)
    {
        $this->db->select('name');
        $this->db->from('staff');
        $this->db->where('id',$id);
        return $this->db->get()->row_array();
    }

    function formatNumber($number,$type='opd') {
        // Limit the number to 6 digits
        if($type=='opd'){
            $limit=6;
            $number = substr($number, -6);
        }
        if($type=='mr'){
            $limit=5;
            $number = substr($number, -5);
        }

        // Pad the number with zeros from the left side
        $formattedNumber = str_pad($number, $limit, '0', STR_PAD_LEFT);

        return $formattedNumber;
    }



}
