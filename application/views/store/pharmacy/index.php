
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('medicines'); ?></h3> 
                        <div class="box-tools pull-right">
                            <a data-toggle="modal" href="<?php echo base_url(); ?>hospital/pharmacy/stockTransfer" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i>Indent / Transfer Medicine</a> 
                            <a href="<?php echo base_url(); ?>hospital/pharmacy/purchase" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> Purchase Medicine</a>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('medicines') . " " .$this->lang->line('stock'); ?></div>
                      <div class="table-responsive-mobile">   
                        <table class="custom-table table table table table-striped table-bordered table-hover test_ajax " cellspacing="0" width="100%">
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


<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    $('.test_ajax').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": base_url+"hospital/pharmacy/dt_search/<?php echo $stock; ?>",
            "type": "POST",
            "dataSrc": function (json) { 
                    return json.data;
                }
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
</script>
