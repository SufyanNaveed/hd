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
                                        <th>Amount</th>
                                        <th>Bank</th>
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
                                                <td><?php echo $payment['amount']; ?></td>
                                                <td><?php echo $payment['bank']; ?></td>
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