<div class="modal fade" id="storeModal" role="dialog" aria-labelledby="storeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " Store"; ?></h4>
            </div>
            <form id="formAddStore" accept-charset="utf-8" action="<?php echo base_url() . "admin/store/addStore" ?>" method="post">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="row row-eq">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <!-- Store Name -->
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label>Name</label><small class="req"> *</small>
                                            <input id="store_name" name="store_name" placeholder="Enter store name" type="text" class="form-control" />
                                            <span class="text-danger"><?php echo form_error('store_name'); ?></span>
                                        </div>
                                    </div>

                                    <!-- Entity Type Dropdown -->
                                    <div class="col-lg-6 col-md-6 col-sm-6" >
                                        <div class="form-group">
                                            <label>Type</label><small class="req"> *</small>
                                            <select id="entity_type" name="entity_type" class="form-control select2">
                                                <option value="">Select Type</option>
                                                <option value="hospital">Hospital</option>
                                                <option value="department">Department</option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('entity_type'); ?></span>
                                        </div>
                                    </div>


                                    <div class="col-lg-6 col-md-6 col-sm-6 ">
                                        <div class="form-group">
                                            <label>Hospital</label><small class="req"> *</small>
                                            <select id="hospital_id" name="entity_id" class="form-control select2">
                                                <option value="">Select Hospital</option>
                                                <?php foreach ($hospitals as $hospital): ?>
                                                    <option value="<?php echo $hospital['id']; ?>"><?php echo $hospital['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('entity_id'); ?></span>
                                        </div>
                                    </div>

                                    <!-- Entity ID Dropdown -->
                                    <div class="col-lg-6 col-md-6 col-sm-6 department-hospital" style="display: none;">
                                        <div class="form-group">
                                            <label>Department</label><small class="req"> *</small>
                                            <select id="department_id" name="department_id" class="form-control select2">
                                                <option value="">Select Department</option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('department_id'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- ./row -->
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formAddStoreBtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2 for dropdowns
        $(".select2").select2();
        $("#entity_type").on("change", function() {
            const entityType = $('#entity_type').val();
            if(entityType == 'hospital'){
                $('.department-hospital').hide();
            }
            // const $entityIdDropdown = $("#department_id");
            // const hospitalIdDropdown = $('#hospital_id').val();

            // $entityIdDropdown.empty(); 
            // hospitalIdDropdown.empty(); 

        });
        // Fetch entities based on selected entity type
        $("#hospital_id").on("change", function() {
            const entityType = $('#entity_type').val();

            const $entityIdDropdown = $("#department_id");
            const hospitalId = $('#hospital_id').val();

            $entityIdDropdown.empty(); // Clear previous options
            $('.department-hospital').hide();

            if (entityType == 'department') {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/store/getEntitiesByType', // Backend endpoint to fetch entities
                    type: 'POST',
                    data: {
                        entity_type: entityType,
                        hospital_id :hospitalId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === "success") {
                            $('.department-hospital').show();
                            $entityIdDropdown.append('<option value="">Select Department</option>');
                            $.each(response.data, function(key, entity) {
                                $entityIdDropdown.append(`<option value="${entity.id}">${entity.name}</option>`);
                            });
                        } else {
                            errorMsg(response.message);
                        }
                    },
                    error: function() {
                        errorMsg("An error occurred while fetching entities.");
                    },
                });
            } else {
                $entityIdDropdown.append('<option value="">Select Department</option>');
            }
        });

        // Submit the store form via AJAX
        $("#formAddStore").on("submit", function(e) {
            e.preventDefault(); // Prevent default form submission
            $("#formAddStoreBtn").prop("disabled", true).button("loading"); // Disable the button and show loading text

            $.ajax({
                url: '<?php echo base_url(); ?>admin/store/addStore',
                type: "POST",
                data: $(this).serialize(), // Serialize form data
                dataType: "json",
                success: function(response) {
                    $("#formAddStoreBtn").prop("disabled", false).button("reset"); // Enable the button

                    if (response.status === "fail") {
                        let message = "";
                        $.each(response.error, function(index, value) {
                            message += value + "<br>";
                        });
                        errorMsg(message); // Show error message
                    } else {
                        successMsg(response.message); // Show success message
                        $("#storeModal").modal("hide"); // Hide the modal
                        window.location.reload(true); // Reload the page
                    }
                },
                error: function() {
                    $("#formAddStoreBtn").prop("disabled", false).button("reset");
                    errorMsg("An error occurred. Please try again."); // Show generic error message
                },
            });
        });
    });
</script>