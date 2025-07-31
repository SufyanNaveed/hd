<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<style type="text/css">
    #easySelectable {/*display: flex; flex-wrap: wrap;*/}
    #easySelectable li {}
    #easySelectable li.es-selected {background: #2196F3; color: #fff;}
    .easySelectable {-webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;}
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('pathology') . " " . $this->lang->line('test') . " " . $this->lang->line('reports'); ?></h3>
                        <div class="box-tools pull-right">

                                <a data-toggle="modal" onclick="holdModal('getTestReportModal')" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print Test Report</a>


                        </div>


                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('pathology') . " " . $this->lang->line('test') . " " . $this->lang->line('reports'); ?></div>
                        <table class="custom-table table table-striped table-bordered table-hover test_ajax" id="testreport"cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('bill') . " " . $this->lang->line('no'); ?></th>
                                    <th><?php echo $this->lang->line('reporting') . " " . $this->lang->line('date'); ?></th>
                                    <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('reference') . " " . $this->lang->line('doctor'); ?></th>
                                    <th><?php echo $this->lang->line('description'); ?></th>
                                    <th><?php echo $this->lang->line('status'); ?></th>
                                    <th><?php echo $this->lang->line('discount'); ?></th>
                                    <th class="text-right" ><?php echo $this->lang->line('applied') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
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

<div class="modal fade" id="addParametervalueModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-4">
                        <div>
                            <select onchange="get_PatientDetails(this.value)" disabled="" style="width: 100%" class="form-control select2" id="addpatientidd" name='' >
                                <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                <?php foreach ($patients as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php
                                    if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                        echo "selected";
                                    }
                                    ?>><?php echo $dvalue["patient_name"] . " ( " . $dvalue["patient_unique_id"] . ")" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <form id="parameteradd" enctype="multipart/form-data" accept-charset="utf-8"  method="post" class="ptt10 parameteradd" >
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">

                            <input type="hidden" name="id" id="preport_id" >
                            <input type="hidden" name="patient_id_patho" id="patientid_patho" >
                            <input type="hidden" name="pathologyid" id="pathologyid" >

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('reporting') . " " . $this->lang->line('date'); ?></label>
                                        <input type="text"  id="pedit_report_date" name="reporting_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('reporting_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                        <?php echo $this->lang->line('refferal') . " " . $this->lang->line('doctor'); ?></label>
                                        <div>
                                            <select class="form-control select2"  style="width: 100%" name='consultant_doctor'  id="pedit_consultant_doctor">
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
                                        <label><?php echo $this->lang->line('test') . " " . $this->lang->line('report'); ?></label>
                                        <input type="file"  class="filestyle form-control" data-height="40" name="pathology_report">
                                        <span class="text-danger"><?php echo form_error('pathology_report'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>

                                        <input type="text"  class="form-control" readonly="" id="pcharge_category_html">

                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('code'); ?></label>
                                        <input type="text" class="form-control" readonly="" id="pcode_html">

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" class="form-control" readonly="" id="pcharge_html">

                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label><?php echo 'Discount'; ?>
                                            <small class="req"> *</small>
                                        </label>
                                        <input type="text" name="pth_discount"  id="pth_discount" class="form-control" >
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('applied') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?>
                                            <small class="req"> *</small>
                                        </label>
                                        <input type="text" name="apply_charge"  id="papply_charge" class="form-control" >
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                    <input type="checkbox" name="show_dscp" class="form-check-input" id="show_dscp">
                                         <label class="form-check-label" for="show_dscp">Show Description</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    <input type="checkbox" name="show_clinical" class="form-check-input" id="show_clinical">
                                         <label class="form-check-label" for="show_clinical">Show Clinical Information</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                    <input type="checkbox" name="show_description_only" class="form-check-input" id="show_description_only">
                                         <label class="form-check-label" for="show_description_only">Show Description Only</label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                      <!--   <small class="req"> *</small>  -->
                                        <textarea name="description" id="pedit_description" class="form-control" ></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div><!--./row-->
                    </div><!--./col-md-12-->

                </div><!--./row-->

            </div>


                <div class="col-md-12" style="clear:both;" >
                         <div class="" id="parameterdetails" > </div>
                </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="button" id="parameteraddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right" ><?php echo $this->lang->line('save'); ?></button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="addParameterreportvalueModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-4">
                        <div>
                            <select onchange="get_PatientDetails(this.value)" disabled="" style="width: 100%" class="form-control select2" id="raddpatientidd" name='' >
                                <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                <?php foreach ($patients as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php
                                    if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                        echo "selected";
                                    }
                                    ?>><?php echo $dvalue["patient_name"] . " ( " . $dvalue["patient_unique_id"] . ")" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <form id="parameteradd" enctype="multipart/form-data" accept-charset="utf-8"  method="post" class="ptt10" >
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">

                            <input type="hidden" name="id" id="rpreport_id" >
                            <input type="hidden" name="patient_id_patho" id="rpatientid_patho" >
                            <input type="hidden" name="pathologyid" id="rpathologyid" >

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('reporting') . " " . $this->lang->line('date'); ?></label>
                                        <input type="text"  id="rpedit_report_date" name="reporting_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('reporting_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('refferal') . " " . $this->lang->line('doctor'); ?></label>
                                        <div>
                                            <select class="form-control select2"  style="width: 100%" name='consultant_doctor'  id="rpedit_consultant_doctor">
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
                                        <label><?php echo $this->lang->line('test') . " " . $this->lang->line('report'); ?></label>
                                        <input type="file"  class="filestyle form-control" data-height="40" name="pathology_report">
                                        <span class="text-danger"><?php echo form_error('pathology_report'); ?></span>
                                    </div>
                                </div>
                              <!--   <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>

                                        <input type="text"  class="form-control" readonly="" id="rpcharge_category_html">

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('code'); ?></label>
                                        <input type="text" class="form-control" readonly="" id="rpcode_html">

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" class="form-control" readonly="" id="rpcharge_html">

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('applied') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?>
                                            <small class="req"> *</small>
                                        </label>
                                        <input type="text" name="apply_charge"  id="rpapply_charge" class="form-control" >
                                    </div>
                                </div> -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                      <!--   <small class="req"> *</small>  -->
                                        <textarea name="description" id="rpedit_description" class="form-control" ></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div><!--./row-->
                    </div><!--./col-md-12-->

                </div><!--./row-->

            </div>


                <div class="col-md-12" style="clear:both;" >
                         <div class="" id="parameterdetailsreport" > </div>
                </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" id="parameteraddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right" ><?php echo $this->lang->line('save'); ?></button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="addParameterbillvalueModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-4">
                        <div>
                            <select onchange="get_PatientDetails(this.value)" disabled="" style="width: 100%" class="form-control select2" id="baddpatientidd" name='' >
                                <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                <?php foreach ($patients as $dkey => $dvalue) { ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php
                                    if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                        echo "selected";
                                    }
                                    ?>><?php echo $dvalue["patient_name"] . " ( " . $dvalue["patient_unique_id"] . ")" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <form id="parameteradd" enctype="multipart/form-data" accept-charset="utf-8"  method="post" class="ptt10" >
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">

                            <input type="hidden" name="id" id="bpreport_id" >
                            <input type="hidden" name="patient_id_patho" id="bpatientid_patho" >
                            <input type="hidden" name="pathologyid" id="bpathologyid" >

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('reporting') . " " . $this->lang->line('date'); ?></label>
                                        <input type="text"  id="bpedit_report_date" name="reporting_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('reporting_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('refferal') . " " . $this->lang->line('doctor'); ?></label>
                                        <div>
                                            <select class="form-control select2"  style="width: 100%" name='consultant_doctor'  id="bpedit_consultant_doctor">
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
                                        <label><?php echo $this->lang->line('test') . " " . $this->lang->line('report'); ?></label>
                                        <input type="file"  class="filestyle form-control" data-height="40" name="pathology_report">
                                        <span class="text-danger"><?php echo form_error('pathology_report'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>

                                        <input type="text"  class="form-control" readonly="" id="bpcharge_category_html">

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('code'); ?></label>
                                        <input type="text" class="form-control" readonly="" id="bpcode_html">

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" class="form-control" readonly="" id="bpcharge_html">

                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('applied') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?>
                                            <small class="req"> *</small>
                                        </label>
                                        <input type="text" name="apply_charge"  id="bpapply_charge" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                      <!--   <small class="req"> *</small>  -->
                                        <textarea name="description" id="bpedit_description" class="form-control" ></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div><!--./row-->
                    </div><!--./col-md-12-->

                </div><!--./row-->

            </div>


               <!--  <div class="col-md-12" style="clear:both;" >
                         <div class="" id="parameterbilldetails" > </div>
                </div> -->
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" id="parameteraddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right" ><?php echo $this->lang->line('save'); ?></button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade in" id="getTestReportModal" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="false">
    <div class="modal-dialog modal-sm" role="document" style="width: 25%;">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal" autocomplete="off">×</button>
                <h4 class="box-title">Print Test Reports</h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row">

                            <div class="col-sm-12">
                                <div class="form-group15">
                                <label><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></label>
                                <small class="req"> *</small>
                                    <div>
                                        <select onchange="get_PatientBill(this.value)"  class="form-control select2" <?php
                                        if ($disable_option == true) {
                                            //echo "disabled";
                                        }
                                        ?> style="width:100%" name='p_name' id="p_name" >
                                            <option value="" selected ><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                            <?php foreach ($patient_bills as $dkey => $dvalue) {
                                                ?>
                                                <option value="<?php echo $dvalue["patient_id"]; ?>"><?php echo $dvalue["patient_name"] . " ( MR " . $dvalue["patient_unique_id"].'-'.date('d',strtotime($dvalue['patient_add'])).'/'. date('Y',strtotime($dvalue['patient_add'])) . ')' ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group15">
                                <label><?php echo $this->lang->line('bill') . " " . $this->lang->line('no'); ?></label>
                                <small class="req"> *</small>
                                    <div>
                                        <select class="form-control select2" <?php
                                        if ($disable_option == true) {
                                            //echo "disabled";
                                        }
                                        ?> style="width:100%" name='b_id' id="b_id" >
                                            <option value="" ><?php echo $this->lang->line('select') . " " . $this->lang->line('bill') ?></option>

                                        </select>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <div class="box-footer">
                <div class="text-center">
                    <button type="button" onclick="Print_Report();" class="btn btn-info btn-md">Print </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="viewModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_delete'>
                        <a href="#"  data-target="#edit_prescription"  data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>

                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="box-title"><?php echo $this->lang->line('report') . " " . $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModalReport"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletereport'>
                        <a href="#"  data-target="#edit_prescription"  data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>

                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="box-title"><?php echo $this->lang->line('report') . " " . $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdatareport"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModalbill"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                        <a href="#"  data-target="#edit_prescription"  data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>

                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="box-title"><?php echo $this->lang->line('bill') . " " . $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportbilldata"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    function viewDetailReportAdvance(id,pathology_id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/pathology/getReportDetailsAdvance/' + id +'/'+pathology_id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdatareport').html(data);
                $('#edit_deletereport').html("<?php if ($this->rbac->hasPrivilege('pathology bill', 'can_view')) { ?><a href='#' data-toggle='tooltip' onclick='printData(" + id + "," + pathology_id + ")' data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?>");
                holdModal('viewModalReport');
            },
        });
    }
    function viewDetailReport(id,pathology_id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/pathology/getReportDetails/' + id +'/'+pathology_id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdatareport').html(data);
                $('#edit_deletereport').html("<?php if ($this->rbac->hasPrivilege('pathology bill', 'can_view')) { ?><a href='#' data-toggle='tooltip' onclick='printData(" + id + "," + pathology_id + ")' data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?>");
                holdModal('viewModalReport');
            },
        });
    }

      function viewDetailbill(id,pathology_id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/pathology/getBillDetails/' + id +'/'+pathology_id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportbilldata').html(data);
                $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('pathology bill', 'can_view')) { ?><a href='#' data-toggle='tooltip' onclick='printData(" + id + "," + pathology_id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?>");
                holdModal('viewModalbill');
            },
        });
    }

    function deleterecord(id) {
        var url = '<?php echo base_url() ?>admin/pathology/deleteTestReport/' + id;
        var msg = "<?php echo $this->lang->line('delete_message') ?>";
        delete_recordById(url, msg)
    }
    function refundRecord(id,status) {
       // alert(status);
        var url = '<?php echo base_url() ?>admin/pathology/RefundTestReport/' + id+'/'+ status;
        var msg = "<?php echo $this->lang->line('refund_message') ?>";
        refund_recordById(url, msg)
    }

    function delete_recordById(url, Msg) {
                if (confirm(<?php echo "'" . $this->lang->line('delete_conform') . "'"; ?>)) {
                    $.ajax({
                        url: url,
                        success: function (res) {
                            successMsg(Msg);
                            window.location.reload(true);
                        }
                    })
                }
            }
    function refund_recordById(url, Msg) {
                if (confirm(<?php echo "'" . $this->lang->line('refund_confirm') . "'"; ?>)) {
                    $.ajax({
                        url: url,
                        success: function (res) {
                            successMsg(Msg);
                            window.location.reload(true);
                        }
                    })
                }
            }

    function editTestReport(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pathology/getPathologyReport',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {

                $("#report_id").val(data.id);
                $("#charge_category_html").val(data.charge_category);
                $("#code_html").val(data.code);
                $("#charge_html").val(data.standard_charge);
                $("#customer_types").val(data.customer_type);
                $("#opdipd").val(data.opd_ipd_no);
                $("#edit_patient_name").val(data.patient_name);
                $("#edit_report_date").val(data.reporting_date);
                if (data.apply_charge == "") {
                    $("#apply_charge").val(data.standard_charge);
                } else {
                    $("#apply_charge").val(data.apply_charge);
                }
                $('select[id="edit_consultant_doctor"] option[value="' + data.consultant_doctor + '"]').attr("selected", "selected");
                $("#edit_description").val(data.description);
                $(".select2").select2().select2('val', data.patient_id);
                $("#viewModal").modal('hide');
                holdModal('editTestReportModal');


            },
        })
    }

    function addParametervalue(id,pathology_id) {

        //alert(pathology_id);

         $.ajax({
                    url: '<?php echo base_url(); ?>admin/pathology/parameterdetails/' + pathology_id + '/'+ id,
                        success: function (res) {

                            $("#parameterdetails").html(res);
                            //holdModal('viewModal');
                        },
                        error: function () {
                            alert("Fail")
                        }
                    });
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pathology/getPathologyReport',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#preport_id").val(data.id);
                $('#pathologyid').val(pathology_id);
                $("#pcharge_category_html").val(data.charge_category);
                $("#pcode_html").val(data.code);
                $("#pcharge_html").val(data.standard_charge);
                $("#pcustomer_types").val(data.customer_type);
                $("#popdipd").val(data.opd_ipd_no);
                $("#pedit_patient_name").val(data.patient_name);
                $("#pedit_report_date").val(data.reporting_date);
                if(data.show_description==1){
                    $('#show_dscp').prop('checked', true);
                }
                if(data.show_clinical==1){
                    $('#show_clinical').prop('checked', true);
                }
                if(data.show_description_only==1){
                    $('#show_description_only').prop('checked', true);
                }
                $("#pth_discount").attr("readonly",true);
                if (data.apply_charge == "") {
                    $("#papply_charge").val(data.standard_charge);
                } else {
                    if(data.pth_discount!=''){
                        $("#pth_discount").val(data.pth_discount).attr("readonly",true);
                        $("#papply_charge").val(data.apply_charge - data.pth_discount).attr("readonly",true);
                    }else{

                        $("#papply_charge").val(data.apply_charge).attr("readonly",true);
                    }

                }
                //  $('select[id="addpatientid"] option[value="' + data.patient_id + '"]').attr("selected", "selected");

                //$('select[id="pedit_consultant_doctor"] option[value="' + data.consultant_doctor + '"]').attr("selected", "selected");
                $("#pedit_description").val(data.description);

                $("#addpatientidd").select2().select2('val', data.patient_id);
                // $("#pedit_consultant_doctor").select2().select2('val', data.consultant_doctor);
                // Assuming `data.consultant_doctor` contains the value you want to select
                $("#pedit_consultant_doctor").select2().val(data.consultant_doctor).trigger('change');

                // To make the select2 input field read-only, you can disable it
                $("#pedit_consultant_doctor").attr("readonly", true);

                // Update the select2 to reflect the disabled property change
                $("#pedit_consultant_doctor").select2();

                $("#viewModal").modal('hide');
                holdModal('addParametervalueModal');


            },
        })
    }

     function addParameterreportvalue(id,pathology_id) {

        //alert(pathology_id);

         $.ajax({
                    url: '<?php echo base_url(); ?>admin/pathology/parameterdetails/' + pathology_id + '/'+ id,
                        success: function (res) {

                            $("#parameterdetailsreport").html(res);
                            //holdModal('viewModal');
                        },
                        error: function () {
                            alert("Fail")
                        }
                    });
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pathology/getPathologyReport',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {

                $("#rpreport_id").val(data.id);
                $('#rpathologyid').val(pathology_id);
                $("#rpcharge_category_html").val(data.charge_category);
                $("#rpcode_html").val(data.code);
                $("#rpcharge_html").val(data.standard_charge);
                $("#rpcustomer_types").val(data.customer_type);
                $("#rpopdipd").val(data.opd_ipd_no);
                $("#rpedit_patient_name").val(data.patient_name);
                $("#rpedit_report_date").val(data.reporting_date);
                if (data.apply_charge == "") {
                    $("#rpapply_charge").val(data.standard_charge);
                } else {
                    $("#rpapply_charge").val(data.apply_charge);
                }
                //  $('select[id="addpatientid"] option[value="' + data.patient_id + '"]').attr("selected", "selected");

                $('select[id="rpedit_consultant_doctor"] option[value="' + data.consultant_doctor + '"]').attr("selected", "selected");
                $("#rpedit_description").val(data.description);

                $("#raddpatientidd").select2().select2('val', data.patient_id);
                $("#viewModalReport").modal('hide');
                holdModal('addParameterreportvalueModal');


            },
        })
    }


      function addParameterbillvalue(id,pathology_id) {



         $.ajax({
                    url: '<?php echo base_url(); ?>admin/pathology/parameterdetails/' + pathology_id + '/'+ id,
                        success: function (res) {

                            $("#parameterbilldetails").html(res);
                            //holdModal('viewModal');
                        },
                        error: function () {
                            alert("Fail")
                        }
                    });
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pathology/getPathologyReport',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {

                $("#bpreport_id").val(data.id);
                $('#bpathologyid').val(pathology_id);
                $("#bpcharge_category_html").val(data.charge_category);
                $("#bpcode_html").val(data.code);
                $("#bpcharge_html").val(data.standard_charge);
                $("#bpcustomer_types").val(data.customer_type);
                $("#bpopdipd").val(data.opd_ipd_no);
                $("#bpedit_patient_name").val(data.patient_name);
                $("#bpedit_report_date").val(data.reporting_date);
                if (data.apply_charge == "") {
                    $("#bpapply_charge").val(data.standard_charge);
                } else {
                    $("#bpapply_charge").val(data.apply_charge);
                }
                $('select[id="bpedit_consultant_doctor"] option[value="' + data.consultant_doctor + '"]').attr("selected", "selected");
                $("#bpedit_description").val(data.description);
                $("#baddpatientidd").select2().select2('val', data.patient_id);
                $("#viewModalbill").modal('hide');
                holdModal('addParameterbillvalueModal');


            },
        })
    }

    function get_PatientDetails(id) {
        //$("#patient_name").html("patient_name");
        //$("#schedule_charge").html("schedule_charge");

        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                // console.log(res);
                if (res) {
                    $('#patientname_patho').val(res.patient_name);
                    $('#patientid_patho').val(res.id);
                } else {
                    //$('#patient_name').val('Null');

                }
            }
        });
    }

    $(document).ready(function (e) {
        $("#updatetest").on('submit', (function (e) {
            e.preventDefault();
            $("#updatetestbtn").button('loading');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/updateTestReport',
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
                    $("#updatetestbtn").button('reset');
                },
                error: function () {
                    //  alert("Fail")
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#parameteraddbtn").on('click', (function (e) {
            e.preventDefault();
            $("#parameteraddbtn").button('loading');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/parameteraddvalue',
                type: "POST",
                data  : new FormData( $( 'form.parameteradd' )[ 0 ]),
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
                    $("#parameteraddbtn").button('reset');
                },
                error: function () {
                    //  alert("Fail")
                }
            });
        }));
    });

	$(function () {
        $(document).on('keyup', '.txt', function (e) {
            if (e.which == 13) {
                var $this = $(this);
                var $td = $this.closest('td'); // Current TD
                var $row = $td.closest('tr'); // Current TR
                var $rows = $row.parent(); // Current TABLE or TBODY - parent of all rows
                var column = $td.index(); // Current column of TD

                // Search on a row basis in current column, then try next column
                // repeat until we run out of cells
                while ($td.length) {
                    // get next row
                    $row = $row.next('tr');
                    // If we were on last row
                    if ($row.length == 0) {

                        $( "#parameteraddbtn" ).trigger( "click" );
                    }
                    // get the position in the row column - if it exists
                    $td = $row.children().eq(column);
                    var $input = $td.find('.txt');
                    if ($input.length) {
                        $input.focus();
                        break;
                    }
                }
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
    $('.test_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "createdRow": function( row, data, dataIndex ) {
            $(row).children(':nth-child(8)').addClass('pull-right');
        },
        "ajax": {
            "url": base_url+"admin/pathology/report_search",
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
function get_PatientBill(id){
        var base_url = "<?php echo base_url(); ?>backend/images/loading.gif";

        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pathology/getPatientBills',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                //console.log(res);

                if (res) {

                    $('#b_id').empty();
                    $("#b_id").append(new Option("Select Bill ", "")).trigger("chosen:updated");
                    $.each(res, function (i, re) {
                        $('#b_id').append($('<option>', {
                        value: re.bill_no,
                        text : re.bill_no
                        })).trigger("chosen:updated");
                    });

                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").hide();
                }
            }
        });
    }

    function Print_Report() {
        var p_id = $("#p_name").val();
        var b_no=$('#b_id').val();
        //alert(r_date);
            if(p_id === "" || b_no=="" ) {
                if(p_name === "") { $("#p_name").css("border-color", "#EF5350"); }
                if(b_no === "") { $("#b_no").css("border-color", "#EF5350");}
            }
         else {

            window.open("printTestBill/"+p_id+"/"+b_no,"myNewWinsr","width=1000,height=665,toolbar=0, menubar=no,status=no,resizable=no,location=no,directories=no,scrollbars=yes");
        }
    }
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
</script>