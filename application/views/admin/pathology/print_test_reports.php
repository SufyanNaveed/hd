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
  <style>
        b {
         border-bottom: 0.5px solid black;

        }
    </style>

    		<table border="0">
              <tr><td><b style="margin-top:0" class="text-center"><?= $this->setting_model->getCurrentHospitalName() ?></b></td></tr>
            <!--<tr><td><h2 style="margin-top:0" class="text-center" ><?= $this->setting_model->getCurrentHospitalName() ?></h2></td></tr>-->
                <tr><td><strong><?= $settinglist[0]['address'] ?></td></strong></tr>
                <tr><td><b style="margin-top:0" class="text-center"><strong><?= $settinglist[0]['phone'] ?></b></td></strong></tr>

            </table>
<br>
            <!--<hr style="height: 1px; clear: both;margin-bottom: 1px; margin-top: 1px">-->
            <table border="1">
                <tr><td><strong><?= $this->lang->line('patient') . ": " . $details['info']['patient_name'] ?></td></strong></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><strong>Bill No : <?= ''.  $details['info']['bill_no'] ?></strong></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><strong>Age : <?= $this->lang->line('age') . $details['info']['age'] ?> - <?= $this->lang->line('gender') . " : ". $details['info']['gender'] ?></strong></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><strong><?= $this->lang->line('blood') . " " .$this->lang->line('group') . ": "  . $details['info']['blood_group'] ?></strong></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><strong>Address : <?= $this->lang->line('') . ": " . $details['info']['address'] ?></strong></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><strong><?= $this->lang->line('phone') . ": ". $details['info']['mobileno'] ?></strong></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><strong>MR LAB #<?= '' . ": ". $details['info']["patient_unique_id"] .'-'. date('m', strtotime($details['info']['patient_reg'])).'/'. date('Y', strtotime($details['info']['patient_reg'])); ?></strong></td></tr>

            </table>

                <h6 class="m-b-5 m-t-10 bg-dark strip text-center">Test(s) Description</h6>
                <table style='font-family:"Courier New", Courier, monospace; font-size:100%' style="table-layout:fixed" cellspacing=0 cellpadding=0 width="25%" border="1" >
                    <thead>
                        <th style="width:0%">Sr#</th>
                        <th style="width:60%" ><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th>

                        <th style="width:20%" ><?php echo $this->lang->line('price'); ?></th>
                        <th style="width:20%" ><?php echo "Report Days"; ?></th>
                    </thead>
                    <tbody>

                    <?php if(is_array($details['report']) && count($details['report']) > 0) {
                        $i = 1; $t_amount = 0;
                        foreach($details['report'] AS $row): $t_amount += $row['apply_charge'] - $row['pth_discount']; ?>
                        <tr>
                            <td ><strong><?= $i ?></strong></td>
                           <!-- <td style="width:63%" word-wrap: break-word;><?= $row['test_name'] ." - ". $row['test_name'] ?> </td> -->
                            <td ><strong><?= $row['test_name'] ?></strong> </td>
                            <td class="text-right" ><strong><?= number_format($row['apply_charge'] - $row['pth_discount']) ?></strong></td>
                            <td class="text-right" ><strong><?= $row['report_days'] ?></strong> </td>
                        </tr>
                    <?php endforeach; } else { ?>
                        <tr><th class="text-right" colspan="5">No Record Found</th></tr>
                    <?php } ?>
                        <tr >

                            <td></td><td><strong>Total  </strong> </td>

                            <th> <strong> <?php echo $currency_symbol?><?= number_format($t_amount) ?> </strong></th>
                            <!--<td><b><?= number_format($t_amount, 2) ?>  PKR</b></td> -->
                        </tr>
                    </tbody>
                </table>


            <p class="m-b-0">

                <br>This is a computer generated receipt - <?= 'printed at '.date('d F Y h:i:s A') ?>.</br>

            </p>
        </code>
    </body>
</html>
<script type="text/javascript">
   window.print();
   window.onfocus=function(){ window.close();}
</script>