<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $this->customlib->getAppName(); ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="theme-color" content="#5190fd" />
        <link href="<?php echo base_url(); ?>backend/images/s-favican.png" rel="shortcut icon" type="image/x-icon">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/jquery.mCustomScrollbar.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/custom_style.css">
    </head>
    <body class="p-5">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="printing-head">
                    <h4 class="m-t-5 m-b-20"><?= $this->lang->line('patient') . ": " . $details['info']['patient_name']  ?></h4>
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <h5 class="m-t-0"><?= $this->lang->line('age') . ": " . $details['info']['age'] ?> - <?= $this->lang->line('gender') . ": ". $details['info']['gender'] ?></h5>
                            <p class="m-b-10">
                                <?= $this->lang->line('address') . ": " . $details['info']['address'] ?> - <?= $this->lang->line('phone') . ": ". $details['info']['mobileno'] ?>
                            </p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right">
                            <h5><?= $this->lang->line('blood') . " " .$this->lang->line('group') . ": "  . $details['info']['blood_group'] ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h4 class="m-b-10 m-t-10 bg-dark strip text-center">Tests Description</h4>
                <table class="custom-table table table-bordered" border="0">
                    <thead>
                        <th width="">Sr#</th>
                        <th width=""><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th>
                        <th><?php echo $this->lang->line('charges'); ?></th>
                    </thead>
                    <tbody>
                        
                    <?php if(is_array($details['report']) && count($details['report']) > 0) {
                        $i = 1; $t_amount = 0;
                        foreach($details['report'] AS $row): $t_amount += $row['apply_charge']; ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $row['test_name'] ." - ". $row['short_name'] ?> </td>
                            <td><?= number_format($row['apply_charge'], 2) ?>  PKR</td>
                        </tr>
                    <?php endforeach; } else { ?>
                        <tr><th class="text-center" colspan="5">No Record Found</th></tr>
                    <?php } ?>
                        <tr class="text-center">
                            <td colspan="2" class="text-center"><strong>Total Amount</strong></td>
                            <td><b><?= number_format($t_amount, 2) ?>  PKR</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <code class="text-center font-10" stlye="margin-top: 0px;">
            <h5 class="font-bold m-t-10"> THANKS YOU FOR PARTICIPATION </h5>
            <p class="m-b-0">
                This is a computer generated receipt - <?= 'printed at '.date('d F Y h:i:s A') ?>.
                
            </p>
        </code>
    </body>
</html>
<script type="text/javascript">
    window.print();
    window.onfocus=function(){ window.close();}
</script>