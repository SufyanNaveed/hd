<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <style type="text/css">
 
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
                font-size: 11px; 
            }
            .printablea4 tr {
                font-size: 11px; /* Adjust font-size for tr */
            }
        </style>
    </head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                    <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div class="pprinta4">
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'];
                            }
                            ?>" class="img-responsive" style="height:100px; width: 100%;">
                        </div>
                    <?php } else{ ?>
                    <br> <br> <br> <br> <br> <br><br>              <?php } ?>
                   <!-- <?php if($print=='yes'){?>
                   <table width="100%" class="printablea4" border="0">
                        <tr>
                            <td align="text-left"><h6><?php echo  "Bill# "; ?><img src="<?= base_url().$barcode ?>"></h6>
                            </td>
                            <td align="center"><h6><?php echo  "Patient Login# <br>"; ?><?php echo 'username : '.$result['username'].'<br> password : '.$result['password']?></h6>
                            </td>
                            <td align="right"><h6><?php echo  "Track Online# "; ?><img src="<?= $qr_code ?>"></h6>
                            </td>
                        </tr>
                    </table>
                    <?php }?> -->
                    <table class="custom-tableprintablea4" style="border-top: 4px solid #000; border-bottom: 1px solid #000; font-weight:bold; border-collapse: separate; border-spacing: 0px; cellspacing=0 cellpadding=0 width=100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="line-height: 1.4 !important; font-size:17px !important;">Pathology Report</th>
                            </tr>

                        </thead>
                    </table>

                    <table width="100%" class="printablea4" border="0" style="margin-top:8px;">

                        <tr class="mt-4">
                            <td>MRNO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b><?php echo $result["patient_unique_id"] ?></b></td>
                            <td></td>
                            <td>Requesting Physician &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $result["doctorname"] . " " . $result["doctorsurname"]; ?></td>
                        </tr>
                        <tr class="mt-4">
                            <td>Patient Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b><?php echo $result["patient_name"] ?></b></td>
                            <td></td>
                            <td>Specimen ID &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo  $result["patient_unique_id"] . '-' . date('m', strtotime($result['patient_reg'])) . '/' . date('Y', strtotime($result['patient_reg']));  ?></td>
                        </tr>
                        <tr class="mt-4">
                            <td>Age/Sex &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $result["age"] . ' Year(s)/' . $result["gender"]; ?></td>
                            <td></td>
                            <td>Receiving Time &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                                <?php 
                                $receivingTime = new DateTime($result['created_at']);
                                //$receivingTime->modify('+5 hours'); // Add 5 hours to the time
                                echo $receivingTime->format('d-M-Y H:i:s');
                                ?>
                            </td>
                        </tr>
                        
                    </table>
                    <hr style="border-color:#000; margin-top:2px; margin-bottom:0px;">

                    <!-- <table class="custom-tableprintablea4" style="width:100%; cellspacing=0 cellpadding=0">
                        <h5 style="margin-left:10px !important; margin-top:7px; margin-bottom:7px;"><b><?php echo strtoupper($detail[0]["test_name"]); ?></b></h5>
                    </table> -->

                    <?php if($detail[0]["show_description_only"]==0){?>

                    <table class="custom-tableprintablea4" style="border: 1px solid #000;padding: 2px !important;border-collapse: separate; border-spacing: 0px; cellspacing=0 cellpadding=0 width=100%; margin-bottom:2px !important; margin-top:3px;">
                        <thead>
                            <tr>
                                <th style="width:15%; font-size: 12px !important; padding-left:7px !important;"><?php echo strtoupper($detail[0]["test_name"]); ?></th>
                                <?php
                                    foreach (array_reverse($reportDetails['reports']) as $key => $report) {
                                ?>
                                    <th style="width:12.5%; font-size: 12px !important; padding-top:7px !important;">
                                    <?php if ($key === 0) : ?>
                                                    <span style="font-weight: bold; font-size:10.5px; margin-left:-7px;">CURRENT RESULT</span> <br><br><br><br>
                                                <?php endif; ?>
                                        <?php echo date('d-M-Y', strtotime($report['updated_at'])); ?> <br><br><br><br><br>
                                        <p style="font-weight:normal; font-size:11px !important;">
                                            &nbsp;&nbsp;&nbsp;
                                            <?php
                                            $updatedTime = new DateTime($report['updated_at']);
                                            $updatedTime->modify('+5 hours'); // Add 5 hours to the time
                                            echo $updatedTime->format('H:i:s'); // Display the time with GMT+5
                                            ?>
                                        </p>
                                    </th>
                                <?php 
                                    } 
                                ?>
                                <th style="width:10%; font-size: 12px !important;">&nbsp;&nbsp;&nbsp;UNIT(s)</th>
                                <th style="width:25%; font-size: 12px !important;">NORMAL RANGE</th>
                                
                            </tr>

                        </thead>
                    </table>
                    <!-- <h6 style="margin-left:10px !important; margin-top:7px; margin-bottom:3px;"><b><?php echo strtoupper($detail[0]["test_name"]); ?></b></h6> -->
                    
                    <table class="custom-tableprintablea4" style="width:100%; border-spacing: 10px; cellspacing=0 cellpadding=0" border="0">
                        <?php
                        $j = 0;
                        foreach ($reportDetails['paramterDetails'] as $value) {
                        ?>
                            <tr>
                                <td style="width:15%"><?php echo strtoupper($value["parameter_name"]); ?> <span class="dotLines" style="padding-right: 20px; float:right;"></span></td>
                                <?php foreach ($reportDetails['reports'] as $report) { 
                                    $found = false;
                                    foreach (array_reverse($reportDetails['reportsResult']) as $resultValue) {
                                        if ($resultValue["pathology_report_id"] == $report["report_id"] && $resultValue["parameter_id"] == $value["id"]) {
                                            $found = true;
                                            echo '<td style="width:12.5%">' . ($resultValue["pathology_report_value"] != "" ? $resultValue["pathology_report_value"] : "N/A") . '<span class="dotLines" style="padding-right: 20px; float:right;"></span></td>';
                                            break;
                                        }
                                    }
                                    if (!$found) {
                                        echo '<td style="width:12.5%">N/A<span class="dotLines" style="padding-right: 20px; float:right;"></span></td>';
                                    }
                                } ?>
                                <td style="width:10%"><?php echo $value["unit_name"]; ?></td>
                                <td style="width:25%"><?php echo $value["reference_range"]; ?></td>

                                

                            </tr>
                        <?php
                            $j++;
                        } 
                        ?>
                    </table>

                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                 <?php }?>
                 <?php if($detail[0]["show_description"]==1 or $detail[0]["show_description_only"]==1){?>
                    <table>
                  <!-- <tr>
                    <td><strong>Remarks : </strong></td>
                  </tr> -->
                  </table>
                  <?php $remarks_lines=count(explode("\n",$detail[0]["description"]));?>
                  <textarea name="description" id="pedit_description" rows="<?php echo $remarks_lines;?>" cols="50" readonly class="form-control" ><?php echo $detail[0]["description"]; ?></textarea>
                   <?php }?>

                    <p><?php
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
    
    function printData(id,parameter_id, patient_id) {

        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/pathology/getCommulativeReport/'  + id +'/'+parameter_id +'/'+patient_id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                // $("#testdata").html(result);
                popup(result);
            }
        });
    }

    function popup(data)
    {
        var base_url = '<?php echo base_url() ?>';
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({"position": "absolute", "top": "-1000000px"});
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);
        return true;
    }
</script>
