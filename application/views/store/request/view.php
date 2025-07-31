<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style type="text/css">
    #easySelectable {
        /*display: flex; flex-wrap: wrap;*/
    }

    #easySelectable li {}

    #easySelectable li.es-selected {
        background: #2196F3;
        color: #fff;
    }

    .easySelectable {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    /*.printablea4{width: 100%;}
    .printablea4 p{margin-bottom: 0;}
    .printablea4>tbody>tr>th,
    .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}*/
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
            <div class="" id="myModal" aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog pup100" role="document">
                        <div class="modal-content modal-media-content">
                        <div class="container mt-4">
    <h3 class="mb-4">Request Details</h3>

    <!-- Main Request Information Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Main Request Information</h5>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4"><strong>Bill No:</strong></div>
                <div class="col-md-8"><?php echo htmlspecialchars($request->purchase_no); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Date:</strong></div>
                <div class="col-md-8"><?php echo date('Y-m-d H:i:s', strtotime($request->date)); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Store:</strong></div>
                <div class="col-md-8"><?php echo htmlspecialchars($request->store_name); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Status:</strong></div>
                <div class="col-md-8">
                    <?php 
                    // Map the status to user-friendly text
                    switch ($request->status) {
                        case 'requested':
                            echo '<span class="badge bg-yellow text-black" style="background:yellow!important">Pending</span>';
                            break;
                        case 'approved':
                            echo '<span class="badge bg-green text-black">Approved</span>';
                            break;
                        case 'approved':
                            echo '<span class="badge bg-success text-black">Approved</span>';
                            break;
                        default:
                            echo '<span class="badge bg-secondary text-black">'.strtoupper($request->status).'</span>';
                    }
                    ?>
                </div>
            </div>
          
            <div class="row mb-2">
                <div class="col-md-4"><strong>Note:</strong></div>
                <div class="col-md-8"><?php echo htmlspecialchars($request->remarks); ?></div>
            </div>
        </div>
    </div>
<hr/>
    <!-- Batch Details Card -->
    <div class="card">
        <div class="card-header">
            <h5>Medicine Details</h5>
        </div>
        <div class="card-body">
            <table class="custom-table table table table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Category</th>
                        <th>Requested Quantity</th>
                        <?php if($request->status == 'approved' || $request->status == 'partial'){ ?>
                        <th>Approved Quantity</th>
                        <?php  } ?>

                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($batch_details)): ?>
                        <?php foreach ($batch_details as $batch): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($batch->medicine_name); ?></td>
                                <td><?php echo htmlspecialchars($batch->medicine_category); ?></td>
                                <td><?php echo $batch->quantity; ?></td>
                        <?php if($request->status == 'approved' || $request->status == 'partial'){ ?>

                                <td><?php echo $batch->approved_quantity; ?></td>
                        <?php  } ?>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No batch details found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

                        </div>
                    </div>
            </div>
            </div>
        </div>
    </section>
            </div>
        