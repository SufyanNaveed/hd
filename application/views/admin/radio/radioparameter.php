

<div class="content-wrapper" style="min-height: 946px;">  

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!--  <?php //if (($this->rbac->hasPrivilege('department', 'can_add')) || ($this->rbac->hasPrivilege('department', 'can_edit'))) {
?>      -->
            <div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                          <?php if ($this->rbac->hasPrivilege('radiology category', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/lab/addLab" ><?php echo $this->lang->line('radiology') . " " . $this->lang->line('category'); ?></a></li>
                    <?php } if ($this->rbac->hasPrivilege('radiology_unit', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/lab/unit" class=""><?php echo $this->lang->line('unit'); ?></a></li>
                    <?php } if ($this->rbac->hasPrivilege('radiology_parameter', 'can_view')) { ?>
                        <li><a class="active" href="<?php echo base_url(); ?>admin/lab/radioparameter" class=""><?php echo $this->lang->line('radiology') . " " . $this->lang->line('parameter'); ?></a></li>
                    <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">              
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('radiology') . " " . $this->lang->line('parameter') . " " . $this->lang->line('list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('radiology_parameter', 'can_add')) { ?>
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm radiology"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('radiology') . " " . $this->lang->line('parameter'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('radiology') . " " . $this->lang->line('parameter') . " " . $this->lang->line('list'); ?></div>
                            <table class="custom-table table table-striped table-bordered table-hover example" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('lab') . " " . $this->lang->line('name'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($parameterName as $parameter) {
                                        ?>
                                        <tr>
                                            <td><?php echo $parameter['parameter_name']; ?></td>
                                            <td class="text-right">
                                                <?php if ($this->rbac->hasPrivilege('radiology_parameter', 'can_edit')) { ?><a data-target="#editmyModal" onclick="get(<?php echo $parameter['id'] ?>)"  class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if ($this->rbac->hasPrivilege('radiology_parameter', 'can_delete')) { ?>
                                                    <a  class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordById('<?php echo base_url(); ?>admin/lab/delete_parameter/<?php echo $parameter['id'] ?>', '<?php echo $this->lang->line('delete_message'); ?>')";>
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="">
                        <div class="mailbox-controls">
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </section>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"> <?php echo $this->lang->line('add'); ?>  <?php echo $this->lang->line('radiology') . " " . $this->lang->line('parameter'); ?></h4> 
            </div>

            <form id="formadd" action="<?php echo site_url('admin/lab/addparameter') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('parameter') . " " . $this->lang->line('name'); //$this->lang->line('name');     ?></label><small class="req"> *</small>
                            <input autofocus="" id="parameter_name"  name="parameter_name" placeholder="" type="text" class="form-control"   />
                            <span class="text-danger"><?php echo form_error('lab_name'); ?></span>
                        </div>  
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('reference') . " " . $this->lang->line('range'); ?></label>
                            <input autofocus="" name="reference_range" placeholder="" type="text" class="form-control"  />
                            <span class="text-danger"><?php echo form_error('reference_range'); ?></span>
                        </div>      
                        <div class="form-group">
                                <label><?php echo $this->lang->line('unit'); ?></label>
                                
                                    <select name="unit" onchange="" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($unitname as $value) {
                                                ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>
                                            <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('unit'); ?></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                            <input autofocus="" name="description" placeholder="" type="text" class="form-control"  />
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>  
                    </div>
                </div><!--./modal-->     
                <div class="box-footer">
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>


        </div><!--./row--> 
    </div>
</div>


<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit') . " " . $this->lang->line('parameter'); ?></h4> 
            </div>

            <form id="editformadd" action="<?php echo site_url('admin/lab/addparameter') ?>" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('parameter') . " " . $this->lang->line('name'); //$this->lang->line('name');    ?></label><small class="req"> *</small>
                            <input autofocus="" id="edit_parameter_name"  name="parameter_name" placeholder="" type="text" class="form-control"  value="" />
                            <span class="text-danger"><?php echo form_error('parameter_name'); ?></span>
                            <input type="hidden" id="edit_id" name="parameter_id">

                        </div>   
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('reference') . " " . $this->lang->line('range'); //$this->lang->line('name');    ?></label>
                            <input autofocus="" id="edit_reference_range"  name="reference_range" placeholder="" type="text" class="form-control"  value="" />
                            <span class="text-danger"><?php echo form_error('reference_range'); ?></span>
                        </div> 
                        <div class="form-group">
                                    <label><?php echo $this->lang->line('unit'); ?></label>
                                  
                                    <select name="unit" id="unit"  onchange="" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($unitname as $value) {
                                                ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>
                                            <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('unit'); ?></span>
                        </div>   
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description');   ?></label>
                            <input autofocus="" id="edit_description"  name="description" placeholder="" type="text" class="form-control"  value="" />
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                           

                        </div>                  

                    </div>
                </div><!--./modal-->      
                <div class="box-footer">
                    <button type="submit" id="editformaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>


        </div><!--./row--> 
    </div>
</div>
<script>


    $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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

                }
            });


        }));

    });


    function get(id) {
        $('#editmyModal').modal('show');
        $.ajax({

            dataType: 'json',

            url: '<?php echo base_url(); ?>admin/lab/get_parameterdata/' + id,

            success: function (result) {

                $('#edit_id').val(result.id);
                $('#edit_parameter_name').val(result.parameter_name);
                $('#edit_reference_range').val(result.reference_range);
                $('#edit_description').val(result.description);
                $('#unit').val(result.unit);

            }

        });

    }


    $(document).ready(function (e) {

        $('#editformadd').on('submit', (function (e) {
            $("#editformaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                    $("#editformaddbtn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });

$(".radiology").click(function(){
	$('#formadd').trigger("reset");
});
</script>