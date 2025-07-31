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
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/bootstrap-rtl/css/bootstrap-rtl.min.css" />
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
        $(function() {
            $('.languageselectpicker').selectpicker();
        });
    </script>
    <script type="text/javascript">
        var baseurl = "<?php echo base_url(); ?>";
        var chk_validate = "";
    </script>
    <style>
        .header {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    margin-top: 10px;
    padding-left: 15px;
}
.skin-blue .sidebar-menu>li.header {
    border-bottom: 1px solid gray!important;
    /* color: #4b646f; */
    background: none!important;
    /* text-decoration: underline; */
    border-top: 1px solid grey!important;
}
    </style>
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

            <a href="<?php echo base_url(); ?>" class="logo">
                <img style="    height: 55px;
    width: 93px;
" src="<?php echo base_url('uploads/default.png'); ?>" alt="Default Logo" />
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
                    <?php 
    $hospital = $this->session->userdata('hospital'); 
    $hospital_name = isset($hospital['hospital_name']) ? $hospital['hospital_name'] : 'N/A';
    $store_name = isset($hospital['store_name']) ? $hospital['store_name'] : 'N/A';
    $username = isset($hospital['username']) ? $hospital['username'] : 'N/A';
?>

<?php echo $hospital_name; ?> - <?php echo $store_name; ?> (<?php echo $username; ?>)

                    </span>
                </div>

                <div class="col-md-7 col-sm-9 col-xs-8">
                    <div class="pull-right">
                        <div class="navbar-custom-menu">
                           
                            <ul class="nav navbar-nav headertopmenu">


                                <?php
                                $image = '';
                                if (!empty($image)) {

                                    $file = $image;
                                } else {

                                    $file = "uploads/patient_images/no_image.png";
                                }
                                ?>
                                <li class="dropdown user-menu">
                                    <a class="dropdown-toggle" style="padding: 15px 13px;" data-toggle="dropdown" href="#" aria-expanded="false">
                                        <img src="<?php echo base_url() . $file; ?>" class="topuser-image" alt="User Image">
                                    </a>
                                    <ul class="dropdown-menu dropdown-user menuboxshadow">
                                        <li>
                                            <div class="sstopuser">
                                                <div class="ssuserleft">
                                                    <img src="<?php echo base_url() . $file; ?>" alt="User Image">
                                                </div>
                                                <div class="sstopuser-test">
                                                    <h4 style="text-transform: capitalize;"></h4>
                                                    <h5> <?php echo $this->session->userdata('hospital')['username']; ?>
                                                    </h5>
                                                    <p><?php echo $this->session->userdata('hospital')['role']; ?></p>
                                                    <!--p>demo</p-->
                                                </div>
                                                <div class="divider"></div>
                                                <div class="sspass">
                                                    <a class="" href="<?php echo base_url(); ?>hospital/hospital/changepass"><i class="fa fa-sign-out fa-fw"></i> Reset Password</a>
                                                    <a class="" href="<?php echo base_url(); ?>site/logout"><i class="fa fa-sign-out fa-fw"></i> <?php echo $this->lang->line('logout'); ?></a>
                                            
                                                </div>
                                            </div><!--./sstopuser-->
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <aside class="main-sidebar" id="alert2">
            <section class="sidebar" id="sibe-box">
                <ul class="sidebar-menu verttop2">
                    <?php
                    $current_controller = $this->uri->segment(2); // Get the first segment of the URL (e.g., 'hospital')
                    $current_action = $this->uri->segment(3); // Get the second segment of the URL (e.g., 'dashboard' or 'itemStock')
                    ?>


                    <!-- Dashboard -->
                    <!-- Divider for Dashboard -->
<li class="treeview <?php echo ($current_action == 'dashboard') ? 'active' : ''; ?>">
    <a href="<?php echo base_url(); ?>hospital/hospital/dashboard">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
</li>
<?php if ($this->session->userdata('hospital')['role'] == 'Store In-Charge' || $this->session->userdata('hospital')['role'] == 'Chief Pharmacist'): ?>

<!-- Divider for Pharmacy -->
<li class="treeview <?php echo ($current_action == 'medicineList') ? 'active' : ''; ?>">
    
    <a href="<?php echo base_url(); ?>hospital/pharmacy/medicineList">
        <i class="fas fa-mortar-pestle"></i> Add Product
    </a>
</li>
<?php if ($this->session->userdata('hospital')['role'] == 'Store In-Charge'):?>
    <li class="treeview <?php echo ($current_action == 'returnSupplierStock') ? 'active' : ''; ?>">

    <a href="<?php echo base_url(); ?>hospital/refundPharmacy/returnSupplierStock">
        <i class="fas fa-mortar-pestle"></i> Supplier Return Medicine
    </a>
</li>
<li class="treeview <?php echo ($current_action == 'returnStock') ? 'active' : ''; ?>">

    <a href="<?php echo base_url(); ?>hospital/refundPharmacy/returnStock">
        <i class="fas fa-mortar-pestle"></i> Return Medicine
    </a>
</li>
<?php endif; ?>

<?php endif; ?>
<?php if ($this->session->userdata('hospital')['role'] == 'Store In-Charge' || $this->session->userdata('hospital')['role'] == 'Chief Pharmacist'): ?>

<!-- Divider for Pharmacy -->
<li class="treeview <?php echo ($current_controller == 'pharmacy') && ($current_action == '') ? 'active' : ''; ?>">
    
    <a href="<?php echo base_url(); ?>hospital/pharmacy">
        <i class="fas fa-mortar-pestle"></i> Pharmacy
    </a>
</li>

<?php endif; ?>


<?php
// Get current URL segment (assuming CI3 routing)
$segment2 = $this->uri->segment(2);  // Example: 'store'
$segment3 = $this->uri->segment(3);  // Example: 'stock_requests'
$segment4 = $this->uri->segment(4);  // Example: 'pending', 'partial', 'approved'

// Determine the current action

// Get the type from the query parameter
$type = $this->input->get('type'); // Example: 'pending', 'partial', 'approved'

// Determine the current action
$current_action = ($this->uri->segment(3) === 'stock_requests') ? 'stock_requests' : '';

// Determine the current sub-action
$current_sub_action = in_array($type, ['pending', 'partial', 'approved']) ? $type : '';

?>

<?php if ($this->session->userdata('hospital')['role'] == 'Department Pharmacist'): ?>

<li class="treeview <?php echo ($segment3 == 'pharmacy') ? 'active' : ''; ?>">
    
    <a href="<?php echo base_url(); ?>hospital/store/pharmacy">
        <i class="fas fa-mortar-pestle"></i> Pharmacy
    </a>
</li>
<li class="treeview <?php echo ($current_controller == 'refundPharmacy') ? 'active' : ''; ?>">
    
    <a href="<?php echo base_url(); ?>hospital/refundPharmacy">
        <i class="fas fa-mortar-pestle"></i> Return Medicine
    </a>
</li>
<?php endif; ?>
<?php if ($this->session->userdata('hospital')['role'] == 'Department Pharmacist' || $this->session->userdata('hospital')['role'] == 'Chief Pharmacist' || $this->session->userdata('hospital')['role'] == 'Store In-Charge'): ?>

<!-- Divider for Pharmacy -->
<li class="treeview <?php echo ($current_controller == 'patient') ? 'active' : ''; ?>">
    <a href="#">
        <i class="fas fa-users"></i> Patient
        <i class="fas fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <!-- <li><a href="<?php echo base_url(); ?>hospital/patient/list"><i class="fas fa-list"></i> List</a></li> -->
        <li><a href="<?php echo base_url(); ?>hospital/patient/search"><i class="fas fa-procedures"></i> OPD</a></li>
        <li><a href="<?php echo base_url(); ?>hospital/patient/ipdsearch"><i class="fas fa-bed"></i> IPD</a></li>
    </ul>
</li>

<?php endif; ?>
<?php if ($this->session->userdata('hospital')['role'] == 'Department Pharmacist'): ?>

<li class="treeview <?php echo ($segment3 == 'medicineRequest') ? 'active' : ''; ?>">
<a href="#">
    <i class="fas fa-clipboard-list"></i> Medicine Requests
    <span class="pull-right-container">
        <i class="fas fa-angle-left pull-right"></i>
    </span>
</a>

    <ul class="treeview-menu">
        <li class="<?php echo ($current_sub_action == 'pending') ? 'active' : ''; ?>">
        <a href="<?php echo base_url(); ?>hospital/store/medicineRequest?type=pending">
        <i class="fas fa-hourglass-half"></i> Pending
            </a>
        </li>
        <li class="<?php echo ($current_sub_action == 'partial') ? 'active' : ''; ?>">
        <a href="<?php echo base_url(); ?>hospital/store/medicineRequest?type=partial">
        <i class="fas fa-tasks"></i> Partial
            </a>
        </li>
        <li class="<?php echo ($current_sub_action == 'rejected') ? 'active' : ''; ?>">
        <a href="<?php echo base_url(); ?>hospital/store/medicineRequest?type=rejected">
        <i class="fas fa-tasks"></i> Closed
            </a>
        </li>
        <li class="<?php echo ($current_sub_action == 'approved') ? 'active' : ''; ?>">
        <a href="<?php echo base_url(); ?>hospital/store/medicineRequest?type=approved">
        <i class="fas fa-check-circle"></i> Approved
            </a>
        </li>
    </ul>
</li>
<?php endif; ?>

<?php if ($this->session->userdata('hospital')['role'] == 'Store In-Charge'): ?>

<li class="treeview <?php echo ($segment3 == 'requests') ? 'active' : ''; ?>">
    <a href="<?php echo base_url(); ?>hospital/store/requests">
    <i class="fas fa-clipboard-list"></i> Requests
    </a>
</li>
<?php endif; ?>

<?php if ($this->session->userdata('hospital')['role'] == 'Store In-Charge' || $this->session->userdata('hospital')['role'] == 'Department Pharmacist' || $this->session->userdata('hospital')['role'] == 'Chief Pharmacist'): ?>

<li class="treeview <?php echo ($segment3 == 'supplier' || $segment3 == 'supplier_type') ? 'active' : ''; ?>">
    <a href="#">
        <i class="fas fa-cogs"></i> <span>Settings</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo ($segment3 == 'supplier_type') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>hospital/suppliertype/supplier">
                <i class="fa fa-circle-o"></i> Supplier Type
            </a>
        </li>
        <li class="<?php echo ($segment3 == 'supplier') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>hospital/medicinecategory/supplier">
                <i class="fa fa-circle-o"></i> Suppliers
            </a>
        </li>


        <li class="<?php echo ($segment3 == 'emergency_icu') ? 'active' : ''; ?>">
                <a href="<?php echo base_url(); ?>hospital/EmergencyIcu">
                    <i class="fa fa-circle-o"></i> Emergency & ICU
                </a>
            </li>
            <li class="<?php echo ($segment3 == 'printing') ? 'active' : ''; ?>">
                <a href="<?php echo base_url(); ?>hospital/printing">
                    <i class="fa fa-circle-o"></i> Printing
                </a>
            </li>

    </ul>
</li>


<?php endif; ?>

<!-- Users (only visible to Hospital Administrator) -->
<?php if ($this->session->userdata('hospital')['role'] == 'Hospital Adminstrator'): ?>
    <li class="treeview <?php echo ($segment3 == 'UserManagement') ? 'active' : ''; ?>">
        <a href="<?php echo base_url(); ?>hospital/hospital/UserManagement">
            <i class="fas fa-users"></i> Users
        </a>
    </li>
<?php endif; ?>


<?php if ($this->session->userdata('hospital')['role'] == 'Store In-Charge' || $this->session->userdata('hospital')['role'] == 'Department Pharmacist'): ?>

<li class="treeview ">
<a href="#">
    <i class="fas fa-chart-line"></i> <span>Reports</span>
    <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
    </span>
</a>

    <ul class="treeview-menu">
    <?php if ($this->session->userdata('hospital')['role'] == 'Store In-Charge'): ?>
        <li class="<?php echo ($segment3 == 'storeconsumptionreport') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>hospital/report/storeconsumptionreport">
                <i class="fa fa-circle-o"></i> Store Consumption Report
            </a>
        </li>
        <li class="<?php echo ($segment3 == 'storeyearlyconsumptionreport') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>hospital/report/storeyearlyconsumptionreport">
                <i class="fa fa-circle-o"></i> Store Yearly Consumption Report
            </a>
        </li>
    <li class="<?php echo ($segment3 == 'storePurchaseStockReport') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>hospital/report/storePurchaseStockReport">
                <i class="fa fa-circle-o"></i> Purchase Stock Report
            </a>
        </li>
        <?php endif; ?>
        
    <?php if ($this->session->userdata('hospital')['role'] == 'Department Pharmacist'): ?>

        <li class="<?php echo ($segment3 == 'storeOpeningStockReport') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>hospital/report/storeOpeningStockReport">
                <i class="fa fa-circle-o"></i> Openning Stock Report
            </a>
        </li>
        
        
        <li class="<?php echo ($segment3 == 'transferStockReport') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>hospital/pharmacy/transferStockReport">
                <i class="fa fa-circle-o"></i> Transfer Report
            </a>
        </li>

    
        
        <?php endif; ?>

        <li class="<?php echo ($segment3 == 'patientSummaryReport') ? 'active' : ''; ?>">
            <a href="<?php echo base_url(); ?>hospital/report/patientSummaryReport">
                <i class="fa fa-circle-o"></i> Patient Summary Report
            </a>
        </li>

    </ul>
</li>


<?php endif; ?>

<!-- Users (only visible to Hospital Administrator) -->
<?php if ($this->session->userdata('hospital')['role'] == 'Hospital Adminstrator'): ?>
    <li class="treeview <?php echo ($segment3 == 'UserManagement') ? 'active' : ''; ?>">
        <a href="<?php echo base_url(); ?>hospital/hospital/UserManagement">
            <i class="fas fa-users"></i> Users
        </a>
    </li>
<?php endif; ?>


                </ul>

            </section>
        </aside>
        <script>
            var base_url = "<?php echo base_url(); ?>";

            function defoult(id) {
                var defoult = $('#languageSwitcher').val();
                $.ajax({
                    type: "POST",
                    url: base_url + "patient/defoult_language/" + id,
                    data: {},
                    //dataType: "json",
                    success: function(data) {
                        successMsg("Status Change Successfully");
                        $('#languageSwitcher').html(data);

                    }
                });

                window.location.reload('true');
            }

            function set_languages(lang_id) {
                $.ajax({
                    type: "POST",
                    url: base_url + "patient/dashboard/user_language/" + lang_id,
                    data: {},
                    //dataType: "json",
                    success: function(data) {
                        successMsg("Status Change Successfully");
                        window.location.reload('true');
                    }
                });
            }
        </script>