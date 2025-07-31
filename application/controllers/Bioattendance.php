<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bioattendance extends CI_Controller {

	public function index()
    {
        $this->load->model('bio_model');
		$path='application/views/attendance_logs/';
        $attendancePush = file_get_contents('php://input');
		$this->writelog($attendancePush, 'Success: ',$path);
		$attendanceData=json_decode($attendancePush);
		$checkUser=$this->bio_model->checkBio($attendanceData->bio_id);
		$dateTime=date('Y-m-d H:i:s',strtotime($attendanceData->time));
		$saveAttendanceLog=$this->bio_model->savedTimeStatus($attendanceData->bio_id,$dateTime);
		// $data='{"device_id":"2018091328","time":"20221015081006","bio_id":"102"}';
		
    }
	
	public function writelog($string, $heading,$path)
    {
        $logsPath = FCPATH . $path . date("Y-m-d") . '/';
        $filesize =  (file_exists($logsPath . 'logs.html')) ? filesize($logsPath . 'logs.html') : 0;
        if (!file_exists($logsPath)) {
            mkdir($logsPath, 0777, true);
        }
        if ($filesize > 72343) {
            $time = time();
            rename($logsPath . 'logs.php', $logsPath . 'logs_' . $time . '.html');
        }
        @write_file($logsPath . 'logs.php', '<h2>' . $heading . '</h2><p> ' . $string . "</p> \r\n", 'a');
    }
	
	
	
	
}
