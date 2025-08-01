<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Enc_lib');
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->config->load("mailsms");
        $marital_status = $this->config->item('marital_status');
        $bloodgroup     = $this->config->item('bloodgroup');
        $this->load->library('Customlib');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getUserImage()
    {
        $id     = $this->session->userdata["hospitaladmin"]["id"];
        $result = $this->staff_model->get($id);
    }

    public function updatePurchaseCode()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('envato_market_purchase_code', 'Purchase Code', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'email'                       => form_error('email'),
                'envato_market_purchase_code' => form_error('envato_market_purchase_code'),
            );
            $array = array('status' => '2', 'error' => $data);
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {

            $response = $this->auth->app_update();
        }
    }

    public function backup()
    {
        if (!$this->rbac->hasPrivilege('backup', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'admin/backup');
        $data['title'] = 'Backup History';
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            if ($this->input->post('backup') == "upload") {
                $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
                if ($this->form_validation->run() == false) {
                } else {
                    if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                        $fileInfo  = pathinfo($_FILES["file"]["name"]);
                        $file_name = "db-" . date("Y-m-d_H-i-s") . ".sql";
                        move_uploaded_file($_FILES["file"]["tmp_name"], "./backup/temp_uploaded/" . $file_name);
                        $folder_name  = 'temp_uploaded';
                        $path         = './backup/';
                        $file_restore = $this->load->file($path . $folder_name . '/' . $file_name, true);
                        $file_array   = explode(';', $file_restore);
                        foreach ($file_array as $query) {
                            $trimQuery1 = trim($query);
                            if (!empty($trimQuery1)) {
                                $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
                                $this->db->query($query);
                                $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                            }
                        }
                        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                        redirect('admin/admin/backup');
                    }
                }
            }
            if ($this->input->post('backup') == "backup") {
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                $this->load->helper('download');
                $this->load->dbutil();
                $filename = "db-" . date("Y-m-d_H-i-s") . ".sql";
                $prefs    = array(
                    'ignore'     => array(),
                    'format'     => 'txt',
                    'filename'   => 'mybackup.sql',
                    'add_drop'   => true,
                    'add_insert' => true,
                    'newline'    => "\n",
                );
                $backup = $this->dbutil->backup($prefs);
                $this->load->helper('file');
                write_file('./backup/database_backup/' . $filename, $backup);
                redirect('admin/admin/backup');
                force_download($filename, $backup);
                $this->session->set_flashdata('feedback', $this->lang->line('success_message_for_client_to_see'));
                redirect('admin/admin/backup');
            } else if ($this->input->post('backup') == "restore") {
                $folder_name  = 'database_backup';
                $file_name    = $this->input->post('filename');
                $path         = './backup/';
                $filePath     = $path . $folder_name . '/' . $file_name;
                $file_restore = $this->load->file($path . $folder_name . '/' . $file_name, true);
                $db           = (array) get_instance()->db;
                $conn         = mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);

                $sql   = '';
                $error = '';

                if (file_exists($filePath)) {
                    $lines = file($filePath);

                    foreach ($lines as $line) {

                        // Ignoring comments from the SQL script
                        if (substr($line, 0, 2) == '--' || $line == '') {
                            continue;
                        }

                        $sql .= $line;

                        if (substr(trim($line), -1, 1) == ';') {
                            $result = mysqli_query($conn, $sql);
                            if (!$result) {
                                $error .= mysqli_error($conn) . "\n";
                            }
                            $sql = '';
                        }
                    }
                    $msg = $this->lang->line('restored_message');
                } // end if file exists
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $msg . '</div>');
                redirect('admin/admin/backup');
            }
        }
        $dir    = "./backup/database_backup/";
        $result = array();
        $cdir   = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }
        $data['dbfileList']  = $result;
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/backup', $data);
        $this->load->view('layout/footer', $data);
    }

    public function changepass()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'changepass/index');
        $data['title'] = 'Change Password';
        $this->form_validation->set_rules('current_pass', $this->lang->line('current_password'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_pass', $this->lang->line('new_password'), 'trim|required|xss_clean|matches[confirm_pass]');
        $this->form_validation->set_rules('confirm_pass', $this->lang->line('confirm_password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $sessionData            = $this->session->userdata('loggedIn');
            $this->data['id']       = $sessionData['id'];
            $this->data['username'] = $sessionData['username'];
            $this->load->view('layout/header', $data);
            $this->load->view('admin/change_password', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $sessionData = $this->session->userdata('hospitaladmin');
            $userdata    = $this->customlib->getUserData();
            $data_array  = array(
                'current_pass' => $this->input->post('current_pass'),
                'new_pass'     => md5($this->input->post('new_pass')),
                'user_id'      => $sessionData['id'],
                'user_email'   => $sessionData['email'],
                'user_name'    => $sessionData['username'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'password' => $this->enc_lib->passHashEnc($this->input->post('new_pass')),
            );
            $check  = $this->enc_lib->passHashDyc($this->input->post('current_pass'), $userdata["password"]);
            $query1 = $this->admin_model->checkOldPass($data_array);

            if ($query1) {

                if ($check) {
                    $query2 = $this->admin_model->saveNewPass($newdata);
                    if ($query2) {
                        $data['error_message'] = "<div class='alert alert-success'>" . $this->lang->line('password_changed_successfully') . "</div>";
                        $this->load->view('layout/header', $data);
                        $this->load->view('admin/change_password', $data);
                        $this->load->view('layout/footer', $data);
                    }
                } else {
                    $data['error_message'] = "<div class='alert alert-danger'>" . $this->lang->line('invalid_current_password') . "</div>";
                    $this->load->view('layout/header', $data);
                    $this->load->view('admin/change_password', $data);
                    $this->load->view('layout/footer', $data);
                }
            } else {

                $data['error_message'] = "<div class='alert alert-danger'>" . $this->lang->line('invalid_current_password') . "</div>";
                $this->load->view('layout/header', $data);
                $this->load->view('admin/change_password', $data);
                $this->load->view('layout/footer', $data);
            }
        }
    }

    public function pdf_report()
    {
        $data        = array();
        $html        = $this->load->view('reports/students_detail', $data, true);
        $pdfFilePath = "output_pdf_name.pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function downloadbackup($file)
    {
        $this->load->helper('download');
        $filepath = "./backup/database_backup/" . $file;
        $data     = file_get_contents($filepath);
        $name     = $file;
        force_download($name, $data);
    }

    public function dropbackup($file)
    {
        if (!$this->rbac->hasPrivilege('backup', 'can_delete')) {
            access_denied();
        }
        unlink('./backup/database_backup/' . $file);
        redirect('admin/admin/backup');
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/patient');
        $data['title']       = 'Search';
        $search_text         = $this->input->post('search_text');
        $data['search_text'] = trim($this->input->post('search_text'));
        $userdata            = $this->customlib->getUserData();
        if (!empty($search_text)) {
            $resultlist         = $this->patient_model->searchAll($search_text);
            $data['resultlist'] = $resultlist;
        }
        $this->load->view('layout/header', $data);
        if (!empty($search_text)) {
            $this->load->view('admin/search_result', $data);
        } else {
            $this->load->view('admin/search', $data);
        }
        $this->load->view('layout/footer', $data);
    }

    public function patient_search()
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
        $resultlist   = $this->patient_model->searchpatient_datatable($where_condition);
        $total_result = $this->patient_model->searchpatient_datatable_count($where_condition);
        $data         = array();

        foreach ($resultlist as $result_key => $result_value) {
            $url = '#';
            if ($result_value->age) {
                $age = $result_value->age . " " . $this->lang->line("years");
            } else {
                $age = " 0 " . $this->lang->line("years");
            }
            if ($result_value->month) {
                $month = ", " . $result_value->month . " " . $this->lang->line("month");
            } else {
                $month = ", " . " 0 " . $this->lang->line("month");
            }

            $action = "<a href='#' onclick='getpatientData(" . $result_value->id . ")' class='btn btn-default btn-xs'  data-toggle='modal' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

            $action .= "<div class='btn-group' style='margin-left:2px;'>";
            $action .= "<a href='#' style='width: 20px;border-radius: 2px;' class='btn btn-default btn-xs'  data-toggle='dropdown' title='" . $this->lang->line('show') . "'><i class='fa fa-ellipsis-v'></i></a>";
            $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";
            $action .= "<li><a href='#' onclick='editRecord(" . $result_value->id . ")'>Edit</a></li>";
            $action .= "<li><a href='#' onclick='delete_record(" . $result_value->id . ")'>Delete</a></li>";
            $action .= "</ul>";

            $action .= "</div>";
            $first_action = "<a href='#' onclick='getpatientData(" . $result_value->id . ")' class='btn btn-default btn-xs'  data-toggle='modal' title=''>";
            $nestedData   = array();
            $nestedData[] = $result_value->patient_unique_id;
            $nestedData[] = $first_action . $result_value->patient_name . "</a>";
            $nestedData[] = $result_value->gender;
            $nestedData[] = $result_value->mobileno;
            $nestedData[] = $result_value->address;
            $nestedData[] = $action;
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

    public function disablepatient()
    {
        $data['title']       = 'Search';
        $search_text         = $this->input->post('search_text');
        $data['search_text'] = trim($this->input->post('search_text'));
        $userdata            = $this->customlib->getUserData();
        $resultlist          = $this->patient_model->searchAlldisable($search_text);
        $data['resultlist']  = $resultlist;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/searchdisablepatient', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getCollectionbymonth()
    {
        $result = $this->admin_model->getMonthlyCollection();
        return $result;
    }

    public function getCollectionbyday($date)
    {
        $result = $this->admin_model->getCollectionbyDay($date);
        if ($result[0]['amount'] == "") {
            $return = 0;
        } else {
            $return = $result[0]['amount'];
        }
        return $return;
    }

    public function getExpensebyday($date)
    {
        $result = $this->admin_model->getExpensebyDay($date);
        if ($result[0]['amount'] == "") {
            $return = 0;
        } else {
            $return = $result[0]['amount'];
        }
        return $return;
    }

    public function getExpensebymonth()
    {
        $result = $this->admin_model->getMonthlyExpense();
        return $result;
    }

    public function whatever($feecollection_array, $start_month_date, $end_month_date)
    {
        $return_amount = 0;
        $st_date       = strtotime($start_month_date);
        $ed_date       = strtotime($end_month_date);
        if (!empty($feecollection_array)) {
            while ($st_date <= $ed_date) {
                $date = date('Y-m-d', $st_date);
                foreach ($feecollection_array as $key => $value) {
                    if ($value['date'] == $date) {
                        $return_amount = $return_amount + $value['amount'] + $value['amount_fine'];
                    }
                }
                $st_date = $st_date + 86400;
            }
        } else {
        }

        return $return_amount;
    }

    public function startmonthandend()
    {
        $startmonth = $this->setting_model->getStartMonth();
        if ($startmonth == 1) {
            $endmonth = 12;
        } else {
            $endmonth = $startmonth - 1;
        }
        return array($startmonth, $endmonth);
    }

    public function handle_upload()
    {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('sql');
            $temp        = explode(".", $_FILES["file"]["name"]);
            $extension   = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'application/octet-stream') {

                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array(strtolower($extension), $allowedExts)) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($_FILES["file"]["size"] > 10240000) {

                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than_100kB'));
                return false;
            }
            return true;
        } else {
            $this->form_validation->set_message('handle_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
    }

    public function generate_key($length = 12)
    {

        $str        = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max        = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function addCronsecretkey($id)
    {
        $key  = $this->generate_key(25);
        $data = array('cron_secret_key' => $key);
        $this->setting_model->add_cronsecretkey($data, $id);
        redirect('admin/admin/backup');
    }

    public function dashboard()
    {
        $this->session->set_userdata('top_menu', '');
        $this->session->set_userdata('sub_menu', '');
        $role                        = $this->customlib->getStaffRole();
        $role_id                     = json_decode($role)->id;
        $staffid                     = $this->customlib->getStaffID();
        $notifications               = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);
        $data['notifications']       = $notifications;
        $systemnotifications         = $this->notification_model->getUnreadNotification();
        $data['systemnotifications'] = $systemnotifications;
        $Current_year                = date('Y');
        $Next_year                   = date("Y");
        $current_date                = date('Y-m-d');
        $data['title']               = 'Dashboard';
        $Current_start_date          = date('01');
        $Current_date                = date('d');
        $Current_month               = date('m');
        $month_collection            = 0;
        $month_expense               = 0;
        $total_opd_patients          = 0;
        $total_ipd_patients          = 0;
        $ar[0]                       = 01;
        $ar[1]                       = 12;
        $year_str_month              = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month              = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        //======================Current Month Collection ==============================
        $first_day_this_month = date('Y-m-01');



        $tot_roles            = $this->role_model->get();
        foreach ($tot_roles as $key => $value) {
            if ($value["id"] != 1) {
                $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);
            }
        }
        $data["roles"]       = $count_roles;
        $expense             = $this->expense_model->getTotalExpenseBwdate(date('Y-m-01'), date('Y-m-t'));
        $data["expense"]     = $expense;
        $start_month         = strtotime($year_str_month);
        $start               = strtotime($year_str_month);
        $end                 = strtotime($year_end_month);
        $coll_month          = array();
        $s                   = array();
        $ex                  = array();
        $total_month         = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_month <= $end) {
            $total_month[] = $this->lang->line(date('M', $start_month));
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);

            $return = $this->patient_model->getIncome($month_start, $month_end);
            if (!empty($return)) {
                $at  = 0;
                $s[] = $at + $return->amount;
            } else {
                $s[] = "0.00";
            }
            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);
            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }
            $start_month = strtotime("+1 month", $start_month);
        }
        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;
        $event_colors              = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"]      = $event_colors;
        $userdata                  = $this->customlib->getUserData();
        $data["role"]              = $userdata["user_type"];
        $search                    = array('date >=' => date('Y-m-01'), 'date <=' => date("Y-m-t"));

        $parameter = array(
            'opd'                    => $this->patient_model->getEarning('amount', 'opd_details', 'status', 'generated', array('appointment_date >=' => date('Y-m-01'), 'appointment_date <=' => date('Y-m-t'))) + ($this->patient_model->getEarning('paid_amount', 'opd_payment', '', '', $search)) + $this->patient_model->getEarning('net_amount', 'opd_billing', '', '', $search),
            'ipd'                    => $this->patient_model->getEarning('paid_amount', 'payment', '', '', $search) + $this->patient_model->getEarning('net_amount', 'ipd_billing', '', '', $search),
            'pharmacy'               => $this->patient_model->getEarning('net_amount', 'pharmacy_bill_basic', '', '', $search),
            'pathology'              => $this->patient_model->getPathologyEarning(array('pathology_report.reporting_date >=' => date('Y-m-01'), 'pathology_report.reporting_date <=' => date('Y-m-t'), 'pathology_report.status' => 'generated')),
            'radiology'              => $this->patient_model->getRadiologyEarning(array('radiology_report.reporting_date >=' => date('Y-m-01'), 'radiology_report.reporting_date <=' => date('Y-m-t'), 'radiology_report.status' => 'generated')),
            'operation_theatre'      => $this->patient_model->getOTEarning(array('operation_theatre.date >=' => date('Y-m-01'), 'operation_theatre.date <=' => date('Y-m-t'))),
            'blood_bank'             => $this->patient_model->getEarning('amount', 'blood_issue', '', '', array('date_of_issue >=' => date('Y-m-01'), 'date_of_issue <=' => date('Y-m-t'))),
            'ambulance'              => $this->patient_model->getEarning('amount', 'ambulance_call', '', '', array('created_at >=' => date('Y-m-01'), 'created_at <=' => date('Y-m-t'))),
            'general'                => $this->income_model->getTotal($search),
        );

        $label  = array($this->lang->line('opd'), $this->lang->line('ipd'), $this->lang->line('pharmacy'), $this->lang->line('pathology'), $this->lang->line('radiology'), $this->lang->line('operation_theatre'), $this->lang->line('blood_bank'), $this->lang->line('ambulance'), $this->lang->line('general') . " " . $this->lang->line('income'));
        $module = array('OPD', 'IPD', 'pharmacy', 'pathology', 'radiology', 'operation_theatre', 'blood_bank', 'ambulance', 'income');

        $tot_data = array_sum($parameter);
        $jsonarr  = array();
        $i        = 0;

        foreach ($parameter as $key => $value) {
            $data[$key . "_income"] = number_format($value, 2);
            if (($this->module_lib->hasActive($module[$i]))) {
                if ($tot_data != 0) {
                    $jsonarr['value'][] = round((($value / $tot_data) * 100), 0);
                    $jsonarr['label'][] = $label[$i];
                } else {
                    $jsonarr['value'][] = 0;
                    $jsonarr['label'][] = $label[$i];
                }
            }
            if ($tot_data != 0) {
                $data[$key . "_cdata"] = ($value / $tot_data) * 100;
            } else {
                $data[$key . "_cdata"] = 0;
            }

            $i++;
        }
        $data['jsonarr'] = $jsonarr;
        $data['hospitals'] = $this->user_model->get_hospital_count();
        $data['pharmacies_count'] = $this->user_model->get_pharmacies_count('hospital');
        $data['department_store_count'] = $this->user_model->get_pharmacies_count('department');
        $data['total_products'] = $this->user_model->get_products_count();
        $data['total_requested'] = $this->user_model->total_requested();
        $data['total_approved'] = $this->user_model->total_approved('approved');
        $data['total_rejected'] = $this->user_model->total_approved('rejected');
        $data['total_patients'] = $this->user_model->total_patients();
        $data['total_ipd_patients'] = $this->user_model->total_patients('ipd');
        $data['total_opd_patients'] = $this->user_model->total_patients('opd');
        $data['total_in_stock_items'] =  $this->pharmacy_model->get_stock_summary();
        $data['total_in_stock_supplier_items'] =  $this->pharmacy_model->get_supplier_stock_summary_Admin();
        
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $data['out_stock_items'] =  $this->pharmacy_model->search_datatable_count($where_condition,"0");
        $data['in_stock_items'] =  $this->pharmacy_model->search_datatable_count($where_condition,"1");

        $data['total_issued_medicine'] =  $this->pharmacy_model->get_issued_medicine_stats();
        $data['total_requests'] = $data['total_approved'] + $data['total_rejected'] + $data['total_requested'];

        $data['total_expiry_count_this_week'] =  $this->pharmacy_model->get_expiry_count_this_week();
// print_r($data['total_expiry_count_this_week']);exit;
        // echo "<pre>"; print_r($data);exit;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('layout/footer', $data);
    }

    public function updateandappCode()
    {
        $this->form_validation->set_rules('app-email', 'Email', 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('app-envato_market_purchase_code', 'Purchase Code', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'app-email'                       => form_error('app-email'),
                'app-envato_market_purchase_code' => form_error('app-envato_market_purchase_code'),
            );
            $array = array('status' => '2', 'error' => $data);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {
            //==================
            $response = $this->auth->andapp_update();
        }
    }

   
    public function getPatientStats()
    {
        $type = $this->input->get('type'); // Get filter type (monthly, weekly, today)
        $this->load->model('Patient_model');
    
        if ($type === 'weekly') {
            $data = $this->Patient_model->get_weekly_data();
        } elseif ($type === 'today') {
            $data = $this->Patient_model->get_today_data();
        } else {
            $data = $this->Patient_model->get_monthly_data();
        }
    
        // Set JSON response header
        echo json_encode($data);
    }

    
    public function supplierList(){
        $id =$this->input->post('id');
        $response         = $this->medicine_category_model->getSupplierByType(null, null, null,$id);
        echo json_encode($response);
         
    }
    
}
