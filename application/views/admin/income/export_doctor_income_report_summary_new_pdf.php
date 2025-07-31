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

        .content {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .caption-row,
        .extra-filter {
            background-color: #3498db;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
        }

        .report-item {
            margin: 10px 0;
            font-size: 16px;
            line-height: 1.5;
        }

        .report-item span.label {
            font-weight: bold;
            margin-right: 10px;
        }

        hr {
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <?php
    $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
    ?>
    <div class="content">
        <div class="caption-row"> <?= $this->setting_model->getCurrentHospitalName() ?> </div>
        <div class="caption-row"> 
            <?php
            $opdReport = "Revenue Statement";
            ?> 
            <?php echo $opdReport; ?> 
        </div>

        <!-- Display search filter -->
        <?php
            // Display date filter based on search_type
            if ($search_type == 'all_time') {
                echo "<div class='extra-filter'>All Records</div>";
            } else {
                $searchLabels = [
                    'today' => 'Today' . ($search_type == 'today' ? ' (' . date('Y-m-d') . ')' : ''),
                    'this_week' => 'This Week',
                    'last_week' => 'Last Week',
                    'this_month' => 'This Month',
                    'last_month' => 'Last Month',
                    'last_3_month' => 'Last 3 Months',
                    'last_6_month' => 'Last 6 Months',
                    'last_12_month' => 'Last 12 Months',
                    'this_year' => 'This Year',
                    'last_year' => 'Last Year',
                    'period' => 'From ' . $from_date . ' to ' . $to_date,
                ];
                $searchLabel = isset($searchLabels[$search_type]) ? $searchLabels[$search_type] : '';
                echo "<div class='extra-filter'>$searchLabel</div>";
            }
        ?>
    </div>
    <hr>
    <hr>

    <!-- Revenue Data -->
    <div class="content">
        <?php
            // Initialize totals array
            $totalAmounts = [
                'total_patients' => 0,
                'total_tpa_amount' => 0,
                'total_amount' => 0,
                'net_amount' => 0,
                'total_refund' => 0,
                'discount' => 0,
                'hospital_share' => 0,
                'doctor_share' => 0,
                'staff_share' => 0,
            ];

            // Initialize an array to track unique patients
            $patients = [];

            // Loop through the results and calculate overall totals
            foreach ($resultlist as $key1 => $result2) {
                foreach ($result2 as $key4 => $result) {
                    $totalAmounts['total_amount'] += $result['amount'] - $result['discount'];

                    // Check and calculate refund
                    if ($result['status'] == 'refund') {
                        $total_refund = $result['amount'] - $result['discount'];
                        $totalAmounts['total_refund'] += $total_refund;
                    } else {
                        // Calculate doctor share
                        $totalAmounts['doctor_share'] += $result['doctor_share'];
                        $totalAmounts['hospital_share'] += $result['hospital_share'];
                        $totalAmounts['staff_share'] += $result['staff_share'];
                    }

                    // Calculate discount
                    $totalAmounts['discount'] += $result['discount'];

                    // Calculate net amount, subtracting doctor share along with discount and refund
                    $net_amount = $result['amount'] - $result['discount'] - ($result['status'] == 'refund' ? $total_refund : 0)
                                 - $result['doctor_share'];  // Only subtract doctor share
                    $totalAmounts['net_amount'] += $net_amount;

                    // Track unique patients based on `patient_id`
                    if (!in_array($result['patient_id'], $patients)) {
                        $patients[] = $result['patient_id'];
                    }
                }
            }

            // Function to print totals
            function printTotals($total, $patients) {
                echo "<div class='report-item'><span class='label'>Total Patients:</span>" . count($patients) . "</div>";

                echo "<div class='report-item'><span class='label'>Total Revenue:</span>" . number_format($total['total_amount']) . "</div>";
                echo "<div class='report-item'><span class='label'>Refund Amount:</span>" . number_format($total['total_refund']) . "</div>";
                echo "<div class='report-item'><span class='label'>Discount Amount:</span>" . number_format($total['discount']) . "</div>";
                echo "<div class='report-item'><span class='label'>Total Doctor Share:</span>" . number_format($total['doctor_share']) . "</div>";
                echo "<div class='report-item'><span class='label'>Net Income:</span>" . number_format($total['net_amount']) . "</div>";
            }

            // Print overall totals
            printTotals($totalAmounts, $patients);
        ?>
    </div>
</body>
</html>
