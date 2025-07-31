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

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/easy-select@1.1.19/lib/EasySelect.min.js"></script>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
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
                                                <?php foreach ($supplierTypes as $dkey => $dvalue) { ?>
        <option value="<?php echo $dvalue->id; ?>" 
            <?php echo ($dvalue->id == $supplier_type_id) ? 'selected' : ''; ?>>
            <?php echo $dvalue->name; ?>
        </option>
    <?php } ?>
                                            </select>

                                            <span class="text-danger"><?php echo form_error('refference'); ?></span>

                                        </div><!--./col-sm-5-->
                    <div class="col-sm-2 col-lg-2 col-md-2">
                        <select style="width: 100%" onchange="get_SupplierDetails(this.value)" class="form-control select2" id="editsupplier" name='supplier' >
                            <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('supplier') ?></option>
                            <?php foreach ($supplierCategory as $dkey => $dvalue) { ?>
                                <option value="<?php echo $dvalue["id"]; ?>" <?php
                                        if ((isset($supplier_select)) && ($supplier_select == $dvalue["id"])) {
                                            echo "selected";
                                        }
                                        ?>><?php echo $dvalue["supplier_category"]; ?></option>   
                         <?php } ?>
                        </select>

                        <span class="text-danger"><?php echo form_error('refference'); ?></span>

                    </div><!--./col-sm-5-->  


                    <div class="col-sm-3 col-lg-3 col-md-3"> 
                        <div class="row">        
                            <div class="col-lg-5 col-sm-5 col-xs-6">
                                <label><?php echo $this->lang->line('purchase') . " " . $this->lang->line('no'); ?></label>
                            </div><!--./col-sm-6-->
                            <div class="col-lg-5 col-sm-5 col-xs-6">                 
                                <input name="purchase_no" id="purchaseno" value="<?php echo $result['purchase_no']; ?>"  readonly type="text" class="form-control" value="" />
                                <span class="text-danger"><?php echo form_error('purchase_no'); ?></span>
                            </div><!--./col-sm-6-->
                        </div><!--./row-->    
                    </div><!--./col-sm-6--> 
                    

                    <div class="col-sm-3 col-lg-3 col-md-3"> 
                        <div class="row">        
                            <div class="col-lg-6 col-sm-6 col-xs-5">
                                <label><?php echo $this->lang->line('purchase') . " " . $this->lang->line('date'); ?></label>
                            </div><!--./col-sm-6-->
                            <div class="col-lg-5 col-sm-6 col-xs-7">                 
                            <input name="datetime-local" id="dateedit_supplier" 
       value="<?php echo date('Y-m-d\TH:i', strtotime($result['date'])); ?>" 
       type="datetime-local" class="form-control" />


                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                            </div><!--./col-sm-6-->
                        </div><!--./row-->    
                    </div><!--./col-sm-6--> 


                    <div class="pull-right">
                        <button type="button" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" class="close" data-dismiss="modal">&times;</button>
                        <!-- <h4 class="box-title"><?php echo $this->lang->line('purchase') . " " . $this->lang->line('medicine'); ?></h4>  -->
                    </div><!--./col-sm-6-->   
                </div><!--./row--> 
            </div>                 
            <div class="modal-body pt0 pb0" id="edit_bill_details">
            <?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/dropify.min.css">
<link href="<?php echo base_url(); ?>backend/dist/css/nprogress.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>backend/dist/js/nprogress.js"></script>
<script src="<?php echo base_url(); ?>backend/dist/js/dropify.min.js"></script>

<!-- -->
<form id="editbill" accept-charset="utf-8" method="post" class="ptt10">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
            <div class="row">
                <?php foreach ($detail as $pkey => $pvalue) {
                    ?>
                    <input type="hidden" name="previous_bill_id[]" value="<?php echo $pvalue['id'] ?>">

                <?php } ?>
                <input name="bill_basic_id" type="hidden" class="form-control" value="<?php echo $result['id']; ?>" />
                <input name="purchase_no"  type="hidden" class="form-control" value="<?php echo $result['purchase_no']; ?>" />
                <input name="invoice_no" id="editinvoiceno" type="hidden" class="form-control" value="" />
                <input name="supplier_id"  id="editsupplierid" type="hidden" class="form-control" value="" />
                <input name="date" id="editdate_supplier" type="hidden" class="form-control" value="" />

                <script type="text/javascript">
                    function geteditQuantity(id, batch = '') {
                        if (batch == "") {
                            var batch_no = $('#batch_edit_no' + id).val();
                        } else {
                            var batch_no = batch;
                        }
                        //  alert(batch_no);
                        if (batch_no != "") {
                            $('#quantity_edit').html("");
                            $.ajax({
                                type: "GET",
                                url: base_url + "hospital/pharmacy/getQuantityedit",
                                data: {'batch_no': batch_no},
                                dataType: 'json',
                                success: function (data) {

                                    $('#medicine_batch_id' + id).val(data.id);
                                    //$('#quantity_edit').html(data.available_quantity);
                                    //$('#totaleditqty' + id).html(data.available_quantity);
                                    //$('#available_edit_quantity' + id).val(data.available_quantity);
                                    $('#purchase_price_edit' + id).val(data.purchase_price);
                                }
                            });
                    }
                    }

                    function geteditExpire(id) {
                        var batch_no = $("#batch_edit_no" + id).val();
                        $('#edit_expiry_date' + id).val('');
                        $.ajax({
                            type: "POST",
                            url: base_url + "hospital/pharmacy/getExpireDate",
                            data: {'batch_no': batch_no},
                            dataType: 'json',
                            success: function (data) {
                                if (data != null) {
                                    $('#edit_expiry_date' + id).val(data.expiry_date);
                                    geteditQuantity(id, batch_no)

                                }
                            }
                        });
                    }

                    function getmedicine_edit_name(id, rowid, selectid = '') {
    $("#medicine_edit_name" + rowid).html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
    
    $.ajax({
        url: '<?php echo base_url(); ?>hospital/pharmacy/get_medicine_name',
        type: "POST",
        data: {medicine_category_id: id},
        dataType: 'json',
        success: function (res) {
            var options = "<option value=''><?php echo $this->lang->line('select') ?></option>";
            $.each(res, function (i, obj) {
                var sel = obj.id == selectid ? "selected" : "";
                options += `<option ${sel} value="${obj.id}">${obj.medicine_name}</option>`;
            });

            $("#medicine_edit_name" + rowid)
                .html(options) // Add options
                .select2(); // Reinitialize select2 after DOM update
        },
        error: function () {
            alert('Error fetching data.');
        },
    });
}



                    function geteditbatchnolist(id, rowid, selectid) {

                        // var batch_no = $("#batch_no"+id).val();
                        //$('#medicine_name'+rowid).select2("val", '');
                        var div_data = "";
                        $('#medicine_batch_id' + rowid).val('');
                        $('#quantity_edit').html('');
                        $('#totaleditqty' + rowid).html('');
                        $('#available_edit_quantity' + rowid).val('');
                        $('#purchase_price_edit' + rowid).val('');
                        $('#edit_expiry_date' + rowid).val('');
                        $("#batch_edit_no" + rowid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
                        $.ajax({
                            type: "POST",
                            url: base_url + "hospital/pharmacy/getBatchNoList",
                            data: {'medicine': id},
                            dataType: 'json',
                            success: function (res) {
                                console.log(res);
                                $.each(res, function (i, obj)
                                {
                                    var sel = "";
                                    if (obj.batch_no == selectid) {
                                        sel = "selected";
                                    }
                                    div_data += "<option " + sel + " value='" + obj.batch_no + "'>" + obj.batch_no + "</option>";
                                });
                                $("#batch_edit_no" + rowid).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                                $('#batch_edit_no' + rowid).append(div_data);
                                $('#mrp_edit_more' + rowid).val(res.mrp);
                                //$('#edit_mrp' + rowid).val('');
                                geteditExpire(rowid)
                            }
                        });
                    }
                </script>
                <div class="col-md-12" style="clear: both;">
                    <div class="table-responsive">
                        <table class="custom-table table tableover table-striped table-bordered table-hover tablefull12" id="edittableID">
                            <tr style="font-size: 13px">
                                <th width="13%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?><small class="req" style="color:red;"> *</small></th>
                                <th width="22%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
                                <th width="8%"><?php echo $this->lang->line('batch') . " " . $this->lang->line('no'); ?><small style="color:red;"> *</small></th>
                                <th width="9%"><?php echo $this->lang->line('expire') . " " . $this->lang->line('date'); ?><small class="req" style="color:red;"> *</small></th>


                                <th width="6%" class="text-right;"><?php echo $this->lang->line('quantity'); ?><small class="req" style="color:red;"> *</small> </th>
                                <th width="11%" class="text-right"><?php echo $this->lang->line('purchase') . " " . $this->lang->line('price') . " " . ' (' . $currency_symbol . ')'; ?><small class="req" style="color:red;"> *</small></th>
                                <th width="11%" class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req" style="color:red;"> *</small></th>
                            </tr>
                            <?php
                            $i = 0;
                            foreach ($detail as $key => $value) {
                              
                                ?>
                                <script type="text/javascript">
                                    // getmedicine_edit_name('<?php echo $value['medicine_category_id'] ?>', '<?php echo $i ?>', '<?php echo $value['medicine_id'] ?>');

                                </script>
                                <tr id="row<?php echo $i ?>">
                                    <td width="16%"> 
                                        <input name="id" type="hidden" class="form-control" value="<?php echo $value['id']; ?>" />
                                        <select class="form-control" name='medicine_category_id[]'  onchange="getmedicine_edit_name(this.value, '<?php echo $i ?>', '<?php echo $value['medicine_name'] ?>')">
                                            <option value=""><?php echo $this->lang->line('select') ?>
                                            </option>
                                            <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                ?>
                                                <option value="<?php echo $dvalue["id"]; ?>" <?php if ($value["medicine_category_id"] == $dvalue["id"]) echo "selected"; ?> ><?php echo $dvalue["medicine_category"] ?>
                                                </option>   
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_category_id[]'); ?>
                                        </span>
                                    </td>
                                    <td >
                                        <input type="text" class="form-control" readonly value="<?php echo $value['medicine_name'] ?>">
                                        <input type="hidden" class="form-control"   name="medicine_name[]"  value="<?php echo $value['medicine_id'] ?>">

                                        
                                        <span class="text-danger"><?php echo form_error('medicine_name[]'); ?>
                                    </td>
                                    <td >
                                        <input type="text"  name="batch_no[]" id="batch_edit_no<?php echo $i ?>" class="form-control" value="<?php echo $value['batch_no']; ?>" >
                                        <span class="text-danger"><?php echo form_error('batch_no[]'); ?>
                                        </span>
                                    </td>
                                    <td >
                                        <input type="text" name="expiry_date[]" id="edit_expiry_date<?php echo $i ?>" class="form-control expires_date expiry" value="<?php echo $value['expiry_date']; ?>" >
                                        <span class="text-danger"><?php echo form_error('expiry_date[]'); ?>
                                        </span>
                                    </td>

                                    
                                  
                                   
                                  

                                    <td class="text-right">
                                      <!--input type="text" name="quantity[]" id="quantity_edit<?php echo $i ?>" placeholder="Quantity" class="form-control" id="quantity_edit" onchange="multiply(<?php echo $i ?>)" onfocus="geteditQuantity('<?php echo $i ?>')" value="<?php echo $value['quantity']; ?>"/>
                                      <span id="totaleditqty<?php echo $i ?>" class="text-danger"><?php echo form_error('quantity[]'); ?></span-->
                                        <div class="input-group">
                                            <input type="text" name="quantity[]" onchange="multiply(<?php echo $i ?>)" onfocus="geteditQuantity(<?php echo $i ?>)" value="<?php echo $value['quantity']; ?>" id="quantity_edit<?php echo $i ?>" class="form-control text-right">
                                            <!--<span class="input-group-addon text-danger"  id="totaleditqty<?php echo $i ?>">&nbsp;&nbsp;</span>-->
                                        </div>
                                        <!--<input type="hidden" name="available_quantity[]" id="available_edit_quantity<?php echo $i ?>">-->
                                        <input type="hidden" name="medicine_batch_id[]" id="medicine_batch_id<?php echo $i ?>" >
                                        <input type="hidden" name="bill_detail_id[]" value="<?php echo $value["id"] ?>" >
                                    </td>
                                    <td class="text-right">
                                        <input type="text" name="purchase_price[]" onchange="multiply(<?php echo $i ?>)" id="purchase_price_edit<?php echo $i ?>" placeholder="purchase Price" class="form-control text-right" value="<?php echo $value['purchase_price']; ?>"/>
                                        <span class="text-danger"><?php echo form_error('purchase_price[]'); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" readonly name="amount[]" id="amount_edit<?php echo $i ?>" placeholder="Amount" class="form-control text-right" value="<?php echo $value['amount']; ?>"/>
                                        <span class="text-danger"><?php echo form_error('amount[]'); ?></span>
                                    </td>
                                        <td><button type='button' onclick="delete_row('<?php echo $i ?>')" class='closebtn'><i class='fa fa-remove'></i></button></td>
                                </tr>
                                <?php
                                $i++;
                            }
                            ?>
                        </table>
                    </div>  
                    <div class="divider"></div>

                    <!-- <div class="col-sm-4">
                      <div class="form-group">
                        <input type="text" placeholder="Total" value="<?php //echo $result["total"]     ?>" name="total" id="edittotal" class="form-control"/>
                      </div>
                    </div>
                    -->
                    <div class="row">   
                        <div class="col-sm-5 col-lg-6 col-md-6">
                            <table class="custom-tableprintablea4" width="100%">
                                <tr>
                                    <th><?php echo $this->lang->line('note'); ?></th>
                                    <td><textarea name="note" id="note" class="form-control"><?php echo $result["note"] ?></textarea></td>
                                </tr>
                            </table>
                            
                        </div><!--./col-sm-6-->
                        <div class="col-sm-7 col-lg-6 col-md-6">
                            <div class="table-responsive">
                                <table class="custom-tableprintablea4">
                                    <tr>
                                        <th width="25%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                        <td class="text-right ipdbilltable" width="75%" colspan="2"><input type="text" placeholder="Total" value="<?php echo $result["total"] ?>" readonly value="0" name="total" id="edittotal"  style="width: 50%; float: right" class="form-control"/></td>
                                    </tr>
                                 
                                   


                                </table>
                            </div>    

                        </div><!--.col-sm-6-->
                    </div><!--./row-->  
                </div><!--.col-sm-12-->
                <!--  <div class="col-sm-offset-9 ">
                   <label>Total</label>
                   <input type="text" name="total" placeholder="Total">
                 </div> -->
            </div><!--./row-->   

        </div><!--./col-md-12--> 
        <div class="box-footer" style="clear: both">
        <div class="pull-left">
                                <button type="button" onclick="editMore()" style="color: #ffff" class="closebtn btn btn-info text-white"><i class="fa fa-plus"></i>Add More</button>

                                </div>
            <div class="pull-right">
                <input type="hidden" id="is_draft" name="is_draft" value="0">
                
                <input type="button" onclick="addEditTotal()" value="<?php echo $this->lang->line('calculate'); ?>" class="btn btn-info"/>&nbsp;
              
                <button type="submit" style="display: none" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="editbillsave" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" style="display: none;margin-right:10px" id="editbilldraft" class="btn btn-warning pull-right">Draft</button>
           
            </div>
        </div><!--./box-footer-->
    </div><!--./row--> 

</form>      







            </div>    
        </div>
    </div> 
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/easy-select@1.1.19/lib/EasySelect.min.js"></script>
<script type="text/javascript">
     var expiry_date = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY',]) ?>';
    // $('.expires_date').datepicker({
    //     format: "M/yyyy",
    //     viewMode: "months",
    //     minViewMode: "months",
    //     autoclose: true
    // });
    function multiply(id) {

        var quantity = $('#quantity_edit' + id).val();
        // var availquantity = $('#available_edit_quantity' + id).val();
        // if (parseInt(quantity) > parseInt(availquantity)) {
        //     errorMsg('Order quantity should not be greater than available quantity');
        // } else {
        //alert(parseInt(quantity));
        // }
        var purchase_price = $('#purchase_price_edit' + id).val();
        var amount = quantity * purchase_price;


        $('#amount_edit' + id).val(amount.toFixed(2));
    }

    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();


    });
   
    $(document).ready(function(e) {

$('.expiry').datepicker({
    format: "M/yyyy",
    viewMode: "months",
    minViewMode: "months",
    autoclose: true
});
});
    function editMore() {
    var table = document.getElementById("edittableID");
    var table_len = table.rows.length;
    var id = parseInt(table_len);

    // Get the last row's selected category and medicine
    var lastCategory = $("select[name='medicine_category_id[]']").last().val();
    var lastMedicine = $("select[name='medicine_name[]']").last().val();

    var div = `<td>
                    <select class='form-control' name='medicine_category_id[]' id='medicine_category_${id}' onchange='getmedicine_edit_name(this.value, ${id})'>
                        <option value=''><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                            <option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"]; ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <select class='form-control select2' name='medicine_name[]' id='medicine_edit_name${id}' onchange='geteditbatchnolist(this.value, ${id})'>
                        <option value=''><?php echo $this->lang->line('select'); ?></option>
                    </select>
                </td>
                <td><input type='text' id='batch_edit_no${id}' name='batch_no[]' class='form-control'></td>
                <td><input type='text' id='edit_expiry_date${id}' name='expiry_date[]' class='form-control expires_date expiry'></td>
                <td><div class='input-group'>
                        <input type='text' name='quantity[]' onchange='multiply(${id})' onfocus='geteditQuantity(${id})' id='quantity_edit${id}' class='form-control text-right'>
                    </div>
                    <input type='hidden' name='available_quantity[]' id='available_edit_quantity${id}'>
                    <input type='hidden' class='form-control' value='0' name='bill_detail_id[]'>
                    <input type='hidden' name='medicine_batch_id[]' id='medicine_batch_id${id}'>
                </td>
                <td><input type='text' name='purchase_price[]' onchange='multiply(${id})' id='purchase_price_edit${id}' class='form-control text-right'></td>
                <td><input type='text' readonly name='amount[]' id='amount_edit${id}' class='form-control text-right'></td>`;

    var row = table.insertRow(table_len).outerHTML = `<tr id='row${id}'>${div}<td>
                    <button type='button' onclick='delete_row(${id})' class='closebtn'><i class='fa fa-remove'></i></button>
                </td></tr>`;

    var expiry_date = '<?php echo strtr($this->customlib->getSchoolDateFormat(), ["d" => "DD", "m" => "MM", "Y" => "YYYY"]); ?>';
    $('.expiry').datepicker({
        format: "M/yyyy",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true,
    });

    $('.select2').select2();

    // Set the category from the previous row
    if (lastCategory) {
        $("#medicine_category_" + id).val(lastCategory).trigger("change");

        // Load medicines automatically when category is set
        setTimeout(function () {
            getmedicine_edit_name(lastCategory, id, lastMedicine);
        }, 500);
    }
}



    function addEditTotal() {
        var total = 0;
        var purchase_price = document.getElementsByName('amount[]');
        for (var i = 0; i < purchase_price.length; i++) {
            var inp = purchase_price[i];
            if (inp.value == '') {
                var inpvalue = 0;
            } else {
                var inpvalue = inp.value;
            }
            total += parseInt(inpvalue);
        }
        var tax_percent = $("#edittax_percent").val();
        var discount_percent = $("#editdiscount_percent").val();

        if (discount_percent != '') {
            var discount = (total * discount_percent) / 100;
            $("#editdiscount").val(discount.toFixed(2));
        } else {
            var discount = $("#editdiscount").val();
            //var discount = 0; 
        }

        if (tax_percent != '') {
            var tax = ((total - discount) * tax_percent) / 100;
            $("#edittax").val(tax.toFixed(2));
        } else {
            var tax = $("#edittax").val();
            // var tax = 0; 
        }

        $("#edittotal").val(total.toFixed(2));

        var net_amount = parseFloat(total) + parseFloat(tax) - parseFloat(discount);
        $("#editnet_amount").val(net_amount.toFixed(2));
        var supplierid = $("#editsupplier").val();
        $("#editsupplierid").val(supplierid);
         var invoiceno = $("#invoicenoup").val();
        $("#editinvoiceno").val(invoiceno);
        var editdate = $("#dateedit_supplier").val();
        $("#editdate_supplier").val(editdate);
        $("#editbillsave").show();
        $("#editbilldraft").show();

    }

    function delete_row(id) {
    var rowElement = $("#row" + id);
    var billDetailId = rowElement.find("input[name='bill_detail_id[]']").val(); // Get bill_detail_id
console.log('billDetailId',billDetailId)
    if (billDetailId != 0) {
        // Confirm before deletion
        if (!confirm("Are you sure you want to delete this item?")) {
            return;
        }

        $.ajax({
            url: '<?php echo base_url(); ?>hospital/pharmacy/deleteSupplierBillDetail',
            type: "POST",
            data: { bill_detail_id: billDetailId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    successMsg("Row deleted successfully.");
                    rowElement.remove(); // Remove row from frontend after successful deletion
                } else {
                    errorMsg(response.message || "Error deleting the row.");
                }
            },
            error: function () {
                errorMsg("Failed to delete. Please try again.");
            }
        });
    } else {
        // If no bill_detail_id, just remove from UI
        rowElement.remove();
    }
}



    $(document).ready(function () {

$("#editbilldraft").click(function(e) {
    e.preventDefault();
    $("#is_draft").val(1); // Set draft status
    submitBillForm($(this)); // Pass the clicked button
});

$("#editbillsave").click(function(e) {
    e.preventDefault();
    if (!confirm("Are you sure you want to save?")) {
            return; // Stop submission if the user clicks "Cancel"
        }
    $("#is_draft").val(0); // Set final status
    submitBillForm($(this)); // Pass the clicked button
});

function submitBillForm(button) {
    addEditTotal();
    button.button('loading'); // Set the clicked button to loading state

    var table = document.getElementById("edittableID");
    var rowCount = table.rows.length;

    for (var k = 0; k < rowCount; k++) {
        var quantityk = $('#quantity_edit' + k).val();
        var availquantityk = $('#available_edit_quantity' + k).val();
        if (parseInt(quantityk) > parseInt(availquantityk)) {
            errorMsg('Order quantity should not be greater than available quantity');
            button.button('reset'); // Reset button state
            return false;
        }
    }

    $.ajax({
        url: '<?php echo base_url(); ?>hospital/pharmacy/updateSupplierBill',
        type: "POST",
        data: new FormData($("#editbill")[0]), // Correct form data submission
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.status == "fail") {
                var message = "";
                $.each(data.error, function (index, value) {
                    message += value + "<br>";
                });
                errorMsg(message);
            } else {
                successMsg(data.message);
                window.location.reload(true);
                window.location.href = "<?php echo base_url(); ?>hospital/pharmacy";
            }
            button.button('reset'); // Reset button state after success/error
        },
        error: function () {
            button.button('reset'); // Reset button state on error
        }
    });
}
});

    // function viewDetail(id){
    //   $.ajax({
    //     url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/'+id,
    //     type: "GET",     
    //     data: { id:id },
    //     success: function (data) { 
    //       $('#reportdata').html(data); 
    //     },
    //   });
    // } 


// function add_instruction(id){
//     $('#ins_patient_id').val(id);
// }
</script>
<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();
        //stopPropagation();
    })
</script>
<script type="text/javascript">
            /*
             Author: mee4dy@gmail.com
             */
                    (function ($) {
                        //selectable html elements
                        $.fn.easySelectable = function (options) {
                            var el = $(this);
                            var options = $.extend({
                                'item': 'li',
                                'state': true,
                                onSelecting: function (el) {
                                },
                                onSelected: function (el) {
                                },
                                onUnSelected: function (el) {
                                }
                            }, options);
                            el.on('dragstart', function (event) {
                                event.preventDefault();
                            });
                            el.off('mouseover');
                            el.addClass('easySelectable');
                            if (options.state) {
                                el.find(options.item).addClass('es-selectable');
                                el.on('mousedown', options.item, function (e) {
                                    $(this).trigger('start_select');
                                    var offset = $(this).offset();
                                    var hasClass = $(this).hasClass('es-selected');
                                    var prev_el = false;
                                    el.on('mouseover', options.item, function (e) {
                                        if (prev_el == $(this).index())
                                            return true;
                                        prev_el = $(this).index();
                                        var hasClass2 = $(this).hasClass('es-selected');
                                        if (!hasClass2) {
                                            $(this).addClass('es-selected').trigger('selected');
                                            el.trigger('selected');
                                            options.onSelecting($(this));
                                            options.onSelected($(this));
                                        } else {
                                            $(this).removeClass('es-selected').trigger('unselected');
                                            el.trigger('unselected');
                                            options.onSelecting($(this))
                                            options.onUnSelected($(this));
                                        }
                                    });
                                    if (!hasClass) {
                                        $(this).addClass('es-selected').trigger('selected');
                                        el.trigger('selected');
                                        options.onSelecting($(this));
                                        options.onSelected($(this));
                                    } else {
                                        $(this).removeClass('es-selected').trigger('unselected');
                                        el.trigger('unselected');
                                        options.onSelecting($(this));
                                        options.onUnSelected($(this));
                                    }
                                    var relativeX = (e.pageX - offset.left);
                                    var relativeY = (e.pageY - offset.top);
                                });
                                $(document).on('mouseup', function () {
                                    el.off('mouseover');
                                });
                            } else {
                                el.off('mousedown');
                            }
                        };
                    })(jQuery);
                    function get_SupplierList(id) {
        $("#editsupplier").html('<option value="">Loading...</option>'); // Show loading

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
                    $('#editsupplier').html(options);
                } else {
                    $('#editsupplier').html('<option value="">No suppliers found</option>');
                }
            },
            error: function() {
                $('#editsupplier').html('<option value="">Error loading suppliers</option>');
            }
        });
    }



            $(document).ready(function () {
                // Basic
                $('.filestyle').dropify();

                // Translated
                $('.dropify-fr').dropify({
                    messages: {
                        default: 'Glissez-déposez un fichier ici ou cliquez',
                        replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
                        remove: 'Supprimer',
                        error: 'Désolé, le fichier trop volumineux'
                    }
                });

                // Used events
                var drEvent = $('#input-file-events').dropify();

                drEvent.on('dropify.beforeClear', function (event, element) {
                    return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
                });

                drEvent.on('dropify.afterClear', function (event, element) {
                    alert('File deleted');
                });

                drEvent.on('dropify.errors', function (event, element) {
                    console.log('Has Errors');
                });

                var drDestroy = $('#input-file-to-destroy').dropify();
                drDestroy = drDestroy.data('dropify')
                $('#toggleDropify').on('click', function (e) {
                    e.preventDefault();
                    if (drDestroy.isDropified()) {
                        drDestroy.destroy();
                    } else {
                        drDestroy.init();
                    }
                })



            });
</script>