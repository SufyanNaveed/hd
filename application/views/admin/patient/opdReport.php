<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('opd') . " " . $this->lang->line('report'); ?></h3>
                    </div>
                    <form role="form" action="<?php echo site_url('admin/patient/opd_report') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-2" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                        <option value="all_time"><?php echo $this->lang->line('all') ?></option>
                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                                <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2"  >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('department'); ?></label>
                                    <select class="form-control select2 department" <?php
                                    if ($disable_option == true) {
                                        echo "disabled";
                                    }
                                    ?> name="department" style="width: 100%" >
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <option value="1" <?php if ((isset($dept_select)) && $dept_select=="1") {
                                                echo "selected";}?>><?php echo "OPD" ?></option>
                                        <option value="private_patient" <?php if ((isset($dept_select)) && $dept_select=="private_patient") {
                                                echo "selected";}?>><?php echo "Private Patient" ?></option>

                                    </select>
                                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2"  >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('doctor'); ?></label>
                                    <select class="form-control select2 doctor" <?php
                                    if ($disable_option == true) {
                                        echo "disabled";
                                    }
                                    ?> name="doctor" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($doctorlist as $dkey => $value) {
                                            ?>
                                            <option value="<?php echo $value["id"] ?>" <?php
                                            if ((isset($doctor_select)) && ($doctor_select == $value["id"])) {
                                                echo "selected";
                                            }
                                            ?> ><?php echo $value["name"] . " " . $value["surname"] ?></option>
<?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2"  >
                                <div class="form-group">
                                    <label><?php echo "KPO"; ?></label>
                                    <select class="form-control select2" <?php
                                    if ($disable_option == true) {
                                        echo "disabled";
                                    }
                                    ?> name="kpo" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($all_kops as $dkey => $kops) {
                                            ?>
                                            <option value="<?php echo $kops["id"] ?>"<?php
                                            if ((isset($kpo)) && ($kpo == $kops["id"])) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $kops["kop_name"]  ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('kop'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('payment')." ".$this->lang->line('type'); ?></label>
                                    <select class="form-control "  name="patient_status" style="width: 100%">
                                        <option value="" <?php  if ((isset($patient_status)) && ($patient_status == 'all')) {
                                                        echo "selected";
                                                    }
                                                    ?> ><?php echo $this->lang->line('all') ?></option>
                                        <option value="visit" <?php  if ((isset($patient_status)) && ($patient_status == 'visit')) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $this->lang->line('visit')?></option>
                                       <option value="rechekup" <?php  if ((isset($patient_status)) && ($patient_status == 'rechekup')) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $this->lang->line('re_checkup')?></option>
                                        <option value="payment" <?php  if ((isset($patient_status)) && ($patient_status == 'payment')) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $this->lang->line('payment') ?></option>
                                        <option value="bill" <?php  if ((isset($patient_status)) && ($patient_status == 'bill')) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $this->lang->line('bill')." ".$this->lang->line('paid') ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('patient_status'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('gender'); ?></label>
                                    <select class="form-control" name="gender" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <option value="Male" <?php  if ((isset($gender)) && ($gender == 'Male')) {
                                                        echo "selected";
                                                    }
                                                    ?>>Male</option>

                                       <option value="Female" <?php  if ((isset($gender)) && ($gender == 'Female')) {
                                                        echo "selected";
                                                    }
                                                    ?>>Female</option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2" >
                                <div class="form-group">
                                    <label><?php echo 'Paeds'; ?></label>
                                    <select class="form-control" name="paed" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <option value="1" <?php  if ((isset($paed)) && ($paed == '1')) {
                                                        echo "selected";
                                                    }
                                                    ?>>Paeds</option>


                                    </select>
                                    <span class="text-danger"><?php echo form_error('paed'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2" >
                                <div class="form-group">
                                    <label><?php echo 'Counter'; ?></label>
                                    <select class="form-control" name="counter" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <option value="yes" <?php  if ((isset($selected_counter)) && ($selected_counter == 'yes')) {
                                                        echo "selected";
                                                    }
                                                    ?>>Counter 1</option>
                                        <option value="no" <?php  if ((isset($selected_counter)) && ($selected_counter == 'no')) {
                                                        echo "selected";
                                                    }
                                                    ?>>Counter 2</option>


                                    </select>
                                    <span class="text-danger"><?php echo form_error('counter'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3" id="fromdate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getSchoolDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3" id="todate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getSchoolDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right " style="
    margin-left: 15px;"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    <button type="submit" name="search" value="export_filter" class="btn btn-warning btn-sm checkbox-toggle pull-right" style=""><i class="fa fa-search"></i> <?php echo "Export" ?></button>
                                </div>

                            </div>
                    </form>
                    <div class="box border0 clear">
                        <div class="box-header ptbnull">
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('opd') . " " . $this->lang->line('report'); ?></div>
                            <table class="custom-table table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('appointment') . " " . $this->lang->line('date'); ?></th>
                                        <!-- <th><?php echo $this->lang->line('opd') . " " . $this->lang->line('id'); ?></th>  -->
                                        <th><?php echo "Invoice No "; ?></th>
                                        <th><?php echo "Counter "; ?></th>
                                        <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('id'); ?></th>
                                        <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('age'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo 'Paed'; ?></th>
                                        <th><?php echo $this->lang->line('mobile_no'); ?></th>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <th><?php echo $this->lang->line('casualty'); ?></th>
                                        <th><?php echo $this->lang->line('refference'); ?></th>
                                        <th><?php echo $this->lang->line('consultant') . " " . $this->lang->line('doctor'); ?></th>
                                        <th>KPO</th>
                                       <!--  <th><?php echo $this->lang->line('charges') ; ?></th> -->
                                       <th><?php echo $this->lang->line('payment') . " " . $this->lang->line('mode'); ?></th>
                                       <th><?php echo $this->lang->line('payment')." ".$this->lang->line('type'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('paid')." ".$this->lang->line('amount') . '(' . $currency_symbol . ')'; ?></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($resultlist)) {
                                        ?>
                                                        <!-- <tr>
                                                            <td colspan="12" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?>
                                                            </td>
                                                        </tr>  -->
                                        <?php
                                    } else {
                                        $count = 1;
                                        $total = 0;
                                        foreach ($resultlist as $report) {
                                            if (!empty($report['amount'])) {

                                                $amount = $report['amount'] ;
                                                $total += $amount ;
                                            }


                                             $paymentmode = $report['payment_mode'];
                                          /*  if($report['payment_mode'] == 'paid'){
                                                    $paymentmode =  $this->lang->line('paid');
                                                }else {
                                                    $paymentmode = $report['payment_mode'];
                                            }*/

                                            if($report['paytype'] == 'visit'){
                                                    $paymenttype =  $this->lang->line('visit');

                                            }elseif ($report['paytype'] == 'rechekup'){
                                                    $paymenttype =  $this->lang->line('re_checkup');

                                            }elseif ($report['paytype'] == 'payment'){
                                                    $paymenttype =  $this->lang->line('payment');

                                            }elseif ($report['paytype'] == 'bill'){
                                                    $paymenttype =  $this->lang->line('bill');
                                            }

                                            ?>
                                            <tr>
                                                <td><?php echo date($this->customlib->getSchoolDateFormat(true, true), strtotime($report['appointment_date'])) ?></td>
                                                <!--  <td><?php echo $report['id']; ?></td> -->
                                                <td><?php echo $report['opd_no']; ?></td>
                                                <td><?php echo isset($report['casualty']) && $report['casualty']=='Yes' ? 'Counter 1' : 'Counter 2'; ?></td>
                                                <td><?php echo $report['patient_unique_id']; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>admin/patient/profile/<?php echo $report['pid']; ?>"><?php echo $report['patient_name'] ?>
                                                    </a>
                                                </td>
                                                <td><?php if(!empty($report['age'])){ echo $report['age']." ".$this->lang->line("years")." "; } if(!empty($report['month'])){ echo $report['month']." ".$this->lang->line("month"); } ?></td>
                                                <td><?php echo $report['gender']; ?></td>
                                                <td><?php echo isset($report['paed']) && $report['paed']==1 ? 'Paed' : ''; ?></td>
                                                <td><?php echo $report['mobileno']; ?></td>
                                                <td><?php echo $report['guardian_name']; ?></td>
                                                <td><?php echo $report['address']; ?></td>
                                                <td><?php echo $report['set_casuality']; ?></td>
                                                <td><?php echo $report['refference']; ?></td>
                                                <td><?php echo $report['name']." ".$report['surname']; ?></td>
                                                <td><?php echo $report['kop_name']; ?></td>
                                               <!--  <td><?php if(isset($report["charges"])){ echo $report["charges"]; } ?></td> -->
                                                <td><?php  echo $paymentmode ; ?></td>
                                                 <td><?php echo $paymenttype; ?></td>
                                                <td class="text-right"><?php echo $amount ; ?></td>
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                        ?>

                                         <tr class="box box-solid total-bg">
                                            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                            <td class="text-right"><?php echo $this->lang->line('total') . " :" . $currency_symbol . $total; ?>
                                            </td>
                                        </tr>
                                    </tbody>
<?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>


<script type="text/javascript">
    $(document).ready(function (e) {
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
    $(function () {

     $('.select2').select2()

    })

    $(".department").change(function(){
        $.ajax({
             url: base_url + 'admin/patient/checkStaffDoctor',
            type: 'POST',
            data: {depid_multi: $('.department').select2("val")},
            success: function (response) {
                var result=JSON.parse(response);
                var toAppend=[];

                //$('.doctor').val(null).trigger('change'); // remove old options
                $('.doctor').empty();
                $('.doctor').append($("<option>Select Doc</option>")
                .attr("value", "")
                .text('Select'));
                $.each(result, function(i,o){
                    //console.log("ooo",o['id']);
                    $('.doctor').append('<option value="'+ o['id'] +'">'+o['name']+'</option>');

                });


            }
        });
});
</script>