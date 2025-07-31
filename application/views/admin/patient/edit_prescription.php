<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script type="text/javascript">
    $(document).ready(function (e) {
        $('.select2').select2();
    });

    function geteditMedicineName(id, selectid = '',dos='',inst='') {
       //alert('dfssfs');
        var category_selected = $("#editmedicine_cat" + id).val();
        var arr = category_selected.split('-');
        var category_set = arr[0];
        div_data = '';
        $("#editsearch-query" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#editsearch-query' + id).select2("val", +id);
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_name",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {
              //  console.log(res);
                $.each(res, function (i, obj)
                {
                    var sel = "";

                    div_data += "<option '" + sel + "' value='" + obj.medicine_name + "'>" + obj.medicine_name + "</option>";
                });
                $("#editsearch-query" + id).html("<option value=''>Select</option>");
                $('#editsearch-query' + id).append(div_data);
                $('#editsearch-query' + id).select2().select2("val", selectid);
                
                geteditMedicineDosage(id,dos);
                geteditMedicineInstruction(id,inst);
            }
        });
    }
    ;

    function geteditMedicineDosage(id, selectid = '') {
        var category_selected = $("#editmedicine_cat" + id).val();
        console.log(category_selected);
        var arr = category_selected.split('-');
        var category_set = arr[0];

        div_data = '';
        
        $("#editsearch-dosage" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#editsearch-dosage' + id).select2("val", +id);
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_dosage",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {

                $.each(res, function (i, obj)
                {
                    var sel = "";
                    if (selectid == obj.dosage) {
                        sel = "selected";
                    }
                    div_data += "<option '" + sel + "' value='" + obj.dosage + "'>" + obj.dosage + "</option>";
                   
                });
                $("#editsearch-dosage" + id).html("<option value=''>Select</option>");
                $('#editsearch-dosage' + id).append(div_data);
                $('#editsearch-dosage' + id).select2("val", selectid);
            }
        });

    }
    function geteditMedicineInstruction(id, selectid = '') {
       
        var category_selected = $("#editmedicine_cat" + id).val();
        var arr = category_selected.split('-');
        var category_set = arr[0];

        var div_data_inst = '';
        //alert(category_selected);
        $("#editsearch-instruction" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#editsearch-instruction' + id).select2("val", +id);
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_instruction",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {

                $.each(res, function (i, obj)
                {
                    var sel = "";
                    if (selectid == obj.instruction) {
                        sel = "selected";
                    }
                    div_data_inst += "<option '" + sel + "' value='" + obj.instruction + "'>" + obj.instruction + "</option>";
                });
                $("#editsearch-instruction" + id).html("<option value=''>Select</option>");
                $('#editsearch-instruction' + id).append(div_data_inst);

                $('#editsearch-instruction' + id).select2("val", selectid);
            }
        });

    }
    
</script>

    <form id="update_prescription" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="">
        <input type="hidden" name="opd_id" value="<?php echo $result['opd_id'] ?>">
        <div class="">
            <div class="row">
                <div class="col-sm-3">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>
                                        <?php echo "Problems"; ?>
                                        <a data-toggle="modal" onclick="holdModal('add_symtoms')" class="btn btn-primary btn-sm adddiagnosis"><i class="fa fa-plus"></i></a>
                                        </label>
                                        
                                            <select class="form-control select2" style="width: 100%" id="symptom_check" name='symptom[]' multiple>
                                                <option value=""><?php echo $this->lang->line('select') ?>
                                                </option>
                                                <?php foreach ($problems as $dkey => $problem) {
                                                    $selected='';
                                                    if (in_array($problem["id"], $symptoms)){
                                                        $selected='selected';
                                                    }
                                                ?>
                                                <option value="<?php echo $problem["id"]; ?>" <?= $selected; ?>><?php echo $problem["symptoms_type"] ?>
                                                </option>   
                                                <?php } ?>
                                            </select>
                                </div>
                            </div>  
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>
                                        <?php echo $this->lang->line('diagnosis'); ?><a data-toggle="modal" onclick="holdModal('add_diagnosis','append_prescription_diagnosis')" class="btn btn-primary btn-sm adddiagnosis"><i class="fa fa-plus"></i></a></label> 
                                            <select class="form-control select2" style="width: 100%" name='diagnosis[]' multiple>
                                                <option value=""><?php echo $this->lang->line('select') ?>
                                                </option>
                                                <?php foreach ($diagnosis_detail as $dkey => $diagnosis_det) {
                                                    $selected='';
                                                    if (in_array($diagnosis_det["id"], $diagnosis)){
                                                        $selected='selected';
                                                    }
                                                ?>
                                                <option value="<?php echo $diagnosis_det["id"]; ?>" <?= $selected; ?>><?php echo $diagnosis_det["report_type"] ?>
                                                </option>   
                                                <?php } ?>
                                            </select>
                                </div>
                            </div>  
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>
                                        <?php echo $this->lang->line('test') ; ?><a data-toggle="modal" onclick="holdModal('add_investigation','append_prescription_lab')" class="btn btn-primary btn-sm addinvestigation "><i class="fa fa-plus"></i></a></label>
                                            <select class="form-control select2" style="width: 100%" name='lab_test[]' multiple>
                                                <option value=""><?php echo $this->lang->line('select') ?>
                                                </option>
                                                <?php foreach ($lab_report as $tr => $lb_report) {
                                                    $selected='';
                                                    if (in_array($lb_report["id"], $lab_test)){
                                                        $selected='selected';
                                                    }
                                                ?>
                                                <option value="<?php echo $lb_report["id"]; ?>" <?= $selected; ?>><?php echo $lb_report["report_type"] ?>
                                                </option>   
                                                <?php } ?>
                                            </select>
                                </div>
                            </div>  
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>
                                        <?php echo $this->lang->line('precaution') ; ?></label>
                                            <select class="form-control select2" style="width: 100%" name='precaution[]' multiple>
                                                <option value=""><?php echo $this->lang->line('select') ?>
                                                </option>
                                                <?php foreach ($precautions as $pk => $precaution) {
                                                     $selected='';
                                                     if (in_array($precaution["id"], $precaution)){
                                                         $selected='selected';
                                                     }
                                                ?>
                                                <option value="<?php echo $precaution["id"]; ?>" <?= $selected; ?>><?php echo $precaution["precaution"] ?>
                                                </option>   
                                                <?php } ?>
                                            </select>
                                </div>
                            </div>  
                        </div> 
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <div class="ptt10">
                        <div class="row">
                             
                             <!-- <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php //echo $this->lang->line('header_note'); ?></label> 
                                    <textarea name="header_note" class="form-control editor" id="compose-textarea" style="height:50px"><?php echo $result["header_note"] ?></textarea>
                                    

                                </div> 
                            </div> -->

                        <?php 
                        if(!empty($prescription_list)){
                        foreach ($prescription_list as $pkey => $pvalue) {
                        ?>
                        <input type="hidden" name="previous_pres_id[]" value="<?php echo $pvalue['id'] ?>">
                        <input type="hidden" name="opd_no_value" value="<?php echo $result['opd_no'] ?>">
                        <input type="hidden" name="visit_id" value="<?php echo $pvalue['visit_id'] ?>">
                         <?php } }else{ ?>
                        <input type="hidden" name="opd_no_value" value="<?php echo $result['opd_no'] ?>">
                        <input type="hidden" name="visit_id" value="0">
                        <?php  } ?>
                        <table class="custom-tablefullwidthtable" id="edittableID">
                            <?php
                            $i = 0;
                            if(!empty($prescription_list)){

                            foreach ($prescription_list as $key => $value) {
                                ?>
                                <tr id="row<?php echo $i ?>">
                                    <td>      
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <?php if ($i == 0) { ?>
                                                    <label><?php echo $this->lang->line('medicine') . " " . $this->lang->line("category"); ?></label> <small class="req"> *</small>
                                                <?php } ?>
                                                <select class="form-control select2" style="width: 100%" name='medicine_cat[]' onchange="geteditMedicineName('<?php echo $i ?>')"  id="editmedicine_cat<?php echo $i ?>">
                                                    <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                    </option>
                                                    <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                        ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                        if ($value['medicine_category_id'] == $dvalue["id"]) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $dvalue["medicine_category"] ?>
                                                        </option>   
    <?php } ?>
                                                </select>
                                            </div>
                                        </div>                              
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                    <?php if ($i == 0) { ?>
                               
                                                    <label>
                                                <?php echo $this->lang->line('medicine'); ?></label>
                                                <?php } ?> 
                                                <select class="form-control select2" style="width: 100%"  name="medicine[]" id="editsearch-query<?php echo $i ?>">
                                                    <option value="l"><?php echo $this->lang->line('select') ?></option>
                                                </select>
                                                <script type="text/javascript">
                                                    geteditMedicineName('<?php echo $i ?>', '<?php echo $value['medicine'] ?>', '<?php echo $value['dosage'] ?>','<?php echo $value['instruction'] ?>');
                                                    
                                                </script>
                                                <input type="hidden" value="<?php echo $value['id'] ?>" name="prescription_id[]" class="form-control" id="report_type" />

                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <?php if ($i == 0) { ?>
                                                
                                                    <label><?php echo $this->lang->line('dosage'); ?></label> 
                                        <?php } ?>
                                                <select   class="form-control select2" style="width: 100%"  name="dosage[]" id="editsearch-dosage<?php echo $i ?>">
                                                    <option value="l"><?php echo $this->lang->line('select') ?></option>
                                                </select>
                                      
                                            </div> 
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                    <?php if ($i == 0) { ?>
                                                    <label><?php echo $this->lang->line('instruction'); ?></label>
                                                    <?php }
                                                    ?> 
                                                <!-- <textarea name="instruction[]" class="form-control" style="height: 28px;" id="instruction[]"><?php //echo $value['instruction'] ?></textarea> -->
                                                <select class="form-control select2" style="width: 100%"  name="instruction[]" id="editsearch-instruction<?php echo $i ?>">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    
                                                </select>

                                            </div> 
                                        </div>
                                    </td>

    <?php if (($i == 0) || (sizeof($prescription_list) == 1)) { ?>
                                        <td><button type="button" onclick="edit_more()" style="color: #2196f3; margin-top: 9px;" class="modaltableclosebtn"><i class="fa fa-plus"></i></button>
                                        </td>
                                    <?php } else {
                                        
                                        ?>
                                       <td><button type='button' onclick="delete_row('<?php echo $i ?>')" class='modaltableclosebtn' style='margin-top: -17px;
    display: block;'><i class='fa fa-remove'></i></button></td>     
                                       
    <?php } ?>
                                </tr>
                             
    <?php
    $i++;
} }else{ ?>

    <tr id="row0">
                                    <td>           
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>
                                                    <?php echo $this->lang->line('medicine') . " " . $this->lang->line("category"); ?></label> <small class="req"> *</small>
                                                <select class="form-control select2" style="width: 100%" name='medicine_cat[]' onchange="geteditMedicineName(0)"  id="editmedicine_cat0">
                                                    <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                    </option>
                                                    <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                        ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                                        </option>   
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>                     
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('medicine'); ?></label> 
                                                <select class="form-control select2" style="width: 100%"  name="medicine[]" id="editsearch-query0">
                                                    <option value="l"><?php echo $this->lang->line('select') ?></option>
                                                </select>
                                                <div id="suggesstion-box0"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('dosage'); ?></label> 

                                                <select class="form-control select2" style="width: 100%"  name="dosage[]" id="editsearch-dosage0">
                                                    <option value="l"><?php echo $this->lang->line('select') ?></option>
                                                </select>
                                            </div> 
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('instruction'); ?></label> 
                                                <!-- <textarea name="instruction[]" style="height: 28px;" class="form-control" ></textarea> -->
                                                <select class="form-control select2" style="width: 100%"  name="instruction[]" id="search-instruction0">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                </select>
                                            </div> 
                                        </div>
                                    </td>
                                    <td><button type="button" onclick="edit_more()" style="color: #2196f3" class="modaltableclosebtn"><i class="fa fa-plus"></i></button></td>
                                </tr>
                            

<?php }
?>
                        </table>


           
                            <hr/>

                            <!-- <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php //echo $this->lang->line('footer_note'); ?></label> 
                                    <textarea name="footer_note" class="form-control editor" id="compose-textareaold" style="height:50px"><?php echo $result["footer_note"] ?></textarea>
                                </div> 
                            </div>   -->

                        </div>
                    </div> 
                </div>
                <div class="col-sm-12">
                        <div class="form-group">
                            <label for="comment">Instruction:</label>
                            <textarea class="form-control" name="note_instruction" rows="3" id="note_instruction"><?php echo $prescription_medical['prescription_note']?></textarea>
                        </div>
                    </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                     <div class="ptt10">
                        <label><?php echo $this->lang->line('notification')." ".$this->lang->line('to'); ?></label>
                             <?php
                                foreach ($roles as $role_key => $role_value) {
                                            $userdata = $this->customlib->getUserData();
                                            $role_id = $userdata["role_id"];
                                            ?>
                                                <div class="checkbox">
                                                    <label><input type="checkbox" name="visible[]" value="<?php echo $role_value['id']; ?>" <?php
                                                        if ($role_value["id"] == $role_id) {
                                                            echo "checked onclick='return false;'";
                                                        }
                                                        ?>  <?php echo set_checkbox('visible[]', $role_value['id'], false) ?> /> <b><?php echo $role_value['name']; ?></b> </label>
                                                </div>
                                                <?php
                                            }
                                            ?>

                     </div>
                </div>
                <?php 
                $nextVisitDate='';
                if(!empty($result['next_visit'])){
                    $nextVisitDate=date('d-m-Y',strtotime($result['next_visit']));
                }
                
                ?>
                <div class="col-sm-4 col-xs-12">
                                            <div class="form-group">
                                                <label>Next Visit</label>
                                                <input type="text" id="next_visit_prescription" class="form-control next_visit" value="<?php echo $nextVisitDate;?>" name="next_visit_prescription" readonly="" autocomplete="off">
                                                <span class="text-danger"></span>
                                            </div> 
                                    </div>
                </div>  
                </div> <!--./modal-body--> 
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>

                </div>
            </div>


            </form>
<script type="text/javascript">
    $(function () {
        $("#compose-textarea,#compose-textareaold").wysihtml5({
            toolbar: {
                "image": false,
            }
        });
        $('.select2').select2();
    });

    function edit_more() {
        var table = document.getElementById("edittableID");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var div = "<div id=row1><div class=col-sm-3><select class='form-control select2' onchange='geteditMedicineName(" + id + ")' name='medicine_cat[]'  id='editmedicine_cat" + id + "'><option value='<?php echo set_value('medicine_category_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineCategory as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"] ?></option><?php } ?></select></div><div class=col-sm-3><div class=form-group><select  class='form-control select2'  name='medicine[]' id='editsearch-query" + id + "'><option value='l'><?php echo $this->lang->line('select') ?></option></select></div><div id='editsuggesstion-box" + id + "'></div></div><div class=col-sm-3><div class=form-group><select class='form-control select2' name='dosage[]' id='editsearch-dosage" + id + "' ><option value='l'><?php echo $this->lang->line('select') ?></option><?php foreach ($dosage as $dkey => $dosagevalue) { ?><option value='<?php echo $dosagevalue["dosage"]; ?>'><?php echo $dosagevalue["dosage"] ?></option><?php } ?></select><input type=hidden class=form-control value='0' name='prescription_id[]' /></div></div><div class=col-sm-3><div class=form-group><select class='form-control select2' name='instruction[]' id='editsearch-instruction" + id + "'><option value='l'><?php echo $this->lang->line('select') ?></option></select></div></div></div>";


        var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'><td>" + div + "</td><td><div class=form-group><button type='button' onclick='delete_row(" + id + ")' class='modaltableclosebtn'><i class='fa fa-remove'></i></button></div></td></tr>";
        $(".select2").select2();
    }

    $(document).ready(function (e) {
        $("#update_prescription").on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update_prescription',
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
                },
                error: function () {
                    //alert("Fail")
                }
            });
        }));
    });
    $(document).ready(function () {
        var date_format = 'dd-mm-yyyy';
            $('.next_visit').datepicker({

                    format: date_format,
                    autoclose: true
                });
            });
</script>                        