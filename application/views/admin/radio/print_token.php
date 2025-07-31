<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Radiology Invoice</title>
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
            margin: 15pt;
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
        <h3 id="logo"><img style="max-height:50px; margin-left:100px" src="<?php echo base_url('uploads/hospital_content/logo/'.$logoresult["mini_logo"]) ?>" alt='Logo'></h3>
        <!-- <h3 id="logo"><br><img style="max-height:50px;margin-left:20px" src="uploads/printing/2.jpg" alt='Logo'></h3> -->
        <div id='printbox'>
            <h2 style="margin-top:0" class="text-center"><?= $this->setting_model->getCurrentHospitalName() ?></h2>

            <table id="products" class="inv_info" style='font-family:"Courier New", Courier, monospace; font-size:92%' border="0">

                <tr>
                    <td width="90"><b>Patient:</b></td>
                    <td><b><?php echo $detail[0]['patient_name'] ?></b></td>
                </tr>
                <tr>
                    <td width="90"><b>Age:</b></td>
                    <td><b><?php echo $detail[0]['age']?></b></td>
                </tr>
                <tr>
                    <td width="90"><b>Gender:</b></td>
                    <td><b><?php echo $detail[0]['gender']?></b></td>
                </tr>
                <tr>
                    <td width="90"><b>MR No.:</b></td>
                    <td><b><?= $detail[0]["patient_unique_id"] .'-'. date('m', strtotime($detail[0]['patient_reg'])).'/'. date('Y', strtotime($detail[0]['patient_reg'])); ?></b></td>
                </tr>
                <tr>
                    <td width="90"><b>Patient ID:</b></td>
                    <td><b><?php echo $detail[0]["patient_unique_id"]?></b></td>
                </tr>
                <tr>
                    <td width="90"><b>Mobile No.:</b></td>
                    <td><b><?php echo $detail[0]['mobileno']?></b></td>
                </tr>
                <tr>
                    <td width="85"><b>Date:</b></td>
                    <td><b><?php echo date('d F Y h:i:s A')?></b></td>
                </tr>

            </table><hr>

            <h4 class="m-b-5 m-t-10 bg-dark strip text-center">Test(s) Description</h4>

            <table id="products" border="0" style='font-family:"Courier New", Courier, monospace; font-size:20%'>

                <tbody>
                    <tr>
                        <td><b>Sr#</b></td>
                        <td><b><?php echo $this->lang->line('test') . " " . $this->lang->line(''); ?></b></td>
                       <!-- <td><b><?php echo $this->lang->line('doctor'); ?></b></td>-->

                        <td><b><?php echo $this->lang->line('price'); ?></b></td>
                        <td><b><?php echo "Report Days"; ?></b></td>
                    </tr>
                    <?php if(is_array($detail) && count($detail) > 0) {
                        $i = 0; $totalAmt = 0;
                        foreach($detail as $bill):
                            $totalAmt+=isset($bill["radio_discount"]) && $bill["radio_discount"] > 0 ? $bill["apply_charge"] - $bill["radio_discount"] : $bill["apply_charge"] ;
                            $amt=isset($bill["radio_discount"]) && $bill["radio_discount"] > 0 ? $bill["apply_charge"] - $bill["radio_discount"] : $bill["apply_charge"] ;
                            ; 
                            $i++;
                        ?>
                        <tr>
                            <td ><b><?= $i; ?></td>
                        <!-- <td style="width:63%" word-wrap: break-word;><?= $bill['test_name'] ." - ". $bill['test_name'] ?> </td> -->
                            <td ><b><?= $bill['test_name'] ?></td>
                           <!-- <td class="text-left" ><b><?=  $bill["doctorname"]." ".$bill["doctorsurname"]; ?></td>-->
                            <td class="text-right" ><b><?= number_format($amt) ?></td>
                            <td class="text-right" ><b><?= $bill['report_days'] ?></td>
                        </tr>
                    <?php endforeach; } else { ?>
                        <tr><th class="text-right" colspan="5" style="font-size: 9px;">No Record Found</th></tr>
                    <?php } ?>

                    <tr><th colspan="5"><hr></tr>
                    
                    <tr>
                        <td></td>
                        <td></td>
                        <td width="90"><strong>Total (Rs.) </strong> </td>
                        
                        <td><strong> <?= number_format($totalAmt) ?> </strong></td>
                    </tr>
                </tbody>

            </table>
            <hr>
            <div class="text-center"><b>Thank you</b></div>
        </div>
    </div>
</body>
</html>
