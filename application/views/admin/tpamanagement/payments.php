<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"> TPA Payments Management (<?php echo $resultlist[0]['organisation_name'] . ' - ' . $resultlist[0]['code']; ?>)</h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('organisation', 'can_add')) { ?>
                                <!-- <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm organisation"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('organisation'); ?></a>  -->
                            <?php } ?>
                        </div>    
                    </div><!-- /.box-header -->
                    <?php
                    if (isset($resultlist)) {
                        ?>
                        <div class="box-body">
                            <div class="download_label"><?php echo $title; ?></div>
                            <table class="custom-table table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr#</th>
                                        <th>Cheque No.</th>
                                        <th>Date</th>
                                        <th>Cheque Amount</th>
                                        <th>Remainig Amount</th>
                                        <th>Bank</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($resultlist)) {
                                        ?>
                                                                        
                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($resultlist as $payment) {
                                            ?>
                                            <tr class="">
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo $payment['cheque_no']; ?></td>
                                                <td><?php echo $payment['date']; ?></td>
                                                <td>
                                                    <?php
                                                        echo $payment['amount'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        if (!is_null($payment['balance_amount']) &&  $payment['balance_amount'] < $payment['amount']) {
                                                            echo $payment['balance_amount'];
                                                        } else {
                                                            echo $payment['amount'];
                                                        }
                                                    ?>
                                                </td>
                                                <td><?php echo $payment['bank']; ?></td>
                                                <td>
                                                    <?php
                                                        if ($payment['amount'] > 0) {
                                                            echo '<a href="#" class="btn btn-default btn-xs adjust-bill-btn" onclick="get_orgdata(\'' . $payment['id'] . '\')" data-toggle="tooltip" title="Adjust Bill"><i class="fa fa-plus"></i></a>';
                                                        }
                                                    ?>
                                                    <a href="<?php echo base_url(); ?>admin/tpamanagement/deductions/<?php echo $payment['id']; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="Discharge Bill Deductions">
                                                        <i class="fa fa-reorder"></i>
                                                    </a>
                                                    <?php if ($this->rbac->hasPrivilege('organisation', 'can_delete')) { ?>
                                                        <a  onclick="delete_recordById('<?php echo base_url(); ?>admin/tpamanagement/deletePayment/<?php echo $payment['id']; ?>', '<?php echo $this->lang->line('delete_message') ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip"  title="<?php echo $this->lang->line('delete'); ?>" >
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                                <input type="hidden" id="org_pay_id" name="org_pay_id" value="<?php echo $payment['id']; ?>">
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div><?php } ?>
                </div>  
            </div>
        </div> 
    </section>
</div>

<div class="modal fade" id="billModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modalfullmobile" role="document" style="width: 80%;">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Adjust TPA Bill</h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="adjustTPA" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>TPA</label><small class="req"> *</small> 
                                        <input id="tpa_name" name="tpa_name" placeholder="" type="text" value="<?php echo $resultlist[0]['organisation_name']?>" class="form-control" autocomplete="off" readonly />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cheque No.</label><small class="req"> *</small> 
                                        <input id="cheque_no" name="cheque_no" placeholder="" type="text" value="" class="form-control" readonly autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total Cheque Amount</label><small class="req"> *</small> 
                                        <input id="cheque_amount" name="cheque_amount" placeholder="" type="text" value="" class="form-control" readonly autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Remaining Adjustable Amount</label><small class="req"> *</small> 
                                        <input id="rem_amount" name="rem_amount" placeholder="" type="text" value="" class="form-control" readonly autocomplete="off" />
                                    </div>
                                </div>
                                <input type="hidden" id="modal_org_pay_id" style="height:28px" name="org_pay_id" value="" class="form-control" readonly />
                            </div><!--./row--> 
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="custom-table table tableover table-striped table-bordered table-hover"
                                            id="adjustTBL">
                                            <tr>
                                                <th>Visit No.<small style="color:red;"> *</small></th>
                                                <th>Prodcedure<small style="color:red;">*</small></th>
                                                <th>Consultant<small style="color:red;">*</small></th>
                                                <th>Lodged Amount<small style="color:red;"> *</small></th>
                                                <th>Approved Amount<small style="color:red;"> *</small></th>
                                                <th>Deduction<small style="color:red;"> *</small></th>
                                                <th>Share<small style="color:red;"> *</small></th>

                                                <!-- <th>Instruction Time</th> -->
                                            </tr>
                                            <tr id="row0">
                                                <td>
                                                    <select name="discharge_id[]" class="form-control select2" style="width:150px;" onchange="populateFields(this)">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php foreach ($discharged_patients as $patient) {
                                                            ?>
                                                            <option value="<?php echo $patient['id']; ?>">
                                                                <?php echo $patient['patient_name'] . ' | ' . $patient['patient_unique_id'] . ' | ' . $patient['mobileno'] . ' | ' . $patient['patient_cnic']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" style="height:28px" name="procedure[]" class="form-control" readonly />

                                                    <!-- <input
                                                        value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>"
                                                        type="text" name="date[]" class="form-control date"> -->
                                                </td>
                                                <td>
                                                    <input type="text" style="height:28px" name="consultant[]" class="form-control" readonly />
                                                </td>
                                                <td>
                                                    <input type="text" style="height:28px" name="lodged_amount[]" class="form-control" readonly />
                                                </td>
                                                <td><input type="text" style="height:28px" name="approved_amount[]" class="form-control approved-amount" pattern="[0-9]*" inputmode="numeric" readonly /></td>
                                                <td><input type="text" style="height:28px" name="deduction_amount[]" id="firstRowDeduction" class="form-control deduction-amount" pattern="[0-9]*" inputmode="numeric" /></td>
                                                <td>
                                                    <select name="deduction_from[]" class="form-control">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <option value="doctor">Doctor</option>
                                                        <option value="hospital">Hospital</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" onclick="add_more()" style="color:#2196f3"
                                                        class="closebtn"><i class="fa fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
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
    $(document).ready(function (e) {
        $(".select2").select2();
    });
    function get_orgdata(id) {
        $('#billModal').modal('show')
    }

    $(document).ready(function() {
        $(document).on('click', '.adjust-bill-btn', function() {
            var row = $(this).closest('tr');
            var remAmount = 0;
            var chequeNo = row.find('td:eq(1)').text().trim();
            var chequeAmount = parseFloat(row.find('td:eq(4)').text().trim()).toFixed(2);
            var paymentId = row.find('[name="org_pay_id"]').val();
            $('#modal_org_pay_id').val(paymentId);
            $('#cheque_no').val(chequeNo);
            $('#cheque_amount').val(chequeAmount);
            $('#rem_amount').val(chequeAmount);
            $('#billModal').modal('show');
        });
        $('.select2').select2();
    });
</script>

<script type="text/javascript">
    
    $(document).ready(function () {
    $("#adjustTPA").on('submit', function (e) {
        $("#formaddbtn").button('loading');
        e.preventDefault();

        var remainingAmount = parseFloat($('#rem_amount').val());
        // if (remainingAmount < 0) {
        //     errorMsg('Adjusted amount exceeds the cheque amount');
        //     $("#formaddbtn").button('reset');
        //     return; // Stop form submission
        // }
        if (remainingAmount > 0) {
            errorMsg('Check amount is remaining');
            $("#formaddbtn").button('reset');
            return; // Stop form submission
        } 
        

        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpamanagement/adjust_bill',
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



			
$(".organisation").click(function(){
    $('#formadd').trigger("reset");
});

function refreshmodal() {
    $('#formadd').trigger("reset");
    var table = document.getElementById("tableID");
    var table_len = (table.rows.length);
    for (i = 1; i < table_len; i++) {
        remove_row(i);
    }
}

var actualAmount; // Set the actual amount here

$(document).ready(function() {
    // Store actual amount on modal load
    $('#billModal').on('show.bs.modal', function() {
        actualAmount = parseFloat($('#cheque_amount').val()) || 0; // Get the amount from the input field
    });

    // Other code for calculations and functions
});

$('#billModal').on('hidden.bs.modal', function() {
    $('#adjustTBL tbody tr:not(:first-child)').each(function() {
        $(this).remove(); // Remove rows except the first one
    });
    // Refresh the first row's data
    $('#adjustTBL tbody tr:first-child').find('select, input').val('');
    location.reload();
});

$(document).ready(function() {
    // Manually trigger the input event for the first row's deduction amount field
    $('#firstRowDeduction').trigger('input');
});

$(document).ready(function() {
    $(document).on('input', '.deduction-amount', function() {
        var row = $(this).closest('tr');
        var originalApprovedAmount = parseInt(row.find('.approved-amount').attr('data-original')) || 0;
        var deduction = parseInt($(this).val()) || 0;
        var remainingApprovedAmount = originalApprovedAmount - deduction;

        // Update approved amount field
        row.find('.approved-amount').val(remainingApprovedAmount);

        // If deduction amount is zero, assign stored original approved amount back
        if (deduction === 0) {
            row.find('.approved-amount').val(originalApprovedAmount);
        }
    });
});

// Add event listener for input validation
// $(document).on('input', '.deduction-amount', function() {
//     var totalDeduction = 0;
//     $('.deduction-amount').each(function() {
//         var deduction = parseInt($(this).val()) || 0;
//         totalDeduction += deduction;
//     });
//     var remainingAmount = actualAmount - totalDeduction;
//     $('#rem_amount').val(remainingAmount);
// });


// Add more rows function
function add_more() {
    var table = $('#adjustTBL tbody');
    var selectedPatients = []; // Array to store selected patient IDs

    // Collect IDs of already selected patients
    table.find('select[name="discharge_id[]"]').each(function() {
        var selectedId = $(this).val();
        if (selectedId !== '') {
            selectedPatients.push(selectedId);
        }
    });

    var row = '<tr><td><select name="discharge_id[]" class="form-control select2" style="width: 150px;" onchange="populateFields(this)"><option value="">Select</option>';

    // Add options dynamically from PHP variable $discharged_patients
    <?php foreach ($discharged_patients as $patient) : ?>
        // Check if patient ID is not already selected
        if ($.inArray('<?php echo $patient['id']; ?>', selectedPatients) === -1) {
            row += '<option value="<?php echo $patient['id']; ?>"><?php echo $patient['patient_name'] . ' | ' . $patient['patient_unique_id'] . ' | ' . $patient['mobileno'] . ' | ' . $patient['patient_cnic']; ?></option>';
        }
    <?php endforeach; ?>

    row += '</select></td><td><input type="text" style="height:28px" name="procedure[]" class="form-control" readonly /></td><td><input type="text" style="height:28px" name="consultant[]" class="form-control" readonly /></td><td><input type="text" style="height:28px" name="lodged_amount[]" class="form-control" readonly /></td><td><input type="text" style="height:28px" name="approved_amount[]" class="form-control approved-amount" pattern="[0-9]*" inputmode="numeric" data-original="0" readonly /></td><td><input type="text" style="height:28px" name="deduction_amount[]" class="form-control deduction-amount" pattern="[0-9]*" inputmode="numeric" /></td><td><select name="deduction_from[]" class="form-control"><option value="">Select</option><option value="doctor">Doctor</option><option value="hospital">Hospital</option></select></td><td><button type="button" onclick="remove_row(this)" style="color:#f00" class="closebtn"><i class="fa fa-remove"></i></button></td></tr>';

    table.append(row);
    $('.select2').select2();
    // Update remaining amount on adding rows
    // updateRemainingAmount();
    // Update approved amount on adding rows
    updateApprovedAmount();
}

// Remove row function
function remove_row(element) {
    $(element).closest('tr').remove();

    // Update remaining amount on removing rows
    // updateRemainingAmount();
    updateApprovedAmount();
    var row = $(element).closest('tr');
    var approvedAmount = parseFloat(row.find('input[name="approved_amount[]"]').val());
    var currentRemAmount = parseFloat($('#rem_amount').val());
    var deductionAmount = parseFloat(row.find('input[name="deduction_amount[]"]').val());

    // Update remaining amount by adding back the approved amount of the removed row
    var updatedRemAmount = currentRemAmount + approvedAmount + deductionAmount;
    $('#rem_amount').val(updatedRemAmount.toFixed(2));

    // Remove the row from the table
    row.remove();
}

// Update remaining amount function
function updateRemainingAmount() {
    var totalDeduction = 0;
    $('.deduction-amount').each(function() {
        var deduction = parseInt($(this).val()) || 0;
        totalDeduction += deduction;
    });
    var remainingAmount = actualAmount - totalDeduction;
    $('#rem_amount').val(remainingAmount);
}

// Update Approved Amount in Row
function updateApprovedAmount() {
    $('.deduction-amount').on('input', function() {
        var row = $(this).closest('tr');
        var originalApprovedAmount = parseInt(row.find('.approved-amount').attr('data-original')) || 0;
        var deduction = parseInt($(this).val()) || 0;
        var remainingApprovedAmount = originalApprovedAmount - deduction;

        // Update approved amount field
        row.find('.approved-amount').val(remainingApprovedAmount);

        // If deduction amount is zero, assign stored original approved amount back
        if (deduction === 0) {
            row.find('.approved-amount').val(originalApprovedAmount);
        }
    });
}

function populateFields(select) {
    var selectedPatientId = select.value;
    var patientData = <?php echo json_encode($discharged_patients); ?>;
    var selectedPatient = patientData.find(patient => patient.id === selectedPatientId);

    if (selectedPatient) {
        var row = $(select).closest('tr');
        row.find('[name="discharge_id[]"]').val(selectedPatient.id);
        row.find('[name="procedure[]"]').val(selectedPatient.code);
        row.find('[name="consultant[]"]').val(selectedPatient.consultant);
        row.find('[name="lodged_amount[]"]').val(selectedPatient.total_amount);
        row.find('[name="approved_amount[]"]').val(selectedPatient.gross_amount);

        // Set the original approved amount as a data attribute
        row.find('.approved-amount').attr('data-original', selectedPatient.gross_amount);

        // Update the approved amount field with the original approved amount
        row.find('.approved-amount').val(selectedPatient.gross_amount);
    }

    // Get the selected patient's ID and approved amount from the selected row
    var selectedId = $(select).val();
    var approvedAmount = $(select).closest('tr').find('input[name="approved_amount[]"]').val();
    $(select).closest('tr').find('[name="deduction_amount[]"]').val(0);

    // Calculate the remaining amount by subtracting the approved amount from $rem_amount
    var remAmount = parseFloat($('#rem_amount').val());
    var remainingAmount = remAmount - parseFloat(approvedAmount);

    // Update the #rem_amount input with the calculated remaining amount
    $('#rem_amount').val(remainingAmount.toFixed(2));
}


// Update remaining approved amount function



function delete_row(id) {
    var table = document.getElementById("tableID");
    var rowCount = table.rows.length;
    $("#row" + id).html("");
    //table.deleteRow(id);
}

$(document).ready(function() {
    // Add event listener for input validation
    $('input[name="amount[]"]').on('input', function() {
        // Remove any non-numeric characters using a regular expression
        var sanitizedValue = $(this).val().replace(/\D/g, '');

        // Update the input field with the sanitized value
        $(this).val(sanitizedValue);
    });
});

</script>