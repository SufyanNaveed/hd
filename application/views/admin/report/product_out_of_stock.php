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
                        <h3 class="box-title">Product Expiry report</h3>
                    </div>
                    <form role="form" action="<?php echo site_url('admin/report/productExpiryReport') ?>" method="post" class="" onsubmit="return validateForm(event)">
                        <div class="box-body row">

                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                        <option value=""><?php echo $this->lang->line('all') ?></option>
                                        <option value="this_week" <?php if ($search_type == 'this_week') echo "selected"; ?>>This Week</option>
                                        <option value="last_month" <?php if ($search_type == 'this_month') echo "selected"; ?>>This Month</option>
                                        <option value="next_month" <?php if ($search_type == 'next_month') echo "selected"; ?>>Next Month</option>


                                    </select>
                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . " Hospital "; ?></label>
                                    <select class="form-control select2 " id="hospital_id" name="hospital_id">
                                        <option value=""><?php echo "Select Hospital" ?></option>
                                        <?php foreach ($hospitals as $key => $hospital) {
                                        ?>
                                            <option value="<?php echo $hospital['id'] ?>" <?php echo isset($selected_hospital_id) && $selected_hospital_id == $hospital['id'] ? 'selected' : '' ?>><?php echo $hospital['name'] ?></option>
                                        <?php } ?>
                                    </select>
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
                                    <!-- <button type="submit" name="search" value="export_pdf" class="btn btn-warning btn-sm checkbox-toggle pull-right " style="margin-right:6px;"><i class="fa fa-search"></i> <?php echo 'Export PDF'; ?></button> -->
                                </div>
                            </div>

                    </form>

                    <div class="box border0 clear">
                        <div class="box-header ptbnull"></div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('pharmacy') . " " . $this->lang->line('bill') . " " . $this->lang->line('report'); ?></div>
                            <table class="custom-table table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Hospital Name</th>
                                        <th>Item Name</th>
                                        <th>Batch No</th>
                                        <th>Latest Expiry Date</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($resultlist)) {
                                        echo '<tr><td colspan="7" class="text-center">No records found</td></tr>';
                                    } else {
                                        $count = 1;
                                        $total = 0;
                                        foreach ($resultlist as $bill) {
                                            $quantity = $bill['total_quantity'] ?? 0;
                                            $amount = is_numeric($bill['total_amount']) ? (float)$bill['total_amount'] : 0;
                                            $total += $amount;
                                    ?>
                                            <tr>
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo $bill['hospital_name']; ?></td>
                                                <td><?php echo $bill['medicine_name']; ?></td>
                                                <td><?php echo $bill['batch_no']; ?></td>
                                                <td><?php echo $bill['latest_expiry_date']; ?></td>
                                                <td><?php echo $quantity; ?></td>
                                            </tr>
                                    <?php
                                            $count++;
                                        }
                                    }
                                    ?>
                                </tbody>
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

    function validateForm(event) {
        //alert("fsfsfd");
        var hospital = document.getElementById("hospital_id").value;
        console.log(hospital, "hospital");
        var searchValue = event.submitter.value;
        // If Export PDF button is clicked and hospital is not selected
        if (searchValue == "export_pdf" && hospital == "") {
            alert("Please select a hospital before exporting the PDF.");
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
</script>