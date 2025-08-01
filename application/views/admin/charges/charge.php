<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<style type="text/css">
    #easySelectable {/*display: flex; flex-wrap: wrap;*/}
    #easySelectable li {}
    #easySelectable li.es-selected {background: #2196F3; color: #fff;}
    .easySelectable {-webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;}
</style>
<style>
        /* Full Page Modal Styles */
.modal-dialog {
    margin: 0;
    width: 100%;
    max-width: 100%; /* Ensure dialog is not wider than the screen */
    height: 100vh;
    display: flex;
    flex-direction: column; /* Stack modal content vertically */
    justify-content: center; /* Center modal vertically */
}

.modal-content {
    height: auto; /* Allow modal content to adjust its height */
    min-height: 100vh; /* Minimum height to fill the screen */
    display: flex;
    flex-direction: column; /* Organize content vertically */
}

.modal-header,
.modal-footer {
    flex-shrink: 0; /* Prevent header and footer from shrinking */
}

.modal-body {
    overflow-y: auto; /* Make modal body scrollable */
    flex-grow: 1; /* Allow modal body to expand as needed */
}
    </style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('hospital_charges', 'can_view')) {?> <li>
                                <a href="<?php echo base_url(); ?>admin/charges" class="active"><?php echo $this->lang->line('charges'); ?></a></li>
                        <?php }?>
                        <?php if ($this->rbac->hasPrivilege('charge_category', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/chargecategory/charges"><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></a></li>
                        <?php }?>
                        <?php if ($this->rbac->hasPrivilege('doctor_opd_charges', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/consultcharges"><?php echo $this->lang->line('doctor') . " " . $this->lang->line('opd') . " " . $this->lang->line('charge'); ?></a></li>
                            <li><a href="<?php echo base_url(); ?>admin/consultcharges/emg" ><?php echo $this->lang->line('doctor') . " " . $this->lang->line('emg') . " " . $this->lang->line('charge'); ?></a></li>
                        <?php }?>
                           <?php if ($this->rbac->hasPrivilege('charge_type', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/chargetype" ><?php echo $this->lang->line('charge') . " " . $this->lang->line('type'); ?></a></li>
                        <?php }?>

                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('charges') . " " . $this->lang->line('details'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php
if ($this->rbac->hasPrivilege('hospital_charges', 'can_add')) {
    ?>
                                <a data-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm charge"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('charge'); ?></a>
                            <?php }?>
                            <div class="btn-group">
                                <ul class="dropdown-menu multi-level pull-right width300" role="menu" aria-labelledby="dropdownMenu1" id="easySelectable">
                                    <li><a href="#">All</a></li>
                                    <li><a href="#">Not Sent</a></li>
                                    <li><a href="#">Invoiced</a></li>
                                    <li><a href="#">Not Invoiced</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">Draft</a></li>
                                    <li class="dropdown-submenu pull-left">
                                        <a href="#">Sale Agent</a>
                                        <ul class="dropdown-menu dropdown-menu-left">
                                            <li><a href="#">Edward Thomas</a></li>
                                            <li><a href="#">Robin Peterson</a></li>
                                            <li><a href="#">Nicolas Fleming</a></li>
                                            <li><a href="#">Glen Stark</a></li>
                                            <li><a href="#">Simon Peterson</a></li>
                                            <li><a href="#">Brian Kohlar</a></li>
                                            <li><a href="#">Laura Clinton</a></li>
                                            <li><a href="#">David Heart</a></li>
                                            <li><a href="#">Emma Thomas</a></li>
                                            <li><a href="#">Benjamin Gates</a></li>
                                            <li><a href="#">Kriti Singh</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#">Sent</a></li>
                                    <li><a href="#">Expired</a></li>
                                    <li><a href="#">Declined</a></li>
                                    <li><a href="#">Accepted</a></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('charges') . " " . $this->lang->line('details') . " " . $this->lang->line('list'); ?></div>
                        <table class="custom-table table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('charge_category'); ?></th>
                                    <th><?php echo $this->lang->line('charge_type'); ?></th>
                                    <th><?php echo $this->lang->line('code'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . " (" . $currency_symbol . ")"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
if (empty($resultlist)) {
    ?>

                                    <?php
} else {
    $count = 1;
    foreach ($resultlist as $charge) {
        ?>
                                        <tr class="">
                                            <td>

                                                <?php echo $charge['charge_category']; ?></a>
                                                <div class="rowoptionview">
                                                    <a href="#"
                                                       onclick="viewDetail('<?php echo $charge['id'] ?>')"
                                                       class="btn btn-default btn-xs"  data-toggle="tooltip"
                                                       title="<?php echo $this->lang->line('show'); ?>" >
                                                        <i class="fa fa-reorder"></i>

                                                        <?php
if ($this->rbac->hasPrivilege('hospital_charges', 'can_edit')) {
            ?>
                                                            <a href="#" onclick="getRecord('<?php echo $charge['id'] ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php }if ($this->rbac->hasPrivilege('hospital_charges', 'can_delete')) {?>
                                                            <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_recordById('<?php echo base_url(); ?>admin/charges/delete/<?php echo $charge['id']; ?>', '<?php echo $this->lang->line('delete_message'); ?>')" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php }?>
                                                </div>
                                            </td>
                                            <td><?php echo $charge['charge_type']; ?></td>
                                            <td><?php echo $charge['code']; ?></td>
                                            <td class="text-right"><?php echo $charge['standard_charge']; ?></td>
                                        </tr>
                                        <?php
$count++;
    }
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('charge'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="formadd" accept-charset="utf-8" method="post" class="ptt10" >
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_type'); ?></label>
                                        <small class="req"> *</small>
                                        <select name="charge_type" onchange="getcharge_category(this.value, 'charge_category')" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($charge_type as $key => $value) {
    ?>
                                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_category'); ?></label>
                                        <small class="req"> *</small>
                                        <select name="charge_category" id="charge_category" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('code'); ?></label><small class="req"> *</small>
                                        <input type="text" name="code" class="form-control">
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                        <input type="text" name="standard_charge" id="standard_charge" class="form-control">
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            <input type="checkbox" id="is_package" name="is_package">
                                            <label for="is_package" style="margin-left: 7px;"> is Package?</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label> <?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="description" class="form-control"></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <label><?php echo $this->lang->line('fee_schedule_based_charges'); ?></label>
                                    <button type="button" class="plusign" onclick="apply_to_all()"><?php echo $this->lang->line('apply_to_all'); ?></button>
                                    <div class="chargesborbg">
                                        <div class="form-group">
                                            <table class="custom-tableprintablea4">
                                                <?php foreach ($schedule as $category) {?>
                                                    <tr id="schedule_charge">
                                                    <input type="hidden" name="schedule_charge_id[]" value="<?php echo $category['id']; ?>">
                                                    <td class="col-sm-8" style="vertical-align: bottom; text-align: right; padding-right: 20px;"><?php echo $category['organisation_name']; ?></td>
                                                    <td class="col-sm-4"><input type="text" name="schedule_charge[]" class="form-control"></td>
                                                    </tr>
                                                <?php }?>
                                            </table>
                                            <span class="text-danger"><?php echo form_error('schedule_charge'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12"></div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="row" id="rowContainer">
                                        <div class="col-md-4 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputFile">
                                                            <?php echo $this->lang->line('doctor'); ?></label><small class="req"> *</small>
                                                        <div>
                                                            <select name='doctor[]' class="form-control select2" style="width:100%" >
                                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                <?php foreach ($doctors as $dkey => $dpt) {
                                                                    ?>
                                                                    <option value="<?php echo $dpt["id"]; ?>"><?php echo $dpt["name"]?></option>
                                                            <?php } ?>
                                                            </select>
                                                        </div>
                                                        <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                    </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                <div class="form-group">
                                                        <label for="exampleInputFile">
                                                        Doctor Percentage Type</label>
                                                        <div>
                                                            <select name="doctor_percentage_type[]"  class="form-control" autocomplete="off">
                                                                <option value="">Select Pecntage Type</option>
                                                                <option value="fixed">Fixed</option>
                                                                <option value="percentage">Percentage</option>
                                                            </select>
                                                        </div>
                                                        <span class="text-danger"></span>
                                                </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Percentage/Amount</label>
                                                        <input type="text" class="form-control doctor_percentage" name="doctor_percentage[]" autocomplete="off">
                                                        <span class="text-danger"></span>
                                                    </div>

                                        </div>
                                    </div>

                                    <button type="button" id="addRowBtn" style="color: #2196f3" class="closebtn" autocomplete="off"><i class="fa fa-plus"></i></button>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="row" id="staffRowContainer">
                                        <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputFile">
                                                            <?php echo $this->lang->line('staff'); ?></label><small class="req"> *</small>
                                                        <div>
                                                            <select name='staff[]' class="form-control select2" style="width:100%">
                                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                <?php foreach ($other_staff as $dkey => $dpt) {
                                                                    ?>
                                                                    <option value="<?php echo $dpt["id"]; ?>"><?php echo $dpt["name"]?></option>
                                                            <?php } ?>
                                                            </select>
                                                        </div>
                                                        <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                    </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                <div class="form-group">
                                                        <label for="exampleInputFile">
                                                        Staff Percentage Type</label>
                                                        <div>
                                                            <select name="staff_percentage_type[]" class="form-control" autocomplete="off">
                                                            <option value="">Select Pecntage Type</option>
                                                                <option value="fixed">Fixed</option>
                                                                <option value="percentage">Percentage</option>
                                                            </select>
                                                        </div>
                                                        <span class="text-danger"></span>
                                                </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Percentage/Amount</label>
                                                        <input type="text" class="form-control percentage" name="staff_percentage[]" autocomplete="off">
                                                        <span class="text-danger"></span>
                                                    </div>

                                        </div>
                                    </div>
                                    <button type="button" id="addStaffRowBtn" style="color: #2196f3" class="closebtn" autocomplete="off"><i class="fa fa-plus"></i></button>
                                </div>
                            </div><!--./row-->
                    </div><!--./col-md-12-->
                    <div class="box-footer" style="clear: both;">
                        <div class="pull-right">
                            <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </div>
                    </form>
                </div><!--./row-->
            </div>
        </div>
    </div>
</div>
<!-- dd -->
<div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit') . " " . $this->lang->line('charge'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="formedit" accept-charset="utf-8" method="post" class="ptt10">
                            <div class="row">
                                <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_type'); ?></label>
                                        <small class="req"> *</small>
                                        <select name="charge_type" id="charge_type" onchange="getcharge_category(this.value, 'edit_charge_category')" class="form-control">
                                            <option value="<?php echo set_value('charge_type'); ?>"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($charge_type as $key => $value) {
    ?>
                                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_category'); ?></label>
                                        <small class="req"> *</small>
                                        <select name="charge_category" id="edit_charge_category" class="form-control">
                                            <option value="<?php echo set_value('charge_category'); ?>">Select</option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('code'); ?></label><small class="req"> *</small>
                                        <input type="text" id="code" name="code" value="<?php echo set_value('code'); ?>" class="form-control">
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                        <input type="text" id="standard" name="standard_charge"  value="<?php echo set_value('standard_charge'); ?>"  class="form-control">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            <input type="checkbox" id="package" name="package">
                                            <label for="package" style="margin-left: 7px;"> is Package?</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label> <?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="description" id="description" class="form-control"><?php echo set_value('description'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                </div>

                                <div id="sc_category"></div>
                                <div class="col-md-12 col-sm-12"></div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="row" id="updateRowContainer">
                                        <div class="col-md-4 col-sm-4">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('doctor'); ?></label><small class="req"> *</small>
                                                    <div>
                                                        <select name='doctor[]' id="doctor" class="form-control select2" style="width:100%"   >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($doctors as $dkey => $dpt) {
                                                                ?>
                                                                <option value="<?php echo $dpt["id"]; ?>"><?php echo $dpt["name"]?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                <div class="form-group">
                                                        <label for="exampleInputFile">
                                                        Doctor Percentage Type</label>
                                                        <div>
                                                            <select name="doctor_percentage_type[]" id="doctor_percentage_type"  class="form-control" autocomplete="off">
                                                                <option value="">Select Pecntage Type</option>
                                                                <option value="fixed">Fixed</option>
                                                                <option value="percentage">Percentage</option>
                                                            </select>
                                                        </div>
                                                        <span class="text-danger"></span>
                                                </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Doctor Percentage</label>
                                                        <input type="text" class="form-control doctor_percentage" id="doctor_percentage" name="doctor_percentage[]" autocomplete="off">
                                                        <span class="text-danger"></span>
                                                    </div>

                                        </div>
                                        <div class="col-md-12 col-sm-12"></div>
                                    </div>
                                    <button type="button" id="updateRowBtn" style="color: #2196f3" class="closebtn" autocomplete="off"><i class="fa fa-plus"></i></button>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="row" id="editstaffRowContainer">
                                        <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputFile">
                                                            <?php echo $this->lang->line('staff'); ?></label><small class="req"> *</small>
                                                        <div>
                                                            <select name='staff[]' class="form-control select2" id="staff" style="width:100%" >
                                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                <?php foreach ($other_staff as $dkey => $dpt) {
                                                                    ?>
                                                                    <option value="<?php echo $dpt["id"]; ?>"><?php echo $dpt["name"]?></option>
                                                            <?php } ?>
                                                            </select>
                                                        </div>
                                                        <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                    </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                <div class="form-group">
                                                        <label for="exampleInputFile">
                                                        Staff Percentage Type</label>
                                                        <div>
                                                            <select name="staff_percentage_type[]" id="staff_percentage_type" class="form-control" autocomplete="off">
                                                            <option value="">Select Pecntage Type</option>
                                                                <option value="fixed">Fixed</option>
                                                                <option value="percentage">Percentage</option>
                                                            </select>
                                                        </div>
                                                        <span class="text-danger"></span>
                                                </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Staff Discount</label>
                                                        <input type="text" id="staff_percentage" class="form-control percentage" name="staff_percentage[]" autocomplete="off">
                                                        <span class="text-danger"></span>
                                                    </div>

                                        </div>
                                        <div class="col-md-12 col-sm-12"></div>
                                    </div>
                                    <button type="button" id="updateStaffRowBtn" style="color: #2196f3" class="closebtn" autocomplete="off"><i class="fa fa-plus"></i></button>
                                </div>
                            </div><!--./row-->
                        </div><!--./col-md-12-->
                    <div class="box-footer" style="clear: both;">
                        <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div><!--./row-->
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
                <h4 class="box-title"><?php echo $this->lang->line('charge') . " " . $this->lang->line('information'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="view" accept-charset="utf-8" method="get" class="ptt10">
                            <div class="table-responsive">
                                <table class="custom-table table mb0 table-striped table-bordered examples tablelr0space">
                                    <tr>
                                        <th><?php echo $this->lang->line('charge_type') ?></th>
                                        <td><span id='charge_types'></span></td>
                                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                                        <td><span id='charge_categorys'></span></td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('code'); ?></th>
                                        <td><span id="codes"></span>
                                        </td>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <td><span id='descriptions'></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . " (" . $currency_symbol . ")"; ?></th>
                                        <td><span id="standard_charges"></span></td>
                                        <th>is Package?</th>
                                        <td><input type="checkbox" id="my_package" name="my_package" disabled></td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div><!--./col-md-12-->
                </div><!--./row-->
                <div id="tabledata"></div>
            </div>

            <div class="box-footer">
                <div class="pull-right paddA10">
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();

    })

    function apply_to_all() {
        var standard_charge = $("#standard_charge").val();
        $('input name=schedule_charge_id').val(standard_charge);
    }
</script>
<script type="text/javascript">
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
    function getChargeCategory(charge_type, charge_category) {
        console.log(charge_category)
        $('#edit_charge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            data: {'charge_type': charge_type},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj) {
                    var sel = "";
                    if (charge_category == obj.name) {
                        sel = "selected";
                    }
                    div_data += "<option value='" + obj.name + "'  " + sel + ">" + obj.name + "</option>";
                });
                $("#edit_charge_category").html("");
                $('#edit_charge_category').append(div_data);
            }
        });
    }

    function getcharge_category(id, htmlid) {
        var div_data = "";
        $("#" + htmlid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.name + "'>" + obj.name + "</option>";
                });
                $("#" + htmlid).html("<option value=''>Select</option>");
                $('#' + htmlid).append(div_data);
            }
        });
    }

    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();

            // Check if package checkbox is checked
            var isPackageChecked = $('#is_package').is(':checked');
            // Set package value accordingly
            var packageValue = isPackageChecked ? 1 : 0;
            // Append package value to form data
            var formData = new FormData(this);
            formData.append('is_package', packageValue);

            $.ajax({
                url: '<?php echo base_url(); ?>admin/charges/add_charges',
                type: "POST",
                data: formData,
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
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            $("#formeditbtn").button('loading');
            e.preventDefault();

            // Check if package checkbox is checked
            var isPackageChecked = $('#package').is(':checked');
            // Set package value accordingly
            var packageValue = isPackageChecked ? 1 : 0;
            // Append package value to form data
            var formData = new FormData(this);
            formData.append('package', packageValue);

            $.ajax({
                url: '<?php echo base_url(); ?>admin/charges/update_charges',
                type: "POST",
                data: formData,
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

                }
            });
        }));
    });

    $(document).ready(function (e) {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        $('#dates_of_birth , #date_of_birth').datepicker();
    });

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getDetails',
            type: "POST",
            data: {charges_id: id},
            dataType: 'json',
            success: function (data) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/charges/scheduleChargeBatchGet',
                    type: "POST",
                    data: {charges_id: id},
                    success: function (res) {
                        $('#sc_category').html(res);
                    },
                });
                $("#id").val(data.id);
                $("#charge_type").val(data.charge_type);
                getChargeCategory(data.charge_type, data.charge_category);
                $("#charges_category").val(data.charge_category);
                 $("#code").val(data.code);
                $("#description").val(data.description);
                $("#standard").val(data.standard_charge);
                $("#updateid").val(id);
                var isPackage = data.is_package;
                if (isPackage == 1) {
                    $('#package').prop('checked', true);
                } else {
                    $('#package').prop('checked', false);
                }
                // $("#staff_percentage_type").val(data.staff_percentage_type);
                // $("#staff_percentage").val(data.staff_percentage);

                //var staffToPreSelect = data.all_staff.map(function(staff) { return staff.staff_id; });

                var doctorsArray = data.all_staff.filter(item => item.type === 'doctor');
                var staffArray = data.all_staff.filter(item => item.type === 'staff');
                doctorsArray.forEach((doctor, index) => {
                    if(index==0){
                        $('#doctor').val(doctor.staff_id).trigger('change');
                        $("#doctor_percentage_type").val(doctor.percentage_type);
                        $("#doctor_percentage").val(doctor.staff_percentage);
                    }else{

                        var selecteddoctorId=doctor.staff_id;
                        var selected_doctor_percentage_type=doctor.percentage_type;
                        var selected_doctor_percentage=doctor.staff_percentage;
                        var doctorsOptions = `<?php foreach ($doctors as $dkey => $dpt) {
                            echo "<option value='" . $dpt["id"] . "'>" . htmlspecialchars($dpt["name"], ENT_QUOTES, 'UTF-8') . "</option>";
                        } ?>`;
                        ;
                            var newRow = `
                                <div class="row appendedRow" style="margin-left: 2px;">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label>Doctor</label><small class="req"> *</small>
                                            <select name='doctor[]' class="form-control select2" style="width:100%">
                                                <option value="">Select</option>
                                                ` + doctorsOptions + `
                                            </select>
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label>Doctor Percentage Type</label>
                                            <select name="doctor_percentage_type[]" class="form-control" autocomplete="off">
                                                <option value="">Select Percentage Type</option>
                                                <option value="fixed">Fixed</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <div class="form-group">
                                            <label>Percentage/Amount</label>
                                            <input type="text" class="form-control doctor_percentage" value=" ` + selected_doctor_percentage + `" name="doctor_percentage[]" autocomplete="off">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <button type="button" style="margin-top: 30px; color: red; padding: 0; border: none; background: none;" class="deleteRowBtn" autocomplete="off"><i class="fas fa-times" style="font-size: 1.5rem;"></i></button>
                                    </div>
                                </div>`;

                                $('#updateRowContainer').append(newRow);
                                $('#updateRowContainer').find('select[name="doctor[]"]:last').val(selecteddoctorId);
                                $('#updateRowContainer').find('select[name="doctor_percentage_type[]"]:last').val(selected_doctor_percentage_type);
                                // Re-initialize Select2 for new elements
                                $('.select2').select2();

                    }
                });

                staffArray.forEach((staff, index) => {
                    if(index==0){
                        $('#staff').val(staff.staff_id).trigger('change');
                        $("#staff_percentage_type").val(staff.percentage_type);
                        $("#staff_percentage").val(staff.staff_percentage);
                    }
                    else {
                            var staffOptions = `<?php foreach ($other_staff as $dkey => $dpt) {
                                echo "<option value='" . $dpt["id"] . "'> " . htmlspecialchars($dpt["name"], ENT_QUOTES, 'UTF-8') . "</option>";
                            } ?>`;
                            var selectedStaffId = staff.staff_id;
                            var selected_staff_percentage_type = staff.percentage_type;
                            var selected_staff_percentage = staff.staff_percentage;

                            // Check if the staff ID already exists
                            var staffExists = false;
                            $('#editstaffRowContainer').find('select[name="staff[]"]').each(function() {
                                if ($(this).val() == selectedStaffId) {
                                    staffExists = true;
                                    return false; // break the loop
                                }
                            });

                            if (!staffExists) {
                                var newRow = `
                                    <div class="row staffRow" style="margin-left: 2px;">
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label>Staff</label><small class="req"> *</small>
                                                <select name='staff[]' class="form-control select2" style="width:100%">
                                                    <option value="">Select</option>
                                                    ` + staffOptions + `
                                                </select>
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label>Staff Percentage Type</label>
                                                <select name="staff_percentage_type[]" class="form-control" autocomplete="off">
                                                    <option value="">Select Percentage Type</option>
                                                    <option value="fixed">Fixed</option>
                                                    <option value="percentage">Percentage</option>
                                                </select>
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2">
                                            <div class="form-group">
                                                <label>Percentage/Amount</label>
                                                <input type="text" class="form-control percentage" value="` + selected_staff_percentage + `" name="staff_percentage[]" autocomplete="off">
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2">
                                            <button type="button" style="margin-top: 30px; color: red; padding: 0; border: none; background: none;" class="deleteStaffRowBtn" autocomplete="off"><i class="fas fa-times" style="font-size: 1.5rem;"></i></button>
                                        </div>
                                    </div>`;

                                $('#editstaffRowContainer').append(newRow);

                                // Re-initialize Select2 for new elements
                                $('#editstaffRowContainer').find('select[name="staff[]"]:last').val(selectedStaffId);
                                $('#editstaffRowContainer').find('select[name="staff_percentage_type[]"]:last').val(selected_staff_percentage_type);
                                $('.select2').select2();
                            }
                        }

                });


                // Pre-select the staff members by setting the value
                //$('#doctor').val(staffToPreSelect).trigger('change');
                //$('#staff').val(staffToPreSelect).trigger('change');

                holdModal('myModaledit');
            }
        });
    }

    function viewDetail(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getDetails',
            type: "POST",
            data: {charges_id: id},
            dataType: 'json',
            success: function (data) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/charges/getScheduleChargeBatch',
                    type: "POST",
                    data: {charges_id: id},
                    success: function (data) {
                        $('#tabledata').html(data);
                    },
                });
                $("#charge_types").html(data.charge_type);
                $("#charge_categorys").html(data.charge_category);
                $("#codes").html(data.code);
                $("#descriptions").html(data.description);
                $("#standard_charges").html(data.standard_charge);
                
                // Check if data.is_package is 1
                var isChecked = data.is_package == 1;
                // Set the checked attribute of the checkbox based on isChecked
                $('#my_package').prop('checked', isChecked);

                holdModal('viewModal');
            }
        });
    }

    function apply_to_all() {
        var total = 0;
        var standard_charge = $("#standard_charge").val();
        var schedule_charge = document.getElementsByName('schedule_charge[]');
        for (var i = 0; i < schedule_charge.length; i++) {
            var inp = schedule_charge[i];
            inp.value = standard_charge;
        }
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

$(".charge").click(function(){
    $('#formadd').trigger("reset");
});

$(function () {
    $('.select2').select2()
})



// $(document).on('change', '#doctor_percentage_type', function() {
//     if($('#doctor_percentage_type').val()=='fixed'){
//         if($('#standard_charge').val()!==''){
//             let apply_charge=$('#standard_charge').val();
//             let opd_discount=$('#doctor_percentage').val();
//             let amount=( Number(apply_charge) - Number(opd_discount));
//             $('#doctor_percentage').val("");
//             $('#apply_charge').val(amount);
//         }else{
//             errorMsg("Applied charges should not be empty!");
//         }

//     }else{
//         let apply_charge=$('#applied_total').val();
//         let opd_discount=$('#opd_discount').val();
//         let discounted=( Number(apply_charge) * (Number(opd_discount)/100));
//         let amount=( Number(apply_charge) - Number(discounted));
//         $('#apply_charge').val("");
//         $('#apply_charge').val(amount);
//     }

// });

</script>
<script>
$(document).ready(function(){
    // Initialize Select2 for existing elements
    $('.select2').select2();

    $('#addRowBtn').click(function(){
        // PHP code for doctors options
        var doctorsOptions = `<?php foreach ($doctors as $dkey => $dpt) {
    echo "<option value='" . $dpt["id"] . "'>" . htmlspecialchars($dpt["name"], ENT_QUOTES, 'UTF-8') . "</option>";
} ?>`;
;

        var newRow = `
        <div class="row appendedRow" style="margin-left: 2px;">
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Doctor</label><small class="req"> *</small>
                    <select name='doctor[]' class="form-control select2" style="width:100%">
                        <option value="">Select</option>
                        ` + doctorsOptions + `
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Doctor Percentage Type</label>
                    <select name="doctor_percentage_type[]" class="form-control" autocomplete="off">
                        <option value="">Select Percentage Type</option>
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <div class="form-group">
                    <label>Percentage/Amount</label>
                    <input type="text" class="form-control doctor_percentage" name="doctor_percentage[]" autocomplete="off">
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <button type="button" style="margin-top: 30px; color: red; padding: 0; border: none; background: none;" class="deleteRowBtn" autocomplete="off"><i class="fas fa-times" style="font-size: 1.5rem;"></i></button>
            </div>
        </div>`;

        $('#rowContainer').append(newRow);

        // Re-initialize Select2 for new elements
        $('.select2').select2();
    });

    $(document).on('click', '.deleteRowBtn', function(){
        $(this).closest('.appendedRow').remove();
    });
});

$(document).ready(function(){
    // Initialize Select2 for existing elements
    $('.select2').select2();

    $('#addStaffRowBtn').click(function(){
        // PHP code for doctors options
        var staffOptions = `<?php foreach ($other_staff as $dkey => $dpt) {
    echo "<option value='" . $dpt["id"] . "'>" . htmlspecialchars($dpt["name"], ENT_QUOTES, 'UTF-8') . "</option>";
} ?>`;


        var newRow = `
        <div class="row staffRow" style="margin-left: 2px;">
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Staff</label><small class="req"> *</small>
                    <select name='staff[]' class="form-control select2" style="width:100%">
                        <option value="">Select</option>
                        ` + staffOptions + `
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Staff Percentage Type</label>
                    <select name="staff_percentage_type[]" class="form-control" autocomplete="off">
                        <option value="">Select Percentage Type</option>
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <div class="form-group">
                    <label>Percentage/Amount</label>
                    <input type="text" class="form-control percentage" name="staff_percentage[]" autocomplete="off">
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <button type="button" style="margin-top: 30px; color: red; padding: 0; border: none; background: none;" class="deleteStaffRowBtn" autocomplete="off"><i class="fas fa-times" style="font-size: 1.5rem;"></i></button>
            </div>
        </div>`;

        $('#staffRowContainer').append(newRow);

        // Re-initialize Select2 for new elements
        $('.select2').select2();
    });

    $(document).on('click', '.deleteStaffRowBtn', function(){
        $(this).closest('.staffRow').remove();
    });
});


///////////////////////update modal /////////////////////////

$(document).ready(function(){
    // Initialize Select2 for existing elements
    $('.select2').select2();

    $('#updateRowBtn').click(function(){
        // PHP code for doctors options
        var doctorsOptions = `<?php foreach ($doctors as $dkey => $dpt) {
    echo "<option value='" . $dpt["id"] . "'>" . htmlspecialchars($dpt["name"], ENT_QUOTES, 'UTF-8') . "</option>";
} ?>`;
;

        var newRow = `
        <div class="row appendedRow" style="margin-left: 2px;">
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Doctor</label><small class="req"> *</small>
                    <select name='doctor[]' class="form-control select2" style="width:100%">
                        <option value="">Select</option>
                        ` + doctorsOptions + `
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Doctor Percentage Type</label>
                    <select name="doctor_percentage_type[]" class="form-control" autocomplete="off">
                        <option value="">Select Percentage Type</option>
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <div class="form-group">
                    <label>Percentage/Amount</label>
                    <input type="text" class="form-control " name="doctor_percentage[]" autocomplete="off">
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <button type="button" style="margin-top: 30px; color: red; padding: 0; border: none; background: none;" class="deleteRowBtn" autocomplete="off"><i class="fas fa-times" style="font-size: 1.5rem;"></i></button>
            </div>
        </div>`;

        $('#updateRowContainer').append(newRow);

        // Re-initialize Select2 for new elements
        $('.select2').select2();
    });

    $(document).on('click', '.deleteRowBtn', function(){
        $(this).closest('.appendedRow').remove();
    });
});

$(document).ready(function(){
    // Initialize Select2 for existing elements
    $('.select2').select2();

    $('#updateStaffRowBtn').click(function(){
        // PHP code for doctors options
        var staffOptions = `<?php foreach ($other_staff as $dkey => $dpt) {
            echo "<option value='" . $dpt["id"] . "'>" . htmlspecialchars($dpt["name"], ENT_QUOTES, 'UTF-8') . "</option>";
        } ?>`;


        var newRow = `
        <div class="row staffRow" style="margin-left: 2px;">
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Staff</label><small class="req"> *</small>
                    <select name='staff[]' class="form-control select2" style="width:100%">
                        <option value="">Select</option>
                        ` + staffOptions + `
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="form-group">
                    <label>Staff Percentage Type</label>
                    <select name="staff_percentage_type[]" class="form-control" autocomplete="off">
                        <option value="">Select Percentage Type</option>
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <div class="form-group">
                    <label>Percentage/Amount</label>
                    <input type="text" class="form-control percentage" name="staff_percentage[]" autocomplete="off">
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-md-2 col-sm-2">
                <button type="button" style="margin-top: 30px; color: red; padding: 0; border: none; background: none;" class="deleteStaffRowBtn" autocomplete="off"><i class="fas fa-times" style="font-size: 1.5rem;"></i></button>
            </div>
        </div>`;

        $('#editstaffRowContainer').append(newRow);

        // Re-initialize Select2 for new elements
        $('.select2').select2();
    });

    $(document).on('click', '.deleteStaffRowBtn', function(){
        $(this).closest('.staffRow').remove();
    });
});
</script>

