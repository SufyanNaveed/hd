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
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('tpa_management'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('organisation', 'can_add')) { ?>
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm organisation"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('organisation'); ?></a> 
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
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('code'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person_name'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person_phone'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($resultlist)) {
                                        ?>
                                                                        
                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($resultlist as $org) {
                                            ?>
                                            <tr class="">
                                                <td>
                                                    <?php echo $org['organisation_name']; ?>
                                                    <div class="rowoptionview">
                                                        <?php if ($this->rbac->hasPrivilege('organisation', 'can_view
                                                        ')) { ?>
                                                            <a href="<?php echo base_url(); ?>admin/tpa/master/<?php echo $org['id']; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('organisation_profile'); ?>" >
                                                                <i class="fa fa-reorder"></i>
                                                            </a>
                                                            <a href="<?php echo base_url(); ?>admin/tpamanagement/payments/<?php echo $org['id']; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="Organisation Payments" >
                                                                <i class="fa fa-reorder"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('organisation', 'can_edit')) { ?>
                                                            <a href="#" class="btn btn-default btn-xs" onclick="get_orgdata('<?php echo $org['id']; ?>')"  data-toggle="tooltip"  title="<?php echo $this->lang->line('edit_organisation'); ?>" >
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } if ($this->rbac->hasPrivilege('organisation', 'can_delete')) { ?>
                                                            <a  onclick="delete_recordById('<?php echo base_url(); ?>admin/tpamanagement/delete/<?php echo $org['id']; ?>', '<?php echo $this->lang->line('delete_message') ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip"  title="<?php echo $this->lang->line('delete'); ?>" >
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </div>  
                                                </td>
                                                <td><?php echo $org['code']; ?></td>
                                                <td><?php echo $org['contact_no']; ?></td>
                                                <td><?php echo $org['address']; ?></td>
                                                <td><?php echo $org['contact_person_name']; ?></td>
                                                <td><?php echo $org['contact_person_phone']; ?></td>
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('organisation') . " " . $this->lang->line('information'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formadd" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small> 
                                        <input id="name" name="name" placeholder="" type="text" class="form-control"   autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('code'); ?></label><small class="req"> *</small> 
                                        <input id="code" name="code" placeholder="" type="text" class="form-control"   autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('contact_no'); ?></label><small class="req"> *</small> 
                                        <input id="name" name="contact_number" placeholder="" type="text" class="form-control"   autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('address'); ?></label> 
                                        <textarea name="address" class="form-control" autocomplete="off"></textarea>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('contact_person_name'); ?> </label>
                                        <input id="name" name="contact_person_name" placeholder="" type="text" class="form-control"   autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('contact_person_phone'); ?></label>
                                        <input id="name" name="contact_person_phone" placeholder="" type="text" class="form-control"   autocomplete="off" />
                                    </div>
                                </div>
                            </div><!--./row--> 
                            
                            <div class="row">
                                <div class="col-sm-4">
                                    <!--  <input name="patient_id" placeholder="" id="ins_patient_id"  type="hidden" class="form-control" /> -->
                                    <input name="patient_id" placeholder="" id="inspatient_id" type="hidden"
                                        class="form-control" />
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="custom-table table tableover table-striped table-bordered table-hover"
                                            id="tableID">
                                            <tr>
                                                <th>Cheque Number<small style="color:red;"> *</small></th>
                                                <th>Date<small style="color:red;">*</small></th>
                                                <th>Amount<small style="color:red;">*</small></th>
                                                <th>From Bank<small style="color:red;"> *</small></th>
                                                <!-- <th>Instruction Time</th> -->
                                            </tr>
                                            <tr id="row0">
                                                <td>
                                                    <input type="text" style="height:28px" name="cheque[]" class="form-control" />
                                                </td>
                                                <td>
                                                    <input
                                                        value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>"
                                                        type="text" name="date[]" class="form-control date">
                                                </td>
                                                <td>
                                                    <input type="text" style="height:28px" name="amount[]" class="form-control" pattern="[0-9]*" inputmode="numeric" />
                                                </td>
                                                <td>
                                                    <input type="text" style="height:28px" name="bank[]" class="form-control" />
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

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit') . " " . $this->lang->line('organisation') . " " . $this->lang->line('information'); ?></h4> 
            </div>

            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formedit" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small> 
                                        <input id="ename" name="ename" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('code'); ?></label><small class="req"> *</small> 
                                        <input id="ecode" name="ecode" placeholder="" type="text" class="form-control"  value="<?php echo set_value('code'); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('contact_no'); ?></label><small class="req"> *</small> 
                                        <input id="econtact_number" name="econtact_number" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('address'); ?></label> 
                                        <textarea name="eaddress" id="eaddress" class="form-control" autocomplete="off"></textarea>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('contact_person_name'); ?> </label>
                                        <input type="hidden" id="org_id" name="org_id" >
                                        <input id="econtact_persion_name" name="econtact_persion_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('contact_person_phone'); ?> </label>
                                        <input id="econtact_persion_phone" name="econtact_persion_phone" placeholder="" type="text" class="form-control"   autocomplete="off" />
                                    </div>
                                </div>
                            </div><!--./row-->
                            <div class="row">
                                <div class="col-sm-4">
                                    <!--  <input name="patient_id" placeholder="" id="ins_patient_id"  type="hidden" class="form-control" /> -->
                                    <input name="patient_id" placeholder="" id="inspatient_id" type="hidden"
                                        class="form-control" />
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="custom-table table tableover table-striped table-bordered table-hover"
                                            id="editTableID">
                                            <tr>
                                                <th>Cheque Number<small style="color:red;"> *</small></th>
                                                <th>Date<small style="color:red;">*</small></th>
                                                <th>Amount<small style="color:red;">*</small></th>
                                                <th>From Bank<small style="color:red;"> *</small></th>
                                                <!-- <th>Instruction Time</th> -->
                                            </tr>
                                            <tr id="row0">
                                                <td>
                                                    <input type="text" style="height:28px" name="cheque[]" class="form-control" />
                                                </td>
                                                <td>
                                                    <input
                                                        value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>"
                                                        type="text" name="date[]" class="form-control date">
                                                </td>
                                                <td>
                                                    <input type="text" style="height:28px" name="amount[]" class="form-control" pattern="[0-9]*" inputmode="numeric" />
                                                </td>
                                                <td>
                                                    <input type="text" style="height:28px" name="bank[]" class="form-control" />
                                                </td>
                                                <td>
                                                    <button type="button" onclick="add_more_edit()" style="color:#2196f3"
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
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formeditbtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </div>
            </form>     
        </div>
    </div> 
</div>
<script type="text/javascript">
    function get_orgdata(id) {
        $('#editModal').modal('show')
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpamanagement/get_data/' + id,
            dataType: 'json',
            success: function (res) {

                $('#org_id').val(res.id);			//alert(res);
                $('#ename').val(res.ename);
                $('#ecode').val(res.ecode);
                $('#econtact_number').val(res.econtact_number);
                $('#eaddress').val(res.eaddress);
                $('#econtact_persion_name').val(res.econtact_persion_name);
                $('#econtact_persion_phone').val(res.econtact_persion_phone);
            }
        });
    }
    $(function () {
        $('#easySelectable').easySelectable();

    })
</script>
<script type="text/javascript">            
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
</script>
<script type="text/javascript">
    
$(document).ready(function () {
    $("#formadd").on('submit', function (e) {
        $("#formaddbtn").button('loading');
        e.preventDefault();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpamanagement/add_organisation',
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


$(document).ready(function (e) {
    $("#formedit").on('submit', (function (e) {
        $("#formeditbtn").button('loading');
        e.preventDefault();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpamanagement/edit',
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
                }
                $("#formeditbtn").button('reset');
            },
            error: function () {
                //  alert("Fail")
            }
        });
    }));
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

function add_more() {
    var table = document.getElementById("tableID");
    var table_len = table.rows.length;
    var id = parseInt(table_len);

    var row = table.insertRow(table_len);
    row.id = "row" + id;

    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);

    cell1.innerHTML = "<input type='text' style='height:28px' name='cheque[]' class='form-control' />";
    cell2.innerHTML = "<input type='text' value='<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>' name='date[]' class='form-control date'>";
    cell3.innerHTML = "<input type='text' style='height:28px' name='amount[]' class='form-control amount' />";
    cell4.innerHTML = "<input type='text' style='height:28px' name='bank[]' class='form-control' />";
    cell5.innerHTML = "<button type='button' onclick='remove_row(" + id + ")' style='color:#f00' class='closebtn'><i class='fa fa-remove'></i></button>";

    // Add event listener for input validation
    $('input[name="amount[]"]').off('input').on('input', function() {
        var sanitizedValue = $(this).val().replace(/\D/g, '');
        $(this).val(sanitizedValue);
    });

    // Update IDs for date inputs if needed
    $(".date").each(function(index) {
        $(this).attr("id", "date" + index);
    });
}

function add_more_edit() {
    var table = document.getElementById("editTableID");
    var table_len = table.rows.length;
    var id = parseInt(table_len);

    var row = table.insertRow(table_len);
    row.id = "row" + id;

    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);

    cell1.innerHTML = "<input type='text' style='height:28px' name='cheque[]' class='form-control' />";
    cell2.innerHTML = "<input type='text' value='<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>' name='date[]' class='form-control date'>";
    cell3.innerHTML = "<input type='text' style='height:28px' name='amount[]' class='form-control amount' />";
    cell4.innerHTML = "<input type='text' style='height:28px' name='bank[]' class='form-control' />";
    cell5.innerHTML = "<button type='button' onclick='remove_row(" + id + ")' style='color:#f00' class='closebtn'><i class='fa fa-remove'></i></button>";

    // Add event listener for input validation
    $('input[name="amount[]"]').off('input').on('input', function() {
        var sanitizedValue = $(this).val().replace(/\D/g, '');
        $(this).val(sanitizedValue);
    });

    // Update IDs for date inputs if needed
    $(".date").each(function(index) {
        $(this).attr("id", "date" + index);
    });
}


function remove_row(row_id) {
    $("#row" + row_id).remove();
}


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
})
</script>