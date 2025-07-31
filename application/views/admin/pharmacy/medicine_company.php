<div class="content-wrapper">  
    <section class="content">
        <div class="row">
            <div class="col-md-12">              
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('company') . " " . $this->lang->line('list'); ?></h3>
                        <div class="box-tools pull-right">
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm medicine"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add'); ?> <?php echo $this->lang->line('medicine') . " " . $this->lang->line('company'); ?></a> 
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('company') . " " . $this->lang->line('list'); ?></div>
                            <table class="custom-table table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('company') . " " . $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($companies as $company) {
                                        ?>
                                        <tr>
                                            <td><?php echo $company['name']; ?></td>
                                            <td><?php echo $company['address']; ?></td>
                                            <td class="text-right">
                                                    <a data-target="#editmyModal" onclick="get(<?php echo $company['id'] ?>)" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordById('<?php echo base_url(); ?>admin/medicinecompany/delete/<?php echo $company['id'] ?>', '<?php echo $this->lang->line('delete_message'); ?>')">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </section>
</div>

<!-- Add Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('medicine') . " " . $this->lang->line('company'); ?></h4> 
            </div>
            <form id="formadd" action="<?php echo site_url('admin/medicinecompany/add') ?>" method="post">
                <div class="modal-body pt0 pb0">  
                    <div class="ptt10">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('company') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input name="name" type="text" class="form-control" placeholder="">
                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('address'); ?></label>
                            <textarea name="address" class="form-control" placeholder=""></textarea>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" id="formaddbtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit') . " " . $this->lang->line('medicine') . " " . $this->lang->line('company'); ?></h4> 
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/medicinecompany/add') ?>" method="post">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('company') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input id="name" name="name" type="text" class="form-control" placeholder="">
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('address'); ?></label>
                            <textarea id="address" name="address" class="form-control" placeholder=""></textarea>
                        </div>
                        <input type="hidden" id="id" name="company_id">
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" id="editformaddbtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Add Form Submit
    $(document).ready(function () {
        $('#formadd').on('submit', function (e) {
            e.preventDefault();
            $("#formaddbtn").button('loading');
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
                        errorMsg(data.message);
                    } else {
                        successMsg(data.message);
                        window.location.reload();
                    }
                    $("#formaddbtn").button('reset');
                }
            });
        });
    });

    // Fetch and Populate Edit Form
    function get(id) {
        $('#editmyModal').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/medicinecompany/get_data/' + id,
            dataType: 'json',
            success: function (data) {
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#address').val(data.address);
            }
        });
    }

    // Reset Add Form on Open
    $(".medicine").click(function () {
        $('#formadd').trigger("reset");
    });
</script>
