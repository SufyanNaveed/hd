<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"> Commission Payments of (<?php echo $resultlist[0]['doctor_name']; ?>)</h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('organisation', 'can_add')) { ?>
                                <!-- <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm organisation"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') . " " . $this->lang->line('organisation'); ?></a>  -->
                            <?php } ?>
                        </div>    
                    </div><!-- /.box-header -->
                    <?php
                    if (isset($resultlist)) {
                        ?>
                        <div class="box-body">
                            <div class="download_label"><?php echo $title; ?></div>
                            <table class="custom-table table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr#</th>
                                        <th>Commission Amount</th>
                                        <th>Amount Paid</th>
                                        <th>Cheque No</th>
                                        <th>Bank</th>
                                        <th>Payment Date</th>
                                        <th>Balance Amount</th>
                                        <!-- <th></th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($resultlist)) {
                                        ?>
                                                                        
                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($resultlist as $payment) {
                                            ?>
                                            <tr class=""> 
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo $payment['commission_amount']; ?></td>
                                                <td><?php echo $payment['paid_amount']; ?></td>
                                                <td><?php echo $payment['cheque_no']; ?></td>
                                                <td><?php echo $payment['bank']; ?></td>
                                                <td><?php echo $payment['payment_date']; ?></td>
                                                <td><?php echo $payment['balance_amount']; ?></td>
                                                <!-- <td>
                                                <?php if ($this->rbac->hasPrivilege('organisation', 'can_delete')) { ?>
                                                        <a  onclick="delete_recordById('<?php echo base_url(); ?>admin/tpamanagement/deleteDeduction/<?php echo $payment['id']; ?>', '<?php echo $this->lang->line('delete_message') ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip"  title="<?php echo $this->lang->line('delete'); ?>" >
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td> -->
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div><?php } ?>
                </div>  
            </div>
        </div> 
    </section>
</div>
