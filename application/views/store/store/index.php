<style>
    .select2-container--default.select2-container--open {
        width: 100% !important;
    }

    .select2-container {
        width: 100% !important;
    }
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <!-- <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Store List</h3>
                        <div class="box-tools pull-right">
                            <a data-toggle="modal" onclick="holdModal('storeModal')" id="addStore" class="btn btn-primary btn-sm newstore">
                                <i class="fa fa-plus"></i> Add New Store
                            </a>
                        </div>
                    </div> -->
                    <div class="box-body">
                        <div class="download_label">Store List</div>
                        <table class="custom-table table table-striped table-bordered table-hover test_ajax">
                            <thead>
                                <tr>
                                    <th>Store Unique ID</th>
                                    <th>Store Name</th>
                                    <th>Type</th> <!-- Updated: Displays Hospital or Department -->
                                    <!-- <th>Name</th> Updated: Displays the associated entity name -->
                                    <!-- <th>Action</th> -->
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-toggle="tooltip" title="Close" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-4 col-xs-6">
                        <!-- Additional heading or content can be added here -->
                    </div>
                </div>
            </div><!-- ./modal-header -->

            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . 'admin/store'; ?>" enctype="multipart/form-data" method="post">
                            <input class="" name="id" type="hidden" id="storeid">
                            <div class="row row-eq">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row ptt10">
                                        <!-- Store Info -->
                                        <div class="col-md-9 col-sm-9 col-xs-9" id="Myinfo">
                                            <ul class="singlelist">
                                                <li class="singlelist24bold">
                                                    <b>Store Name:</b> <span id="store_name"></span>
                                                </li>
                                                <li>
                                                    <b>Unique ID:</b> <span id="store_unique_id"></span>
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


<div class="modal fade" id="editModalStore" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">Edit Store</h4>
            </div>
            <div class="modal-body pt0 pb0">
            <form id="formeditpa" accept-charset="utf-8" action="<?php echo base_url(); ?>admin/store/updateStore" enctype="multipart/form-data" method="post">

                <div class="row row-eq">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row">
                            <input type="hidden" name="store_id" class="store_id">
                            <!-- Store Name -->
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>Name</label><small class="req"> *</small>
                                    <input id="store_name" name="store_name" placeholder="Enter store name" type="text" class="form-control store_name" />
                                    <span class="text-danger"><?php echo form_error('store_name'); ?></span>
                                </div>
                            </div>

                            <!-- Entity Type Dropdown -->
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>Type</label><small class="req"> *</small>
                                    <select name="entity_type" class="form-control select2 entity_type">
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
                                            <select  name="entity_id" class="form-control select2 hospital_id">
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
                                            <select  name="department_id" class="form-control select2 department_id">
                                                <option value="">Select Department</option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('department_id'); ?></span>
                                        </div>
                                    </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formeditpabtn" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>





<script type="text/javascript">
     $(".entity_type").on("change", function() {
            if($(this).val() == 'hospital'){
                $('.department-hospital').hide();
            }
            // const $entityIdDropdown = $("#department_id");
            // const hospitalIdDropdown = $('#hospital_id').val();

            // $entityIdDropdown.empty(); 
            // hospitalIdDropdown.empty(); 

        });
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

    function getStoreData(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/store/getStoreDetails',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                $("#store_id").text(data.id);
                $("#store_name").text(data.store_name);
                $("#store_unique_id").text(data.store_unique_id).change();
                $("#hospital_name").text(data.hospital_name).change();
                holdModal('myModal');
            },
        });
    }
    var baseUrl = '<?php echo base_url(); ?>';

    function editRecord(id) {
    $.ajax({
        url: baseUrl + 'admin/store/getStoreDetails', // URL to fetch store details
        type: "POST",
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            if (data) {
                // Populate modal fields
                $(".store_name").val(data.store_name); // Set store name
                $(".store_id").val(data.id); // Set store ID
                $(".entity_type").val(data.entity_type).change(); // Set entity type and trigger change event
                $(".hospital_id").val(data.entity_id).change(); // Set hospital ID and trigger change event

                // Populate entity dropdown dynamically based on entity_type
                if (data.entity_type === 'department') {
                    $.ajax({
                        url: baseUrl + 'admin/store/getEntitiesByType', // Fetch entities based on type
                        type: "POST",
                        data: { entity_type: data.entity_type, hospital_id: data.entity_id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === "success") {
                                $('.department-hospital').show(); // Show dropdown
                                const $entityDropdown = $(".department_id");
                                $entityDropdown.empty().append('<option value="">Select Department</option>'); // Clear existing options
                                $.each(response.data, function(key, entity) {
                                    $entityDropdown.append(`<option value="${entity.id}">${entity.name}</option>`);
                                });
                                $entityDropdown.val(data.department_id).change(); // Set selected value
                            } else {
                                errorMsg(response.message);
                            }
                        },
                        error: function() {
                            errorMsg('Failed to fetch departments.');
                        }
                    });
                } else {
                    $('.department-hospital').hide(); // Hide dropdown if not a department
                }

                // Show the modal
                $("#editModalStore").modal('show');
            } else {
                errorMsg('Failed to fetch store details.');
            }
        },
        error: function() {
            errorMsg('An error occurred while fetching store data.');
        }
    });
}






    $(document).ready(function(e) {
        $("#formeditpa").on('submit', (function(e) {
            $("#formeditpabtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/store/updateStore',
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
        if (confirm('Are you sure you want to delete this store?')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/store/deleteStore',
                type: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    alert('Store deleted successfully');
                    location.reload();
                },
            });
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

    //     $(".entity_type").on("change", function () {
    //     const entityType = $(this).val();
    //     const $entityIdDropdown = $(".entity_id");
    //     $entityIdDropdown.empty(); // Clear previous options

    //     if (entityType) {
    //         $.ajax({
    //             url: '<?php echo base_url(); ?>admin/store/getEntitiesByType', // Backend endpoint to fetch entities
    //             type: 'POST',
    //             data: { entity_type: entityType },
    //             dataType: 'json',
    //             success: function (response) {
    //                 if (response.status === "success") {
    //                     $entityIdDropdown.append('<option value="">Select Entity</option>');
    //                     $.each(response.data, function (key, entity) {
    //                         $entityIdDropdown.append(`<option value="${entity.id}">${entity.name}</option>`);
    //                     });
    //                 } else {
    //                     errorMsg(response.message);
    //                 }
    //             },
    //             error: function () {
    //                 errorMsg("An error occurred while fetching entities.");
    //             },
    //         });
    //     } else {
    //         $entityIdDropdown.append('<option value="">Select Entity</option>');
    //     }
    // });

        $('.test_ajax').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo base_url(); ?>hospital/store/store_list',
                type: 'POST',
            },
            columns: [{
                    data: 'store_unique_id',
                    title: 'Store ID', // Update column header
                },
                {
                    data: 'store_name',
                    title: 'Store Name', // Update column header
                },
                {
                    data: 'entity_type',
                    title: 'Type', // New column for entity type (e.g., Hospital/Department)
                },
                // {
                //     data: 'action',
                //     title: 'Actions', // Update column header for action buttons
                //     orderable: false, // Disable sorting for actions
                //     searchable: false, // Disable searching for actions
                // },
            ],
        });
        $(".hospital_id").on("change", function() {
            const entityType = $('.entity_type').val();

            const $entityIdDropdown = $(".department_id");
            const hospitalId = $('.hospital_id').val();

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
<?php $this->load->view('admin/store/storeModal') ?>