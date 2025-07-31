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

    <div style="height: 50px;width:100%">
			<img src="uploads/printing/2.jpg" style="width: 30%;height:40%" alt="">	
	</div>
                   
	<div class="row" style="width: 100%;height:50px">
		<div class="col-6" style="width:70%;float:left">
			<p>Bill # <?php echo $invoice_detail['id']?></p>
		</div>
		<div class="col-6" style="width:30%;float:left">
			<p>Date:  <?php echo $invoice_detail['appointment_date']?></p>
		</div>
	</div>
    <hr>
	<div  style="width: 100%;;height:50px">
		<div class="col-6" style="width: 70%;float:left">
			<p><b>Name:</b>    <?php echo $invoice_detail['patient_name']?></p><br><br>
			<p><b>Opd No:</b>    <?php echo $invoice_detail['opd_no']?></p>
		</div>
		<div class="col-6" style="width: 30%;float:left;">
			<p><b>Doctor:</b>  <?php echo $invoice_detail['name']?></p><br><br>
			<p><b>TPA:</b>  </p>
		</div>
	</div><br>
    <hr>
	<div  style="width: 100%;;height:50px">
		<div class="col-6" style="width: 70%;float:left">
			<p><b>Case:</b>    <?php echo $invoice_detail['case_type']?></p><br><br>
			<p><b>Symptoms:</b>    <?php echo $invoice_detail['symptoms']?></p>
		</div>
		<div class="col-6" style="width: 30%;float:left;">
			<p><b>Cusality:</b>  <?php echo $invoice_detail['note']?></p><br><br>
			<p><b>Note:</b>  </p>
		</div>
	</div><br>
    <hr>
	<div  style="width: 100%;;height:50px">
		<div class="col-6" style="width: 70%;float:left">
			<p><b>Paid Amount (PKR):</b>    <?php echo $invoice_detail['apply_charge']?></p><br><br>
					</div>
	</div><br>
    
	<!-- <div>
		<div style="width: 100%;">
			<div style="width: 30%;float:left">
				<p>Name</p>
			</div>
		</div>
	</div> -->
</body>
</html>