<!DOCTYPE html>
<html>

<head>
	<style>
	/* Define overall page style */

	body {
		width: 100%;
		/* Set the page width to 100% */
		margin: 0;
		padding: 0;
	}
	/* Define overall table style */

	table {
		width: 100%;
		border-collapse: collapse;
		font-family: Arial, sans-serif;
		margin-bottom: 20px;
	}
	/* Define table header style */

	th {
		background-color: #3498db;
		color: #fff;
		font-weight: bold;
		text-align: center;
		border: 1px solid #ddd;
        font-size: 11px;
	}
	/* Define table cell style */

	td {
		text-align: center;
		font-size: 10px;
		border: 1px solid #ddd;
	}
	/* Define table row hover effect */

	tr:hover {
		background-color: #f2f2f2;
	}
	/* Define table alternate row background */

	tr:nth-child(even) {
		background-color: #ecf0f1;
	}
	/* Add shadow to the table */

	table {
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
	}
	/* Style the table caption row */

	.caption-row {
		background-color: #3498db;
		/* Use the same color as the th elements */
		color: #fff;
		font-size: 14px;
		font-weight: bold;
		text-align: center;
	}
	/* Style the HR line */

	hr {
		border: 1px solid #ddd;
	}
	/* Define the page number style */

	.page-number {
		position: fixed;
        bottom: 10px;
        width: 100%;
        text-align: center;
	}
	</style>
</head>

<body>
	        <div class="content">
		        <table>
                    <div class="caption-row">
                        <?= $this->setting_model->getCurrentHospitalName() ?>
                    </div>
                    <div class="caption-row">
                    <?php echo "OPD LIST" ;?></div>
                     <hr>
							<thead>
								<tr>
									<th>Sr No</th>
									<th>Last Visit</th>
									<th style="text-align:left">Patient</th>
									<th>MR No</th>
									<th>Invoice No </th>
									<th>CNIC</th>
									<th>Gender</th>
									<th>Contact No</th>
									<th style="text-align:left">Consultant</th>
									<th style="text-align:left">KPO</th>
									<th>Amt</th>
									<th>Total Visit</th>
								</tr>
							</thead>
							<tbody>
								<?php
                                    if (!empty($resultlist)) {
										$i=1;
                                        foreach ($resultlist as $report) {
                                        ?>
									<tr>
										<td>
											<?php echo $i; ?>
										</td>
										<td>
											<?php echo date('d-M-Y h:i A', strtotime($report->last_visit)) ?>
										</td>
										<td style="text-align:left">
											<?php echo $report->patient_name; ?>
										</td>
										<td>
											<?php echo $report->patient_unique_id; ?>
										</td>
										<td>
											<?php echo $report->opd_no; ?>
										</td>
										<td>
											<?php echo $report->patient_cnic; ?>
										</td>
										<td >
											<?php echo $report->gender; ?>
										</td>
										<td>
											<?php echo $report->mobileno; ?>
										</td>
										<td style="text-align:left">
											<?php echo $report->name; ?>
										</td>
										<td style="text-align:left">
											<?php echo $report->kpo_name; ?>
										</td>
										<td>
											<?php echo  isset($result_value->opddiscount) && $result_value->opddiscount > 0 ? floor($result_value->apply_charge - $result_value->opddiscount) : floor($result_value->apply_charge) ; ?>
										</td>
                                        <td>
											<?php echo $report->total_visit; ?>
										</td>
									</tr>
									<?php $i++;}} ?>
							</tbody>

                </table>

	        </div>
	<!-- <div class="page-number"></div> -->
	<!-- Page number div placed at the bottom -->

</body>

</html>