<style type="text/css">
    @media print {

        .no-print,
        .no-print * {
            display: none !important;
        }
    }
</style>
<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo "Doctor Income Report" ?></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    <div class="box-body pb0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">

                                    <form role="form" action="<?php echo site_url('admin/income/doctorIncomereport') ?>" method="post" class="">
                                        <div class="box-body row">
                                            <?php echo $this->customlib->getCSRF(); ?>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                                        <option value="all_time"><?php echo $this->lang->line('all') ?></option>
                                                        <?php foreach ($searchlist as $key => $search) { ?>
                                                            <option value="<?php echo $key ?>" <?php
                                                                                                if ((isset($search_type)) && ($search_type == $key)) {
                                                                                                    echo "selected";
                                                                                                }
                                                                                                ?>><?php echo $search == 'Today' ? $search.'('. date('Y-m-d').')' : $search ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('search') . " " . " Doctor "; ?></label>
                                                    <select  class="form-control"  name='doctorId' id="revisit_doctor" >
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                <?php foreach ($doctors as $dkey => $dvalue) {
                                                                    ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                        if ((isset($search_doctor_id)) && ($search_doctor_id == $dvalue["id"])) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
                                                    <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('kpoid'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('search') . " " . " Department "; ?></label>
                                                    <select class="form-control" name="department" id="revisit_doctor">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                            <option value="OPD" <?php echo (empty($search_department) || $search_department == 'OPD') ? 'selected' : ''; ?>>OPD</option>
                                                            <option value="Pathology" <?php echo isset($search_department) && $search_department == 'Pathology' ? 'selected' : ''; ?>>Pathology</option>
                                                            <option value="Radiology" <?php echo isset($search_department) && $search_department == 'Radiology' ? 'selected' : ''; ?>>Radiology</option>
                                                        </select>

                                                    <span class="text-danger"><?php echo form_error('kpoid'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4" id="fromdate" style="display: none">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getSchoolDateFormat())); ?>" />
                                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4" id="todate" style="display: none">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getSchoolDateFormat())); ?>" />
                                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                                    <button type="submit" name="search" value="export_pdf" class="btn btn-warning btn-sm checkbox-toggle pull-right " style="margin-right:6px;"><i class="fa fa-search"></i> <?php echo 'Export PDF'; ?></button>
                                                    <!-- <button type="submit" name="search" value="export_summary_pdf" class="btn btn-warning btn-sm checkbox-toggle pull-right " style="margin-right:6px;"><i class="fa fa-download"></i> <?php echo 'Export Summary PDF'; ?></button> -->
                                                    <button type="submit" name="search" value="export_summary_new_pdf" class="btn btn-warning btn-sm checkbox-toggle pull-right " style="margin-right:6px;"><i class="fa fa-download"></i> <?php echo 'Export Summary PDF'; ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>

                    <?php if (isset($parameter)) { ?>
                        <div class="tabsborderbg"></div>
                        <div class="nav-tabs-custom border0">
                            <ul class="nav nav-tabs">
                                <?php
                                $j = 0;
                                $language_module = array(
                                    'opd_patient'    => 'opd_patient',
                                    // 'ipd_patient'    => 'ipd_patient',
                                    // 'pharmacy bill'  => 'pharmacy_bill',
                                    'pathology test' => 'pathology_test',
                                    'radiology test' => 'radiology_test',
                                    // 'ot_patient'     => 'ot_patient',
                                    // 'ambulance_call' => 'blood_issue',
                                    // 'blood_issue'    => 'ambulance_call',
                                    // 'income'         => 'income',
                                    // 'expense'        => 'expense',
                                    // 'payroll_report' => 'payroll_report',
                                );
                                //$permission_array = array('opd_patient', 'ipd_patient', 'pharmacy bill', 'pathology test', 'radiology test', 'ot_patient', 'ambulance_call', 'blood_issue', 'income', 'expense', 'payroll_report');
                                $permission_array = array('opd_patient', 'pathology test', 'radiology test');
                                //$report_module    = array('opd_report', 'ipd_report', 'pharmacy_bill_report', 'pathology_patient_report', 'radiology_patient_report', 'ot_report', 'ambulance_call', 'blood_donor_report', 'income', 'expense', 'payroll_report');
                                $report_module    = array('opd_report', 'pathology_patient_report', 'radiology_patient_report');
                                foreach ($parameter as $ckey => $value) {
                                    if (($this->rbac->hasPrivilege($permission_array[$j], 'can_view')) || ($this->rbac->hasPrivilege($report_module[$j], 'can_view'))) { ?>
                                        <!-- <li class="<?php //if($j == 0){ echo "active" ;}
                                                    ?>"><a href="#<?php echo $ckey ?>" data-toggle="tab" aria-expanded="true">
                                                <font><?php echo $this->lang->line($language_module[$permission_array[$j]]); ?></font>
                                            </a></li> -->
                                <?php
                                    }
                                    $j++;
                                }
                                ?>
                            </ul>

                            <div class="tab-content">
                                
                                <?php
                                $i    = 0;
                                $reff = 0;
                                foreach ($parameter as $key => $value) {
                                    
                                ?>
                                    <div class="tab-pane active" id="<?php echo $key ?>">
                                        <div class="download_label"><?php echo $this->lang->line('transaction_report'); ?></div>
                                        <div class="box-body table-responsive">
                                            <table class="custom-table table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                                <thead>
                                                    <th><?php echo "S/N"; ?></th>
                                                    <th><?php echo 'DATE TIME'; ?></th>
                                                    <th><?php echo "MRN"; ?></th>
                                                    <th><?php echo "VISIT"; ?></th>
                                                    <th><?php echo "INVOICE NO"; ?></th>
                                                    <th><?php echo "DEPARTMENT"; ?></th>
                                                    <th><?php echo "PATIENT NAME"; ?></th>
                                                    <th><?php echo "REF.BY"; ?></th>
                                                    <th><?php echo "District"; ?></th>
                                                    <th><?php echo "Test Name"; ?></th>
                                                    <th><?php echo "TPA"; ?></th>
                                                    <th class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <th><?php echo "Hospital Share"; ?></th>
                                                    <th><?php echo "Doctor Share"; ?></th>
                                                    <th><?php echo "Staff Share"; ?></th>
                                                    <th><?php echo "Refund"; ?></th>
                                                    <th><?php echo "Discount"; ?></th>
                                                    <th class="text-right"><?php echo "KPO Name"; ?></th>
                                                </thead>
                                                <?php
                                                $tot[$value["label"]] = 0;
                                                $cd = 1;
                                                $ptotal      = 0;
                                                $ptotal_hshare      = 0;
                                                $ptotal_dshare      = 0;
                                                $ptotal_staffshare      = 0;
                                                $ptotal_refund      = 0;
                                                if (!empty($value["resultList"])) {
                                                    foreach ($value["resultList"] as $key2 => $transaction) {
                                                        $pt_amt = $transaction["amount"];
                                                        if ($transaction["status"] == 'refund') {
                                                            $ptotal_refund += $pt_amt;
                                                        } else {
                                                            $phospital_share = $pt_amt - ($transaction["doctor_share"] + $transaction["staff_share"]) - $transaction["discount"];
                                                            $ptotal_dshare += $transaction["doctor_share"];
                                                            $ptotal_staffshare += $transaction["staff_share"];
                                                            $ptotal_hshare += $phospital_share;
                                                            $ptotal += $pt_amt;
                                                        }
                                                ?>
                                                        <tr>
                                                            <td><?php echo $cd ?></td>
                                                            <td><?php echo date("d-m-Y H:i:s", strtotime($transaction['created_at'])); ?></td>
                                                            <td><?php echo $transaction["mrno"]  ?></td>
                                                            <td><?php echo "1"  ?></td>
                                                            <td><?php echo $transaction['reff'] ?></td>
                                                            <td style="text-transform:capitalize;"><?php echo $value["label"] ?></td>
                                                            <td><?php echo $transaction["patient_name"] . " " . $patient_id ?></td>
                                                            <td class="text-right <?php echo $class ?>"><?php echo $transaction["name"] . ' ' . $transaction["surname"] ?></td>
                                                            <td class="text-right <?php echo $class ?>"><?php echo $transaction['address'] ?></td>
                                                            <td class="text-right <?php echo $class ?>"><?php echo $transaction['department_name'] ?></td>
                                                            <td class="text-right <?php echo $class ?>"><?php echo $transaction['organisation_name'] ?></td>
                                                            <td class="text-right <?php echo $class ?>"><?php echo $prefix . number_format($pt_amt) ?></td>

                                                            <td class="text-right <?php echo $class ?>">
                                                                <?php
                                                                    if($transaction["status"] !== 'refund') {
                                                                        echo $phospital_share;
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-right <?php echo $class ?>">
                                                                <?php
                                                                    if($transaction["status"] !== 'refund') {
                                                                        echo $transaction["doctor_share"];
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-right <?php echo $class ?>">
                                                                <?php
                                                                    if($transaction["status"] !== 'refund') {
                                                                        echo $transaction["staff_share"];
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-right <?php echo $class ?>">
                                                                <?php
                                                                    if($transaction["status"] == 'refund') {
                                                                        echo $prefix . number_format($pt_amt);
                                                                    }
                                                                ?>
                                                            </td>

                                                            <td class="text-right <?php echo $class ?>"><?php echo number_format($transaction["discount"]) ?></td>
                                                            <td class="text-right <?php echo $class ?>"><?php echo $transaction["kpo_name"] ?></td>

                                                        </tr>
                                                <?php $cd++;
                                                    }
                                                } ?>
                                                <!-- <tr class="box box-solid total-bg">
															<td ></td>
															<td ></td>
															<td ></td>
															<td ></td>
															<td ></td>
															<td class="text-right" ><?php //echo $this->lang->line('total') . " : " . $currency_symbol . $tot[$value["label"]];
                                                                                    ?></td>
														</tr> -->
                                                <tr class="box box-solid total-bg">
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-right"><?php echo $currency_symbol . ($ptotal+$ptotal_refund); ?></td>
                                                    <td class="text-right"><?php echo $currency_symbol . $ptotal_hshare; ?></td>
                                                    <td class="text-right"><?php echo $currency_symbol . $ptotal_dshare; ?></td>
                                                    <td class="text-right"><?php echo $currency_symbol . $ptotal_staffshare; ?></td>
                                                    <td class="text-right"><?php echo $currency_symbol . $ptotal_refund; ?></td>
                                                    <td></td>
                                                    <td></td>

                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php $i++;
                                }
                                ?>
                            </div>
                        </div>

                </div>
            <?php
                    }
            ?>

            </div>
        </div> <!-- /.row -->
    </section><!-- /.content -->
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        // var capital_date_format=date_format.toUpperCase();
        //        $.fn.dataTable.moment(capital_date_format);
        $(".date").datepicker({
            // format: "dd-mm-yyyy",
            format: date_format,
            autoclose: true,
            todayHighlight: true

        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $.extend($.fn.dataTable.defaults, {
            ordering: true,
            paging: true,
            bSort: true,
            info: true,
        });
    });
</script>
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';

    function printDiv(elem) {
        Popup(jQuery(elem).html());
    }

    function Popup(data) {

        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({
            "position": "absolute",
            "top": "-1000000px"
        });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function() {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);


        return true;
    }
</script>
<script type="text/javascript">
    $(document).ready(function(e) {
        showdate('<?php echo $search_type; ?>');
    });

    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').show();
            $('#todate').show();
        } else {
            $('#fromdate').hide();
            $('#todate').hide();
        }
    }
</script>