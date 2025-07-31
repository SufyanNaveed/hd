<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dashboard extends Admin_Controller
{

     public function index(){
        $this->session->set_userdata('top_menu', 'setup');
        $data['title']       = 'Dashboard';
         $this->load->view('layout/header', $data);
        $this->load->view('admin/dashboard');
        $this->load->view('layout/footer', $data);
     }
}