<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
<?php
$genderList = $this->customlib->getGender_Patient();
$marital_status = $this->config->item('marital_status');
$bloodgroup = $this->config->item('bloodgroup');
?>
<div class="modal fade" id="myModalpa" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " Hospital"  ?></h4>
            </div>
            <form id="formaddpa" accept-charset="utf-8" action="<?php echo base_url() . "admin/hospital/addHospital" ?>" enctype="multipart/form-data" method="post">
            <input type="hidden" name="bulk_form" id="bulk_form" class="bulk_form">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">

                        <div class="row row-eq">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                            <input id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                                        </div>
                                    </div>
                                 
                                   


                                    


                                 
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                            <input id="number" autocomplete="off" name="mobileno" data-inputmask="'mask': '9999-9999999'" pattern="\d{4}-\d{7}" placeholder="XXXX-XXXXXXX"  type="text" placeholder="" class="form-control config"  value="<?php echo set_value('mobileno'); ?>" />
                                        </div>
                                    </div>


                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address"><?php echo $this->lang->line('address'); ?></label>
                                            <input name="address" placeholder="" class="form-control" /><?php echo set_value('address'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                        Hospital Photo
                                                    </label>
                                                    <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' data-height="26" />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('file'); ?></span>
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


<script type="text/javascript">
    $(document).ready(function (e) {
        $("#formaddpa").on('submit', (function (e) {
            $('#formaddpabtn').prop('disabled', true);
            $("#formaddpabtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/hospital/addHospital',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                        if(data.patientID){
                            $('#myModalpa').modal('hide');
                            getRevisitRecord(data.patientID);
                        }
                        $("#formaddpabtn").button('reset');
                    } else {
                        successMsg(data.message);
                        $("#myModalpa").modal('toggle');
                        if($('.bulk_form').val()=='bulk'){
                            addappointmentModal(data.id, 'addPathologyTestReport');
                        }
                        else if($('.bulk_form').val()=='bulk_radio'){
                            addappointmentModal(data.id, 'myModalBulk');
                        }
                        else{
                            addappointmentModal(data.id, 'myModal');
                        }
                        //$("#formaddpabtn").button('reset');
                       window.location.reload(true);
                    }
                   // $("#formaddpabtn").button('reset');
                },
                error: function () {
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
            success: function (data) {
                // $("#addpatient_id").html("");
                if($('.bulk_form').val()==='bulk'){

                    $('#addpatient_id_bulk').html(div_data);
                    $('#addpatient_id_bulk_new').html(div_data);
                    $.each(data, function (i, obj)
                    {
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

                }
                else if($('.bulk_form').val()==='bulk_radio')
                {
                    $('.addpatient_id').html(div_data);
                    $.each(data, function (i, obj)
                    {
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
                }
                else{

                    $('#addpatient_id').html(div_data);
                    $.each(data, function (i, obj)
                    {
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
    $(document).ready(function () {
        $("#birth_date").change(function () {
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
    $(".ager").inputmask('Regex', { regex: "^[1-9][0-9][0-9]?$|^" });
    $(".month").inputmask('Regex', { regex: "^([1-9]|1[011])$" });
    $(".day").inputmask('Regex', { regex: "^([1-9]|[12][0-9]|3[01])$" });
    $('.select2').select2();
</script>