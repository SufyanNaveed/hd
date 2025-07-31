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
                        <h3 class="box-title">Deduction Reports</h3>
                    </div>
                    <form role="form" action="<?php echo site_url('admin/tpamanagement/deductionreport') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-2" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                        <option value=""><?php echo $this->lang->line('all') ?></option>
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
                                    <label>Cheque No.</label>
                                    <select name="cheque" class="form-control select2"  style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($chequelist as $dkey => $value) {
                                            ?>
                                            <option value="<?php echo $value["id"] ?>" <?php
                                            if ((isset($cheque_select)) && ($cheque_select == $value["id"])) {
                                                echo "selected";
                                            }
                                            ?> ><?php echo $value["cheque_no"]; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('cheque'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2"  >
                                <div class="form-group">
                                    <label>Patient</label>
                                    <select name="patient" class="form-control select2"  style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($discharged_patients as $dkey => $value) {
                                            ?>
                                            <option value="<?php echo $value["p_id"] ?>" <?php
                                            if ((isset($patient_select)) && ($patient_select == $value["p_id"])) {
                                                echo "selected";
                                            }
                                            ?> ><?php echo $value['patient_name'] . ' | ' . $value['patient_unique_id'] . ' | ' . $value['mobileno'] . ' | ' . $value['patient_cnic']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('cheque'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('organisation'); ?></label>
                                    <select class="form-control select2"  name="organisation" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($organisation as $orgkey => $orgvalue) {
                                            ?>
                                            <option value="<?php echo $orgvalue["id"] ?>" <?php
                                            if ((isset($tpa_select)) && ($tpa_select == $orgvalue["id"])) {
                                                echo "selected";
                                            }
                                            ?> ><?php echo $orgvalue["organisation_name"] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2" id="fromdate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getSchoolDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-2" id="todate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getSchoolDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                    </form>
                    <div class="box border0 clear">
                        <div class="box-header ptbnull"></div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('organisation') . " " . $this->lang->line('report'); ?></div>
                            <table class="custom-table table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>Sr#</th>
                                        <th>Date</th>
                                        <th>Cheque No | Amount | Bank</th>
                                        <th>Patient Name</th>
                                        <th>Procedure</th>
                                        <th>Lodged Amount PCR</th>
                                        <th>Received Approved Amount PCR</th>
                                        <th>Deduction PCR Vise</th>
                                        <th>TPA</th>
                                        <th>User Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($resultlist)) {
                                    ?>
                                        <!-- Add empty row or message here if resultlist is empty -->
                                    <?php
                                    } else {
                                        $count = 1;
                                        $total_lodged_amount = 0;
                                        $total_received_amount = 0;
                                        $total_deduction_amount = 0;

                                        foreach ($resultlist as $result) {
                                            // Calculate totals
                                            $total_lodged_amount += $result["total_amount"];
                                            $total_received_amount += $result["gross_amount"];
                                            $total_deduction_amount += $result["deduction_amount"];
                                    ?>
                                            <tr>
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($result['adjusted_date'])) ?></td>
                                                <td><?php echo $result["cheque_no"] . ' : ' . $result["amount"] . ' : ' . $result["bank"]; ?></td>

                                                <td><?php echo $result["patient_name"] . ' | ' . $result['patient_unique_id']; ?></td>
                                                <td><?php echo $result["code"]; ?></td>
                                                <td><?php echo number_format($result["total_amount"], 2); ?></td>
                                                <td><?php echo number_format($result["gross_amount"], 2); ?></td>
                                                <td><?php echo number_format($result["deduction_amount"], 2) . ' | ' . $result["deduction_from"]; ?></td>
                                                <td><?php echo $result["organisation_name"]; ?></td>
                                                <td><?php echo $result["set_by"]; ?></td>
                                            </tr>
                                    <?php
                                            $count++;
                                        }
                                    ?>
                                </tbody>
                                <tr class="box box-solid total-bg">
                                    <td colspan="4"></td>
                                    <td><?php echo $count-1; ?></td>
                                    <td><?php echo number_format($total_lodged_amount, 2); ?></td>
                                    <td><?php echo number_format($total_received_amount, 2); ?></td>
                                    <td><?php echo number_format($total_deduction_amount, 2); ?></td>
                                    <td colspan="2"></td>
                                </tr>
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
        $(".select2").select2();
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