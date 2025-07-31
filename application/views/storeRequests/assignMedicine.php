<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<style type="text/css">
    #easySelectable {/*display: flex; flex-wrap: wrap;*/}
    #easySelectable li {}
    #easySelectable li.es-selected {background: #2196F3; color: #fff;}
    .easySelectable {-webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;}
    /*.printablea4{width: 100%;}
    .printablea4 p{margin-bottom: 0;}
    .printablea4>tbody>tr>th,
    .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}*/
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
            <div class="modal fade" id="closeRequestModal" tabindex="-1" role="dialog" aria-labelledby="closeRequestModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="closeRequestModalLabel">Close Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="closeRequestForm">
                        <input type="hidden" name="bill_id" value="<?php echo isset($result['id']) ? $result['id'] : null; ?>">
                        <div class="form-group">
                            <label for="closeRemarks">Remarks</label>
                            <textarea class="form-control" id="closeRemarks" name="remarks" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
            <div class="" id="edit_bill" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
<form id="editbill"  accept-charset="utf-8" method="post" class="ptt10">
            <div class="modal-header modal-media-header">
                <div class="row modalbillform">
                    <div class="col-lg-5 col-sm-5">
                        <div class="row">
                            <div class="col-sm-9">
                                <span style="color:white"> <b>Store</b> <?php echo $result['store_name'] ?></span>
                               


                                <!-- <select onchange="get_PatienteditDetails(this.value)"  style="width: 100%" class="form-control select2" id="addeditpatient_id" name='patientid' >
                                    <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                   
                                </select> -->
                                <input name="patient_id"  id="patienteditid" type="hidden" class="form-control" value="" />
                                <input name="customer_name"  id="patienteditname" type="hidden" class="form-control" value="" />
                            </div><!--./col-sm-3-->
                            <!--<div class="col-sm-3">
                                        <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('new') . " " . $this->lang->line('patient') ?></a>
                        </div>--><!--./col-sm-3-->
                        </div><!--./row-->
                    </div><!--./col-sm-6-->
                    <div class="col-lg-6 col-sm-6">
                        <div class="row">
                            <div class="col-lg-2 col-sm-3 col-xs-3">
                                 <label>Indent No</label><!-- <small class="req" style="color:red;"> *</small> -->
                            </div>
                            <div class="col-lg-2 col-sm-3 col-xs-9">
                                <input readonly name="bill_no" id="editbillno" type="text" class="form-control"/>
                                <span class="text-danger"><?php echo form_error('bill_no'); ?></span>
                            </div>
                            <div class="mdclear"></div>
                            <div class="col-lg-2 col-sm-3 col-xs-3">
                                <label><?php echo $this->lang->line('date'); ?></label>
                            </div>
                            <div class="col-lg-5 col-sm-3 col-xs-9">
                                <input name="date" id=""  type="datetime-local" value="<?php echo date('Y-m-d\TH:i'); ?>" class="form-control"/>
                            </div>
                        </div><!--./row-->
                    </div><!--./col-sm-6-->

                    <div class="col-sm-1 pull-right">
                        <button type="button" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" class="close " data-dismiss="modal">&times;</button>
                      <!-- <h4 class="box-title"><?php echo $this->lang->line('generate') . " " . $this->lang->line('bill'); ?></h4>  -->
                    </div><!--./col-sm-1-->
                </div><!--./row-->

            </div>

            <div class="modal-body pt0 pb0" id="edit_bill_details">
            <?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<input type="hidden" name="store_id" id="store_id" value="<?php echo $result['target_store_id'] ?>">
                                <input type="hidden" name="bill_id" value="<?php echo $result['id'] ?>">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
            <div class="row">
                <?php foreach ($detail as $pkey => $pvalue) {
                    ?>
                    <input type="hidden" name="previous_bill_id[]" value="<?php echo $pvalue['id'] ?>">

                <?php } ?>
                <input name="bill_basic_id" type="hidden" class="form-control" value="<?php echo $result['id']; ?>" />
                <input type="hidden" name="patient_id" id="editbillpatientid">
                <input name="purchase_no" type="hidden" class="form-control" value="<?php echo $result['purchase_no']; ?>" />
               <!--  <input name="date" id="editdate" type="hidden" class="form-control datetime" value="<?php echo date($this->customlib->getSchoolDateFormat(true, true), strtotime($result['date'])); ?>" /> -->


               
               <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>


               

                <script type="text/javascript">
                    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();


    });

                    function get_PatienteditDetails(id) {
                        //$("#patient_name").html("patient_name");
                        //$("#schedule_charge").html("schedule_charge");

                        $.ajax({
                            url: '<?php echo base_url(); ?>hospital/pharmacy/patientDetails',
                            type: "POST",
                            data: {id: id},
                            dataType: 'json',
                            success: function (res) {
                                // console.log(res);
                                if (res) {
                                    //$('#patient_name').val(res.patient_name);
                                    $('#patienteditid').val(res.id);
                                    $('#patienteditname').val(res.patient_name);

                                }
                            }
                        });
                    }

                    function geteditQuantity(id,med_id,batch = '', newrow = '') {
                        if (batch == "") {
                            var batch_no = $('#batch_edit_no' + id).val();
                            //var batch_no = $('#medicine_edit_name' + id).val();
                        } else {
                            var batch_no = batch;
                           // var med_id = med_id
                        }

                         // alert(id);
                        if (batch_no != "") {
                            $('#quantity_edit').val("");
                            $.ajax({
                                type: "GET",
                                url: base_url + "hospital/pharmacy/getQuantityedit",
                                data: {'batch_no': batch_no,'med_id':med_id},
                                dataType: 'json',
                                success: function (data) {
                            //console.log(data);
                                    var requestedQty = $('#totalRequestedqty'+id).html();
                                   console.log('requestedQty',requestedQty);
                                    // if (data.available_quantity < requestedQty) {
                                    //     errorMsg(`Sorry, you have only ${data.available_quantity} available for this medicine`);
                                    //     return;
                                    // }
                                    $('#id' + id).val(data.id);


                                    $('#medicine_batch_id' + id).val(data.id);
                                    $('#sale_price_edit' + id).val(data.purchase_price);

                                    $('#quantity_edit'+id).val('');
                                    $('#totaleditqty' + id).html(data.available_quantity);
                                    $('#available_edit_quantity' + id).val(data.available_quantity);
                                    $('#available_edit_quantity' + id).val(data.available_quantity);

                                    if (newrow != '') {
                                        $('#sale_price_edit' + id).val(data.sale_rate);
                                    }
                                    // $('#sale_price_edit' + id).val(data.sale_price);
                                }
                            });
                    }

                    
                    }


                    
                    function getStoreQuantityedit(id,batch) {
                      var store_id = $('#store_id').val();
                        $.ajax({
                                type: "GET",
                                url: base_url + "hospital/pharmacy/getStoreQuantityedit",
                                data: {'med_id': batch,'store_id':store_id},
                                dataType: 'json',
                                success: function (data) {
                                     $('#totalDepqty'+id).html(data);
                                //     var requestedQty = $('#totalRequestedqty'+id).html();
                                //    console.log('requestedQty',requestedQty);
                                //     // if (data.available_quantity < requestedQty) {
                                //     //     errorMsg(`Sorry, you have only ${data.available_quantity} available for this medicine`);
                                //     //     return;
                                //     // }
                                //     $('#id' + id).val(data.id);


                                //     $('#medicine_batch_id' + id).val(data.id);
                                //     $('#sale_price_edit' + id).val(data.purchase_price);

                                //     $('#quantity_edit'+id).val('');
                                //     $('#totaleditqty' + id).html(data.available_quantity);
                                //     $('#available_edit_quantity' + id).val(data.available_quantity);
                                //     $('#available_edit_quantity' + id).val(data.available_quantity);

                                //     if (newrow != '') {
                                //         $('#sale_price_edit' + id).val(data.sale_rate);
                                //     }
                                    // $('#sale_price_edit' + id).val(data.sale_price);
                                }
                            });

                    
                    }
                    function get_DocEditname(id) {
                        // $("#standard_charge").html("standard_charge");
                        //$("#schedule_charge").html("schedule_charge");

                        $.ajax({
                            url: '<?php echo base_url(); ?>hospital/patient/doctName',
                            type: "POST",
                            data: {doctor: id},
                            dataType: 'json',
                            success: function (res) {
                                //alert(res);
                                if (res) {
                                    $('#docteditname').val(res.name + " " + res.surname);
                                    //$('#surname').val(res.surname);

                                } else {

                                }
                            }
                        });
                    }
                    function geteditExpire(id) {
    var batchDropdown = $("#batch_edit_no" + id);
    var batch_no = batchDropdown.val();
    $('#edit_expire_date' + id).val('');

    // Try to get the current product ID
    var currentProductId = $("#medicine_edit_name" + id).val();

    if (!currentProductId) {
        console.error("Error: Could not retrieve product ID for row", id);
        return;
    }

    // Store selected batch numbers per product
    var productSelectedBatches = {};

    // Iterate over all batch dropdowns and collect selected batch numbers **per product**
    $("select[name='batch_no[]']").each(function () {
        var rowId = $(this).attr("id").replace("batch_edit_no", ""); // Extract row ID
        var productId = $("#medicine_edit_name" + rowId).val(); // Get product ID

        if (!productId) {
            console.warn("Skipping row", rowId, "as product ID is missing");
            return;
        }

        if (!productSelectedBatches[productId]) {
            productSelectedBatches[productId] = [];
        }

        var selectedBatch = $(this).val();
        if (selectedBatch) {
            productSelectedBatches[productId].push(selectedBatch);
        }
    });

    // Debugging Output
    console.log("Collected productSelectedBatches:", productSelectedBatches);

    // Ensure batches are removed only within the same product
    $("select[name='batch_no[]']").each(function () {
        var dropdown = $(this);
        var rowId = dropdown.attr("id").replace("batch_edit_no", ""); // Extract row ID
        var productId = $("#medicine_edit_name" + rowId).val(); // Get product ID

        if (!productId) return;

        dropdown.find("option").each(function () {
            var optionValue = $(this).val();

            if (
                optionValue &&
                productId === currentProductId && // Only affect the same product
                productSelectedBatches[productId].includes(optionValue) &&
                optionValue !== dropdown.val()
            ) {
                $(this).remove();
            }
        });
    });

    // If the selected batch is already used in another row of the same medicine, reset selection
    if (
        productSelectedBatches[currentProductId] &&
        productSelectedBatches[currentProductId].filter(b => b === batch_no).length > 1
    ) {
        alert("This batch is already selected in another row for the same medicine.");
        batchDropdown.val(null).trigger('change'); // Reset Select2 dropdown
        return;
    }

    // Fetch expiry date and quantity for the selected batch
    $.ajax({
        type: "POST",
        url: base_url + "hospital/pharmacy/getExpireDate",
        data: { 'batch_no': batch_no },
        dataType: 'json',
        success: function (data) {
            if (data != null) {
                $('#edit_expire_date' + id).val(data.expiry_date);
                geteditQuantity(id, batch_no);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}




                    function getneweditExpire(id) {
                        var med_id = $("#medicine_edit_name" + id).val();
                        var batch_no = $("#batch_edit_no" + id).val();
                        $('#edit_expire_date' + id).val('');
                        $.ajax({
                            type: "POST",
                            url: base_url + "hospital/pharmacy/getExpiryDate",
                            data: {'batch_no': batch_no,'med_id':med_id},
                            dataType: 'json',
                            success: function (data) {
                                if (data != null) {
                                    $('#edit_expire_date' + id).val(data.expiry_date);
                                    geteditQuantity(id,med_id,batch_no, newrow = 'yes')

                                }
                            }
                        });
                    }

                    function getmedicine_edit_name(id, rowid, selectid = '') {
    // Clear the <select> and display a loading option
    $("#medicine_edit_name" + rowid).html("<option value=''>Loading...</option>");

    // Perform AJAX request
    $.ajax({
        url: '<?php echo base_url(); ?>hospital/pharmacy/get_medicine_name',
        type: "POST",
        data: { medicine_category_id: id },
        dataType: 'json',
        success: function (res) {
            // Build options dynamically
            let div_data = "<option value=''>Select</option>";
            $.each(res, function (i, obj) {
                let sel = obj.id === selectid ? "selected" : "";
                div_data += `<option ${sel} value="${obj.id}">${obj.medicine_name}</option>`;
            });

            // Update the <select> and reinitialize Select2
            $("#medicine_edit_name" + rowid)
                .html(div_data)
                .select2(); // Reinitialize Select2
        },
        error: function () {
            console.error("Error fetching medicine data.");
        }
    });
}


                    function geteditbatchnolist(id, rowid, selectid) {

                        // var batch_no = $("#batch_no"+id).val();
                        //$('#medicine_name'+rowid).select2("val", '');
                        //alert(rowid)
                        var div_data = "";
                        $('#medicine_batch_id' + rowid).val('');
                        $('#quantity_edit').val('');
                        $('#totaleditqty' + rowid).html('');
                        $('#available_edit_quantity' + rowid).val('');
                        //$('#sale_price_edit' + rowid).val('');
                        $('#edit_expire_date' + rowid).val('');
                        $("#batch_edit_no" + rowid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
                        $("#batch_edit_no" + rowid).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                        $.ajax({
                            type: "POST",
                            url: base_url + "hospital/pharmacy/getBatchNoList",
                            data: {'medicine': id},
                            dataType: 'json',
                            success: function (res) {
                               if(res.length > 0){
                                console.log('res',res);
                                   $.each(res, function (i, obj)
                                   {
                                       var sel = "";
                                       if (obj.batch_no == selectid) {
                                           sel = "selected";
                                       }
                                       div_data += "<option " + sel + " value='" + obj.batch_no + "'>" + obj.batch_no + "</option>";
                                   });
                                   // $("#batch_edit_no" + rowid).html("<option value=''>Select</option>");
                                   $('#batch_edit_no' + rowid).append(div_data);
                                   geteditExpire(rowid)
                               }else{
                                $('#edit_expire_date'+rowid).prop('disabled', true);
                                $('#quantity_edit'+rowid).prop('readonly', true);
                                $('#sale_price_edit'+rowid).prop('disabled', true);
                                $('#amount_edit'+rowid).prop('disabled', true);
                                $('#batch_edit_no'+rowid).prop('disabled', true);
                                $('#batch_id'+rowid).prop('disabled', true);
                                $('#medicine_batch_id'+rowid).prop('disabled', true);

                                $('#medicine_category_id'+rowid).prop('disabled', true);
                                $('#pharmacy_id'+rowid).prop('disabled', true);
                                $('#quantity'+rowid).prop('disabled', true);
                                $('#available_edit_quantity'+rowid).prop('disabled', true);
                                $('#bill_detail_id'+rowid).prop('disabled', true);
                                $('#id'+rowid).prop('disabled', true);
                                $('#quantity_edit'+rowid).prop('disabled', true);
                                $('#quantity_edit'+rowid).prop('disabled', true);







                               }
                            }
                        });
                    }
                </script>
   
                
                <div class="col-md-12" style="clear: both;">
                    <div class="table-responsive">
                    <button type="button" style="float: right;" class="btn btn-danger" id="closeRequestBtn">
    Close Request
</button>

                        <table class="custom-table table tableover table-striped table-bordered table-hover tablefull12" id="edittableID">
                            <tr >
                                <th><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?><small class="req" style="color:red;"> *</small></th>
                                <th><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
                                <th><?php echo $this->lang->line('batch') . " " . $this->lang->line('no'); ?><small class="req" style="color:red;"> *</small></th>
                                <th><?php echo $this->lang->line('expire') . " " . $this->lang->line('date'); ?><small class="req" style="color:red;"> *</small></th>
                                <th class="text-right" style="width: 20%;"><?php echo $this->lang->line('quantity'); ?><small class="req" style="color:red;"> *</small> | Requested <?php echo " | " . $this->lang->line('available') . " " . $this->lang->line('qty'); ?>| Requested <?php echo " | Assigned Qty" ; ?></th>
                                <th>Dep Existing Qty<small class="req" style="color:red;"> *</small></th>
            
                                <th class="text-right">Purchase Price<?php '(' . $currency_symbol . ')'; ?><small class="req" style="color:red;"> *</small></th>
								<th class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req" style="color:red;"> *</small></th>
                            </tr>
                            <?php
                            $i = 0;
                            foreach ($detail as $key => $value) {
                                # code...                            
                                ?>
                                <script type="text/javascript">
                                    getmedicine_edit_name('<?php echo $value['medicine_category_id'] ?>', '<?php echo $i ?>', '<?php echo $value['pharmacy_id'] ?>');
                                    geteditbatchnolist('<?php echo $value['pharmacy_id'] ?>', '<?php echo $i ?>', '<?php echo $value['batch_no'] ?>');
                                    geteditQuantity('<?php echo $i ?>','<?php echo $value['pharmacy_id'] ?>','<?php echo $value['batch_no'] ?>');
                                    getStoreQuantityedit('<?php echo $i ?>','<?php echo $value['pharmacy_id'] ?>','<?php echo $value['batch_no'] ?>');

                                </script>
                                <tr id="row<?php echo $i ?>">
                                    <td width="16%"> 
                                        <input type="hidden" name="medicine_category_id[]" id="medicine_category_id<?php echo $i ?>" value="<?php echo $value['medicine_category_id'] ?>">
                                        <input type="hidden" name="pharmacy_id[]" id="pharmacy_id<?php echo $i ?>" value="<?php echo $value['pharmacy_id'] ?>">

                                        <select class="form-control" disabled  name='medicine_category_id[]'  onchange="getmedicine_edit_name(this.value, '<?php echo $i ?>', '<?php echo $value['medicine_name'] ?>')">
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
                                    <td width="24%">
                                        <select class="form-control select2" disabled  style="width: 100%" id="medicine_edit_name<?php echo $i ?>" name='medicine_id[]' onchange="geteditbatchnolist(this.value, '<?php echo $i ?>')">
                                            <option value="<?php echo set_value('medicine_name'); ?>"><?php echo $this->lang->line('select') ?>
                                            </option>  
                                        </select>
                                        <span class="text-danger"><?php echo form_error('medicine_name[]'); ?>
                                    </td>
                                    <td width="16%"> 
                                        <select name="batch_no[]" onchange="geteditExpire(<?php echo $i ?>)" placeholder="Batch No" class="form-control" id="batch_edit_no<?php echo $i ?>">
                                            <option value="<?php echo $value['batch_no']; ?>" selected><?php echo $value['batch_no']; ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                                    </td>
                                    <td width="8%">
                                        <input type="text" readonly="" name="expiry_date[]" id="edit_expire_date<?php echo $i ?>" class="form-control" value="<?php echo $value['expiry_date']; ?>" >
                                        <span class="text-danger"><?php echo form_error('expiry_date[]'); ?>
                                        </span>
                                    </td>

                                    <td class="text-right">
                                      <!--input type="text" name="quantity[]" id="quantity_edit<?php echo $i ?>" placeholder="Quantity" class="form-control" id="quantity_edit" onchange="multiply(<?php echo $i ?>)" onfocus="geteditQuantity('<?php echo $i ?>')" value="<?php echo $value['quantity']; ?>"/>
                                      <span id="totaleditqty<?php echo $i ?>" class="text-danger"><?php echo form_error('quantity[]'); ?></span-->
                                        <div class="input-group">
                                            <input type="hidden" name="batch_id[]" id="batch_id<?php echo $i ?>"  value="<?php echo $value['id']; ?>">
                                            <input type="text" name="quantity[]" onchange="multiply(<?php echo $i ?>)" onfocus="geteditQuantity(<?php echo $i ?>)" value="" id="quantity_edit<?php echo $i ?>" class="form-control text-right">
                                            <span class="input-group-addon text-danger"  id="totalRequestedqty<?php echo $i ?>"><?php echo $value['quantity']; ?></span>
                                            <span class="input-group-addon text-danger"  id="totaleditqty<?php echo $i ?>">&nbsp;&nbsp;</span>
                                            <span class="input-group-addon text-danger"  ><?php echo $value['approved_quantity']; ?></span>

                                        </div>
                                        <input type="hidden" name="available_quantity[]" id="available_edit_quantity<?php echo $i ?>">
                                        <input type="hidden" name="medicine_batch_id[]" id="medicine_batch_id<?php echo $i ?>" >
                                        <input type="hidden" name="bill_detail_id[]" id="bill_detail_id<?php echo $i ?>" value="<?php echo $value["id"] ?>" >
                                    </td>
                                    <td>
                                    <span class="input-group-addon text-danger"  id="totalDepqty<?php echo $i ?>"><?php echo $value['quantity']; ?></span>

                                    </td>
                                    <td class="text-right">
                                        <input type="text" name="purchase_price[]" onchange="multiply(<?php echo $i ?>)" id="sale_price_edit<?php echo $i ?>" value="<?php echo $value['purchase_price']; ?>" placeholder="Purchase Price" class="form-control text-right" />
                                        <span class="text-danger"><?php echo form_error('purchase_price[]'); ?></span>
                                    </td>
                                    <input type="hidden" name="id[]" id="id<?php echo $i ?>">

									
                                    <td class="text-right">
                                        <input type="text" name="amount[]"  id="amount_edit<?php echo $i ?>" placeholder="Amount" class="form-control text-right" value="<?php echo $value['amount']; ?>"/>
                                       
                                        <span class="text-danger"><?php echo form_error('amount[]'); ?></span>
                                    </td>
                                  
                                    <td>
        <button type="button" class="btn btn-sm btn-success" onclick="splitBatch(<?php echo $i ?>)">Split Batch</button>
    </td>
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
                    <div class="col-sm-6">
                        <div class="form-group">   
                            <label><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="note" rows="3" id="note" class="form-control"><?php echo $result["note"] ?></textarea>
                        </div> 
                    </div>  
                    <div class="col-sm-6">

                        <table class="custom-tableprintablea4">
                            <tr>
                                <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                <td width="60%" class="text-right ipdbilltable" colspan="2"><input type="text" placeholder="Total" value="<?php echo $result["total"] ?>" value="0" name="total" id="edittotal"  style="width: 40%; float: right" class="form-control"/></td>
                            </tr>
                           
                            <tr>

                               
                            </tr>
                            <!-- <tr>
                                <th><?php echo $this->lang->line('net_amount') . "(" . $currency_symbol . ")"; ?></th>
                                <td class="text-right ipdbilltable" colspan="2"><input type="text" placeholder="Net Amount" value="<?php echo $result["net_amount"] ?>" name="net_amount" id="editnet_amount" style="width: 39%; float: right" class="form-control"/></td>
                            </tr> -->


                        </table>


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
        <div class="pull-right">
            <input type="button" onclick="addEditTotal()" value="<?php echo $this->lang->line('calculate'); ?>" class="btn btn-info"/>&nbsp;
            <button type="submit" style="display: none" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="editbillsave" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
        </div>
    </div><!--./box-footer-->


</form>      


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>


<script type="text/javascript">
var uniqueIndex = 1000; // Ensure unique IDs

function splitBatch(rowId) {
    var table = document.getElementById("edittableID");

    if (!table) {
        console.error("Table not found!");
        return;
    }

    var originalRow = document.getElementById("row" + rowId);
    var batchSelect = document.getElementById("batch_edit_no" + rowId);

    if (!originalRow || !batchSelect) {
        alert("Original row or batch selection not found!");
        return;
    }

    // Count the number of splits for this row
    var totalBatches = batchSelect.options.length;
    var existingSplits = document.querySelectorAll(`[id^="row${rowId}_split"]`).length;
    var maxAllowedSplits = totalBatches - 2; // Allow splitting until 2 remaining batches

    if (existingSplits >= maxAllowedSplits) {
        alert("No more batches available to split!");
        return;
    }

    uniqueIndex++; // Ensure unique row ID
    var newId = uniqueIndex;

    // Clone the original row
    var newRow = originalRow.cloneNode(true);
    newRow.id = `row${rowId}_split${existingSplits + 1}`;

    // Store the original values of `totalDepqty`, `totalRequestedqty`, and `batch_id`
    var totalDepqtyValue = $(`#totalDepqty${rowId}`).text().trim();
    var totalRequestedqtyValue = $(`#totalRequestedqty${rowId}`).text().trim();
    var pharmacyIdValue = $(`input[name='pharmacy_id[]']`, originalRow).val();
    var medicineCategoryIdValue = $(`input[name='medicine_category_id[]']`, originalRow).val();
    var batchIdValue = $(`input[name='batch_id[]']`, originalRow).val();

    // Reset inputs/selects inside the cloned row while preserving specific values
    $(newRow).find("input, select, span").each(function () {
        var oldId = $(this).attr("id");
        if (oldId) {
            var newElementId = oldId.replace(/\d+$/, newId); // Replace last digits with new ID
            $(this).attr("id", newElementId);
        }
        $(this).val(""); // Clear values for new row (except hidden inputs)
    });

    // **Fix hidden input values for pharmacy, category, and batch ID**
    $(newRow).find("input[name='pharmacy_id[]']").val(pharmacyIdValue);
    $(newRow).find("input[name='medicine_category_id[]']").val(medicineCategoryIdValue);
    $(newRow).find("input[name='batch_id[]']").val(batchIdValue);

    // **Fix index updates for dynamic elements**
    $(newRow).find("[id^='totalRequestedqty'], [id^='totaleditqty']").each(function () {
        var oldId = $(this).attr("id");
        if (oldId) {
            var newElementId = oldId.replace(/\d+$/, newId);
            $(this).attr("id", newElementId);
        }
        $(this).html(""); // Reset values (except `totalDepqty` and `totalRequestedqty`)
    });

    // **Fix `totalDepqty` so it retains its original value**
    $(newRow).find("[id^='totalDepqty']").each(function () {
        var oldId = $(this).attr("id");
        if (oldId) {
            var newElementId = oldId.replace(/\d+$/, newId);
            $(this).attr("id", newElementId);
        }
        $(this).html(totalDepqtyValue); // Preserve original value
    });

    // **Fix `totalRequestedqty` so it retains its original value**
    $(newRow).find("[id^='totalRequestedqty']").each(function () {
        var oldId = $(this).attr("id");
        if (oldId) {
            var newElementId = oldId.replace(/\d+$/, newId);
            $(this).attr("id", newElementId);
        }
        $(this).html(totalRequestedqtyValue); // Preserve original value
    });

    // Replace IDs inside `innerHTML`
    newRow.innerHTML = newRow.innerHTML
        .replace(/medicine_edit_name\d+/g, "medicine_edit_name" + newId)
        .replace(/batch_edit_no\d+/g, "batch_edit_no" + newId)
        .replace(/edit_expire_date\d+/g, "edit_expire_date" + newId)
        .replace(/quantity_edit\d+/g, "quantity_edit" + newId)
        .replace(/available_edit_quantity\d+/g, "available_edit_quantity" + newId)
        .replace(/medicine_batch_id\d+/g, "medicine_batch_id" + newId)
        .replace(/sale_price_edit\d+/g, "sale_price_edit" + newId)
        .replace(/amount_edit\d+/g, "amount_edit" + newId)
        .replace(/totalRequestedqty\d+/g, "totalRequestedqty" + newId)
        .replace(/totaleditqty\d+/g, "totaleditqty" + newId)
        .replace(/totalDepqty\d+/g, "totalDepqty" + newId)
        .replace(/id\d+/g, "id" + newId)
        .replace(/splitBatch\(\d+\)/g, `splitBatch(${newId})`) // Update split button action
        .replace(/geteditbatchnolist\(\d+,/g, `geteditbatchnolist(${newId},`)
        .replace(/geteditQuantity\(\d+\)/g, `geteditQuantity(${newId})`)
        .replace(/multiply\(\d+\)/g, `multiply(${newId})`)
        .replace(/geteditExpire\(\d+\)/g, `geteditExpire(${newId})`);

    // Insert the new row right below the original row
    originalRow.parentNode.insertBefore(newRow, originalRow.nextSibling);

    // Reinitialize Select2 dropdowns
    $('#batch_edit_no' + newId).select2();

    // Ensure alignment remains consistent
    $(newRow).find("td").css("vertical-align", "middle");

    console.log("New row added with ID:", newId, 
                "TotalDepQty retained:", totalDepqtyValue, 
                "TotalRequestedQty retained:", totalRequestedqtyValue,
                "Pharmacy ID retained:", pharmacyIdValue,
                "Medicine Category ID retained:", medicineCategoryIdValue,
                "Batch ID retained:", batchIdValue);
}






    function multiply(id) {

       var quantity = $('#quantity_edit' + id).val();
        var discount = $('#discount_single' + id).val();
        var availquantity = $('#available_edit_quantity' + id).val();
        var available = $('#totaleditqty'+id).html();
        if ( parseInt(available) <  parseInt(quantity)) {
            errorMsg(`Sorry, you dont have enough quantity`);
            $('#quantity_edit' + id).val('');
            return;
        }

        if (parseInt(quantity) > parseInt(availquantity)) {
            errorMsg('Order quantity should not be greater than available quantity');
        } else {
            //alert(parseInt(quantity));
        }
        var sale_price = $('#sale_price_edit' + id).val();
        var amount = quantity * sale_price;
        $('#amount_edit' + id).val(amount);
        if(parseInt(discount) > 0 && (parseInt(quantity) <= parseInt(availquantity))){

            var total_amt=amount - (amount * parseInt(discount)/100);
            $('#amount_edit' + id).val('');
            $('#amount_edit' + id).val(total_amt);

        }
    }

   
    var expire_date = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY',]) ?>';
    $('.expires_date').datepicker({
        format: "M/yyyy",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    });

    function editMore() {
        var table = document.getElementById("edittableID");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var div = "<td><select class='form-control' name='medicine_category_id[]' onchange='getmedicine_edit_name(this.value," + id + ")'><option value='<?php echo set_value('medicine_category_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineCategory as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"] ?></option><?php } ?></select></td><td><select class='form-control select2' name='medicine_name[]' onchange='geteditbatchnolist(this.value," + id + ")' id='medicine_edit_name" + id + "' ><option value='<?php echo set_value('medicine_name'); ?>'><?php echo $this->lang->line('select') ?></option></select></td><td><select name='batch_no[]' onchange='getneweditExpire(" + id + ")' id='batch_edit_no" + id + "'  class='form-control'><option></option></select></td><td><input type='text' id='edit_expire_date" + id + "' readonly name='expire_date[]' class='form-control'></td><td><div class='input-group'><input type='text' name='quantity[]' onchange='multiply(" + id + ")' onfocus='geteditQuantity(" + id + ")' id='quantity_edit" + id + "' class='form-control text-right'><span class='input-group-addon text-danger'  id='totaleditqty" + id + "'>&nbsp;&nbsp;</span></div><input type='hidden' name='available_quantity[]' id='available_edit_quantity" + id + "'><input type=hidden class=form-control value='0' name='bill_detail_id[]'  /><input type='hidden' name='medicine_batch_id[]' id='medicine_batch_id" + id + "'></td><td> <input type='text' name='sale_price[]' onchange='multiply(" + id + ")' id='sale_price_edit" + id + "'  class='form-control text-right'></td><td><input type='text' name='discount_single[]' onkeyup='multiply(" + id + ")'  id='discount_single" + id + "'  class='form-control text-right'></td><td><input type='text' name='amount[]' id='amount_edit" + id + "'  class='form-control text-right'></td>";

        var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "<td><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";

        var expire_date = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY',]) ?>';
        $('.expires_date').datepicker({
            format: "M/yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true,
        });
        $('.select2').select2();
    }
    function addEditTotal() {
        var total = 0;
        var sale_price = document.getElementsByName('amount[]');
        for (var i = 0; i < sale_price.length; i++) {
            var inp = sale_price[i];
            if (inp.value == '') {
                var inpvalue = 0;
            } else {
                var inpvalue = inp.value;
            }
            total += parseFloat(inpvalue);
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
        var patient_id = $("#addeditpatient_id").val();
        $("#editbillpatientid").val(patient_id);
        var editdate = $("#editdate").val();
        $("#editbilldate").val(editdate);
        $("#editbillsave").show();
    }

    function delete_row(id) {
        var table = document.getElementById("edittableID");
        var rowCount = table.rows.length;
        $("#row" + id).html("");
    }

    $(document).ready(function (e) {
        $("#editbill").on('submit', (function (e) {
            if (!confirm("Are you sure you want to save?")) {
                return; // Stop submission if the user clicks "Cancel"
            }
            $("#editbillsave").button('loading');

            /* var patient_id = $("#addeditpatient_id").val();
             $("#editbillpatientid").val(patient_id);
             var editdate = $("#editdate").val();
             $("#editbilldate").val(editdate);*/
            // console.log(editdate);
            var table = document.getElementById("edittableID");
            var rowCount = table.rows.length;

            for (var k = 0; k < rowCount; k++) {
                var quantityk = $('#quantity_edit' + k).val();
                var availquantityk = $('#available_edit_quantity' + k).val();
                if (parseInt(quantityk) > parseInt(availquantityk)) {
                    errorMsg('Order quantity should not be greater than available quantity');

                    return false;
                } else {
                }
            }
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/store/assignMedicineToStore',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                        window.location.href = "<?php echo base_url(); ?>hospital/store/requests";
                    }

                },
                error: function () {}
            });
            $("#editbillsave").button('reset');
        }));
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


            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                        <a href="#"  data-target="#edit_prescription"  data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>

                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="box-title"><?php echo $this->lang->line('bill') . " " . $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
     
    </section>

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
    $(document).ready(function () {
        // Show modal when Close Request button is clicked
        $("#closeRequestBtn").click(function () {
            $("#closeRequestModal").modal("show");
        });

        // Handle form submission
        $("#closeRequestForm").submit(function (e) {
            e.preventDefault();
            
            var formData = $(this).serialize(); // Get form data
            
            $.ajax({
                url: "<?php echo base_url('hospital/pharmacy/closeRequest'); ?>", // Replace with your actual backend URL
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        successMsg('Request closed successfully');

                        $("#closeRequestModal").modal("hide");
                        window.location.href = "<?php echo base_url(); ?>hospital/store/requests";
                    } else {
                        alert("Error closing request: " + response.message);
                    }
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });

</script>