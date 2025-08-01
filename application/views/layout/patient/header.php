<!DOCTYPE html>
<html <?php echo $this->customlib->getRTL(); ?>>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $this->customlib->getAppName(); ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="theme-color" content="#424242" />
        <link href="<?php echo base_url(); ?>backend/images/s-favican.png" rel="shortcut icon" type="image/x-icon">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">

        <?php
$this->load->view('layout/theme');
?>
        <?php
if ($this->customlib->getRTL() != "") {
    ?>
            <!-- Bootstrap 3.3.5 RTL -->
            <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/bootstrap-rtl/css/bootstrap-rtl.min.css"/>
            <!-- Theme RTL style -->
            <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/AdminLTE-rtl.min.css" />
            <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/ss-rtlmain.css">
            <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/skins/_all-skins-rtl.min.css" />

            <?php
} else {

}
?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/all.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ionicons.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/iCheck/flat/blue.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/morris/morris.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/datepicker/datepicker3.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/daterangepicker/daterangepicker-bs3.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/sweet-alert/sweetalert2.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/custom_style.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
        <!--file dropify-->
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/dropify.min.css">
        <!--file nprogress-->
        <link href="<?php echo base_url(); ?>backend/dist/css/nprogress.css" rel="stylesheet">
        <!--print table-->
        <link href="<?php echo base_url(); ?>backend/dist/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>backend/dist/datatables/css/buttons.dataTables.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>backend/dist/datatables/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <!--print table mobile support-->
        <link href="<?php echo base_url(); ?>backend/dist/datatables/css/responsive.dataTables.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>backend/dist/datatables/css/rowReorder.dataTables.min.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>backend/datepicker/date.js"></script>
        <script src="<?php echo base_url(); ?>backend/dist/js/jquery-ui.min.js"></script>
        <script src="<?php echo base_url(); ?>backend/js/school-custom.js"></script>
        <script src="<?php echo base_url(); ?>backend/dist/js/moment.min.js"></script>
        <!-- fullCalendar -->
        <link rel="stylesheet" href="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.print.min.css" media="print">
    <!--language css-->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>/backend/dist/css/bootstrap-select.min.css">
        <script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/js/bootstrap-select.min.js"></script>

 <script type="text/javascript">
    $(function(){
      $('.languageselectpicker').selectpicker();
   });
</script>
        <script type="text/javascript">
            var baseurl = "<?php echo base_url(); ?>";
            var chk_validate = "";
        </script>
    </head>
    <body class="hold-transition skin-blue fixed sidebar-mini">
        <script type="text/javascript">
            function collapseSidebar() {

                if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
                    sessionStorage.setItem('sidebar-toggle-collapsed', '');
                } else {
                    sessionStorage.setItem('sidebar-toggle-collapsed', '1');
                }
            }

            function checksidebar() {
                if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
                    var body = document.getElementsByTagName('body')[0];
                    body.className = body.className + ' sidebar-collapse';
                }
            }
            checksidebar();
        </script>
        <div class="wrapper">
            <header class="main-header" id="alert">
                <?php
if ($_SESSION['patient']['patient_type'] == "Outpatient") {
    $url = 'patient/dashboard/profile';
} else {

    $url = 'patient/dashboard/ipdprofile';
}
?>
                <?php
$logoresult = $this->customlib->getLogoImage();
if (!empty($logoresult["image"])) {
    $logo_image = base_url() . "uploads/hospital_content/logo/" . $logoresult["image"];
} else {
    $logo_image = base_url() . "uploads/hospital_content/logo/s_logo.png";
}
if (!empty($logoresult["mini_logo"])) {
    $mini_logo = base_url() . "uploads/hospital_content/logo/" . $logoresult["mini_logo"];
} else {
    $mini_logo = base_url() . "uploads/hospital_content/logo/smalllogo.png";
}
?>

                <a href="<?php echo base_url() . $url; ?>" class="logo">
                    <span class="logo-mini"><img width="31" height="19" src="<?php echo $mini_logo; ?>" alt="<?php echo $this->customlib->getAppName() ?>" /></span>
                    <span class="logo-lg"><img src="<?php echo $logo_image; ?>" alt="<?php echo $this->customlib->getAppName() ?>" /></span>
                </a>
                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" onclick="collapseSidebar()" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <div class="col-md-5 col-sm-3 col-xs-4">
                        <span href="#" class="sidebar-session">
                       <?php echo $this->setting_model->getCurrentHospitalName(); ?>
                        </span>
                    </div>
                    <div class="col-md-7 col-sm-9 col-xs-8">
                        <div class="pull-right">
                            <div class="navbar-custom-menu">
                                
                               
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <aside class="main-sidebar" id="alert2">
                <section class="sidebar" id="sibe-box">
                    <ul class="sidebar-menu verttop2">
                        <?php if ($_SESSION['patient'] == true) {
    if ($this->module_lib->hasActive('front_office')) {
        ?>

                                   <li class="treeview <?php echo set_Topmenu('myprofile'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/appointment"><i class="fas fa-hospital-alt"></i><span><?php echo $this->lang->line('my_appointments'); ?></span>
                                        </a>
                                    </li>
                                <?php
}if ($this->module_lib->hasActive('OPD')) {
        ?>

                                    <li class="treeview <?php echo set_Topmenu('profile'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/profile">
                                            <i class="fas fa-stethoscope"></i> <span> <?php echo $this->lang->line('opd'); ?></span>
                                        </a>

                                    </li>
                                <?php
}if ($this->module_lib->hasActive('IPD')) {
        ?>

                                    <li class="treeview <?php echo set_Topmenu('ipdprofile'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/ipdprofile">
                                            <i class="fas fa-procedures" ></i> <span> <?php echo $this->lang->line('ipd'); ?></span>
                                        </a>

                                    </li>
                                <?php
}if ($this->module_lib->hasActive('pharmacy')) {
        ?>

                                    <li class="treeview <?php echo set_Topmenu('pharmacy'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/bill">
                                            <i class="fas fa-mortar-pestle"></i> <span> <?php echo $this->lang->line('pharmacy'); ?></span>
                                        </a>

                                    </li>
                                <?php
}if ($this->module_lib->hasActive('pathology')) {
        ?>
                                    <li class="treeview <?php echo set_Topmenu('pathology'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/search">
                                            <i class="fas fa-flask"></i> <span> <?php echo $this->lang->line('pathology'); ?></span>
                                        </a>

                                    </li>
                                <?php
}if ($this->module_lib->hasActive('radiology')) {
        ?>

                                    <li class="treeview <?php echo set_Topmenu('radiology'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/radioreport">
                                            <i class="fas fa-microscope"></i><span> <?php echo $this->lang->line('radiology'); ?></span>
                                        </a>

                                    </li>
                                <?php
}if ($this->module_lib->hasActive('operation_theatre')) {
        ?>
                                    <li class="treeview <?php echo set_Topmenu('operation_theatre'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/otsearch">
                                            <i class="fas fa-cut"></i><span> <?php echo $this->lang->line('operation_theatre'); ?></span>
                                        </a>

                                    </li>
                                <?php
}if ($this->module_lib->hasActive('ambulance')) {
        ?>
                                    <li class="treeview <?php echo set_Topmenu('ambulance'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/ambulance">
                                            <i class="fas fa-ambulance"></i> <span> <?php echo $this->lang->line('ambulance'); ?></span>
                                        </a>

                                    </li>
                                <?php
}if ($this->module_lib->hasActive('blood_bank')) {
        ?>
                                    <li class="treeview <?php echo set_Topmenu('blood_bank'); ?>">
                                        <a href="<?php echo base_url(); ?>patient/dashboard/bloodbank">
                                           <i class="fas fa-tint"></i> <span> <?php echo $this->lang->line('blood_bank'); ?></span>
                                        </a>

                                    </li>
 <?php
}if ($this->module_lib->hasActive('zoom_live_meeting')) {
        ?>
                                <li class="treeview <?php echo set_Topmenu('live_consult'); ?>">
                                    <a href="<?php echo base_url(); ?>patient/dashboard/liveconsult">
                                    <i class="fa fa-video-camera" aria-hidden="true"></i> <span> <?php echo $this->lang->line('live_consult'); ?></span>
                                    </a>

                                </li>
                                <?php
}

}
?>
                          
                    </ul>
                </section>
            </aside>
            <script>
    var base_url="<?php echo base_url(); ?>";
     function defoult(id){
      var defoult=  $('#languageSwitcher').val();
        $.ajax({
            type: "POST",
            url: base_url + "patient/defoult_language/"+id,
            data: {},
            //dataType: "json",
            success: function (data) {
                successMsg("Status Change Successfully");
              $('#languageSwitcher').html(data);

            }
        });

        window.location.reload('true');
    }

    // function set_languages(lang_id){
    //     $.ajax({
    //         type: "POST",
    //         url: base_url + "patient/dashboard/user_language/"+lang_id,
    //         data: {},
    //         //dataType: "json",
    //         success: function (data) {
    //             successMsg("Status Change Successfully");
    //             window.location.reload('true');
    //         }
    //     });
    // }
            </script>