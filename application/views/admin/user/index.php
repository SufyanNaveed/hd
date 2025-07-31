<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
$marital_status = $this->config->item('marital_status');
$bloodgroup = $this->config->item('bloodgroup');
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo form_error('Opd'); ?>
                            User List
                        </h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-toggle="modal" href="<?php echo base_url() ?>/admin/UserManagement/create" id="addp" class="btn btn-primary btn-sm newpatient"><i class="fa fa-plus"></i> Add User</a>
                            <?php
                            }
                            if ($this->rbac->hasPrivilege('patient_import', 'can_view')) {
                            ?>
                                <!-- <a data-toggle="" href="<?php echo base_url() ?>admin/patient/import" id="addp" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i>  <?php echo $this->lang->line('import') . " " . $this->lang->line('patient') ?></a>  -->
                            <?php } ?>
                            <!-- <a  href="<?php echo base_url() ?>admin/admin/disablepatient" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('disabled') . " " . $this->lang->line('patient') . " " . $this->lang->line('list'); ?></a>  -->
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="download_label">User List</div>
                        <table class="custom-table table table table table-striped table-bordered table-hover test_ajax">
                            <thead>
                                <tr>
                                    <th>UserId</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Father Name</th>
                                    <th>Role</th>
                                    <th>Hospital</th>
                                    <th>Department</th>
                                    <th>Store</th>



                                    <!-- <th><?php echo $this->lang->line('guardian_name'); ?></th> -->
                                    <th class=""><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>


                        </table>

                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-toggle="tooltip" title="Close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_delete' class="pt4">
                        <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                            <!-- <a href="#"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" ><i class="fa fa-pencil"></i></a> -->
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('revisit', 'can_delete')) {
                        ?>
                            <!-- <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a> -->
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-xs-6">
                        <div class="form-group15">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <!-- <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new') . " " . $this->lang->line('patient') ?></span></a> -->
                            <?php } ?>
                        </div>
                    </div><!--./col-sm-4-->
                </div><!-- ./row -->
            </div><!--./modal-header-->

            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
                            <input class="" name="id" type="hidden" id="patientid">
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <div class="col-md-9 col-sm-9 col-xs-9" id="Myinfo">
                                            <ul class="singlelist">
                                                <li class="singlelist24bold">
                                                    <span id="patient_name"></span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-user-secret" data-toggle="tooltip" data-placement="top" title="Patient"></i>
                                                    <span id="guardian"></span>
                                                </li>
                                            </ul>
                                            <ul class="multilinelist">
                                                <li>
                                                    <i class="fas fa-venus-mars" data-toggle="tooltip" data-placement="top" title="Gender"></i>
                                                    <span id="genders"></span>
                                                </li>


                                            </ul>
                                            <ul class="singlelist">

                                                <i class="fa fa-phone-square" data-toggle="tooltip" data-placement="top" title="Phone"></i>
                                                <span id="contact"></span>
                                                </li>

                                                <li>
                                                    <i class="fas fa-street-view" data-toggle="tooltip" data-placement="top" title="Address"></i>
                                                    <span id="address"></span>
                                                </li>

                                            </ul>
                                        </div><!-- ./col-md-9 -->

                                    </div><!-- ./col-md-3 -->
                                </div>
                            </div><!--./col-md-8-->
                    </div><!--./row-->
                    </form>
                </div><!--./col-md-12-->
            </div><!--./row-->
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="editModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"> <?php echo $this->lang->line('user') . " " . $this->lang->line('information'); ?></h4>
            </div><!--./modal-header-->
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formeditUser" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                            <input id="eupdateid" name="updateid" placeholder="" type="hidden" class="form-control" value="" />
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                                <input id="ename" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <div class="form-group">
                                                <label>Father Name</label><small class="req"> *</small>
                                                <input id="father_name" name="father_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('father_name'); ?>" />
                                                <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Role</label><small class="req"> *</small>
                                                <select id="role" name="role" class="form-control">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($roles as $key => $role) {
                                                    ?>
                                                        <option value="<?php echo $role['id'] ?>" <?php echo set_select('role', $role['id'], set_value('role')); ?>><?php echo $role["name"] ?></option>
                                                    <?php }
                                                    ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('role'); ?></span>
                                            </div>
                                        </div>

                                     
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <div class="form-group">
                                                <label>Username <small class="req"> *</small></label>
                                                <input type="text" id="user_cnic" name="user_cnic" data-inputmask="'mask': '99999-9999999-9'" pattern="\d{5}-\d{7}-\d" placeholder="XXXXX-XXXXXXX-X" class="form-control config">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                                    <div class="form-group">
                                                        <label for="mobileno">Phone <small class="req"> *</small></label>
                                                        <input id="mobileno" autocomplete="off" name="mobileno" data-inputmask="'mask': '9999-9999999'" pattern="\d{4}-\d{7}" placeholder="XXXX-XXXXXXX" type="text" class="form-control config" value="<?php echo set_value('mobileno'); ?>" />
                                                    </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <div class="form-group">
                                                <label>Shift Start Time</label><small class="req"> *</small>
                                                <input type="time" name="shift_start_time" class="form-control" required />
                                                <span class="text-danger"><?php echo form_error('shift_start_time'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <div class="form-group">
                                                <label>Shift End Time</label><small class="req"> *</small>
                                                <input type="time" name="shift_end_time" class="form-control" required />
                                                <span class="text-danger"><?php echo form_error('shift_end_time'); ?></span>
                                            </div>
                                        </div>
                                    </div><!--./row-->
                                </div><!--./col-md-8-->
                            </div><!--./row-->
                            <div class="row">
                                <div class="box-footer">
                                    <div class="pull-right">
                                        <button type="submit" id="formeditpabtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right">Save</button>
                                    </div>
                                </div>
                            </div><!--./row-->
                        </form>
                    </div><!--./col-md-12-->
                </div><!--./row-->
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    
    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').show();
            $('#todate').show();
        } else {
            $('#fromdate').hide();
            $('#todate').hide();
        }
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }


    function getpatientData(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                if (data.is_active == 'yes') {
                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' data-toggle='tooltip'  onclick='patient_deactive(" + id + ")' data-original-title='<?php echo $this->lang->line('disable'); ?>'><i class='fa fa-thumbs-o-down'></i></a><?php } ?>";
                } else {
                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' data-toggle='tooltip'  onclick='patient_active(" + id + ")' data-original-title='<?php echo $this->lang->line('enable'); ?>'><i class='fa fa-thumbs-o-up'></i></a> <?php }
                                                                                                                                                                                                                                                                                    if ($this->rbac->hasPrivilege('patient', 'can_delete')) { ?><a href='#' data-toggle='tooltip'  onclick='delete_record(" + id + ")' data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a> <?php } ?>";
                }
                $("patientid").val(data.id);
                $("#patient_name").html(data.patient_name);
                $("#genders").html(data.gender);
                $("#contact").html(data.mobileno);
                $("#address").html(data.address);
                holdModal('myModal');
            },
        });
    }

    function editRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/userManagement/getUserDetail',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    const user = data.data;

                    // Populate modal fields with user data
                    $("#eupdateid").val(user.userId);
                    $("#ename").val(user.username);
                    $("#father_name").val(user.father_name);
                    $("#role").val(user.role_id).change();
                    // $("#employee_code").val(user.employee_code);
                    $("#user_cnic").val(user.cnic);
                    $("#mobileno").val(user.mobileno);
                    $("[name='shift_start_time']").val(user.shift_start_time);
                    $("[name='shift_end_time']").val(user.shift_end_time);

                    // Show the edit modal
                    holdModal('editModal');
                } else {
                    alert(data.message || 'Failed to fetch user details.');
                }
            },
            error: function() {
                alert('An error occurred while fetching user details.');
            }
        });
    }

    $(document).ready(function(e) {
        $("#formeditUser").on('submit', (function(e) {
            $("#formeditpabtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/UserManagement/update',
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
                    }
                    $("#formeditpabtn").button('reset');
                },
                error: function() {

                }
            });
        }));
    });

    function deleteRecord(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_conform') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/UserManagement/deleteUser',
                type: "POST",
                data: {
                    delid: id
                },
                dataType: 'json',
                success: function(data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    function patient_deactive(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_deactive_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deactivePatient',
                type: "POST",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data.message);
                    if (data.status == "fail") {
                        var message = (data.message);
                        errorMsg(message);
                    } else {
                        successMsg(<?php echo "'" . $this->lang->line('deactive_message') . "'"; ?>);
                        window.getpatientData(id);
                    }

                }
            })
        }
    }

    function CalculateAgeInQCe(DOB, txtAge, Txndate) {
        if (DOB.value != '') {
            now = new Date(Txndate)
            var txtValue = DOB;
            if (txtValue != null)
                dob = txtValue.split('/');
            if (dob.length === 3) {
                born = new Date(dob[2], dob[1] * 1 - 1, dob[0]);
                if (now.getMonth() == born.getMonth() && now.getDate() == born.getDate()) {
                    age = now.getFullYear() - born.getFullYear();
                } else {
                    age = Math.floor((now.getTime() - born.getTime()) / (365.25 * 24 * 60 * 60 * 1000));
                }
                if (isNaN(age) || age < 0) {
                    // alert('Input date is incorrect!');
                } else {
                    if (now.getMonth() > born.getMonth()) {
                        var calmonth = now.getMonth() - born.getMonth();
                    } else {
                        var calmonth = born.getMonth() - now.getMonth();
                    }
                    $("#eage_year").val(age);
                    $("#eage_month").val(calmonth);
                    return age;
                }
            }
        }
    }

    $(document).ready(function() {
        $("#ebirth_date").change(function() {
            var mdate = $("#ebirth_date").val().toString();
            var yearThen = parseInt(mdate.substring(6, 10), 10);
            var dayThen = parseInt(mdate.substring(0, 2), 10);
            var monthThen = parseInt(mdate.substring(3, 5), 10);
            var DOB = dayThen + "/" + monthThen + "/" + yearThen;
            CalculateAgeInQCe(DOB, '', new Date());
        });
    });

    function patient_active(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_active_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/activePatient',
                type: "POST",
                data: {
                    activeid: id
                },
                dataType: 'json',
                success: function(data) {
                    successMsg(<?php echo "'" . $this->lang->line('active_message') . "'"; ?>);
                    window.getpatientData(id);
                }
            })
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.test_ajax').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url + "admin/userManagement/user_list",
                "type": "POST"
            },
            responsive: 'true',
            dom: "Bfrtip",
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

    $(".newpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });

    $(".modalbtnpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });
</script>