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
                    <?php if (!empty($print_details[0]['print_header'])) {
    ?>
                                <div class="pprinta4">
                                    <img src="<?php
if (!empty($print_details[0]['print_header'])) {
        echo base_url() . $print_details[0]['print_header'];
    }
    ?>" class="img-responsive" style="height:100px; width: 100%;">
                                </div>
                    <?php }?>
                    <table width="100%" class="printablea4">
                        <tr>
                            <td align="text-left"><h5><?php echo $this->lang->line('report') . " #"; ?><?php echo $result["bill_no"] ?></h5>
                            </td>
                            <td align="right"><h5><?php echo $this->lang->line('date') . " : "; ?><?php echo date($this->customlib->getSchoolDateFormat(true, false), strtotime($result['reporting_date'])) ?></h5>
                            </td>
                        </tr>
                    </table>
                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                    <table class="custom-tableprintablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th width=""><?php echo $this->lang->line('name'); ?></th>
                            <th width=""><?php echo $this->lang->line('doctor'); ?></th>
                            <th width=""><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('radiology') . " " . $this->lang->line('report'); ?></th>
                        </tr>
                         <tr>
                            <td width=""><?php echo $result["patient_name"]; ?></td>
                            <td width="" align="left"><?php echo $result["doctorname"] . " " . $result["doctorsurname"]; ?></td>
                             <?php
$j = 0;
foreach ($detail as $bill) {
    ?>

                            <td width=""><?php echo $bill["test_name"]; ?></td>
                            <td><?php echo $bill["short_name"]; ?></td>
                            <td><?php if (!empty($bill["radiology_report"])) {?>
                                        <a <?php if ($print != 'yes') {?> href="<?php echo base_url(); ?>patient/dashboard/radio_download/<?php echo $bill["radiology_report"]; ?>"<?php }?> class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>">
                                            <i class="fa fa-download"></i>
                                        </a>
                                <?php }?>
                            </td>
                        <?php
$j++;
}
?>
                        </tr>

                    </table>
                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                    <table class="custom-tableprintablea4" id="testreport" width="100%">
                        <tr>
                            <th><?php echo $this->lang->line('description'); ?></th>
                        </tr>
                        <?php
$i = 0;
foreach ($detail as $bill) {
    ?>
                            <tr>
                                <td><?php echo $bill['description']; ?></td>
                            </tr>
                        <?php
$i++;
}
?>
                    </table>
                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                      <table class="custom-table table table-striped table-bordered table-hover" id="tableID">
                                            <thead>
                                                <tr style="font-size: 13px">
                                                    <th><?php echo $this->lang->line('test') . " " . $this->lang->line('parameter') . " " . $this->lang->line('name'); ?></th>
                                                    <th><?php echo $this->lang->line('refference') . " " . $this->lang->line('range'); ?></th>
                                                     <th><?php echo $this->lang->line('value'); ?></th>
                                                    <th><?php echo $this->lang->line('unit'); ?></th>
                                                </tr>
                                            </thead>
                                            <?php

foreach ($parameterdetails as $value) {

    ?>
                                            <tr id="row0">
                                                <td align="left"><?php echo $value["parameter_name"]; ?></td>
                                                <td align="left"><?php echo $value["reference_range"]; ?></td>
                                                <td align="left"><?php echo $value["radiology_report_value"]; ?></td>
                                                <td align="left"><?php echo $value["unit_name"]; ?></td>
                                            </tr>
                                            <?php }?>
                                        </table>
                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </div>
</html>
<script type="text/javascript">
    function printData(id,radiology_id) {

        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/dashboard/getReportDetailsRadio/' + id +'/'+ radiology_id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
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