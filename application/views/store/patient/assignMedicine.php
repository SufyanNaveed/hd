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

                <div class="" id="myModal" aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
                    <h3 class="text-center">Issue Medicine</h3>
                    <div class="modal-dialog pup100" role="document">
                        <div class="modal-content modal-media-content">
                            <form id="bill" accept-charset="utf-8" method="post" class="ptt10">
                                <div class="modal-header modal-media-header">
                                    <input type="hidden" name="patient_id" value="<?php echo $patient_id ?>">
                                    <input type="hidden" name="visit_id" value="<?php echo $visit_id ?>">


                                    <button type="button" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" class="close closemobile" data-dismiss="modal">&times;</button>
                                    <div class="row modalbillform">
                                        <div class="col-lg-5 col-sm-6">
                                            <div class="row">
                                                <?php if($this->session->userdata()['hospital']['role'] == "Department Pharmacist"){ ?>
                                                    <div class="row">
                                                        <div class="col-lg-3 col-sm-3 col-xs-3">
                                                            <label><?php echo 'Deparment(s)'; ?><small class="req" style="color:red;"> *</small></label>
                                                        </div>
                                                        <div class="col-sm-8 col-xs-6">
                                                            <select class="form-control select2" name="department_id" id="department_id">
                                                                <option value=""><?php echo "Select Department"; ?></option>
                                                                <?php foreach ($departments as $department) { ?>
                                                                    <option value="<?php echo $department['id']; ?>" <?php echo count($departments) == 1 ? "selected='selected'" : ""; ?>><?php echo $department['department_name']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <span class="text-danger"><?php echo form_error('department_id'); ?></span>
                                                        </div><!--./col-sm-3-->
                                                        <div class="col-sm-3 col-xs-5">
                                                        </div><!--./col-sm-3-->
                                                    </div><!--./row-->
                                                <?php } ?> 
                                            </div><!--./row-->
                                        </div><!--./col-sm-6-->
                                        <div class="col-lg-6 col-sm-5">
                                            <div class="row">
                                                <div class="col-lg-2 col-sm-3 col-xs-3">
                                                    <label><?php echo $this->lang->line('bill') . " " . $this->lang->line('no'); ?></label><!-- <small class="req" style="color:red;"> *</small> -->
                                                </div>

                                                <div class="mdclear"></div>
                                                <div class="col-lg-2 col-sm-2 col-xs-3">
                                                    <label><?php echo $this->lang->line('date'); ?></label>
                                                </div>
                                                <div class="col-lg-5 col-sm-4 col-xs-9">

                                                    <input name="date" type="datetime-local" value="<?php echo date('Y-m-d\TH:i'); ?>" class="form-control" />
                                                </div>
                                            </div><!--./row-->
                                        </div><!--./col-sm-6-->

                                        <div class="col-sm-1 pull-right" style="display: none">
                                            <button type="button" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" class="close " data-dismiss="modal">&times;</button>
                                            <!-- <h4 class="box-title"><?php echo $this->lang->line('generate') . " " . $this->lang->line('bill'); ?></h4>  -->
                                        </div><!--./col-sm-1-->
                                    </div><!--./row-->
                                </div><!--./modal-header-->
                                <div class="modal-body pt0 pb0">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <input name="patient_name" id="patient_name" type="hidden" class="form-control" />
                                            <input name="bill_no" id="billnoform" type="hidden" class="form-control" />
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="custom-table table table table tableover table-striped table-bordered table-hover tablefull12" id="tableID">
                                                            <thead>
                                                                <tr class="font13">
                                                                    <th width="13%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?><small class="req" style="color:red;"> *</small></th>
                                                                    <th width="11%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
                                                                    <th width="8%"><?php echo $this->lang->line('batch') . " " . $this->lang->line('no'); ?> <small class="req" style="color:red;">*</small></th>
                                                                    <th width="9%"><?php echo $this->lang->line('expire') . " " . $this->lang->line('date'); ?><small class="req" style="color:red;"> *</small></th>
                                                                    <th class="text-right" width="15%"><?php echo $this->lang->line('quantity'); ?><small class="req" style="color:red;"> *</small> <?php echo " | " . $this->lang->line('available') . " " . $this->lang->line('qty'); ?></th>
                                                                    <th class="text-right" width="12%">Purchase Price <small class="req" style="color:red;"> *</small></th>
                                                                    <th class="text-right" width="9%"><?php echo $this->lang->line('amount'); ?><small class="req" style="color:red;"> *</small></th>
                                                                    <th class="text-right">Dosage</th>
                                                                    <th class="text-right">Instruction</th>
                                                                </tr>
                                                            </thead>
                                                            <tr id="row0">
                                                                <td width="160">
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
                                                                <td width="24%">
                                                                    <select class="form-control select2" style="width:100%" onchange="getbatchnolist(this.value, 0)" id="medicine_name0" name='medicine_name[]'>
                                                                        <option value=""><?php echo $this->lang->line('select') ?>
                                                                        </option>
                                                                    </select>
                                                                    <span class="text-danger"><?php echo form_error('medicine_name[]'); ?></span>

                                                                </td>
                                                                <td width="16%">
                                                                    <!-- <input type="text" name="batch_no[]" onchange="getExpire(0)" placeholder="" class="form-control" id="batch_no0" > -->
                                                                    <select class="form-control" id="batch_no0" name="batch_no[]" onchange="getExpire(0)">
                                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                    </select>
                                                                    <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                                                                </td>
                                                                <td width="8%">
                                                                    <input type="text" readonly="" name="expire_date[]" id="expire_date0" class="form-control">

                                                                </td>

                                                                <td>
                                                                    <!--  <input type="text" name="quantity[]" placeholder="" class="form-control text-right" id="quantity0" onchange="multiply(0)" onfocus="getQuantity(0)">
                                                  <span id="totalqty0" class="text-danger"><?php echo form_error('quantity[]'); ?></span> -->
                                                                    <div class="input-group">
                                                                        <input type="text" name="quantity[]" onchange="multiply(0)" onfocus="getQuantity(0)" id="quantity0" class="form-control text-right">
                                                                        <span class="input-group-addon text-danger" style="font-size: 10pt" id="totalqty0">&nbsp;&nbsp;</span>
                                                                    </div>
                                                                    <input type="hidden" name="available_quantity[]" id="available_quantity0">
                                                                    <input type="hidden" name="id[]" id="id0">
                                                                </td>
                                                                <td class="text-right">

                                                                    <input type="text" readonly name="purchase_price[]" onchange="multiply(0)" id="sale_price0" placeholder="" class="form-control text-right">
                                                                    <span class="text-danger"><?php echo form_error('purchase_price[]'); ?></span>
                                                                </td>

                                                                <td class="text-right">
                                                                    <input type="text" name="amount[]" readonly id="amount0" placeholder="" class="form-control text-right">
                                                                    <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control select2" style="width:100%" id="dosage_name0" name='dosage[]'>
                                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                                        <?php foreach ($dosage as $d) { ?>
                                                                            <option value="<?php echo $d['id']; ?>"><?php echo $d['dosage']; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control select2" style="width:100%" id="instruction0" name='instruction[]'>
                                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                                        <?php foreach ($instruction as $inst) { ?>
                                                                            <option value="<?php echo $inst['id']; ?>"><?php echo $inst['instruction']; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>

                                                                <td><button type="button" onclick="addMore()" style="color: #2196f3" class="closebtn"><i class="fa fa-plus"></i></button></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="divider"></div>
                                                    <!--    <div class="col-sm-8">
                                     <div class="form-group">
                                       <input type="button" onclick="addTotal()" value="Calculate" class="btn btn-info pull-right"/>
                                     </div>
                                   </div> -->
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="row">
                                                                <div class="col-sm-12">

                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label><?php echo $this->lang->line('note'); ?></label>
                                                                        <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!--./col-sm-6-->


                                                        <div class="col-sm-6">
                                                            <table class="custom-table tableprintablea4">
                                                                <tr>
                                                                    <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                                                    <td width="60%" colspan="2" class="text-right ipdbilltable"><input type="text" placeholder="Total" value="0" name="total" id="total" style="width: 30%; float: right" class="form-control" /></td>
                                                                </tr>







                                                            </table>


                                                        </div>

                                                    </div><!--./row-->
                                                </div><!--./col-md-12-->


                                            </div><!--./row-->
                                        </div><!--./box-footer-->
                                    </div><!--./col-md-12-->
                                </div><!--./row-->

                                <div class="box-footer" style="clear: both;">
                                    <div class="pull-left">
                                        <button type="button" onclick="addMore()" style="color: #ffff" class="closebtn btn btn-info text-white"><i class="fa fa-plus"></i>Add More</button>

                                    </div>
                                    <div class="pull-right">
                                        <input type="button" onclick="addTotal()" value="<?php echo $this->lang->line('calculate'); ?>" class="btn btn-info" />&nbsp;
                                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" style="display: none" id="billsave" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                        </button>
                                    </div>

                                </div>
                            </form>

                        </div><!--./modal-body-->


                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    <script type="text/javascript">
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

        });

        function edit_bill(id, bill_no, patient_id) {
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/pharmacy/getdate',
                type: "POST",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(true, true), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy', 'H' => 'hh', 'i' => 'mm']) ?>';

                    //  alert(data.date);
                    // alert($this->customlib->getSchoolDateFormat(true, true));
                    // var newformat = hours >= 12 ? 'PM' : 'AM';, 'i' => 'mm'
                    var date = new Date(data.date).toString(date_format);
                    //var hours = date.getHours();
                    //alert(date);
                    $('#editdate').val(data.date);
                    $("#editbillno").val(bill_no);
                    $("#addeditpatient_id").val(patient_id);
                    $.ajax({
                        url: '<?php echo base_url(); ?>hospital/pharmacy/editPharmacyBill/' + id,
                        success: function(res) {
                            $('#viewModal').modal('hide');
                            $("#edit_bill_details").html(res);
                            holdModal('edit_bill');
                        },
                        error: function() {
                            alert("Fail")
                        }
                    });

                }

            });

        }

        // function edit_bill(id, bill_no, patient_id) {
        //     // var billno = bill_no;
        //     // console.log(billno);
        //     $.ajax({
        //         url: '<?php echo base_url(); ?>admin/pharmacy/editPharmacyBill/' + id,
        //         success: function (res) {
        //         // var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(true, false), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy']) ?>';
        //         // ///new Date().toLocaleTimeString();
        //         // var date = new Date(data.date).toString(date_format);
        //             //$('#editdate').val(date);
        //             $('#viewModal').modal('hide');
        //             $("#editbillno").val(bill_no);
        //             $("#addeditpatient_id").val(patient_id);
        //             // $("#editdate").val(date);
        //             $("#edit_bill_details").html(res);

        //             holdModal('edit_bill');
        //         },
        //         error: function () {
        //             alert("Fail")
        //         }
        //     });
        // }

        function get_StoreDetails(id) {
            //$("#patient_name").html("patient_name");
            //$("#schedule_charge").html("schedule_charge");

            $.ajax({
                url: '<?php echo base_url(); ?>hospital/pharmacy/storeDetails',
                type: "POST",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
                    if (res) {
                        $('#store_name').val(res.store_name);
                        $('#store_id').val(res.id);
                    } else {
                        $('#store_name').val('Null');

                    }
                }
            });
        }

        function get_PatienteditDetails(id) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pharmacy/patientDetails',
                type: "POST",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
                    if (res) {
                        //$('#patient_name').val(res.patient_name);
                        $('#patienteditid').val(res.id);
                        $('#patienteditname').val(res.patient_name);
                        // console.log(res.patient_name)

                    }
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

        $(document).ready(function(e) {

            $(".printsavebtn").on('click', (function(e) {
                $('#billsave').hide();
                $('.printsavebtn').prop('disabled', true);
                var form = $(this).parents('form').attr('id');
                var str = $("#" + form).serializeArray();
                var postData = new FormData();
                $.each(str, function(i, val) {
                    postData.append(val.name, val.value);
                });
                //  $("#"+form).submit();

                $("#billsave").button('loading');
                e.preventDefault();
                $.ajax({
                    url: '<?php echo base_url(); ?>hospital/patient/addBill',
                    type: "POST",
                    data: postData,
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
                            $('.printsavebtn').prop('disabled', false);
                            $('#billsave').show();
                        } else {

                            successMsg(data.message);
                            printData(data.insert_id);

                            // window.location.reload(true);
                        }
                        $("#billsave").button('reset');
                    },
                    error: function() {
                        //  alert("Fail")
                    }
                });


            }));
        });

        function printData(insert_id, id) {
            // alert(insert_id);

            var base_url = '<?php echo base_url() ?>';
            $.ajax({
                url: base_url + 'admin/pharmacy/getBillDetails/' + insert_id,
                type: 'POST',
                data: {
                    id: insert_id,
                    print: 'yes'
                },
                success: function(result) {
                    // $("#testdata").html(result);
                    popup(result);
                }
            });
        }

        function popup(data) {
            var base_url = '<?php echo base_url() ?>';
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
            frameDoc.document.write('<body >');
            frameDoc.document.write(data);
            frameDoc.document.write('</body>');
            frameDoc.document.write('</html>');
            frameDoc.document.close();
            setTimeout(function() {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                frame1.remove();
                window.location.reload(true);
            }, 500);


            return true;
        }


        function addMore() {
            var table = document.getElementById("tableID");
            var table_len = (table.rows.length);
            var id = parseInt(table_len - 1);
            var lastCategory = $("select[name='medicine_category_id[]']").last().val();

            var div = "<td><select class='form-control' name='medicine_category_id[]' onchange='getmedicine_name(this.value," + id + ")'><option value=''><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineCategory as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"] ?></option><?php } ?></select></td><td><select class='form-control select2' style='width:100%' name='medicine_name[]' onchange='getbatchnolist(this.value," + id + ")' id='medicine_name" + id + "' ><option value=''><?php echo $this->lang->line('select') ?></option></select></td><td><select name='batch_no[]' id='batch_no" + id + "' onchange='getExpire(" + id + ")' class='form-control'><option value=''><?php echo $this->lang->line('select') ?></option></select></td><td><input type='text' name='expire_date[]' readonly id='expire_date" + id + "' class='form-control'></td><td><div class='input-group'><input type='text' name='quantity[]' onchange='multiply(" + id + ")' onfocus='getQuantity(" + id + ")' id='quantity" + id + "' class='form-control text-right'><span class='input-group-addon text-danger' style='font-size:10pt'  id='totalqty" + id + "'>&nbsp;&nbsp;</span></div><input type='hidden' name='available_quantity[]' id='available_quantity" + id + "'><input type='hidden' name='id[]' id='id" + id + "'></td><td> <input type='text' onchange='multiply(" + id + ")' readonly name='purchase_price[]' id='sale_price" + id + "'  class='form-control text-right'></td><td><input type='text' name='amount[]' readonly id='amount" + id + "'  class='form-control text-right'></td>";

            // Add Dosage Dropdown
            div += "<td><select class='form-control select2' style='width:100%' name='dosage[]' id='dosage_name" + id + "'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($dosage as $d) { ?><option value='<?php echo $d['id']; ?>'><?php echo $d['dosage']; ?></option><?php } ?></select></td>";

            // Add Instruction Dropdown
            div += "<td><select class='form-control select2' style='width:100%' name='instruction[]' id='instruction" + id + "'><option value=''><?php echo $this->lang->line('select'); ?></option><?php foreach ($instruction as $inst) { ?><option value='<?php echo $inst['id']; ?>'><?php echo $inst['instruction']; ?></option><?php } ?></select></td>";

            // Add Delete Button
            div += "<td><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td>";

            var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "</tr>";

            // Re-initialize Select2 for new elements
            $('.select2').select2();

            var expire_date = '<?php echo strtr($this->customlib->getSchoolDateFormat(), ["d" => "DD", "m" => "MM", "Y" => "YYYY"]); ?>';
            $('.expire_date').datepicker({
                format: "M/yyyy",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true
            });

            generateBillNo();

            // Set the last selected category to the newly added row
            var newCategorySelect = document.querySelector("tr#row" + id + " select[name='medicine_category_id[]']");
            if (lastCategory) {
                newCategorySelect.value = lastCategory;
                getmedicine_name(lastCategory, id); // Fetch medicine list for the selected category
            }
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
                total += parseFloat(inpvalue);
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
            var editdate = $("#date_pharmacy").val();
            $("#date_result").val(editdate);
            $("#billsave").show();
            $(".printsavebtn").show();
        }

        function delete_row(id) {
            var table = document.getElementById("tableID");
            var rowCount = table.rows.length;
            $("#row" + id).remove();
        }


        $(document).ready(function(e) {
            $("#bill").on('submit', (function(e) {
                e.preventDefault();
                var btn = $("#billsave");
                btn.button('loading');
                var table = document.getElementById("tableID");
                var rowCount = table.rows.length;

                for (var k = 0; k < rowCount; k++) {
                    var quantityk = $('#quantity' + k).val();
                    var availquantityk = $('#available_quantity' + k).val();
                    if (parseInt(quantityk) > parseInt(availquantityk)) {
                        errorMsg('Order quantity should not be greater than available quantity');
                        return false;
                    } else {}
                }
                $.ajax({
                    url: '<?php echo base_url(); ?>hospital/patient/addBill',
                    type: "POST",
                    data: new FormData(this),
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
                            window.location.reload(true);
                            window.history.back(); // Redirect back to the previous page
                        }
                        $("#billsave").button('reset');
                    },
                    error: function() {}
                });

            }));
        });


        function viewDetail(id) {

            $.ajax({
                url: '<?php echo base_url() ?>hospital/pharmacy/getBillDetails/' + id,
                type: "GET",
                data: {
                    id: id
                },
                success: function(data) {
                    $('#reportdata').html(data);
                    holdModal('viewModal');
                },
            });
        }

        function getQuantity(id) {
            var batch_no = $('#batch_no' + id).val();
            var selectedBatchId = $('#batch_no' + id).find(':selected').data('batch-id');
            console.log('Selected Batch ID:', selectedBatchId);

            var medicine = $("#medicine_name" + id).val();
            if (batch_no != "") {
                $('#quantity').html("");
                $.ajax({
                    type: "GET",
                    url: base_url + "hospital/pharmacy/getDepPharQuantity",
                    data: {
                        'batch_no': batch_no,
                        'med_id': medicine,
                        'batch_id': selectedBatchId

                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#id' + id).val(data.id);
                        $('#totalqty' + id).html(data.available_quantity);
                        $('#available_quantity' + id).val(data.available_quantity);
                        $('#sale_price' + id).val(data.purchase_price);
                    }
                });
            }
        }

        function getExpire(id) {
            var medicine = $("#medicine_name" + id).val();
            var batch_no = $("#batch_no" + id).val();
            $.ajax({
                type: "POST",
                url: base_url + "hospital/pharmacy/getExpiryDate",
                data: {
                    'batch_no': batch_no,
                    'med_id': medicine
                },
                dataType: 'json',
                success: function(res) {
                    if (res != null) {
                        $('#expire_date' + id).val(res.expiry_date);
                        getQuantity(id);
                    }
                }
            });
        }

        let selectedBatchIdsByMedicine = {}; // Track selected batch IDs per medicine

        function getbatchnolist(medicineId, rowid) {
            var div_data = "";
            $('#totalqty' + rowid).html("");
            $('#available_quantity' + rowid).val('');
            $('#sale_price' + rowid).val('');
            $('#expire_date' + rowid).val('');
            $('#amount' + rowid).val('');
            $('#quantity' + rowid).val('');
            $("#batch_no" + rowid).html("<option value=''><?php echo $this->lang->line('select') ?></option>");

            // Ensure batch tracking exists for this medicine
            if (!selectedBatchIdsByMedicine[medicineId]) {
                selectedBatchIdsByMedicine[medicineId] = [];
            }

            // Update selected batch IDs for this medicine only
            selectedBatchIdsByMedicine[medicineId] = [];
            $('select[name="batch_no[]"]').each(function() {
                var currentMedicine = $(this).closest('tr').find('select[name="medicine_name[]"]').val();
                var selectedBatchId = $(this).find(":selected").data("batch-id"); // Get batch ID from data attribute

                if (currentMedicine === medicineId && selectedBatchId) {
                    selectedBatchIdsByMedicine[medicineId].push(selectedBatchId);
                }
            });
            console.log('selectedBatchIdsByMedicine', selectedBatchIdsByMedicine)

            $.ajax({
                type: "POST",
                url: base_url + "hospital/pharmacy/getDepartmentPharmasistBatchNoList",
                data: {
                    'medicine': medicineId
                },
                dataType: 'json',
                success: function(res) {
                    console.log('res', res)
                    $.each(res, function(i, obj) {

                        // Exclude batch IDs already selected for the same medicine
                        if (!selectedBatchIdsByMedicine[medicineId].includes(obj.batch_id)) {
                            let label = '';
                            if (obj.request_type === 'transfer' && obj.parent_request_id !== null && obj.parent_request_id !== undefined) {
                                label = '(Requested)';
                            } else if (!obj.request_type && !obj.transfer_store_id) {
                                label = '(Opening)';
                            } else if (!obj.request_type && (obj.parent_request_id === null || obj.parent_request_id === undefined)) {
                                label = '(Transferred)';
                            }

                            div_data += `<option value="${obj.batch_no}" data-batch-id="${obj.id}">${obj.batch_no} ${label}</option>`;
                        }
                    });

                    $("#batch_no" + rowid).html("<option value=''>Select</option>");
                    $('#batch_no' + rowid).append(div_data);
                }
            });
        }



        function get_Docname(id) {
            $("#standard_charge").html("standard_charge");
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/patient/doctName',
                type: "POST",
                data: {
                    doctor: id
                },
                dataType: 'json',
                success: function(res) {
                    if (res) {
                        $('#doctname').val(res.name + " " + res.surname);
                    } else {

                    }
                }
            });
        }

        function multiply(id) {

            var quantity = $('#quantity' + id).val();
            var discount = $('#discount_single' + id).val();
            var availquantity = $('#available_quantity' + id).val();
            if (parseInt(quantity) > parseInt(availquantity)) {
                errorMsg('Order quantity should not be greater than available quantity');
            } else {
                //alert(parseInt(quantity));
            }

            var sale_price = $('#sale_price' + id).val();
            var amount = quantity * sale_price;
            $('#amount' + id).val(amount);
            if (parseInt(discount) > 0 && (parseInt(quantity) <= parseInt(availquantity))) {
                var total_amt = amount - (amount * parseInt(discount) / 100);
                $('#amount' + id).val('');
                $('#amount' + id).val(total_amt);
            }






        }

        function generateBillNo() {
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/pharmacy/getBillNo',
                type: "POST",
                dataType: 'json',
                data: {
                    id: 1
                },
                success: function(data) {
                    $('#billno').val(data);
                    $('#billnoform').val(data);
                }
            });

        }

        /* function getPatientIdName(opd_ipd_no) {
         //var opd_ipd_patient_type = $('select[name=customer_type]:selected').val();
         //alert(opd_ipd_patient_type);
         //alert($("#customer_type").val());
         $('#patient_id').val("");
         $('#patient_name').val("");
         var opd_ipd_patient_type = $("#customer_type").val();
         $.ajax({
         url: '<?php echo base_url(); ?>admin/patient/getPatientType',
         type: "POST",
         data: {opd_ipd_patient_type: opd_ipd_patient_type, opd_ipd_no: opd_ipd_no},
         dataType: 'json',
         success: function (data) {
         $('#patient_id').val(data.patient_id);
         $('#patient_name').val(data.patient_name);
         $('#doctor_name').val(data.doctorname + ' ' + data.surname);
         }
         });
         }*/
        // function add_instruction(id){
        //     $('#ins_patient_id').val(id);
        // }

        function holdModal(modalId) {
            $('#' + modalId).modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
            //$('#tableID').html('');
            /* var table = document.getElementById("tableID");
             var table_len = (table.rows.length);
             var id = parseInt(table_len - 1);
             var div = "<td><select class='form-control' name='medicine_category_id[]' onchange='getmedicine_name(this.value," + id + ")'><option value='<?php echo set_value('medicine_category_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineCategory as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"] ?></option><?php } ?></select></td><td><select class='form-control select2' style='width:100%' name='medicine_name[]' onchange='getbatchnolist(this.value," + id + ")' id='medicine_name" + id + "' ><option value='<?php echo set_value('medicine_name'); ?>'><?php echo $this->lang->line('select') ?></option></select></td><td><select name='batch_no[]' id='batch_no" + id + "' onchange='getExpire(" + id + ")' class='form-control'><option value='<?php echo set_value('batch_no'); ?>'><?php echo $this->lang->line('select') ?></option></select></td><td><input type='text' name='expire_date[]' readonly id='expire_date" + id + "' class='form-control expire_date'></td><td><div class='input-group'><input type='text' name='quantity[]' onchange='multiply(" + id + ")' onfocus='getQuantity(" + id + ")' id='quantity" + id + "' class='form-control text-right'><span class='input-group-addon text-danger' style='font-size:10pt'  id='totalqty" + id + "'>&nbsp;&nbsp;</span></div><input type='hidden' name='available_quantity[]' id='available_quantity" + id + "'><input type='hidden' name='id[]' id='id" + id + "'></td><td> <input type='text' onchange='multiply(" + id + ")' name='sale_price[]' id='sale_price" + id + "'  class='form-control text-right'></td><td><input type='text' name='amount[]' readonly id='amount" + id + "'  class='form-control text-right'></td>";

             var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "<td><button type='button' onclick='addMore()'style='color: #2196f3' class='closebtn'><i class='fa fa-plus'></i></button></td></tr>"; */
            $('.select2').select2();

            var expire_date = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
            $('.expire_date').datepicker({
                format: "m/yyyy",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true
            });
            generateBillNo()
        }
    </script>
    <script type="text/javascript">
        $(function() {
            $('#easySelectable').easySelectable();
            //stopPropagation();
        })
    </script>
    <script type="text/javascript">
        /*
             Author: mee4dy@gmail.com
             */
        (function($) {
            //selectable html elements
            $.fn.easySelectable = function(options) {
                var el = $(this);
                var options = $.extend({
                    'item': 'li',
                    'state': true,
                    onSelecting: function(el) {},
                    onSelected: function(el) {},
                    onUnSelected: function(el) {}
                }, options);
                el.on('dragstart', function(event) {
                    event.preventDefault();
                });
                el.off('mouseover');
                el.addClass('easySelectable');
                if (options.state) {
                    el.find(options.item).addClass('es-selectable');
                    el.on('mousedown', options.item, function(e) {
                        $(this).trigger('start_select');
                        var offset = $(this).offset();
                        var hasClass = $(this).hasClass('es-selected');
                        var prev_el = false;
                        el.on('mouseover', options.item, function(e) {
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
                    $(document).on('mouseup', function() {
                        el.off('mouseover');
                    });
                } else {
                    el.off('mousedown');
                }
            };
        })(jQuery);

        function showtextbox(value) {
            if (value != 'direct') {
                $("#opd_ipd_no").show();
            } else {
                $("#opd_ipd_no").hide();
            }
        }


        $(".generatebill").click(function() {
            $('#select2-addpatient_id-container').html("");
            $('#bill').trigger("reset");
            var table = document.getElementById("tableID");
            var table_len = (table.rows.length);
            for (i = 1; i < table_len; i++) {
                delete_row(i);
            }
        });

        $(".modalbtnpatient").click(function() {
            $('#formaddpa').trigger("reset");
            $(".dropify-clear").trigger("click");
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.test_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": base_url + "hospital/pharmacy/bill_search",
                    "type": "POST",
                },
                responsive: 'true',
                dom: "Bfrtip",

                /* columnDefs: [
                  {

                  className: 'dt-body-hover'
                     }
                  ],*/

                buttons: [

                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-files-o"></i>',
                        titleAttr: 'Copy',
                        title: $('.download_label').html(),
                        exportOptions: {
                            columns: ':visible'
                        }
                    },

                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: 'Excel',

                        title: $('.download_label').html(),
                        exportOptions: {
                            columns: ':visible'
                        }
                    },

                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text-o"></i>',
                        titleAttr: 'CSV',
                        title: $('.download_label').html(),
                        exportOptions: {
                            columns: ':visible'
                        }
                    },

                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
                        title: $('.download_label').html(),
                        exportOptions: {
                            columns: ':visible'

                        }
                    },

                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: 'Print',
                        title: $('.download_label').html(),
                        customize: function(win) {
                            $(win.document.body)
                                .css('font-size', '10pt');

                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                        exportOptions: {
                            columns: ':visible'
                        }
                    },

                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns"></i>',
                        titleAttr: 'Columns',
                        title: $('.download_label').html(),
                        postfixButtons: ['colvisRestore']
                    },
                ]
            });
        });

        function searchPatients() {
            var searchTerm = $('#patientSearch').val().toLowerCase(); // Get search term from input

            // Make an AJAX request to fetch patients based on the search term
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/patient/searchPatient',
                type: 'GET',
                data: {
                    searchTerm: searchTerm
                }, // Pass searchTerm to the backend
                success: function(response) {
                    var patients = JSON.parse(response); // Ensure it's parsed into an array

                    updatePatientDropdown(patients); // Update the dropdown with filtered data
                }
            });
        }


        // Function to update the dropdown with the filtered patient data
        function updatePatientDropdown(patients) {
            console.log(patients); // Log the patients array to check if it's correct

            var dropdown = $('#addpatient_id');
            dropdown.empty(); // Clear existing options
            dropdown.append('<option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>');
            // Ensure patients is an array and has elements
            if (Array.isArray(patients) && patients.length > 0) {
                $.each(patients, function(index, patient) {
                    var optionText = patient.patient_name + " (" + patient.patient_unique_id + ")";
                    var option = '<option value="' + patient.id + '">' + optionText + '</option>';
                    $('#addpatient_id').append(option);
                });
            } else {
                dropdown.append('<option value="">No patients found</option>');
            }
            $("#addpatient_id").show();
        }

        function get_PatientDetails(id) {
            //$("#patient_name").html("patient_name");
            //$("#schedule_charge").html("schedule_charge");

            $.ajax({
                url: '<?php echo base_url(); ?>hospital/pharmacy/patientDetails',
                type: "POST",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(res) {
                    // console.log(res);
                    if (res) {
                        $('#patient_name').val(res.patient_name);
                        $('#patient_id').val(res.id);
                    } else {
                        $('#patient_name').val('Null');

                    }
                }
            });
        }
    </script>