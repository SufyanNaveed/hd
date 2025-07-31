<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat();?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
         

                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"> <?php echo $this->lang->line('item_list'); ?></h3>
                            <div class="box-tools pull-right">
                               
                                    <a href="" data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm additem" ><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_item'); ?></a>

                            </div><!-- /.box-tools -->
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive mailbox-messages">
                                <div class="download_label"><?php echo $this->lang->line('item_list'); ?></div>
                                <table class="custom-table table table table table-hover table-striped table-bordered example">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('item'); ?></th>
                                            <th><?php echo $this->lang->line('category'); ?>
                                            </th>
                                            <th><?php echo $this->lang->line('unit'); ?>
                                            </th>
                                            <th><?php echo $this->lang->line('available_quantity'); ?>
                                            </th>
                                            <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
if (empty($itemlist)) {
        ?>

                                            <?php
} else {
        foreach ($itemlist as $items) {
            ?>
                                                <tr>
                                                    <td class="mailbox-name">
                                                        <a href="#" data-toggle="popover" class="detail_popover"><?php echo $items['name'] ?></a>

                                                        <div class="fee_detail_popover" style="display: none">
                                                            <?php
if ($items['description'] == "") {
                ?>
                                                                <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                                <?php
} else {
                ?>
                                                                <p class="text text-info"><?php echo $items['description']; ?></p>
                                                                <?php
}
            ?>
                                                        </div>
                                                    </td>


                                                    <td class="mailbox-name">
                                                        <?php echo $items['item_category']; ?>

                                                    </td>
                                                    <td class="mailbox-name">
                                                        <?php echo $items['unit']; ?>

                                                    </td>
                                                    <td class="mailbox-name">
                                                        <?php
echo $items['added_stock'] - $items['issued'];

            ?>

                                                    </td>
                                                    <td class="mailbox-date pull-right">
                                                        <a  class="btn btn-default btn-xs" data-target="#editmyModal"  data-toggle="tooltip" onclick="get_data(<?php echo $items['id']; ?>);" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a  class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordById('<?php echo base_url(); ?>hospital/itemDelete/<?php echo $items['id'] ?>', '<?php echo $this->lang->line('delete_message') ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                        
                                                    </td>
                                                </tr>
                                                <?php
}
    }
    ?>

                                    </tbody>
                                </table><!-- /.table -->



                            </div><!-- /.mail-box-messages -->
                        </div><!-- /.box-body -->
                    </div>
                </div><!--/.col (left) -->
                <!-- right column -->

        </div>
        <div class="row">
            <!-- left column -->

            <!-- right column -->
            <div class="col-md-12">

            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add_item') ?></h4>
            </div>

            <div class="modal-body pt0 pb0">
                <form id="form1" action="<?php echo base_url() ?>hospital/itemAdd" name="itemstockform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="row ptt10">
                        <?php echo $this->customlib->getCSRF(); ?>

                        <!-- Item Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name"><?php echo $this->lang->line('item'); ?></label><small class="req"> *</small>
                                <input autofocus="" id="name" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                            </div>
                        </div>

                        <!-- Item Category -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="item_category_id"><?php echo $this->lang->line('item_category'); ?></label><small class="req"> *</small>
                                <select id="item_category_id" name="item_category_id" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($itemcatlist as $item_category): ?>
                                        <option value="<?php echo $item_category['id'] ?>" <?php echo set_value('item_category_id') == $item_category['id'] ? "selected" : ""; ?>>
                                            <?php echo $item_category['item_category'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                            </div>
                        </div>

                        <!-- Unit -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit"><?php echo $this->lang->line('unit'); ?></label><small class="req"> *</small>
                                <input id="unit" name="unit" placeholder="" type="text" class="form-control" value="<?php echo set_value('unit'); ?>" />
                                <span class="text-danger"><?php echo form_error('unit'); ?></span>
                            </div>
                        </div>

        
                       

                        <!-- Opening Quantity -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opening_qty">Opening Qty</label>
                                <input id="opening_qty" name="opening_qty" type="number" class="form-control" value="<?php echo set_value('opening_qty'); ?>" />
                                <span class="text-danger"><?php echo form_error('opening_qty'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expiry_date_min">Min Qty</label>
                                <input id="min_qty" name="min_qty" type="number" class="form-control" value="<?php echo set_value('min_qty'); ?>" />
                                <span class="text-danger"><?php echo form_error('min_qty'); ?></span>
                            </div>
                        </div>
                      

                        <!-- Reorder Quantity -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reorder_qty">ReOrder Qty</label>
                                <input id="reorder_qty" name="reorder_qty" type="number" class="form-control" value="<?php echo set_value('reorder_qty'); ?>" />
                                <span class="text-danger"><?php echo form_error('reorder_qty'); ?></span>
                            </div>
                        </div>

                        <!-- Expiry Is Optional -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expiry_is_optional">Is Expire</label>
                                <select id="expiry_is_optional" name="expiry_is_optional" class="form-control">
                                    <option value="y" <?php echo set_value('expiry_is_optional') == 'y' ? "selected" : ""; ?>>Yes</option>
                                    <option value="n" <?php echo set_value('expiry_is_optional') == 'n' ? "selected" : ""; ?>>No</option>
                                </select>
                                <span class="text-danger"><?php echo form_error('expiry_is_optional'); ?></span>
                            </div>
                        </div>
  <!-- Expiry Date Minimum -->
  <div class="col-md-6 min_exp">
                            <div class="form-group">
                                <label for="expiry_date_min">Expiry Date</label>
                                <input id="expiry_date_min" name="expiry_date_min" type="date" class="form-control" value="<?php echo set_value('expiry_date_min'); ?>" />
                                <span class="text-danger"><?php echo form_error('expiry_date_min'); ?></span>
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                <span class="text-danger"><?php echo form_error('description'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer clear">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit_item'); ?></h4>
            </div>

            <div class="modal-body pt0 pb0">
    <form id="eform1" action="<?php echo base_url() ?>hospital/itemUpdate" method="post" accept-charset="utf-8" enctype="multipart/form-data">
        <?php echo $this->customlib->getCSRF(); ?>
        <input type="hidden" name="id" id="e_id" />

        <!-- Row 1 -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ename">Name</label>
                    <input type="text" class="form-control" id="ename" name="name" required />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="eunit">Unit</label>
                    <input type="text" class="form-control" id="eunit" name="unit" required />
                </div>
            </div>
        </div>

        <!-- Row 2 -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="eitem_category_id">Item Category</label>
                    <select id="eitem_category_id" name="item_category_id" class="form-control" required>
                        <option value="">Select</option>
                        <?php foreach ($itemcatlist as $item_category): ?>
                            <option value="<?php echo $item_category['id']; ?>"><?php echo $item_category['item_category']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="opening_qty">Opening Quantity</label>
                    <input type="number" class="form-control opening_qty" id="opening_qty" name="opening_qty" min="0" required />
                </div>
            </div>
        </div>

        <!-- Row 3 -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="min_qty">Minimum Quantity</label>
                    <input type="number" class="form-control min_qty" id="min_qty" name="min_qty" min="0" required />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="reorder_qty">Reorder Quantity</label>
                    <input type="number" class="form-control reorder_qty" id="reorder_qty" name="reorder_qty" min="0" required />
                </div>
            </div>
        </div>

        <!-- Row 4 -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expiry_is_optional">Expiry Optional</label>
                    <select id="expiry_is_optional" name="expiry_is_optional" class="form-control expiry_is_optional" required>
                        <option value="y">Yes</option>
                        <option value="n">No</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expiry_date_min">Expiry Date</label>
                    <input type="date" class="form-control expiry_date_min" id="expiry_date_min" name="expiry_date_min" />
                </div>
            </div>
        </div>

        <!-- Row 5 -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="edescription">Description</label>
                    <textarea class="form-control" id="edescription" name="description"></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-info pull-right">Save</button>
            </div>
        </div>
    </form>
</div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        $('#date').datepicker({
            //  format: "dd-mm-yyyy",
            format: date_format,
            autoclose: true
        });

        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });

    });
</script>
<script>
    $(document).on('change', '#expiry_is_optional', function () {
        var expiry_is_optional = $(this).val();
        if (expiry_is_optional == 'y') {
            $('.min_exp').show();
        } else {
            $('.min_exp').hide();
        }
    });
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });

    $(document).ready(function (e) {

        $('#form1').on('submit', (function (e) {

            e.preventDefault();
            console.log('ues');
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

                },
                error: function () {
                    //  alert("Fail")
                }
            });


        }));

    });

    function get_data(id) {

        //alert(id);
        $.ajax({

            url: "<?php echo base_url() ?>hospital/getItem/" + id,
            type: "POST",
            dataType: 'json',

            success: function (res) {
                console.log(res);
                $('#ename').val(res.name);
                $('#eunit').val(res.unit);
                $('#epurchase_price').val(res.purchase_price);
                $('#e_id').val(res.id);
                $('#eitem_category_id').val(res.item_category_id);
                $('#edescription').val(res.description);
                $('.opening_qty').val(res.opening_qty);
                $('.min_qty').val(res.min_qty);
                $('.reorder_qty').val(res.reorder_qty);
                $('.expiry_is_optional').val(res.expiry_is_optional);
                $('.expiry_date_min').val(res.expiry_date_min);
                $('#editmyModal').modal('show');
            }

        });



    }


    $(document).ready(function (e) {

        $('#eform1').on('submit', (function (e) {

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

                },
                error: function () {
                    //  alert("Fail")
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
$(".additem").click(function(){
    $('#form1').trigger("reset");
});
</script>

