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
            margin-right: 10px;
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
        .table_reports{
            border: 1px solid;
        }
    </style>
</head>
<body>
    <?php $logoresult = $this->setting_model->getLogoImage(); ?>
    <div class="content" >
        <h3 id="logo"><br><img style="max-height:50px; margin-left:40px" src="uploads/hospital_content/logo/<?php echo $logoresult["mini_logo"] ?>" alt='Logo'></h3>
        <!-- <h3 id="logo"><br><img style="max-height:50px;margin-left:20px" src="uploads/printing/2.jpg" alt='Logo'></h3> -->
        <div id='printbox'>
            <h2 style="margin-top:0" class="text-center"><?= $this->setting_model->getCurrentHospitalName() ?></h2>

            <table border="0"> <!-- class="inv_info" -->
                <tr>
                    <td>Pateint ID:</td>
                    <td><u><?php echo $patient_detail['patient_unique_id']?></u></td>
                </tr>
                <!-- <tr>
                    <td>Bill:</td>
                    <td>000000-000<?php echo $patient_detail['id']?></td>
                </tr> -->
                <tr>
                    <td>Date:</td>
                    <td><u><?php echo date('d/m/Y H:i A')?></u></td>
                </tr>
                <tr>
                    <td>Patient Name:</td>
                    <td><u><?php echo $patient_detail['patient_name']?></u></td>
                    <td><u><?php echo $patient_detail['mobileno'] ? $patient_detail['mobileno'] :'0000-000-0000'?></u></td>

                </tr>
                <!-- <tr>
                    <td>Phone:</td>
                    <td><?php echo $patient_detail['mobileno']?></td>
                </tr> -->
                <tr>
                    <td>Age:</td>
                    <td><u><?php echo $patient_detail['age']?></u></td>
                    <td>Gender:</td>
                    <td><u><?php echo $patient_detail['gender']?>&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
                </tr> 
                <tr>
                    <td>K.P.O:</td>
                    <td><u><?php echo $patient_detail['generated_byname']?></u></td>
                </tr>                        
            </table><hr>
            <table class="custom-table table_reports">
                <?php $sumOfReports = 0; if($lab_report){ foreach($lab_report as $report){ $sumOfReports+= $report['apply_charge']; ?>
                    <tr>
                        <td style="width: 200px;" class="products_td table_reports"><?php echo $report['report_type']?></td>
                        <td class="products_td table_reports"><?php echo $report['apply_charge']?></td>
                    </tr>
                <?php }}?>   
            </table>
            <table border="0"> <!-- class="inv_info"> -->
                <tr>
                    <td style="width: 188px;" class="products_td">&nbsp;<b>Total Amount (PKR)</b></td>
                    <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $sumOfReports;?></b></td>
                </tr>
                <tr>
                    <td><b>Consultant Name: </b></td>
                </tr>
                <tr>
                    <td><u><?php echo $patient_detail['name'].' '. $patient_detail['surname']?> </u></td>
                </tr>            
            </table>
            <!-- <hr>
            <div class="text-center">Thank you</div> -->
        </div>
    </div>
</body>
</html>
