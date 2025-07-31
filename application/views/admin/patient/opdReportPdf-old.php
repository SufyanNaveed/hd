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
	}
	/* Define table cell style */

	td {
		text-align: center;
		font-size: 14px;
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
			<div class="caption-row"> OPD Report </div>
			<?php if(empty($date_data['to_date']) && empty($date_data['from_date'])) { ?>
				<div class="caption-row"> All Record </div>
				<?php } elseif(isset($date_data['to_date']) && !empty($date_data['to_date'])) { ?>
					<div class="caption-row"> From :
						<?php echo date('d-M-Y',strtotime($date_data['from_date'])) .' To : '.date('d-M-Y',strtotime($date_data['to_date']));?>
					</div>
					<?php } else{?>
						<div class="caption-row"> From :
							<?php echo date('d-M-Y',strtotime($date_data['from_date'])) ;?>
						</div>
						<?php } ?>
							<hr>
							<hr>
							<thead>
								<tr>
									<th>Sr No</th>
									<th>Appointment Date</th>
									<th>OPD NO</th>
									<th>Pateint ID </th>
									<th>Pateint Name </th>
									<th>KPO</th>
									<th>Amount</th>
								</tr>
							</thead>
							<tbody>
								<?php
                                    if (!empty($resultlist)) {
										$i=1;
                                        $count = 1;
                                        $total = 0;
                                        foreach ($resultlist as $report) {
                                            if (!empty($report['amount'])) {

                                                $amount = $report['amount'] ;
                                                $total += $amount ;
                                            }


                                             $paymentmode = $report['payment_mode'];


                                            if($report['paytype'] == 'visit'){
                                                    $paymenttype =  $this->lang->line('visit');

                                            }elseif ($report['paytype'] == 'rechekup'){
                                                    $paymenttype =  $this->lang->line('re_checkup');

                                            }elseif ($report['paytype'] == 'payment'){
                                                    $paymenttype =  $this->lang->line('payment');

                                            }elseif ($report['paytype'] == 'bill'){
                                                    $paymenttype =  $this->lang->line('bill');
                                            }

                ?>
									<tr>
										<td>
											<?php echo $i; ?>
										</td>
										<td>
											<?php echo date('d-M-Y', strtotime($report['appointment_date'])) ?>
										</td>
										<td>
											<?php echo $report['opd_no']; ?>
										</td>
										<td>
											<?php echo $report['patient_unique_id']; ?>
										</td>
										<td>
											<?php echo $report['patient_name'] ?>
										</td>
										<td>
											<?php echo $report['kop_name']; ?>
										</td>
										<td>
											<?php echo number_format($amount) ; ?>
										</td>
									</tr>
									<?php $i++;}} ?>
										<tr>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td>
												<b><?php echo $this->lang->line('total') . " :" ; ?></b>
 											</td>
											<td>
												<b><?php echo $currency_symbol . number_format($total); ?></b>
											</td>
										</tr>
							</tbody>
		</table>

	</div>
	<!-- <div class="page-number"></div> -->
	<!-- Page number div placed at the bottom -->

</body>

</html>