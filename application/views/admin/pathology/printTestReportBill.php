<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
//echo "<pre>";print_r($data);exit;
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <style type="text/css">
            .printablea4{width: 100%;}
            /*.printablea4 p{margin-bottom: 0;}*/
            .printablea4>tbody>tr>th,
            .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 14px;}
            @media print {
                #testreport_check  {
                    background-color: #80808038 !important;
                    -webkit-print-color-adjust: exact; 
                }
            }

            @media print {
                #testreport_check {
                    
                    background-color: #80808038 !important;
                }
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
                    <br> <br> <br> <br> <br> <br>             <?php } ?>
                    <?php if($print=='yes'){?>
                    <table width="100%" class="printablea4" border="0">
                        <tr>
                            <td align="text-left"><?php echo  "Bill# "; ?><img src="<?= base_url().$barcode ?>">
                            </td>
                            <td align="center"><?php echo  "Patient Login# <br>"; ?><?php echo 'username : '.$result['username'].'<br> password : '.$result['password']?>
                            </td>
                            <td align="right"><?php echo  "Track Online# "; ?><img src="<?= $qr_code ?>">
                            </td>
                        </tr>
                    </table>
                    <?php }?>
                    <table width="100%" class="printablea4" border="0">
                        <tr>
                            <td align="text-left"><?php echo "Pateint Name :" ?><?php echo ucfirst($result["patient_name"]) ; ?>
                            </td>
                            
                            <td align="right"><?php echo "MR LAB # :" ?><?php echo $result["patient_unique_id"] .'-'. date('m', strtotime($result['patient_reg'])).'/'. date('Y', strtotime($result['patient_reg'])); ?>
                            </td>
                            
                        </tr>
                        <tr>
                            <td align="text-left"><?php echo "Phone No :" ?><?php echo $result["mobileno"]; ?>
                            </td>
                            <td align="right"><?php echo "Case # :" ?><?php echo $result["bill_no"] .'-'. date('m', strtotime($result['reporting_date'])).'/'. date('Y', strtotime($result['reporting_date'])); ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="text-left"><?php echo "Consultant :" ?><?php echo $result["doctorsurname"]; ?>
                            </td>
                            <td align="right"><?php echo $this->lang->line('date') . " : "; ?><?php echo date($this->customlib->getSchoolDateFormat(true, false), strtotime($result['reporting_date'])) ?>
                            </td>
                           
                        </tr>
                    </table>
                   
                    <?php foreach($details as $key=>$dt){
                        //echo "innner <pre>";print_r($dt);exit;
                        ?>
                        <table class="custom-tableprintablea4" id="testreport_check" width="100%" style="border: 1px solid;background-color: #80808038;">
                            <tr>
                                
                                <th style="text-align:left;"><?php echo $dt['test_name']; ?></th>
                            </tr>
                        

                        </table> 
                        <table class="custom-tableprintablea4" id="testreport" width="100%">
                            <tr>
                                <th style="text-align: left; width:30%;"><?php echo $this->lang->line('parameter') . " " . $this->lang->line('name'); ?></th> 
                                <th style="text-align: left; width:40%;"><?php echo $this->lang->line('reference') . " " . $this->lang->line('range'); ?></th>
                                <th style="text-align: left; width:20%;"><?php echo $this->lang->line('value'); ?></th>
                                <th style="text-align: left; width:10%;"><?php echo $this->lang->line('unit'); ?></th>
                            
                            </tr>
                            <?php
                            $j = 0;
                            foreach ($dt['test_report'] as $value) {
                                ?>
                                <tr>
                                    <td width=""><?php echo $value["parameter_name"]; ?></td>
                                    <td><?php echo $value["reference_range"]; ?></td>
                                    <td><?php echo $value["pathology_report_value"]; ?></td>
                                    <td><?php echo $value["unit_name"]; ?></td>
                                </tr>
                                <?php
                                $j++;
                            }
                            ?>

                        </table>
                        
                    <?php }?> 
                   
                    <table class="custom-tableprintablea4" width="100%" style="width:30%; float: right;">
                     
                            <tr id="generated_by">
                                <th><?php echo $this->lang->line('prepared_by'); ?></th>
                            </tr>
                            <tr>
                                <td><?php echo $result["generated_byname"]; ?></td>
                            </tr>
                     
                    </table>
                   
                
                    
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
    

    
    window.print();
    window.onfocus=function(){ window.close();}
</script>