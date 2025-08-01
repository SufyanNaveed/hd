<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<style type="text/css">

  
    /*body{padding-right: 0 !important;}*/

    #myModaledit{overflow-y: scroll;}
    #myModaledit{position: absolute; top:0; bottom:0; left: 0; right:0; margin-left: auto; margin-right: auto;height: 82rem;}
    /*.modal{
      overflow-y: scroll !important;
      overflow-x: hidden !important;
      margin-right: -17px !important;
      max-height: calc(100vh - 0px);
    }*/


    
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row"><div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                        <li>
                            <a href="<?php echo base_url(); ?>admin/" class=""><?php echo $this->lang->line('opd') ; ?></a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>admin/" class=""><?php echo $this->lang->line('ipd')  ; ?></a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>admin/" class=""><?php echo $this->lang->line('pathology')  ; ?></a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>admin/" class=""><?php echo $this->lang->line('radiology') ; ?></a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>admin/patientbill/searchoperation" class="active"><?php echo $this->lang->line('operation_theatre'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>admin/patientbill/searchbloodissue" class=""><?php echo $this->lang->line('blood_bank') ; ?></a>
                        </li>
                         <li>
                            <a href="<?php echo base_url(); ?>admin/patientbill/searchambulance" class=""><?php echo $this->lang->line('ambulance') ; ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('operation_theatre') . " " . $this->lang->line('patient'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('ot_patient', 'can_add')) { ?>   
                                <a data-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addpatient"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('patient'); ?></a> 
                            <?php } ?>
                        </div>  
                    </div><!-- /.box-header -->
                    <?php
                    if (isset($resultlist)) {
                        ?>
                        <div class="box-body">
                            <div class="download_label"><?php echo $this->lang->line('operation_theatre') . " " . $this->lang->line('patient'); ?></div>
                            <table class="custom-table table table-striped table-bordered table-hover test_ajax" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('bill') . " " . $this->lang->line('no'); ?></th>
                                        <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('id'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('operation') . " " . $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('operation') . " " . $this->lang->line('type'); ?></th>
                                        <th><?php echo $this->lang->line('consultant'); ?></th>
                                        <th><?php echo $this->lang->line('operation') . " " . $this->lang->line('date'); ?>
                                        <th class=""><?php echo $this->lang->line('amount'). " (" . $currency_symbol . ")"; ?></th>
                                        </th>
                                        <th><?php echo $this->lang->line('discount'). " (" . $currency_symbol . ")"; ?></th>
                                        <th><?php echo $this->lang->line('tax'). " (" . $currency_symbol . ")"; ?></th>
                                        <th><?php echo $this->lang->line('total')." ".$this->lang->line('amount'). " (" . $currency_symbol . ")"; ?></th>
                                        <th><?php echo $this->lang->line('paid') . " " . $this->lang->line('amount'). " (" . $currency_symbol . ")"; ?></th>
                                        <th><?php echo $this->lang->line('balance') . " " . $this->lang->line('amount'). " (" . $currency_symbol . ")"; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <!--     <?php
                                    if (empty($resultlist)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
             foreach ($resultlist as $optpatient) {
                                            ?>
                <tr class="">
                <td><?php echo $optpatient['bill_no']; ?>

                </td>
                  <td>
           <?php if ($this->rbac->hasPrivilege('ot_patient', 'can_view')) { ?>   
            <a href="#"   onclick="viewDetail('<?php echo $optpatient['pid'] ?>')" data-toggle="tooltip" title="<?php echo $this->lang->line('detail'); ?>"  href="<?php echo base_url(); ?> student/view/<?php echo $optpatient['id']; ?>">
            
              <?php echo $optpatient['patient_name']; ?>
                     </a>
              <?php } ?>

              <div class="rowoptionview">
             <?php
            if ($this->rbac->hasPrivilege('ot_consultant_instruction', 'can_add')) {
                ?>
          <a href="#" onclick="add_instruction('<?php echo $optpatient['pid'] ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('consultant'); ?> <?php echo $this->lang->line('instruction'); ?> " >
          <i class="fa fa-user-md"></i>
           </a> 
            <?php } ?> 
            
             <?php if ($this->rbac->hasPrivilege('ot_patient', 'can_view')) { ?>         
           <a href="#" onclick="viewDetail('<?php echo $optpatient['pid'] ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" >
          <i class="fa fa-reorder"></i></a>
             <?php } ?>
        
         <?php if ($this->rbac->hasPrivilege('ot_patient', 'can_view')) { ?>         
           <a href="#"  onclick="viewDetailBill('<?php echo $optpatient['id'] ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip"  title="<?php echo $this->lang->line('print'); ?>" >
            <i class="fa fa-print"></i></a>
              <?php } ?>

                                        </div>  
                                                </td>
                                                <td><?php echo $optpatient["patient_unique_id"] ?></td>

                                                <td><?php echo $optpatient['gender']; ?></td>
                                                <td><?php echo $optpatient['mobileno']; ?></td>
                                                <td><?php echo $optpatient['operation_name']; ?></td>
                                                <td><?php echo $optpatient['operation_type']; ?></td>
                                                <td><?php echo $optpatient['name'] . " " . $optpatient['surname']; ?></td>
                                                <td><?php echo date($this->customlib->getSchoolDateFormat(), strtotime($optpatient['date'])) ?></td>
                                                <td><?php echo $optpatient['apply_charge']; ?>
                                                    
                                                </td>
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                    }
                                    ?> -->
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>  
            </div>
        </div> 
    </section>
</div>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg modalfullmobile" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group15">                                     
                            <div>
                                <select onchange="get_PatientDetails(this.value)" class="form-control select2" <?php
                                if ($disable_option == true) {
                                   // echo "disabled";
                                }
                                ?> style="width:100%" id="addpatient_id" name='' >
                                    <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient'); ?></option>
                                    <?php foreach ($patients as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue["id"]; ?>" <?php
                                        if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                            echo "selected";
                                        }
                                        ?>><?php echo $dvalue["patient_name"] . " (" . $dvalue["patient_unique_id"] . ")" ?>
                                        </option>   
<?php } ?>
                                </select>
                            </div>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div><!--./col-sm-6 col-xs-8 -->

                    <div class="col-sm-4 col-xs-5">
                        <div class="form-group15"><?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?><a data-toggle="modal" id="addpip" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new') . " " . $this->lang->line('patient') ?></span></a> <?php } ?> 
                        </div>
                    </div>
                </div><!--./row--> 
            </div>

            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formadd" name="formadd" accept-charset="utf-8" action="<?php echo base_url("admin/patient/add_inpatient") ?>" enctype="multipart/form-data" method="post">
                            <input  id="patientuniqueid" name="patient_unique_id" placeholder="" type="hidden" class="form-control"  value="<?php echo set_value('patient_unique_id'); ?>" />
                            <input  id="patientidot" name="patient_id" placeholder="" type="hidden" class="form-control"  value="" />
                            <input  id="patientname" name="patientname" placeholder="" type="hidden" class="form-control"  value="" />

                            <div class="row row-eq">
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    <div id="ajax_load"></div>
                                    <div class="row ptt10" id="patientDetails" style="display:none">

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
                                                <img class="profile-user-img img-responsive" src="<?php echo base_url() . $file ?>" id="image" alt="User profile picture">
                                            </div>           
                                        </div><!-- ./col-md-3 --> 
                                    </div>

                                     <div class="row">
                                        <div class="col-md-12"> 
                                            <div class="dividerhr"></div>
                                        </div><!--./col-md-12-->
                                        <div class="col-sm-2 col-xs-4">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('height'); ?></label> 
                                                <input name="height" type="text" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-4">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('weight'); ?></label> 
                                                <input name="weight" type="text" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-4">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('bp'); ?></label> 
                                                <input name="bp" type="text" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-4">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('pulse'); ?></label> 
                                                <input name="pulse" type="text" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-4">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('temperature'); ?></label> 
                                                <input name="temperature" type="text" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-4">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('respiration'); ?></label> 
                                                <input name="respiration" type="text" class="form-control" />
                                            </div>
                                        </div>

                                   <div class="col-sm-3 col-xs-4">
                                        <div class="form-group">
                                                <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('symptoms')." ".$this->lang->line('type') ; ?></label>
                                                <div><select  name='symptoms_type'  id="act"  class="form-control select2 act"  style="width:100%">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                            ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option>

                                                    <?php } ?>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                            </div>
                                        </div>
                                        
                                          <input name="rows[]" type="hidden" value="1">
                                            <div class="col-sm-3 col-xs-4">
                                                <label for="exampleInputFile"> 
                                                    <?php echo $this->lang->line('symptoms')." ".$this->lang->line('title') ; ?></label>
                                                <div id="dd" class="wrapper-dropdown-3">
                                                    <input class="form-control filterinput" type="text">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li><label class="checkbox"><?php echo $this->lang->line('select') ?></label></li>
                                                    </ul>
                                                </div>
                                            </div>

                                       <div class="col-sm-6 col-xs-4">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('symptoms')." ".$this->lang->line('description'); ?></label>
                                                <textarea class="form-control" id="symptoms_description" name="symptoms" ></textarea> 
                                            </div> 
                                        </div>
                                       
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('note'); ?></label> 
                                                <textarea name="note" rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                            </div>
                                        </div>     
                                    </div><!--./row-->
                                </div><!--./col-md-6--> 
                                <div class="col-lg-5 col-md-5 col-sm-5 col-eq ptt10">               
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="operation_name --r"><?php echo $this->lang->line('operation') . " " . $this->lang->line('name'); ?></label>
                                                <small class="req"> *</small> 
                                                <input id="number" autocomplete="off" name="operation_name" placeholder="" type="text" class="form-control"/>
                                                <span class="text-danger"><?php echo form_error('operation_name'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('operation') . " " . $this->lang->line('type'); ?></label>

                                                <input type="text" name="operation_type" class="form-control">
                                                <span class="text-danger"><?php echo form_error('operation_type'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('operation') . " " . $this->lang->line('date'); ?></label>
                                                <small class="req"> *</small> 
                                                <input type="text" value="<?php //echo set_value('email');     ?>" id="date" name="date" class="form-control date">
                                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('consultant') . " " . $this->lang->line('doctor'); ?></label>
                                                <small class="req"> *</small> 
                                                <div><select class="form-control select2"  <?php
                                                    if ($disable_option == true) {
                                                        echo "disabled";
                                                    }
                                                    ?> style="width:100%" id='consultant_doctorid' name='consultant_doctor' >
                                                        <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) {
                                                            ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                    if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                                        echo "selected";
                                                                    }
                                                                    ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>   
<?php } ?>
                                                    </select>
                                                    <input type="hidden" id="consultant_doctorname" name="consultant_doctor">
                                                </div>
                                                <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                            </div>
                                        </div> 
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('assistent') . " " . $this->lang->line('consultant') . " " . '1'; ?></label>
                                                <input type="text" name="ass_consultant_1" class="form-control">                     
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('assistent') . " " . $this->lang->line('consultant') . " " . '2'; ?></label>
                                                <input type="text" name="ass_consultant_2" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('anesthetist'); ?></label>
                                                <input type="text" name="anesthetist" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('anaethesia') . " " . $this->lang->line('type'); ?></label>
                                                <input type="text" name="anaethesia_type" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('ot') . " " . $this->lang->line('technician'); ?></label>
                                                <input type="text" name="ot_technician" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('ot') . " " . $this->lang->line('assistent'); ?></label>
                                                <input type="text" value="<?php //echo set_value('email');     ?>" name="ot_assistant" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('organisation'); ?></label>
                                                <div>
                                                    <select class="form-control" id="organisation"   name='organisation' >
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
<?php foreach ($organisation as $orgkey => $orgvalue) {
    ?>
                                                            <option value="<?php echo $orgvalue["id"]; ?>"><?php echo $orgvalue["organisation_name"] ?></option>   
<?php } ?>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('organisation'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
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
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('code'); ?></label>
                                                <small class="req">*</small> 
                                                <div>
                                                    <select class="form-control" name='code' onchange="getchargeDetails(this.value, 'standard_charge')" id="code" >
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('code'); ?></span>
                                            </div>
                                        </div> 
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('standard') . ' ' . $this->lang->line('charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                                                <small class="req">*</small> 
                                                <div>
                                                    <input readonly="" class="form-control" name='standard_charge' id="standard_charge" >

                                                </div>
                                                <span class="text-danger"><?php echo form_error('code'); ?></span>
                                            </div>
                                        </div> 
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('applied') . ' ' . $this->lang->line('charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                                                <small class="req">*</small> 
                                                <div>
                                                    <input class="form-control" type="text" name="apply_charge" id="apply_charge0"  onchange="multiply(0)" />

                                                </div>
                                                <span class="text-danger"><?php echo form_error('code'); ?></span>
                                            </div>
                                        </div>
                            
                            <div class="col-sm-6">
                               <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                    <div class="input-persign">
                                        <input type="text" name="discount_amount" onchange="multiply(0)" id="discount_amount0" placeholder="Amount" class="form-control text-right">
                                        <div class="input-persign-btn">
                                         <input type="text" name="discount_percent" onchange="multiply(0)" disabled id="discount_percent0" placeholder="%" class="form-control text-center">
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="col-sm-6">
                               <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                  <div class="input-persign">
					                <input type="text" name="tax_amount" onchange="multiply(0)" id="tax_amount0" placeholder="Amount" class="form-control text-right">
                                    <div class="input-persign-btn">
                                        <input type="text" name="tax_percent" onchange="multiply(0)" disabled id="tax_percent0" placeholder="%" class="form-control text-center">
                                    </div>    
					              </div>
                                </div>
                               
                            </div> 
                            <div class="col-sm-6">
                               <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('total')." ".$this->lang->line('amount')  . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                  
                                   <input type="text" name="total_amount" onchange="multiply(0)" id="total_amount0" placeholder="" class="form-control text-right">
                                </div>
                               
                            </div>      
                            <div class="col-sm-6">
                               <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('paid') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                  
                                   <input type="text" name="paid_amount" onchange="multiply(0)" id="paid_amount0" placeholder="" class="form-control text-right">
                                </div>
                               
                            </div>
                             <div class="col-sm-6">
                                 <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('balance') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                   <!--  <input name="balance" type="text" class="form-control" /> -->
                                    <input type="text" name="" id="balance_amount0" disabled placeholder="" class="form-control text-right">
                                    <input type="hidden" name="balance_amount" id="due_amount0" placeholder="" class="form-control text-right">
                                </div> 
                            </div>

                            </div><!--./col-md-4-->
                        </div>

                            </div><!--./row-->   
                            <div class="row">            
                                <div class="box-footer">
                                    <div class="pull-right mrminus8">
                                        <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                    <div class="pull-right" style="margin-right:10px;">
                                        <button type="button"  data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right printsavebtn"><?php echo $this->lang->line('save') . " & " . $this->lang->line('print'); ?></button>
                                    </div>
                                </div>
                            </div><!--./row-->  
                        </form>                       
                    </div><!--./col-md-12-->       

                </div><!--./row--> 

            </div>

        </div>
    </div>    
</div>

<!-- dd -->
<div class="modal fade" id="myModaledit" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg modalfullmobile" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-6 col-xs-8">
                        <div class="form-group15">                                     
                            <div>
                                <select onchange="get_ePatientDetails(this.value)" class="form-control select2" <?php
                            if ($disable_option == true) {
                                echo "disabled";
                            }
                            ?> style="width:100%" id="eaddpatient_id" name='' >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($patients as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue["id"]; ?>" <?php
                                    if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
                                        echo "selected";
                                    }
                                    ?>><?php echo $dvalue["patient_name"] . " (" . $dvalue["patient_unique_id"] . ")" ?>
                                        </option>   
                                    <?php } ?>
                                </select>
                            </div>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div><!--./col-sm-6 col-xs-8 -->

                </div><!--./row-->  
            </div>

            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="formedit" accept-charset="utf-8" method="post">
                            <div class="row row-eq">
                                <div class="col-lg-7 col-md-7 col-sm-7">
                                    <div class="">
                                        <div id="eajax_load"></div>
                                        <div class="row ptt10" id="epatientDetails" style="display:none">

                                            <div class="col-md-9 col-sm-9 col-xs-9">
                                                
                                                <ul class="singlelist">
                                                    <li class="singlelist24bold">
                                                        <span id="elistname"></span></li>
                                                    <li>
                                                        <i class="fas fa-user-secret" data-toggle="tooltip" data-placement="top" title="Guardian"></i>
                                                        <span id="eguardian"></span>
                                                    </li>
                                                </ul>   
                                                <ul class="multilinelist">   
                                                    <li>
                                                        <i class="fas fa-venus-mars" data-toggle="tooltip" data-placement="top" title="Gender"></i>
                                                        <span id="egenders" ></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-tint" data-toggle="tooltip" data-placement="top" title="Blood Group"></i>
                                                        <span id="eblood_group"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-ring" data-toggle="tooltip" data-placement="top" title="Marital Status"></i>
                                                        <span id="emarital_status"></span>
                                                    </li> 
                                                </ul>  
                                                <ul class="singlelist">  
                                                    <li>
                                                        <i class="fas fa-hourglass-half" data-toggle="tooltip" data-placement="top" title="Age"></i>
                                                        <span id="eage"></span>
                                                    </li>    

                                                    <li>
                                                        <i class="fa fa-phone-square" data-toggle="tooltip" data-placement="top" title="Phone"></i> 
                                                        <span id="elistnumber"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="Email"></i>
                                                        <span id="eemail"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-street-view" data-toggle="tooltip" data-placement="top" title="Address"></i>
                                                        <span id="eaddress" ></span>
                                                    </li>

                                                    <li>
                                                        <b><?php echo $this->lang->line('any_known_allergies') ?> </b> 
                                                        <span id="eallergies" ></span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line('remarks') ?> </b> 
                                                        <span id="enote"></span>
                                                    </li>    
                                                </ul>                               
                                            </div><!-- ./col-md-9 -->
                                            <div class="col-md-3 col-sm-3 col-xs-3"> 
                                                <div class="pull-right">  
                                                    <?php
                                                    $file = "uploads/patient_images/no_image.png";
                                                    ?>        
                                                    <img class="profile-user-img img-responsive" src="<?php echo base_url() . $file ?>" id="eimage" alt="User profile picture">
                                                </div>           
                                            </div><!-- ./col-md-3 --> 
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12"> 
                                                <div class="dividerhr"></div>
                                            </div><!--./col-md-12-->
                                            <div class="col-sm-2 col-xs-4">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('height'); ?></label> 
                                                    <input name="height" id="eheight" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-xs-4">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('weight'); ?></label> 
                                                    <input name="weight" id="eweight" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-xs-4">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('bp'); ?></label> 
                                                    <input name="bp" id="ebp" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-xs-4">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('pulse'); ?></label> 
                                                    <input name="pulse" id="epulse" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-xs-4">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('temperature'); ?></label> 
                                                    <input name="temperature" id="etemperature" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-xs-4">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('respiration'); ?></label> 
                                                    <input name="respiration" id="erespiration" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-xs-4">
                                            <div class="form-group">
                                                <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('symptoms')." ".$this->lang->line('type') ; ?></label>
                                                <div><select  name='symptoms_type'  id="act"  class="form-control select2 act"  style="width:100%" >
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                            ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option>   
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                            </div>
                                        </div>
                                            <div class="col-sm-12 col-lg-3">
                                                <label for="exampleInputFile"> 
                                                    <?php echo $this->lang->line('symptoms')." ".$this->lang->line('title') ; ?></label>
                                                <div id="dd" class="wrapper-dropdown-3">
                                                    <input class="form-control filterinput" type="text">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li><label class="checkbox"><?php echo $this->lang->line('select') ?></label></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-lg-6">
                                                <div class="form-group">
                                                    <label for="email"><?php echo $this->lang->line('symtoms')." ".$this->lang->line('description'); ?></label> 
                                                    <textarea row="3" name="symptoms" id="esymptoms" class="form-control" ><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label> 
                                                    <textarea name="note" id="view_note" rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div>
                                            </div>     
                                        </div><!--./row--> 

                                        <input name="otid" id="otid" type="hidden" class="form-control"  value="<?php echo set_value('id'); ?>" />
                                        <input id="epatients_id" name="patientid" type="hidden" class="form-control"  value="<?php echo set_value('patient_id'); ?>" />

                                    </div><!--./row--> 
                                </div><!--./col-md-6--> 
                                <div class="col-lg-5 col-md-5 col-sm-5 col-eq ptt10">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('date'); ?></label>
                                                <small class="req"> *</small> 
                                                <input type="text" value="<?php echo set_value('date'); ?>" id="dates" name="date" class="form-control date" autocomplete="off">
                                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('consultant') . " " . $this->lang->line('doctor'); ?></label>
                                                <small class="req"> *</small> 
                                                <div><select class="form-control select2" <?php
                                                        if ($disable_option == true) {
                                                            echo "disabled";
                                                        }
                                                        ?> style="width: 100%" name='consultant_doctor' id="cons_doctor" >
                                                        <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($doctors as $dkey => $dvalue) {
                                                    ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>   
                                                <?php } ?>
                                                    </select>
                                                    <input type="hidden" name="cons_name" id="cons_name">
                                                </div>
                                                <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="operation_name --r"><?php echo $this->lang->line('operation') . " " . $this->lang->line('name'); ?></label>
                                                <small class="req"> *</small> 
                                                <input id="operation_name" autocomplete="off" name="operation_name" type="text" class="form-control"  value="<?php echo set_value('operation_name'); ?>" />
                                                <span class="text-danger"><?php echo form_error('operation_name'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('operation') . " " . $this->lang->line('type'); ?></label>

                                                <input type="text" id="operation_type"
                                                       value="<?php echo set_value('operation_type'); ?>" name="operation_type" class="form-control">
                                                <span class="text-danger"><?php echo form_error('operation_type'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('assistent') . " " . $this->lang->line('consultant') . " " . '1'; ?>
                                                </label>
                                                <input type="text" id="ass_consultant_1" value="<?php echo set_value('ass_consultant_1'); ?>" name="ass_consultant_1" class="form-control">
                                                <span class="text-danger"><?php echo form_error('ass_consultant_1'); ?>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('assistent') . " " . $this->lang->line('consultant') . " " . '2'; ?></label>
                                                <input type="text" id="ass_consultant_2" value="<?php echo set_value('ass_consultant_2'); ?>" name="ass_consultant_2" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('anesthetist'); ?></label>

                                                <input type="text" id="anesthetist" value="<?php echo set_value('anesthetist'); ?>" name="anesthetist" class="form-control">
                                                <span class="text-danger"><?php echo form_error('anesthetist'); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('anaethesia') . " " . $this->lang->line('type'); ?></label>
                                                <input type="text" id="anaethesia_type" value="<?php echo set_value('anaethesia_type'); ?>" name="anaethesia_type" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('ot') . " " . $this->lang->line('technician'); ?></label>

                                                <input type="text" id="ot_technician" value="<?php echo set_value('ot_technician'); ?>" name="ot_technician" class="form-control">
                                                <span class="text-danger"><?php echo form_error('ot_technician'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('ot') . " " . $this->lang->line('assistent'); ?></label>

                                                <input type="text" id="ot_assistant" value="<?php echo set_value('ot_assistant'); ?>" name="ot_assistant" class="form-control">
                                                <span class="text-danger"><?php echo form_error('ot_assistant'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('organisation'); ?></label>

                                                <div>
                                                    <select class="form-control" name='organisation' id="edit_organisation"  >
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
<?php foreach ($organisation as $orgkey => $orgvalue) {
    ?>
                                                            <option value="<?php echo $orgvalue["id"]; ?>"><?php echo $orgvalue["organisation_name"] ?></option>   
<?php } ?>
                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('organisation'); ?>

                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>
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
                                                <span class="text-danger"><?php echo form_error('charge_category_id'); ?>

                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('code') ?></label>
                                                <small class="req">*</small> 
                                                <div>
                                                    <select class="form-control" name='charge_category_id' onchange="getchargeDetails(this.value, 'edit_standard_charge')" id="edit_code" >
                                                        <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>

                                                    </select>
                                                </div>
                                                <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                                            </div>
                                        </div> 
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line("standard") . " " . $this->lang->line("charge") ?></label><?php echo '(' . $currency_symbol . ')'; ?>
                                                <small class="req">*</small> 
                                                <div>
                                                    <input class="form-control" readonly="" name='standard_charge' id="edit_standard_charge" >

                                                </div>
                                                <span class="text-danger"><?php echo form_error('code'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line("applied") . " " . $this->lang->line("charge") ?></label><?php echo '(' . $currency_symbol . ')'; ?>
                                                <small class="req">*</small> 
                                                <div>
                                                    <input class="form-control" name='apply_charge' id="apply_charge1"  onchange="multiply(1)" >

                                                </div>
                                                <span class="text-danger"><?php echo form_error('code'); ?></span>
                                            </div>
                                        </div>
                                         
                                        <div class="col-sm-6">
                                           <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                 <div class="input-persign">
                                                    <input type="text" name="discount_amount" onchange="multiply(1)" id="discount_amount1" placeholder="Amount" class="form-control text-right">
                                                    <div class="input-persign-btn">
                                                         <input type="text" name="discount_percent" onchange="multiply(1)" disabled id="discount_percent1" placeholder="%" class="form-control text-right">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                           <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                <div class="input-persign">
                                                     <input type="text" name="tax_amount" onchange="multiply(1)" id="tax_amount1" placeholder="Amount" class="form-control text-right">
                                                    <div class="input-persign-btn">
                                                         <input type="text" name="tax_percent" onchange="multiply(1)" disabled id="tax_percent1" placeholder="%" class="form-control text-right">
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="col-sm-6">
                                           <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('total')." ".$this->lang->line('amount')  . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                              
                                               <input type="text" name="total_amount" onchange="multiply(1)" id="total_amount1" placeholder="%" class="form-control text-right">
                                            </div>
                                           
                                        </div>      
                                        <div class="col-sm-6">
                                           <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('paid') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                              
                                               <input type="text" name="paid_amount" onchange="multiply(1)" id="paid_amount1" disabled 
                                               placeholder="" class="form-control text-right">
                                            </div>
                                           
                                        </div>
                                         <div class="col-sm-6">
                                             <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('balance') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                               <!--  <input name="balance" type="text" class="form-control" /> -->
                                                <input type="text" name="" id="balance_amount1" disabled placeholder="" class="form-control text-right">
                                                <input type="hidden" name="balance_amount" id="due_amount1" placeholder="" class="form-control text-right">
                                            </div> 
                                        </div> 
                                    </div><!--./row-->
                                </div><!--./col-lg-6-->
                            </div><!--./row-->
                            <div class="row">            
                                <div class="box-footer">
                                    <div class="pull-right mrminus8">
                                        <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </div><!--./row-->  
                        </form>                      
                    </div><!--./col-md-12-->       

                </div><!--./row--> 

            </div>
        </div>
    </div>    
</div>

<div class="modal fade" id="viewModalBill"  role="dialog" aria-labelledby="myModalLabel">
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
                <div id="reportdataot"></div>
            </div>
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

                        <a href="#" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" ><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="box-title"><?php echo $this->lang->line('operation') . " " . $this->lang->line('information'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="view" accept-charset="utf-8" method="get" class="ptt10">
                            <div class="table-responsive">
                                <div class="col-md-6">
                                    <table class="custom-tableprintablea4 examples">
                                        <tr>
                                            <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                                            <td><span id='patients_name'></span></td>

                                        </tr>
                                        <tr>

                                            <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('id'); ?></th>
                                            <td><span id='patientsids'></span></td>

                                        </tr>
                                       <!--  <tr>
                                            <th><?php echo $this->lang->line('opd_ipd_no'); ?></th>
                                            <td><span id="opd_ipd_no"></span>
                                            </td>

                                        </tr> -->
                                        <tr>
                                            <th><?php echo $this->lang->line('gender') ?></th>
                                            <td><span id="genderes"></span>
                                            </td>

                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('age') ?></th>
                                            <td><span id="age_age"></span>

                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('phone'); ?></th>
                                            <td><span id='mobileno'></span></td>

                                        </tr>

                                        <tr>
                                            <th><?php echo $this->lang->line('guardian_name') ?></th>
                                            <td><span id='guardians_name'></span></td>


                                        </tr>

                                        <tr>
                                            <th><?php echo $this->lang->line('address') ?></th>
                                            <td><span id='guardians_address'></span></td>

                                        </tr>
                                         <tr>
                                            <th><?php echo $this->lang->line('height') ?></th>
                                            <td><span id='view_height'></span></td>

                                        </tr>
                                         <tr>
                                            <th><?php echo $this->lang->line('weight') ?></th>
                                            <td><span id='view_weight'></span></td>

                                        </tr>
                                         <tr>
                                            <th><?php echo $this->lang->line('bp') ?></th>
                                            <td><span id='view_bp'></span></td>

                                        </tr>
                                         <tr>
                                            <th><?php echo $this->lang->line('pulse') ?></th>
                                            <td><span id='view_pulse'></span></td>

                                        </tr>
                                        <tr>
                                            <th width="30%"><?php echo $this->lang->line('temperature') ?></th>
                                            <td><span id='view_temperature'></span></td>

                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('respiration') ?></th>
                                            <td><span id='view_respiration'></span></td>

                                        </tr>
                                         <tr>
                                            <th><?php echo $this->lang->line('symptoms') ?></th>
                                            <td><span id='view_symptoms'></span></td>

                                        </tr>
                                      <!--   <tr>

                                            <th><?php echo $this->lang->line('result'); ?></th>
                                            <td><span id='results'></span></td>

                                        </tr>   -->                           
                                        <tr>
                                            <th><?php echo $this->lang->line('remarks'); ?></th>
                                            <td><span id="remarks"></span>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="custom-tableprintablea4 examples">
                                        <tr>
                                            <th><?php echo $this->lang->line('operation') . " " . $this->lang->line('name'); ?></th>
                                            <td><span id='operations_name'></span></td>
                                        </tr>
                                        <tr>

                                            <th><?php echo $this->lang->line('operation') . " " . $this->lang->line('date') ?></th>
                                            <td><span id="date_s"></span>
                                            </td>
                                        </tr>
                                         <tr>

                                            <th><?php echo $this->lang->line('operation') . " " . $this->lang->line('type') ?></th>
                                            <td><span id="ot_type"></span>
                                            </td>
                                        </tr>
                                        <tr>                                  
                                            <th><?php echo $this->lang->line('consultant') . " " . $this->lang->line('doctor'); ?></th>
                                            <td><span id='cons_doctors'></span></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('assistent') . " " . $this->lang->line('consultant') . " " . '1'; ?></th>
                                            <td><span id="ass_consultants_1"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('assistent') . " " . $this->lang->line('consultant') . " " . '2'; ?></th>
                                            <td><span id='ass_consultants_2'></span></td>

                                        </tr>
                                        <tr>

                                            <th><?php echo $this->lang->line('anesthetist'); ?></th>
                                            <td><span id="anesthetists"></span>
                                            </td>

                                        </tr>
                                        <tr>

                                            <th><?php echo $this->lang->line('anaethesia') . " " . $this->lang->line('type'); ?></th>
                                            <td><span id='anaethesia_types'></span></td>

                                        </tr>

                                        <tr>

                                            <th><?php echo $this->lang->line('ot') . " " . $this->lang->line('technician'); ?></th>
                                            <td><span id="ot_techniciandata"></span>
                                            </td>
                                        </tr>

                                        <tr>                                   
                                            <th><?php echo $this->lang->line('ot') . " " . $this->lang->line('assistent'); ?></th>
                                            <td><span id='ot_assistent'></span></td>                                   
                                        </tr>
                                        <tr>                                   
                                            <th><?php echo $this->lang->line('organisation'); ?></th>
                                            <td><span id="organisation_name"></span>
                                            </td>                               
                                        </tr>
                                        <tr>
                                        <tr>                                   
                                            <th><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></th>
                                            <td><span id="charge_categorys"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('code') . " (" . $this->lang->line('description') . ")"; ?></th>
                                            <td><span id='codes'></span>
                                                <span id="description"></span>
                                            </td>
                                        </tr>

                                        <th><?php echo $this->lang->line('applied') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></th>
                                        <td><span id='apply_chargeview'></span></tr>
                                    </table>
                                </div>


                            </div>
                        </form>
                    </div>
                </div>
                <div id="reportdata"></div>
            </div>    
        </div>
    </div>
</div>

<div class="modal fade" id="add_instruction" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('consultant') . " " . $this->lang->line('instruction'); ?></h4> 
            </div>
            <form id="consultant_register" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="">   
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="row">
                            <div class="col-sm-4">
                               <!--  <input name="patient_id" placeholder="" id="ins_patient_id"  type="hidden" class="form-control" /> -->
                               <input name="patient_id" placeholder="" id="inspatient_id"  type="hidden" class="form-control" />
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="custom-table table tableover table-striped table-bordered table-hover" id="tableID">
                                        <tr>
                                            <th><?php echo $this->lang->line('applied') . " " . $this->lang->line('date') ?><small style="color:red;"> *</small></th>
                                            <th><?php echo $this->lang->line('consultant') ?><small style="color:red;"> *</small></th>
                                            <th><?php echo $this->lang->line('instruction') ?><small style="color:red;"> *</small></th>
                                            <th><?php echo $this->lang->line('instruction') . " " . $this->lang->line('date'); ?><small style="color:red;"> *</small></th>
                                            <!-- <th>Instruction Time</th> -->
                                        </tr>
                                        <tr id="row0">
                                            <td>
                                                <input type="text" name="date[]" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat(true, true))); ?>" class="form-control datetime">
                                                <span class="text-danger"><?php echo form_error('date'); ?>
                                                </span>
                                            </td>
                                            <td> 
                                                <select name="doctor[]" class="form-control select2" <?php
                                                    if ($disable_option == true) {
                                                        //secho "disabled";
                                                    }
                                                    ?> style="width: 100%">
                                                    
                                                <?php foreach ($doctors as $key => $value) {
                                                    ?>
                                                                                                    <option value="<?php echo $value["id"] ?>" <?php
                                                    if ((isset($doctor_select)) && ($doctor_select == $value["id"])) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value["name"] . " " . $value["surname"] ?></option>
                                                <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('doctor[]'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <textarea style="height:28px" name="instruction[]" class="form-control"></textarea>
                                            </td>
                                            <td>
                                                <input value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>"  type="text"  name="insdate[]" class="form-control date">
                                            </td>

                                            <td><button type="button" onclick="add_more()" style="color:#2196f3" class="closebtn"><i class="fa fa-plus"></i></button></td>

                                        </tr>
                                    </table>
                                </div>    
                            </div>
                        </div>
                    </div> 
                </div>        
                <div class="box-footer">
                    <button type="submit" id="consultant_registerbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right" ><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>



        </div>
    </div>   
</div>
<div class="modal fade" id="myPaymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('payment'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="add_payment" accept-charset="utf-8" method="post" class="ptt10" >
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small> 
                                        <input type="hidden" name="operation_id" id="otidpay" class="form-control">
                                        <input type="hidden" name="paid_amount" id="paidamountpay" class="form-control">
                                        <input type="hidden" name="patient_id" id="patient_idpay" class="form-control">
                                        <input type="hidden" name="bill_no" id="billno" class="form-control">
                                        <input type="text" name="amount" id="balanceamountpay" class="form-control">
                                        <input type="hidden" name="total_amount" id="total_amount_ot" class="form-control">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('payment') . " " . $this->lang->line('mode'); ?></label> 
                                        <select class="form-control" name="payment_mode">
                                            <?php foreach ($payment_mode as $key => $value) {
                                                ?>
                                                <option value="<?php echo $key ?>" <?php
                                                if ($key == 'cash') {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value ?></option>
<?php } ?>
                                        </select>    
                                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="payment_date" id="date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" class="filestyle form-control"   name="document">
                                        <span class="text-danger"><?php echo form_error('document'); ?></span> 
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('note'); ?></label> 
                                        <input type="text" name="note" id="note" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">   
                                <div class="box-footer">
                                    <button type="submit" id="add_paymentbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                </div>  
                            </div>   
                        </form>
                    </div>
                </div>
            </div>    
        </div>
    </div> 
</div>
<script>
    var rows = 2;
   

    var link = 1;
    $(document).on('click', '.custom-select', function () {
        var currents = $(this);

        if (currents.parent().find('div.section_checkboxs').is(":visible")) {
            currents.parent().find('div.section_checkboxs').hide();
        } else {
            currents.parent().find('div.section_checkboxs').show();
        }

    });

    function toggleFillColor(obj) {

        // $("#custom-select-option-box").show();
        if ($(obj).prop('checked') == true) {
            console.log($(obj).closest('li'));
            $(obj).closest('li').css("background-color", '#ddd');
        } else {
            $(obj).closest('li').css("background-color", '#FFF');
        }
    }

    $(document).on("click", ".checkbox", function (e) {
        var checkboxObj = $(this).children("input");
        // console.log(checkboxObj);

        toggleFillColor(checkboxObj);
    });

    $(document).click(function (e) {
        e.stopPropagation();
        var container = $(".a");

        //check if the clicked area is dropDown or not
        if (container.has(e.target).length === 0) {
            $("div.section_checkboxs").hide();
        }
    })
</script>
<script type="text/javascript">
    $(document).on('click', '.add-btn', function () {
        var s = "";
        s += "<div class='row'>";
        s += "<input name='rows[]' type='hidden' value='" + rows + "'>";
        s += "<div class='col-md-6'>";
        s += "<div class='form-group'>";
        s += "<label for='act'>Act</label>";
        s += "<select class='form-control act select2' id='act' name='act" + rows + "' data-row_id='" + rows + "'>";
        s += "<option value=''>--Select--</option>";
        s += $('#act-template').html();
        s += "</select>";
        s += "<small class='text text-danger help-inline'></small>";
        s += "</div>";
        s += "</div>";
        s += "<div class='col-md-5'>";
        s += "<label for='validationDefault02'>Section</label>";
        s += "<div id='dd' class='wrapper-dropdown-3'>";
        s += "<input class='form-control filterinput' type='text'>";
        s += "<ul class='dropdown scroll150 section_ul'>";
        s += "<li><label class='checkbox'>--Select--</label></li>";
        s += "</ul>";
        s += "</div>";
        s += "</div>";
        s += "<div class='col-md-1'>";
        s += "<div class='form-group'>";
        s += "<label for='removebtn'>&nbsp;</label>";
        s += "<button type='button' class='form-control btn btn-sm btn-danger remove_row'><i class='fa fa-remove'></i></button>";
        s += "</div>";
        s += "</div>";
        s += "</div>";
        $(".multirow").append(s);
        $('.select2').select2();
        link = 2;
        rows++;
    });
</script>

<script type="text/html" id="act-template">

    
   <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                            ?>
        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option> 
        <?php
    }
    ?>
</script>  

<script>
    $(document).on('change', '.act', function () {
        $this = $(this);
        var sys_val = $(this).val();
        //console.log(sys_val);
        var row_id = $this.data('row_id');
        var section_ul = $(this).closest('div.row').find('ul.section_ul');

        var sel_option = "";
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getPartialsymptoms',
            data: {'sys_id': sys_val, 'row_id': row_id},
            dataType: 'JSON',
            beforeSend: function () {
                // setting a timeout
                $('ul.section_ul').find('li:not(:first-child)').remove();
                $("div.wrapper-dropdown-3").removeClass('active');

            },
            success: function (data) {
           
                section_ul.append(data.record);

            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");

            },
            complete: function () {

            }
        });

    });
</script>
<script type="text/javascript">

    

    $(document).on('click', '.remove_row', function () {
        $this = $(this);
        $this.closest('.row').remove();

    });
    $(document).mouseup(function (e)
    {
        var container = $(".wrapper-dropdown-3"); // YOUR CONTAINER SELECTOR

        if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            $("div.wrapper-dropdown-3").removeClass('active');
        }
    });

    $(document).on('click', '.filterinput', function () {

        if (!$(this).closest('.wrapper-dropdown-3').hasClass("active")) {
            $(".wrapper-dropdown-3").not($(this)).removeClass('active');
            $(this).closest("div.wrapper-dropdown-3").addClass('active');
        }


    });

    $(document).on('click', 'input[name="section[]"]', function () {
        $(this).closest('label').toggleClass('active_section');
    });

    $(document).on('keyup', '.filterinput', function () {

        var valThis = $(this).val().toLowerCase();
        var closer_section = $(this).closest('div').find('.section_ul > li');

        var noresult = 0;
        if (valThis == "") {
            closer_section.show();
            noresult = 1;
            $('.no-results-found').remove();
        } else {
            closer_section.each(function () {
                var text = $(this).text().toLowerCase();
                var match = text.indexOf(valThis);
                if (match >= 0) {
                    $(this).show();
                    noresult = 1;
                    $('.no-results-found').remove();
                } else {
                    $(this).hide();
                }
            });
        }
        ;
        if (noresult == 0) {
            closer_section.append('<li class="no-results-found">No results found.</li>');
        }
    });
</script>

<script type="text/javascript">
    function showtextbox(value) {
        if (value != 'direct') {
            $("#opd_ipd_no").show();
        } else {
            $("#opd_ipd_no").hide();
        }
    }

    // var capital_date_format=date_format.toUpperCase();      
    //         $.fn.dataTable.moment(capital_date_format);

    function add_more() {

        var table = document.getElementById("tableID");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var div = "<td><input type='text' name='date[]' class='form-control datetime'></td><td><select name='doctor[]' class='form-control select2' style='width:100%'><option value=''><?php echo $this->lang->line('select') ?></option><?php foreach ($doctors as $key => $value) { ?><option value='<?php echo $value["id"] ?>'><?php echo $value["name"] . ' ' . $value["surname"] ?></option><?php } ?></select></td><td><textarea name='instruction[]' style='height:28px;' class='form-control'></textarea></td><td><input type='text' name='insdate[]' class='form-control date'></td>";

        var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "<td><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";
        $('.select2').select2();

        // $('.instime').timepicker();
    }

    function delete_row(id) {
        var table = document.getElementById("tableID");
        var rowCount = table.rows.length;
        $("#row" + id).html("");
//table.deleteRow(id);
    }


</script>
<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();
        //stopPropagation();
    })

</script>
<script type="text/javascript">



    function viewDetailBill(id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/operationtheatre/getBillDetails/' + id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdataot').html(data);
                $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('operationtheatre bill', 'can_view')) { ?><a href='#' data-toggle='tooltip' onclick='printData(" + id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?><?php if ($this->rbac->hasPrivilege('operationtheatre bill', 'can_edit')) { ?><a href='#'' onclick='getRecord(" + id + ")' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('operationtheatre bill', 'can_edit')) { ?><a onclick='delete_bill(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                holdModal('viewModalBill');
            },
        });
    }



    function get_PatientDetails(id) {
        //$("#schedule_charge").html("schedule_charge");
        //$('#guardian_name').html("Null");
        var base_url = "<?php echo base_url(); ?>backend/images/loading.gif";
		$("#patientDetails").removeClass('hide');
        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                //console.log(res);

                if (res) {
                    $("#ajax_load").html("");
                    $("#patientDetails").show();
                    $('#patient_unique_id').html(res.patient_unique_id);
                    $('#listname').html(res.patient_name);
                    $('#patientname').val(res.patient_name);
                    $('#patientidot').val(res.id);
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
                    //$("#dob").html(res.dob);
                    $("#bp").html(res.bp);
                    //$("#month").html(res.month);
                    $("#symptoms").html(res.symptoms);
                    $("#known_allergies").html(res.known_allergies);
                    $("#address").html(res.address);
                    $("#note").html(res.note);
                    $("#height").html(res.height);
                    $("#weight").html(res.weight);
                    $("#genders").html(res.gender);
                    $("#marital_status").html(res.marital_status);
                    $("#blood_group").html(res.blood_group);
                    $("#allergies").html(res.known_allergies);
                    //$("#image").attr("src",res.image);
                    $("#image").attr("src", '<?php echo base_url() ?>' + res.image);

                } else {

                    $("#ajax_load").html("");
                    $("#patientDetails").hide();
                }
            }
        });
    }



    function get_ePatientDetails(id) {
        //$("#schedule_charge").html("schedule_charge");
        //$('#guardian_name').html("Null");
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
                    $("#eajax_load").html("");
                    $("#epatientDetails").show();
                    $('#epatient_unique_id').html(res.patient_unique_id);
                    $('#elistname').html(res.patient_name);
                    $('#epatients_id').val(res.id);
                    $('#eguardian').html(res.guardian_name);
                    $('#elistnumber').html(res.mobileno);
                    $('#eemail').html(res.email);

                    if (res.age == "") {
                        $("#eage").html("");
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

                        $("#eage").html(age + "," + month + " " + dob);
                    }

                    /*if(res.age ==""){
                     $("#eage").html("");
                     }else{
                     $("#eage").html(res.age+" Years "+res.month+" Month ("+res.dob+")");
                     }*/
                    $('#edoctname').val(res.name + " " + res.surname);
                    //$("#dob").html(res.dob);
                    $("#ebp").html(res.bp);
                    //$("#month").html(res.month);
                    $("#esymptoms").html(res.symptoms);
                    $("#eknown_allergies").html(res.known_allergies);
                    $("#eaddress").html(res.address);
                    $("#enote").html(res.note);
                    //$("#eheight").html(res.height);
                    //$("#eweight").html(res.weight);
                    $("#egenders").html(res.gender);
                    $("#emarital_status").html(res.marital_status);
                    $("#eblood_group").html(res.blood_group);
                    $("#eallergies").html(res.known_allergies);
                    //$("#image").attr("src",res.image);
                    $("#eimage").attr("src", '<?php echo base_url() ?>' + res.image);

                } else {

                    $("#eajax_load").html("");
                    $("#epatientDetails").hide();
                }
            }
        });
    }


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
    function getPatientIdName(opd_ipd_no) {
        var opd_ipd_patient_type = $("#customer_type").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getPatientType',
            type: "POST",
            data: {opd_ipd_patient_type: opd_ipd_patient_type, opd_ipd_no: opd_ipd_no},
            dataType: 'json',
            success: function (data) {

                $('#patientsid').val(data.patient_id);
                $('.adm_date').val(data.admission_date);
                $('#patientsname').val(data.patient_name);
                $('#patientsage').val(data.age);
                $('#patientsguardian_name').val(data.guardian_name);
                // $('#edit_age').val(data.age);
                // $('#edit_month').val(data.month);
                $('#patientsguardian_address').val(data.guardian_address);
                $('select[id="patientsgender"] option[value="' + data.gender + '"]').attr("selected", "selected");
            }
        });
    }

    function getchargeDetails(id, htmlid) {


        var orgid = $("#organisation").val();

        $('#' + htmlid).val("");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getDetails',
            type: "POST",
            data: {charges_id: id, organisation: orgid},
            dataType: 'json',
            success: function (res) {
                $('#' + htmlid).val(res.standard_charge);

                if (orgid != "") {
                    $('#apply_charge0').val(res.org_charge);
                    $('#apply_charge1').val(res.org_charge);
                } else {
                    $('#apply_charge0').val(res.standard_charge);
                    $('#apply_charge1').val(res.standard_charge);
                }
            }
        });
    }

    function get_symptoms(id) {

       
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getsymptoms',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                   
                        $('#symptoms_description').val(res.description);
                      
                } else{
                    $('#symptoms_description').val("");
                }   
            }
        });
    }

    function getchargecode(charge_category) {
        var div_data = "";
        $('#code').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.code + " - " + obj.description + "</option>";
                });
                $('#code').html("<option value=''>Select</option>");
                $('#code').append(div_data);
            }
        });
    }


    function editchargecode(charge_category, charge_id) {
        var div_data = "";
        $('#edit_code').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj) {
                    var sel = "";
                    if (charge_id == obj.id) {
                        sel = "selected";
                    }
                    div_data += "<option value='" + obj.id + "' " + sel + ">" + obj.code + " - " + obj.description + "</option>";
                });
                $('#edit_code').html("<option value=''>Select</option>");
                $('#edit_code').append(div_data);
            }
        });
    }

        
    $(document).ready(function () {
        $("#birth_date").change(function () {
            var mdate = $("#birth_date").val().toString();
            //console.log(mdate);
            var yearThen = parseInt(mdate.substring(6, 10), 10);
            //console.log(yearThen);
            var monthThen = parseInt(mdate.substring(0, 2), 10);
            //console.log(monthThen);
            var dayThen = parseInt(mdate.substring(3, 5), 10);
            //console.log(dayThen);
            var today = new Date();
            var birthday = new Date(yearThen, monthThen - 1, dayThen);

            var differenceInMilisecond = today.valueOf() - birthday.valueOf();

            var year_age = Math.floor(differenceInMilisecond / 31536000000);
            var day_age = Math.floor((differenceInMilisecond % 31536000000) / 86400000);

            /*if ((today.getMonth() == birthday.getMonth()) && (today.getDate() == birthday.getDate())) {
             alert("Happy B'day!!!");
             }*/

            var month_age = Math.floor(day_age / 30);

            day_age = day_age % 30;

            if (isNaN(year_age) || isNaN(month_age) || isNaN(day_age)) {
                $("#exact_age").text("Invalid birthday - Please try again!");
            } else {
                $("#exact_age").html("You are<br/><span id=\"age\">" + year_age + " years " + month_age + " months " + day_age + " days</span> old");
                //console.log(month_age);
                $("#age_year").val(year_age);
                $("#age_month").val(month_age);
                $("#age_day").val(day_age);

            }
        });
    });

    $(document).ready(function (e) {

        $(".printsavebtn").on('click', (function (e) {
            // $(this).submit();
               var did = $("#consultant_doctorid").val();
            $("#consultant_doctorname").val(did);
            var form = $(this).parents('form').attr('id');
            var str = $("#" + form).serializeArray();
            var postData = new FormData();
            $.each(str, function (i, val) {
                postData.append(val.name, val.value);
            });
            //  $("#"+form).submit();
                     
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/add',
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
                        successMsg(data.message);
                        printData(data.id);

                        // window.location.reload(true);
                    }
                    $("#formaddbtn").button('reset');
                },
                error: function () {
                    //  alert("Fail")
                }
            });


        }));
    });

    function printData(id) {

        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/operationtheatre/getBillDetails/' + id,
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

    function myFunction() {
        alert("This document is now being printed");
    }


    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            var did = $("#consultant_doctorid").val();
            $("#consultant_doctorname").val(did);

            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/add',
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
            var cons = $("#cons_doctor").val();
            $("#cons_name").val(cons);
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/update',
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
        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/operationtheatre/getOtPatientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#eajax_load").html("");
                $("#epatientDetails").show();
                // $('#patient_unique_id').html(res.patient_unique_id);
                $('#elistname').html(data.patient_name);
                $('#epatientidot').val(data.id);
                $('#epatientid').val(data.patient_id);
                $('#eguardian').html(data.guardian_name);
                $('#elistnumber').html(data.mobileno);
                $('#eemail').html(data.email);
                if (data.age == "") {
                    $("#eage").html("");
                } else {
                    if (data.age) {
                        var age = data.age + " " + "Years";
                    } else {
                        var age = '';
                    }
                    if (data.month) {
                        var month = data.month + " " + "Month";
                    } else {
                        var month = '';
                    }
                    if (data.dob) {
                        var dob = "(" + data.dob + ")";
                    } else {
                        var dob = '';
                    }

                    $("#eage").html(age + "," + month + " " + dob);
                }
                //console.log(data.dob);
                $('#edoctname').val(data.name + " " + data.surname);
                $("#ebp").val(data.bp);
                $("#esymptoms").text(data.symptoms);
                $("#eaddress").html(data.address);
                $("#enote").html(data.note);
                $("#eheight").val(data.height);
                $("#eweight").val(data.weight);
                $("#epulse").val(data.pulse);
                $("#etemperature").val(data.temperature);
                $("#erespiration").val(data.respiration);
                $("#egenders").html(data.gender);
                $("#emarital_status").html(data.marital_status);
                $("#eblood_group").html(data.blood_group);
                $("#eallergies").html(data.known_allergies);
                $("#otid").val(data.id);
                $("#epatients_id").val(data.patient_id);
                $("#patientid").val(data.patient_unique_id);
                $("#admissions_date").val(data.admission_date);
                $("#patient_name").val(data.patient_name);
                $("#genders").val(data.gender);
                $("#edit_age").val(data.age);
                $("#edit_month").val(data.month);
                $("#guardian_name").val(data.guardian_name);
                $("#edit_guardian_address").val(data.guardian_address);
                $("#edit_mobileno").val(data.mobileno);
                $("#dates").val(data.date);
                $("#operation_name").val(data.operation_name);
                $("#operation_type").val(data.operation_type);
                //$("#cons_doctor").val(data.consultant_doctor);
                $("#ass_consultant_1").val(data.ass_consultant_1);
                $("#ass_consultant_2").val(data.ass_consultant_2);
                $("#anesthetist").val(data.anesthetist);
                $("#anaethesia_type").val(data.anaethesia_type);
                $("#ot_technician").val(data.ot_technician);
                $("#ot_assistant").val(data.ot_assistant);
                $("#edit_charge_category").val(data.charge_category);
                editchargecode(data.charge_category, data.charge_id);
                $("#edit_standard_charge").val(data.standard_charge);
                $("#apply_charge1").val(data.apply_charge);
                $("#result").val(data.result);
                $("#discount_amount1").val(data.discount);
                $("#tax_amount1").val(data.tax);
                var amount = (data.apply_charge);
                var discount_amount = (data.discount);
                var tax_amount = (data.tax);
                var discount_txt = (discount_amount / amount ) * 100;
                $("#discount_percent1").val((discount_amount / amount ) * 100);
                var tax_txt = (tax_amount / (amount-discount_amount) ) * 100;
                $("#tax_percent1").val(tax_txt);
                $("#total_amount1").val(data.total_amount);
                //console.log(data.paidamount);
                 $("#paid_amount1").val(data.paidamount);
                 var total_amount = (data.total_amount);
                 var paid_amount = (data.paidamount);
                $("#balance_amount1").val(total_amount - paid_amount);
                $("#due_amount1").val(total_amount - paid_amount);
                $("#view_note").text(data.remark);
                $("#updateid").val(id);
                // $('select[id="cons_doctor"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $('select[id="edit_organisation"] option[value="' + data.organisation + '"]').attr("selected", "selected");
                $('select[id="genders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="charge_category_id"] option[value="' + data.charge_category_id + '"]').attr("selected", "selected");
                $("#cons_doctor").select2().select2('val', data.consultant_doctor);
                $("#eaddpatient_id").select2().select2('val', data.patient_id);
                $("#viewModal").modal('hide').css("display", "none");
                $("#viewModalBill").modal('hide');
                holdModal('myModaledit');
            },
        })
    }

    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
    function viewDetail(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/operationtheatre/getDetails',
            type: "POST",
            data: {patient_id: id},
            dataType: 'json',
            success: function (data) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/operationtheatre/getConsultantBatch',
                    type: "POST",
                    data: {patient_id: id},
                    success: function (data) {
                        $('#reportdata').html(data);
                    },
                });
                //console.log(data.opd_ipd_no);
                $("#patientsids").html(data.patient_unique_id);
                $("#admit_date").html(data.admission_date);
                $("#patients_name").html(data.patient_name);
                $("#genderes").html(data.gender);

                if (data.age == "") {
                    $("#age_age").html("");
                } else {
                    if (data.age) {
                        var age = data.age + " " + "Years";
                    } else {
                        var age = '';
                    }
                    if (data.month) {
                        var month = data.month + " " + "Month";
                    } else {
                        var month = '';
                    }
                    if (data.dob) {
                        var dob = "(" + data.dob + ")";
                    } else {
                        var dob = '';
                    }

                    $("#age_age").html(age + "," + month + " " + dob);
                }

                //  $("#age_age").html(data.age + " Year " + data.month + " Month");
                $("#guardians_name").html(data.guardian_name);
                $("#guardians_address").html(data.address);
                //console.log(data.address);
                $("#date_s").html(data.date);
                $("#operations_name").html(data.operation_name);
                $("#operations_type").html(data.operation_type);
                $("#organisation_name").html(data.organisation_name);
                $("#cons_doctors").html(data.name + "\n" + data.surname);
                $("#ass_consultants_1").html(data.ass_consultant_1);
                $("#ass_consultants_2").html(data.ass_consultant_2);
                $("#anesthetists").html(data.anesthetist);
                $("#anaethesia_types").html(data.anaethesia_type);
                $("#ot_technicians").html(data.ot_technician);
                $("#ot_assistants").html(data.ot_assistant);
                $("#charge_categorys").html(data.charge_category);
                $("#codes").html(data.code);
                $("#opd_ipd_no").html(data.opd_ipd_no);
                $("#description").html("(" + data.description + ")");
                $("#stdcharge").html(data.standard_charge);
                $("#results").html(data.result);
                $("#remarks").html(data.remark);
                $("#view_height").html(data.height);
                $("#view_weight").html(data.weight);
                $("#view_bp").html(data.bp);
                $("#view_pulse").html(data.pulse);
                $("#view_temperature").html(data.temperature);
                $("#view_respiration").html(data.respiration);
                $("#view_symptoms").html(data.symptoms);
                $("#ot_type").html(data.operation_type);
                $("#patient_type").html(data.customer_type);
                $("#ot_assistent").html(data.ot_assistant);
                $("#ot_techniciandata").html(data.ot_technician);
                $("#apply_chargeview").html(data.apply_charge);
                $("#mobileno").html(data.mobileno);
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('ot_patient', 'can_edit')) { ?><a href='#'' onclick='getRecord(" + data.id + ")' data-target='#editModal' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } if ($this->rbac->hasPrivilege('ot_patient', 'can_delete')) { ?><a href='#' data-toggle='tooltip'  onclick='delete_record(" + data.id + ","+id+")' data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                holdModal('viewModal');
            },
        })
    }
    $(document).ready(function (e) {
        $("#consultant_register").on('submit', (function (e) {
            $("#consultant_registerbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/add_ot_consultant_instruction',
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
                    $("#consultant_registerbtn").button('reset');
                },
                error: function () {}
            });
        }));
    });

    function add_instruction(id,pid) {
        $('#ins_patient_id').val(id);
        $('#inspatient_id').val(pid);
        holdModal('add_instruction');
    }

    function delete_record(id,patient_id) {

        if (confirm('<?php echo $this->lang->line('delete_conform') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/operationtheatre/delete/' + id,
                type: "POST",
                data: {id: id,'patient_id':patient_id},
                dataType: 'json',
                success: function (data) {
                    successMsg('<?php echo $this->lang->line('delete_message') ?>');
                    window.location.reload(true);
                }
            })
        }
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }
<?php
if (isset($id)) {
    ?>
        viewDetail(<?php echo $id ?>);
    <?php
}
?>

  $(document).ready(function() {
    $('.test_ajax').DataTable({
        "processing": true,
        "serverSide": true,
          "createdRow": function( row, data, dataIndex ) {
            $(row).children(':nth-child(10)').addClass('pull-right');
        },
        "ajax": {
            "url": base_url+"admin/searchdatatable/ot_search",
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

$(".addpatient").click(function(){
	$('#formadd').trigger("reset");
	$("#patientDetails").addClass('hide');
	$('#select2-addpatient_id-container').html('');
	$('#select2-act-container').html('');
});

$(".modalbtnpatient").click(function(){	
	$('#formaddpa').trigger("reset");
	$(".dropify-clear").trigger("click");
});

	function refreshmodal(){
		$('#consultant_register').trigger("reset");
		var table = document.getElementById("tableID");
		var table_len = (table.rows.length);	
		for (i = 1; i < table_len; i++) {			
			delete_row(i);
		}
	}
            function multiply(id) {

                var amount = $('#apply_charge' + id).val();
                var tax = $('#tax_amount' + id).val();
                var discount = $('#discount_amount' + id).val();
               

                if (discount !='') {
                    var discount_txt = (discount / amount ) * 100;
                    $("#discount_percent" + id).val(discount_txt.toFixed(2));
                }else{
                    var discount = 0;
                     $('#discount_amount' + id).val(discount);
                }

                 if (tax != '') {
                    var tax_txt = (tax / (amount-discount) ) * 100;
                     $("#tax_percent" + id).val(tax_txt.toFixed(2));
                }else{
                    tax = 0;
                    $('#tax_amount' + id).val(tax);
                }

                var total_amount = parseFloat(amount)  - parseFloat(discount) + parseFloat(tax);
                 $('#total_amount' + id).val(total_amount);
                var paid_amount = $('#paid_amount' + id).val();
                var balance_amount = total_amount - paid_amount;
                $('#balance_amount' + id).val(balance_amount);
                $('#due_amount' + id).val(balance_amount);
                //$('#balance_amount' + id).prop( "disabled", true );
            }

          
         

             function addpaymentModal(id) {
                 $.ajax({
                    url: '<?php echo base_url(); ?>admin/operationtheatre/getotDetails',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $("#otidpay").val(data.id);
                       // console.log(data);
                        $("#paidamountpay").val(data.paidamount);
                        var paidamount = (data.paidamount);
                        var amount = (data.total_amount);
                        $("#balanceamountpay").val(amount - paidamount) ;
                        $("#billno").val(data.bill_no);
                        $("#patient_idpay").val(data.patient_name);
                        $("#total_amount_ot").val(data.total_amount);
                        holdModal('myPaymentModal');
                    },
                });
            } 

              $(document).ready(function (e) {
            $("#add_payment").on('submit', (function (e) {
               // alert("Hello")
                e.preventDefault();
                $("#add_paymentbtn").button("loading");
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/payment/addotPayment',
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
                        $("#add_paymentbtn").button("reset");
                    }, error: function () {}
                });
            }));
        });

$(".addpayment").click(function(){      
    $('#add_payment').trigger("reset");
    $(".dropify-clear").trigger("click");
});

</script>
<?php $this->load->view('admin/patient/patientaddmodal') ?>