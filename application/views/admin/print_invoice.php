<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=
	, initial-scale=1.0">
	<title>Invoice</title>
	 <link src="http://localhost/HMS/backend/bootstrap/css/bootstrap.min.css">
	<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> -->
</head>
<body>
	<div class="row" >
		<div class="col-6" >
			<p>Bill # <?php echo $invoice_detail['id']?></p>
		</div>
		<div class="col-6" >
			<p>Date:  <?php echo $invoice_detail['appointment_date']?></p>
		</div>
	</div><br>
	<div class="row" style="width: 100%;">
		<div class="col-6" style="width: 30%;float:left">
			<p><b>Name</b>    <?php echo $invoice_detail['patient_name']?></p>
			<!-- <p><?php echo $invoice_detail['patient_name']?></p> -->
		</div>
		<div class="col-6" style="width: 30%;float:left;">
			<p>Date:  <?php echo $invoice_detail['appointment_date']?></p>
		</div>
	</div>
	<!-- <div>
		<div style="width: 100%;">
			<div style="width: 30%;float:left">
				<p>Name</p>
			</div>
		</div>
	</div> -->
</body>
</html>