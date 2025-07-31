
<style>
    .select2-container--default.select2-container--open {
        width: 100% !important;
    }

    .select2-container {
        width: 100% !important;
    }
</style>
<div class="modal fade" id="addDepartmentModal" role="dialog" aria-labelledby="departmentModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " Department"; ?></h4>
            </div>
            <form id="formAddDepartment" accept-charset="utf-8" action="<?php echo base_url() . "admin/department/addDepartment" ?>" method="post">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="row row-eq">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <!-- Department Name -->
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Department Name</label><small class="req"> *</small>
                                            <input id="department_name" name="department_name" placeholder="Enter department name" type="text" class="form-control" />
                                            <span class="text-danger"><?php echo form_error('department_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Hospital</label><small class="req"> *</small>
                                            <select id="hospital_id" name="hospital_id" class="form-control select2">
                                                <option value="">Select Hospital</option>
                                                <?php foreach ($hospitals as $hospital): ?>
                                                    <option value="<?php echo $hospital['id']; ?>"><?php echo $hospital['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('hospital_id'); ?></span>
                                        </div>
                                    </div>

                                    <!-- Store Dropdown -->
                                    <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Hospital</label><small class="req"> *</small>
                                            <select id="hospital_id" name="hospital_id" class="form-control select2">
                                                <option value="">Select Hospital</option>
                                                <?php foreach ($hospitals as $hospital): ?>
                                                    <option value="<?php echo $hospital['id']; ?>"><?php echo $hospital['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('hospital_id'); ?></span>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div><!-- ./row -->
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formAddDepartmentBtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Initialize Select2 for dropdowns
        $(".select2").select2();

        // Submit the department form via AJAX
        $("#formAddDepartment").on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            $("#formAddDepartmentBtn").prop('disabled', true).button('loading'); // Disable the button and show loading text

            $.ajax({
                url: '<?php echo base_url(); ?>admin/department/addDepartment',
                type: "POST",
                data: $(this).serialize(), // Serialize form data
                dataType: 'json',
                success: function (response) {
                    $("#formAddDepartmentBtn").prop('disabled', false).button('reset'); // Enable the button

                    if (response.status === "fail") {
                        let message = "";
                        $.each(response.error, function (index, value) {
                            message += value + "<br>";
                        });
                        errorMsg(message); // Show error message
                    } else {
                        successMsg(response.message); // Show success message
                        $("#addDepartmentModal").modal('hide'); // Hide the modal
                        window.location.reload(true); // Reload the page
                    }
                },
                error: function () {
                    $("#formAddDepartmentBtn").prop('disabled', false).button('reset');
                    errorMsg("An error occurred. Please try again."); // Show generic error message
                }
            });
        });
    });
</script>
