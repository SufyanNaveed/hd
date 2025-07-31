<div class="content-wrapper">  
    <section class="content">
        <div class="row">
            <div class="col-md-12">              
                <div class="box box-primary" id="departmentList">
                    <div class="box-header ptbnull">
                        <h3 class="box-title">Emergency & ICU Departments</h3>
                        <div class="box-tools pull-right">
                            <a data-toggle="modal" data-target="#addModal" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add Department
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="custom-table table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($departments as $dept) { ?>
                                        <tr>
                                            <td><?php echo $dept->name; ?></td>
                                            <td><?php echo $dept->department_type; ?></td>
                                            <td><?php echo $dept->description; ?></td>
                                            <td class="text-right">
                                                <a data-target="#editModal" onclick="editDepartment(<?php echo $dept->id ?>)" class="btn btn-default btn-xs" data-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a class="btn btn-default btn-xs" data-toggle="tooltip" onclick="deleteDepartment('<?php echo base_url(); ?>hospital/EmergencyIcu/delete/<?php echo $dept->id ?>', 'Are you sure you want to delete this?')";>
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </section>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Add Emergency/ICU Department</h4> 
            </div>
            <form id="formAddDepartment" action="<?php echo site_url('hospital/EmergencyIcu/add') ?>" method="post">
                <div class="modal-body pt0 pb0">  
                    <div class="form-group">
                        <label>Department Name</label><small class="req"> *</small>
                        <input name="name" type="text" class="form-control" required />
                        <span class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label>Department Type</label><small class="req"> *</small>
                        <select name="department_type" class="form-control" required>
                            <option value="Emergency">Emergency</option>
                            <option value="ICU">ICU</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>        
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Edit Emergency/ICU Department</h4> 
            </div>
            <form id="editFormDepartment" action="<?php echo site_url('hospital/EmergencyIcu/update') ?>" method="post">
                <div class="modal-body pt0 pb0">
                    <input type="hidden" id="dept_id" name="id">
                    <div class="form-group">
                        <label>Department Name</label><small class="req"> *</small>
                        <input id="dept_name" name="name" type="text" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Department Type</label><small class="req"> *</small>
                        <select id="dept_type" name="department_type" class="form-control">
                            <option value="Emergency">Emergency</option>
                            <option value="ICU">ICU</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="dept_description" name="description" class="form-control"></textarea>
                    </div>                  
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#formAddDepartment').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.status == "fail") {
                        errorMsg(data.error);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {
                    errorMsg("Error processing request.");
                }
            });
        });

        $('#editFormDepartment').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.status == "fail") {
                        errorMsg(data.message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {
                    errorMsg("Error processing request.");
                }
            });
        });
    });

    function deleteDepartment(url, Msg) {
        if (confirm(Msg)) {
            $.ajax({
                url: url,
                success: function () {
                    successMsg("Deleted successfully");
                    window.location.reload(true);
                }
            });
        }
    }

    function editDepartment(id) {
        $('#editModal').modal('show');
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>hospital/EmergencyIcu/edit/' + id,
            success: function (result) {
                $('#dept_id').val(result.id);
                $('#dept_name').val(result.name);
                $('#dept_type').val(result.department_type);
                $('#dept_description').val(result.description);
            }
        });
    }

    $(".supplier").click(function () {
        $('#formAddDepartment').trigger("reset");
    });
</script>
