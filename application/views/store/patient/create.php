<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12 col-md-12">
                    <div class=" modalfullmobile" role="document">
                        <div class="modal-content shadow-lg p-4" style="padding-bottom: 10px">

                            <div class="card-header bg-primary text-white p-4" style="padding:10px">
                                <h4 class="mb-0">Patient Registration</h4>
                            </div>
                            <!-- <div class="card border-0 container"> -->
                            <!-- <div class="card-body"> -->


                            <form id="formadd" action="<?php echo base_url('hospital/patient/add_inpatient'); ?>" enctype="multipart/form-data" method="post">
                                <!-- Select Patient Type -->
                                <div class="card border-0">
                                    <div class="card-body" style="padding:25px;background: #d1ecf1 !important;">
                                        <div class="row">
                                            <div class="col-md-2" style="float: right;">
                                                <label for="date">Date</label>
                                                <input name="date" id="datetime" type="datetime-local" class="form-control" />


                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5><b>Select Patient Type:</b></h5>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" name="patient_type" class="form-check-input patient_type" value="cnic_holder" checked>
                                                    <label class="form-check-label">CNIC Holder</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" name="patient_type" class="form-check-input patient_type" value="other">
                                                    <label class="form-check-label">Other</label>
                                                </div>
                                                <button class="btn btn-sm btn-outline-primary ml-3" type="button" id="list_details">List Details</button>
                                            </div>


                                        </div>

                                        <!-- Search Patient Record -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5><b>Search Patient Record</b></h5>
                                                <div class="row">
                                                    <!-- First row with CNIC, Other Nationality & MRN -->
                                                    <div class="col-md-4 col-sm-6 col-12 mb-2" id="cinc_details">
                                                        <label for="cnic">CNIC</label>
                                                        <input type="text" class="form-control config" data-inputmask="'mask': '99999-9999999-9'"
                                                            pattern="\d{5}-\d{7}-\d" placeholder="XXXXX-XXXXXXX-X" id="cnic" name="patient_cnic">
                                                    </div>

                                                    <div class="col-md-4 col-sm-6 col-12 mb-2" id="other_details" style="display: none;">
                                                        <label for="_other_nationality">Other Nationality</label>
                                                        <input type="text" class="form-control" id="_other_nationality" name="other_nationality" placeholder="Other Nationality">
                                                    </div>

                                                    <div class="col-md-4 col-sm-6 col-12 mb-2">
                                                        <label for="mrn">MRN #</label>
                                                        <input type="text" class="form-control" id="mrn" name="mrn" placeholder="Enter MRN">
                                                    </div>
                                                </div>

                                                <!-- Second row for buttons -->
                                                <div class="row mt-3" style="margin-top: 25px;">
                                                    <div class="col-md-2 col-sm-6 col-12" style="float:right;">
                                                        <button type="button" id="search_patient" class="btn btn-primary btn-block">Search</button>
                                                    </div>
                                                    <div class="col-md-2 col-sm-6 col-12" style="float:right;">
                                                        <button type="button" id="reset_form" class="btn btn-secondary btn-block">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body" style="padding:25px;background: #f5c6cb !important;">
                                        <input type="hidden" name="is_ipd" value="<?php echo $ipd; ?>">
                                        <input type="hidden" name="patient_id" id="patient_id">
                                        <!-- Patient Information -->
                                        <div class="row">
                                            <?php if ($this->session->userdata()['hospital']['role'] === "Department Pharmacist"){ ?>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="department_type">
                                                            <?php echo 'Select Department'; ?>
                                                        </label>
                                                        <select class="form-control" id="department_id" name="department_id">
                                                            <option value="">-- Select --</option>
                                                            <?php foreach ($departments as $department) { ?>
                                                                <option value="<?php echo $department['id']; ?>" <?php echo count($departments) == 1 ? "selected='selected'" : ""; ?>>
                                                                    <?php echo $department['department_name']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="department_type">
                                                        <?php echo $ipd ? 'Select ICU' : 'Select Emergency'; ?>
                                                    </label>
                                                    <select class="form-control" id="department_type" name="emergency_icu_id">
                                                        <option value="">-- Select --</option>
                                                        <?php foreach ($resultlist as $dept) { ?>
                                                            <option value="<?php echo $dept['id']; ?>">
                                                                <?php echo $dept['name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if (!$ipd): ?>
                                                <div class="col-md-4">
                                                    <label for="name">Opd Slip No</label>
                                                    <input type="number" class="form-control" id="opd_slip_no" name="opd_slip_no">
                                                </div>
                                            <?php endif; ?>
                                        </div>


                                        <!-- New Patient / Existing Patient Form -->
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <h5><b>New Patient / Existing Patient</b></h5>
                                                <div class="row">
                                                <div class="col-md-4 cnic_details_form">
                                                        <label for="cnic">CNIC</label>
                                                        <input type="text" data-inputmask="'mask': '99999-9999999-9'" pattern="\d{5}-\d{7}-\d" placeholder="XXXXX-XXXXXXX-X" class="form-control config" id="patient_cnic" name="cnic">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="fname">Father's Name</label>
                                                        <input type="text" class="form-control" id="fname" name="father_name">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="mobile">Mobile</label>
                                                        <input type="text" data-inputmask="'mask': '9999-9999999'" pattern="\d{4}-\d{7}" placeholder="XXXX-XXXXXXX" class="form-control config" id="mobile" name="mobile" value="">
                                                        <span id="mobile_error" style="color:red;"></span>
                                                    </div>
                                                    <div class="col-md-4 other_details_form" style="display: none;">
                                                        <label for="country">Country</label>
                                                        <select class="form-control" id="country" name="country">
                                                            <option value="">Select Country</option>
                                                            <option value="Afghanistan">Afghanistan</option>
                                                            <option value="Armenia">Armenia</option>
                                                            <option value="Azerbaijan">Azerbaijan</option>
                                                            <option value="Bahrain">Bahrain</option>
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Bhutan">Bhutan</option>
                                                            <option value="Brunei">Brunei</option>
                                                            <option value="Cambodia">Cambodia</option>
                                                            <option value="China">China</option>
                                                            <option value="Cyprus">Cyprus</option>
                                                            <option value="Georgia">Georgia</option>
                                                            <option value="India">India</option>
                                                            <option value="Indonesia">Indonesia</option>
                                                            <option value="Iran">Iran</option>
                                                            <option value="Iraq">Iraq</option>
                                                            <option value="Israel">Israel</option>
                                                            <option value="Japan">Japan</option>
                                                            <option value="Jordan">Jordan</option>
                                                            <option value="Kazakhstan">Kazakhstan</option>
                                                            <option value="Kuwait">Kuwait</option>
                                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                            <option value="Laos">Laos</option>
                                                            <option value="Lebanon">Lebanon</option>
                                                            <option value="Malaysia">Malaysia</option>
                                                            <option value="Maldives">Maldives</option>
                                                            <option value="Mongolia">Mongolia</option>
                                                            <option value="Myanmar">Myanmar</option>
                                                            <option value="Nepal">Nepal</option>
                                                            <option value="North Korea">North Korea</option>
                                                            <option value="Oman">Oman</option>
                                                            <option value="Pakistan">Pakistan</option>
                                                            <option value="Palestine">Palestine</option>
                                                            <option value="Philippines">Philippines</option>
                                                            <option value="Qatar">Qatar</option>
                                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                                            <option value="Singapore">Singapore</option>
                                                            <option value="South Korea">South Korea</option>
                                                            <option value="Sri Lanka">Sri Lanka</option>
                                                            <option value="Syria">Syria</option>
                                                            <option value="Tajikistan">Tajikistan</option>
                                                            <option value="Thailand">Thailand</option>
                                                            <option value="Timor-Leste">Timor-Leste</option>
                                                            <option value="Turkey">Turkey</option>
                                                            <option value="Turkmenistan">Turkmenistan</option>
                                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                                            <option value="Uzbekistan">Uzbekistan</option>
                                                            <option value="Vietnam">Vietnam</option>
                                                            <option value="Yemen">Yemen</option>
                                                        </select>
                                                    </div>
    
    
                                                    <div class="col-md-4 other_details_form" style="display: none;">
                                                        <label for="other_nationality">Nationality No</label>
                                                        <input type="text" class="form-control" id="other_nationality" name="other_nationality">
                                                    </div>
                                                   
                                                    <div class="col-md-4">
                                                        <label for="age">Age</label>
                                                        <input type="text" class="form-control" id="age" name="age">
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Information -->
                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <label for="gender">Gender</label>
                                                <select class="form-control" id="gender" name="gender">
                                                    <option value="">Select Gender</option>
                                                    <option value="Male" selected>Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="ward">Ward</label>
                                                <input type="text" class="form-control" id="ward" name="ward">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="doctor">Doctor</label>
                                                <input type="text" class="form-control" id="doctor" name="doctor">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="address" name="address">
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label for="remarks">Remarks</label>
                                                <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <?php if (!$ipd) { ?>
                                            <!-- Submit Button -->
                                            <div class="row mt-4">
                                                <div class="col-md-12 text-right mb-5 pb-5">
                                                    <!-- Print Prescription Button -->
                                                    <button type="submit" name="action" value="print_prescription" class="btn btn-info form-submit-btn" style="margin-top:10px">
                                                        Print Prescription
                                                    </button>

                                                    <!-- Print Token Button -->
                                                    <button type="submit" name="action" value="print_token" class="btn btn-info form-submit-btn" style="margin-top:10px">
                                                        Print Token
                                                    </button>

                                                    <!-- <button type="submit" id="formaddbtn" button-attribute="print_prescription"  style="margin-top:10px" class="btn btn-info">Print Prescription</button>
                                                <button type="submit" id="formaddbtn" style="margin-top:10px" class="btn btn-info">Print Token</button> -->

                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <button type="submit" name="action" id="submitBtn" class="btn btn-info" style="margin-top:10px">
                                                Save
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>

                            </form>

                            <!-- </div> -->
                            <!-- </div>  -->
                        </div> <!-- Modal Content -->
                    </div> <!-- Modal Dialog -->
                </div> <!-- Col -->
            </div> <!-- Row -->
        </div> <!-- Container -->
    </section>
</div>

<!-- jQuery (required for Inputmask) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Inputmask CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script> 
<script>
    $(document).on('click', '#list_details', function() {
        const type = $('input[name="patient_type"]:checked').val();
        $('#cinc_details').show()
        $('.cnic_details_form').show()
        $('#other_details').hide()
        $('.other_details_form').hide()



        if (type == 'other') {
            $('#cinc_details').hide()
            $('.cnic_details_form').hide()
            $('#other_details').show()
            $('.other_details_form').show()
        }
    })
    $(document).on('keyup', '#mobile', function(){
        var mobile = $('#mobile').val();
        mobile = mobile.replace('_','0');
        if(mobile.length > 10){
            if(mobile == '0000-0000000' || mobile == '1111-1111111' || mobile == '2222-2222222' || mobile == '3333-3333333' ||
                mobile == '4444-4444444' || mobile == '5555-5555555' || mobile == '6666-6666666' || mobile == '7777-7777777' ||
                mobile == '8888-8888888' || mobile == '9999-999999'){
                $('#mobile').val('');
                $('#mobile_error').text('Mobile no is invalid.')
            }else{
                $('#mobile_error').text('')
            }
        }
    });


    $(document).on('click', '#search_patient', function() {
        var cnic = $('#cnic').val();
        var other_nationality = $('#_other_nationality').val();
        var mrn = $('#mrn').val();
        var $btn = $(this); // Store button reference

        // Disable button and show loading text
        $btn.prop('disabled', true).html('Searching...');

        $.ajax({
            url: '<?php echo base_url(); ?>hospital/patient/getPatientDetail',
            type: 'POST',
            data: {
                cnic: cnic,
                mrn: mrn,
                other_nationality: other_nationality
            },
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);

                if (response.status === "success" && response.data) {
                    let data = response.data;

                    // Populate form fields with patient data
                    $('#name').val(data.patient_name).prop('readonly', true);
                    $('#fname').val(data.father_name).prop('readonly', true);
                    if(data.mobileno){
                        $('#mobile').val(data.mobileno).prop('readonly', true);
                    }else{
                        $('#mobile').val('').prop('readonly', false); // Allow editing if no mobile number
                    }
                    $('#patient_cnic').val(data.patient_cnic).prop('readonly', true);
                    $('#other_nationality').val(data.other_nationality).prop('readonly', true);
                    $('#age').val(data.age).prop('readonly', true);
                    $('#gender').val(data.gender).prop('readonly', true); // Disable select field
                    $('#ward').val(data.ward).prop('readonly', true);
                    $('#doctor').val(data.doctor).prop('readonly', true);
                    $('#address').val(data.address).prop('readonly', true);
                    $('#remarks').val(data.remarks).prop('readonly', true);
                    $('#patient_id').val(data.id);
                    $('#country').val(data.gender).prop('readonly', true)
                    $('#country').val(data.country)


                } else {
                    $('#patient_id').val('');
                    alert("Patient not found!");
                    // Clear all input fields
                    clearFormFields();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert("An error occurred while fetching patient details.");
                clearFormFields(); // Also clear fields in case of an error
            },
            complete: function() {
                // Re-enable button and restore text
                $btn.prop('disabled', false).html('Search');
            }
        });
    });
    $(".config").inputmask();

    // Function to clear all input fields
    function clearFormFields() {
        $('#name, #fname, #mobile, #age, #gender, #ward, #doctor, #address, #remarks').val('');
    }

    function popup(data) {
        var base_url = '<?php echo base_url() ?>';
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({
            "position": "absolute",
            "top": "-1000000px"
        });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function() {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
            window.location.reload(true);
        }, 500);

        return true;

    }
    $(document).ready(function() {
        $("#patient_cnic").on("blur", function() {
        var cnic = $(this).val().trim();

        if (cnic.length === 15) { // Ensure correct CNIC format
            $.ajax({
                url: '<?php echo base_url(); ?>hospital/patient/getPatientDetail',
                type: 'POST',
                data: { cnic: cnic },
                dataType: 'json',
                beforeSend: function() {
                    $("#search_patient").prop('disabled', true).html('Checking...');
                },
                success: function(response) {
                    if (response.status === "success" && response.data) {
                        let data = response.data;

                        // Populate existing patient details
                        $('#patient_id').val(data.id);
                        $('#name').val(data.patient_name).prop('readonly', true);
                        $('#fname').val(data.father_name).prop('readonly', true);
                        if(data.mobileno){
                            $('#mobile').val(data.mobileno).prop('readonly', true);
                        }else{
                            $('#mobile').val('').prop('readonly', false); // Allow editing if no mobile number
                        }
                        $('#age').val(data.age).prop('readonly', true);
                        $('#gender').val(data.gender); // Disable select field
                        $('#ward').val(data.ward).prop('readonly', true);
                        $('#doctor').val(data.doctor).prop('readonly', true);
                        $('#address').val(data.address).prop('readonly', true);
                        $('#remarks').val(data.remarks).prop('readonly', true);

                        // Show a message indicating the patient already exists
                        // alert("Existing patient found! Details auto-filled.");

                    } else {
                        $('#patient_id').val('');

                        // No record found, allow new patient entry
                        alert("No record found. Please enter new patient details.");
                        $("#formadd input, #formadd select, #formadd textarea").prop("readonly", false);

                        // clearFormFields();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert("Error while checking patient record.");
                    clearFormFields();
                },
                complete: function() {
                    $("#search_patient").prop('disabled', false).html('Search');
                }
            });
        }
    });
        $("#reset_form").on("click", function() {
            // Reset all input fields in the form
            $("#formadd")[0].reset();
            $("#patient_id").val('');

            // Clear specific input fields
            $('#cnic, #mrn, #_other_nationality').val('');

            // Re-enable read-only fields in case they were populated
            $("#formadd input, #formadd select, #formadd textarea").prop("readonly", false);

            // Hide/show relevant sections
            $('#cinc_details').show();
            $('.cnic_details_form').show();
            $('#other_details').hide();
            $('.other_details_form').hide();

            // Reset the datetime-local field to Karachi time
            const now = new Date();

            // Convert to Karachi Time (UTC+5)
            const options = {
                timeZone: "Asia/Karachi",
                hour12: false
            };
            const karachiTime = new Date(now.toLocaleString("en-US", options));

            // Format Date & Time as YYYY-MM-DDTHH:MM for datetime-local input
            const year = karachiTime.getFullYear();
            const month = String(karachiTime.getMonth() + 1).padStart(2, "0"); // Month is 0-based
            const day = String(karachiTime.getDate()).padStart(2, "0");
            const hours = String(karachiTime.getHours()).padStart(2, "0");
            const minutes = String(karachiTime.getMinutes()).padStart(2, "0");

            const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

            console.log("Reset to Karachi Time:", formattedDateTime); // Debugging

            // Set the datetime-local input value
            $('input[type="datetime-local"]').val(formattedDateTime);
        });


        function updateDateTime() {
            // Get current time in Karachi Timezone
            const now = new Date();

            // Convert to Karachi Time (UTC+5)
            const options = {
                timeZone: "Asia/Karachi",
                hour12: false
            };
            const karachiTime = new Date(now.toLocaleString("en-US", options));

            // Extract YYYY-MM-DDTHH:MM
            const year = karachiTime.getFullYear();
            const month = String(karachiTime.getMonth() + 1).padStart(2, "0"); // Month is 0-based
            const day = String(karachiTime.getDate()).padStart(2, "0");
            const hours = String(karachiTime.getHours()).padStart(2, "0");
            const minutes = String(karachiTime.getMinutes()).padStart(2, "0");

            const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

            console.log("Karachi Time:", formattedDateTime); // Debugging

            // Set the input value
            document.getElementById("datetime").value = formattedDateTime;
        }

        // Update time every second
        //setInterval(updateDateTime, 1000);

        // Initialize with Karachi time
        updateDateTime();
        $("#formadd").on("submit", function(e) {
            e.preventDefault(); // Prevent default form submission
            var buttonAtr = e.originalEvent.submitter.value;
            if (buttonAtr == 'print_token' || buttonAtr == 'print_prescription') {
                $.ajax({
                    url: "<?php echo base_url('hospital/patient/addIPDOPDPatient'); ?>", // Update with your actual endpoint
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $("#formaddbtn").prop("disabled", true).text("Saving...");
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            successMsg(response.message);
                            var id = '';
                            if (buttonAtr == 'print_prescription') {
                                var base_url = '<?php echo base_url() ?>';
                                $.ajax({
                                    url: base_url + 'hospital/patient/getPrescriptionmanual/' + response.patient_id + '/' + response.opd_id,
                                    type: 'POST',
                                    data: {
                                        payslipid: id,
                                        print: 'yes'
                                    },
                                    //dataType: "json",
                                    success: function(result) {
                                        $("#testdata").html(result);
                                        popup(result);
                                    }
                                });
                            } else if (buttonAtr == 'print_token') {

                                var base_url = '<?php echo base_url() ?>';
                                var url = base_url + 'hospital/patient/printBillInvoice/' + response.patient_id + '/' + response.opd_id + '/' + response.date;
                                setTimeout(() => {
                                    window.open(url, '_blank');

                                }, 2000);
                            }


                            $("#formadd")[0].reset(); // Clear form
                            $("#patient_id").val('');

                            // window.location.href = "<?php echo base_url(); ?>hospital/patient/search";

                        } else {
                            console.log('response.error', response.error)
                            var message = "";
                            $.each(response.error, function(index, value) {
                                message += value;
                            });
                            if (response.message) {
                                errorMsg(response.message);
                            }
                            console.log('message', message)
                            errorMsg(message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert("An error occurred while saving the patient.");
                    },
                    complete: function() {
                        $("#formaddbtn").prop("disabled", false).text("Save");
                    }
                });
            } else {
                $.ajax({
                    url: "<?php echo base_url('hospital/patient/add_inpatient'); ?>", // Update with your actual endpoint
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $("#formaddbtn").prop("disabled", true).text("Saving...");
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            successMsg(response.message);
                            var id = '';



                            $("#formadd")[0].reset(); // Clear form
                            $("#patient_id").val('');

                            // window.location.href = "<?php echo base_url(); ?>hospital/patient/search";

                        } else {
                            console.log('response.error', response.error)
                            var message = "";
                            $.each(response.error, function(index, value) {
                                message += value;
                            });
                            if (response.message) {
                                errorMsg(response.message);
                            }
                            console.log('message', message)
                            errorMsg(message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert("An error occurred while saving the patient.");
                    },
                    complete: function() {
                        $("#formaddbtn").prop("disabled", false).text("Save");
                    }
                });
            }
        });
    });
</script>