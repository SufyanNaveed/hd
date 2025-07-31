<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<?php
$genderList = $this->customlib->getGender_Patient();
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

                    <div class="" id="myModalpa" role="dialog" aria-labelledby="myModalLabel">
    <div class="" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Add User</h4>
            </div>
            <form id="formaddpa" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
                <input type="hidden" name="bulk_form" id="bulk_form" class="bulk_form">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">

                        <div class="row row-eq">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                            <input id="name" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label>Father Name</label><small class="req"> *</small>
                                            <input id="name" name="father_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('father_name'); ?>" />
                                            <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('role'); ?></label><small class="req"> *</small>
                                            <select id="role"  name="role" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
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
                                            <label>Password</label><small class="req"> *</small>
                                            <input id="name" name="password" placeholder="" type="password" class="form-control" />
                                            <span class="text-danger"><?php echo form_error('Password'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label>Employee Code</label>
                                            <input id="mr_no" name="employee_code" placeholder="" type="text" class="form-control" value="<?php echo set_value('employee_code'); ?>" />
                                            <span class="text-danger"><?php echo form_error('employee_code'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label>Username <small class="req"> *</small></label>
                                            <input type="text" name="user_cnic" data-inputmask="'mask': '99999-9999999-9'" pattern="\d{5}-\d{7}-\d" placeholder="XXXXX-XXXXXXX-X" class="form-control config">
                                        </div>
                                    </div>


                                    <div class="col-md-6 col-sm-12">
                                        <div class="row">

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('phone'); ?> <small class="req"> *</small></label>
                                                    <input id="number" autocomplete="off" name="mobileno" data-inputmask="'mask': '9999-9999999'" pattern="\d{4}-\d{7}" placeholder="XXXX-XXXXXXX" type="text" placeholder="" class="form-control config" value="<?php echo set_value('mobileno'); ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--./col-md-6-->
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
                          <div class="row row-eq">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-3">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Select Hospital</label><small class="req"> *</small>
                                                    <select id="hospital_id" name="hospital_id" class="form-control select2 hospital_ids">
                                                        <option value="">Select Hospital</option>
                                                        <?php foreach ($hospitals as $hospital) { ?>
                                                            <option value="<?php echo $hospital['id']; ?>"
                                                                <?php echo (!empty($user) && $user->hospital_id == $hospital['id']) ? 'selected' : ''; ?>>
                                                                <?php echo $hospital['name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('hospital_id'); ?></span>
                                                </div>
                                            </div>



                                            <div class="col-lg-3 col-md-3 col-sm-3"
                                                style="<?php echo (!empty($user) && !empty($user->department_id)) ? '' : 'display: none;'; ?>"
                                                id="department_section">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Select Department</label><small class="req"> *</small>
                                                    <select id="department_id" name="department_id[]" class="form-control select2" multiple="multiple" data-placeholder="Select Department" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                    
                                                        <option value="">Select Department</option>
                                                        <?php if (!empty($departments)) { ?>
                                                            <?php foreach ($departments as $department) { ?>
                                                                <option value="<?php echo $department['id']; ?>"
                                                                    <?php echo (!empty($user) && $user->department_id == $department['id']) ? 'selected' : ''; ?>>
                                                                    <?php echo $department['department_name']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-sm-3"
                                                style="<?php echo (!empty($user) && !empty($user->store_id)) ? '' : 'display: none;'; ?>"
                                                id="store_section">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Select Store</label><small class="req"> *</small>
                                                    <select id="store_id" name="store_id" class="form-control ">
                                                        <option value="">Select Store</option>
                                                        <?php if (!empty($stores)) { ?>
                                                            <?php foreach ($stores as $store) { ?>
                                                                <option value="<?php echo $store['id']; ?>"
                                                                    <?php echo (!empty($user) && $user->store_id == $store['id']) ? 'selected' : ''; ?>>
                                                                    <?php echo $store['store_name']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>


                                        </div><!--./row-->
                                    </div><!--./col-md-8-->
                                </div><!--./row-->
                    </div>
                </div>

                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formaddpabtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    

<script type="text/javascript">
    $(document).ready(function(e) {
        $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()

    });

        $('#hospital_id').on('change', function() {
            const hospitalId = $(this).val();
            const userId = $('#user_id').val(); // Ensure user ID is available
            const role = $("#role").val();

            // Reset department and store dropdowns and hide sections
            $('#department_id').html('<option value="">Select Department</option>');
            $('#department_section').hide();
            $('#store_id').html('<option value="">Select Store</option>');
            $('#store_section').hide();

            // Validate inputs
            if (!hospitalId || !role) {
                alert('Please Select Role Frist');
                return;
            }

            // Fetch data based on hospital and user
            $.ajax({
                url: '<?php echo base_url("admin/userManagement/fetchDataByRole"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    hospital_id: hospitalId,
                    role_id: role,
                },
                success: function(response) {
                    if (response.status === 'success') {
                        
                        const data = response.data;

                        // Populate department dropdown if departments exist
                        if (data.departments && data.departments.length > 0) {
                            let departmentOptions = '<option value="">Select Department</option>';
                            data.departments.forEach(department => {
                                departmentOptions += `<option value="${department.id}">${department.department_name}</option>`;
                            });
                            $('#department_id').html(departmentOptions);
                            $('#department_section').show(); // Ensure section is visible
                        }

                        // Populate store dropdown if stores exist
                        if (data.stores && data.stores.length > 0) {
                            let storeOptions = '<option value="">Select Store</option>';
                            data.stores.forEach(store => {
                                storeOptions += `<option value="${store.id}">${store.store_name}</option>`;
                            });
                            $('#store_id').html(storeOptions);
                            $('#store_section').show(); // Ensure section is visible
                        }

                        console.log('Data fetched successfully:', data);
                    } else {
                        alert(response.message || 'Failed to fetch data.');
                    }
                },
                error: function() {
                    // alert('An error occurred while fetching data.');
                },
            });
        });

        // Handle Department Change Event
        $('#department_id').on('change', function() {
            const departmentId = $(this).val();
            const hospitalId = $('#hospital_id').val();
            const roleId = $('#role').val();

            // Validate inputs
            if (!departmentId || !hospitalId || !roleId) {
                alert('Please select a valid department, hospital, and ensure role set.');
                return;
            }

            // Fetch stores based on department
            $.ajax({
                url: '<?php echo base_url("admin/userManagement/fetchDataByRole"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    hospital_id: hospitalId,
                    role_id:roleId,
                    department_id: departmentId,
                },
                success: function(response) {
                    if (response.status === 'success' && response.data.stores) {
                        let storeOptions = '<option value="">Select Store</option>';
                        response.data.stores.forEach(store => {
                            storeOptions += `<option value="${store.id}">${store.store_name}</option>`;
                        });
                        $('#store_id').html(storeOptions);
                        $('#store_section').show(); // Ensure section is visible
                    } else {
                        // alert(response.message || 'No stores available for the selected department.');
                    }
                },
                error: function() {
                    alert('An error occurred while fetching stores.');
                },
            });
        });
        $("#formaddpa").on('submit', (function(e) {
            $('#formaddpabtn').prop('disabled', true);
            $("#formaddpabtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/userManagement/addUser',
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
                        if (data.patientID) {
                            $('#myModalpa').modal('hide');
                            getRevisitRecord(data.patientID);
                        }
                        $("#formaddpabtn").button('reset');
                    } else {
                        successMsg(data.message);

                         window.location.href = "<?php echo base_url(); ?>admin/UserManagement";

                        //$("#formaddpabtn").button('reset');
                    }
                    // $("#formaddpabtn").button('reset');
                },
                error: function() {
                    //  alert("Fail")
                }
            });
        }));
    });

    function addappointmentModal(patient_id = '', modalid) {
        //alert(patient_id);
        var div_data = '';
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getPatientList',
            type: "POST",
            data: '',
            dataType: 'json',
            success: function(data) {
                // $("#addpatient_id").html("");
                if ($('.bulk_form').val() === 'bulk') {

                    $('#addpatient_id_bulk').html(div_data);
                    $('#addpatient_id_bulk_new').html(div_data);
                    $.each(data, function(i, obj) {
                        if (obj.id == patient_id) {
                            ne = 'selected';
                        } else {
                            ne = "";
                        }

                        div_data += "<option value='" + obj.id + "' " + ne + " >" + obj.patient_name + " (" + obj.patient_unique_id + ")" + "</option>";
                    });


                    $('#addpatient_id_bulk').append(div_data);
                    $('#addpatient_id_bulk').select2().select2("val", patient_id);
                    $('#addpatient_id_bulk_new').append(div_data);
                    $('#addpatient_id_bulk_new').select2().select2("val", patient_id);
                    get_PatientDetails(patient_id);
                    $("#" + modalid).modal('show');
                    holdModal(modalid);
                    $('#patho_patientid_bulk').val(patient_id);
                    $('#patho_patientid_bulk_new').val(patient_id);

                } else if ($('.bulk_form').val() === 'bulk_radio') {
                    $('.addpatient_id').html(div_data);
                    $.each(data, function(i, obj) {
                        if (obj.id == patient_id) {
                            ne = 'selected';
                        } else {
                            ne = "";
                        }

                        div_data += "<option value='" + obj.id + "' " + ne + " >" + obj.patient_name + " (" + obj.patient_unique_id + ")" + "</option>";
                    });

                    $('.addpatient_id').append(div_data);
                    $('.addpatient_id').select2().select2("val", patient_id);
                    get_PatientDetails(patient_id);
                    $("#" + modalid).modal('show');
                    holdModal(modalid);
                } else {

                    $('#addpatient_id').html(div_data);
                    $.each(data, function(i, obj) {
                        if (obj.id == patient_id) {
                            ne = 'selected';
                        } else {
                            ne = "";
                        }

                        div_data += "<option value='" + obj.id + "' " + ne + " >" + obj.patient_name + " (" + obj.patient_unique_id + ")" + "</option>";
                    });

                    $('#addpatient_id').append(div_data);
                    $('#addpatient_id').select2().select2("val", patient_id);
                    get_PatientDetails(patient_id);
                    $("#" + modalid).modal('show');
                    holdModal(modalid);

                }

            }
        })
    }

    // $(document).ready(function(){
    //  $("#birth_date").change(function(){
    //  var mdate = $("#birth_date").val().toString();
    //  var yearThen = parseInt(mdate.substring(6,10), 10);
    //  //console.log(yearThen);
    //  var monthThen = parseInt(mdate.substring(0,2), 10);
    //  //console.log(monthThen);
    //  var dayThen = parseInt(mdate.substring(3,5), 10);

    //  var today = new Date();
    //  var birthday = new Date(yearThen, monthThen-1, dayThen);

    //  var differenceInMilisecond = today.valueOf() - birthday.valueOf();

    //  var year_age = Math.floor(differenceInMilisecond / 31536000000);
    //  var day_age = Math.floor((differenceInMilisecond % 31536000000) / 86400000);

    //  var month_age = Math.floor(day_age/30);
    //  console.log("month age",month_age);
    //  day_age = day_age % 30;

    //  if (isNaN(year_age) || isNaN(month_age) || isNaN(day_age)) {
    //  $("#exact_age").text("Invalid birthday - Please try again!");
    //  }
    //  else {
    //  $("#exact_age").html("You are<br/><span id=\"age\">" + year_age + " years " + month_age + " months " + day_age + " days</span> old");

    //  $("#age_year").val(year_age);
    //  $("#age_month").val(month_age);
    //  $("#age_day").val(day_age);

    //  }
    //  });
    //  });

    function CalculateAgeInQC(DOB, txtAge, Txndate) {
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
                    //console.log(age);
                    //console.log(now.getMonth());
                    // console.log(calmonth);
                    $("#age_year").val(age);
                    $("#age_month").val(calmonth);
                    return age;
                    //  document.getElementById(txtAge).value = age;
                    // document.getElementById(txtAge).focus();
                }
            }
        }

        //$("#age_day").val(day_age);
    }
    $(document).ready(function() {
        $("#birth_date").change(function() {
            var mdate = $("#birth_date").val().toString();
            var yearThen = parseInt(mdate.substring(6, 10), 10);
            var dayThen = parseInt(mdate.substring(0, 2), 10);
            var monthThen = parseInt(mdate.substring(3, 5), 10);

            var DOB = dayThen + "/" + monthThen + "/" + yearThen;
            // console.log(DOB);
            CalculateAgeInQC(DOB, '', new Date());
        });
    });
    $(".config").inputmask();
    $(".ager").inputmask('Regex', {
        regex: "^[1-9][0-9][0-9]?$|^"
    });
    $(".month").inputmask('Regex', {
        regex: "^([1-9]|1[011])$"
    });
    $(".day").inputmask('Regex', {
        regex: "^([1-9]|[12][0-9]|3[01])$"
    });
</script>