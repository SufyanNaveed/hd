<div class="content-wrapper">  
    <section class="content">
        <div class="row">
            <div class="col-md-12">              
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('supplier') . " " . $this->lang->line('type') . " " . $this->lang->line('list'); ?></h3>
                        <div class="box-tools pull-right">
                            <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm supplier">
                                <i class="fa fa-plus"></i>  
                                <?php echo $this->lang->line('add') . " " . $this->lang->line('supplier') . " " . $this->lang->line('type'); ?>
                            </a> 
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <table class="custom-table table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('supplier') . " " . $this->lang->line('type') . " " . $this->lang->line('name'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($supplierTypes as $type) { ?>
                                        <tr>
                                            <td><?php echo $type->name; ?></td>
                                            <td class="text-right">
                                                <a data-target="#editmyModal" onclick="getSupplier(<?php echo $type->id ?>)" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a class="btn btn-default btn-xs" data-toggle="tooltip" onclick="delete_recordById('<?php echo base_url(); ?>hospital/SupplierType/deleteSupplierType/<?php echo $type->id ?>', '<?php echo $this->lang->line('delete_message'); ?>')";>
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

<!-- Add Supplier Type Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"> <?php echo $this->lang->line('add') . " " . $this->lang->line('supplier') . " " . $this->lang->line('type'); ?></h4> 
            </div>
            <form id="formAddSupplier" action="<?php echo site_url('hospital/SupplierType/addSupplierType') ?>" method="post">
                <div class="modal-body pt0 pb0">  
                    <div class="form-group">
                        <label><?php echo $this->lang->line('supplier') . " " . $this->lang->line('type') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                        <input name="name" type="text" class="form-control" required />
                        <span class="text-danger"></span>
                    </div>          
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Type Modal -->
<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"> <?php echo $this->lang->line('edit') . " " . $this->lang->line('supplier') . " " . $this->lang->line('type'); ?></h4> 
            </div>
            <form id="editFormSupplier" action="<?php echo site_url('hospital/SupplierType/addSupplierType') ?>" method="post">
                <div class="modal-body pt0 pb0">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('supplier') . " " . $this->lang->line('type') ?></label><small class="req"> *</small>
                        <input id="supplier_name" name="name" type="text" class="form-control" required />
                        <input type="hidden" id="supplier_id" name="id">
                    </div>                 
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#formAddSupplier').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                success: function (data) {
                    console.log('data.error',data.error)
                    if (data.status == "fail") {
                        errorMsg(data.error.name);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {
                    errorMsg("Error in processing request.");
                }
            });
        });

        $('#editFormSupplier').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.status == "fail") {
                        errorMsg(data.message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {
                    errorMsg("Error in processing request.");
                }
            });
        });
    });

    function delete_recordById(url, Msg) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_conform') . "'"; ?>)) {
            $.ajax({
                url: url,
                success: function () {
                    successMsg(Msg);
                    window.location.reload(true);
                }
            });
        }
    }

    function getSupplier(id) {
        $('#editmyModal').modal('show');
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>hospital/SupplierType/getSupplierType/' + id,
            success: function (result) {
                $('#supplier_id').val(result.id);
                $('#supplier_name').val(result.name);
            }
        });
    }

    $(".supplier").click(function () {
        $('#formAddSupplier').trigger("reset");
    });
</script>
