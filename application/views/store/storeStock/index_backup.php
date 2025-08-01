
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('medicines'); ?></h3>
                        <!-- <small id="total_qty" class="box-title titlefix" style="font-size: 13px;"></small>
                        <small id="total_purchase" class="box-title titlefix" style="font-size: 13px;"></small>
                        <small id="total_sale" class="box-title titlefix" style="font-size: 13px;"></small> -->

                        <div class="box-tools pull-right">
                        <!-- <a data-toggle="modal" href="<?php echo base_url(); ?>hospital/store/medicineRequest" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> Request Medicine
                        </a> -->
                            <!-- <a data-toggle="modal" href="<?php echo base_url(); ?>hospital/pharmacy/import" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> <?php echo $this->lang->line('import_medicine'); ?>
                            </a> -->
                            

                            <a href="<?php echo base_url() ?>hospital/store/openingStockList" class="btn btn-primary btn-sm addmedicine"><i class="fa fa-plus"></i>Add Opening Stock</a> 
                            

                          
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('medicines') . " " .$this->lang->line('stock'); ?></div>
                      <div class="table-responsive-mobile">   
                        <table class="custom-table table table-striped table-bordered table-hover test_ajax " cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('medicine') . " " . $this->lang->line('company'); ?></th>
                                    <th><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></th> 
                                    <th>Available Quantity</th> 
                                </tr>
                            </thead>
                            <tbody>
                             
                            </tbody>
                        </table>
                      </div>  
                    </div>
                </div>                                                    
            </div>                                                                                                                                          
        </div>  
    </section>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('medicine') . " " . $this->lang->line('details'); ?></h4> 
            </div>
            <form id="formadd" accept-charset="utf-8" method="post" class="ptt10" enctype="multipart/form-data"> 
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?></label>
                                        <small class="req"> *</small> 
                                        <input id="medicine_name" name="medicine_name" placeholder="" type="text" class="form-control"/>
                                        <span class="text-danger"><?php echo form_error('medicine_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></label>
                                        <small class="req"> *</small> 
                                        <div>
                                            <select class="form-control select2" style="width:100%" name='medicine_category_id' >
                                                <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                </option>
                                                <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                    ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                                    </option>   
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                    </div>
                                </div>  
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('medicine') . " " . $this->lang->line('company'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="medicine_company" value="" class="form-control">
                                        <span class="text-danger"><?php echo form_error('medicine_company'); ?></span>
                                    </div>
                                </div> 
                                
                               
                                <!-- <div class="col-sm-3">
                                     <div class="form-group">
                                         <label><?php echo $this->lang->line('supplier'); ?></label>
                                         <input type="text" name="supplier" class="form-control">
                                     </div>
                                 </div>-->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('unit'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="unit" class="form-control">
                                        <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('min_level'); ?></label>
                                        <input type="text" name="min_level" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>OpenStock</label>
                                        <input type="text" name="open_stock" class="form-control">
                                    </div>
                                </div>
  


                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('re_order_level'); ?></label>
                                        <input type="text" name="reorder_level" class="form-control">
                                    </div>
                                </div>
                               
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('unit') . "/" . $this->lang->line('packing'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="unit_packing" class="form-control">
                                        <span class="text-danger"><?php echo form_error('unit_packing'); ?>
                                    </div>
                                </div>
                               
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('note'); ?></label>
                                        <textarea name="note" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('medicine') . " " . $this->lang->line('photo') . " ( " . $this->lang->line('jpg_jpeg_png') . " )"; ?></label>
                                        <input type="file" name="file" id="file" class="form-control filestyle" />
                                    </div>
                                </div>
                               
                            </div><!--./row-->  
                            <div class="row">
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
                                <div class="col-sm-4 min_exp">

                                <div class="form-group">
                                <label for="expiry_date_min">Expiry Date</label>
                                <input id="expiry_date_min" name="expiry_date_min" type="date" class="form-control" value="<?php echo set_value('expiry_date_min'); ?>" />
                                <span class="text-danger"><?php echo form_error('expiry_date_min'); ?></span>
                                </div>
                            </div>
                            </div> 

                        </div><!--./col-md-12-->       
                    </div><!--./row--> 
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>

                    </div>
                </div>
            </form> 
        </div>
    </div>    
</div>


<div class="modal fade" id="myModalImport" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('medicine'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="formimp" accept-charset="utf-8"  method="post" class="ptt10" enctype="multipart/form-data" >
                            <div class="row">

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></label>
                                        <small class="req"> *</small> 
                                        <div>
                                            <select class="form-control select2" style="width:100%" name='medicine_category_id' >
                                                <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                </option>
                                                <?php foreach ($medicineCategory as $dkey => $dvalue) {
                                                    ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                                    </option>   
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('medicine_category_id'); ?></span>
                                    </div>
                                </div>  

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('medicine'); ?>    CSV File Upload</label>
                                        <input type="file" name="medicine_image" class="form-control filestyle" />
                                    </div>
                                </div>
                            </div><!--./row-->   

                    </div><!--./col-md-12-->       
                </div><!--./row--> 
            </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" id="formimpbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right">Import <?php echo $this->lang->line('medicine'); ?></button>
                    </form> 
                </div>
            </div>

        </div>
    </div>    
</div>
<!-- dd -->
<div class="modal fade" id="myModaledit" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit') . " " . $this->lang->line('medicine') . " " . $this->lang->line('details'); ?></h4>
            </div>
            <form id="formedit" accept-charset="utf-8" method="post" class="ptt10" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                            <div class="row">
                                <input type="hidden" name="id" class="form-id">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?></label>
                                        <small class="req"> *</small>
                                        <input name="medicine_name" type="text" class="form-control medicine-name">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></label>
                                        <small class="req"> *</small>
                                        <select class="form-control select2 medicine-category" name="medicine_category_id" style="width:100%;">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($medicineCategory as $dkey => $dvalue) { ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('medicine') . " " . $this->lang->line('company'); ?></label>
                                        <small class="req"> *</small>
                                        <input name="medicine_company" type="text" class="form-control medicine-company">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('unit'); ?></label>
                                        <small class="req"> *</small>
                                        <input name="unit" type="text" class="form-control medicine-unit">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('min_level'); ?></label>
                                        <input name="min_level" type="text" class="form-control medicine-min-level">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Open Stock</label>
                                        <input name="open_stock" type="text" class="form-control open-stock">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('re_order_level'); ?></label>
                                        <input name="reorder_level" type="text" class="form-control reorder-level">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('unit') . "/" . $this->lang->line('packing'); ?></label>
                                        <small class="req"> *</small>
                                        <input name="unit_packing" type="text" class="form-control unit-packing">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('note'); ?></label>
                                        <textarea name="note" class="form-control medicine-note"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('medicine') . " " . $this->lang->line('photo'); ?></label>
                                        <input type="file" name="file" class="form-control filestyle medicine-photo">
                                        <input type="hidden" name="pre_medicine_image" class="form-control pre-medicine-image">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expiry_is_optional">Is Expire</label>
                                        <select name="expiry_is_optional" class="form-control expiry-is-optional">
                                            <option value="y"><?php echo $this->lang->line('yes'); ?></option>
                                            <option value="n"><?php echo $this->lang->line('no'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 min_exp">
                                    <div class="form-group">
                                        <label for="expiry_date_min">Expiry Date</label>
                                        <input name="expiry_date_min" type="date" class="form-control expiry-date-min">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" id="formeditbtn" class="btn btn-info formedit-btn"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="Close" data-dismiss="modal">&times;</button>

                <div class="modalicon"> 
                    <div id='edit_delete' class="">
                        <!-- <a href="#"  onclick="holdModal('myModaledit')" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a> -->

                        <!-- <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete') ?>"><i class="fa fa-trash"></i></a> -->
                    </div>
                </div>
                <h4 class="box-title"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('information'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
            <div class="row">
                                <div class="col-lg-6 col-md-2 col-sm-4">
                                <img id="medicine_image" src="#" style="width:100px;height: 100px;" />
                                </div>
                            </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="view" accept-charset="utf-8" method="get" class="ptt10">
                            
                            <div class="col-lg-11 col-md-10 col-sm-8">
                                <div class="table-responsive">
                                    <table class="custom-table table mb0 table-striped table-bordered examples">
                                        <tr>
                                            <th></th>
                                            <td></td>
                                            <th width="15%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('name'); ?></th>
                                            <td width="35%"><span id='medicine_names'></span></td>
                                            <th width="15%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('category'); ?></th>
                                            <td width="35%"><span id="medicine_category_ids"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td></td>
                                            <th width="15%"><?php echo $this->lang->line('medicine') . " " . $this->lang->line('company'); ?></th>
                                            <td width="35%"><span id='medicine_companys'></span></td>
                                            

                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td></td>
                                            
                                            <th width="15%"><?php echo $this->lang->line('unit'); ?></th>
                                            <td width="35%"><span id="units"></span>
                                            </td>

                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td></td>
                                            <th width="15%"><?php echo $this->lang->line('min_level'); ?></th>
                                            <td width="35%"><span id='min_levels'></span></td>
                                            <th width="15%"><?php echo $this->lang->line('re_order_level'); ?></th>
                                            <td width="35%"><span id="reorder_levels"></span>
                                            </td>

                                        </tr>
                                        <tr>                                  <th></th>
                                            <td></td>
                                          
                                            <th width="15%"><?php echo $this->lang->line('unit') . "/" . $this->lang->line('packing'); ?></th>
                                            <td width="35%"><span id="unit_packings"></span>
                                            </td>

                                        </tr>
                                        <tr>
    <th width="15%"><?php echo $this->lang->line('barcode'); ?></th>
    <td colspan="3">
        <canvas id="medicine_barcode"></canvas>
    </td>
                                        </tr>

                                        <tr>
                                            <th></th>
                                            <td></td>

                                           
                                        </tr>
                                    </table>
                                </div>    
                            </div>
                           
                        </form>   
                             
                    </div><!--./col-md-12-->       
                </div><!--./row-->
                 
                <div id="tabledata"></div> 
            </div>
            <div class="box-footer">
                <div class="pull-right paddA10">
                  <!--  <a  onclick="saveEnquiry()" class="btn btn-info pull-right"><?php //echo $this->lang->line('save');     ?></a> -->
                </div>
            </div>
        </div>
    </div>    
</div>
<div class="modal fade" id="addBulkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('stock') ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="formbatch" accept-charset="utf-8"  method="post" class="ptt10" >
                            <input type="hidden" name="pharmacy_id" id="pharm_id" >
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('batch') . " " . $this->lang->line('no'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="batch_no" class="form-control">
                                        <span class="text-danger"><?php echo form_error('batch_no'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('expire') . " " . $this->lang->line('date'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" id="expiry" name="expiry_date" class="form-control">
                                        <span class="text-danger"><?php echo form_error('expiry_date'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('inward') . " " . $this->lang->line('date'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" id="inward_date" name="inward_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('inward_date'); ?></span>
                                    </div>
                                </div> 

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('packing') . " " . $this->lang->line('qty'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="packing_qty" class="form-control">
                                        <span class="text-danger"><?php echo form_error('packing_qty'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('purchase_rate') ; ?></label>

                                        <input type="text" name="purchase_rate_packing" class="form-control">
                                        <span class="text-danger"><?php echo form_error('purchase_rate_packing'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('quantity'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="quantity" class="form-control">
                                        <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('mrp') ; ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="mrp" class="form-control">
                                        <span class="text-danger"><?php echo form_error('mrp'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('sale_price') ; ?></label>
                                        <small class="req"> *</small> 
                                        <input  name="sale_rate" type="text" class="form-control"/>
                                        <span class="text-danger"><?php echo form_error('sale_rate'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('batch') . " " . $this->lang->line('amount'); ?></label>

                                        <input type="text" name="amount" class="form-control">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                </div> 
                            </div><!--./row-->   

                    </div><!--./col-md-12-->       

                </div><!--./row--> 

            </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" id="formbatchbtn" data-loading-text="<?php echo $this->lang->line("processing") ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>

                </div>

            </div>
            </form>
        </div>
    </div>    
</div>

<div class="modal fade" id="addBadStockModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('bad') . " " . $this->lang->line('stock'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="formstock"  accept-charset="utf-8"  method="post" class="ptt10" >
                            <input type="hidden" name="pharmacy_id" id="pharm_id" >
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('batch') . " " . $this->lang->line('no'); ?></label>
                                        <small class="req"> *</small> 
                                        <select name="batch_no" onchange="getExpire(this.value)" id="batch_stock_no" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('batch_no'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('expire') . " " . $this->lang->line('date'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" id="batch_expire"  name="expiry_date" id="stockexpiry_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('expiry_date'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('outward') . " " . $this->lang->line('date'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text"  name="inward_date" value="<?php echo date($this->customlib->getSchoolDateFormat()) ?>" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('inward_date'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('qty'); ?></label>
                                        <small class="req"> *</small> 
                                        <input type="text" name="packing_qty" class="form-control">
                                        <input type="hidden" name="pharmacy_id" id="pharmacy_stock_id" class="form-control">
                                        <input type="hidden" name="available_quantity" id="batch_available_qty" class="form-control">
                                        <input type="hidden" name="medicine_batch_id" id="medicine_batch_id" class="form-control">
                                        <span class="text-danger"><?php echo form_error('packing_qty'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('note'); ?></label>
                                        <textarea  name="note" class="form-control "></textarea>
                                    </div>
                                </div> 

                            </div><!--./row-->   

                    </div><!--./col-md-12-->       

                </div><!--./row--> 

            </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" id="formstockbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                    </form>  
                </div>
            </div>
        </div>
    </div>    
</div>



<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script type="text/javascript">
    
            $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    e.preventDefault();
                    $("#formaddbtn").button('loading');
                    $.ajax({
                        url: '<?php echo base_url(); ?>hospital/pharmacy/add',
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
                $("#formstock").on('submit', (function (e) {
                    e.preventDefault();
                    $("#formstockbtn").button('loading');
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/addBadStock',
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
                            $("#formstockbtn").button('reset');
                        },
                        error: function () {
                            //  alert("Fail")
                        }
                    });
                }));
            });
            $(document).ready(function (e) {
                $("#formedit").on('submit', (function (e) {
                    e.preventDefault();
                    $("#formeditbtn").button('loading');
                    $.ajax({
                        url: '<?php echo base_url(); ?>hospital/pharmacy/update',
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
            $(document).ready(function (e) {

                $('#expiry,#stockexpiry_date').datepicker({
                    format: "M/yyyy",
                    viewMode: "months",
                    minViewMode: "months",
                    autoclose: true
                });
            });
            function getRecord(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/getDetails',
                    type: "POST",
                    data: {pharmacy_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#id").val(data.id);
                        $("#medicines_name").val(data.medicine_name);
                        $("#medicines_category_id").val(data.medicine_category_id);
                        $("#medicine_company").val(data.medicine_company);
                        $("#medicine_composition").val(data.medicine_composition);
                        $("#medicine_group").val(data.medicine_group);
                        $("#unit").val(data.unit);
                        $("#min_level").val(data.min_level);
                        $("#reorder_level").val(data.reorder_level);
                        $("#vat").val(data.vat);
                       // console.log(vat);
                        $("#unit_packing").val(data.unit_packing);
                        //$("#supplier").val(data.supplier);
                        $("#pre_medicine_image").val(data.pre_medicine_image);
                        $("#vat_ac").val(data.vat_ac);
                        $("#edit_note").val(data.note);
                        $("#updateid").val(id);
                        $("#viewModal").modal('hide');
                        $(".select2").select2().select2('val', data.medicine_category_id);
                        //$('select[id="medicines_category_id"] option[value="' + data.medicines_category_id + '"]').attr("selected", "selected");
                        holdModal('myModaledit');
                    },
                });
            }
           
            $(document).ready(function (e) {
                $("#formbatch").on('submit', (function (e) {
                    e.preventDefault();
                    $("#formbatchbtn").button("loading");
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/medicineBatch',
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
                            //  alert("Fail")
                        }
                    });
                }));
            });
            function delete_record(id) {
                if (confirm('Are you sure')) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/delete/' + id,
                        type: "POST",
                        data: {opdid: ''},
                        dataType: 'json',
                        success: function (data) {

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

            function addbadstock(id) {
                $("#pharmacy_stock_id").val(id);
                getbatchnolist(id);
                holdModal('addBadStockModal');
            }


            function getbatchnolist(id, selectid = '') {
                var div_data = "";
                $("#batch_stock_no").html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/pharmacy/getBatchNoList",
                    data: {'medicine': id},
                    dataType: 'json',
                    success: function (res) {
                        console.log(res);
                        $.each(res, function (i, obj)
                        {
                            var sel = "";
                            if (obj.batch_no == selectid) {
                                sel = "selected";
                            }
                            div_data += "<option " + sel + " value='" + obj.batch_no + "'>" + obj.batch_no + "</option>";
                        });
                        $('#batch_stock_no').append(div_data);
                    }
                });
            }

            function getExpire(batch_no) {
                //var batch_no = $("#batch_expire").val();
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/pharmacy/getExpiryDate",
                    data: {'batch_no': batch_no},
                    dataType: 'json',
                    success: function (data) {
                        if (data != null) {
                            $('#batch_expire').val(data.expiry_date);
                            $('#batch_available_qty').val(data.available_quantity);
                            $('#medicine_batch_id').val(data.id);
                        }
                    }
                });
            }
            function viewDetail(id) {
    $.ajax({
        url: '<?php echo base_url(); ?>hospital/pharmacy/getDetails',
        type: "POST",
        data: { pharmacy_id: id },
        dataType: 'json',
        success: function (data) {
            // $.ajax({
            //     url: '<?php echo base_url(); ?>admin/pharmacy/getMedicineBatch',
            //     type: "POST",
            //     data: { pharmacy_id: id },
            //     success: function (data) {
            //         $('#tabledata').html(data);
            //     },
            // });

            // Medicine Image
            if (data.medicine_image != "") {
                $("#medicine_image").attr('src', '<?php echo base_url() ?>' + data.medicine_image);
            } else {
                $("#medicine_image").attr('src', '<?php echo base_url() ?>uploads/medicine_images/no_medicine_image.png');
            }

            // Medicine Details
            $("#medicine_names").html(data.medicine_name);
            $("#medicine_category_ids").html(data.medicine_category);
            $("#medicine_companys").html(data.medicine_company);
            $("#units").html(data.unit);
            $("#min_levels").html(data.min_level);
            $("#reorder_levels").html(data.reorder_level);
            $("#unit_packings").html(data.unit_packing);

            // Generate and Display Barcode
            if (data.barcode) {
                JsBarcode("#medicine_barcode", data.barcode, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 2,
                    height: 50,
                    displayValue: true,
                });
            } else {
                $("#medicine_barcode").html("<p>No Barcode Available</p>");
            }

            // Open the modal
            holdModal('viewModal');
        },
    });
}

</script>


<script type="text/javascript">
    $(document).ready(function() {
        $('.test_ajax').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": base_url+"hospital/store/dt_search",
                "type": "POST",
                "dataSrc": function (json) {
                        // Update the totals in the HTML elements
                        // $('#total_qty').text("( Total Avb Qty: " + json.total_qty + " ) ");
                        // $('#total_purchase').text("( Total Purchase Val: " + json.total_purchase + " ) ");
                        // $('#total_sale').text("( Total Sale Val: " + json.total_sale + " ) ");
                        
                        // Return the data to DataTables
                        return json.data;
                    }
                },
                responsive: 'true',
                dom: "Bfrtip",

            /* columnDefs: [
                {
                
                className: 'dt-body-hover'
                    }
                ],*/

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
	
	
        $(".addmedicine").click(function(){
            $('#formadd').trigger("reset");	
        });

</script>
<script>
    $(document).ready(function () {
        // Generate barcode when medicine name or another field changes
        $('#medicine_name').on('input', function () {
            var barcodeValue = $(this).val() || 'MEDICINE123';
            $('#barcode').val(barcodeValue); // Set barcode value in input
            JsBarcode("#barcodePreview", barcodeValue, {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 50,
                displayValue: true
            });
        });
    });
    $(document).on('click', '#addBarcodeButton', function () {
        $('#barcodeContainer').append(`
            <div class="form-group">
                <label>Barcode</label>
                <input type="text" name="barcodes[]" class="form-control">
                <button type="button" class="btn btn-danger removeBarcodeButton">Remove</button>
            </div>
        `);
    });
    $(document).on('click', '.removeBarcodeButton', function () {
        $(this).closest('.form-group').remove();
    });

    $(document).on('change','#expiry_is_optional',function(){
        if($(this).val() == 'y'){
            $('.min_exp').hide();
        }else{
            $('.min_exp').show();
        }
    });
    function editRecord(id) {
    $.ajax({
        url: '<?php echo base_url(); ?>hospital/pharmacy/getDetails',
        type: "POST",
        data: { pharmacy_id: id },
        dataType: 'json',
        success: function (data) {
            // Populate the fields with data using class selectors
            $(".form-id").val(data.id);
            $(".medicine-name").val(data.medicine_name);
            $(".medicine-category").val(data.medicine_category_id).trigger('change');
            $(".medicine-company").val(data.medicine_company);
            $(".medicine-unit").val(data.unit);
            $(".medicine-min-level").val(data.min_level);
            $(".open-stock").val(data.open_stock);
            $(".reorder-level").val(data.reorder_level);
            $(".unit-packing").val(data.unit_packing);
            $(".medicine-note").val(data.note);
            $(".expiry-is-optional").val(data.expiry_is_optional);
            $(".expiry-date-min").val(data.expiry_date_min);

            // Populate the medicine image
            if (data.medicine_image) {
                $(".pre-medicine-image").val(data.medicine_image);
                $(".medicine-photo").attr('src', '<?php echo base_url(); ?>' + data.medicine_image);
            } else {
                $(".medicine-photo").attr('src', '<?php echo base_url(); ?>uploads/medicine_images/no_medicine_image.png');
            }

            // Open the modal
            holdModal('myModaledit');
        },
    });
}


    </script>
