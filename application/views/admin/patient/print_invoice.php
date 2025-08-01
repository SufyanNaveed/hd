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
        .token-number {
            font-weight: bold;
            font-size: 24pt; /* Adjust font size as needed */
            text-align: center;
            margin-bottom: 0px !important;
            font-family:"Courier New", Courier, monospace;
        }
    </style>
</head>
<body>
    <?php $logoresult = $this->setting_model->getLogoImage(); ?>
    <div class="content" >
       <!--  <h3 id="logo"><img style="max-height:50px; margin-left:100px" src="<?php echo base_url('uploads/hospital_content/logo/').$logoresult["mini_logo"]?>" alt='Logo'></h3> -->
        <div id='printbox'>
           
            <h2 style="margin-top:0" class="text-center"><?= $this->setting_model->getCurrentHospitalName() ?></h2>
            
            <table class="custom-tableinv_info" style='font-family:"Courier New", Courier, monospace; font-size:100%' border="0">
<tr>
    <td>
    <div class="token-number">
                <div style="padding-bottom: 0px !important; font-size:16px;">Token#</div>
                <div><?php echo sprintf('%02d', $token_number); ?></div>
            </div>
    </td>
</tr>
                <tr>
                    <td><b>Patient Name:</b></td>
                    <td><h2><?php echo $invoice_detail['patient_name']?></h2></td>
                </tr>
                <?php if(isset($invoice_detail['mrno'])){?>
                <!--<tr>
                    <td><b>MR No.:</b></td>
                    <td><b><?php echo $invoice_detail['mrno']?></b></td>
                </tr> -->
                <?php } ?>
                <tr>
                    <td><b>MR No.:</b></td>
                    <td><b><?php echo $invoice_detail['patient_unique_id']?></b></td>
                </tr>
                <tr>
                    <td><b>Mobile No.:</b></td>
                    <td><b><?php echo $invoice_detail['mobileno']?></b></td>
                </tr>
                <tr>
                    <td><b>Patient Cnic.:</b></td>
                    <td><b><?php echo $invoice_detail['patient_cnic'] ? $invoice_detail['patient_cnic'] : $invoice_detail['other_nationality'] ?></b></td>
                </tr>
                <tr>
                    <td><b>OPD Slip No.:</b></td>
                    <td><b><?php echo $invoice_detail['opd_slip_no']?></b></td>
                </tr>
                <tr>
                    <td><b>Date & Time:</b></td>
                    <td><b><?php echo date('d-M-Y h:i:s',strtotime($invoice_detail["appointment_date"]))?></b></td>
                </tr>

              
                <tr>
                    <td><b>Hospital Name:</b></td>
                    <td><b><?php echo $hospital_name?></b></td>
                </tr>


                <tr>
                    <td><b>Generated By:</b></td>
                    <td><b><?php echo $generated_by?></b></td>
                </tr>
                
                <tr>
                    <td><b>Remarks:</b></td>
                    <td><b><?php echo isset($invoice_detail['remarks']) && $invoice_detail['remarks'] != "" ? $invoice_detail['remarks'] : "" ?></b></td>
                </tr>



            </table><hr>
           
            <div class="text-center"><b>Thank you</b></div>
        </div>
    </div>
</body>
</html>
