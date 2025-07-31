<!DOCTYPE html>
<html>
  <head>
  <style>
        body {
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Use fixed table layout */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        th, td {
            text-align: center;
            font-size: 12px; /* Adjusted font size */
            border: 1px solid #ddd;
            padding: 4px; /* Adjusted padding */
            word-wrap: break-word; /* Ensure content wraps */
        }

        th {
            background-color: #3498db;
            color: #fff;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #ecf0f1;
        }

        .caption-row, .extra-filter {
            background-color: #3498db;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            padding: 4px; /* Adjusted padding */
        }

        hr {
            border: 1px solid #ddd;
        }

        .page-number {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
        }
    </style>
  </head>
  <body>
  <?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
    <div class="content">
        <div class="caption-row"> <?= $this->setting_model->getCurrentHospitalName() ?> </div>
        <div class="caption-row"> <?php
			$opdReport="Income Summary";


			?> <?php echo $opdReport ;?> </div> <?php if(empty($date_data['to_date']) && empty($date_data['from_date'])) { ?> <div class="caption-row"> All Record </div> <?php }elseif($search_type=='all_time') { ?> <div class="caption-row"> All Record </div> <?php } elseif(isset($date_data['to_date']) && !empty($date_data['to_date'])) {

                        $from_date = new DateTime($date_data['from_date']);
                        $to_date = new DateTime($date_data['to_date']);

                        // Calculate the difference
                        $diff = $to_date->diff($from_date);

                        // Get the difference in days as a floating-point number
                        $diff_in_days = $diff->days + ($diff->h / 24) + ($diff->i / 60 / 24) + ($diff->s / 60 / 60 / 24);
                        $diff_in_days = ceil($diff->days + 1);
                        if($diff_in_days==1){$diff_in_days=$from_date->format('l');}

                    ?> <div class="caption-row"> <?php echo "From: " . $from_date->format('d-M-Y') . " To: " . $to_date->format('d-M-Y') . " Days: " . $diff_in_days;?> </div> <?php } else{?> <div class="caption-row"> From : <?php
                            // Convert the date string to a DateTime object
                            $date = new DateTime($date_data['from_date']);
                            // Get the day of the week as a string (e.g., "Monday", "Tuesday", etc.)
                            $day_of_week = $date->format('l');
                            echo date('d-M-Y',strtotime($date_data['from_date'])).' : Day : '.$day_of_week ;

                            ?> </div> <?php } ?> <div class="extra-filter"> <?php
                            if(isset($choose_head) && $choose_head=='all'){
                                echo " Filter : All ";
                            }
							if(isset($choose_head) && $choose_head!=='all'){
                                //$kpo_names = array_column($choose_head, 'kpo_name');
                                echo " Filter : " .implode(', ', $choose_head);
                            }

                            ;?> </div>
                                    <hr>
                                    <hr>
                                <table>

                                    <thead>
                                    <tr>
                                    <th style="width:5%">S/N</th>
                                    <th style="width:10%">DATE TIME</th>
                                    <th style="width:5%">MRN</th>
                                    <!-- <th style="width:5%">VISIT</th> -->
                                    <th style="width:10%">INVOICE NO</th>
                                    <th style="width:10%">DEPARTMENT</th>
                                    <th style="width:15%">PATIENT NAME</th>
                                    <th style="width:10%">REF.BY</th>
                                    <!-- <th style="width:10%">District</th> -->
                                    <th style="width:10%">Test Name</th>
                                    <th style="width:15%">TPA</th>
                                    <th style="width:15%">AMOUNT</th>
                                    <th style="width:10%">Hospital Share</th>
                                    <th style="width:10%">Doctor Share</th>
                                    <th style="width:10%">Staff Share</th>
                                    <th style="width:10%">Discount</th>
                                    <th style="width:10%">KPO Name</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

$serialNumber = 1;
    $total      = 0;
    $total_hshare      = 0;
    $total_dshare      = 0;
    $total_staffshare      = 0;
    $class      = "";
    $refference = "";
    $prefix     = "";
    foreach ($resultlist as $key1 => $result2) {
        $ji         = 1;
        foreach ($result2 as $key4 => $result) {
            $t_amt=$result["amount"] - $result["discount"];
            $hospital_share=$result["amount"] - ($result["doctor_share"]+$result["staff_share"]);
            $total_dshare+=$result["doctor_share"];
            $total_staffshare+=$result["staff_share"];
            $total_hshare +=$hospital_share;
            $total +=$t_amt;
            $surname = "";
            $patient_id = "";
            if (isset($result["patient_unique_id"])) {
                $patient_id = " (" . $result["patient_unique_id"] . ")";
            }
            if (isset($result["reff"])) {
                $refference = $result["reff"];
            }?>
<tr>
                                                                <td><?php echo $serialNumber ?></td>
                                                                <td><?php echo date('d-m-Y H:i:s', strtotime($result['created_at'])); ?></td>
                                                                <td><?php echo $result["mrno"]  ?></td>
                                                                <!-- <td><?php echo "1"  ?></td> -->
                                                                <td><?php echo $refference ?></td>
                                                                <td style="text-transform:capitalize;"><?php echo $key1 ?></td>
                                                                <td><?php echo $result["patient_name"] . " " . $patient_id ?></td>
                                                                <td class="text-right <?php echo $class ?>"><?php echo $result["name"].' '.$result["surname"] ?></td>
                                                                <!-- <td class="text-right <?php echo $class ?>"><?php echo $result['address']?></td> -->
                                                                <td class="text-right <?php echo $class ?>"><?php echo $result['department_name']?></td>
                                                                <td class="text-right <?php echo $class ?>"><?php echo $result['organisation_name']?></td>
                                                                <td class="text-right <?php echo $class ?>"><?php echo $prefix . number_format($t_amt) ?></td>
                                                                <td class="text-right <?php echo $class ?>"><?php echo $hospital_share ?></td>
                                                                <td class="text-right <?php echo $class ?>"><?php echo $result["doctor_share"] ?></td>
                                                                <td class="text-right <?php echo $class ?>"><?php echo $result["staff_share"] ?></td>
                                                                <td class="text-right <?php echo $class ?>"><?php echo number_format($result["discount"]) ?></td>
                                                                <td class="text-right <?php echo $class ?>"><?php echo $result["kpo_name"] ?></td>
                                                            </tr>
                                                            <?php  $serialNumber++; }}  ?>

                                    <tr class="box box-solid total-bg">
												<td ></td>
												<td ></td>
												<td ></td>
												<td ></td>
                                                <td ></td>
                                                <td ></td>
                                                <td ></td>
                                                <td ></td>
                                                <td ></td>
                                                <td ></td>
                                                <td ></td>
                                                <td class="text-right"><?php echo  $currency_symbol . $total; ?></td>
                                                <td class="text-right"><?php echo  $currency_symbol . $total_hshare; ?></td>
                                                <td class="text-right"><?php echo  $currency_symbol . $total_dshare; ?></td>
                                                <td class="text-right"><?php echo  $currency_symbol . $total_staffshare; ?></td>
                                                <td ></td>
                                                <td ></td>

                                            </tr>                                </tbody>
                            </table>
    </div>
  </body>
</html>