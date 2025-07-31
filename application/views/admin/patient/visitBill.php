<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">

    .printablea4{width: 100%;}
    .printablea4>tbody>tr>th,
    .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}
</style>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
    </head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                    <div class="pprinta4">
                        <?php if (!empty($print_details[0]['print_header'])) { ?>
                            <img style="height:100px" class="img-responsive" src="<?php echo base_url() . $print_details[0]["print_header"] ?>">
                        <?php } ?>
                        <div style="height: 10px; clear: both;"></div>
                    </div>
                    <table width="100%" class="printablea4">
                        <tr>
                            <td align="text-left"><h5><?php echo $this->lang->line('bill') . " #" ?><?php echo $result["opdid"] ?></h5></td>
                            <td align="right"><h5><?php echo $this->lang->line('date') . " : " ?><?php
                                    if (!empty($result['appointment_date'])) {
                                        echo date($this->customlib->getSchoolDateFormat(true, true), strtotime($result['appointment_date']));
                                    }
                                    ?></h5></td>
                        </tr>
                    </table>
                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                    <table class="custom-tableprintablea4" cellspacing="0" width="100%">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('name'); ?></th>
                            <td width="25%"><?php echo $result["patient_name"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('doctor'); ?></th>
                            <td width="25%"><?php echo $result["name"] . " " . $result["surname"]; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('opd') . " " . $this->lang->line('no'); ?></th>
                            <td><?php echo $result['opd_no']; ?></td>
                            <th><?php echo $this->lang->line('organisation'); ?></th>
                            <td align=""><?php echo $result['organisation_name']; ?></td>
                        </tr>
                        <tr>
                           <!-- th><?php echo $this->lang->line('admission') . " " . $this->lang->line('date'); ?></th>
                           <td><?php echo date($this->customlib->getSchoolDateFormat(true, true), strtotime($result['date'])); ?></td>  -->
                            <?php if (!empty($result['discharge_date'])) { ?>
                                  <!--  <th><?php echo $this->lang->line('discharged') . " " . $this->lang->line('date'); ?></th>
                                   <td align="right"><?php echo date($this->customlib->getSchoolDateFormat(), strtotime($result['discharge_date'])); ?></td>  -->
                            <?php } ?>
                        </tr>
                    </table>
                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">


                    <table class="custom-table table" width="100%" style="font-size: 12px;">
                        <tr style="line-height: 14px;">
                            <th width="25%"><?php echo $this->lang->line('guardian_name') ?></th>
                            <td width="25%"><?php echo $result['guardian_name'] ?></td>

                            <th width="25%">Patient CNIC</th>
                            <td width="25%"><?php echo $result['patient_cnic'] ?></td>
                        </tr>
                        <tr style="line-height: 14px;">
                            <th width="25%">Martial Status</th>
                            <td width="25%"><?php echo $result['marital_status'] ?></td>
                            <th width="25%"><?php echo $this->lang->line('gender') ?></th>
                            <td width="25%"><?php echo $result['gender'] ?></td>


                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('email') ?></th>
                            <td width="25%"><?php echo $result['email'] ?></td>
                            <th width="25%"><?php echo $this->lang->line('phone') ?></th>
                            <td width="25%"><?php echo $result["mobileno"] ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('age') ?></th>
                            <td width="25%"><?php
                                if (!empty($result["age"])) {
                                    echo $result["age"] . " " . $this->lang->line("year") . " ";
                                }if (!empty($result["month"])) {
                                    echo $result["month"] . " " . $this->lang->line("month");
                                }
                                ?> </td>
                            <th width="25%"><?php echo $this->lang->line('blood_group') ?></th>
                            <td width="25%"><?php echo $result["blood_group"] ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('height') ?></th>
                            <td width="25%"><?php echo $result['height'] ?></td>

                            <th width="25%"><?php echo $this->lang->line('weight') ?></th>
                            <td width="25%"><?php echo $result['weight'] ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('bp') ?></th>
                            <td width="25%"><?php echo $result['bp'] ?></td>
                            <th width="25%"><?php echo $this->lang->line('pulse') ?></th>
                            <td width="25%"><?php echo $result["pulse"] ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('temperature') ?></th>
                            <td width="25%"><?php echo $result['temperature'] ?></td>

                            <th width="25%"><?php echo $this->lang->line('respiration') ?></th>
                            <td width="25%"><?php echo $result['respiration'] ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('known_allergies') ?></th>
                            <td width="25%"><?php echo $result['opdknown_allergies'] ?></td>
                            <th width="25%"><?php echo $this->lang->line('appointment') ?></th>
                            <td width="25%"><?php echo $result["appointment_date"] ?></td>
                        </tr>

                        <tr>
                            <th width="25%"><?php echo $this->lang->line('case') ?></th>
                            <td width="25%"><?php echo $result['case_type'] ?></td>

                            <th width="25%"><?php echo $this->lang->line('casualty') ?></th>
                            <td width="25%"><?php echo $result['casualty'] ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('symptoms') ?></th>
                            <td width="25%"><?php echo $result['symptoms'] ?></td>
                            <th width="25%"><?php echo $this->lang->line('note') ?></th>
                            <td width="25%"><?php echo $result["note_remark"] ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('address') ?></th>
                            <td width="25%"><?php echo $result['address'] ?></td>
                        </tr>
                    </table>


                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                    <table class="custom-tableprintablea4" width="100%">

                        <tr>
                          <th width="25%" class=""><?php echo "Discount "; ?></th>
                          <th width="25%" class=""><?php echo $result["opd_discount"] ; ?></th>
                            <th width="25%"><?php echo $this->lang->line('paid') . " " . $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>

                            <td width="25%"><?php echo isset($result["opd_discount"]) && $result["opd_discount"] > 0 ? $result["apply_charge"] - $result["opd_discount"] : $result["apply_charge"] ?></td>

                        </tr>
                    </table>
                    <hr style="height: 1px; clear: both;margin-bottom: 10px; margin-top: 10px">
                    <?php if (!empty($print_details[0]['print_footer'])) { ?>
                        <p class="ptt10"><?php echo $print_details[0]['print_footer']; ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</html>