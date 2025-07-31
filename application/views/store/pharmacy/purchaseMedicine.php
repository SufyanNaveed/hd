<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style type="text/css">
    #easySelectable {
        /*display: flex; flex-wrap: wrap;*/
    }

    #easySelectable li {}

    #easySelectable li.es-selected {
        background: #2196F3;
        color: #fff;
    }

    .easySelectable {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    /*.printablea4{width: 100%;}
    .printablea4 p{margin-bottom: 0;}
    .printablea4>tbody>tr>th,
    .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}*/
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">Purchase Medicine</h3>
                <div class=" " style="display: block;" id="myModal" role="dialog" aria-labelledby="myModalLabel">
                    <form id="bill" accept-charset="utf-8" method="post" class="ptt10">

                        <div class="modal-dialog pup100" role="document">
                            <div class="modal-content modal-media-content">
                                <div class="modal-header modal-media-header">
                                    <div class="row modalbillform">
                                    <div class="col-lg-3 col-sm-4">
                                            <!--  <label for="">
                        <?php echo $this->lang->line('supplier'); ?>
                                            </label>
                                            <small class="req" style="color:red;"> *</small> -->

                                            <select style="width:100%" name="supplier_type_id" onchange="get_SupplierList(this.value)" class="form-control select2" <?php
                                                                                                                                                                    if ($disable_option == true) {
                                                                                                                                                                        echo "disabled";
                                                                                                                                                                    }
                                                                                                                                                                    ?> id="" name=''>
                                                <option value="">Supplier Type</option>
                                                <?php foreach ($supplierTypes as $dkey => $dvalue) {
                                                ?>
                                                    <option value="<?php echo $dvalue->id; ?>" <?php
                                                                                                   
                                                                                                    ?>><?php echo $dvalue->name; ?></option>
                                                <?php } ?>
                                            </select>

                                            <span class="text-danger"><?php echo form_error('refference'); ?></span>

                                        </div><!--./col-sm-5-->
                                        <div class="col-lg-3 col-sm-4">
                                            <!--  <label for="">
                        <?php echo $this->lang->line('supplier'); ?>
                                            </label>
                                            <small class="req" style="color:red;"> *</small> -->

                                            <select style="width:100%" name="supplier_id" id="supplier_list" onchange="get_SupplierDetails(this.value)" class="form-control select2" <?php
                                                                                                                                                                    if ($disable_option == true) {
                                                                                                                                                                        echo "disabled";
                                                                                                                                                                    }
                                                                                                                                                                    ?> id="" name=''>
                                                <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('supplier') ?></option>
                                              
                                            </select>

                                            <span class="text-danger"><?php echo form_error('refference'); ?></span>

                                        </div><!--./col-sm-5-->
                                        <div class="col-lg-3 col-sm-3">
                                            <div class="row">
                                                <div class="col-lg-4 col-sm-4 col-xs-4">
                                                    <label><?php echo $this->lang->line('invoice_no'); ?></label>
                                                </div><!--./col-sm-6-->
                                                <div class="col-lg-4 col-sm-4 col-xs-4">
                                                    <input name="invoice_no" id="invoice_no" required type="text" value="" class="form-control" />
                                                    <span class="text-danger"><?php echo form_error('invoice_no'); ?></span>
                                                </div><!--./col-sm-6-->
                                            </div><!--./row-->
                                        </div><!--./col-sm-6-->
                                        <div class="col-lg-3 col-sm-3">
                                            <div class="row">
                                                <div class="col-lg-4 col-sm-4 col-xs-4">
                                                    <label><?php echo $this->lang->line('purchase') . " " . $this->lang->line('date'); ?></label>
                                                </div><!--./col-sm-6-->
                                                <div class="col-lg-6 col-sm-6 col-xs-6">
                                                    <input name="date" id="date_supplier" type="datetime-local"
                                                        value="<?php echo date('Y-m-d\TH:i'); ?>"
                                                        class="form-control" />

                                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                                </div><!--./col-sm-6-->
                                            </div><!--./row-->
                                        </div><!--./col-sm-6-->
                                        <div class="">
                                            <button type="button" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" class="close" data-dismiss="modal">&times;</button>
                                            <!-- <h4 class="box-title"><?php echo $this->lang->line('purchase') . " " . $this->lang->line('medicine'); ?></h4>  -->
                                        </div><!--./col-sm-6-->
                                    </div><!--./row-->
                                </div><!--./modal-header-->
                                <div class="modal-body pt0 pb0">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 paddlr">

                                            <div class="row">

                                                <input name="invoiceno" id="invoiceno" type="hidden" class="form-control" />
                                                <input name="date" id="date_result" type="hidden" class="form-control" />
                                                <div class="col-sm-2" hidden>
                                                    <div class="form-group">
                                                        <label>
                                                            <th><?php echo $this->lang->line('supplier') . " " . $this->lang->line('person'); ?></th>
                                                        </label>
                                                        <small class="req" style="color:red;"> *</small>
                                                        <input name="supplier_name" readonly hidden id="supplier_name" type="text" class="form-control" />

                                                        <span class="text-danger"><?php echo form_error('supplier_name'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-12" style="clear: both;">
                                                    <div class="table-responsive">
                                                        <table class="custom-table table table table tableover table-striped table-bordered table-hover mb10 tablefull12" id="tableID">
                                                            <tr>
                                                                <th width="13%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?><small class="req" style="color:red;"> *</small></th>
                                                                <th width="30%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
                                                                <th width="8%"><?php echo $this->lang->line('batch') . " " . $this->lang->line('no'); ?><small style="color:red;"> *</small></th>
                                                                <th width="9%"><?php echo $this->lang->line('expire') . " " . $this->lang->line('date'); ?><small class="req" style="color:red;"> *</small></th>

                                                                <th width="8%"><?php echo $this->lang->line('packing') . " " . $this->lang->line('qty'); ?></th>

                                                                <th width="7%" class="text-right;"><?php echo $this->lang->line('quantity'); ?><small class="req" style="color:red;"> *</small> </th>
                                                                <th width="12%" class="text-right"><?php echo $this->lang->line('purchase') . " " . $this->lang->line('price'); ?><small class="req" style="color:red;"> *</small></th>
                                                                <th class="text-right" width="9%"><?php echo $this->lang->line('amount'); ?><small class="req" style="color:red;"> *</small></th>
                                                            </tr>
                                                            <tr id="row0">
                                                                <td>
                                                                    <select class="form-control" name='medicine_category_id[]' onchange="getmedicine_name(this.value, '0')">
                                                                        <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                                        </option>
                                                                        <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                                        ?>
                                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                    <span class="text-danger"><?php echo form_error('medicine_category_id[]'); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control select2" style="width:100%" onchange="getbatchnolist(this.value, 0)" id="medicine_name0" name='medicine_name[]'>

                                                                        <option value=""><?php echo $this->lang->line('select') ?>
                                                                        </option>

                                                                    </select>
                                                                    <span class="text-danger"><?php echo form_error('medicine_name[]'); ?>
                                                                </td>


                                                                <td>
                                                                    <input type="text" name="batch_no[]" id="batchno" class="form-control">
                                                                    <span class="text-danger"><?php echo form_error('batch_no[]'); ?>
                                                                    </span>
                                                                </td>

                                                                <td>
                                                                    <input type="text" name="expiry_date[]" id="expiry" class="form-control">
                                                                    <span class="text-danger"><?php echo form_error('expiry_date[]'); ?>
                                                                    </span>
                                                                </td>





                                                                <td>
                                                                    <input type="text" name="packing_qty[]" id="packing_qty" class="form-control">
                                                                    <span class="text-danger"><?php echo form_error('packing_qty[]'); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="text" name="quantity[]" onchange="multiply(0)" id="quantity0" class="form-control text-right">
                                                                    </div>
                                                                </td>

                                                                <td class="text-right">

                                                                    <input type="text" name="purchase_price[]" onchange="multiply(0)" id="purchase_price0" placeholder="" class="form-control text-right">
                                                                    <span class="text-danger"><?php echo form_error('purchase_price[]'); ?></span>
                                                                </td>

                                                                <td class="text-right">
                                                                    <input type="text" name="amount[]" readonly id="amount0" placeholder="" class="form-control text-right">
                                                                    <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                                                                </td>
                                                                <td><button type="button" onclick="addMore()" style="color: #2196f3" class="closebtn"><i class="fa fa-plus"></i></button></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="divider"></div>

                                                    <div class="row">
                                                        <div class="col-sm-5">
                                                            <div class="form-group">
                                                                <?php echo $this->lang->line('note'); ?>
                                                                <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                                                            </div>

                                                            <!-- <div class="form-group">
                                                <label><?php echo $this->lang->line('attach_document') ?></label>
                                                <input type="file" name="file" id="file" class="form-control filestyle" />
                                            </div> -->
                                                        </div>
                                                        <div class="col-sm-7">
                                                            <table class="custom-table tableprintablea4">
                                                                <tr>
                                                                    <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                                                    <td width="60%" colspan="2" class="text-right ipdbilltable"><input type="text" readonly placeholder="Total" value="0" name="total" id="total" style="width: 50%; float: right" class="form-control" /></td>
                                                                </tr>


                                                                <!-- <tr>
                                                                    <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                                    <td colspan="2" class="text-right ipdbilltable">
                                                                        <input type="text" placeholder="Net Amount" value="0" name="net_amount" id="net_amount" style="width: 50%; float: right" class="form-control" />
                                                                    </td>
                                                                </tr> -->
                                                            </table>
                                                        </div>


                                                    </div><!--./row-->
                                                </div><!--./col-md-12-->

                                            </div><!--./row-->

                                        </div><!--./col-md-12-->
                                    </div><!--./row-->

                                </div><!--./modal-body-->
                                <div class="box-footer" style="clear: both;">
                                    <div class="pull-left">
                                        <button type="button" onclick="addMore()" style="color: #ffff" class="closebtn btn btn-info text-white"><i class="fa fa-plus"></i>Add More</button>

                                    </div>
                                    <div class="pull-right">
                                        <input type="hidden" id="is_draft" name="is_draft" value="0">
                                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" style="display: none;margin-right:10px" id="billsave" class="btn btn-info pull-right">Publish</button>
                                        <input type="button" onclick="addTotal()" value="<?php echo $this->lang->line('calculate'); ?>" class="btn btn-info" />&nbsp;
                                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" style="display: none;margin-right:10px" id="billdraft" class="btn btn-warning pull-right">Draft</button>
                                    </div>
                                </div><!--./box-footer-->
                            </div>
                    </form>
                </div>
            </div>
        </div>
</div>
</section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/easy-select@1.1.19/lib/EasySelect.min.js"></script>
<script type="text/javascript">
   $(document).ready(function() {
    $("#billdraft").click(function(e) {
        e.preventDefault();
        $("#is_draft").val(1); // Set draft status
        submitBillForm($(this));
    });

    $("#billsave").click(function(e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to save?")) {
            return; // Stop submission if the user clicks "Cancel"
        }
        $("#is_draft").val(0); // Set final status
        submitBillForm($(this));
    });

    function submitBillForm(button) {
        addTotal();
        var btn = button;
        btn.button('loading');

        $.ajax({
            url: '<?php echo base_url(); ?>hospital/pharmacy/addBillSupplier',
            type: "POST",
            data: new FormData($("#bill")[0]),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function(index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.href = "<?php echo base_url(); ?>hospital/pharmacy/purchase";
                }
                btn.button('reset');
            },
            error: function() {
                btn.button('reset');
            }
        });
    }
});


    function addMore() {
    var table = document.getElementById("tableID");
    var table_len = table.rows.length;
    var id = parseInt(table_len - 1); // Assign new row ID

    // Get the last row's selected category and medicine
    var lastCategory = $("select[name='medicine_category_id[]']").last().val();
    var lastMedicine = $("select[name='medicine_name[]']").last().val();

    var div = `<td>
                    <select class='form-control' name='medicine_category_id[]' id='medicine_category_${id}' onchange='getmedicine_name(this.value, ${id})'>
                        <option value=''><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                            <option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"]; ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <select class='form-control select2' style='width:100%' name='medicine_name[]' id='medicine_name${id}' onchange='getbatchnolist(this.value, ${id})'>
                        <option value=''><?php echo $this->lang->line('select'); ?></option>
                    </select>
                </td>
                <td><input type='text' name='batch_no[]' id='batchno${id}' class='form-control batch_no'></td>
                <td><input type='text' name='expiry_date[]' id='expiry${id}' class='form-control expiry_date'></td>
                <td><input type='text' name='packing_qty[]' id='packingqty${id}' class='form-control packing_qty'></td>
                <td><div class='input-group'>
                        <input type='text' name='quantity[]' onchange='multiply(${id})' id='quantity${id}' class='form-control text-right'>
                    </div>
                </td>
                <td><input type='text' onchange='multiply(${id})' name='purchase_price[]' id='purchase_price${id}' class='form-control text-right'></td>
                <td><input type='text' name='amount[]' readonly id='amount${id}' class='form-control text-right'></td>`;

    var row = table.insertRow(table_len).outerHTML = `<tr id='row${id}'>${div}<td>
                    <button type='button' onclick='delete_row(${id})' class='closebtn'><i class='fa fa-remove'></i></button>
                </td></tr>`;

    $('.select2').select2();

    var expiry_date = '<?php echo strtr($this->customlib->getSchoolDateFormat(), ["d" => "DD", "m" => "MM", "Y" => "YYYY"]); ?>';
    $('.expiry_date').datepicker({
        format: "M/yyyy",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    });

    // Set the category from the previous row
    if (lastCategory) {
        $("#medicine_category_" + id).val(lastCategory).trigger("change");

        // Load medicines automatically when category is set
        setTimeout(function () {
            getmedicine_name(lastCategory, id);
        }, 500);
    }

    // Set the medicine name from the previous row
    setTimeout(function () {
        if (lastMedicine) {
            $("#medicine_name" + id).val(lastMedicine).trigger("change");
        }
    }, 1000);
}



    function delete_row(id) {
        var table = document.getElementById("tableID");
        var rowCount = table.rows.length;
        $("#row" + id).remove();
    }


    function multiply(id) {

        var quantity = $('#quantity' + id).val();
        var availquantity = $('#available_quantity' + id).val();
        if (parseInt(quantity) > parseInt(availquantity)) {
            errorMsg('Order quantity should not be greater than available quantity');
        } else {
            //alert(parseInt(quantity));
        }
        var purchase_price = $('#purchase_price' + id).val();
        var amount = quantity * purchase_price;
        $('#amount' + id).val(amount);
    }

    function getExpire(id) {
        var batch_no = $("#batch_no" + id).val();
        $.ajax({
            type: "POST",
            url: base_url + "hospital/pharmacy/getExpiryDate",
            data: {
                'batch_no': batch_no
            },
            dataType: 'json',
            success: function(res) {
                if (res != null) {
                    $('#expiry_date' + id).val(res.expiry_date);
                    getQuantity(id);
                }
            }
        });
    }

    function addTotal() {
        var total = 0;
        var sale_price = document.getElementsByName('amount[]');
        for (var i = 0; i < sale_price.length; i++) {
            var inp = sale_price[i];
            if (inp.value == '') {
                var inpvalue = 0;
            } else {
                var inpvalue = inp.value;
            }
            total += parseInt(inpvalue);
        }
        var discount_percent = $("#discount_percent").val();
        var tax_percent = $("#tax_percent").val();
        // var discount_amnt = $("#discount").val();
        //var tax_amnt = $("#tax").val();

        if (discount_percent != '') {
            var discount = (total * discount_percent) / 100;
            $("#discount").val(discount.toFixed(2));
        } else {
            var discount = $("#discount").val();
            //var discount = 0; 
        }

        if (tax_percent != '') {
            var tax = ((total - discount) * tax_percent) / 100;
            $("#tax").val(tax.toFixed(2));
        } else {
            var tax = $("#tax").val();
            // var tax = 0; 
        }


        //   var tax = $("#tax").val();
        //  var discount = $("#discount").val();
        $("#total").val(total.toFixed(2));

        var net_amount = parseFloat(total) + parseFloat(tax) - parseFloat(discount);
        // var net_amount = (total)+(tax) - (discount);
        //  alert(net_amount);
        var cnet_amount = net_amount.toFixed(2)
        $("#net_amount").val(cnet_amount);
        var editdate = $("#date_supplier").val();
        $("#date_result").val(editdate);
        var invoiceno = $("#invoice_no").val();
        $("#invoiceno").val(invoiceno);
        $("#billsave").show();
        $("#billdraft").show();
        $(".printsavebtn").show();
    }
    $(document).ready(function(e) {

        $('#expiry').datepicker({
            format: "M/yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
    });
    $(function() {
        var datetime_format = '';
        $("body").delegate(".datetime", "focusin", function() {
            $(this).datetimepicker({
                format: datetime_format,
                locale: 'en',

            });
        });

        var date_format = '';
        $("body").delegate(".date", "focusin", function() {

            $(this).datepicker({
                todayHighlight: false,
                format: date_format,
                autoclose: true,
                language: 'en'
            });
        });
        var daterange_format = '';
        $("body").delegate(".daterange", "focusin", function() {
            $(this).daterangepicker({
                locale: {
                    format: daterange_format,
                },

            });
        });
    });
    $(function() {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
    $(function() {
        // $('#easySelectable').easySelectable();
        //stopPropagation();
    })

    function get_SupplierDetails(id) {
        $("#supplier_name").html("supplier_name");
        //$("#schedule_charge").html("schedule_charge");

        $.ajax({
            url: '<?php echo base_url(); ?>hospital/pharmacy/supplierDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                if (res) {
                    $('#supplier_name').val(res.supplier_person);
                    $('#supplierid').val(res.id);
                } else {
                    $('#supplier_name').val('Null');

                }
            }
        });
    }
    function get_SupplierList(id) {
        $("#supplier_list").html('<option value="">Loading...</option>'); // Show loading

        $.ajax({
            url: '<?php echo base_url(); ?>hospital/pharmacy/supplierList',
            type: "POST",
            data: { id: id },
            dataType: 'json',
            success: function(res) {
                console.log(res);

                if (res.length > 0) {
                    let options = '<option value="">-- Select Supplier --</option>';
                    $.each(res, function(index, supplier) {
                        options += `<option value="${supplier.id}">${supplier.supplier_category}</option>`;
                    });
                    $('#supplier_list').html(options);
                } else {
                    $('#supplier_list').html('<option value="">No suppliers found</option>');
                }
            },
            error: function() {
                $('#supplier_list').html('<option value="">Error loading suppliers</option>');
            }
        });
    }

    function getmedicine_name(id, rowid) {
        var div_data = "";

        //$("#medicine_name" + rowid).prepend($('<option></option>').html('Loading...'));
        $("#medicine_name" + rowid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#medicine_name' + rowid).select2("val", 'l');
        $.ajax({
            url: '<?php echo base_url(); ?>hospital/pharmacy/get_medicine_name',
            type: "POST",
            data: {
                medicine_category_id: id
            },
            dataType: 'json',
            success: function(res) {
                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value=" + obj.id + ">" + obj.medicine_name + "</option>";
                });
                $("#medicine_name" + rowid).html("<option value=''>Select</option>");
                $('#medicine_name' + rowid).append(div_data);
                $('#medicine_name' + rowid).select2("val", '');
                //$('#medicine_name'+rowid).select2();
            }
        });
    }

    function getbatchnolist(id, rowid) {
        var div_data = "";
        $('#totalqty' + rowid).html("<span class='input-group-addon text-danger' style='font-size:10pt'  id='totalqty" + rowid + "'></span>");
        $('#available_quantity' + rowid).val('');
        $('#purchase_price' + rowid).val('');
        $('#expiry_date' + rowid).val('');
        $('#amount' + rowid).val('');
        $('#quantity' + rowid).val('');
        $("#batch_no" + rowid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");

        $.ajax({
            type: "POST",
            url: base_url + "hospital/pharmacy/getBatchNoList",
            data: {
                'medicine': id
            },
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.batch_no + "'>" + obj.batch_no + "</option>";
                });
                $("#batch_no" + rowid).html("<option value=''>Select</option>");
                $('#batch_no' + rowid).append(div_data);
            }
        });
    }
</script>