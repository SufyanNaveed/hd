<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }
    td {
        padding: 4px 8px;
    }
    .bold {
        font-weight: bold;
    }
    .header {
        font-size: 16px;
        font-weight: bold;
    }
    .right {
        text-align: right;
    }
</style>
<style type="text/css">
    @media print {
        .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
            float: left;
        }
        .col-sm-12 {
            width: 100%;
        }
        .col-sm-11 {
            width: 91.66666667%;
        }
        .col-sm-10 {
            width: 83.33333333%;
        }
        .col-sm-9 {
            width: 75%;
        }
        .col-sm-8 {
            width: 66.66666667%;
        }
        .col-sm-7 {
            width: 58.33333333%;
        }
        .col-sm-6 {
            width: 50%;
        }
        .col-sm-5 {
            width: 41.66666667%;
        }
        .col-sm-4 {
            width: 33.33333333%;
        }
        .col-sm-3 {
            width: 25%;
        }
        .col-sm-2 {
            width: 16.66666667%;
        }
        .col-sm-1 {
            width: 8.33333333%;
        }
        .col-sm-pull-12 {
            right: 100%;
        }
        .col-sm-pull-11 {
            right: 91.66666667%;
        }
        .col-sm-pull-10 {
            right: 83.33333333%;
        }
        .col-sm-pull-9 {
            right: 75%;
        }
        .col-sm-pull-8 {
            right: 66.66666667%;
        }
        .col-sm-pull-7 {
            right: 58.33333333%;
        }
        .col-sm-pull-6 {
            right: 50%;
        }
        .col-sm-pull-5 {
            right: 41.66666667%;
        }
        .col-sm-pull-4 {
            right: 33.33333333%;
        }
        .col-sm-pull-3 {
            right: 25%;
        }
        .col-sm-pull-2 {
            right: 16.66666667%;
        }
        .col-sm-pull-1 {
            right: 8.33333333%;
        }
        .col-sm-pull-0 {
            right: auto;
        }
        .col-sm-push-12 {
            left: 100%;
        }
        .col-sm-push-11 {
            left: 91.66666667%;
        }
        .col-sm-push-10 {
            left: 83.33333333%;
        }
        .col-sm-push-9 {
            left: 75%;
        }
        .col-sm-push-8 {
            left: 66.66666667%;
        }
        .col-sm-push-7 {
            left: 58.33333333%;
        }
        .col-sm-push-6 {
            left: 50%;
        }
        .col-sm-push-5 {
            left: 41.66666667%;
        }
        .col-sm-push-4 {
            left: 33.33333333%;
        }
        .col-sm-push-3 {
            left: 25%;
        }
        .col-sm-push-2 {
            left: 16.66666667%;
        }
        .col-sm-push-1 {
            left: 8.33333333%;
        }
        .col-sm-push-0 {
            left: auto;
        }
        .col-sm-offset-12 {
            margin-left: 100%;
        }
        .col-sm-offset-11 {
            margin-left: 91.66666667%;
        }
        .col-sm-offset-10 {
            margin-left: 83.33333333%;
        }
        .col-sm-offset-9 {
            margin-left: 75%;
        }
        .col-sm-offset-8 {
            margin-left: 66.66666667%;
        }
        .col-sm-offset-7 {
            margin-left: 58.33333333%;
        }
        .col-sm-offset-6 {
            margin-left: 50%;
        }
        .col-sm-offset-5 {
            margin-left: 41.66666667%;
        }
        .col-sm-offset-4 {
            margin-left: 33.33333333%;
        }
        .col-sm-offset-3 {
            margin-left: 25%;
        }
        .col-sm-offset-2 {
            margin-left: 16.66666667%;
        }
        .col-sm-offset-1 {
            margin-left: 8.33333333%;
        }
        .col-sm-offset-0 {
            margin-left: 0%;
        }
        .visible-xs {
            display: none !important;
        }
        .hidden-xs {
            display: block !important;
        }
        table.hidden-xs {
            display: table;
        }
        tr.hidden-xs {
            display: table-row !important;
        }
        th.hidden-xs,
        td.hidden-xs {
            display: table-cell !important;
        }
        .hidden-xs.hidden-print {
            display: none !important;
        }
        .hidden-sm {
            display: none !important;
        }
        .visible-sm {
            display: block !important;
        }
        table.visible-sm {
            display: table;
        }
        tr.visible-sm {
            display: table-row !important;
        }
        th.visible-sm,
        td.visible-sm {
            display: table-cell !important;
        }
        @page
        {
            size: auto;   /* auto is the initial value */
            margin-top: 2.5mm;  /* this affects the margin in the printer settings */
            margin-bottom: 0mm;  /* this affects the margin in the printer settings */
        }
    }

    .printablea4{width:100%;}
    .printablea4>tbody>tr>th,
    .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 11px;}

    body{
        border: 2px solid black;
        padding:10px;
    }
    /* tr.spaceUnder>td {
  padding-bottom: 1em;
} */
</style>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Prescription</title>
    </head>

    <div id="html-2-pdfwrapper" style=" position:relative font-family: 'Cambria', serif;">

        <div class="row">
            
            <!-- left column -->
            <div class="col-md-12">
            <?php if (!empty($print_details[0]['print_header'])) { ?>
                    <div class="pprinta4">
                        <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'];
                            }
                            ?>" style="height:100px; width:100%;" class="img-responsive">
                        <div style="height: 10px; clear: both;"></div>
                    </div>
                <?php } else{ ?>
                    <br> <br> <br> <br> <br> <br>             <?php } ?>
                
                <div class="">
                    <?php
                        $date = $result["appointment_date"];
                        $appointment_date = date("Y-m-d H:i:s A", strtotime($result["appointment_date"]));
                        ?>
                    <table width="100%" class="printablea4">
                        <tr>
                            <td><?php echo $this->lang->line(''); ?> </td> <td></td>
                            <td class="text-right"></td>

                        </tr>
                    </table>
                    <p style="text-align:right;"><strong> <?php echo isset($result["casualty"]) && $result["casualty"] =='Yes'  ? " (Counter 1)" : "(Counter 2)" ;?></strong></p>
                    <hr style="height: 0px; border-top: 1px solid black; margin-bottom: 10px; margin-top: 10px" />
                    <div style="display:flex;justify-content:space-between">
                    <table border="0" width="100%">
    <tr>
        <td class="bold">OPD No:</td>
        <td><?php echo $result["opd_no"] ?? "N/A"; ?></td>
        <td class="bold right">MR #:</td>
        <td><?php echo $result["patient_unique_id"] ?? "N/A"; ?></td>
    </tr>
    <tr>
        <td class="bold">Patient:</td>
        <td colspan="3"><?php echo strtoupper($result["patient_name"] ?? "N/A"); ?></td>
    </tr>
    <tr>
        <td class="bold">Date:</td>
        <td><?php echo date("d-m-Y h:i A", strtotime($result["appointment_date"] ?? "now")); ?></td>
        <td class="bold right">Gender:</td>
        <td><?php echo ucfirst($result["gender"] ?? "N/A"); ?></td>
    </tr>
    <tr>
        <td class="bold">Age:</td>
        <td>
            <?php
            echo (!empty($result['age']) ? $result['age'] . " Years " : "");
            ?>
        </td>
    </tr>
    <tr>
        <td class="bold">CNIC:</td>
        <td><?php echo $result["patient_cnic"] ?? "N/A"; ?></td>
    </tr>
    <tr>
        <td class="bold">Diagnostic:</td>
        <td colspan="3">_________________________</td>
    </tr>
</table>
                  
                    </div>
                    <hr style="height: 1px; border-top: 1px solid black; clear: both;margin-bottom: 10px; margin-top: 10px " />
                    <div style="display: flex; flex-direction: row; ">
                  
                   <table align="left" border="0" class="printablea4" style="border-right: 1px solid black; width:100%;margin-top:10%;margin-right:20px">
    <?php 
    $categories = isset($print_details[0]['categories']) ? $print_details[0]['categories']: null;

    // Decode JSON properly
    $categories = json_decode($categories, true); 
    if (is_string($categories)) {
        $categories = json_decode($categories, true); 
    }

    // Check if categories exist
    if (!empty($categories) && is_array($categories)) {
        foreach ($categories as $category) {
            $category_name = isset($category['value']) ? $category['value'] : 'Unknown';
            echo '<tr>';
            echo '<td style="text-align:left; padding:10px; border:1px solid black;">' . htmlspecialchars($category_name) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td style="text-align:left;">No Categories Available</td></tr>';
    }
    ?>
</table>

                            <div style="display:flex; flex-direction: column;  width:100%; margin-right:448px;">
                            <table align="left" border = 0 class="printablea4" style="margin-left:2px;">
                                <tr> <td style="font-size:25px;margin-left: -16px;"> Rx </td></tr>

                            </table>

                        </div>

                    </div>

                    <!-- <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px" /> -->
                </div>
            </div>
            <!--/.col (left) -->


        </div>
    </div>
    <p style="position: absolute;  bottom: 0; right: 0; border-top: 1px solid black; padding-top:0px;width: 150px;text-align:center; "><b>Signature & Stamp<b></p>
    <br>
    <p style="position: absolute; bottom: 5px; left: 0; width: 100%; text-align: center;">براہ کرم دوبارہ چیک اپ کے لیے یہ پرچی اپنے ساتھ لائیں - شکریہ</p>
    <br>

    <!-- <p style=" position: absolute; bottom: 0; left: 0; width: 100%; text-align: center;"><?php //echo $result["header_note"] ?></p> -->
    <p style="position: absolute; bottom: 0; left: 0; width: 100%; text-align: left;"><?php echo date('d-M-Y h:i A',strtotime($result["appointment_date"]))?></p><br>
</html>
<div id="payslipview"  class="modal fade" role="dialog">

    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details'); ?>   <span id="print"></span></h4>
            </div>
            <div class="modal-body" id="testdata">

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">



    function delete_prescription(id, opdid,visitid='') {
        console.log(id);
        if (confirm('Are you sure')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/prescription/deletePrescription/' + id + '/' + opdid+'/'+visitid,
                success: function (res) {
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });

        }
    }
</script>

