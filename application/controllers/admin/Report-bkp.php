<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Report extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the Store Model
        $this->load->model('store_model');
        $this->load->model('hospital_model');
        $this->load->model('supplier_type_model');
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type');
    }

    public function storeconsumptionreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/report/storeconsumptionreport');
        $this->load->model('common_model');
        $select = 'hospitals.name as hospital_name, 
        departments.department_name, 
        supplier_bill_basic.status as request_status, 
        supplier_bill_basic.id as supplier_bill_basic_id, 
        supplier_bill_basic.parent_request_id, 
        supplier_bill_basic.created_at,  
        medicine_batch_details.batch_no, 
        medicine_batch_details.expiry_date,   
        medicine_batch_details.amount / NULLIF(medicine_batch_details.quantity, 0) AS purchase_price,  
        medicine_batch_details.quantity AS approved_quantity,  
        medicine_batch_details.amount AS amount,  
        COALESCE(pharmacy.medicine_name, "Unknown") as medicine_name, 
        medicine_category.medicine_category';

        $join = array(
            'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
            'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
            'LEFT JOIN main_stores ON main_stores.id = medicine_batch_details.target_store_id',
            'LEFT JOIN departments ON departments.id = main_stores.department_id',
            'LEFT JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id',
            'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id'
        );

        $table_name  = "supplier_bill_basic";
        $search_type = $this->input->post("search_type") ?? "this_month"; // Default to "this_month"
        $hospital_id = $this->input->post('hospital_id');
        $selected_hospital_id = isset($hospital_id) && !empty($hospital_id) ? $hospital_id : '';
        $additional_where = [
            "supplier_bill_basic.parent_request_id IS NOT NULL",
            "supplier_bill_basic.parent_request_id IN (SELECT id FROM supplier_bill_basic WHERE status = 'approved')",
            "pharmacy.medicine_name IS NOT NULL"

        ];

        if (!empty($selected_hospital_id)) {
            $additional_where[] = "supplier_bill_basic.hospital_id = '$selected_hospital_id'";
        }



        // $formatted_additional_where = [];
        // foreach ($additional_where as $key => $value) {
        //     $formatted_additional_where[] = "$key = '$value'";
        // }
        $search_table  = "supplier_bill_basic"; // Change to correct table
        $search_column = "created_at";
        $resultlist    = $this->report_model->searchReportStoreConsumption($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where);
        // echo "<pre>"; print_r($)
        $data["searchlist"]  = $search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist['main_data'];
        // echo "<pre>"; print_r($data["resultlist"]);exit;
        // $data["selected_hospital_id"]  = $selected_hospital_id;
        $data['hospitals'] = $this->common_model->getRecord($id = null, 'hospitals', $where = '');
        $data["searchlist"]  = $this->search_type;
        if ($this->input->post('search') == 'export_pdf') {
            $this->storeConsumptionPdf($data);
        } else {

            $this->load->view('layout/header');
            $this->load->view('admin/report/store_consumption_report', $data);
            $this->load->view('layout/footer');
        }
    }
    public function storeyearlyconsumptionreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/report/storeconsumptionreport');
        $this->load->model('common_model');
        $select = 'hospitals.name as hospital_name, 
        departments.department_name, 
        supplier_bill_basic.status as request_status, 
        supplier_bill_basic.id as supplier_bill_basic_id, 
        supplier_bill_basic.parent_request_id, 
        supplier_bill_basic.created_at,  
        medicine_batch_details.batch_no,  
        medicine_batch_details.expiry_date,  
        medicine_batch_details.amount / NULLIF(medicine_batch_details.quantity, 0) AS purchase_price,  
        medicine_batch_details.quantity AS approved_quantity,  
        medicine_batch_details.amount AS amount,  
        COALESCE(pharmacy.medicine_name, "Unknown") as medicine_name, 
        medicine_category.medicine_category';

        $join = array(
            'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
            'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
            'LEFT JOIN main_stores ON main_stores.id = medicine_batch_details.target_store_id',
            'LEFT JOIN departments ON departments.id = main_stores.department_id',
            'LEFT JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id',
            'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id'
        );

        $table_name  = "supplier_bill_basic";
        $search_type = $this->input->post("search_type") ?? "this_month"; // Default to "this_month"
        $hospital_id = $this->input->post('hospital_id');
        $selected_hospital_id = isset($hospital_id) && !empty($hospital_id) ? $hospital_id : '';
        $additional_where = [
            "supplier_bill_basic.parent_request_id IS NOT NULL",
            "supplier_bill_basic.parent_request_id IN (SELECT id FROM supplier_bill_basic WHERE status = 'approved')",
            "pharmacy.medicine_name IS NOT NULL"

        ];

        if (!empty($selected_hospital_id)) {
            $additional_where[] = "supplier_bill_basic.hospital_id = '$selected_hospital_id'";
        }



        // $formatted_additional_where = [];
        // foreach ($additional_where as $key => $value) {
        //     $formatted_additional_where[] = "$key = '$value'";
        // }
        $search_table  = "supplier_bill_basic"; // Change to correct table
        $search_column = "created_at";
        $resultlist    = $this->report_model->searchReportStoreConsumption($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where);
        // echo "<pre>"; print_r($)
        $data['is_stock'] = false;

        $data["searchlist"]  = $search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist['main_data'];
        $data["selected_hospital_id"]  = $selected_hospital_id;
        $data['hospitals'] = $this->common_model->getRecord($id = null, 'hospitals', $where = '');
        $data["searchlist"]  = $this->search_type;
        if ($this->input->post('search') == 'export_pdf') {
            $this->storeYearlyConsumptionPdf($data);
        } else {

            $this->load->view('layout/header');
            $this->load->view('admin/report/Monthly Drug Consumption Summary (Yearly Overview Summary) (A month-wise summary of drug usage throughout the year).pdf', $data);
            $this->load->view('layout/footer');
        }
    }
    public function storePurchaseStockReport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/report/storeconsumptionreport');
        $this->load->model('common_model');
        $select = 'hospitals.name as hospital_name, 
        departments.department_name, 
        supplier_bill_basic.id as supplier_bill_basic_id, 
        supplier_bill_basic.created_at,  
        medicine_batch_details.batch_no,  
        medicine_batch_details.expiry_date,  
        medicine_batch_details.amount / NULLIF(medicine_batch_details.quantity, 0) AS purchase_price,  
        medicine_batch_details.quantity AS approved_quantity,  
        medicine_batch_details.amount AS amount,  
        COALESCE(pharmacy.medicine_name, "Unknown") AS medicine_name, 
        medicine_category.medicine_category,
        supplier_category.id as supplier_id,
        supplier_category.supplier_category as supplier_name,
        supplier_bill_basic.store_id';

        $join = array(
            'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
            'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
            'LEFT JOIN main_stores ON main_stores.id = medicine_batch_details.target_store_id',
            'LEFT JOIN departments ON departments.id = main_stores.department_id',
            'LEFT JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id',
            'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id',
            'INNER JOIN supplier_category ON supplier_category.id = supplier_bill_basic.supplier_id' // Changed from LEFT JOIN to INNER JOIN
        );


        $table_name  = "supplier_bill_basic";
        $search_type = $this->input->post("search_type") ?? "this_month"; // Default to "this_month"
        $hospital_id = $this->input->post('hospital_id');
        $selected_hospital_id = isset($hospital_id) && !empty($hospital_id) ? $hospital_id : '';

        $store_id = $this->input->post('store_id'); // Get store ID from user input
        $supplier_id = $this->input->post('supplier_id'); // Get supplier ID from user input
        $supplier_type_id = $this->input->post('supplier_type_id'); // Get supplier ID from user input


        $additional_where = [];

        // Filter by hospital
        if (!empty($selected_hospital_id)) {
            $additional_where[] = "supplier_bill_basic.hospital_id = '$selected_hospital_id'";
        }

        // Filter by store
        if (!empty($store_id)) {
            $additional_where[] = "supplier_bill_basic.store_id = '$store_id'";
        }

        // Filter by supplier (if provided)
        if (!empty($supplier_id)) {
            $additional_where[] = "supplier_category.id = '$supplier_id'";
        }

        if (!empty($supplier_type_id)) {
            $additional_where[] = "supplier_category.supplier_type_id = '$supplier_type_id'";
        }

        // **Exclude records with empty supplier_id**
        $additional_where[] = "supplier_bill_basic.supplier_id IS NOT NULL AND TRIM(supplier_bill_basic.supplier_id) != '' AND supplier_category.id IS NOT NULL AND pharmacy.medicine_name IS NOT NULL";

        $search_table  = "supplier_bill_basic";
        $search_column = "created_at";

        $resultlist = $this->report_model->searchReportStoreConsumption(
            $select,
            $join,
            $table_name,
            $search_type,
            $search_table,
            $search_column,
            $additional_where
        );
        $data['is_stock'] = true;
        $data['supplierTypes'] = $this->supplier_type_model->getSupplierTypes();

        $data["searchlist"]  = $search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist['main_data'];
        $data["selected_hospital_id"]  = $selected_hospital_id;
        $data['hospitals'] = $this->common_model->getRecord($id = null, 'hospitals', $where = '');
        $data["searchlist"]  = $this->search_type;
        if ($this->input->post('search') == 'export_pdf') {
            $this->purchaseConsumptionPdf($data);
        } else {

            $this->load->view('layout/header');
            $this->load->view('admin/report/store_purchase_report', $data);
            $this->load->view('layout/footer');
        }
    }
    public function storeOpeningStockReport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/report/storeconsumptionreport');
        $this->load->model('common_model');

        $select = 'hospitals.name as hospital_name, 
            departments.department_name, 
            supplier_bill_basic.id as supplier_bill_basic_id, 
            supplier_bill_basic.created_at,  
            medicine_batch_details.batch_no,  
            medicine_batch_details.expiry_date,  
            medicine_batch_details.amount / NULLIF(medicine_batch_details.quantity, 0) AS purchase_price,  
            medicine_batch_details.quantity AS approved_quantity,  
            medicine_batch_details.amount AS amount,  
            COALESCE(pharmacy.medicine_name, "Unknown") AS medicine_name, 
            medicine_category.medicine_category,
            supplier_bill_basic.target_store_id';

        $join = array(
            'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
            'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
            'LEFT JOIN main_stores ON main_stores.id = medicine_batch_details.target_store_id',
            'LEFT JOIN departments ON departments.id = main_stores.department_id',
            'LEFT JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id',
            'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id'
        );

        $table_name  = "supplier_bill_basic";
        $search_type = $this->input->post("search_type") ?? "this_month"; // Default to "this_month"
        $hospital_id = $this->input->post('hospital_id');
        $selected_hospital_id = isset($hospital_id) && !empty($hospital_id) ? $hospital_id : '';

        $target_store_id = $this->input->post('target_store_id'); // Using target_store_id instead of store_id

        $additional_where = [];

        // Filter by hospital
        if (!empty($selected_hospital_id)) {
            $additional_where[] = "supplier_bill_basic.hospital_id = '$selected_hospital_id'";
        }

        // Filter by target store ID
        if (!empty($target_store_id)) {
            $additional_where[] = "supplier_bill_basic.target_store_id = '$target_store_id'";
        }

        // **Apply condition: parent_request_id should be NULL**
        $additional_where[] = "supplier_bill_basic.parent_request_id IS NULL";

        // **Apply condition: status should be NULL**
        $additional_where[] = "supplier_bill_basic.status IS NULL";
        $additional_where[] = "supplier_bill_basic.target_store_id IS NOT NULL";
        $additional_where[] = "pharmacy.medicine_name IS NOT NULL";


        $search_table  = "supplier_bill_basic";
        $search_column = "created_at";

        $resultlist = $this->report_model->searchReportStoreConsumption(
            $select,
            $join,
            $table_name,
            $search_type,
            $search_table,
            $search_column,
            $additional_where
        );

        // echo "<pre>"; print_r($resultlist);exit;
        $data['is_stock'] = true;

        $data["searchlist"]  = $search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist['main_data'];
        $data["selected_hospital_id"]  = $selected_hospital_id;
        $data['hospitals'] = $this->common_model->getRecord($id = null, 'hospitals', $where = '');
        $data["searchlist"]  = $this->search_type;
        // echo "<pre>";print_r($data["resultlist"]);exit;
        if ($this->input->post('search') == 'export_pdf') {
            $this->storeOpeningConsumptionPdf($data);
        } else {
            $this->load->view('layout/header');
            $this->load->view('admin/report/store_opening_stock_report', $data);
            $this->load->view('layout/footer');
        }
    }


    public function storeYearlyConsumptionPdf($data)
    {
        $this->load->library('pdf');

        if (empty($data['resultlist'])) {
            $html = '<style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
            .no-records { text-align: center; font-size: 14px; font-weight: bold; color: #e74c3c; }
        </style>';
            $html .= '<h1>Store Consumption</h1>';
            $html .= '<div class="no-records">No records found</div>';

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->loadHtml($html);
            $this->dompdf->render();
            $this->dompdf->stream("Store_Consumption_Report.pdf", ["Attachment" => 1]);
            return;
        }

        // Step 1: Group Data by Medicine + Batch + Expiry + Price
        $months = ["AUG", "SEP", "OCT", "NOV", "DEC", "JAN", "FEB", "MAR", "APR", "MAY", "JUN"];
        $grouped_data = [];
        $total_amount = 0;

        foreach ($data['resultlist'] as $row) {
            $month = strtoupper(date('M', strtotime($row['created_at'])));
            $group_key = md5(
                $row['medicine_name'] . '|' .
                    $row['batch_no'] . '|' .
                    $row['expiry_date'] . '|' .
                    $row['purchase_price']
            );

            if (!isset($grouped_data[$group_key])) {
                $grouped_data[$group_key] = [
                    'medicine_name' => $row['medicine_name'],
                    'batch_no' => $row['batch_no'],
                    'expiry_date' => $row['expiry_date'],
                    'purchase_price' => $row['purchase_price'],
                    'monthly' => array_fill_keys($months, 0),
                ];
            }

            $grouped_data[$group_key]['monthly'][$month] += $row['approved_quantity'];
        }

        // Step 2: Prepare Dates
        $prepareDate = $this->prepareDate();
        $from_date_raw = $prepareDate['from_date'] ?? null;
        $to_date_raw = $prepareDate['to_date'] ?? null;

        $isAllTime = $from_date_raw === 'all_time' || !$from_date_raw;

        $from_date = ($from_date_raw && strtotime($from_date_raw)) ? date('F j, Y', strtotime($from_date_raw)) : 'N/A';
        $to_date = ($to_date_raw && strtotime($to_date_raw)) ? date('F j, Y', strtotime($to_date_raw)) : 'Present';

        $header_date_range = !$isAllTime
            ? "From $from_date to $to_date"
            : "All Time";

        // Step 3: Generate HTML
        $html = '<style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2980b9; color: white; padding: 8px; font-size: 10px; }
        td { padding: 6px; text-align: center; border: 1px solid #ddd; font-size: 9px; }
        .item-name { text-align: left; padding-left: 10px; font-weight: bold; }
    </style>';

        $html .= '<table>';
        $html .= '<tr><th colspan="15" style="background-color: #2980b9; color: white; text-align: center;">' . $data['resultlist'][0]['hospital_name'] . 'Monthly Drug Consumption Summary (Yearly Overview Summary) (A month-wise summary of drug usage throughout the year) (' . $header_date_range . ')</th></tr>';
        $html .= '</table>';

        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr>
        <th>S. No</th>
        <th>Item Name</th>';
        foreach ($months as $month) {
            $html .= '<th>' . $month . '</th>';
        }
        $html .= '<th>IssuedQty</th>
        <th>Rate</th>
        <th>Amount</th>
    </tr>';

        // Step 4: Populate Data
        $sno = 1;

        foreach ($grouped_data as $item) {
            $html .= '<tr>';
            $html .= '<td>' . $sno++ . '</td>';
            $html .= '<td class="item-name">' . htmlspecialchars($item['medicine_name']) . ' (' . htmlspecialchars($item['batch_no']) . ')</td>';

            $total_quantity = 0;
            foreach ($months as $month) {
                $quantity = $item['monthly'][$month] ?? 0;
                $html .= '<td>' . number_format($quantity) . '</td>';
                $total_quantity += $quantity;
            }

            $rate = is_numeric($item['purchase_price']) ? (float)$item['purchase_price'] : 0;
            $amount = $total_quantity * $rate;
            $total_amount += $amount;

            $html .= '<td><strong>' . number_format($total_quantity) . '</strong></td>';
            $html .= '<td><strong>' . number_format($rate, 2) . '</strong></td>';
            $html .= '<td><strong>' . number_format($amount, 2) . '</strong></td>';
            $html .= '</tr>';
        }

        // Step 5: Add Total Amount Row
        $html .= '<tr>';
        $html .= '<td colspan="14" style="text-align: right; font-weight: bold;">Total Amount</td>';
        $html .= '<td colspan="2" style="text-align: right; font-weight: bold;">' . number_format($total_amount, 2) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        // Step 6: Generate PDF
        $this->dompdf->set_paper("A4", "landscape");
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();

        // Footer with time and page number
        $canvas = $this->dompdf->getCanvas();
        $canvas->page_text(10, 800, date('d-M-Y h:i A'), null, 8, [0, 0, 0]);
        $canvas->page_text(290, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        $this->dompdf->stream("Yearly_Store_Consumption_Report.pdf", ["Attachment" => 1]);
    }


    public function purchaseConsumptionPdf($data)
    {

        $this->load->library('pdf');
        if (empty($data['resultlist'])) {
            $html = '<style>
                body { font-family: Arial, sans-serif; font-size: 10px; }
                h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
                .no-records { text-align: center; font-size: 14px; font-weight: bold; color: #e74c3c; }
            </style>';
            $html .= '<h1>Store Consumption</h1>';
            $html .= '<div class="no-records">No records found</div>';

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->loadHtml($html);
            $this->dompdf->render();
            $this->dompdf->stream("Purchase_Consumption_Report.pdf", ["Attachment" => 1]);

            return;
        }

        // Grouping Data by Medicine Name and Batch Number
        $grouped_data = [];
        $rates = [];
        $total_amount = 0;
        foreach ($data['resultlist'] as $row) {
            $medicine_name = $row['medicine_name'];
            $batch_number = $row['batch_no'] ? 'Batch-' . $row['batch_no'] : 'N/A';
            $supplier_name = $row['supplier_name']; // Extract supplier name

            if (!isset($grouped_data[$medicine_name])) {
                $grouped_data[$medicine_name] = [
                    'supplier_name' => $supplier_name, // Store supplier name for this medicine
                    'batches' => [] // Store batch-wise quantities
                ];
            }

            // Store batch-wise data under "batches"
            $grouped_data[$medicine_name]['batches'][$batch_number][$row['department_name']] = $row['approved_quantity'];
            $grouped_data[$medicine_name]['batches'][$batch_number]['expiry_date'] = $row['expiry_date'];

            // Store purchase price separately
            $rates[$medicine_name] = $row['purchase_price'];
        }


        // Get unique departments from the data
        $departments = array_unique(array_column($data['resultlist'], 'department_name'));
        //   echo "<pre>";  print_r($departments);exit;
        $prepareDate = $this->prepareDate();
        $header_date_range = '';
        if (isset($prepareDate['from_date']) && $prepareDate['from_date'] !== 'all_time') {
            // Format the date range based on 'from_date' and 'to_date'
            $from_date = date('F j, Y', strtotime($prepareDate['from_date']));
            $to_date = isset($prepareDate['to_date']) ? date('F j, Y', strtotime($prepareDate['to_date'])) : 'Present';
            $header_date_range = "From $from_date to $to_date";
        } else {
            $header_date_range = "All Time";
        }

        $html = '<style>
            body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
            h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 10px; }
            th { background-color: #2980b9; color: white; padding: 8px; font-size: 10px; word-wrap: break-word; }
            td { padding: 6px; text-align: center; border: 1px solid #ddd; font-size: 9px; word-wrap: break-word; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .item-name { text-align: left; padding-left: 10px; font-weight: bold; }
            .header { margin-bottom: 10px; }
            .footer { position: fixed; left: 0; bottom: 0; width: 100%; text-align: center; font-size: 8px; }
            .footer .time { position: absolute; right: 10px; bottom: 10px; font-size: 8px; }
        </style>';
        $html .= '<style>
                .table-header {
                    background-color: #2980b9; /* Blue background */
                    color: white;
                    font-size: 16px;
                    text-align: center;
                    font-weight: bold;
                    padding: 10px;
                    border-radius: 5px;
                    text-transform: uppercase;
                }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                th, td {
                    padding: 8px;
                    text-align: center;
                    border: 1px solid #ddd;
                }
            </style>';

        $html .= '<table class="table">';
        $html .= '<tr>';
        $html .= '<th colspan="6" class="table-header">' . $data['resultlist'][0]['hospital_name'] . ' Purchase Report <span class="date-range">' . $header_date_range . '</th>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr>';
        $html .= '<th colspan="2">' . ucfirst($data['resultlist'][0]['hospital_name']) . '</th>';
        $html .= '<th colspan="' . (count($departments)) . '">Departments</th>';
        $html .= '<th colspan="6">QTY & AMOUNT</th>';
        $html .= '</tr>';
        $html .= '<tr>
            <th style="width: 5%;">S. No</th>
            <th style="width: 50%;">Item Name</th>';

        // foreach ($departments as $dept) {
        //     $html .= '<th style="width: ' . (100 / (count($departments) + 4)) . '%;">' . $dept . '</th>';
        // }
        $html .= '<th style="width: 10%;">Supplier</th>';
        $html .= '<th style="width: 10%;">Batch No</th>';
        $html .= '<th style="width: 10%;">Expiry Date</th>';
        $html .= '<th style="width: 10%;">Unit</th>';
        $html .= '<th style="width: 10%;">Rate</th>';
        $html .= '<th style="width: 10%;">Amount</th>';
        $html .= '</tr>';

        $sno = 1;
        // echo "<pre>"; print_r($grouped_data);exit;
        foreach ($grouped_data as $medicine_name => $medicine_data) {
            $supplier_name = $medicine_data['supplier_name']; // Extract supplier name

            foreach ($medicine_data['batches'] as $batch_number => $dept_quantities) {
                $html .= '<tr>';
                $html .= '<td>' . $sno++ . '</td>';
                $html .= '<td class="item-name">' . $medicine_name . ' (' . $batch_number . ')' . '</td>';
                $html .= '<td><strong>' . $supplier_name . '</strong></td>';
                $html .= '<td><strong>' . $batch_number . '</strong></td>';
                $html .= '<td><strong>' . $dept_quantities['expiry_date'] . '</strong></td>';


                // Display Supplier Name

                $total_quantity = 0;
                $item_amount = 0;

                foreach ($departments as $dept) {
                    $quantity = isset($dept_quantities[$dept]) ? $dept_quantities[$dept] : 0;
                    // $html .= '<td>' . $quantity . '</td>';
                    $total_quantity += $quantity;
                }

                $rate = isset($rates[$medicine_name]) && $rates[$medicine_name] !== '' ? $rates[$medicine_name] : 1;
                $amount = $total_quantity * $rate;
                $item_amount = $amount;
                $total_amount += $item_amount;
                $html .= '<td><strong>' . $total_quantity . '</strong></td>';
                $html .= '<td><strong>' . number_format($rate, 2) . '</strong></td>';
                $html .= '<td><strong>' . number_format($amount, 2) . '</strong></td>';
                $html .= '</tr>';
            }
        }


        $html .= '<tr>';
        $html .= '<td colspan="' . (count($departments) + 4) . '" style="text-align: right; font-weight: bold;">Total Amount</td>';
        $html .= '<td colspan="2" style="text-align: right; font-weight: bold;">' . number_format($total_amount, 2) . '</td>';
        $html .= '</tr>';

        $html .= '</table>';

        // Render the PDF
        $this->dompdf->set_paper("A4", "portrait");
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();

        // Get the canvas and adjust the footer
        $canvas = $this->dompdf->getCanvas();
        $canvas->page_text(10, 800, date('d-M-Y h:i A'), null, 8, [0, 0, 0]);  // Time at the bottom right
        $canvas->page_text(290, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);  // Page number at the center-bottom

        // Stream the PDF
        $this->dompdf->stream("Purchase_Stock_Report.pdf", ["Attachment" => 1]);
    }

    public function storeConsumptionPdf($data)
    {
        $this->load->library('pdf');
        // echo "<pre>"; print_r($data['resultlist']);exit;
        if (empty($data['resultlist'])) {
            $html = '<style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
            .no-records { text-align: center; font-size: 14px; font-weight: bold; color: #e74c3c; }
        </style>';
            $html .= '<h1>Store Consumption</h1>';
            $html .= '<div class="no-records">No records found</div>';

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->loadHtml($html);
            $this->dompdf->render();
            $this->dompdf->stream("Department-wise Drug Utilization Report (Detailed report of drugs consumed by each department).pdf", ["Attachment" => 1]);

            return;
        }

        // Grouping Data by Medicine Name and Batch Number
        $grouped_data = [];
        $rates = [];
        $total_amount = 0;
        // echo "<pre>"; print_r($data['resultlist']);exit;
        foreach ($data['resultlist'] as $row) {
            $medicine_name = $row['medicine_name'];
            $batch_number = $row['batch_no'] ? 'Batch-' . $row['batch_no'] : 'N/A';
            $expiry_date = $row['expiry_date'] ?? 'N/A';
            $purchase_price = $row['purchase_price'];

            // Create a composite key with name, batch, expiry, and price
            $medicine_key = md5($medicine_name . '|' . $batch_number . '|' . $expiry_date . '|' . $purchase_price);

            if (!isset($grouped_data[$medicine_key])) {
                $grouped_data[$medicine_key] = [
                    'medicine_name' => $medicine_name,
                    'batch_no' => $batch_number,
                    'expiry_date' => $expiry_date,
                    'purchase_price' => $purchase_price,
                    'quantities' => [],
                ];
            }

            if (!isset($grouped_data[$medicine_key]['quantities'][$row['department_name']])) {
                $grouped_data[$medicine_key]['quantities'][$row['department_name']] = 0;
            }

            $grouped_data[$medicine_key]['quantities'][$row['department_name']] += $row['approved_quantity'];
        }


        // echo "<pre>"; print_r($grouped_data);exit;
        // Get unique departments from the data
        $departments = array_unique(array_column($data['resultlist'], 'department_name'));
        $prepareDate = $this->prepareDate();
        $header_date_range = isset($prepareDate['from_date']) && $prepareDate['from_date'] !== 'all_time'
            ? "From " . date('F j, Y', strtotime($prepareDate['from_date'])) . " to " . date('F j, Y', strtotime($prepareDate['to_date'] ?? 'Present'))
            : "All Time";

        $html = '<style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 10px; }
        th { background-color: #2980b9; color: white; padding: 8px; font-size: 10px; word-wrap: break-word; }
        td { padding: 6px; text-align: center; border: 1px solid #ddd; font-size: 9px; word-wrap: break-word; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .item-name { text-align: left; padding-left: 10px; font-weight: bold; }
    </style>';

        $html .= '<table>';
        $html .= '<tr><th colspan="15" style="background-color: #2980b9; color: white; text-align: center;">' . $data['resultlist'][0]['hospital_name'] . ' Department-wise Drug Utilization Report (Detailed report of drugs consumed by each department) (' . $header_date_range . ')</th></tr>';
        $html .= '</table>';

        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr>
        <th>S. No</th>
        <th>Item Name</th>
        <th>Batch No</th>
        <th>Expiry Date</th>';

        foreach ($departments as $dept) {
            $html .= '<th>' . $dept . '</th>';
        }

        $html .= '<th>Total Qty</th>
        <th>Rate</th>
        <th>Amount</th>
    </tr>';

        $sno = 1;
        $total_amount = 0;
        $sno = 1;

        foreach ($grouped_data as $medicine_key => $details) {
            $html .= '<tr>';
            $html .= '<td>' . $sno++ . '</td>';

            $medicine_name = $details['medicine_name'] ?? 'N/A';
            $batch_no = $details['batch_no'] ?? 'N/A';
            $expiry_date = $details['expiry_date'] ?? 'N/A';
            $rate = is_numeric($details['purchase_price']) ? (float)$details['purchase_price'] : 0;

            $html .= '<td class="item-name">' . htmlspecialchars($medicine_name) . '</td>';
            $html .= '<td>' . htmlspecialchars($batch_no) . '</td>';
            $html .= '<td>' . htmlspecialchars($expiry_date) . '</td>';

            $total_quantity = 0;

            foreach ($departments as $dept) {
                $quantity = $details['quantities'][$dept] ?? 0;
                $html .= '<td>' . number_format($quantity) . '</td>';
                $total_quantity += $quantity;
            }

            $amount = $total_quantity * $rate;
            $total_amount += $amount;

            $html .= '<td><strong>' . number_format($total_quantity) . '</strong></td>';
            $html .= '<td><strong>' . number_format($rate, 2) . '</strong></td>';
            $html .= '<td><strong>' . number_format($amount, 2) . '</strong></td>';
            $html .= '</tr>';
        }

        // Optional total row



        $html .= '<tr>';
        $html .= '<td colspan="' . (count($departments) + 6) . '" style="text-align: right; font-weight: bold;">Total Amount</td>';
        $html .= '<td colspan="2" style="text-align: right; font-weight: bold;">' . number_format($total_amount, 2) . '</td>';
        $html .= '</tr>';

        $html .= '</table>';

        // Render the PDF
        $this->dompdf->set_paper("A4", "portrait");
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();

        // Add footer
        $canvas = $this->dompdf->getCanvas();
        $canvas->page_text(10, 800, date('d-M-Y h:i A'), null, 8, [0, 0, 0]);
        $canvas->page_text(290, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        $this->dompdf->stream("Department-wise Drug Utilization Report (Detailed report of drugs consumed by each department).pdf", ["Attachment" => 1]);
    }
    public function storeOpeningConsumptionPdf($data)
    {
        $this->load->library('pdf');
        // echo "<pre>"; print_r($data['resultlist']);exit;
        if (empty($data['resultlist'])) {
            $html = '<style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
            .no-records { text-align: center; font-size: 14px; font-weight: bold; color: #e74c3c; }
        </style>';
            $html .= '<h1>Store Consumption</h1>';
            $html .= '<div class="no-records">No records found</div>';

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->loadHtml($html);
            $this->dompdf->render();
            $this->dompdf->stream("Store_Opening_Stock_Report.pdf", ["Attachment" => 1]);

            return;
        }

        // Grouping Data by Medicine Name and Batch Number
        $grouped_data = [];
        $rates = [];
        $total_amount = 0;

        foreach ($data['resultlist'] as $row) {
            $medicine_name = $row['medicine_name'];
            $batch_number = $row['batch_no'] ? 'Batch-' . $row['batch_no'] : 'N/A';
            $expiry_date = $row['expiry_date'] ?? 'N/A';
            $medicine_key = $medicine_name . ' (' . $batch_number . ')';

            if (!isset($grouped_data[$medicine_key])) {
                $grouped_data[$medicine_key] = [];
            }

            $grouped_data[$medicine_key]['expiry_date'] = $expiry_date;
            $grouped_data[$medicine_key]['quantities'] = $grouped_data[$medicine_key]['quantities'] ?? [];

            if (!isset($grouped_data[$medicine_key]['quantities'][$row['department_name']])) {
                $grouped_data[$medicine_key]['quantities'][$row['department_name']] = 0;
            }

            $grouped_data[$medicine_key]['quantities'][$row['department_name']] += $row['approved_quantity'];
            $grouped_data[$medicine_key]['amount'] = ($grouped_data[$medicine_key]['amount'] ?? 0) + $row['amount'];

            $rates[$medicine_key] = $row['purchase_price'];
        }


        // echo "<pre>"; print_r($grouped_data);exit;
        // Get unique departments from the data
        $departments = array_unique(array_column($data['resultlist'], 'department_name'));
        $prepareDate = $this->prepareDate();
        $header_date_range = isset($prepareDate['from_date']) && $prepareDate['from_date'] !== 'all_time'
            ? "From " . date('F j, Y', strtotime($prepareDate['from_date'])) . " to " . date('F j, Y', strtotime($prepareDate['to_date'] ?? 'Present'))
            : "All Time";

        $html = '<style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 10px; }
        th { background-color: #2980b9; color: white; padding: 8px; font-size: 10px; word-wrap: break-word; }
        td { padding: 6px; text-align: center; border: 1px solid #ddd; font-size: 9px; word-wrap: break-word; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .item-name { text-align: left; padding-left: 10px; font-weight: bold; }
    </style>';

        $html .= '<table>';
        $html .= '<tr><th colspan="15" style="background-color: #2980b9; color: white; text-align: center;">' . $data['resultlist'][0]['hospital_name'] . ' Store Opening Stock Report (' . $header_date_range . ')</th></tr>';
        $html .= '</table>';

        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr>
        <th>S. No</th>
        <th>Item Name</th>
        <th>Batch No</th>
        <th>Expiry Date</th>';

        foreach ($departments as $dept) {
            $html .= '<th>' . $dept . '</th>';
        }

        $html .= '<th>Total Qty</th>
        <th>Rate</th>
        <th>Amount</th>
    </tr>';

        $sno = 1;
        foreach ($grouped_data as $medicine_key => $details) {
            $html .= '<tr>';
            $html .= '<td>' . $sno++ . '</td>';

            // Extract batch number from medicine_key
            preg_match('/Batch-(.*?)\)/', $medicine_key, $matches);
            $extracted_batch = $matches[1] ?? 'N/A';
            $medicine_display_name = preg_replace('/ \(Batch-.*?\)/', '', $medicine_key);

            $html .= '<td class="item-name">' . $medicine_display_name . '</td>';
            $html .= '<td>' . $extracted_batch . '</td>';
            $html .= '<td>' . $details['expiry_date'] . '</td>';

            $total_quantity = 0;
            foreach ($departments as $dept) {
                $quantity = $details['quantities'][$dept] ?? 0;
                $html .= '<td>' . $quantity . '</td>';
                $total_quantity += $quantity;
            }

            $rate = $rates[$medicine_key] ?? 0;
            $amount = $total_quantity * $rate;
            $rate = $rates[$medicine_key] ?? 0;
            $amount = $details['amount'];
            $total_amount += $amount;
            $html .= '<td><strong>' . $total_quantity . '</strong></td>';
            $html .= '<td><strong>' . number_format($rate, 2) . '</strong></td>';
            $html .= '<td><strong>' . number_format($amount, 2) . '</strong></td>';
            $html .= '</tr>';
        }



        $html .= '<tr>';
        $html .= '<td colspan="' . (count($departments) + 4) . '" style="text-align: right; font-weight: bold;">Total Amount</td>';
        $html .= '<td colspan="2" style="text-align: right; font-weight: bold;">' . number_format($total_amount, 2) . '</td>';
        $html .= '</tr>';

        $html .= '</table>';

        // Render the PDF
        $this->dompdf->set_paper("A4", "portrait");
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();

        // Add footer
        $canvas = $this->dompdf->getCanvas();
        $canvas->page_text(10, 800, date('d-M-Y h:i A'), null, 8, [0, 0, 0]);
        $canvas->page_text(290, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        $this->dompdf->stream("Store_Consumption_Report.pdf", ["Attachment" => 1]);
    }

    public function prepareDate()
    {
        $search_type = $this->input->post('search_type');
        $data = [];
        switch ($search_type) {
            case 'period':
                $data['from_date'] = $this->input->post('date_from');
                $data['to_date'] = $this->input->post('date_to');
                break;
            case 'today':
                $today = strtotime('today 00:00:00');
                $data['from_date'] = date('Y-m-d', $today);
                break;
            case 'this_week':
                $data['from_date'] = date('Y-m-d H:i:s', strtotime('last monday 00:00:00'));
                $data['to_date'] = date('Y-m-d H:i:s', strtotime('next sunday 23:59:59'));
                break;
            case 'last_week':
                $data['from_date'] = date('Y-m-d H:i:s', strtotime('last monday -1 week 00:00:00'));
                $data['to_date'] = date('Y-m-d H:i:s', strtotime('last sunday -1 week 23:59:59'));
                break;
            case 'this_month':
                $data['from_date'] = date('Y-m-01');
                $data['to_date'] = date('Y-m-t 23:59:59.993');
                break;
            case 'last_month':
                $month = date("m", strtotime("-1 month"));
                $data['from_date'] = date('Y-' . $month . '-01');
                $data['to_date'] = date('Y-' . $month . '-' . date('t', strtotime($data['from_date'])) . ' 23:59:59.993');
                break;
            case 'last_3_month':
                $month = date("m", strtotime("-2 month"));
                $data['from_date'] = date('Y-' . $month . '-01');
                $data['to_date'] = date('Y-m-t 23:59:59.993');
                break;
            case 'last_6_month':
                $month = date("m", strtotime("-5 month"));
                $data['from_date'] = date('Y-' . $month . '-01');
                $data['to_date'] = date('Y-m-t 23:59:59.993');
                break;
            case 'last_12_month':
                $data['from_date'] = date('Y-m-01', strtotime("-11 month"));
                $data['to_date'] = date('Y-m-t 23:59:59.993');
                break;
            case 'last_year':
                $data['from_date'] = date('Y-01-01', strtotime("-1 year"));
                $data['to_date'] = date('Y-12-31 23:59:59', strtotime("-1 year"));
                break;
            case 'this_year':
                $data['from_date'] = date('Y-01-01');
                $data['to_date'] = date('Y-12-31 23:59:59');
                break;

            case 'all_time':
                $data['from_date'] = 'ALL'; // or null, depending on usage
                $data['to_date'] = 'ALL';   // or null
                break;
        }

        return $data;
    }

    public function productExpiryReport()
    {
        // print_r($this->input->post());exit;
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/report/storeconsumptionreport');
        $this->load->model('common_model');

        $select = 'hospitals.name as hospital_name, 
    supplier_bill_basic.status as request_status, 
    supplier_bill_basic.id as supplier_bill_basic_id, 
    supplier_bill_basic.parent_request_id, 
    supplier_bill_basic.created_at,  
    medicine_batch_details.pharmacy_id,
    medicine_batch_details.batch_no,
    MAX(medicine_batch_details.expiry_date) as latest_expiry_date,
    MAX(medicine_batch_details.expiry_date_format) as latest_expiry_date_format,
    SUM(medicine_batch_details.quantity) AS total_quantity,
    SUM(medicine_batch_details.amount) AS total_amount,
    pharmacy.medicine_name as medicine_name, 
    medicine_category.medicine_category';

        $join = array(
            'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
            'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
            'JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id',  // force match
            'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id'
        );


        $table_name = "supplier_bill_basic";

        $search_type = $this->input->post("search_type") ?? "this_week";
        $hospital_id = $this->input->post('hospital_id');
        $near_expiry_days = $this->input->post('near_expiry_days') ?? 30;

        // $date_from_input = $this->input->post('date_from');
        // $date_to_input = $this->input->post('date_to');

        $additional_where = [
            "medicine_batch_details.expiry_date_format IS NOT NULL",
            "medicine_batch_details.bill_status = 'final'",
            "medicine_batch_details.operation_type IS NULL",
            "medicine_batch_details.status IS NULL",
            "medicine_batch_details.approved_quantity IS NULL"
        ];


        if (!empty($hospital_id)) {
            $additional_where[] = "supplier_bill_basic.hospital_id = '$hospital_id'";
        }
        if ($search_type === 'this_week') {

            // Monday to Sunday of this week
            $start_date = date('Y-m-d H:i:s', strtotime('last monday 00:00:00'));
            $end_date = date('Y-m-d H:i:s', strtotime('next sunday 23:59:59'));
        } elseif ($search_type === 'today') {
            // Today only
            $today = strtotime('today 00:00:00');
            $data['from_date'] = date('Y-m-d', $today);
        } elseif ($search_type === 'this_month') {

            // First to last day of this month
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t 23:59:59.993');
        } elseif ($search_type === 'last_month') {
            // First to last day of this month
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t 23:59:59.993');
        } elseif ($search_type === 'next_month') {
            // First to last day of next month
            //      echo $search_type;
            // exit;
            $start_date = date('Y-m-01', strtotime('first day of next month'));
            $end_date = date('Y-m-t 23:59:59.993', strtotime('last day of next month'));
        } else {
            // Default fallback: today + near_expiry_days
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime("+$near_expiry_days days"));
        }
        // echo $search_type;
        // exit;
        // echo $start_date;
        // exit;


        // Build group and having
        $group_by = "medicine_batch_details.pharmacy_id, medicine_batch_details.batch_no, hospitals.name, supplier_bill_basic.status, supplier_bill_basic.id, supplier_bill_basic.parent_request_id, supplier_bill_basic.created_at, pharmacy.medicine_name, medicine_category.medicine_category";
        $having = "MAX(medicine_batch_details.expiry_date_format) BETWEEN '$start_date' AND '$end_date'";

        // Force 'all_time' so model skips created_at filtering
        $search_type_override = 'all_time';
        $search_table = "supplier_bill_basic";
        $search_column = "created_at";

        $resultlist = $this->report_model->searchReportStoreConsumptionWithHaving(
            $select,
            $join,
            $table_name,
            $search_type_override,
            $search_table,
            $search_column,
            $additional_where,
            [],
            [],
            $group_by,
            $having
        );

        echo "<pre>"; print_r($resultlist); exit;

        $data["searchlist"] = $this->search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"] = $resultlist['main_data'];
        $data['hospitals'] = $this->common_model->getRecord(null, 'hospitals', '');

        if ($this->input->post('search') == 'export_pdf') {
            $this->productExpiryPdf($data);
        } else {
            $this->load->view('layout/header');
            $this->load->view('admin/report/product_expiry_report', $data);
            $this->load->view('layout/footer');
        }
    }



    public function productExpiryPdf($data)
    {
        $this->load->library('pdf');

        if (empty($data['resultlist'])) {
            $html = '<style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
            .no-records { text-align: center; font-size: 14px; font-weight: bold; color: #e74c3c; }
        </style>';
            $html .= '<h1>Product Expiry Report</h1>';
            $html .= '<div class="no-records">No near-expiry products found</div>';

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->loadHtml($html);
            $this->dompdf->render();
            $this->dompdf->stream("Product_Expiry_Report.pdf", ["Attachment" => 1]);

            return;
        }

        $html = '<style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: center; font-size: 9px; }
        th { background-color: #2980b9; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .item-name { text-align: left; }
    </style>';

        $html .= '<h1>' . htmlspecialchars($data['resultlist'][0]['hospital_name']) . ' - Product Expiry Report</h1>';
        $html .= '<table>';
        $html .= '<tr>
        <th>S. No</th>
        <th>Item Name</th>
        <th>Batch No</th>
        <th>Expiry Date</th>
        <th>Approved Quantity</th>
        <th>Purchase Price</th>
        <th>Total Amount</th>
    </tr>';

        $sno = 1;
        $grand_total = 0;

        foreach ($data['resultlist'] as $row) {
            $medicine_name = $row['medicine_name'] ?? 'N/A';
            $batch_no = $row['batch_no'] ?? 'N/A';
            $expiry_date = $row['expiry_date'] ?? 'N/A';
            $approved_qty = $row['approved_quantity'] ?? 0;
            $purchase_price = is_numeric($row['purchase_price']) ? (float)$row['purchase_price'] : 0;
            $amount = $approved_qty * $purchase_price;
            $grand_total += $amount;

            $html .= '<tr>';
            $html .= '<td>' . $sno++ . '</td>';
            $html .= '<td class="item-name">' . htmlspecialchars($medicine_name) . '</td>';
            $html .= '<td>' . htmlspecialchars($batch_no) . '</td>';
            $html .= '<td>' . htmlspecialchars($expiry_date) . '</td>';
            $html .= '<td>' . number_format($approved_qty) . '</td>';
            $html .= '<td>' . number_format($purchase_price, 2) . '</td>';
            $html .= '<td>' . number_format($amount, 2) . '</td>';
            $html .= '</tr>';
        }

        $html .= '<tr>
        <td colspan="6" style="text-align: right; font-weight: bold;">Grand Total</td>
        <td style="font-weight: bold;">' . number_format($grand_total, 2) . '</td>
    </tr>';

        $html .= '</table>';

        $this->dompdf->set_paper("A4", "portrait");
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();

        $canvas = $this->dompdf->getCanvas();
        $canvas->page_text(10, 800, date('d-M-Y h:i A'), null, 8, [0, 0, 0]);
        $canvas->page_text(290, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        $this->dompdf->stream("Product_Expiry_Report.pdf", ["Attachment" => 1]);
    }

   public function outOFStockReport()
{
    $select = 'hospitals.name as hospital_name, 
        departments.department_name, 
        supplier_bill_basic.id as supplier_bill_basic_id, 
        supplier_bill_basic.created_at,  
        medicine_batch_details.batch_no,  
        medicine_batch_details.expiry_date,  
        medicine_batch_details.amount / NULLIF(medicine_batch_details.quantity, 0) AS purchase_price,  
        medicine_batch_details.quantity AS approved_quantity,  
        medicine_batch_details.amount AS amount,  
        COALESCE(pharmacy.medicine_name, "Unknown") AS medicine_name, 
        medicine_category.medicine_category,
        supplier_category.id as supplier_id,
        supplier_category.supplier_category as supplier_name,
        supplier_bill_basic.store_id';

    $join = array(
        'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
        'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
        'LEFT JOIN main_stores ON main_stores.id = medicine_batch_details.target_store_id',
        'LEFT JOIN departments ON departments.id = main_stores.department_id',
        'LEFT JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id',
        'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id',
        'INNER JOIN supplier_category ON supplier_category.id = supplier_bill_basic.supplier_id'
    );

    $table_name  = "supplier_bill_basic";
    $search_type = $this->input->post("search_type") ?? "all_time";
    $hospital_id = $this->input->post('hospital_id');
    $selected_hospital_id = !empty($hospital_id) ? $hospital_id : '';

    $store_id = $this->input->post('store_id');
    $supplier_id = $this->input->post('supplier_id');
    $supplier_type_id = $this->input->post('supplier_type_id');

    $additional_where = [];

    if (!empty($selected_hospital_id)) {
        $additional_where[] = "supplier_bill_basic.hospital_id = '$selected_hospital_id'";
    }

    if (!empty($store_id)) {
        $additional_where[] = "supplier_bill_basic.store_id = '$store_id'";
    }

    if (!empty($supplier_id)) {
        $additional_where[] = "supplier_category.id = '$supplier_id'";
    }

    if (!empty($supplier_type_id)) {
        $additional_where[] = "supplier_category.supplier_type_id = '$supplier_type_id'";
    }

    $additional_where[] = "supplier_category.id IS NOT NULL AND medicine_batch_details.quantity = 0";

    $search_table  = "supplier_bill_basic";
    $search_column = "created_at";

    // ✅ Add GROUP BY to avoid collapsing rows
    $group_by = "supplier_bill_basic.id, 
        supplier_bill_basic.created_at, 
        medicine_batch_details.batch_no, 
        medicine_batch_details.expiry_date, 
        pharmacy.medicine_name, 
        medicine_category.medicine_category, 
        supplier_category.id";

    $resultlist = $this->report_model->searchReportStoreConsumption(
        $select,
        $join,
        $table_name,
        $search_type,
        $search_table,
        $search_column,
        $additional_where,
        [],
        [],
        $group_by  // <- added
    );

    $data["searchlist"]  = $search_type;
    $data["search_type"] = $search_type;
    $data["resultlist"]  = $resultlist['main_data'];

    echo "<pre>";
    print_r($data["resultlist"]);
    exit;

    $data['hospitals'] = $this->common_model->getRecord(null, 'hospitals', '');
    $data["searchlist"]  = $this->search_type;

    if ($this->input->post('search') == 'export_pdf') {
        $this->productExpiryPdf($data);
    } else {
        $this->load->view('layout/header');
        $this->load->view('admin/report/product_out_of_stock', $data);
        $this->load->view('layout/footer');
    }
}

}
