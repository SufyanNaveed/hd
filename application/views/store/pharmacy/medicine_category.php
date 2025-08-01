<div class="content-wrapper">  

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!--  <?php //if (($this->rbac->hasPrivilege('department', 'can_add')) || ($this->rbac->hasPrivilege('department', 'can_edit'))) {
?>      -->
            <?php $this->load->view('store/pharmacy/pharmacyMasters') ?>

            <div class="col-md-10">              
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category') . " " . $this->lang->line('list'); ?></h3>
                        <div class="box-tools pull-right">
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm medicine"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add'); ?> <?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></a> 
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category') . " " . $this->lang->line('list'); ?></div>
                            <table class="custom-table table table table-striped table-bordered table-hover example" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('category') . " " . $this->lang->line('name'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($medicineCategory as $category) {
                                        ?>
                                        <tr>
                                            <td><?php echo $category['medicine_category']; ?></td>
                                            <td class="text-right">
                                                    <a data-target="#editmyModal" onclick="get(<?php echo $category['id'] ?>)"  class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                  
                                                    <a  class="btn btn-default btn-xs"  data-toggle="tooltip" title="" onclick="delete_recordById('<?php echo base_url(); ?>hospital/medicinecategory/delete/<?php echo $category['id'] ?>', '<?php echo $this->lang->line('delete_message'); ?>')";>
                                                        <i class="fa fa-trash"></i>
                                                    </a>
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
                <h4 class="box-title"> <?php echo $this->lang->line('add') . " " . $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></h4> 
            </div>

            <form id="formadd" action="<?php echo site_url('hospital/medicinecategory/add') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                <div class="modal-body pt0 pb0">  
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('category') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input autofocus="" name="medicine_category" placeholder="" type="text" class="form-control"  value="<?php
                            if (isset($result)) {
                                echo $result["medicine_category"];
                            }
                            ?>" />
                            <span class="text-danger"><?php echo form_error('medicine_category'); ?></span>

                        </div>          

                    </div>
                </div><!--./modal-body-->        
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
                <h4 class="box-title"> <?php echo $this->lang->line('edit') . " " . $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></h4> 
            </div>


            <form id="editformadd" action="<?php echo site_url('hospital/medicinecategory/add') ?>" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('category') . " " . $this->lang->line('name') ?></label><small class="req"> *</small>
                            <input autofocus="" id="medicine_category" name="medicine_category" placeholder="" type="text" class="form-control"  value="<?php
                            if (isset($result)) {
                                echo $result["medicine_category"];
                            }
                            ?>" />
                            <span class="text-danger"><?php echo form_error('medicine_category'); ?></span>
                            <input type="hidden" id="id" name="medicinecategoryid">
                        </div>                 
                    </div>
                </div><!--./madal-body-->     
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

    function get(id) {
        $('#editmyModal').modal('show');
        $.ajax({

            dataType: 'json',

            url: '<?php echo base_url(); ?>hospital/medicinecategory/get_data/' + id,

            success: function (result) {

                $('#id').val(result.id);
                $('#medicine_category').val(result.medicine_category);

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


$(".medicine").click(function(){
	$('#formadd').trigger("reset");
});
</script>

