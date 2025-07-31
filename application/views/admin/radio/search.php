<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<style>
div.test_check {
    /* margin:4px, 4px;
                padding:4px;
                height: 100vh;
                overflow-x: hidden;
                overflow-y: auto;
                text-align:justify; */

    height: 80vh;
    overflow-x: hidden;
    overflow-y: scroll;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-lite.min.js"></script>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('radiology') . " " . $this->lang->line('test'); ?></h3>
                        <div class="box-tools pull-right">
                        <a onclick="addTestReport('','modal')" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo "Add Multiple Tests of One Patient"; ?></a>
                            <?php if ($this->rbac->hasPrivilege('radiology test', 'can_add')) { ?>
                                <a type="button" href="<?php echo site_url('admin/radio/exportformat')?>" class="btn btn-info btn-sm " autocomplete="off"><i class="fa fa-upload"></i> Download Sample Data</a>
                                <a data-toggle="modal" onclick="holdModal('importTestRadiology')"
                                class="btn btn-primary btn-sm pathology"><i class="fa fa-plus"></i>
                                <?php echo $this->lang->line('add') . " " . $this->lang->line('test') . " " . $this->lang->line('import'); ?></a>
                                <a data-toggle="modal" onclick="holdModal('addTestReportModal')" class="btn btn-primary btn-sm radiology"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('radiology') . " " . $this->lang->line('test'); ?></a>
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_view')) { ?>
                                <a href="<?php echo base_url(); ?>admin/radio/getTestReportBatch" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('test') . " " . $this->lang->line('report'); ?></a>
                            <?php } ?>



                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('radiology') . " " . $this->lang->line('test'); ?></div>
                        <table class="custom-table table table-striped table-bordered table-hover test_ajax" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('test') . " " . $this->lang->line('type'); ?></th>
                                    <th><?php echo $this->lang->line('category'); ?></th>
                                    <th><?php echo $this->lang->line('sub') . " " . $this->lang->line('category'); ?></th>
                                    <th><?php echo $this->lang->line('report') . " " . $this->lang->line('days'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('charge') . " (" . $currency_symbol . ")"; ?></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="addTestReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('test') . " " . $this->lang->line('details'); ?></h4>
            </div>
             <form id="formadd" accept-charset="utf-8"  method="post" class="ptt10" >
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></label>
                                        <small class="req"> *</small>
                                        <input type="text" name="test_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('test_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></label>
                                        <small class="req"> *</small>
                                        <input type="text" name="short_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('short_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('test') . " " . $this->lang->line('type'); ?></label>
                                        <small class="req"> *</small>
                                        <input type="text" name="test_type" class="form-control">
                                        <span class="text-danger"><?php echo form_error('test_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('category') . " " . $this->lang->line('name'); ?></label>
                                        <small class="req"> *</small>
                                        <div>
                                            <select class="form-control select2" style="width: 100%" name='radiology_category_id' >
                                                <option value="<?php echo set_value('radio_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($categoryName as $dkey => $dvalue) {
                                                    ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["lab_name"] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('radio_category_id'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('sub') . " " . $this->lang->line('category'); ?></label>
                                        <input type="text" name="sub_category" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('report') . " " . $this->lang->line('days'); ?></label>
                                        <input type="text" name="report_days" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>
                                        <small class="req">*</small>
                                        <div>
                                            <select class="form-control" onchange="getchargecode(this.value)" name='charge_category_id' >
                                                <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($charge_category as $dkey => $dvalue) {
                                                    ?>
                                                    <option value="<?php echo $dvalue["name"]; ?>"><?php echo $dvalue["name"] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('code'); ?></label>
                                        <small class="req">*</small>
                                        <div>
                                            <select class="form-control select2" name='code' style="width: 100%" onchange="getchargeDetails(this.value,'standard_charge')" id="code" >
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                                        <small class="req">*</small>
                                        <div>
                                            <input class="form-control"  name='standard_charge' id="standard_charge" readonly="true">

                                        </div>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="test_description" class="test_description" id="test_description"></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?>
                                        </span>
                                    </div>
                                </div>


                            </div><!--./row-->

                    </div><!--./col-md-12-->
                </div><!--./row-->
            </div>
             <div class="divider"></div>
             <div class="col-md-12" style="clear:both;">
                                    <div class="">
                                        <table class="custom-table table table-striped table-bordered table-hover" id="tableID">
                                            <thead>
                                                <tr style="font-size: 13px">
                                                    <th><?php echo $this->lang->line('test') . " " .$this->lang->line('parameter') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
                                                    <th><?php echo $this->lang->line('refference') . " " . $this->lang->line('range'); ?></th>
                                                    <th><?php echo $this->lang->line('unit') ; ?><small class="req" style="color:red;"> *</small></th>
                                                </tr>
                                            </thead>
                                            <tr id="row0">
                                                <td width="35%">
                                                    <select class="form-control select2" style="width:100%" onchange="getparameterdetails(this.value, 0)" id="parameter_name0" name='parameter_name[]'>
                                                       <option value="<?php echo set_value('radiology_parameter_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($parametername as $dkey => $dvalue) {
                                                            ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["parameter_name"] ?></option>
                                                        <?php } ?>

                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('parameter_name[]'); ?></span>
                                                </td>
                                                <td width="30%">
                                                    <input type="text" readonly="" name="reference_range[]"  id="reference_range0" class="form-control">
                                                </td>
                                                <td width="30%">
                                                    <input type="text" readonly="" name="radio_unit[]"  id="radio_unit0" class="form-control">
                                                </td>
                                                <td><button type="button" onclick="addMore()" style="color: #2196f3" class="closebtn"><i class="fa fa-plus"></i></button></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div><!--./col-md-12-->
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formaddbtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- dd -->
<div class="modal fade" id="myModaledit" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit') . " " . $this->lang->line('test') . " " . $this->lang->line('information'); ?></h4>
            </div>
            <form  id="formedit" accept-charset="utf-8"  method="post" class="">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="row">
                            <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></label>
                                    <small class="req"> *</small>
                                    <input type="text" name="test_name" id="test_name" class="form-control" value="<?php echo set_value('test_name'); ?>">
                                    <span class="text-danger"><?php echo form_error('test_name'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></label>
                                    <small class="req"> *</small>
                                    <input type="text" name="short_name" id="short_name" class="form-control" value="<?php echo set_value('short_name'); ?>">
                                    <span class="text-danger"><?php echo form_error('short_name'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('test') . " " . $this->lang->line('type') ?></label>
                                    <small class="req"> *</small>
                                    <input type="text" name="test_type" id="test_type" class="form-control" value="<?php echo set_value('test_type'); ?>">
                                    <span class="text-danger"><?php echo form_error('test_type'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="exampleInputFile">
                                        <?php echo $this->lang->line('category') . " " . $this->lang->line('name') ?></label>
                                    <small class="req"> *</small>
                                    <div>
                                        <select class="form-control select2" style="width: 100%" name='radiology_category_id' id="radiology_category_id">
                                            <option value="<?php echo set_value('radiology_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($categoryName as $dkey => $dvalue) {
                                                ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["lab_name"] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('sub') . " " . $this->lang->line('category') ?></label>
                                    <input type="text" name="sub_category" id="sub_category" class="form-control" value="<?php echo set_value('sub_category'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('report') . " " . $this->lang->line('days') ?></label>
                                    <input type="text" name="report_days" id="report_days" class="form-control" value="<?php echo set_value('report_days'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="exampleInputFile"><?php echo $this->lang->line('charge') . " " . $this->lang->line('category') ?></label>
                                    <small class="req">*</small>
                                    <div>
                                        <select class="form-control" name='charge_category_id' id="edit_charge_category" onchange="editchargecode(this.value)" >
                                            <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($charge_category as $dkey => $dvalue) {
                                                ?>
                                                <option value="<?php echo $dvalue["name"]; ?>"><?php echo $dvalue["name"] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="exampleInputFile"><?php echo $this->lang->line('code') ?></label>
                                    <small class="req">*</small>
                                    <div>
                                        <select class="form-control select2" style="width: 100%" name='charge_category_id' id="edit_code" onchange="getchargeDetails(this.value,'edit_standard_charge')" >
                                            <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="exampleInputFile"><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                                    <small class="req">*</small>
                                    <div>
                                        <input class="form-control" name='standard_charge' id="edit_standard_charge" readonly="true">

                                    </div>
                                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="test_description" class="test_description" id="edit_test_description"></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?>
                                        </span>
                                    </div>
                            </div>

                        </div><!--./row-->

                            <div class="divider"></div>
                                <div class="" id="edit_parameter_details">
                                </div>

                    </div><!--./row-->
                </div>
                <div class="box-footer">
                    <div class="pull-right ">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formeditbtn" class="btn btn-info pull-right" ><?php echo $this->lang->line('save') ?></button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_delete'>
                        <a href="#"  data-target="#editModal" data-toggle="modal" title="" data-original-title="Edit"><i class="fa fa-pencil"></i></a>

                        <a href="#" data-toggle="tooltip" title="" data-original-title="Delete"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="box-title"><?php echo $this->lang->line('test') . " " . $this->lang->line('information') ?></h4>
            </div>
            <form id="view" accept-charset="utf-8" method="get" class="">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="table-responsive">
                            <table class="custom-table table mb0 table-striped table-bordered examples ">
                                <tr>
                                    <th width="25%"><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th>
                                    <td width="25%"><span id='test_names'></span></td>
                                    <th width="25%"><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></th>
                                    <td width="25%"><span id="short_names"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="25%"><?php echo $this->lang->line('test') . " " . $this->lang->line('type'); ?></th>
                                    <td width="25%"><span id='test_types'></span></td>
                                    <th width="25%"><?php echo $this->lang->line('category') . " " . $this->lang->line('name'); ?></th>
                                    <td width="25%"><span id="radiology_category_ids"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="25%"><?php echo $this->lang->line('sub') . " " . $this->lang->line('category'); ?></th>
                                    <td width="25%"><span id="sub_categorys"></span>
                                    <th width="25%"><?php echo $this->lang->line('report') . " " . $this->lang->line('days'); ?></th>
                                    <td width="25%"><span id='report_dayss'></span></td>
                                </tr>
                                <tr>
                                    <th width="25%"><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></th>
                                    <td width="25%"><span id='charge_categorys'></span></td>
                                    <th width="25%"><?php echo $this->lang->line('code'); ?></th>
                                    <td width="25%"><span id="codes"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                                    <td><span id='stdcharge'></span></td>
                                    </td>
                                </tr>

                            </table>
                            <table class="custom-table table mb0 table-striped table-bordered examples ">
                                <tr>
                                    <th width="30%"><?php echo 'Test Description'; ?></th>
                                    <td width="70%"><span id='testdscp'></span></td>
                                    </td>
                                </tr>
                            </table>
                                <div class="" id="parameterview">
                                </div>
                        </div>

                    </div><!--./row-->
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group15">
                          <div>
                                <select onchange="get_PatientDetails(this.value)" style="width:100%" class="form-control select2"  name='patient_id' id="addpatient_id" >
                                    <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                    <?php foreach ($patients as $dkey => $dvalue) {
                                        $check=isset($dvalue['patient_cnic']) && !empty($dvalue['patient_cnic']) ? " (" . $dvalue["patient_cnic"] . ")" : '';
                                        ?>
                                        <option value="<?php echo $dvalue["id"]; ?>" <?php
                                        if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                            echo "selected";
                                        }
                                        ?>><?php echo $dvalue["patient_name"] . " (" . $dvalue["patient_unique_id"] . ')'." (" . $dvalue["mobileno"] . ')'. $check ?></option>
                                            <?php } ?>
                                </select>
                            </div>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div><!--./col-sm-8-->
                    <div class="col-sm-4 col-xs-5">
                        <div class="form-group15">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new') . " " . $this->lang->line('patient') ?></span></a>
                            <?php } ?>

                        </div>
                    </div><!--./col-sm-4-->
                </div><!-- ./row -->
            </div>
            <form id="formbatch" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                <div class="modal-body pt0 pb0">
                    <div class="">


                        <div class="row row-eq">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div id="ajax_load"></div>
                                <div class="row ptt10" id="patientDetails" style="display:none">
                                    <input type="hidden" name="radiology_id" id="radio_id" >
                                    <input type="hidden" name="patient_id" id="radio_patientid" >
                                    <div class="col-md-9 col-sm-9 col-xs-9">

                                        <ul class="singlelist">
                                            <li class="singlelist24bold">
                                                <span id="listname"></span></li>
                                            <li>
                                                <i class="fas fa-user-secret" data-toggle="tooltip" data-placement="top" title="Guardian"></i>
                                                <span id="guardian"></span>
                                            </li>
                                        </ul>
                                        <ul class="multilinelist">
                                            <li>
                                                <i class="fas fa-venus-mars" data-toggle="tooltip" data-placement="top" title="Gender"></i>
                                                <span id="genders" ></span>
                                            </li>
                                            <li>
                                                <i class="fas fa-tint" data-toggle="tooltip" data-placement="top" title="Blood Group"></i>
                                                <span id="blood_group"></span>
                                            </li>
                                            <li>
                                                <i class="fas fa-ring" data-toggle="tooltip" data-placement="top" title="Marital Status"></i>
                                                <span id="marital_status"></span>
                                            </li>
                                        </ul>
                                        <ul class="singlelist">
                                            <li>
                                                <i class="fas fa-hourglass-half" data-toggle="tooltip" data-placement="top" title="Age"></i>
                                                <span id="age"></span>
                                            </li>

                                            <li>
                                                <i class="fa fa-phone-square" data-toggle="tooltip" data-placement="top" title="Phone"></i>
                                                <span id="listnumber"></span>
                                            </li>
                                            <li>
                                                <i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="Email"></i>
                                                <span id="email"></span>
                                            </li>
                                            <li>
                                                <i class="fas fa-street-view" data-toggle="tooltip" data-placement="top" title="Address"></i>
                                                <span id="address" ></span>
                                            </li>

                                            <li>
                                                <b><?php echo $this->lang->line('any_known_allergies') ?> </b>
                                                <span id="allergies" ></span>
                                            </li>
                                            <li>
                                                <b><?php echo $this->lang->line('remarks') ?> </b>
                                                <span id="note"></span>
                                            </li>
                                        </ul>
                                    </div><!-- ./col-md-9 -->
                                    <div class="col-md-3 col-sm-3 col-xs-3">
                                        <div class="pull-right">
                                          <!--<b><?php echo $this->lang->line('patient') . " " . $this->lang->line('photo') ?> </b>-->
                                                    <!--<span id="image"></span>-->
                                            <?php
                                            $file = "uploads/patient_images/no_image.png";
                                            ?>
                                            <img class="modal-profile-user-img img-responsive" src="<?php echo base_url() . $file ?>" id="image" alt="User profile picture">
                                        </div>
                                    </div><!-- ./col-md-3 -->
                                </div>
                            </div><!--./col-md-8-->

                            <div class="col-lg-6 col-md-6 col-sm-6 col-eq ptt10">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('reporting') . " " . $this->lang->line('date'); ?></label><small class="req"> *</small>
                                            <input type="text" id="report_date" name="reporting_date" class="form-control date">
                                            <span class="text-danger"><?php echo form_error('reporting_date'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="description"><?php echo $this->lang->line('description'); ?></label>

                                            <textarea name="description" class="form-control" ></textarea>
                                            <span class="text-danger"><?php echo form_error('description'); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('test') . " " . $this->lang->line('report'); ?></label>
                                            <input type="file" class="filestyle form-control" data-height="40" name="radiology_report">
                                            <span class="text-danger"><?php echo form_error('radiology_report'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputFile">
                                                <?php echo $this->lang->line('refferal') . " " . $this->lang->line('doctor'); ?>
                                            </label>
                                            <div>
                                                <select class="form-control select2" style="width:100%" name='consultant_doctor' id="consultant_doctor">
                                                    <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($doctors as $dkey => $dvalue) {
                                                        ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>
                                            <input type="text" id="charge_category_html" class="form-control" readonly>

                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('code'); ?></label>

                                            <input type="text" id="code_html" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>

                                            <input type="text" id="charge_html" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('applied') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?>
                                                <small class="req"> *</small>
                                            </label>
                                            <input type="text" name="apply_charge" id="apply_charge" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo 'Discount Type'; ?></label>
                                                <select name="discount_type" id="discount_type" class="form-control" autocomplete="off">
                                                        <option value="fixed" selected="">Fixed</option>
                                                        <option value="percentage">Percentage</option>
                                                </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo 'Discount'; ?></label>
                                            <input type="text" name="radio_discount" id="radio_discount" class="form-control" >
                                        </div>
                                    </div>

                                </div><!--./row-->
                            </div><!--./col-md-4-->

                        </div><!--./row-->
                    </div><!--./row-->
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formbatchbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right" ><?php echo $this->lang->line('save'); ?>
                        </button>
                    </div>
                    <div class="pull-right" style="margin-right:10px;">
                        <button type="button"  data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right printsavebtn"><?php echo $this->lang->line('save') . " & " . $this->lang->line('print'); ?></button>
                    </div>
                </div>
            </form>


        </div>
    </div>
</div>
<div class="modal fade" id="myModalBulk" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group15">
                          <div>
                                <select onchange="get_PatientDetails(this.value,'bulk')" style="width:100%" class="form-control select2 addpatient_id"  name='patient_id' id="addpatient_id" >
                                    <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                    <?php foreach ($patients as $dkey => $dvalue) {
                                        $check=isset($dvalue['patient_cnic']) && !empty($dvalue['patient_cnic']) ? " | CNIC# " . $dvalue["patient_cnic"]  : '';
                                        $mrno=isset($dvalue['mrno']) && !empty($dvalue['mrno']) ? " | Patient ID " . $dvalue["mrno"]  : '';
                                        ?>
                                        <option value="<?php echo $dvalue["id"]; ?>" <?php
                                        if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                            echo "selected";
                                        }
                                        ?>><?php echo $dvalue["patient_name"] . " | MR# " . $dvalue["mrno"] . '| '."Mobile # " . $dvalue["mobileno"] . '| '."Patient ID# " . $dvalue["patient_unique_id"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div><!--./col-sm-8-->
                    <div class="col-sm-4 col-xs-5">
                        <div class="form-group15">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-toggle="modal" id="add" onclick="holdModal('myModalpa','bulk_radio')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new') . " " . $this->lang->line('patient') ?></span></a>
                            <?php } ?>

                        </div>
                    </div><!--./col-sm-4-->
                </div><!-- ./row -->
            </div>
            <form id="formbatchbulk" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                <div class="modal-body pt0 pb0">
                    <div class="">


                        <div class="row row-eq">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div id="ajax_load"></div>
                                <div class="row ptt10" id="patientDetailsBulk" style="display:none">
                                    <!-- <input type="hidden" name="radiology_id" id="radio_id" > -->
                                    <input type="hidden" name="patient_id" id="radio_patientid_bulk" >
                                    <div class="col-md-9 col-sm-9 col-xs-9">

                                        <ul class="singlelist">
                                            <li class="singlelist24bold">
                                                <span id="listname_bulk"></span></li>
                                            <li>
                                                <i class="fas fa-user-secret" data-toggle="tooltip" data-placement="top" title="Guardian"></i>
                                                <span id="guardian_bulk"></span>
                                            </li>
                                        </ul>
                                        <ul class="multilinelist">
                                            <li>
                                                <i class="fas fa-venus-mars" data-toggle="tooltip" data-placement="top" title="Gender"></i>
                                                <span id="genders_bulk" ></span>
                                            </li>
                                            <li>
                                                <i class="fas fa-tint" data-toggle="tooltip" data-placement="top" title="Blood Group"></i>
                                                <span id="blood_group_bulk"></span>
                                            </li>
                                            <li>
                                                <i class="fas fa-ring" data-toggle="tooltip" data-placement="top" title="Marital Status"></i>
                                                <span id="marital_status_bulk"></span>
                                            </li>
                                        </ul>
                                        <ul class="singlelist">
                                            <li>
                                                <i class="fas fa-hourglass-half" data-toggle="tooltip" data-placement="top" title="Age"></i>
                                                <span id="age_bulk"></span>
                                            </li>

                                            <li>
                                                <i class="fa fa-phone-square" data-toggle="tooltip" data-placement="top" title="Phone"></i>
                                                <span id="listnumber_bulk"></span>
                                            </li>
                                            <li>
                                                <i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="Email"></i>
                                                <span id="email_bulk"></span>
                                            </li>
                                            <li>
                                                <i class="fas fa-street-view" data-toggle="tooltip" data-placement="top" title="Address"></i>
                                                <span id="address_bulk" ></span>
                                            </li>

                                            <li>
                                                <b><?php echo $this->lang->line('any_known_allergies') ?> </b>
                                                <span id="allergies_bulk" ></span>
                                            </li>
                                            <li>
                                                <b><?php echo $this->lang->line('remarks') ?> </b>
                                                <span id="note_bulk"></span>
                                            </li>
                                        </ul>
                                    </div><!-- ./col-md-9 -->
                                    <div class="col-md-3 col-sm-3 col-xs-3">
                                        <div class="pull-right">
                                          <!--<b><?php echo $this->lang->line('patient') . " " . $this->lang->line('photo') ?> </b>-->
                                                    <!--<span id="image"></span>-->
                                            <?php
                                            $file = "uploads/patient_images/no_image.png";
                                            ?>
                                            <img class="modal-profile-user-img img-responsive" src="<?php echo base_url() . $file ?>" id="image" alt="User profile picture">
                                        </div>
                                    </div><!-- ./col-md-3 -->
                                </div>
                            </div><!--./col-md-8-->

                            <div class="col-lg-6 col-md-6 col-sm-6 col-eq ptt10">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-eq ptt10 test_check"
                                        id="append_new_row">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('reporting') . " " . $this->lang->line('date'); ?></label><small class="req"> *</small>
                                                <input type="text" id="report_date" name="reporting_date_bulk[]" class="form-control" value="<?= date('d-m-Y H:i:s') ?>">
                                                <span class="text-danger"><?php echo form_error('reporting_date'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="description"><?php echo $this->lang->line('description'); ?></label>

                                                <textarea name="description_bulk[]" class="form-control" ></textarea>
                                                <span class="text-danger"><?php echo form_error('description_bulk'); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('test') . " " . $this->lang->line('report'); ?></label>
                                                <input type="file" class="filestyle form-control" data-height="40" name="report_bulk">
                                                <span class="text-danger"><?php echo form_error('report_bulk'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></label>
                                                    <div>
                                                        <select class="form-control select2"
                                                            onchange="getRadioTest(this.value,0)" style="width:100%"
                                                            name='test_id_bulk[]' id="test_id_bulk_0">
                                                            <option value=" "><?php echo $this->lang->line('select') ?>
                                                            </option>
                                                            <?php foreach ($tests as $test) {
                                                                ?>
                                                            <option value="<?php echo $test["id"]; ?>">
                                                                <?php echo $test["test_name"] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span
                                                        class="text-danger"><?php echo form_error('test_name'); ?></span>
                                                </div>
                                            </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('refferal') . " " . $this->lang->line('doctor'); ?>
                                                </label>
                                                <div>
                                                    <select class="form-control select2 cons-doctor" style="width:100%" name='consultant_doctor_bulk[]' id="consultant_doctor_bulk[]">
                                                        <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) {
                                                            ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                        <?php echo 'TPA' ?></label>
                                                    <div>
                                                        <select class="form-control select2" style="width:100%"
                                                            name='organisation_bulk[]' onchange="get_Charges('',0)"
                                                            id="organisation_bulk_0">
                                                            <option value="<?php echo set_value('tpa_charges'); ?>">
                                                                <?php echo $this->lang->line('select') ?></option>
                                                                <?php foreach ($organisation as $tpa) {
                                                                ?>
                                                                    <option value="<?php echo $tpa["id"]; ?>">
                                                                    <?php echo $tpa["organisation_name"] ?></option>
                                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span
                                                        class="text-danger"><?php echo form_error('tpa_charges_bulk'); ?></span>
                                                </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>
                                                <input type="text" id="charge_category_html_bulk_0" class="form-control" readonly>

                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('code'); ?></label>

                                                <input type="text" id="code_html_bulk_0" class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>

                                                <input type="text" id="charge_html_bulk_0" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <input type="hidden" name="applied_total[]" id="applied_total_0">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('applied') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?>
                                                    <small class="req"> *</small>
                                                </label>
                                                <input type="text" name="apply_charge_bulk[]" id="apply_charge_bulk_0" class="form-control sub_charges" >
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo 'Discount Type'; ?></label>
                                                    <select id="discount_type_0" name="discount_type[]" class="form-control" autocomplete="off">
                                                            <option value="fixed" selected="">Fixed</option>
                                                            <option value="percentage">Percentage</option>
                                                    </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo 'Discount'; ?></label>
                                                <input type="text" onkeyup="onchnageDiscount(this.value,0)" name="radio_discount[]" id="radio_discount_0" class="form-control" >
                                            </div>
                                        </div>

                                    </div><!--./row-->
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="form-group">
                                            <label>Total Charges
                                                <small class="req"> *</small>
                                            </label>
                                            <input type="text" name="total_charges" id="total_charges"
                                                class="form-control total_charges">
                                        </div>

                                    </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-2">
                                        <button type="button" onclick="appendNewRowTest()" class="btn btn-primary pull-right" autocomplete="off" id="newrowbutton">+</button>
                                </div>
                            </div><!--./col-md-4-->

                        </div><!--./row-->
                    </div><!--./row-->
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formbatchbtnbulk" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right" ><?php echo $this->lang->line('save'); ?>

                        </button>
                    </div>
                    <div class="pull-right" style="margin-right: 10px; ">
                        <button type="button"  data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right printRadioToken" data-print-option='A4'>Print Token</button>
                    </div>
                    <div class="pull-right" style="margin-right:10px;">
                        <button type="button"  data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right printsavebtnbulk"><?php echo $this->lang->line('save') . " & " . $this->lang->line('print'); ?></button>
                    </div>
                    <div class="pull-right cancel-btn-test" style="margin-right:10px;display:none;">
                        <button type="button" onClick="window.location.reload();"
                            data-loading-text="<?php echo $this->lang->line('processing') ?>"
                            class="btn btn-info pull-right "><?php echo $this->lang->line('cancel'); ?></button>
                    </div>
                </div>
            </form>


        </div>
    </div>
</div>

<div class="modal fade" id="importTestRadiology" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">
                    <?php echo $this->lang->line('import') . " " . $this->lang->line('test') ; ?>
                </h4>
            </div>
            <form action="<?php echo site_url('admin/radio/import') ?>" id="employeeform" name="employeeform" method="post" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                            <div class="col-md-6">
                            <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('select_csv_file'); ?></label><small class="req"> *</small>
                                        <div><input  class="filestyle form-control" type='file' name='file' id="file" size='20' />
                                            <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                                    </div>
                            </div>
                            </div>
                            <!--./row-->
                        </div>
                        <!--./col-md-12-->
                    </div>
                    <!--./row-->
                </div>

                <div class="divider"></div>

                <!--./col-md-12-->
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formaddbtn"
                            data-loading-text="<?php echo $this->lang->line('processing') ?>"
                            class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?> </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

// $('#easySelectable').bind('click', function (e) { e.stopPropagation() })

 function holdModal(modalId,modal_name='') {
                $('#' + modalId).modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
                if(modal_name==='bulk_radio'){
                    $('.bulk_form').val(modal_name);
                }
                if(modalId==='myModalBulk'){
                    setTimeout(showReportDate, 100);
                }
            }

 $(function () {
        $('#easySelectable').easySelectable();
//stopPropagation();
    })

function showReportDate() {
    $('#report_date_0').val('<?= date('d-m-Y')?>');
}


</script>


<script type="text/javascript">
            /*
             Author: mee4dy@gmail.com
             */
                    (function ($) {
                        //selectable html elements
                        $.fn.easySelectable = function (options) {
                            var el = $(this);
                            var options = $.extend({
                                'item': 'li',
                                'state': true,
                                onSelecting: function (el) {

                                },
                                onSelected: function (el) {

                                },
                                onUnSelected: function (el) {

                                }
                            }, options);
                            el.on('dragstart', function (event) {
                                event.preventDefault();
                            });
                            el.off('mouseover');
                            el.addClass('easySelectable');
                            if (options.state) {
                                el.find(options.item).addClass('es-selectable');
                                el.on('mousedown', options.item, function (e) {
                                    $(this).trigger('start_select');
                                    var offset = $(this).offset();
                                    var hasClass = $(this).hasClass('es-selected');
                                    var prev_el = false;
                                    el.on('mouseover', options.item, function (e) {
                                        if (prev_el == $(this).index())
                                            return true;
                                        prev_el = $(this).index();
                                        var hasClass2 = $(this).hasClass('es-selected');
                                        if (!hasClass2) {
                                            $(this).addClass('es-selected').trigger('selected');
                                            el.trigger('selected');
                                            options.onSelecting($(this));
                                            options.onSelected($(this));
                                        } else {
                                            $(this).removeClass('es-selected').trigger('unselected');
                                            el.trigger('unselected');
                                            options.onSelecting($(this))
                                            options.onUnSelected($(this));
                                        }
                                    });
                                    if (!hasClass) {
                                        $(this).addClass('es-selected').trigger('selected');
                                        el.trigger('selected');
                                        options.onSelecting($(this));
                                        options.onSelected($(this));
                                    } else {
                                        $(this).removeClass('es-selected').trigger('unselected');
                                        el.trigger('unselected');
                                        options.onSelecting($(this));
                                        options.onUnSelected($(this));
                                    }
                                    var relativeX = (e.pageX - offset.left);
                                    var relativeY = (e.pageY - offset.top);
                                });
                                $(document).on('mouseup', function () {
                                    el.off('mouseover');
                                });
                            } else {
                                el.off('mousedown');
                            }
                        };
                    })(jQuery);
</script>
<script type="text/javascript">

            $(document).ready(function (e) {

                $(".printsavebtn").on('click', (function (e) {
                    var form = $(this).parents('form').attr('id');
                    var str = $("#" + form).serializeArray();
                    var postData = new FormData();
                    $.each(str, function (i, val) {
                        postData.append(val.name, val.value);
                    });

                    var input = document.querySelector('input[type=file]'),
                    file = input.files[0];
                    postData.append("radiology_report", file);
                    $("#formbatchbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/testReportBatch',
                        type: "POST",
                        data: postData,
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
                            } else {

                                var radioid = $("#radio_id").val();
                                successMsg(data.message);
                                printData(data.id,radioid);

                                // window.location.reload(true);
                            }
                            $("#formbatchbtn").button('reset');
                        },
                        error: function () {
                            //  alert("Fail")
                        }
                    });


                }));
            });

            function printData(id,radioid) {
                //alert(id);
                var base_url = '<?php echo base_url() ?>';
                $.ajax({
                    url: base_url + 'admin/radio/getBillDetails/' + id +'/'+radioid,
                    type: 'POST',
                    data: {id: id, print: 'yes'},
                    success: function (result) {
                        // $("#testdata").html(result);
                        popup(result);
                    }
                });
            }

            function popup(data)
            {
                var base_url = '<?php echo base_url() ?>';
                var frame1 = $('<iframe />');
                frame1[0].name = "frame1";
                frame1.css({"position": "absolute", "top": "-1000000px"});
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
                frameDoc.document.write('<body >');
                frameDoc.document.write(data);
                frameDoc.document.write('</body>');
                frameDoc.document.write('</html>');
                frameDoc.document.close();
                setTimeout(function () {
                    window.frames["frame1"].focus();
                    window.frames["frame1"].print();
                    frame1.remove();
                    window.location.reload(true);
                }, 500);


                return true;
            }


            function get_PatientDetails(id,bulk='') {
                // $("#patientDetails").html("<?php echo $this->lang->line('loading') ?>");
                var base_url = "<?php echo base_url(); ?>backend/images/loading.gif";
                $("#ajax_load").html("<center><img src='" + base_url + "'/>");
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/patient/patientDetails',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (res) {
                        //console.log(res);
                        if (res) {
                            if(bulk=='bulk'){
                                $("#ajax_load").html("");
                                $("#patientDetailsBulk").show();
                                $('#patient_unique_id_bulk').html(res.patient_unique_id);
                                $('#radio_patientid_bulk').val(res.id);

                                $('#listname_bulk').html(res.patient_name);
                                $('#guardian_bulk').html(res.guardian_name);
                                $('#listnumber_bulk').html(res.mobileno);
                                $('#email_bulk').html(res.email);
                                if (res.age == "") {
                                    $("#age_bulk").html("");
                                } else {
                                        if (res.age) {
                                            var age = res.age + " " + "Years";
                                        } else {
                                            var age = '';
                                        }
                                        if (res.month) {
                                            var month = res.month + " " + "Month";
                                        } else {
                                            var month = '';
                                        }
                                        if (res.dob) {
                                            var dob = "(" + res.dob + ")";
                                        } else {
                                            var dob = '';
                                        }

                                        $("#age_bulk").html(age + "," + month + " " + dob);
                                // console.log(data.dob);
                                   }

                                $('#doctname_bulk').val(res.name + " " + res.surname);
                                $("#bp_bulk").html(res.bp);
                                $("#symptoms_bulk").html(res.symptoms);
                                $("#address_bulk").html(res.address);
                                $("#note_bulk").html(res.note);
                                $("#height_bulk").html(res.height);
                                $("#weight_bulk").html(res.weight);
                                $("#genders_bulk").html(res.gender);
                                $("#marital_status_bulk").html(res.marital_status);
                                $("#blood_group_bulk").html(res.blood_group);
                                $("#allergies_bulk").html(res.known_allergies);
                                $("#image_bulk").attr("src", '<?php echo base_url() ?>' + res.image);
                            }
                            else{

                                $("#ajax_load").html("");
                                $("#patientDetails").show();
                                $('#patient_unique_id').html(res.patient_unique_id);
                                $('#radio_patientid').val(res.id);

                                $('#listname').html(res.patient_name);
                                $('#guardian').html(res.guardian_name);
                                $('#listnumber').html(res.mobileno);
                                $('#email').html(res.email);
                            if (res.age == "") {
                                $("#age").html("");
                            } else {
                                if (res.age) {
                                    var age = res.age + " " + "Years";
                                } else {
                                    var age = '';
                                }
                                if (res.month) {
                                    var month = res.month + " " + "Month";
                                } else {
                                    var month = '';
                                }
                                if (res.dob) {
                                    var dob = "(" + res.dob + ")";
                                } else {
                                    var dob = '';
                                }

                                $("#age").html(age + "," + month + " " + dob);
                                // console.log(data.dob);
                            }

                                $('#doctname').val(res.name + " " + res.surname);
                                $("#bp").html(res.bp);
                                $("#symptoms").html(res.symptoms);
                                $("#address").html(res.address);
                                $("#note").html(res.note);
                                $("#height").html(res.height);
                                $("#weight").html(res.weight);
                                $("#genders").html(res.gender);
                                $("#marital_status").html(res.marital_status);
                                $("#blood_group").html(res.blood_group);
                                $("#allergies").html(res.known_allergies);
                                $("#image").attr("src", '<?php echo base_url() ?>' + res.image);
                            }

                            if (res.organisation && res.organisation != "") {
                                // Update the value of select2 dropdown
                                $('#organisation_bulk_0').val(res.organisation).trigger('change');
                            } else {
                                // If no organisation is associated, reset the select2 dropdown
                                $('#organisation_bulk_0').val('').trigger('change');
                            }

                            if (res.cons_doctor && res.cons_doctor != "") {
                                // Update the value of select2 dropdown
                                $('.cons-doctor').val(res.cons_doctor).trigger('change');
                            } else {
                                // If no organisation is associated, reset the select2 dropdown
                                $('.cons-doctor').val('').trigger('change');
                            }


                        } else {
                            $("#ajax_load").html("");
                            $("#patientDetails").hide();
                        }
                    }
                });
            }



            $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    $("#formaddbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/add',
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
                            } else {
                                successMsg(data.message);
                                window.location.reload(true);
                            }
                            $("#formaddbtn").button('reset');
                        },
                        error: function () {
                            //  alert("Fail")
                        }
                    });
                }));
            });




            $(document).ready(function (e) {
                $("#formedit").on('submit', (function (e) {
                    $("#formeditbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/update',
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
                            } else {
                                successMsg(data.message);
                                window.location.reload(true);
                            }
                            $("#formeditbtn").button('reset');
                        },
                        error: function () {
                            //  alert("Fail")
                        }
                    });
                }));
            });

            function getRecord(id) {
                // $('#myModaledit').modal('show');
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/radio/getDetails',
                    type: "POST",
                    data: {radiology_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#id").val(data.id);
                        $("#test_name").val(data.test_name);
                        $("#short_name").val(data.short_name);
                        $("#test_type").val(data.test_type);
                        $("#sub_category").val(data.sub_category);
                        $("#report_days").val(data.report_days);
                        $("#edit_charge_category").val(data.charge_category);
                        $("#edit_standard_charge").val(data.standard_charge);
                        $('#edit_test_description').summernote('destroy');
                        $("#edit_test_description").val(data.test_description);
                        $('#edit_test_description').summernote();
                        editchargecode(data.charge_category, data.charge_id);
                        $("#updateid").val(id);
                        //console.log(data);
                        $('select[id="radiology_category_id"] option[value="' + data.radiology_category_id + '"]').attr("selected", "selected");
                        $('select[id="charge_category_id"] option[value="' + data.charge_category_id + '"]').attr("selected", "selected");
                        $("#viewModal").modal('hide');
                        $("#radiology_category_id").select2().select2('val', data.radiology_category_id);
                        //$("#edit_code").select2().select2('val',2);
                        holdModal('myModaledit');
                    },
                })
                $.ajax({
                url: '<?php echo base_url(); ?>admin/radio/editparameter/' + id,
                    success: function (res) {

                        $("#edit_parameter_details").html(res);
                        holdModal('myModaledit');
                    },
                    error: function () {
                        alert("Fail")
                    }
                });
            }

            $(function () {
                //Initialize Select2 Elements
                $('.select2').select2();
            });
            function delete_record(id) {
                if (confirm('<?php echo $this->lang->line('delete_conform'); ?>')) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/delete/' + id,
                        type: "POST",
                        data: {opdid: ''},
                        dataType: 'json',
                        success: function (data) {
                            successMsg('<?php echo $this->lang->line('delete_message'); ?>');

                            window.location.reload(true);
                        }
                    })
                }
            }




            function editchargecode(charge_category, charge_id) {
                var div_data = "";

                $('#edit_code').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
                $('#edit_code').select2("val", 'l');
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
                    type: "POST",
                    data: {charge_category: charge_category},
                    dataType: 'json',
                    success: function (res) {
                        //alert(res)
                        $.each(res, function (i, obj)
                        {
                            var sel = "";
                            if (charge_id == obj.id) {
                                //  sel = "selected";
                            }
                            div_data += "<option value='" + obj.id + "' " + sel + ">" + obj.code + " - " + obj.description + "</option>";
                        });
                        $('#edit_code').html("<option value=''>Select</option>");
                        $('#edit_code').append(div_data);
                        $("#edit_code").select2().select2('val', charge_id);
                    }
                });
            }

            function getchargecode(charge_category) {
                var div_data = "";

                $('#code').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
                $('#code').select2("val", 'l');
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
                    type: "POST",
                    data: {charge_category: charge_category},
                    dataType: 'json',
                    success: function (res) {
                        //alert(res)
                        $.each(res, function (i, obj)
                        {
                            var sel = "";
                            div_data += "<option value='" + obj.id + "'>" + obj.code + " - " + obj.description + "</option>";

                        });
                        $('#code').html("<option value=''>Select</option>");
                        $('#code').append(div_data);
                        $('#code').select2("val", '');
                    }
                });
            }


            function viewDetail(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/radio/getDetails',
                    type: "POST",
                    data: {radiology_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#test_names").html(data.test_name);
                        $("#short_names").html(data.short_name);
                        $("#test_types").html(data.test_type);
                        $("#radiology_category_ids").html(data.lab_name);
                        $("#sub_categorys").html(data.sub_category);
                        $("#report_dayss").html(data.report_days);
                        $("#charge_categorys").html(data.charge_category);
                        $("#codes").html(data.code);
                        $("#description").html("(" + data.description + ")");
                        $("#stdcharge").html(data.standard_charge);
                        $("#testdscp").html(data.test_description);
                        $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('radiology test', 'can_edit')) { ?><a href='#'' onclick='getRecord(" + id + ")'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } if ($this->rbac->hasPrivilege('radiology test', 'can_delete')) { ?><a onclick='delete_record(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                        holdModal('viewModal');
                    },
                });
                 $.ajax({
                url: '<?php echo base_url(); ?>admin/radio/parameterview/' + id,
                    success: function (res) {
                        //console.log(res)
                        $("#parameterview").html(res);
                        holdModal('viewModal');
                    },
                    error: function () {
                        alert("Fail")
                    }
                });

            }
            function addTestReport(id='',modal='') {
                if(modal=='modal'){
                    holdModal('myModalBulk');
                }else{
                    $.ajax({
                    url: '<?php echo base_url(); ?>admin/radio/getRadiology',
                    type: "POST",
                    data: {radiology_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#radio_id").val(id);
                        $("#charge_category_html").val(data.charge_category);
                        $("#code_html").val(data.code);
                        $("#charge_html").val(data.standard_charge);
                        $("#apply_charge").val(data.standard_charge);
                        holdModal('myModal');
                    },
                })
                }

            }


            $(document).ready(function (e) {
                $("#formbatch").on('submit', (function (e) {
                    $("#formbatchbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/testReportBatch',
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
                            } else {
                                successMsg(data.message);
                                window.location.reload(true);
                            }
                            $("#formbatchbtn").button('reset');
                        },
                        error: function () {

                        }
                    });
                }));
            });

            function showtextbox(value) {
                if (value != 'direct') {
                    $("#opd_ipd_no").show();
                } else {
                    $("#opd_ipd_no").hide();
                }
            }

            function addMore() {

                    var table = document.getElementById("tableID");
                    var table_len = (table.rows.length);
                    var id = parseInt(table_len - 1);
                    var div = "<td width='35%'><select class='form-control select2' name='parameter_name[]' onchange='getparameterdetails(this.value," + id + ")'><option value='<?php echo set_value('parameter_name'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($parametername as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["parameter_name"] ?></option><?php } ?></select></td><td width='30%'><input type='text' name='reference_range[]' readonly id='reference_range" + id + "' class='form-control'></td><td width='30%'><input type='text' name='radio_unit[]' readonly id='radio_unit" + id + "' class='form-control'></td>";
                    console.log(div);
                    var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "<td><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";
                    $('.select2').select2();
                }

                function delete_row(id) {
                    var table = document.getElementById("tableID");
                    var rowCount = table.rows.length;
                    $("#row" + id).remove();
                }

                  function getparameterdetails(parameter_id,id) {
               // var medicine = $("#parameter_name" + id).val();
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/radio/getparameterdetails",
                    data: {'id': parameter_id },
                    dataType: 'json',
                    success: function (res) {
                        if (res != null) {
                            $('#reference_range' + id).val(res.reference_range);
                            $('#radio_unit' + id).val(res.unit_name);
                            // getQuantity(id);
                        }
                    }
                });
            }

               function getchargeDetails(id, htmlid) {

                $('#' + htmlid).val("");
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/charges/getDetails',
                    type: "POST",
                    data: {charges_id: id, organisation: ''},
                    dataType: 'json',
                    success: function (res)
                    {
                        $('#' + htmlid).val(res.standard_charge);
                    }
                })
            }



</script>
<script type="text/javascript">
    $(document).ready(function() {
    $('.test_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "createdRow": function( row, data, dataIndex ) {
            $(row).children(':nth-child(7)').addClass('pull-right');
        },
        "ajax": {
            "url": base_url+"admin/searchdatatable/radiology_search",
            "type": "POST"
        },
           responsive: 'true',
            dom: "Bfrtip",
         buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',

                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'

                    }
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.download_label').html(),
                        customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' );

                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size','inherit');
                },
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
    });
});

$(".radiology").click(function(){
	$('#formadd').trigger("reset");
	var table = document.getElementById("tableID");
    var table_len = (table.rows.length);
	for (i = 1; i < table_len; i++) {
		delete_row(i);
	}
});

function addpatientreport(){
	$('#formbatch').trigger("reset");
	$("#patientDetails").hide();
	$('#select2-addpatient_id-container').html("");
	$(".dropify-clear").trigger("click");
}

$(".modalbtnpatient").click(function(){
	$('#formaddpa').trigger("reset");
	$(".dropify-clear").trigger("click");
});

var id = 1;
var inc = 0;
var animateDivNew = ".test_check";

var selectedTestIds = [];

// Listen for change event on the select2 dropdown in the modal
$('.select2[name^="test_id_bulk"]').one('change', function() {
    var selectedTest = $(this).val();
    if (selectedTest) {
        selectedTestIds.push(selectedTest);
    }
});

function appendNewRowTest() {
    var organisationValue = $('#organisation_bulk_0').val();
    var consultantDoctorValue = $('#consultant_doctor_bulk\\[\\]').val();
    var html = `<hr><div class="row_${id}">
                    <button type="button" onclick="delete_row_test(${id})" class="closebtn pull-right"><i class="fa fa-remove"></i></button>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('reporting') . " " . $this->lang->line('date'); ?></label><small class="req"> *</small>
                                                <input type="text" id="report_date_${id}" name="reporting_date_bulk[]" class="form-control" value="<?= date('d-m-Y H:i:s') ?>">
                                                <span class="text-danger"><?php echo form_error('reporting_date'); ?></span>
                                            </div>
                                        </div>



                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="description"><?php echo $this->lang->line('description'); ?></label>

                                                <textarea name="description_bulk[]" class="form-control" ></textarea>
                                                <span class="text-danger"><?php echo form_error('description_bulk'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('test') . " " . $this->lang->line('report'); ?></label>
                                                <input type="file" name="report_bulk[]" class="filestyle form-control" data-height="40" />
                                                <span class="text-danger"><?php echo form_error('report_bulk'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></label>
                                                <div>
                                                    <select class="form-control select2" onchange="getRadioTest(this.value,${id})" style="width:100%" name='test_id_bulk[]' id="test_id_bulk_${id}">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($tests as $test) {
                                                            ?>
                                                            <option value="<?php echo $test["id"]; ?>"><?php echo $test["test_name"] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('test_name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('refferal') . " " . $this->lang->line('doctor'); ?></label>
                                                    <small class="req"> *</small>
                                                <div>
                                                    <select class="form-control select2" style="width:100%" name='consultant_doctor_bulk[]' id="consultant_doctor_bulk[]">
                                                        <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) {
                                                            ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">
                                                    <?php echo 'TPA' ?></label>
                                                <div>
                                                    <select class="form-control select2" style="width:100%" name='organisation_bulk[]' onchange="get_Charges('',${id})" id="organisation_bulk_${id}">
                                                        <option value="<?php echo set_value('tpa_charges'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($organisation as $tpa) {
                                                        ?>
                                                        <option value="<?php echo $tpa["id"]; ?>" <?php echo ($organisationValue == $tpa["id"]) ? "selected" : ""; ?>>
                                                            <?php echo $tpa["organisation_name"] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('tpa_charges'); ?></span>
                                            </div>
                                        </div>



                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>

                                                <input type="text" id="charge_category_html_bulk_${id}" class="form-control" readonly>

                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('code'); ?></label>

                                                <input type="text" id="code_html_bulk_${id}" class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>

                                                <input type="text" id="charge_html_bulk_${id}" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <input type="hidden" name="applied_total[]" id="applied_total_${id}">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('applied') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?>
                                                    <small class="req"> *</small>
                                                </label>
                                                <input type="text" name="apply_charge_bulk[]"  id="apply_charge_bulk_${id}" class="form-control sub_charges" >
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                                <div class="form-group">
                                                <label for="exampleInputFile">
                                                <?php echo 'Discount Type'; ?></label>
                                                <div>
                                                    <select name="discount_type[]" onchange="discountType(this.value,${id})" id="discount_type_${id}" class="form-control">
                                                        <option value="fixed" selected><?php echo 'Fixed' ?></option>
                                                        <option value="percentage" ><?php echo 'Percentage' ?></option>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('discount_type'); ?></span>
                                                </div>
                                            </div>
                                        <div class="col-sm-6">
                                                <div class="form-group">
                                                <label><?php echo "Discount" ; ?></label>
                                                <input type="text" id="radio_discount_${id}" class="form-control discount" onkeyup="onchnageDiscount(this.value,${id})"  name="radio_discount[]" autocomplete="off">
                                                <span class="text-danger"><?php echo form_error('discount_bulk'); ?></span>
                                                </div>
                                            </div>
                                    </div>`;

    $('#append_new_row').append(html);
    $('#organisation_bulk_' + id).val(organisationValue).trigger('change');
    // Check if consultantDoctorValue is not empty
    if (consultantDoctorValue) {
        // Update the consultant_doctor dropdown in the new row with the selected value
        var option = `<option value="${consultantDoctorValue}" selected>${$('#consultant_doctor_bulk\\[\\] option:selected').text()}</option>`;
        $('#consultant_doctor_bulk\\[\\]').append(option);
    }

    // Event handler for the test name select2 change
    $('#test_id_bulk_' + id).on('change', function() {
        var newTestId = $(this).val(); // ID of the test in the new row

        // Check if the new test ID is already selected
        if (selectedTestIds.includes(newTestId)) {
            errorMsg('This test is already selected. Please select a different test');
            // Reset the selection in the current row
            $(this).val('').trigger('change.select2');
        } else {
            $('#newrowbutton').on('click', function() {
                // Add the new test ID to the selectedTestIds array
                selectedTestIds.push(newTestId);
            });
        }
    });

    //$('#report_date_'+id).val('<?= date('d-m-Y')?>');
    $('.select2').select2();
    $('.filestyle').dropify();
    inc = inc + 500;

    var target = $(".row_" + id);
    if (target.length) {
        $('div,test_check').animate({
            scrollTop: inc
        }, 100);

    }
    var sum = 0;
    $("input[class *= 'sub_charges']").each(function() {
        sum += +$(this).val();
    });
    $(".total").val(sum);

    $('#total_charges').val('');
    $('#total_charges').val(sum);
    id = parseInt(id) + 1;
    inc = parseInt(inc) + 1000;
    animateDivNew = ".row_" + id;

}


function delete_row_test(id) {
    $('.row_' + id).remove();
    var sum = 0;
    $("input[class *= 'sub_charges']").each(function() {
        sum += +$(this).val();
    });
    $(".total").val(sum);

    $('#total_charges').val('');
    $('#total_charges').val(sum);
}

function getRadioTest(id,row) {

                    $.ajax({
                    url: '<?php echo base_url(); ?>admin/radio/getRadiology',
                    type: "POST",
                    data: {radiology_id: id},
                    dataType: 'json',
                    success: function (data) {
                        //$("#radio_id").val(id);
                        $("#charge_category_html_bulk_"+row).val(data.charge_category);
                        $("#code_html_bulk_"+row).val(data.code);
                        $("#charge_html_bulk_"+row).val(data.standard_charge);
                        $("#apply_charge_bulk_"+row).val(data.standard_charge);
                        $("#applied_total_"+row).val(data.standard_charge);
                        // $("#organisation_bulk_" + row).empty();
                        // $("#organisation_bulk_" + row).append(new Option("Select", "")).trigger("chosen:updated");
                        var tpa_charges = data.getOrganiztionCharges;
                        // $.each(tpa_charges, function(i, tpa_charge) {
                        //     $("#organisation_bulk_" + row).append($('<option>', {
                        //         value: tpa_charge.id,
                        //         text: tpa_charge.organisation_name
                        //     })).trigger("chosen:updated");
                        // });
                        var sum = 0;
                        $("input[class *= 'sub_charges']").each(function() {
                            sum += +$(this).val();
                        });
                        $(".total").val(sum);

                        $('#total_charges').val('');
                        $('#total_charges').val(sum);
                                        },
                })


            }

            $(document).ready(function (e) {
                $("#formbatchbulk").on('submit', (function (e) {
                    $("#formbatchbtnbulk").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/testReportBatchBulk',
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
                            } else {
                                successMsg(data.message);
                                window.location.reload(true);
                            }
                            $("#formbatchbtnbulk").button('reset');
                        },
                        error: function () {

                        }
                    });
                }));
            });

function onchnageDiscount(value,row){
    if($('#discount_type_'+row).val()=='fixed'){
        if($('#applied_total_'+row).val()!==''){
            let apply_charge=$('#applied_total_'+row).val();
            let opd_discount=$('#radio_discount_'+row).val();
            let amount=( Number(apply_charge) - Number(opd_discount));
            $('#apply_charge_bulk_'+row).val("");
            $('#apply_charge_bulk_'+row).val(amount);
        }else{
            errorMsg("Applied charges should not be empty!");
        }

    }else{
        let apply_charge=$('#applied_total_'+row).val();
        let opd_discount=$('#radio_discount_'+row).val();
        let discounted=( Number(apply_charge) * (Number(opd_discount)/100));
        let amount=( Number(apply_charge) - Number(discounted));
        $('#apply_charge_bulk_'+row).val("");
        $('#apply_charge_bulk_'+row).val(amount);
    }

    var sum = 0;
        $("input[class *= 'sub_charges']").each(function() {
        sum += +$(this).val();
        });
        $(".total").val(sum);
        $('#total_charges').val('');
        $('#total_charges').val(sum);

}

function discountType(value,row)
{
    if($('#discount_type_'+row).val()=='fixed'){
        if($('#applied_total_'+row).val()!==''){
            let apply_charge=$('#applied_total_'+row).val();
            let opd_discount=$('#radio_discount_'+row).val();
            let amount=( Number(apply_charge) - Number(opd_discount));
            $('#apply_charge_bulk_'+row).val("");
            $('#apply_charge_bulk_'+row).val(amount);
        }else{
            errorMsg("Applied charges should not be empty!");
        }

    }else{
        let apply_charge=$('#applied_total_'+row).val();
        let opd_discount=$('#radio_discount_'+row).val();
        let discounted=( Number(apply_charge) * (Number(opd_discount)/100));
        let amount=( Number(apply_charge) - Number(discounted));
        $('#apply_charge_bulk_'+row).val("");
        $('#apply_charge_bulk_'+row).val(amount);
    }

    var sum = 0;
        $("input[class *= 'sub_charges']").each(function() {
        sum += +$(this).val();
        });
        $(".total").val(sum);
        $('#total_charges').val('');
        $('#total_charges').val(sum);
}

$(document).ready(function (e) {
    $(".printsavebtnbulk").on('click', (function (e) {
        var form = $(this).parents('form').attr('id');
        var str = $("#" + form).serializeArray();
        var postData = new FormData();
        $.each(str, function (i, val) {
            postData.append(val.name, val.value);
        });

        var input = document.querySelector('input[type=file]'),
        file = input.files[0];
        postData.append("radiology_report", file);
        $("#formbatchbtn").button('loading');
        e.preventDefault();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/radio/testReportBatchBulk',
            type: "POST",
            data: postData,
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
                } else {

                    var radioid = $("#radio_id").val();
                    successMsg(data.message);
                    printBulkData(data.id,data.bil_no);

                    // window.location.reload(true);
                }
                $("#formbatchbtn").button('reset');
            },
            error: function () {
                //  alert("Fail")
            }
        });
    }));
});

$(document).ready(function (e) {
    $(".printRadioToken").on('click', (function (e) {
        $(this).prop('disabled', true);
        var form = $(this).parents('form').attr('id');
        var str = $("#" + form).serializeArray();
        var postData = new FormData();
        $.each(str, function (i, val) {
            postData.append(val.name, val.value);
        });

        var input = document.querySelector('input[type=file]'),
        file = input.files[0];
        postData.append("radiology_report", file);
        $("#formbatchbtn").button('loading');
        e.preventDefault();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/radio/generateRadioPatientTest',
            type: "POST",
            data: postData,
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
                } else {

                    var radioid = $("#radio_id").val();
                    successMsg(data.message);
                    var base_url = '<?php echo base_url() ?>';
                    var url = base_url + 'admin/radio/printToken/' + data.bil_no + '/' + data.id;
                    var newWindow = window.open(url, '_blank');
                    newWindow.onload = function() {
                        newWindow.print();
                    };

                    // window.location.reload(true);
                }
                $("#formbatchbtn").button('reset');
                $('.printsavebtnbulk').hide();
                $('.printRadioToken').hide();
                $("#formbatchbtnbulk").hide();
                $('.cancel-btn-test').show();
            },
            error: function () {
                //  alert("Fail")
            }
        });
    }));
});

function printBulkData(report_ids,bill_nos) {
    //alert(id);
    var base_url = '<?php echo base_url() ?>';
    $.ajax({
        url: base_url + 'admin/radio/getBillDetailsBulk/',
        type: 'POST',
        data: {id: bill_nos,report_id:report_ids, print: 'yes'},
        success: function (result) {
            // $("#testdata").html(result);
            popup(result);
        }
    });
}

$(document).ready(function() {
   $('.test_description').summernote();
});


function get_Charges(id = '', row_num = '') {

var orgid = '';
var testId = '';
//alert(row_num);
if (row_num > -1) {

    orgid = $("#organisation_bulk_" + row_num).val();
    testId = $("#test_id_bulk_" + row_num).val();
    //alert(orgid);
} else if (row_num == '') {
    orgid = $("#organisation").val();
    testId = $("#test_id_bulk_").val();
}

if (id == '') {
    id = $("#consultant_doctor").val();
}
// alert(testId);return;
$.ajax({
    url: '<?php echo base_url(); ?>admin/patient/getorganizationChargeRadio',
    type: "POST",
    data: {
        doctor: id,
        organisation: orgid,
        testId: testId,
    },
    dataType: 'json',
    success: function(res) {
        if (res) {
            if (orgid) {
                if (row_num > -1) {
                    $('#apply_charge_bulk_' + row_num).val('');
                    $('#applied_total_' + row_num).val('');
                    $('#apply_charge_bulk_' + row_num).val(res.org_charge);
                    $('#applied_total_' + row_num).val(res.org_charge);
                    var sum = 0;
                    $("input[class *= 'sub_charges']").each(function() {
                        sum += +$(this).val();
                    });
                    $(".total").val(sum);

                    $('#total_charges').val('');
                    $('#total_charges').val(sum);
                } else {
                    $('#apply_charge').val(res.org_charge);
                }

            } else {
                if (row_num > -1) {
                    $('#apply_charge_bulk_' + row_num).val('');
                    $('#applied_total_' + row_num).val('');
                    $('#apply_charge_bulk_' + row_num).val(res.standard_charge);
                    $('#applied_total_' + row_num).val(res.standard_charge);
                    var sum = 0;
                    $("input[class *= 'sub_charges']").each(function() {
                        sum += +$(this).val();
                    });
                    $(".total").val(sum);

                    $('#total_charges').val('');
                    $('#total_charges').val(sum);
                } else {
                    $('#apply_charge').val(res.standard_charge);
                }

            }
        } else {
            $('#apply_charge').val('0');
        }
    }
});
}

function get_ChargesNew(id = '', row_num = '') {
var orgid = '';
//alert(row_num);
if (row_num > -1) {

    orgid = $("#organisation_bulk_new_" + row_num).val();
    //alert(orgid);
} else if (row_num == '') {
    orgid = $("#organisation").val();
}

if (id == '') {
    id = $("#consultant_doctor").val();
}
//alert(orgid);return;
$.ajax({
    url: '<?php echo base_url(); ?>admin/patient/getorganizationCharge',
    type: "POST",
    data: {
        doctor: id,
        organisation: orgid
    },
    dataType: 'json',
    success: function(res) {
        if (res) {

            if (orgid) {
                if (row_num > -1) {
                    $('#apply_charge_bulk_new_' + row_num).val('');
                    $('#apply_charge_bulk_new_' + row_num).val(res.org_charge);
                    var sum = 0;
                    $("input[class *= 'sub_charges']").each(function() {
                        sum += +$(this).val();
                    });
                    $(".total").val(sum);

                    $('#total_charges_new').val('');
                    $('#total_charges_new').val(sum);
                } else {
                    $('#apply_charge_new').val(res.org_charge);
                }

            } else {
                if (row_num > -1) {
                    $('#apply_charge_bulk_new_' + row_num).val('');
                    $('#apply_charge_bulk_new_' + row_num).val(res.standard_charge);
                    var sum = 0;
                    $("input[class *= 'sub_charges']").each(function() {
                        sum += +$(this).val();
                    });
                    $(".total").val(sum);

                    $('#total_charges_new').val('');
                    $('#total_charges_new').val(sum);
                } else {
                    $('#apply_charge_new').val(res.standard_charge);
                }

            }
        } else {
            $('#apply_charge_new').val('0');
        }
    }
});
}
</script>

<?php $this->load->view('admin/patient/patientaddmodal') ?>