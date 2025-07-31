<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dashboard extends Patient_Controller
{
   
    public function dashboard()
    {
        $this->session->set_userdata('top_menu', 'myprofile');
        $this->load->view("layout/patient/header");
        $this->load->view("user/dashboard");
        $this->load->view("layout/patient/footer");
    }

}