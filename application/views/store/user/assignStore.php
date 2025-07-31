<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo form_error('Opd'); ?>
                            Assign Hospital Warehouse/Department/Store
                        </h3>
                        <div class="box-tools pull-right">
                            
                            <!-- <a  href="<?php echo base_url() ?>admin/admin/disablepatient" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('disabled') . " " . $this->lang->line('patient') . " " . $this->lang->line('list'); ?></a>  -->
                        </div>
                    </div>
                    <form id="assignForm" accept-charset="utf-8" action="<?php echo base_url() . "admin/userManagement/saveUserAssignments" ?>" enctype="multipart/form-data" method="post">
                        <input type="hidden" name="bulk_form" id="bulk_form" class="bulk_form">
                        <div class="modal-body pt0 pb0">
                            <div class="ptt10">
                                <input type="hidden" name="user_id" id="user_id" value="<?php echo $user->userId; ?>">
                                <input type="hidden"  id="store_id" value="<?php echo $user->store_id; ?>">
                                <input type="hidden"  id="department_id" value="<?php echo $user->department_id; ?>">


                                <div class="row row-eq">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="row">
                                           
<input type="hidden" name="hospital_id" id="hospital_id" value="<?php echo (!empty($user) && !empty($user->hospital_id)) ? $user->hospital_id : ''; ?>">


                                            <div class="col-lg-3 col-md-3 col-sm-3"
                                                style="<?php echo (!empty($user) && !empty($user->department_id)) ? '' : 'display: none;'; ?>"
                                                id="department_section">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Select Department</label><small class="req"> *</small>
                                                    <select id="department_id" name="department_id" class="form-control select2">
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
                                                    <select id="store_id" name="store_id" class="form-control select2">
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
                                <button type="submit" id="formassignbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </section>
</div>


<script type="text/javascript">

    $(document).ready(function(e) {
        function fetchHospitalData() {
    const hospitalId = $('#hospital_id').val();
    const userId = $('#user_id').val(); // Ensure user ID is available

    // Reset department and store dropdowns and hide sections
    $('#department_id_dropdown').html('<option value="">Select Department</option>');
    $('#department_section').hide();
    $('#store_id_dropdown').html('<option value="">Select Store</option>');
    $('#store_section').hide();

    // Get values from hidden input fields
    const selectedDepartmentId = $('#department_id').val();
    const selectedStoreId = $('#store_id').val();

    // Validate inputs
    if (!hospitalId || !userId) {
        alert('Please select a valid hospital and ensure user ID is set.');
        return;
    }

    // Fetch data based on hospital and user
    $.ajax({
        url: '<?php echo base_url("hospital/hospital/fetchDataByRole"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            hospital_id: hospitalId,
            user_id: userId,
        },
        success: function (response) {
            if (response.status === 'success') {
                const data = response.data;

                // Populate department dropdown if departments exist
                if (data.departments && data.departments.length > 0) {
                    let departmentOptions = '<option value="">Select Department</option>';
                    data.departments.forEach(department => {
                        departmentOptions += `<option value="${department.id}" ${
                            selectedDepartmentId == department.id ? 'selected' : ''
                        }>${department.department_name}</option>`;
                    });
                    $('#department_id_dropdown').html(departmentOptions);
                    $('#department_section').show(); // Ensure section is visible
                }

                // Populate store dropdown if stores exist
                if (data.stores && data.stores.length > 0) {
                    let storeOptions = '<option value="">Select Store</option>';
                    data.stores.forEach(store => {
                        storeOptions += `<option value="${store.id}" ${
                            selectedStoreId == store.id ? 'selected' : ''
                        }>${store.store_name}</option>`;
                    });
                    $('#store_id_dropdown').html(storeOptions);
                    $('#store_section').show(); // Ensure section is visible
                }

                console.log('Data fetched successfully:', data);
            } else {
                alert(response.message || 'Failed to fetch data.');
            }
        },
        error: function () {
            alert('An error occurred while fetching data.');
        },
    });
}

    // Call fetchHospitalData on page load
    fetchHospitalData();
        $("#assignForm").on('submit', (function(e) {
            $('#formassignbtn').prop('disabled', true);
            $("#formassignbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/hospital/saveUserAssignments',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.status == "fail") {
                        var message = "";

                        // Check if data.error is a string
                        if (typeof data.error === "string") {
                            message = data.error; // Use the string directly
                        } else if (typeof data.error === "object") {
                            // Iterate if it's an object or array
                            $.each(data.error, function(index, value) {
                                message += value;
                            });
                        }

                        errorMsg(message); // Display the error message
                        if (data.patientID) {
                            $('#myModalpa').modal('hide');
                            getRevisitRecord(data.patientID);
                        }
                        $("#formassignbtn").button('reset');
                    } else {
                        successMsg(data.message);

                        $("#formassignbtn").button('reset');
                        window.location.reload(true);
                    }
                    // $("#formassignbtn").button('reset');
                },
                error: function() {
                    //  alert("Fail")
                }
            });
        }));
        $('#hospital_id').on('change', function() {
            const hospitalId = $(this).val();
            const userId = $('#user_id').val(); // Ensure user ID is available

            // Reset department and store dropdowns and hide sections
            $('#department_id').html('<option value="">Select Department</option>');
            $('#department_section').hide();
            $('#store_id').html('<option value="">Select Store</option>');
            $('#store_section').hide();

            // Validate inputs
            if (!hospitalId || !userId) {
                alert('Please select a valid hospital and ensure user ID is set.');
                return;
            }

            // Fetch data based on hospital and user
            $.ajax({
                url: '<?php echo base_url("hospital/hospital/fetchDataByRole"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    hospital_id: hospitalId,
                    user_id: userId,
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
                        // alert(response.message || 'Failed to fetch data.');
                    }
                },
                error: function() {
                    alert('An error occurred while fetching data.');
                },
            });
        });

        // Handle Department Change Event
        $('#department_id').on('change', function() {
            const departmentId = $(this).val();
            const hospitalId = $('#hospital_id').val();
            const userId = $('#user_id').val();

            // Validate inputs
            if (!departmentId || !hospitalId || !userId) {
                alert('Please select a valid department, hospital, and ensure user ID is set.');
                return;
            }

            // Fetch stores based on department
            $.ajax({
                url: '<?php echo base_url("admin/userManagement/fetchDataByRole"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    hospital_id: hospitalId,
                    user_id: userId,
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
                        alert(response.message || 'No stores available for the selected department.');
                    }
                },
                error: function() {
                    alert('An error occurred while fetching stores.');
                },
            });
        });
    });
    
</script>