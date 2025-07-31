<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Invoice</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-size: 9pt;
            background-color: #fff;
            margin-left: -4px;
            margin-top: 15px;
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
            font-size: 8pt;
        }

        #printbox {
            width: 280px;
            margin: 5pt;
            padding: 5px;
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
    <?php $logoresult = $this->setting_model->getLogoImage(); ?>
    <div class="content" >
        <h3 id="logo"><br><img style="max-height:50px; margin-left:100px" src="uploads/hospital_content/logo/<?php echo $logoresult["mini_logo"] ?>" alt='Logo'></h3>
        <!-- <h3 id="logo"><br><img style="max-height:50px;margin-left:20px" src="uploads/printing/2.jpg" alt='Logo'></h3> -->
        <div id='printbox'>
            <h2 style="margin-top:0" class="text-center"><?= $this->setting_model->getCurrentHospitalName() ?></h2>

            <table class="custom-tableinv_info" style='font-family:"Courier New", Courier, monospace; font-size:75%' border="0">

                <tr>
                    <td><b>Patient Name:</b></td>
                    <td><b><?php echo $invoice_detail['patient_name']?></b></td>
                </tr>
                <?php if(isset($invoice_detail['mrno'])){?>
                <tr>
                    <td><b>MR No.:</b></td>
                    <td><b><?php echo $invoice_detail['mrno']?></b></td>
                </tr>
                <?php } ?>
                <tr>
                    <td><b>Patient ID:</b></td>
                    <td><b><?php echo $invoice_detail['patient_unique_id']?></b></td>
                </tr>
                <tr>
                    <td><b>Mobile No.:</b></td>
                    <td><b><?php echo $invoice_detail['mobileno']?></b></td>
                </tr>
                <tr>
                    <td><b>Invoice No.:</b></td>
                    <td><b><?php echo $invoice_detail['opd_no']?></b></td>
                </tr>
                <tr>
                    <td><b>Date:</b></td>
                    <td><b><?php echo date('d-M-Y h:i A',strtotime($invoice_detail["appointment_date"]))?></b><br></td>
                </tr>

                <!--<tr>
                    <td><b>CNIC:</b></td>
                    <td><?php echo $invoice_detail['patient_cnic']?></td>
                </tr> -->




            </table><hr>
            <table id="products" border="0" style='font-family:"Courier New", Courier, monospace; font-size:20%'>
                <tr>
                    <td><b>Consultant:</b></td>
                    <td class="products_td"><b><?php echo $invoice_detail['name']?></b></td>
                    <td><b>KPO:</b></td>
                    <td class="products_td"><b><?php echo $invoice_detail['kop_name']?></b></td>
                    <!--<td><b> TPA:</b></td>
                    <td class="products_td"><?php echo $invoice_detail['organisation_name']?></td>-->
                </tr>
                <tr>
                    <td><b> Department:</b></td>
                    <td><b><?php echo $invoice_detail['department_name']?></b></td>

                    <!--<td><b> Age:</b></td>
                    <td class="products_td"><?php echo $invoice_detail['age']?></td>-->
                </tr>
               <!-- <tr>
                    <td><b> Cusality:</b></td>
                    <td class="products_td"><?php echo $invoice_detail['casualty']?></td>
                    <td><b> Note:</b></td>
                    <td class="products_td"><?php echo $invoice_detail['note_remark']?></td>
                </tr>    -->
            </table><hr>
            <table class="custom-tableinv_info">
                <?php
                //$opd_discount=0;
                $opd_discount=isset($invoice_detail['opd_discount']) && !empty($invoice_detail['opd_discount']) ? $invoice_detail['opd_discount'] : 0;
                ?>
                <?php
                if($opd_discount > 0){?>
                <tr>
                    <td><b>Discount</b></td>
                    <td><b><?php echo $invoice_detail['opd_discount']?></b></td>
                </tr>
                <?php } ?>
                <tr>
                    <td><b>Paid Amount (Rs.) </b></td>
                    <td><b><?php echo isset($opd_discount) && $opd_discount > 0 ? floor($invoice_detail['apply_charge'] - $opd_discount) : floor($invoice_detail['apply_charge'])?></b></td>
                </tr>
            </table>
            <hr>
            <div class="text-center"><b>Thank you</b></div>
        </div>
    </div>
</body>
</html>
