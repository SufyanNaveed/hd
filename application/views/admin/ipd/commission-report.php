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
                        <h3 class="box-title">IPD Commission Reports</h3>
                    </div>
                    <form role="form" action="<?php echo site_url('admin/ipd/commissionreport') ?>" method="post" class="">
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
                                    <label><?php echo $this->lang->line('doctor'); ?></label>
                                    <select name="doctor" class="form-control select2"  style="width: 100%">
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
                                        <th>Doctor Name</th>
                                        <th>Commission Amount (Rs.)</th>
                                        <th>Total Paid</th>
                                        <th>Remaining Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($resultlist)) {
                                        ?>

                                        <?php
                                    } else {
                                        foreach ($resultlist as $result) {
                                            ?>
                                            <tr>
                                                <td><?php echo $result["doctor_name"]; ?></td>
                                                <td><?php echo number_format($result["total_doctor_share"] , 2); ?></td>
                                                <td><?php echo number_format($result["total_paid"] , 2); ?></td>
                                                <td><?php echo number_format($result["total_doctor_share"] - $result["total_paid"] , 2); ?></td>
                                                <td>
                                                    <?php
                                                        if ($result['total_doctor_share'] > 0) {
                                                            echo '<a href="#" class="btn btn-default btn-xs adjust-pay-btn" onclick="get_orgdata(\'' . $result['doctor_id'] . '\')" data-toggle="tooltip" title="Pay Commision"><i class="fa fa-money"></i></a>';
                                                        }
                                                    ?>
                                                    <a href="<?php echo base_url(); ?>admin/ipd/commissionpayments/<?php echo $result['doctor_id']; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="View Payments">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                                <input type="hidden" id="doctor_id" name="doctor_id" value="<?php echo $result['doctor_id']; ?>">
                                            </tr>
                                            <?php
                                        }
                                        ?>
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

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modalfullmobile modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Pay IPD Commission</h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="payCommission" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Doctor Name</label><small class="req"> *</small> 
                                        <input id="doctor_name" name="doctor_name" placeholder="" type="text" value="<?php echo $resultlist[0]['organisation_name']?>" class="form-control" autocomplete="off" readonly />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Commission Amount (Rs.)</label><small class="req"> *</small> 
                                        <input id="commission_amount" name="commission_amount" placeholder="" type="text" value="" class="form-control" readonly autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Arrears</label><small class="req"> *</small> 
                                        <input id="arrears" name="arrears" placeholder="" type="text" value="" class="form-control" pattern="[0-9]*" inputmode="numeric" readonly autocomplete="off" />
                                    </div>
                                </div>
                
                                <input type="hidden" id="pay_doctor_id" style="height:28px" name="pay_doctor_id" value="" class="form-control" readonly />
                            </div><!--./row--> 
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Payment Amount</label><small class="req"> *</small> 
                                        <input id="pay_amount" name="pay_amount" placeholder="" type="text" value="" class="form-control" pattern="[0-9]*" inputmode="numeric" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cheque No.</label><small class="req"> *</small> 
                                        <input id="cheque_no" name="cheque_no" placeholder="" type="text" value="" class="form-control" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Bank</label><small class="req"> *</small> 
                                        <input id="bank_name" name="bank_name" placeholder="" type="text" value="" class="form-control" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date</label><small class="req"> *</small> 
                                        <input id="pay_date" name="pay_date" placeholder="" type="date" value="" class="form-control" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                    </div><!--./col-md-12-->       
                </div>
            </div>
            <div class="box-footer clear">
                <div class="pull-right">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formaddbtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </div>
            </form>     
        </div>
    </div> 
</div> 

<script type="text/javascript">

    function get_orgdata(id) {
        $('#payModal').modal('show')
    }

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

    $(document).ready(function() {
        var commissionAmount = parseFloat($('#commission_amount').val());

        // Function to update arrears amount based on payment amount
        function updateArrears() {
            var paymentAmount = parseFloat($('#pay_amount').val());
            var commissionAmount = parseFloat($('#commission_amount').val());

            var arrears = (commissionAmount - paymentAmount).toFixed(2);
            $('#arrears').val(arrears);
        }

        // Event listener for typing in payment amount field
        $('#pay_amount').on('input', function() {
            // Allow only numbers and a dot for decimal input
            var inputValue = $(this).val().replace(/[^0-9.]/g, '');
            $(this).val(inputValue);

            // Update arrears amount
            updateArrears();
        });

        // Reset arrears to original amount if payment amount is empty or 0
        $('#pay_amount').on('change', function() {
            var paymentAmount = parseFloat($(this).val());
            if (isNaN(paymentAmount) || paymentAmount == 0) {
                $('#arrears').val(commissionAmount.toFixed(2));
            }
            
        });

        $(document).on('click', '.adjust-pay-btn', function() {
            var row = $(this).closest('tr');
            var doctorName = row.find('td:eq(0)').text().trim();
            var commissionAmount = parseFloat(row.find('td:eq(3)').text().trim().replace(/,/g, '')); // Remove commas and convert to float
            var doctorId = row.find('[name="doctor_id"]').val();
            $('#pay_doctor_id').val(doctorId);
            $('#doctor_name').val(doctorName);
            $('#commission_amount').val(commissionAmount.toFixed(2)); // Set commission amount with decimals
            $('#arrears').val(commissionAmount.toFixed(2)); // Set initial arrears amount
            $('#payModal').modal('show');
        });
    });


    $(document).ready(function () {
        $("#payCommission").on('submit', function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();

            var remainingAmount = parseFloat($('#arrears').val());
            if (remainingAmount < 0) {
                errorMsg('Added amount exceeds the commission amount');
                $("#formaddbtn").button('reset');
                return; // Stop form submission
            }
                       

            $.ajax({
                url: '<?php echo base_url(); ?>admin/ipd/pay_commission',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn").button('reset');
                },
                error: function () {
                    // Handle error here
                    $("#formaddbtn").button('reset');
                }
            });
        });
    });
</script>