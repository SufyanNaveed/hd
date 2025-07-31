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
                table-layout: fixed;
                /* Use fixed table layout */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            th,
            td {
                text-align: center;
                font-size: 12px;
                /* Adjusted font size */
                border: 1px solid #ddd;
                padding: 4px;
                /* Adjusted padding */
                word-wrap: break-word;
                /* Ensure content wraps */
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

            .caption-row,
            .extra-filter {
                background-color: #3498db;
                color: #fff;
                font-size: 14px;
                font-weight: bold;
                text-align: center;
                padding: 4px;
                /* Adjusted padding */
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
            $opdReport = "Income Summary";
            ?> <?php echo $opdReport; ?> </div>
            <?php
                // Display date filter based on search_type
                if ($search_type == 'all_time') {
                    echo "<div class='extra-filter'>All Records</div>";
                } else {
                    $searchLabels = [
                        'today' => 'Today',
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

                // Display KPO names
                if (empty($kpo_id)) {
                    echo "<div class='extra-filter'>KPO's = All</div>";
                } else {
                    $kpo_name = $kpo_filterData[$kpo_id] ?? ''; 
                    echo "<div class='extra-filter'>KPO = $kpo_name</div>";
                }
            ?>
        </div>
            <hr>
            <hr>
            <!-- Existing HTML and PHP code -->
            <table>
                <!-- Table headers -->
                <thead>
                    <!-- Header row -->
                    <tr>
                        <!-- Header cells -->
                        <th style="width: 5%">S/N</th>
                        <th style="width: 15%">DEPARTMENT</th>
                        <th style="width: 10%">TOTAL PATIENTS</th>
                        <th style="width: 10%">TOTAL TPA & AMOUNT</th>
                        <th style="width: 10%">TOTAL AMOUNT</th>
                        <th style="width: 10%">TOTAL REFUND</th>
                        <th style="width: 10%">DISCOUNT</th>
                        <th style="width: 10%">HOSPITAL SHARE</th>
                        <th style="width: 10%">DOCTOR SHARE</th>
                        <th style="width: 10%">STAFF SHARE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Define department names and initialize totals array
                        $departments = ['OPD', 'Pathology', 'Radiology'];
                        $departmentTotals = [];

                        // Initialize department-wise totals
                        foreach ($departments as $department) {
                            $departmentTotals[$department] = [
                                'total_patients' => 0,
                                'total_tpa_amount' => 0,
                                'total_amount' => 0,
                                'total_refund' => 0,
                                'discount' => 0,
                                'hospital_share' => 0,
                                'doctor_share' => 0,
                                'staff_share' => 0,
                            ];
                        }

                        // Loop through the results and calculate department-wise totals
                        $serialNumber = 1;
                        foreach ($resultlist as $key1 => $result2) {
                            foreach ($result2 as $key4 => $result) {
                                // Your existing code here

                                // Update department-wise totals based on department name
                                if (isset($departmentTotals[$key1])) {
                                    $departmentTotals[$key1]['total_patients']++;
                                    $departmentTotals[$key1]['total_tpa_amount'] += $result['tpa_amount'];
                                    $departmentTotals[$key1]['total_amount'] += $result['amount'];
                                    $departmentTotals[$key1]['discount'] += $result['discount'];

                                    // Check and calculate refund
                                    if ($result['status'] == 'refund') {
                                        $total_refund = $result['amount'] - $result['discount'];
                                        $departmentTotals[$key1]['total_refund'] += $total_refund;
                                    } else {
                                        // Calculate hospital share
                                        $hospital_share = $result['amount'] - ($result['doctor_share'] + $result['staff_share']);
                                        $departmentTotals[$key1]['hospital_share'] += $hospital_share;

                                        // Exclude staff_share and doctor_share calculation for refunds
                                        if ($result['status'] != 'refund') {
                                            $departmentTotals[$key1]['doctor_share'] += $result['doctor_share'];
                                            $departmentTotals[$key1]['staff_share'] += $result['staff_share'];
                                        }
                                    }
                                }
                            }
                        }

                        // Function to print department totals row
                        function printDepartmentTotalRow($serialNumber, $department, $total) {
                            echo "<tr>";
                            echo "<td>{$serialNumber}</td>";
                            echo "<td>{$department}</td>";
                            echo "<td>" . number_format($total['total_patients']) . "</td>";
                            echo "<td>" . number_format($total['total_tpa_amount']) . "</td>";
                            echo "<td>" . number_format($total['total_amount']) . "</td>";
                            echo "<td>" . number_format($total['total_refund']) . "</td>";
                            echo "<td>" . number_format($total['discount']) . "</td>";
                            echo "<td>" . number_format($total['hospital_share']) . "</td>";
                            echo "<td>" . number_format($total['doctor_share']) . "</td>";
                            echo "<td>" . number_format($total['staff_share']) . "</td>";
                            echo "</tr>";
                        }

                        // Loop through departments and print department totals rows
                        foreach ($departments as $department) {
                            printDepartmentTotalRow($serialNumber, $department, $departmentTotals[$department]);
                            $serialNumber++;
                        }
                        // Function to print totals row
                        function printTotalsRow($total) {
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td><b>TOTAL</b></td>";
                            echo "<td>" . number_format($total['total_patients']) . "</td>";
                            echo "<td>" . number_format($total['total_tpa_amount']) . "</td>";
                            echo "<td>" . number_format($total['total_amount']) . "</td>";
                            echo "<td>" . number_format($total['total_refund']) . "</td>";
                            echo "<td>" . number_format($total['discount']) . "</td>";
                            echo "<td>" . number_format($total['hospital_share']) . "</td>";
                            echo "<td>" . number_format($total['doctor_share']) . "</td>";
                            echo "<td>" . number_format($total['staff_share']) . "</td>";
                            echo "</tr>";
                        }

                        // Calculate overall totals
                        $overallTotal = [
                            'total_patients' => array_sum(array_column($departmentTotals, 'total_patients')),
                            'total_tpa_amount' => array_sum(array_column($departmentTotals, 'total_tpa_amount')),
                            'total_amount' => array_sum(array_column($departmentTotals, 'total_amount')),
                            'total_refund' => array_sum(array_column($departmentTotals, 'total_refund')),
                            'discount' => array_sum(array_column($departmentTotals, 'discount')),
                            'hospital_share' => array_sum(array_column($departmentTotals, 'hospital_share')),
                            'doctor_share' => array_sum(array_column($departmentTotals, 'doctor_share')),
                            'staff_share' => array_sum(array_column($departmentTotals, 'staff_share')),
                        ];

                        // Print overall totals row
                        printTotalsRow($overallTotal);
                    ?>
                </tbody>
            </table>
            <!-- Additional HTML and PHP code -->
        </div>
    </body>
</html>