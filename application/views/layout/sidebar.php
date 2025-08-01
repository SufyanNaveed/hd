<?php
$method_name = $this->router->fetch_method();
?>
<aside class="main-sidebar" id="alert2">
    <?php if ($this->rbac->hasPrivilege('student', 'can_view')) { ?>
        <form class="navbar-form navbar-left search-form2" role="search" action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
            <?php echo $this->customlib->getCSRF(); ?>
            <div class="input-group ">
                <input type="text" name="search_text" class="form-control search-form" placeholder="<?php echo $this->lang->line('search_by_name'); ?>">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" style="padding: 3px 12px !important;border-radius: 0px 30px 30px 0px; background: #fff;" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
    <?php } ?>
    <section class="sidebar" id="sibe-box">
        <?php $this->load->view('layout/top_sidemenu'); ?>
        <ul class="sidebar-menu verttop">
        <?php
                    $current_url = $this->uri->segment(2); // Get the second segment of the URL
                    ?>
                    <li class="treeview <?php echo ($current_url == 'dashboard') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/admin/dashboard">
                        <i class="fas fa-home"></i> <span>Dashboard</span>
                        </a>
                    </li>
            <?php
            if ($this->module_lib->hasActive('front_office')) {
                if ($this->rbac->hasPrivilege('appointment', 'can_view')) {
            ?>
                    <li class="treeview <?php echo set_Topmenu('front_office'); ?>">
                        <a href="<?php echo base_url(); ?>admin/appointment/search">
                            <i class="fas fa-dungeon"></i> <span><?php echo $this->lang->line('front_office'); ?></span>
                        </a>
                    </li>
            <?php
                }
            }
            ?>
            <?php
            if ($this->module_lib->hasActive('emergency_patient')) {
                if ($this->rbac->hasPrivilege('emergency_patient', 'can_view')) {
            ?>
                    <li class="treeview <?php if ($method_name == 'emgpatients') {
                                            echo 'active';
                                        } ?>">
                        <a href="<?php echo base_url(); ?>admin/patient/emgpatients">
                            <i class="fas fa-stethoscope"></i> <span> <?php echo $this->lang->line('emergency_patient'); ?></span>
                        </a>
                    </li>
            <?php }
            }
            ?>
            <?php

            if ($this->rbac->hasPrivilege('quee_list', 'can_view')) {
            ?>
                <!-- <li class="treeview <?php if ($method_name == 'emgpatients') {
                                                echo 'active';
                                            } ?>">
                        <a href="<?php echo base_url(); ?>admin/patient/queeList">
                            <i class="fas fa-stethoscope"></i> <span> <?php echo $this->lang->line('queue'); ?></span>
                        </a>
                    </li> -->
            <?php }

            ?>

            <?php
            if ($this->module_lib->hasActive('OPD')) {
                if ($this->rbac->hasPrivilege('opd_patient', 'can_view')) {
                    // echo set_Topmenu('OPD_Out_Patient');
            ?>
                    <li class="treeview <?php if ($method_name == 'search') {
                                            echo 'active';
                                        } ?>">
                        <a href="<?php echo base_url(); ?>admin/patient/search">
                            <i class="fas fa-stethoscope"></i> <span> <?php echo $this->lang->line('opd_out_patient'); ?></span>
                        </a>
                    </li>
            <?php }
            }
            ?>

            <?php
            if ($this->module_lib->hasActive('IPD')) {
                if ($this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
            ?>
                    <li class="treeview <?php echo set_Topmenu('IPD_in_patient'); ?>">
                        <a href="<?php echo base_url() ?>admin/patient/ipdsearch">
                            <i class="fas fa-procedures" aria-hidden="true"></i> <span> <?php echo $this->lang->line('ipd_in_patient'); ?></span>
                        </a>
                    </li>
            <?php }
            }
            ?>
            <?php
            if ($this->module_lib->hasActive('pharmacy')) {
                if ($this->rbac->hasPrivilege('pharmacy bill', 'can_view')) {
            ?>
                    <li  class="treeview <?php echo set_Topmenu('pharmacy'); ?> <?php echo ($current_url == 'pharmacy') ? 'active' : ''; ?>"">
                        <a href="<?php echo base_url(); ?>admin/pharmacy/search">
                            <i class="fas fa-mortar-pestle"></i> <span> <?php echo $this->lang->line('pharmacy'); ?></span>
                        </a>
                    </li>
            <?php }
            }
            ?>
            <?php
            if ($this->module_lib->hasActive('pathology')) {
                if ($this->rbac->hasPrivilege('pathology test', 'can_view')) {
            ?>
                    <li class="treeview <?php echo set_Topmenu('pathology'); ?>">
                        <a href="<?php echo base_url(); ?>admin/pathology/search">
                            <i class="fas fa-flask"></i> <span><?php echo $this->lang->line('pathology'); ?></span>
                        </a>
                    </li>
            <?php }
            }
            ?>
            <?php
            if ($this->module_lib->hasActive('radiology')) {
                if ($this->rbac->hasPrivilege('radiology test', 'can_view')) {
            ?>
                    <li class="treeview <?php echo set_Topmenu('radiology'); ?>">
                        <a href="<?php echo base_url(); ?>admin/radio/search">
                            <i class="fas fa-microscope"></i> <span><?php echo $this->lang->line('radiology'); ?></span>
                        </a>
                    </li>
            <?php }
            }
            ?>
            <?php
            if ($this->module_lib->hasActive('operation_theatre')) {
                if ($this->rbac->hasPrivilege('ot_patient', 'can_view')) {
            ?>
                    <li class="treeview <?php echo set_Topmenu('operation_theatre'); ?> ">
                        <a href="<?php echo base_url() ?>admin/operationtheatre/otsearch">
                            <i class="fas fa-cut"></i> <span><?php echo $this->lang->line('operation_theatre'); ?></span>
                        </a>
                    </li>
            <?php }
            }
            ?>
            <?php
            if ($this->module_lib->hasActive('blood_bank')) {
                if ($this->rbac->hasPrivilege('blood_bank_status', 'can_view')) {
            ?>
                    <li class="treeview <?php echo set_Topmenu('blood_bank'); ?>">
                        <a href="<?php echo base_url() ?>admin/bloodbankstatus/">
                            <i class="fas fa-tint"></i> <span><?php echo $this->lang->line('blood_bank'); ?></span>
                        </a>
                    </li>
            <?php }
            }
            ?>
            <?php if ($this->module_lib->hasActive('zoom_live_meeting')) {
                if (($this->rbac->hasPrivilege('live_consultation', 'can_view')) || ($this->rbac->hasPrivilege('live_meeting', 'can_view'))) { ?>
                    <li class="treeview <?php echo set_Topmenu('conference'); ?>">
                        <a href="#">
                            <i class="fa fa-video-camera ftlayer"></i> <span><?php echo $this->lang->line('live_consult'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if ($this->rbac->hasPrivilege('live_consultation', 'can_view')) { ?>
                                <li class="<?php echo set_Submenu('conference/live_consult'); ?>"><a href="<?php echo base_url('admin/conference/consult'); ?>"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('live_consult'); ?></a></li>
                            <?php }
                            if ($this->rbac->hasPrivilege('live_meeting', 'can_view')) { ?>
                                <li class="<?php echo set_Submenu('conference/live_meeting'); ?>"><a href="<?php echo base_url('admin/conference/meeting'); ?>"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('live_meeting'); ?> </a></li>
                            <?php } ?>
                        </ul>
                    </li>
            <?php
                }
            } ?>
            <?php
            if ($this->module_lib->hasActive('tpa_management')) {
                if ($this->rbac->hasPrivilege('organisation', 'can_view')) {
            ?>
                    <li class="treeview <?php echo set_Topmenu('tpa_management'); ?>">
                        <a href="<?php echo base_url() ?>admin/tpamanagement">
                            <i class="fas fa-umbrella"></i> <span><?php echo $this->lang->line('tpa_management'); ?></span>
                        </a>
                    </li>
                <?php
                }
            }


            //IPD Commission

            if (($this->module_lib->hasActive('income')) || ($this->module_lib->hasActive('expense'))) {
                if (($this->rbac->hasPrivilege('income', 'can_view')) || ($this->rbac->hasPrivilege('expense', 'can_view'))) {
                ?>
                    <li class="treeview <?php echo set_Topmenu('IPD Commission'); ?>">
                        <a href="<?php echo base_url(); ?>admin/patient/search">
                            <i class="fas fa-money-bill-wave"></i> <span>IPD Commission</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php
                            if ($this->module_lib->hasActive('income')) {
                                if ($this->rbac->hasPrivilege('income', 'can_view')) {
                            ?>
                                    <li class="<?php echo set_Submenu('ipd/commission'); ?>"><a href="<?php echo base_url(); ?>admin/ipd/commission"><i class="fas fa-angle-right"></i> View Details </a></li>
                                <?php
                                }
                            }
                            if ($this->module_lib->hasActive('expense')) {
                                if ($this->rbac->hasPrivilege('expense', 'can_view')) {
                                ?>
                                    <!-- <li class="<?php echo set_Submenu('ipd/commission-report'); ?>"><a href="<?php echo base_url(); ?>admin/ipd/commissionreport"><i class="fas fa-angle-right"></i> Commission Report</a></li> -->
                            <?php }
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
            }

            if (($this->module_lib->hasActive('income')) || ($this->module_lib->hasActive('expense'))) {
                if (($this->rbac->hasPrivilege('income', 'can_view')) || ($this->rbac->hasPrivilege('expense', 'can_view'))) {
                ?>
                    <li class="treeview <?php echo set_Topmenu('finance'); ?>">
                        <a href="<?php echo base_url(); ?>admin/patient/search">
                            <i class="fas fa-money-bill-wave"></i> <span><?php echo $this->lang->line('finance'); ?></span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php
                            if ($this->module_lib->hasActive('income')) {
                                if ($this->rbac->hasPrivilege('income', 'can_view')) {
                            ?>
                                    <li class="<?php echo set_Submenu('income/index'); ?>"><a href="<?php echo base_url(); ?>admin/income"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('income'); ?> </a></li>
                                <?php
                                }
                            }
                            if ($this->module_lib->hasActive('expense')) {
                                if ($this->rbac->hasPrivilege('expense', 'can_view')) {
                                ?>
                                    <li class="<?php echo set_Submenu('expense/index'); ?>"><a href="<?php echo base_url(); ?>admin/expense"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('expenses'); ?></a></li>
                            <?php }
                            }
                            ?>
                        </ul>
                    </li>
                <?php
                }
            }

            if ($this->module_lib->hasActive('ambulance')) {
                if ($this->rbac->hasPrivilege('ambulance', 'can_view')) {
                ?>
                    <li class="treeview <?php echo set_Topmenu('Transport'); ?>">
                        <a href="<?php echo base_url(); ?>admin/vehicle/search">
                            <i class="fas fa-ambulance" aria-hidden="true"></i>
                            <span> <?php echo $this->lang->line('ambulance'); ?></span>
                        </a>
                    </li>
                <?php
                }
            }

            if ($this->module_lib->hasActive('human_resource')) {
                if (($this->rbac->hasPrivilege('staff', 'can_view') ||
                    $this->rbac->hasPrivilege('staff_attendance', 'can_view') ||
                    $this->rbac->hasPrivilege('staff_attendance_report', 'can_view') ||
                    $this->rbac->hasPrivilege('staff_payroll', 'can_view') ||
                    $this->rbac->hasPrivilege('payroll_report', 'can_view'))) {
                ?>

                    <!-- <li class="treeview <?php echo set_Topmenu('HR'); ?>">
                        <a href="<?php echo base_url(); ?>admin/staff">
                            <i class="fas fa-sitemap"></i> <span><?php echo $this->lang->line('human_resource'); ?></span>
                        </a>
                    </li> -->
                  
                    <li class="treeview <?php echo ($current_url == 'hospital') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/hospital">
                            <i class="fas fa-hospital"></i> <span>Hospitals</span>
                        </a>
                    </li>
                    <li class="treeview <?php echo ($current_url == 'store') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/store">
                            <i class="fas fa-warehouse"></i> <span>Stores</span>
                        </a>
                    </li>
                    <li class="treeview <?php echo ($current_url == 'department') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/department/list">
                            <i class="fas fa-th-list"></i> <span>Departments</span>
                        </a>
                    </li>
                    <?php
                    $next_url = $this->uri->segment(3); // Get the second segment of the URL
                    
                    ?>
                    <li class="treeview <?php echo ($current_url == 'medicinedosage' && $next_url != 'medicinInstruction')  ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/medicinedosage">
                            <i class="fas fa-th-list"></i> <span>Dosage</span>
                        </a>
                    </li>
                  
                    <li class="treeview <?php echo ($next_url == 'medicinInstruction') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/medicinedosage/medicinInstruction">
                            <i class="fas fa-th-list"></i> <span>Medicine Instruction</span>
                        </a>
                    </li>
                    <?php
                    $current_url = $this->uri->segment(2); // Get the second segment of the URL
                    ?>

                    <li class="treeview <?php echo ($current_url == 'roles' || $current_url == 'UserManagement') ? 'active menu-open' : ''; ?>">
                        <a href="<?php echo base_url(); ?>">
                            <i class="fas fa-cogs"></i> <span>User Management</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu" style="<?php echo ($current_url == 'roles' || $current_url == 'UserManagement') ? 'display: block;' : 'display: none;'; ?>">
                            <li class="<?php echo ($current_url == 'roles') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/roles">
                                    <i class="fas fa-user-tag"></i> Roles
                                </a>
                            </li>
                            <li class="<?php echo ($current_url == 'UserManagement') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/UserManagement">
                                    <i class="fas fa-users"></i> Users
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview <?php echo ($current_url == 'roles' || $current_url == 'Reports') ? 'active menu-open' : ''; ?>">
                        <a href="<?php echo base_url(); ?>">
                            <i class="fas fa-cogs"></i> <span>Reports</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu" style="<?php echo ($current_url == 'roles' || $current_url == 'Report') ? 'display: block;' : 'display: none;'; ?>">
                           
                        <li class="<?php echo ($current_url == 'Report') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/report/storeconsumptionreport">
                                    <i class="fas fa-users"></i> Store Consumption Report
                                </a>
                            </li>
                            <li class="<?php echo ($current_url == 'Report') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/report/storeyearlyconsumptionreport">
                                    <i class="fas fa-users"></i> Store Yearly Consumption Report
                                </a>
                            </li>
                            <li class="<?php echo ($current_url == 'Report') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/report/storePurchaseStockReport">
                                    <i class="fas fa-users"></i> Purchase Stock Report
                                </a>
                            </li>
                            <li class="<?php echo ($current_url == 'Report') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/report/storeOpeningStockReport">
                                    <i class="fas fa-users"></i> Opening Stock Report
                                </a>
                            </li>
                             <li class="<?php echo ($current_url == 'Report') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/report/productExpiryReport">
                                    <i class="fas fa-users"></i> Product Expiry Report
                                </a>
                            </li>
                            <li class="<?php echo ($current_url == 'Report') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/report/patientSummaryReport">
                                    <i class="fas fa-users"></i> Paitent Summary Report
                                </a>
                            </li>

                            <li class="<?php echo ($current_url == 'Report') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/report/patientMedicineReport">
                                    <i class="fas fa-users"></i> Paitent Medicine Report
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- <li class="treeview <?php if ($method_name == 'search') {
                                            echo 'active';
                                        } ?>">
                        <a href="<?php echo base_url(); ?>admin/patient/search">
                            <i class="fas fa-stethoscope"></i> <span> <?php echo $this->lang->line('opd_out_patient'); ?></span>
                        </a>
                    </li>
                    <li class="treeview <?php echo set_Topmenu('IPD_in_patient'); ?>">
                        <a href="<?php echo base_url() ?>admin/patient/ipdsearch">
                            <i class="fas fa-procedures" aria-hidden="true"></i> <span> <?php echo $this->lang->line('ipd_in_patient'); ?></span>
                        </a>
                    </li> -->
                <?php
                }
            }

            if ($this->module_lib->hasActive('communicate')) {
                if (($this->rbac->hasPrivilege('notice_board', 'can_view') ||
                    $this->rbac->hasPrivilege('email_sms', 'can_view') ||
                    $this->rbac->hasPrivilege('email_sms_log', 'can_view'))) {
                ?>
                    <!-- <li class="treeview <?php echo set_Topmenu('Messaging'); ?>">
                        <a href= "<?php echo base_url(); ?>admin/notification">
                            <i class = "fas fa-bullhorn"></i> <span><?php echo $this->lang->line('messaging'); ?></span>
                        </a>
                    </li> -->
                <?php
                }
            }
            if ($this->module_lib->hasActive('download_center')) {
                if (($this->rbac->hasPrivilege('upload_content', 'can_view'))) {
                ?>
                    <li class="treeview <?php echo set_Topmenu('Download Center'); ?>">
                        <a href="<?php echo base_url(); ?>admin/content">
                            <i class="fas fa-download"></i> <span><?php echo $this->lang->line('download_center'); ?></span>
                        </a>
                    </li>
                <?php
                }
            }

            if ($this->module_lib->hasActive('inventory')) {
                if (($this->rbac->hasPrivilege('issue_item', 'can_view') ||
                    $this->rbac->hasPrivilege('item_stock', 'can_view') ||
                    $this->rbac->hasPrivilege('item', 'can_view') ||
                    $this->rbac->hasPrivilege('item_category', 'can_view') ||
                    $this->rbac->hasPrivilege('item_category', 'can_view') ||
                    $this->rbac->hasPrivilege('store', 'can_view') ||
                    $this->rbac->hasPrivilege('supplier', 'can_view'))) {
                ?>
                    <!-- <li class="treeview <?php echo set_Topmenu('Inventory'); ?>">
                        <a href="<?php echo base_url(); ?>admin/itemstock">
                            <i class="fas fa-luggage-cart"></i> <span><?php echo $this->lang->line('inventory'); ?></span>
                        </a>
                    </li> -->
                <?php
                }
            }
            if ($this->module_lib->hasActive('front_cms')) {
                if (($this->rbac->hasPrivilege('event', 'can_view') ||
                    $this->rbac->hasPrivilege('gallery', 'can_view') ||
                    $this->rbac->hasPrivilege('notice', 'can_view') ||
                    $this->rbac->hasPrivilege('media_manager', 'can_view') ||
                    $this->rbac->hasPrivilege('pages', 'can_view') ||
                    $this->rbac->hasPrivilege('menus', 'can_view') ||
                    $this->rbac->hasPrivilege('banner_images', 'can_view'))) {
                ?>
                    <li class="treeview <?php echo set_Topmenu('Front CMS'); ?>">
                        <a href="<?php echo base_url(); ?>admin/front/page">
                            <i class="fas fa-solar-panel"></i> <span><?php echo $this->lang->line('front_cms'); ?></span>
                        </a>
                    </li>
                <?php
                }
            }

            
$current_top_menu = $this->uri->segment(1); // Get the first segment of the URL

            if (($this->rbac->hasPrivilege('general_setting', 'can_view')) || ($this->rbac->hasPrivilege('charges', 'can_view')) || ($this->rbac->hasPrivilege('bed_status', 'can_view')) || ($this->rbac->hasPrivilege('opd_prescription_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ipd_prescription_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('pharmacy_bill_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('setup_front_office', 'can_view')) || ($this->rbac->hasPrivilege('medicine_category', 'can_view')) || ($this->rbac->hasPrivilege('pathology_category', 'can_view')) || ($this->rbac->hasPrivilege('radiology_category', 'can_view')) || ($this->rbac->hasPrivilege('income_head', 'can_view')) || $this->rbac->hasPrivilege('leave_types', 'can_view') || ($this->rbac->hasPrivilege('item_category', 'can_view')) || ($this->rbac->hasPrivilege('hospital_charges', 'can_view')) || ($this->rbac->hasPrivilege('medicine_supplier', 'can_view')) || ($this->rbac->hasPrivilege('medicine_dosage', 'can_view'))) {
                ?>
<!-- <li class="treeview <?php echo ($current_top_menu == 'setup' || $current_top_menu == 'admin') ? 'active menu-open' : ''; ?>">
<a href="<?php echo base_url(); ?>"> -->
                        <!-- <i class="fas fa-cogs"></i> <span><?php echo $this->lang->line('setup'); ?></span> <i class="fa fa-angle-left pull-right"></i> -->
                    </a>
                    <ul class="treeview-menu">
                        <?php
                        if ($this->rbac->hasPrivilege('general_setting', 'can_view')) {
                        ?>
                            <li class="<?php echo set_Submenu('schsettings/index'); ?>"><a href="<?php echo base_url(); ?>schsettings"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('settings'); ?></a></li>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('patient', 'can_view')) {
                        ?>

                            <!-- <li class="<?php echo set_Submenu('setup/patient'); ?>"> <a href="<?php echo base_url(); ?>admin/admin/search"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('patient'); ?></a></li> -->
                        <?php
                        }


                        if ($this->rbac->hasPrivilege('hospital_charges', 'can_view')) {
                        ?>
                            <!-- <li class="<?php echo set_Submenu('charges/index'); ?>"><a href="<?php echo base_url(); ?>admin/charges"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('hospital') . " " . $this->lang->line('charges'); ?></a></li> -->
                            <?php
                        }
                        if ($this->module_lib->hasActive('IPD')) {
                            if ($this->rbac->hasPrivilege('bed', 'can_view')) {
                            ?>
                                <li class="<?php echo set_Submenu('bed'); ?>"><a href="<?php echo base_url(); ?>admin/setup/bed/status"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('bed'); ?></a></li>
                            <?php
                            }
                        }

                        if (($this->rbac->hasPrivilege('opd_prescription_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ipd_bill_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('ipd_prescription_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('pharmacy_bill_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('print_payslip_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('birth_print_header_footer', 'can_view')) || ($this->rbac->hasPrivilege('death_print_header_footer', 'can_view'))) {
                            ?>
                            <!-- <li class="<?php echo set_Submenu('admin/printing'); ?>"><a href="<?php echo base_url(); ?>admin/printing"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('print') . " " . $this->lang->line('header') . " " . $this->lang->line('footer'); ?></a></li> -->
                            <?php
                        }
                        if ($this->module_lib->hasActive('front_office')) {
                            if ($this->rbac->hasPrivilege('setup_front_office', 'can_view')) {
                            ?>
                                <!-- <li class="<?php echo set_Submenu('admin/visitorspurpose'); ?>"><a href="<?php echo base_url(); ?>admin/visitorspurpose"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('front_office'); ?></a></li> -->
                            <?php
                            }
                        }
                        if ($this->module_lib->hasActive('pharmacy')) {
                            if (($this->rbac->hasPrivilege('medicine_category', 'can_view') || ($this->rbac->hasPrivilege('medicine_supplier', 'can_view')) || ($this->rbac->hasPrivilege('medicine_dosage', 'can_view')))) {
                            ?>
                                <!-- <li class="<?php echo set_Submenu('medicine/index'); ?>"><a href="<?php echo base_url(); ?>admin/medicinecategory/index"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('pharmacy'); ?></a></li> -->
                            <?php
                            }
                        }
                        if ($this->module_lib->hasActive('pathology')) {
                            if ($this->rbac->hasPrivilege('pathology_category', 'can_view') || $this->rbac->hasPrivilege('pathology_unit', 'can_view') || $this->rbac->hasPrivilege('pathology_parameter', 'can_view')) {
                            ?>
                                <!-- <li class="<?php echo set_Submenu('addCategory/index'); ?>"><a href="<?php echo base_url(); ?>admin/pathologycategory/addcategory"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('pathology'); ?></a></li> -->
                            <?php
                            }
                        }
                        if ($this->module_lib->hasActive('radiology')) {
                            if ($this->rbac->hasPrivilege('radiology category', 'can_view') || $this->rbac->hasPrivilege('radiology_unit', 'can_view') || $this->rbac->hasPrivilege('radiology_parameter', 'can_view')) {
                            ?>
                                <!-- <li class="<?php echo set_Submenu('addlab/index'); ?>"><a href="<?php echo base_url(); ?>admin/lab/addLab"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('radiology'); ?></a></li> -->
                            <?php
                            }
                        }

                        if (($this->rbac->hasPrivilege('symptoms_type', 'can_view')) || ($this->rbac->hasPrivilege('symptoms_head', 'can_view'))) {
                            ?>
                            <!-- <li class="<?php echo set_Submenu('symptoms/index'); ?>"><a href="<?php echo base_url(); ?>admin/symptoms"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('symptoms'); ?></a></li> -->
                        <?php
                        }

                        if ($this->rbac->hasPrivilege('setting', 'can_view')) { ?>
                            <!-- <li class="<?php echo set_Submenu('conference/zoom_api_setting'); ?>"><a href="<?php echo base_url('admin/conference'); ?>"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('zoom_setting') ?></a></li> -->
                            <?php }

                        if (($this->module_lib->hasActive('income')) || ($this->module_lib->hasActive('expense'))) {

                            if (($this->rbac->hasPrivilege('income_head', 'can_view')) || ($this->rbac->hasPrivilege('income_head', 'can_view'))) {
                            ?>
                                <li class="<?php echo set_Submenu('finance/index'); ?>"><a href="<?php echo base_url(); ?>admin/incomehead"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('finance'); ?></a></li>
                            <?php
                            }
                        }
                        if ($this->rbac->hasPrivilege('birth_death_customfields', 'can_view')) {
                            ?>
                            <!-- <li class="<?php echo set_Submenu('birthordeathcustom/index'); ?>"> <a href="<?php echo base_url(); ?>admin/birthordeathcustom"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('birth_death_record'); ?></a></li> -->
                        <?php
                        }
                        if (($this->rbac->hasPrivilege('leave_types', 'can_view')) || ($this->rbac->hasPrivilege('leave_types', 'can_view'))) {
                        ?>
                            <!-- <li class="<?php echo set_Submenu('hr/index'); ?>"><a href="<?php echo base_url(); ?>admin/leavetypes"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('human_resource'); ?></a></li> -->
                            <?php
                        }
                        if ($this->module_lib->hasActive('inventory')) {
                            if ($this->rbac->hasPrivilege('item_category', 'can_view')) {
                            ?>
                                <!-- <li class="<?php echo set_Submenu('inventory/index'); ?>"><a href="<?php echo base_url(); ?>admin/itemcategory"><i class="fas fa-angle-right"></i> <?php echo $this->lang->line('inventory'); ?></a></li> -->
                        <?php }
                        }
                        ?>

                    </ul>

                <!-- </li> -->
                <?php
            }

            if ($this->module_lib->hasActive('system_settings')) {
                if (($this->rbac->hasPrivilege('general_setting', 'can_view') ||
                    $this->rbac->hasPrivilege('session_setting', 'can_view') ||
                    $this->rbac->hasPrivilege('notification_setting', 'can_view') ||
                    $this->rbac->hasPrivilege('sms_setting', 'can_view') ||
                    $this->rbac->hasPrivilege('email_setting', 'can_view') ||
                    $this->rbac->hasPrivilege('payment_methods', 'can_view') ||
                    $this->rbac->hasPrivilege('languages', 'can_view') ||
                    $this->rbac->hasPrivilege('languages', 'can_add') ||
                    $this->rbac->hasPrivilege('backup_restore', 'can_view') ||
                    $this->rbac->hasPrivilege('front_cms_setting', 'can_view'))) {
                ?>

            <?php
                }
            }
            ?>

        </ul>
    </section>
</aside>