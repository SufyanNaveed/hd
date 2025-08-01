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
                        <div class="" id="editModal" role="dialog" aria-labelledby="myModalLabel">
                            <div class="" role="document">
                                <div class="modal-content modal-media-content">
                                    <div class="modal-header modal-media-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="box-title"> <?php echo $this->lang->line('user') . " " . $this->lang->line('information'); ?></h4>
                                    </div><!--./modal-header-->
                                    <div class="modal-body pt0 pb0">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <form id="formeditUser" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                                                <input id="eupdateid" name="updateid" placeholder="" type="hidden" class="form-control" value="<?php echo isset($user->userId) ? $user->userId : ''; ?>" />
<div class="row row-eq">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="row ptt10">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                    <input id="ename" name="name" placeholder="" type="text" class="form-control" value="<?php echo isset($user->username) ? $user->username : set_value('name'); ?>" />
                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <label>Father Name</label><small class="req"> *</small>
                    <input id="father_name" name="father_name" placeholder="" type="text" class="form-control" value="<?php echo isset($user->father_name) ? $user->father_name : set_value('father_name'); ?>" />
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
                            $selected = isset($user->role_id) && $user->role_id == $role['id'] ? 'selected' : set_select('role', $role['id']);
                        ?>
                            <option value="<?php echo $role['id'] ?>" <?php echo $selected; ?>><?php echo $role["name"] ?></option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo form_error('role'); ?></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <label>Username <small class="req"> *</small></label>
                    <input type="text" id="user_cnic" name="user_cnic" data-inputmask="'mask': '99999-9999999-9'" pattern="\d{5}-\d{7}-\d" placeholder="XXXXX-XXXXXXX-X" class="form-control config" value="<?php echo isset($user->cnic) ? $user->cnic : ''; ?>" />
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <label for="mobileno">Phone <small class="req"> *</small></label>
                    <input id="mobileno" autocomplete="off" name="mobileno" data-inputmask="'mask': '9999-9999999'" pattern="\d{4}-\d{7}" placeholder="XXXX-XXXXXXX" type="text" class="form-control config" value="<?php echo isset($user->mobileno) ? $user->mobileno : set_value('mobileno'); ?>" />
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <label>Shift Start Time</label><small class="req"> *</small>
                    <input type="time" name="shift_start_time" class="form-control" required value="<?php echo isset($user->shift_start_time) ? $user->shift_start_time : ''; ?>" />
                    <span class="text-danger"><?php echo form_error('shift_start_time'); ?></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="form-group">
                    <label>Shift End Time</label><small class="req"> *</small>
                    <input type="time" name="shift_end_time" class="form-control" required value="<?php echo isset($user->shift_end_time) ? $user->shift_end_time : ''; ?>" />
                    <span class="text-danger"><?php echo form_error('shift_end_time'); ?></span>
                </div>
            </div>
        </div><!-- ./row -->
    </div><!-- ./col-md-8 -->
</div><!-- ./row -->

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
                                                                        <select id="department_id" name="department_id[]" class="form-control select2" multiple="multiple">
                                                                            <option value="">Select Department</option>
                                                                            <?php if (!empty($departments)) { ?>
                                                                                <?php foreach ($departments as $department) { ?>
                                                                                    <option value="<?php echo $department['id']; ?>"
                                                                                        <?php echo (!empty($userDepartments) && in_array($department['id'], $userDepartments)) ? 'selected' : ''; ?>>
                                                                                        <?php echo $department['department_name']; ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <!-- <div class="col-lg-3 col-md-3 col-sm-3"
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
                                                                </div> -->
 
                                                                <div class="col-lg-3 col-md-3 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Password</label>
                                                                        <input type="password" id="password" name="password" class="form-control" />
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-3 col-md-3 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Confirm Password</label>
                                                                        <input type="password" id="con_password" name="con_password" class="form-control"/>
                                                                        <span id="con_password_error"></span>
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
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<script>

    $(document).ready(function(e) {
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2(); 
        });
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

        $(document).ready(function(e) {
        $("#formeditUser").on('submit', (function(e) {

            if($('#password').val() != $('#con_password').val()){
                $('#con_password_error').text('Passwords do not match');
                $('#con_password_error').css('color','red');
                return false;
            }else{
                $('#con_password_error').text('');
            }
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

    $('#password').on('keyup', function(){
        $('#con_password').attr('required','required');
    })
</script>