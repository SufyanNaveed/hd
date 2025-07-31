<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machinedata extends CI_Controller {

	public function index()
    {
        $this->load->model('machine_model');
        $spmData=urldecode($this->input->post('spm_info'));
        $obxData=urldecode($this->input->post('obx_info'));
        $obx=explode('OBX|1||',$obxData);
        $obx=explode('||',$obx[1]);
        $parameterName=$obx[0];
        $parameterValue=explode('^',$obx[1]);
        $value=$parameterValue[0];
        $spm=explode('SPM|1|',$spmData);
        $spm=explode('^^',$spm[1]);
        $report_id=$spm[0];
        $machineInfo=array(
                'report_id'=>$report_id,
                'parameterName'=>$parameterName,
                'value'=>$value,
            );

        $this->db->insert('machine_data',$machineInfo);
        $updatePatientReport=$this->machine_model->checkTestParameter($report_id,$parameterName,$value);
        return true;
        
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

    public function tmpFunction()
    {
        $this->load->model('bio_model');
        $getData=$this->bio_model->tmpQry();
        foreach($getData as $gty){
            $fetchdata=$gty['attendance_data'];
            $data=urldecode($fetchdata);
            $exp=explode('&',$data);
            $exp=$exp[0];
            $output=str_replace('attendance=',"",$exp);
            $output=json_decode($output,true);
            $checkUser=$this->bio_model->checkBio($output['bio_id']);
		    $dateTime=date('Y-m-d H:i:s',strtotime($output['time']));
            $saveAttendanceLog=$this->bio_model->savedTimeStatus($output['bio_id'],$dateTime);


        }
        

    }

    public function bcsData()
    { 
        
        $wbc_data=urldecode($this->input->post('wbc_data'));
        $neu_data=urldecode($this->input->post('neu_data'));
        $lym_data=urldecode($this->input->post('lym_data'));
        $mon_data=urldecode($this->input->post('mon_data'));
        $eos_data=urldecode($this->input->post('eos_data'));
        $bas_data=urldecode($this->input->post('bas_data'));
        $img_data=urldecode($this->input->post('img_data'));
        $neuper_data=urldecode($this->input->post('neuper_data'));
        $eosper_data=urldecode($this->input->post('eosper_data'));
        $basper_data=urldecode($this->input->post('basper_data'));
        $imgper_data=urldecode($this->input->post('imgper_data'));
        $hct_data=urldecode($this->input->post('hct_data'));
        $mcv_data=urldecode($this->input->post('mcv_data'));
        $mch_data=urldecode($this->input->post('mch_data'));
        $mchc_data=urldecode($this->input->post('mchc_data'));
        $rdwc_data=urldecode($this->input->post('rdwc_data'));
        $sd_data=urldecode($this->input->post('sd_data'));
        $pltl_data=urldecode($this->input->post('pltl_data'));
        $pdwl_data=urldecode($this->input->post('pdwl_data'));
        $pctd_data=urldecode($this->input->post('pctd_data'));
        $plcc_data=urldecode($this->input->post('plcc_data'));
        $nrbcd_data=urldecode($this->input->post('nrbcd_data'));
        $sample_data=urldecode($this->input->post('sample_data'));
        $sample_id=explode('OBR|1|',$sample_data);$sample_id=explode('|',$sample_id[1]);
        
        if(!empty($sample_id[1])){
            $wbc=explode('||',$wbc_data);$wbc=explode('|',$wbc[1]);$wbc_parameter_name='WBC';
            isset($wbc[0]) && !empty($wbc[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$wbc_parameter_name,$wbc[0]) : '';
            $lym=explode('||',$lym_data);$lym=explode('|',$lym[1]);$lym_parameter_name='LYM';
            isset($lym[0]) && !empty($lym[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$lym_parameter_name,$lym[0]) : '';
            $neu=explode('||',$neu_data);$neu=explode('|',$neu[1]);$neu_parameter_name='NEU';
            isset($neu[0]) && !empty($neu[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$neu_parameter_name,$neu[0]) : '';
            $mon=explode('||',$mon_data);$mon=explode('|',$mon[1]);$mon_parameter_name='MON';
            isset($mon[0]) && !empty($mon[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$mon_parameter_name,$mon[0]) : '';
            $eos=explode('||',$eos_data);$eos=explode('|',$eos[1]);$eos_parameter_name='EOS';
            isset($eos[0]) && !empty($eos[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$eos_parameter_name,$eos[0]) : '';
            $bas=explode('||',$bas_data); $bas=explode('|',$bas[1]);$bas_parameter_name='BAS';
            isset($bas[0]) && !empty($bas[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$bas_parameter_name,$bas[0]) : '';
            $img=explode('||',$img_data); $img=explode('|',$img[1]);$img_parameter_name='IMG';
            isset($img[0]) && !empty($img[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$img_parameter_name,$img[0]) : '';
            $neuper=explode('||',$neuper_data);$neuper=explode('|',$neuper[1]);$neuper_parameter_name='NEU%';
            isset($neuper[0]) && !empty($neuper[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$neuper_parameter_name,$neuper[0]) : '';
            $eosper=explode('||',$eosper_data);$eosper=explode('|',$eosper[1]);$eosper_parameter_name='EOS%';
            isset($eosper[0]) && !empty($eosper[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$eosper_parameter_name,$eosper[0]) : '';
            $basper=explode('||',$basper_data);$basper=explode('|',$basper[1]);$basper_parameter_name='BAS%';
            isset($basper[0]) && !empty($basper[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$basper_parameter_name,$basper[0]) : '';
            $imgper=explode('||',$imgper_data);$imgper=explode('|',$imgper[1]);$imgper_parameter_name='IMG%';
            isset($imgper[0]) && !empty($imgper[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$imgper_parameter_name,$imgper[0]) : '';
            $hct=explode('||',$hct_data);$hct=explode('|',$hct[1]);$hct_parameter_name='HCT';
            isset($hct[0]) && !empty($hct[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$hct_parameter_name,$hct[0]) : '';
            $mcv=explode('||',$mcv_data);$mcv=explode('|',$mcv[1]);$mcv_parameter_name='MCV';
            isset($mcv[0]) && !empty($mcv[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$mcv_parameter_name,$mcv[0]) : '';
            $mch=explode('||',$mch_data); $mch=explode('|',$mch[1]);$mch_parameter_name='MCH';
            isset($mch[0]) && !empty($mch[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$mch_parameter_name,$mch[0]) : '';
            $mchc=explode('||',$mchc_data); $mchc=explode('|',$mchc[1]);$mchc_parameter_name='MCHC';
            isset($mchc[0]) && !empty($mchc[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$mchc_parameter_name,$mchc[0]) : '';
            $rdwc=explode('||',$rdwc_data);$rdwc=explode('|',$rdwc[1]);$rdwcv_parameter_name='RDW-CV';
            isset($rdwc[0]) && !empty($rdwc[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$rdwcv_parameter_name,$rdwc[0]) : '';
            $sd=explode('||',$sd_data);$sd=explode('|',$sd[1]);$rdwsd_parameter_name='RDW-SD';
            isset($sd[0]) && !empty($sd[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$rdwsd_parameter_name,$sd[0]) : '';
            $mpvd=explode('||',$mpvd_data);$mpvd=explode('|',$mpvd[1]);$mpv_parameter_name='MPV';
            isset($mpvd[0]) && !empty($mpvd[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$mpv_parameter_name,$mpvd[0]) : '';
            $pltl=explode('||',$pltl_data);$pltl=explode('|',$pltl[1]);$plt_parameter_name='PLT';
            isset($pltl[0]) && !empty($pltl[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$plt_parameter_name,$pltl[0]) : '';
            $pdwl=explode('||',$pdwl_data);$pdwl=explode('|',$pdwl[1]);$pdw_parameter_name='PDW';
            isset($pdwl[0]) && !empty($pdwl[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$pdw_parameter_name,$pdwl[0]) : '';
            $pctd=explode('||',$pctd_data);$pctd=explode('|',$pctd[1]);$pct_parameter_name='PCT';
            isset($pctd[0]) && !empty($pctd[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$pct_parameter_name,$pctd[0]) : '';
            $plcc=explode('||',$plcc_data);$plcc=explode('|',$plcc[1]);$plcc_parameter_name='PLCR';
            isset($plcc[0]) && !empty($plcc[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$plcc_parameter_name,$plcc[0]) : '';
            $nrbcd=explode('||',$nrbcd_data);$nrbcd=explode('|',$nrbcd[1]);$nrbcd_parameter_name='NRBC#';
            isset($nrbcd[0]) && !empty($nrbcd[0]) ? $this->machine_model->checkTestParameter($sample_id[1],$nrbcd_parameter_name,$nrbcd[0]) : '';

            $machine_bc_data=array(

                $wbc_parameter_name=>$wbc[0],
                $neu_parameter_name=>$neu[0],
                $lym_parameter_name=>$lym[0],
                $mon_parameter_name=>$mon[0],
                $eos_parameter_name=>$eos[0],
                $bas_parameter_name=>$bas[0],
                $img_parameter_name=>$img[0],
                $neuper_parameter_name=>$neuper[0],
                $eosper_parameter_name=>$eosper[0],
                $basper_parameter_name=>$basper[0],
                $imgper_parameter_name=>$imgper[0],
                $hct_parameter_name=>$hct[0],
                $mcv_parameter_name=>$mcv[0],
                $mch_parameter_name=>$mch[0],
                $mchc_parameter_name=>$mchc[0],
                $rdwcv_parameter_name=>$rdwc[0],
                $rdwsd_parameter_name=>$sd[0],
                $mpv_parameter_name=>$mpvd[0],
                $plt_parameter_name=>$pltl[0],
                $pdw_parameter_name=>$pdwl[0],
                $pct_parameter_name=>$pctd[0],
                $plcc_parameter_name=>$plcc[0],
                $nrbcd_parameter_name=>$nrbcd[0],
                'sample_id'=>$sample_id[1],
    
            );
            $machine_bc['device_data']= json_encode($machine_bc_data);
            if($this->db->insert('bc_two_hundred',$machine_bc))
            {
               echo  "hello";
            }else echo  "nooo";

        }
        
        
    }


	
	
	
	
}
