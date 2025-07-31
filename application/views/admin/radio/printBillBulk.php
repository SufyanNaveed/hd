<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $this->customlib->getAppName(); ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="theme-color" content="#5190fd" />
        <link href="<?php echo base_url(); ?>backend/images/s-favican.png" rel="shortcut icon" type="image/x-icon">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/jquery.mCustomScrollbar.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/custom_style.css">
    </head>
  <style>
        b {
         border-bottom: 0.5px solid black;

        }
    </style>

    		<table border="0">
              <tr><td><b style="margin-top:0" class="text-center"><?= $this->setting_model->getCurrentHospitalName() ?></b></td></tr>
                <tr><td><strong><?= $settinglist[0]['address'] ?></td></strong></tr>
                <tr><td><b style="margin-top:0" class="text-center"><strong><?= $settinglist[0]['phone'] ?></b></td></strong></tr>

            </table>
<br>
            <table border="0">
                <tr><td><strong><?= $this->lang->line('patient') . ": " . $detail[0]['patient_name'] ?></td></strong></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><?= " Age : " .$detail[0]['age'].' Year '.$detail[0]['month'].' Month ' ?> - <?= $this->lang->line('gender') . " : ". $detail[0]['gender'] ?></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><?= $this->lang->line('blood') . " " .$this->lang->line('group') . ": "  . $detail[0]['blood_group'] ?></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><?= $this->lang->line('address') . ": " . $detail[0]['address'] ?></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><?= $this->lang->line('phone') . ": ". $detail[0]['mobileno'] ?></td></tr>
                <tr class="table table-bordered" style='font-family:"Courier New", Courier, monospace; font-size:80%'><td><?= 'MR # :' . ": ". $detail[0]["patient_unique_id"] ; ?></td></tr>

            </table>

                <h6 class="m-b-5 m-t-10 bg-dark strip text-center">Test(s) Description</h6>
                <table style='font-family:"Courier New", Courier, monospace; font-size:100%' style="table-layout:fixed" cellspacing=0 cellpadding=0 width="25%" border="1" >
                    <thead>
                        <th style="width:10%"><?php echo $this->lang->line('bill'). " # "; ?></th>
                        <!-- <th style="width:20%"><?php echo $this->lang->line('reporting')." ".$this->lang->line('date'); ?></th> -->
                        <th style="width:40%" ><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th>
                        <th style="width:20%"><?php echo $this->lang->line('doctor'); ?></th>
                        <th style="width:5%"><?php echo 'Report Days'; ?></th>
                        <!-- <th style="width:5%"><?php echo 'Discount'; ?></th> -->
                        <th style="width:20%" ><?php echo $this->lang->line('price'); ?></th>
                    </thead>
                    <tbody>

                    <?php if(is_array($detail) && count($detail) > 0) {
                        $totalAmt = 0;
                        foreach($detail as $bill):
                            $totalAmt+=isset($bill["radio_discount"]) && $bill["radio_discount"] > 0 ? $bill["apply_charge"] - $bill["radio_discount"] : $bill["apply_charge"] ;
                            $amt=isset($bill["radio_discount"]) && $bill["radio_discount"] > 0 ? $bill["apply_charge"] - $bill["radio_discount"] : $bill["apply_charge"] ;
                        ; ?>
                        <tr>
                            <td><?php echo $bill["bill_no"]; ?></td>
                            <!-- <td><?php echo date('d-M-Y',strtotime($bill["reporting_date"])); ?></td> -->
                            <td style="word-wrap: break-word;"><strong><?= $bill['test_name'] ?></strong> </td>
                            <td><?php echo $bill["doctorname"]." ".$bill["doctorsurname"];?></td>
                            <td class="text-right"><?php echo $bill["report_days"]; ?></td>
                            <!-- <td><?php echo $bill["radio_discount"]; ?></td> -->
                            <td class="text-right" ><strong><?= number_format($amt) ?></strong></td>
                        </tr>
                    <?php endforeach; } else { ?>
                        <tr><th class="text-right" colspan="5">No Record Found</th></tr>
                    <?php } ?>
                        <tr >

                        <td></td><td></td><td></td><td><strong>Total </strong> </td>

                            <th> <strong> <?= number_format($totalAmt) ?> <?php echo $currency_symbol?></strong></th>
                        </tr>
                    </tbody>
                </table>

            <h6 class="font-bold m-t-10"> THANKS YOU FOR PARTICIPATION </h6>
            <p class="m-b-0">
                This is a computer generated receipt - <?= 'printed at '.date('d F Y h:i:s A') ?>.

            </p>
        </code>
    </body>
</html>
<script type="text/javascript">
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_conform') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/radio/deletePharmacyBill/' + id,
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
    function printData(id,radiology_id) {

        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/radio/getBillDetails/' + id +'/'+radiology_id,
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