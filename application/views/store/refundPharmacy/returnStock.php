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
                <h3 class="text-center">Create Request</h3>
                <div class="" id="myModal" aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog pup100" role="document">
                        <div class="modal-content modal-media-content">
                            <form id="bill" accept-charset="utf-8" method="post" class="ptt10">
                                <div class="modal-header modal-media-header">
                                    <button type="button" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" class="close closemobile" data-dismiss="modal">&times;</button>
                                    <div class="row modalbillform">
                                        <div class="col-lg-5 col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-6">
                                                    <select onchange="get_StoreDetails(this.value)" style="width: 100%" class="form-control select2" id="addpatient_id" name=''>
                                                        <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('store') ?></option>
                                                        <?php foreach ($hospitalStores as $dkey => $dvalue) {
                                                        ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                                                            if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                                                                                                echo "selected";
                                                                                                            }
                                                                                                            ?>><?php echo $dvalue["store_name"]  ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div><!--./col-sm-3-->
                                                <div class="col-sm-3 col-xs-5">
                                                </div><!--./col-sm-3-->
                                            </div><!--./row-->
                                        </div><!--./col-sm-6-->
                                        <div class="col-lg-6 col-sm-5">
                                            <div class="row">
                                                <div class="col-lg-2 col-sm-3 col-xs-3">
                                                    <label><?php echo $this->lang->line('bill') . " " . $this->lang->line('no'); ?></label><!-- <small class="req" style="color:red;"> *</small> -->
                                                </div>
                                                <div class="col-lg-2 col-sm-3 col-xs-9">
                                                    <input readonly name="bill_no" id="billno" type="text" class="form-control" />
                                                    <span class="text-danger"><?php echo form_error('bill_no'); ?></span>
                                                </div>
                                                <div class="mdclear"></div>
                                                <div class="col-lg-2 col-sm-2 col-xs-3">
                                                    <label><?php echo $this->lang->line('date'); ?></label>
                                                </div>
                                                <div class="col-lg-5 col-sm-4 col-xs-9">
                                                    <input name="date" id="" type="datetime-local" class="form-control"
                                                        value="<?php echo date('Y-m-d\TH:i'); ?>" />

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
                                            <input name="store_name" id="store_name" type="hidden" class="form-control" />
                                            <!-- <input name="date" id="date_result" type="hidden" class="form-control" /> -->
                                            <input name="store_id" id="store_id" type="hidden" class="form-control" />
                                            <input name="bill_no" id="billnoform" type="hidden" class="form-control" />
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="custom-table table tableover table-striped table-bordered table-hover tablefull12" id="tableID">
                                                            <thead>
                                                                <tr class="font13">
                                                                    <th width="13%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?><small class="req" style="color:red;"> *</small></th>
                                                                    <th width="11%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
                                                                    <th width="8%"><?php echo $this->lang->line('batch') . " " . $this->lang->line('no'); ?> <small class="req" style="color:red;">*</small></th>
                            <th>Return Type</th>
                                                                    <th class="text-right" width="15%"><?php echo $this->lang->line('quantity'); ?><small class="req" style="color:red;"> *</small> </th>
                                                                </tr>
                                                            </thead>
                                                            <tr id="row0">
                                                                <td width="160">
                                                                    <select class="form-control" name='medicine_category_id[]' onchange="getmedicine_name(this.value, '0')">
                                                                        <option value="<?php echo set_value('medicine_category_id'); ?>">
                                                                            <?php echo $this->lang->line('select') ?>
                                                                        </option>
                                                                        <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                                                            <option value="<?php echo $dvalue["id"]; ?>">
                                                                                <?php echo $dvalue["medicine_category"] ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                    <span class="text-danger"><?php echo form_error('medicine_category_id[]'); ?></span>
                                                                </td>

                                                                <td width="50%">
                                                                    <select class="form-control select2" style="width:100%" onchange="getbatchnolist(this.value, '0')" id="medicine_name0" name='medicine_name[]'>
                                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                    </select>
                                                                    <span class="text-danger"><?php echo form_error('medicine_name[]'); ?></span>
                                                                </td>
                                                                <td width="16%">
                                                                    <!-- <input type="text" name="batch_no[]" onchange="getExpire(0)" placeholder="" class="form-control" id="batch_no0" > -->
                                                                    <select class="form-control" id="batch_no0" name="batch_no[]" onchange="getmedicine_qty(this.value, '0')">
                                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                    </select>
                                                                    <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                                                                    <input type="hidden" id="batch_id0" name="batch_id[]">
                                                                </td>
                                                                <td width="10%">
                                                                    <!-- <input type="text" name="batch_no[]" onchange="getExpire(0)" placeholder="" class="form-control" id="batch_no0" > -->
                                                                    <select class="form-control" id="return_type" name="return_type[]">
                                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                        <option value="damage">Damage</option>
                                                                        <option value="broken">Broken</option>
                                                                        <option value="expired">Expired</option>
                                                                        <option value="normal">Normal</option>
                                                                    </select>
                                                                    <span class="text-danger"><?php echo form_error('return_type[]'); ?></span>
                                                                </td>
                                                                <td width="40%">
                                                                    <div class="input-group">
                                                                        <input type="text" name="quantity[]" onchange="multiply(0)" onfocus="getQuantity(0)" id="quantity0" class="form-control text-right">
                                                                        <span class="input-group-addon text-danger" style="font-size: 10pt" id="totalqty0">&nbsp;&nbsp;</span>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <button type="button" onclick="addMore()" style="color: #2196f3" class="closebtn">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            <!-- Return Reason row (spanning across all columns) -->
                                                            <tr>
                                                                <td colspan="4">
                                                                    <label for="note">Return Reason</label>
                                                                    <textarea name="return_reason[]" rows="3" id="note" class="form-control"></textarea>
                                                                </td>
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
                                                            <table class="custom-tableprintablea4">







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
                                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="billsave" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                        <input type="hidden" id="is_draft" name="is_draft" value="0">
                                        <input type="button" onclick="addTotal()" value="<?php echo $this->lang->line('calculate'); ?>" class="btn btn-info" />&nbsp;
                                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" style="display: none;margin-right:10px" id="billdraft" class="btn btn-warning pull-right">Draft</button>
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
            var store_id = $('#addpatient_id').val();

            if(!store_id){
                $("select[name='medicine_category_id[]']").val("");
                errorMsg('Please Select store First');
                // alert('Please Select Store First');
                
            }

            //$("#medicine_name" + rowid).prepend($('<option></option>').html('Loading...'));
            $("#medicine_name" + rowid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
            $('#medicine_name' + rowid).select2("val", 'l');
            
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/pharmacy/get_store_medicine_name',
                type: "POST",
                data: {
                    medicine_category_id: id,
                    transfer_store_id:store_id
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

        function getmedicine_qty(id, rowid) {
            var div_data = "";
            var medicine_id = $("#medicine_name"+rowid).val();
            var store_id = $('#addpatient_id').val();
            var batch_no_id = $("#batch_no" + rowid + " option:selected").data('batch-id');
            $("#batch_id"+rowid).val(batch_no_id);
            console.log('batch_id',batch_no_id);
            //$("#medicine_name" + rowid).prepend($('<option></option>').html('Loading...'));
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/pharmacy/get_store_medicine_qty',
                type: "POST",
                data: {
                    medicine_id: medicine_id,
                    transfer_store_id:store_id,
                    batch_id:batch_no_id
                },
                dataType: 'json',
                success: function(res) {
                    $("#totalqty" + rowid).html('0');
                    if (res.status) {

                        // getbatchnolist(id,rowid);
                        $("#totalqty" + rowid).html(res.total_qty);
                    }

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
                    url: '<?php echo base_url(); ?>hospital/pharmacy/addReturnStock',
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
                            window.location.href = "<?php echo base_url(); ?>hospital/pharmacy/medicineRequest?type=pending";

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
    var table_len = table.rows.length;
    var id = parseInt(table_len - 1);

    // Get the last selected category value
    var lastCategory = $("select[name='medicine_category_id[]']").last().val();

    var div = "<td colspan='6'> \
        <div style='display: flex; gap: 10px; align-items: center;'> \
            <select class='form-control' style='width:20%'  name='medicine_category_id[]' onchange='getmedicine_name(this.value," + id + ")'> \
                <option value=''><?php echo $this->lang->line('select') ?></option> \
                <?php foreach ($medicineCategory as $dkey => $dvalue) { ?> \
                <option value='<?php echo $dvalue['id']; ?>'><?php echo $dvalue['medicine_category'] ?></option> \
                <?php } ?> \
            </select> \
            <select class='form-control select2' style='width:40%' name='medicine_name[]' onchange='getbatchnolist(this.value," + id + ")' id='medicine_name" + id + "'> \
                <option value=''><?php echo $this->lang->line('select') ?></option> \
            </select> \
            <select class='form-control' style='width:20%' name='batch_no[]' id='batch_no" + id + "' onchange='getmedicine_qty(this.value," + id + ")'> \
                <option value=''><?php echo $this->lang->line('select') ?></option> <input type='hidden' id='batch_id" + id + "' name='batch_id[]'>\
            </select> \
             <select class='form-control' style='width:20%' name='return_type[]' id='return_type" + id + "'> \
                <option value=''><?php echo $this->lang->line('select') ?></option>\
                <option value='damage'>Damage</option>\
                <option value='broken'>Broken</option>\
                <option value='expired'>Expired</option>\
                <option value='normal'>Normal</option>\
            </select> \
            <div class='input-group'> \
                <input type='text' name='quantity[]' onchange='multiply(" + id + ")' onfocus='getQuantity(" + id + ")' id='quantity" + id + "' class='form-control text-right'> \
                <span class='input-group-addon text-danger' style='font-size:10pt' id='totalqty" + id + "'>&nbsp;&nbsp;</span> \
            </div> \
            <button type='button' onclick='delete_row(" + id + ")' class='closebtn' \
                style='background: none; border: none; font-size: 16px; color: red; cursor: pointer;'> \
                <i class='fa fa-remove'></i> \
            </button> \
        </div> \
        <div style='margin-top: 5px; background: #FFF3CD; padding: 8px; border-radius: 5px;'> \
            <label for='note" + id + "' style='font-weight: bold; display: block;'>Return Reason</label> \
            <textarea name='return_reason[]' rows='2' id='note" + id + "' class='form-control' \
                style='width: 100%;'></textarea> \
        </div> \
    </td>";

    var row = table.insertRow(table_len);
    row.id = "row" + id;
    row.innerHTML = div;

    $('.select2').select2();

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
            $("#billdraft").show();
            $(".printsavebtn").show();
        }

        function delete_row(id) {
            var table = document.getElementById("tableID");
            var rowCount = table.rows.length;
            $("#row" + id).remove();
        }


        $(document).ready(function() {
            $("#billdraft").click(function(e) {
                e.preventDefault();
                $("#is_draft").val(1); // Set draft status
                submitBillForm($(this)); // Pass the clicked button
            });

            $("#billsave").click(function(e) {
                e.preventDefault();
                $("#is_draft").val(0); // Set final status
                submitBillForm($(this)); // Pass the clicked button
            });

            function submitBillForm(button) {
    if (!confirm("Are you sure you want to save?")) {
        return; // Stop submission if the user clicks "Cancel"
    }

    button.button('loading'); // Apply loading state to clicked button

    var table = document.getElementById("tableID");
    var rowCount = table.rows.length;
    
    // Quantity validation before sending data to the server
    for (var k = 0; k < rowCount; k++) {
        var quantity = $('#quantity' + k).val();
        var availableQuantity = $('#available_quantity' + k).val();
        
        if (parseInt(quantity) > parseInt(availableQuantity)) {
            errorMsg(`Error: Row ${k + 1} - Order quantity (${quantity}) exceeds available stock (${availableQuantity})`);
            button.button('reset'); // Reset button state
            return false;
        }
    }

    $.ajax({
        url: '<?php echo base_url(); ?>hospital/pharmacy/addReturnStock',
        type: "POST",
        data: new FormData($("#bill")[0]), // Correct form reference
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            button.button('reset'); // Reset button state

            if (data.status === "fail") {
                let message = "Some stock updates failed due to insufficient quantity.<br>";

                if (typeof data.error === "string") {
                    message += data.error; // Handle string error messages
                } else if (Array.isArray(data.error)) {
                    data.error.forEach(function (err) {
                        message += err + "<br>";
                    });
                }

                errorMsg(message); // Show the error message to the user
            } else {
                successMsg(data.message);
                window.location.href = "<?php echo base_url(); ?>hospital/refundPharmacy";
            }
        },
        error: function(xhr, status, error) {
            button.button('reset'); // Reset button state on error
            errorMsg("An unexpected error occurred. Please try again.");
        }
    });
}

        });



        function viewDetail(id, bill_no, patient_id) {

            $.ajax({
                url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/' + id,
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
            var medicine = $("#medicine_name" + id).val();
            if (batch_no != "") {
                $('#quantity').html("");
                $.ajax({
                    type: "GET",
                    url: base_url + "hospital/pharmacy/getQuantity",
                    data: {
                        'batch_no': batch_no,
                        'med_id': medicine
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#id' + id).val(data.id);
                        $('#totalqty' + id).html(data.available_quantity);
                        $('#available_quantity' + id).val(data.available_quantity);
                        $('#sale_price' + id).val(data.sale_rate);
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

        function getbatchnolist(id, rowid) {
            var div_data = "";
            var store_id = $('#addpatient_id').val();

            $('#totalqty' + rowid).html("<span class='input-group-addon text-danger' style='font-size:10pt'  id='totalqty" + rowid + "'></span>");
            $('#available_quantity' + rowid).val('');
            $('#sale_price' + rowid).val('');
            $('#expire_date' + rowid).val('');
            $('#amount' + rowid).val('');
            $('#quantity' + rowid).val('');
            $("#batch_no" + rowid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");

            $.ajax({
                type: "POST",
                url: base_url + "hospital/pharmacy/getStorePharmasistBatchNoList",
                data: {
                    'medicine': id,
                    'transfer_store_id':store_id
                },
                dataType: 'json',
                success: function(res) {
                    $.each(res, function(i, obj) {
                        var sel = "";
                        div_data += "<option value='" + obj.batch_no + "' data-batch-id='"+obj.id+"'>" + obj.batch_no + "</option>";
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
    </script>