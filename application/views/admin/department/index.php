
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Department List</h3>
                        <div class="box-tools pull-right">
                            <a data-toggle="modal" onclick="holdModal('addDepartmentModal')" id="addDepartment" class="btn btn-primary btn-sm newdepartment">
                                <i class="fa fa-plus"></i> Add New Department
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="download_label">Department List</div>
                        <table class="custom-table table table table table-striped table-bordered table-hover test_ajax">
                            <thead>
                                <tr>
                                    <th>Department Unique ID</th>
                                    <th>Department Name</th>
                                    <th>Hospital Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="departmentModal" role="dialog" aria-labelledby="departmentModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-toggle="tooltip" title="Close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Department Details</h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formViewDepartment" accept-charset="utf-8" enctype="multipart/form-data" method="post">
                            <!-- <input type="hidden" id="department_id" name="department_id"> -->
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <!-- Department Info -->
                                        <div class="col-md-9 col-sm-9 col-xs-9" id="Myinfo">
                                            <ul class="singlelist">
                                                <li class="singlelist24bold">
                                                    <b>Department Name:</b> <span id="department_name"></span>
                                                </li>
                                                <li>
                                                    <b>Unique ID:</b> <span id="department_unique_id"></span>
                                                </li>
                                            </ul>
                                            <ul class="multilinelist">
                                                <li>
                                                    <b>Hospital Name:</b> <span id="hospital_name"></span>
                                                </li>
                                            </ul>
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


<div class="modal fade" id="editModalDepartment" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Edit Department</h4>
            </div>
            <div class="modal-body pt0 pb0">
                <form id="formEditDepartment" action="<?php echo base_url(); ?>admin/department/updateDepartment" enctype="multipart/form-data" method="post">
                    <input type="hidden" id="department_id" name="department_id">
                    <div class="row">
                        <!-- Department Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_department_name">Department Name</label><small class="req"> *</small>
                                <input id="edit_department_name" name="department_name" placeholder="Enter department name" type="text" class="form-control" />
                            </div>
                        </div>
                        <!-- Store Dropdown -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_hospital_id">Select Hospital</label><small class="req"> *</small>
                                <select id="edit_hospital_id" name="hospital_id" class="form-control">
                                    <!-- Populate dynamically from backend -->
                                    <?php foreach ($hospitals as $hospital): ?>
                                        <option value="<?php echo $hospital['id']; ?>"><?php echo $hospital['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" id="formeditpabtn" class="btn btn-primary pull-right">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        // Load DataTable for departments
        $('.test_ajax').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo base_url(); ?>admin/department/department_list',
                type: 'POST',
            },
            columns: [{
                    data: 'department_unique_id'
                },
                {
                    data: 'department_name'
                },
                {
                    data: 'hospital_name'
                },
                {
                    data: 'action'
                },
            ],
        });




        // Add Department Form Submission
        // $("#formAddDepartment").on('submit', function(e) {
        //     e.preventDefault();
        //     $.ajax({
        //         url: '<?php echo base_url(); ?>admin/department/addDepartment',
        //         type: 'POST',
        //         data: $(this).serialize(),
        //         dataType: 'json',
        //         success: function(response) {
        //             if (response.status === 'success') {
        //                 successMsg(response.message);
        //                 location.reload();
        //             } else {
        //                 errorMsg(response.message);
        //             }
        //         },
        //     });
        // });

        $("#formEditDepartment").on('submit', (function(e) {
            $("#formeditpabtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/department/updateDepartment',
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

    function holdModal(modalId) {
        $('#' + modalId).modal('show');
    }

    function getDepartmentData(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/department/getDepartmentDetails',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                // Populate the modal fields with department data
                $("#department_id").val(data.id);
                $("#department_name").text(data.department_name);
                $("#department_unique_id").text(data.department_unique_id);
                $("#hospital_name").text(data.hospital_name);

                // Show the modal
                $('#departmentModal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            },
            error: function() {
                alert('Failed to fetch department details.');
            }
        });
    }

    function editRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/department/getDepartmentDetails', // URL for department details
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                // Populate modal fields with department data
                $("#department_id").val(data.id);
                $("#edit_department_name").val(data.department_name);

                // Set store dropdown and show store name
                $("#edit_hospital_id").val(data.hospital_id).change(); // Select the appropriate store in the dropdown
                $("#edit_store_name").text(data.store_name); // Show store name in plain text

                // Show the edit modal
                holdModal('editModalDepartment');
            },
            error: function() {
                alert('Failed to fetch department data.');
            }
        });
    }

    function deleteRecord(id) {
        if (confirm('Are you sure you want to delete this department?')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/department/deleteDepartment',
                type: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        successMsg(response.message);
                        location.reload();
                    } else {
                        errorMsg(response.message);
                    }
                },
            });
        }
    }
</script>
<?php $this->load->view('admin/department/deparmentModal') ?>
