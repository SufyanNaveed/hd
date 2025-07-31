<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Pathology Invoice</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-size: 9pt;
            background-color: #fff;
            margin-left: 0px;
            margin-top: 0px;
        }
        /*.content {
            height: 100px;
            width: 500px;
            color: #111;
        }*/
        #products {
            width: 100%;
        }

        #products tr td {
            font-size: 10pt;
        }

        #printbox {
            width: 280px;
            margin: 1pt;
            padding: 1px;
            text-align: justify;
        }
        .inv_info tr td {
            padding-right: 10pt;
        }

        .products_td {
            margin-right: 0px;
        }

        .product_row {
            margin: 10pt;
        }

        .stamp {
            margin: 5pt;
            padding: 3pt;
            border: 3pt solid #111;
            text-align: center;
            font-size: 20pt;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php $logoresult = $this->setting_model->getLogoImage();?>
    <div class="content" >
        <h3 id="logo"><img style="max-height:60px; margin-left:100px" src="<?php echo base_url('uploads/hospital_content/logo/'.$logoresult["mini_logo"]) ?>" alt='Logo'></h3>
        <!-- <h3 id="logo"><br><img style="max-height:50px;margin-left:20px" src="uploads/printing/2.jpg" alt='Logo'></h3> -->
        <div id='printbox'>
            <h2 style="margin-top:0" class="text-center"><?= $this->setting_model->getCurrentHospitalName() ?></h2>

            <table id="products" class="inv_info" style='font-family:"Courier New", Courier, monospace; font-size:100%' border="0">

                <tr>
                    <td width="95"><b>Patient:</b></td>
                    <td><b><?php echo $details['info']['patient_name']?></b></td>
                </tr>
                <tr>
                    <td width="95"><b>Age:</b></td>
                    <td><b><?php echo $details['info']['age']?></b></td>
                </tr>
                <tr>
                    <td width="95"><b>Gender:</b></td>
                    <td><b><?php echo $details['info']['gender']?></b></td>
                </tr>
                <tr>
                    <td width="95"><b>MR No.:</b></td>
                    <td><b><?= $details['info']["patient_unique_id"] .'-'. date('m', strtotime($details['info']['patient_reg'])).'/'. date('Y', strtotime($details['info']['patient_reg'])); ?></b></td>
                </tr>
                <tr>
                    <td width="95"><b>Patient ID:</b></td>
                    <td><b><?php echo $details['info']["patient_unique_id"]?></b></td>
                </tr>
                <tr>
                    <td width="95"><b>Mobile No.:</b></td>
                    <td><b><?php echo $details['info']['mobileno']?></b></td>
                </tr>
                <tr>
                    <td width="100"><b>Invoice No.:</b></td>
                    <td><b><?php echo $details['info']['bill_no']?></b></td>
                </tr>
                <tr>
                    <td width="95"><b>Date:</b></td>
                    <td><b><?php echo date('d F Y h:i:s A')?></b><br></td>
                </tr>
            </table><hr>

            <h5 class="m-b-5 m-t-10 bg-dark strip text-center">Test(s) Description</h5>

            <table id="products" border="0" style='font-family:"Courier New", Courier, monospace; font-size:20%'>

                <tbody>
                    <tr>
                        <td><b>Sr#</b></td>
                        <td><b><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></b></td>

                        <td><b><?php echo $this->lang->line('price'); ?></b></td>
                        <td><b><?php echo "Report Days"; ?></b></td>
                    </tr>
                    <?php if(is_array($details['report']) && count($details['report']) > 0) {
                        $i = 1; $t_amount = 0;
                        foreach($details['report'] AS $row): $t_amount += $row['apply_charge'] - $row['pth_discount']; ?>
                        <tr>
                            <td ><b><?= $i ?></b></td>
                        <!-- <td style="width:63%" word-wrap: break-word;><?= $row['test_name'] ." - ". $row['test_name'] ?> </td> -->
                            <td ><b><?= $row['test_name'] ?></b></td>
                            <td class="text-right" ><b><?= number_format($row['apply_charge'] - $row['pth_discount']) ?><b></td>
                            <td class="text-right" ><b><?= $row['report_days'] ?></b></td>
                        </tr>
                    <?php endforeach; } else { ?>
                        <tr><th class="text-right" colspan="5" style="font-size: 9px;">No Record Found</th></tr>
                    <?php } ?>

                    <tr><th colspan="5"><hr></tr>
                    
                    <tr>
                        <td></td>
                        <td ><strong>Total Amount (Rs.) </strong> </td>
                        
                        <td style="margin-left:12px"><strong> <?= number_format($t_amount) ?> </strong></td>
                    </tr>
                </tbody>

            </table>
            <hr>
            <div class="text-center"><b>Thank you</b></div>
        </div>
    </div>
</body>
</html>
