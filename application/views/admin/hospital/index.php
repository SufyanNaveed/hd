
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo form_error('Opd'); ?>
                            Hospital List
                        </h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-toggle="modal" onclick="holdModal('myModalpa')" id="addp" class="btn btn-primary btn-sm newpatient"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('new') . " hospital"   ?></a>
                            <?php
                            }
                            if ($this->rbac->hasPrivilege('patient_import', 'can_view')) {
                            ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('patient') . " " . $this->lang->line('list'); ?></div>
                        <table class="custom-table table table table table-striped table-bordered table-hover test_ajax">
                            <thead>
                                <tr>
                                    <th>Hospital ID</th>
                                    <th>Hospital Name</th>
                                    <!-- <th>Address</th> -->
                                    <!-- <th><?php echo $this->lang->line('phone'); ?></th> -->
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
                <!-- <div class="modalicon">
                    <div id='edit_delete' class="pt4">
                        <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                            <a href="#" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('revisit', 'can_delete')) {
                        ?>
                            <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-sm-4 col-xs-6">
                        
                    </div><!--./col-sm-4-->
                </div><!-- ./row -->
            </div><!--./modal-header-->

            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/hospital" ?>" enctype="multipart/form-data" method="post">
                            <input class="" name="id" type="hidden" id="hospitalid">
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <!-- Hospital Info -->
                                        <div class="col-md-9 col-sm-9 col-xs-9" id="Myinfo">
                                            <ul class="singlelist">
                                                <li class="singlelist24bold">
                                                    <b>Hospital Name:</b> <span id="hospital_name"></span>
                                                </li>
                                                <li>
                                                    <b>Unique ID:</b> <span id="hospital_unique_id"></span>
                                                </li>
                                            </ul>
                                            <ul class="multilinelist">
                                                <li>
                                                    <i class="fa fa-phone-square" data-toggle="tooltip" data-placement="top" title="Phone"></i>
                                                    <span id="hospital_phone"></span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-map-marker-alt" data-toggle="tooltip" data-placement="top" title="Address"></i>
                                                    <span id="hospital_address"></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- Hospital Image -->
                                        <div class="col-md-3 col-sm-3 col-xs-3">
                                            <div class="pull-right">
                                                <?php $file = "uploads/hospital_images/no_image.png"; ?>
                                                <img class="profile-user-img img-responsive" src="<?php echo base_url() . $file ?>" id="hospital_image" alt="Hospital profile picture">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Edit Hospital Information</h4>
            </div><!--./modal-header-->
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formeditpa" accept-charset="utf-8" action="<?php echo base_url(); ?>admin/hospital/updateHospital" enctype="multipart/form-data" method="post">
                            <!-- Hidden field for Hospital ID -->
                            <input id="eupdateid" name="updateid" type="hidden" class="form-control" value="" />
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <!-- Hospital Name -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hospital Name</label><small class="req"> *</small>
                                                <input id="ename" name="name" placeholder="Enter hospital name" type="text" class="form-control" />
                                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                                            </div>
                                        </div>

                                        <!-- Hospital Phone -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="emobileno">Phone</label>
                                                 <input id="emobileno" autocomplete="off" name="phone" data-inputmask="'mask': '9999-9999999'" pattern="\d{4}-\d{7}" placeholder="XXXX-XXXXXXX"  type="text" placeholder="" class="form-control config"  value="<?php echo set_value('mobileno'); ?>" />
                                            </div>
                                        </div>

                                        <!-- Hospital Address -->
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="eaddress">Address</label>
                                                <input name="address" id="eaddress" placeholder="Enter hospital address" class="form-control" />
                                            </div>
                                        </div>

                                        <!-- Hospital Image -->
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="eimage">Upload Image</label>
                                                <input class="filestyle form-control" type='file' name='file' id="file" size='20' data-height="26" />
                                                <!-- <input type="file" id="eimage" name="image" class="form-control" /> -->
                                            </div>
                                        </div>
                                    </div><!--./row-->
                                </div><!--./col-md-12-->
                            </div><!--./row-->

                            <!-- Save Button -->
                            <div class="row">
                                <div class="box-footer">
                                    <div class="pull-right">
                                        <button type="submit" id="formeditpabtn" data-loading-text="Processing" class="btn btn-info pull-right">Save</button>
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

    function getHospitalData(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/hospital/getHospitalDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                // Populate the modal fields with hospital data
                $("#hospitalid").val(data.id);
                $("#hospital_name").html(data.name);
                $("#hospital_unique_id").html(data.hospital_unique_id);
                $("#hospital_phone").html(data.phone_number);
                $("#hospital_address").html(data.address);

                // Update the hospital image
                let imagePath = data.logo ? '<?php echo base_url(); ?>' + data.logo : '<?php echo base_url(); ?>uploads/hospital_images/no_image.png';
                $("#hospital_image").attr("src", imagePath);

                // Show the modal
                holdModal('myModal');
            },
            error: function() {
                alert('Failed to fetch hospital data.');
            }
        });
    }


    function editRecord(id) {
    $.ajax({
        url: '<?php echo base_url(); ?>admin/hospital/getHospitalDetails',
        type: "POST",
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            // Populate the modal fields with hospital data
            $("#eupdateid").val(data.id);
            $("#ename").val(data.name);
            $("#emobileno").val(data.phone_number);
            $("#eaddress").val(data.address);

            // Handle image preview
            let imagePath = data.image ? '<?php echo base_url(); ?>' + data.image : '<?php echo base_url(); ?>uploads/hospital_images/no_image.png';
            $("#eimage").attr("data-default-file", imagePath);
            $(".dropify-render").find("img").attr("src", imagePath);

            // Show the edit modal
            holdModal('editModal');
        },
        error: function() {
            alert('Failed to fetch hospital data.');
        }
    });
}


    $(document).ready(function(e) {
        $("#formeditpa").on('submit', (function(e) {
            $("#formeditpabtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/hospital/updateHospital',
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
                    }
                    window.location.reload(true);
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
                url: '<?php echo base_url(); ?>admin/hospital/deleteHospital',
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
                        window.getHospitalData(id);
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
                "url": base_url + "admin/hospital/hospital_list",
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
<?php $this->load->view('admin/hospital/hospitalModal') ?>