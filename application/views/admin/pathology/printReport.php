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
            .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}
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
                    <br>
                    <table class="custom-tableprintablea4" style="border: 2px solid #999;padding: 7px !important;border-collapse: separate; border-spacing: 10px;" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th width=""><?php echo $this->lang->line('report') ."#"; ?></th>
                            <th width=""><?php echo "MR LAB #" ; ?></th>
                            <th width=""><?php echo $this->lang->line('name'); ?></th>
                            <th width=""><?php echo $this->lang->line('gender').'/'.$this->lang->line('age'); ?></th>
                            <th width=""><?php echo $this->lang->line('doctor'); ?></th>
                            <th width=""><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th>
                            <th width=""><?php echo $this->lang->line('date') ; ?></th>
                            <!--<th><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('pathology') . " " . $this->lang->line('report'); ?></th> -->
                        </tr>
                        <tr>
                           <td width=""><?php echo $result["bill_no"]; ?></td>
                           <td width=""><?php echo $result["patient_unique_id"] .'-'. date('m', strtotime($result['patient_reg'])).'/'. date('Y', strtotime($result['patient_reg'])); ?></td>
                           <td width=""><?php echo $result["patient_name"]; ?></td>
                           <td width=""><?php echo $result["gender"].'/'.$result["age"]; ?></td>
                           <td width="" align="left"><?php echo $result["doctorname"]." ".$result["doctorsurname"]; ?></td>

                        <?php
                        $j = 0;
                        foreach ($detail as $bill) {
                            ?>

                                <td width=""><?php echo $bill["test_name"]; ?></td>
                                <!--<td><?php echo $bill["short_name"]; ?></td> -->


                            <?php
                            $j++;
                        }
                        ?>
                        <td width=""><?php echo date($this->customlib->getSchoolDateFormat(true, false), strtotime($result['reporting_date'])); ?></td>
                        </tr>
                    </table>
                    <?php if($detail[0]["show_description_only"]==0){?>
                    <hr style="height: 1px; clear: both;margin-bottom: 5px; margin-top: 5px">

                     <table class="custom-tableprintablea4" style="border: 1px solid #999;padding: 3px !important;border-collapse: separate; border-spacing: 10px;" id="testreport" width="100%">
                        <thead>
                            <tr>
                                <th width=""><?php echo $this->lang->line('parameter') . " " . $this->lang->line('name'); ?></th>
                                <th><?php echo "Current ".$this->lang->line('result'); ?></th>
                                <th><?php echo $this->lang->line('unit'); ?></th>
                                <th><?php echo $this->lang->line('reference') . " " . $this->lang->line('range'); ?></th>

                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $j = 0;
                        foreach ($parameterdetails as $value) {
                            $number_lines=count(explode("\n",$value["reference_range"]));

                            ?>
                        <tr>
                            <td style="width:30%"><b><?php echo strtoupper($value["parameter_name"]); ?></b> <span class="dotLines" style="padding-right: 20px; float:right;"></span></td>
                            <td style="width:20%"><?php echo isset($value["pathology_report_value"]) && !empty($value["pathology_report_value"]) ? $value["pathology_report_value"]: "N/A"; ?> <span class="dotLines" style="padding-right: 20px; float:right;"></span></td>
                            <td style="width:10%"><?php echo $value["unit_name"]; ?></td>
                            <td style="width:40%">

                                <textarea style="border: none; outline: none; overflow:hidden; white-space: nowrap;" name="reference_range" id="reference_range" rows="<?php echo $number_lines;?>" cols="10"  class="form-control reference_range" ><?php echo $value["reference_range"]; ?></textarea></td>
                            </tr>
                            <?php
                            $j++;
                        }
                        ?>
                        </tbody>
                    </table>

                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                    <?php }?>
                 <?php if($detail[0]["show_description"]==1 or $detail[0]["show_description_only"]==1){?>
                    <table>
                  <tr>
                    <td><strong>Remarks : </strong></td>
                  </tr>
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
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_conform') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/deletePharmacyBill/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }
    function printData(id,parameter_id) {

        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/pathology/getReportDetails/'  + id +'/'+parameter_id,
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