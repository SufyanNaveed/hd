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
                        <h3 class="box-title titlefix"> TPA Bill Deductions (<?php echo $resultlist[0]['organisation_name'] . ' - ' . $resultlist[0]['cheque_no']; ?>)</h3>
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
                                        <th>Name</th>
                                        <th>IPD No</th>
                                        <th>Procedure</th>
                                        <th>Consultant</th>
                                        <th>Discharge Date</th>
                                        <th>Cheque No</th>
                                        <th>Total Amount</th>
                                        <th>Tax</th>
                                        <th>Approved Amount</th>
                                        <th>Doctor Share</th>
                                        <th>Hospital Share</th>
                                        <th>Deduction Amount</th>
                                        <th>Cheque Balance</th>
                                        <th>Share Type</th>
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
                                        foreach ($resultlist as $deduction) {
                                            ?>
                                            <tr class=""> 
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo $deduction['patient_name']; ?></td>
                                                <td><?php echo $deduction['ipd_id']; ?></td>
                                                <td><?php echo $deduction['code']; ?></td>
                                                <td><?php echo $deduction['consultant']; ?></td>
                                                <td><?php echo $deduction['discharge_date']; ?></td>
                                                <td><?php echo $deduction['cheque_no']; ?></td>
                                                <td><?php echo $deduction['total_amount']; ?></td>
                                                <td><?php echo $deduction['tax']; ?></td>
                                                <td><?php echo $deduction['approved_amount']; ?></td>
                                                <td><?php echo $deduction['doctor_share']; ?></td>
                                                <td><?php echo $deduction['hospital_share']; ?></td>
                                                <td><?php echo $deduction['deduction_amount']; ?></td>
                                                <td><?php echo $deduction['balance_amount']; ?></td>
                                                <td><?php echo $deduction['deduction_from']; ?></td>
                                                <!-- <td>
                                                <?php if ($this->rbac->hasPrivilege('organisation', 'can_delete')) { ?>
                                                        <a  onclick="delete_recordById('<?php echo base_url(); ?>admin/tpamanagement/deleteDeduction/<?php echo $deduction['id']; ?>', '<?php echo $this->lang->line('delete_message') ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip"  title="<?php echo $this->lang->line('delete'); ?>" >
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
