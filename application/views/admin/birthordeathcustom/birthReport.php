<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<style type="text/css">
    #easySelectable {/*display: flex; flex-wrap: wrap;*/}
    #easySelectable li {}
    #easySelectable li.es-selected {background: #2196F3; color: #fff;}
    .easySelectable {-webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;}
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('birth_death_customfields', 'can_view')) {?> <li>
                                <a href="<?php echo base_url(); ?>admin/birthordeathcustom" class="active"><?php echo $this->lang->line('birth_record') . " " . $this->lang->line('custom') . " " . $this->lang->line('fields'); ?></a></li>
                        <?php }?>
                        <?php if ($this->rbac->hasPrivilege('birth_death_customfields', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/birthordeathcustom/death"><?php echo $this->lang->line('death_record') . " " . $this->lang->line('custom') . " " . $this->lang->line('fields'); ?></a></li>
                        <?php }?>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('birth_record') . " " . $this->lang->line('custom') . " " . $this->lang->line('fields'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php
if ($this->rbac->hasPrivilege('birth_death_customfields', 'can_add')) {
    ?>
                                <a data-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm birth_record"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('birth') . " " . $this->lang->line('details') . " " . $this->lang->line('list'); ?></div>
                        <table class="custom-table table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('fields') . " " . $this->lang->line('name'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
if (empty($resultlist)) {
    ?>

                                    <?php
} else {
    $count = 1;
    foreach ($resultlist as $birth) {
        ?>
                                        <tr class="">
                                            <td>
                                                <?php echo $birth['name']; ?>
                                                <div class="rowoptionview">
                                                    <?php
if ($this->rbac->hasPrivilege('birth_death_customfields', 'can_edit')) {
            ?>
                                                        <a href="#" onclick="getRecord('<?php echo $birth['id'] ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }if ($this->rbac->hasPrivilege('birth_death_customfields', 'can_delete')) {?>
                                                        <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_recordById('<?php echo base_url(); ?>admin/birthordeathcustom/deletecustom/<?php echo $birth['id']; ?>', '<?php echo $this->lang->line('delete_message'); ?>')" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php }?>
                                                </div>
                                            </td>
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
    </section>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog  modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('custom') . " " . $this->lang->line('fields'); ?></h4>
            </div>
            <form id="form1" action="<?php echo site_url('admin/birthordeathcustom') ?>" accept-charset="utf-8" method="post" class="" >
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <?php echo validation_errors(); ?>
                        <?php if ($this->session->flashdata('msg')) {?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php }?>
                        <?php
if (isset($error_message)) {
    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
}
?>
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label> <small class="req">*</small>
                            <input id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo $this->lang->line('visiblility') ?></label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="content_available" name="display_tbl" id="edisplay_tbl" value="1" <?php echo set_checkbox('display_tbl', '1', (set_value('display_tbl') == 1) ? true : false); ?>>
                                    <?php echo $this->lang->line('on') . " " . $this->lang->line('print') ?>
                                </label>
                            </div>
                        </div>
                    </div><!--./row-->
                </div><!--./col-md-12-->
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- dd -->
<div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit') . " " . $this->lang->line('custom') . " " . $this->lang->line('fields'); ?></h4>
            </div>
            <form id="form11"  accept-charset="utf-8" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="eid" value="<?php echo set_value('id'); ?>">
                    <?php echo validation_errors(); ?>
                    <?php if ($this->session->flashdata('msg')) {?>
                        <?php echo $this->session->flashdata('msg') ?>
                    <?php }?>
                    <?php
if (isset($error_message)) {
    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
}
?>
                    <?php echo $this->customlib->getCSRF(); ?>

                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label> <small class="req">*</small>
                        <input id="ename" value="<?php echo set_value('name'); ?>" name="name" placeholder="" type="text" class="form-control"  />
                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo $this->lang->line('visiblility') ?></label>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="content_available" name="display_tbl" id="evisible_check" value="1" <?php echo set_checkbox('display_tbl', '1', (set_value('display_tbl') == 1) ? true : false); ?>>
                                <?php echo $this->lang->line('on') . " " . $this->lang->line('print') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" id="" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('charge') . " " . $this->lang->line('information'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="view" accept-charset="utf-8" method="get" class="ptt10">
                            <div class="table-responsive">
                                <table class="custom-table table mb0 table-striped table-bordered examples">
                                    <tr>
                                        <th><?php echo $this->lang->line('opd_ipd_no') ?></th>
                                        <td><span id='vopd_ipd_no'></span></td>
                                        <th><?php echo $this->lang->line('child') . " " . $this->lang->line('name'); ?></th>
                                        <td><span id='vchild_name'></span></td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('birth_date'); ?></th>
                                        <td><span id="vbirth_date"></span>
                                        </td>
                                        <th><?php echo $this->lang->line('weight'); ?></th>
                                        <td><span id='vweight'></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('mother_name'); ?></th>
                                        <td><span id="vmother_name"></span>
                                        </td>
                                        <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <td><span id="vfather_name"></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div><!--./col-md-12-->
                </div><!--./row-->
                <div id="tabledata"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('#myModal').on('hidden.bs.modal', function (e) {
        $(this).find('#formadd')[0].reset();
    });

    $(function () {
        $('#easySelectable').easySelectable();
        $('.select2').select2();
    })

    function apply_to_all() {
        var standard_charge = $("#standard_charge").val();
        $('input name=schedule_charge_id').val(standard_charge);
    }

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
    function getChargeCategory(charge_type, charge_category) {
        console.log(charge_category)
        $('#edit_charge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            data: {'charge_type': charge_type},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj) {
                    var sel = "";
                    if (charge_category == obj.name) {
                        sel = "selected";
                    }
                    div_data += "<option value='" + obj.name + "'  " + sel + ">" + obj.name + "</option>";
                });
                $('#edit_charge_category').append(div_data);
            }
        });
    }

    function getcharge_category(id, htmlid) {
        var div_data = "";
        $("#" + htmlid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.name + "'>" + obj.name + "</option>";
                });
                $("#" + htmlid).html("<option value=''>Select</option>");
                $('#' + htmlid).append(div_data);
            }
        });
    }

    $(document).ready(function (e) {
        $("#form1").on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeathcustom/addCustomfiled',
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
                    $("#formaddbtn").button('reset');
                },
                error: function () {
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#form11").on('submit', (function (e) {
            $("#formeditbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeathcustom/addCustomfiled',
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

                }
            });
        }));
    });

    $(document).ready(function (e) {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        $('#dates_of_birth , #date_of_birth').datepicker();
    });

    function getRecord(id) {
        $('#myModaledit').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/birthordeathcustom/edit',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#eid").val(data.id);
                $("#ename").val(data.name);
                //console.log(data.child_name);
                $("#edisplay_tbl").val(data.display_tbl);
                if (data.visible_on_table == '0') {
                    $("#evisible_check").attr('checked', false);
                } else
                {
                    $("#evisible_check").attr('checked', true);
                }
            },
        });
    }

    function viewDetail(id) {
        $('#viewModal').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/birthordeath/getBirthdata',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#vid").html(data.id);
                $("#vopd_ipd_no").html(data.opd_ipd_no);
                $("#vchild_name").html(data.child_name);
                $("#vbirth_date").html(data.birth_date);
                $("#vweight").html(data.weight);
                $("#vmother_name").html(data.mother_name);
                $("#vcontact").html(data.contact);
                $("#vaddress").html(data.address);
                $("#vfather_name").html(data.father_name);
                $("#vmother_pic").html(data.mother_pic);
                $("#vfather_pic").html(data.father_pic);
                $("#vbirth_report").html(data.birth_report);
            },
        });
    }

    function apply_to_all() {
        var total = 0;
        var standard_charge = $("#standard_charge").val();
        var schedule_charge = document.getElementsByName('schedule_charge[]');
        for (var i = 0; i < schedule_charge.length; i++) {
            var inp = schedule_charge[i];
            inp.value = standard_charge;
        }
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

$(".birth_record").click(function(){
    $('#form1').trigger("reset");
});
</script>