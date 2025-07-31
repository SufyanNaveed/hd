<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .text-uppercase {
        text-transform: uppercase;
    }

    .dashboard {
        max-width: 1200px;
        padding: 20px;
        background: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .number {
        font-size: 30px;
        font-weight: bold;
    }

    .text {
        font-size: 25px;
        font-weight: 10px !important;
    }

    .section {
        margin-bottom: 20px;
    }

    /* .section h2 {
            margin-bottom: 10px;
            font-size: 18px;
        } */
    .overview {
        display: flex;
        gap: 30px;
    }

    .overview-left {
        flex: 0.7;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .overview-left .card {
        /* flex: 1; */
    }

    .overview-right {
        flex: 2;
    }

    .inventory,
    .medicine,
    .demands {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .inventory .card {
        flex: 1 1 calc(20% - 10px);
    }

    .medicine .card {
        flex: 1 1 calc(33.33% - 10px);
    }

    .demands .card {
        flex: 1 1 calc(25% - 10px);
    }

    .padding {
        padding: 15px;
    }

    /* .padding{
            padding-left: 0px;
            padding-right: 0px;
            padding-top: 6px ;
            padding-bottom: 6px;
        } */
    .card {
        /* padding: 0px; */
        /* border-radius: 8px; */
        color: white;
        padding: 10px;
        /* font-weight: bold; */
        text-align: center;
        justify-content: center;
    }

    .card.green {
        background-color: #28a745;
    }

    .card.orange {
        background-color: #fd7e14;
    }

    .card.orange-light {
        background-color: #efad76;
    }

    .card.blue {
        background-color: #17a2b8;
    }

    .card.purple {
        background-color: #ac6eae;
    }

    .card.gray {
        background-color: #c8cdcd;
    }

    .card.red {
        background-color: #dc3545;
    }

    .card.yellow {
        background-color: #dbe36e;
    }

    .card.zink {
        background-color: #1ea398;
    }

    canvas {
        max-width: 100%;
    }

    @media (max-width: 768px) {
        .overview {
            flex-direction: column;
        }

        .overview-left,
        .overview-right {
            flex: 1 1 100%;
        }

        .inventory .card,
        .medicine .card,
        .demands .card {
            flex: 1 1 calc(50% - 10px);
        }
    }

    @media (max-width: 480px) {

        .inventory .card,
        .medicine .card,
        .demands .card {
            flex: 1 1 100%;
        }
    }

    .top-statistics {
        display: flex;
        justify-content: space-between;
        /* gap: 10px; */
        margin-bottom: 10px;
    }

    .stat-box {
        /* flex: 1; */
        display: flex;
        align-items: center;
        background: #fff;
        padding-top: 4px;
        padding-left: 30px;
        padding-right: 30px;
        /* border-radius: 8px; */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    .stat-box.green {
        background-color: #28a745;
    }

    .stat-box.blue {
        background-color: #17a2b8;
    }

    .stat-box.purple {
        background-color: #cf7cc2;
    }


    .chart-section {
        margin-bottom: 20px;
    }

    .tabs {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 10px;
    }

    .tabs button {
        background: none;
        border: 1px solid #ccc;
        padding: 5px 10px;
        margin-left: 5px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .tabs button.active {
        background: #28a745;
        color: white;
    }

    .chart-legend {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin: 0 10px;
        font-size: 14px;
    }

    .text-black {
        color: black;
    }

    .line {
        height: 30px;
        color: white;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .green-line {

        width: 100%;
        background-color: green;
    }

    .blue-line {
        width: 70%;
        background-color: rgb(98, 182, 237);
    }

    .red-line {
        width: 30%;
        background-color: red;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        margin-right: 5px;
        border-radius: 2px;
    }

    .legend-color.green {
        background-color: #28a745;
    }

    .legend-color.blue {
        background-color: #17a2b8;
    }

    .legend-color.purple {
        background-color: #6f42c1;
    }

    canvas {
        max-width: 100%;
    }

    @media (max-width: 768px) {
        .top-statistics {
            flex-direction: column;
        }

        .stat-box {
            margin-bottom: 10px;
        }
    }

    @media (max-width: 768px) {
        canvas {
            height: 300px;
            /* Adjust based on your requirements */
        }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="dashboard">
                    <h1 class="text-uppercase">Health department dashboard - main screen </h1>

                    <div class="section overview">
                        <div class="overview-left">
                            <h2>Hospitals OverView:</h2>
                            <a href="<?php echo base_url('admin/hospital'); ?>" target="_blank"> 
                                <div class="card green text-uppercase">
                                    <p class="number"><?php echo isset($hospitals) ? $hospitals : 0; ?> </p>
                                    <p class="text">Hospitals</p>
                                </div>
                            </a>
                            <a href="<?php echo base_url('admin/department/list'); ?>" target="_blank"> 
                                <div class="card orange">
                                    <p class="number"><?php echo isset($pharmacies_count) ? $pharmacies_count : 0; ?> </p>
                                    <p class="text">Main Pharmacies</p>
                                </div>
                            </a>
                            <a href="<?php echo base_url('admin/store'); ?>" target="_blank"> 
                                <div class="card blue">
                                    <p class="number"><?php echo isset($department_store_count) ? $department_store_count : 0; ?> </p>
                                    <p class="text">Departments Store</p>
                                </div>
                            </a>

                        </div>
                        <div class="overview-right">
                            <h2>Patients Statistics:</h2>
                            <div class="top-statistics">
                                <div class="stat-box green"> 
                                    <a href="<?php echo base_url('admin/report/patientSummaryReport'); ?>" target="_blank" style="color:white;">
                                        <div>
                                            <i class="fas fa-users" style="font-size: 24px; margin-bottom: 5px;"> </i> 
                                            <?php echo isset($total_patients) ? $total_patients : 0; ?>
                                            <p class="">TOTAL PATIENTS</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="stat-box blue">
                                    <a href="<?php echo base_url('admin/patient/search'); ?>" target="_blank" style="color:white;">
                                        <div>
                                            <i class="fas fa-users" style="font-size: 24px; margin-bottom: 5px;"> </i> <?php echo isset($total_opd_patients) ? $total_opd_patients : 0; ?><p class="">OPD PATIENTS</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="stat-box purple">
                                    <a href="<?php echo base_url('admin/patient/ipdsearch'); ?>" target="_blank" style="color:white;">
                                        <div>
                                            <i class="fas fa-users" style="font-size: 24px; margin-bottom: 5px;"> </i> <?php echo isset($total_ipd_patients) ? $total_ipd_patients : 0; ?> <p class="">IPD PATIENTS</p>
                                        </div>
                                    </a>
                                </div>
                            </div>


                            <div class="chart-section">
                                <h4>Patient Statistic</h4>
                                <div class="tabs">
                                    <button class="active">Monthly</button>
                                    <button>Weekly</button>
                                    <button>Today</button>
                                </div>
                                <canvas id="patientChart"></canvas>
                                <div class="chart-legend">
                                    <div class="legend-item"><span class="legend-color green"></span> Total Patients</div>
                                    <div class="legend-item"><span class="legend-color blue"></span> IPD Patients</div>
                                    <div class="legend-item"><span class="legend-color purple"></span> OPD Patients</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h2>Inventory Details:</h2>
                    <div class="section inventory ">
                        <!-- <h2>Inventory Details:</h2> -->
                        <!-- <a href="<?php echo base_url('admin/pharmacy/search'); ?>" target="_blank" style="color:white;">
                            <div class="card gray padding text-black">
                                <p class="number"><?php echo isset($total_products) ? $total_products : 0; ?> </p>
                                <p class="text">Total Products</p>

                            </div>
                        </a> -->
                        <div class="card gray padding text-black">
                            <a href="<?php echo base_url('admin/pharmacy/search/1'); ?>" style="display: block;" class="text-black">
                                <p class="number"><?php echo $in_stock_items ? $in_stock_items : 0; ?> </p>
                                <p class="text">In Stock </p>
                            </a>
                        </div>
                        <div class="card gray padding text-black">
                            <a href="<?php echo base_url('admin/pharmacy/search/0'); ?>" style="display: block;" class="text-black">
                                <p class="number">
                                    <?php echo $out_stock_items ? $out_stock_items : 0; ?>
                                </p>
                                <p class="text">Out of Stock</p>
                            </a>
                        </div>
                        <!-- <div class="card orange padding">
                            <p class="number">
                                <?php
                                echo max(0, $total_products - ($total_in_stock_items->in_stock_count + $total_in_stock_items->out_of_stock_count));
                                ?>
                            </p>
                            <p class="text">UnPurchased</p>
                        </div> -->
                        <div class="card yellow padding text-black">
                            <a href="<?php echo base_url(); ?>/admin/report/productExpiryReport" style="display: block;" class="text-black">
                                <p class="number"><?php echo isset($total_expiry_count_this_week->expiry_count_this_week) ? $total_expiry_count_this_week->expiry_count_this_week : 0; ?>
                                </p>
                                <p class="text">Near Expiry</p>
                            </a>
                        </div>
                    </div>
                    <h2>Medicine Value:</h2>
                    <div class="section medicine">
                        <!-- <h2>Medicine Value:</h2> -->
                        <div class="card blue padding">
                            <p class="number">
                                <?php 
                                    echo isset($total_in_stock_supplier_items) 
                                        ? number_format($total_in_stock_supplier_items, 2) 
                                        : '0.00'; 
                                    ?>
                            </p>
                            <p class="text">Total Medicine Value</p>
                        </div>
                        <div class="card zink padding text-black">
                            <p class="number"><?php echo isset($total_issued_medicine->total_issued_price) ? number_format($total_issued_medicine->total_issued_price, 2) : 0; ?></p>
                            <p class="text">Issued to Patients</p>
                        </div>
                        <div class="card blue padding text-black">
                            <p class="number">
                                <?php  $balance = (float) $total_in_stock_supplier_items - (float) $total_issued_medicine->total_issued_price;

                                echo number_format($balance, 2);
                                ?>
                            </p>
                            <p class="text">Balance in Stock</p>
                        </div>

                    </div>
                    <h2>Demands Status:</h2>
                    <div class="section demands">
                        <!-- <h2>Demands Status:</h2> -->
                        <div class="card orange padding">
                            <p class="number"><?php echo isset($total_requests) ? $total_requests : 0; ?> </p>
                            <p class="text">Total Demands</p>
                        </div>
                        <div class="card orange-light text-black padding">
                            <p class="number"><?php echo isset($total_approved) ? $total_approved : 0; ?> </p>
                            <p class="text">Approved</p>
                        </div>
                        <div class="card yellow padding text-black">
                            <p class="number"><?php echo isset($total_requested) ? $total_requested : 0; ?> </p>
                            <p class="text">Pending</p>
                        </div>
                        <div class="card red padding">
                            <p class="number"><?php echo isset($total_rejected) ? $total_rejected : 0; ?> </p>
                            <p class="text">Rejected</p>
                        </div>
                    </div>
                </div>
                <script>
                    const ctx = document.getElementById('patientChart').getContext('2d');
                    let patientChart;

                    function fetchPatientData(type = 'monthly') {
                        fetch(`<?php echo base_url(); ?>admin/admin/getPatientStats?type=${type}`) // Adjust the URL
                            .then(response => response.json())
                            .then(data => {
                                if (patientChart) {
                                    patientChart.destroy(); // Destroy the previous chart instance
                                }

                                patientChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: data.labels,
                                        datasets: data.datasets.map(dataset => ({
                                            ...dataset,
                                            borderColor: dataset.label === 'Total Patients' ? '#28a745' : dataset.label === 'IPD Patients' ? '#17a2b8' : '#6f42c1',
                                            backgroundColor: dataset.label === 'Total Patients' ? 'rgba(40, 167, 69, 0.2)' : dataset.label === 'IPD Patients' ? 'rgba(23, 162, 184, 0.2)' : 'rgba(111, 66, 193, 0.2)',
                                            fill: true,
                                        })),
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                display: true,
                                            },
                                        },
                                        scales: {
                                            x: {
                                                ticks: {
                                                    font: {
                                                        size: window.innerWidth < 768 ? 10 : 14
                                                    }
                                                }
                                            },
                                            y: {
                                                ticks: {
                                                    font: {
                                                        size: window.innerWidth < 768 ? 10 : 14
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            })
                            .catch(error => console.error('Error fetching data:', error));
                    }

                    // Fetch patient data when the page loads
                    fetchPatientData();

                    // Bind the fetch function to the buttons
                    document.querySelector('.tabs').addEventListener('click', (event) => {
                        if (event.target.tagName === 'BUTTON') {
                            // Remove active class from all buttons
                            document.querySelectorAll('.tabs button').forEach(button => button.classList.remove('active'));
                            // Add active class to the clicked button
                            event.target.classList.add('active');
                            // Fetch data based on the selected button
                            const type = event.target.textContent.toLowerCase();
                            fetchPatientData(type);
                        }
                    });
                </script>
                <!-- <script>
        const ctx = document.getElementById('patientChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [
                    {
                        label: 'Total Patients',
                        data: [500, 700, 800, 600, 400, 700, 900, 800, 600, 700, 800, 900],
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true,
                    },
                    {
                        label: 'IPD Patients',
                        data: [300, 400, 500, 400, 300, 500, 600, 500, 400, 500, 600, 700],
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.2)',
                        fill: true,
                    },
                    {
                        label: 'OPD Patients',
                        data: [200, 300, 300, 200, 100, 200, 300, 300, 200, 300, 200, 200],
                        borderColor: '#6f42c1',
                        backgroundColor: 'rgba(111, 66, 193, 0.2)',
                        fill: true,
                    }
                ]
            },
        options: {
    responsive: true,
    plugins: {
        legend: {
            display: false,
        },
    },
    scales: {
        x: {
            ticks: {
                font: {
                    size: window.innerWidth < 768 ? 10 : 14
                }
            }
        },
        y: {
            ticks: {
                font: {
                    size: window.innerWidth < 768 ? 10 : 14
                }
            }
        }
    }
}

        });
    </script> -->
            </div>
        </div>
    </section>
</div>