<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
//echo "<pre>";print_r($data);exit;
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <style type="text/css">
            body {
                font-family: Arial, sans-serif; /* Set the font family to Arial */
                font-size: 11px !important; /* Set the default font size */
            }
            .printablea4{width: 100%;}
            /*.printablea4 p{margin-bottom: 0;}*/
            .printablea4>tbody>tr>th,
            .printablea4>tbody>tr>td{padding:2px 10px; line-height: 1.42857143;vertical-align: top; font-size: 11px;}

            @media print {
                textarea , TD{
                    font-size: 11pt !important;
                }
            }
            #parameterTable {
                width: 100%;
                border-spacing: 10px;
                cellspacing: 0;
                cellpadding: 0;
            }
            #parameterTable>tbody>tr>th,
            #parameterTable>tbody>tr>td{padding:2px 10px; line-height: 1.42857143;vertical-align: top; font-size: 11px;}
            #parameterTable tr:nth-child(odd) {
                background-color: #f0f0f0; /* Light background color for odd rows */
            }

            #parameterTable tr:nth-child(even) {
                background-color: #ffffff; /* Plain background color for even rows */
            }

            @media print {
                #parameterTable tr:nth-child(odd) {
                    background-color: #dcdcdc !important; /* Slightly darker background color for odd rows in print */
                }
            }
            .printablea4 th {
                line-height: 0.2;
                font-size: 11px !important; 
            }
            .printablea4 tr {
                font-size: 11px !important; /* Adjust font-size for tr */
            }
        </style>
        
    </head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                    <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div style="display: flex;">
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'];
                            }
                            ?>" style="height: 80px; width: 100%; padding: 0; margin: 0;">
                        </div>


                    <?php } else{ ?>
                    <br> <br> <br> <br> <br> <br>             <?php } ?>
                    <?php if($print=='yes'){?>
                    <!-- <table width="100%" class="printablea4" border="0">
                        <tr>
                            <td align="text-left"><?php echo  "Bill# "; ?><img src="<?= base_url().$barcode ?>">
                            </td>
                            <td align="center"><?php echo  "Patient Login# <br>"; ?><?php echo 'username : '.$result['username'].'<br> password : '.$result['password']?>
                            </td>
                            <td align="right"><?php echo  "Track Online# "; ?><img src="<?= $qr_code ?>">
                            </td>
                        </tr>
                    </table> -->
                    <?php }?>
                    <table class="custom-tableprintablea4" style="border-top: 4px solid #000; border-bottom: 1px solid #000; font-weight:bold; font-size:18px; border-collapse: separate; border-spacing: 0px; cellspacing=0 cellpadding=0 width=100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="line-height: 1.4 !important; font-size: 17px !important;">Pathology Report</th>
                            </tr>

                        </thead>
                    </table>
                    <table width="100%" class="printablea4" border="0" style="border-bottom: 0.3px solid #000; margin-bottom:3px; font-size: 11px !important;">
                        <tr class="mt-4">
                            <td style="font-size: 11px !important;">MRNO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b><?php echo $result["patient_unique_id"] ?></b></td>
                            <td></td>
                            <td style="font-size: 11px !important;">Requesting Physician &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $result["doctorname"] . " " . $result["doctorsurname"]; ?></td>
                        </tr>
                        <tr class="mt-4">
                            <td style="font-size: 11px !important;">Patient Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b><?php echo $result["patient_name"] ?></b></td>
                            <td></td>
                            <td style="font-size: 11px !important;">Specimen ID &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo  $result["patient_unique_id"] . '-' . date('m', strtotime($result['patient_reg'])) . '/' . date('Y', strtotime($result['patient_reg']));  ?></td>
                        </tr>
                        <tr class="mt-4">
                            <td style="font-size: 11px !important;">Age/Sex &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $result["age"] . ' Year(s)/' . $result["gender"]; ?></td>
                            <td></td>
                            <td style="font-size: 11px !important;">Receiving Time &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                                <?php 
                                $receivingTime = new DateTime($result['created_at']);
                                //$receivingTime->modify('+5 hours'); // Add 5 hours to the time
                                echo $receivingTime->format('d-M-Y H:i:s');
                                ?>
                             </td>
                        </tr>
                        <!-- <tr class="mt-4">
                            <td>Phone &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $result["mobileno"]; ?></td>
                            <td></td>
                            <td>Collected On &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo date('d-M-Y', strtotime($result['reporting_date'])); ?></td>
                        </tr> -->

                        <!-- <tr class="mt-4">
                            <td>Address &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $result["address"] ?></td>
                            <td></td>
                            <td>Reported On &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo date('d-M-Y', strtotime($result['reporting_date'])); ?></td>
                        </tr> -->
                    </table>
                    <!-- <hr style="border-color:#000; margin-top:1px; margin-bottom:2px;"> -->
                   
                    <!-- <?php foreach ($reportDetails['reportsResult'] as $report) { ?>
                        <table class="custom-tableprintablea4" style="width:100%; cellspacing=0 cellpadding=0">
                            <h4 style="margin-left:3px !important; margin-top:7px; margin-bottom:7px;"><b><?php echo strtoupper($report["test_name"]); ?></b></h4>
                        </table>
                    <?php }?>  -->
                        <!-- <table class="custom-tableprintablea4" id="testreport_check" width="100%" style="border: 1px solid;background-color: #80808038;">
                            <tr>
                                
                                <th style="text-align:left;"><?php echo $report['test_name']; ?></th>
                            </tr>
                        

                        </table>  -->

                        <?php foreach ($reportDetails as $patho_id => $patho_data): ?>
    <table class="custom-tableprintablea4" style="border: 1px solid #000; padding: 2px !important; border-collapse: separate; border-spacing: 0px; width: 100%; margin-bottom: 2px !important; margin-top: 3px;">
        <thead>
            <tr>
                <th class="col-test-name" style="width: 17%; text-align: left; font-size: 12px !important; padding-left: 7px !important;">
                    <?php echo strtoupper($patho_data['pathologyInfo']['test_name']); ?>
                </th>
                <?php foreach (array_reverse($patho_data['reports']) as $key => $report): ?>
                    <?php if ($key === 0): ?>
                    <th class="col-result" style="width: 20%; font-size: 12px !important; padding-top: 7px !important; padding-bottom: 3px !important;">
                            <span style="font-weight: bold; font-size: 10.5px;">CURRENT RESULT</span>
                            <p style="font-weight: normal; font-size: 11px !important;">
                                <?php
                                $updatedTime = new DateTime($report['updated_at']);
                                $updatedTime->modify('+5 hours'); // Add 5 hours
                                echo $updatedTime->format('d-M-Y H:i:s');
                                ?>
                            </p>
                        </th>
                        <?php endif; ?>
                <?php endforeach; ?>
                <th class="col-units" style="width: 23%; text-align: left; font-size: 12px !important;">UNIT(s)</th>
                <th class="col-normal-range" style="width: 8%; text-align: left; font-size: 12px !important;">NORMAL RANGE</th>
            </tr>
        </thead>
    </table>

    <table class="custom-tableprintablea4" style="width: 100%; border-collapse: separate; border-spacing: 0px; margin-bottom: 2px;">
        <?php foreach ($patho_data['paramterDetails'] as $value): ?>
            <tr>
                <td class="col-test-name" style="width: 17%; text-align: left; font-size: 11px !important; padding: 2px;">
                    <?php echo strtoupper($value["parameter_name"]); ?>
                </td>
                <?php foreach (array_reverse($patho_data['reports']) as $key => $report): ?>
                    <td class="col-result" style="width: 8%; text-align: center; font-size: 11px !important; padding: 2px; <?php echo $key != 0 ? 'display:none;' : ''; ?>"
                    >
                        <?php
                        $found = false;
                        foreach ($patho_data['reportsResult'] as $resultValue) {
                            if ($resultValue["pathology_report_id"] == $report["report_id"] && $resultValue["parameter_id"] == $value["id"]) {
                                echo $resultValue["pathology_report_value"] ?: "N/A";
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            echo "N/A";
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
                <td class="col-units" style="width: 7%; text-align: center; font-size: 11px !important; padding: 2px;">
                    <?php echo $value["unit_name"]; ?>
                </td>
                <td class="col-normal-range" style="width: 20%; text-align: right; font-size: 11px !important; padding: 2px;">
                    <?php echo $value["reference_range"]; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <hr style="height: 1px; clear: both; margin-bottom: 2px; margin-top: 2px;">
<?php endforeach; ?>


                    <p style="margin-top:2px !important;"><?php
                        if (!empty($print_details[0]['print_footer'])) {
                            echo $print_details[0]['print_footer'];
                        }
                        ?></p>
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </div>
</html>
<script type="text/javascript">
    window.print();
    window.onfocus=function(){ window.close();}
</script>

