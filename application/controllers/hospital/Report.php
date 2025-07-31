<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Report extends Hospital_Controller
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

        $this->load->model('store_model'); // Load the user model if not already loaded
        $this->marital_status       = $this->config->item('marital_status');
        $this->blood_group          = $this->config->item('bloodgroup');

        // Get session data
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;

        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $this->store_id = isset($user_details->store_id) ? $user_details->store_id : null;
        $this->user_id = $user_id;
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
        COALESCE(pharmacy.medicine_name, "Unknown") AS medicine_name, 
        medicine_category.medicine_category';

        $join = array(
            'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
            'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
            'LEFT JOIN main_stores ON main_stores.id = medicine_batch_details.target_store_id',
            'LEFT JOIN departments ON departments.id = main_stores.department_id',
            'LEFT JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id', // Handle missing pharmacy records
            'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id'
        );

        $table_name  = "supplier_bill_basic";
        $search_type = $this->input->post("search_type") ?? "this_month"; // Default to "this_month"
        $hospital_id = $this->input->post('hospital_id');
        $selected_hospital_id = !empty($this->hospital_id)
            ? $this->hospital_id
            : (!empty($hospital_id) ? $hospital_id : '');

        $additional_where = [
            "supplier_bill_basic.parent_request_id IS NOT NULL",
            "supplier_bill_basic.parent_request_id IN (SELECT id FROM supplier_bill_basic WHERE status = 'approved')"
        ];

        if (!empty($selected_hospital_id)) {
            $additional_where[] = "supplier_bill_basic.hospital_id = '$selected_hospital_id'";
        }

        $search_table  = "supplier_bill_basic"; // Change to correct table
        $search_column = "created_at";
        $resultlist    = $this->report_model->searchReportStoreConsumption($select, $join, $table_name, $search_type, $search_table, $search_column, $additional_where);

        $data["searchlist"]  = $search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist['main_data'];
        $data["selected_hospital_id"]  = $selected_hospital_id;
        $data['hospitals'] = $this->common_model->getRecord($id = null, 'hospitals', $where = '');
        $data["searchlist"]  = $this->search_type;

        if ($this->input->post('search') == 'export_pdf') {
            $this->storeConsumptionPdf($data);
        } else {
            $this->load->view('layout/user/header');
            $this->load->view('store/report/store_consumption_report', $data);
            $this->load->view('layout/user/footer');
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
        $selected_hospital_id = $this->hospital_id;
        $additional_where = [
            "supplier_bill_basic.parent_request_id IS NOT NULL",
            "supplier_bill_basic.parent_request_id IN (SELECT id FROM supplier_bill_basic WHERE status = 'approved')"
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

            $this->load->view('layout/user/header');
            $this->load->view('store/report/store_yearly_consumption_report', $data);
            $this->load->view('layout/user/footer');
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
        $hospital_id = $this->hospital_id;
        $selected_hospital_id = isset($hospital_id) && !empty($hospital_id) ? $hospital_id : '';

        $store_id = $this->store_id; // Get store ID from user input
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
        $additional_where[] = "supplier_bill_basic.supplier_id IS NOT NULL 
            AND TRIM(supplier_bill_basic.supplier_id) != '' 
            AND supplier_category.id IS NOT NULL 
            AND medicine_batch_details.bill_status = 'final'";
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
        $data['supplierTypes'] = $this->supplier_type_model->getSupplierTypes($this->hospital_id, $this->store_id);

        $data["searchlist"]  = $search_type;
        $data["search_type"] = $search_type;
        $data["resultlist"]  = $resultlist['main_data'];
        $data["selected_hospital_id"]  = $selected_hospital_id;
        $data['hospitals'] = $this->common_model->getRecord($id = null, 'hospitals', $where = '');
        $data["searchlist"]  = $this->search_type;
        if ($this->input->post('search') == 'export_pdf') {
            $this->purchaseConsumptionPdf($data);
        } else {

            $this->load->view('layout/user/header');
            $this->load->view('store/report/store_purchase_report', $data);
            $this->load->view('layout/user/footer');
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
        $selected_hospital_id = $this->hospital_id;
        // $selected_store_id = $this->store_id;


        $target_store_id = $this->store_id; // Using target_store_id instead of store_id

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

        if ($this->input->post('search') == 'export_pdf') {
            $this->storeConsumptionPdf($data);
        } else {
            $this->load->view('layout/user/header');
            $this->load->view('store/report/store_opening_stock_report', $data);
            $this->load->view('layout/user/footer');
        }
    }

    public function transferStockReport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/report/storeconsumptionreport');
        $this->load->model('common_model');

        $select = 'hospitals.name as hospital_name, 
            supplier_bill_basic.created_at,  
            medicine_batch_details.batch_no,  
            medicine_batch_details.expiry_date,  
            medicine_batch_details.amount / NULLIF(medicine_batch_details.quantity, 0) AS purchase_price,  
            medicine_batch_details.quantity AS approved_quantity,  
            medicine_batch_details.amount AS amount,  
            COALESCE(pharmacy.medicine_name, "Unknown") AS medicine_name, 
            medicine_category.medicine_category,
            supplier_bill_basic.transfer_store_id,
            supplier_bill_basic.target_store_id';


        $join = array(
            'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
            'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
            'LEFT JOIN main_stores ON main_stores.id = medicine_batch_details.target_store_id',
            'LEFT JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id',
            'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id'
        );

        $table_name  = "supplier_bill_basic";
        $search_type = $this->input->post("search_type") ?? "this_month"; // Default to "this_month"
        $hospital_id = $this->input->post('hospital_id');
        $selected_hospital_id = $this->hospital_id;
        // $selected_store_id = $this->store_id;


        $target_store_id = $this->store_id; // Using target_store_id instead of store_id

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

        if ($this->input->post('search') == 'export_pdf') {
            $this->storeConsumptionPdf($data);
        } else {
            $this->load->view('layout/user/header');
            $this->load->view('store/report/store_opening_stock_report', $data);
            $this->load->view('layout/user/footer');
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

        // **Step 1: Group Data by Month**
        $months = ["AUG", "SEP", "OCT", "NOV", "DEC", "JAN", "FEB", "MAR", "APR", "MAY", "JUN"];
        $grouped_data = [];
        $rates = [];
        $total_amount = 0;

        foreach ($data['resultlist'] as $row) {
            $medicine_name = $row['medicine_name'];
            $month = strtoupper(date('M', strtotime($row['created_at']))); // Extract Month (e.g., AUG)

            if (!isset($grouped_data[$medicine_name])) {
                $grouped_data[$medicine_name] = array_fill_keys($months, 0);
            }

            $grouped_data[$medicine_name][$month] += $row['approved_quantity'];
            $rates[$medicine_name] = $row['purchase_price']; // Store Rate
        }

        // **Step 2: Build PDF HTML**
        $prepareDate = $this->prepareDate();
        $header_date_range = isset($prepareDate['from_date']) && $prepareDate['from_date'] !== 'all_time' ?
            "From " . date('F j, Y', strtotime($prepareDate['from_date'])) . " to " . date('F j, Y', strtotime($prepareDate['to_date'] ?? 'Present')) :
            "All Time";

        $html = '<style>
            body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
            h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background-color: #2980b9; color: white; padding: 8px; font-size: 10px; }
            td { padding: 6px; text-align: center; border: 1px solid #ddd; font-size: 9px; }
            .item-name { text-align: left; padding-left: 10px; font-weight: bold; }
        </style>';

        $html .= '<table>';
        $html .= '<tr><th colspan="15" style="background-color: #2980b9; color: white; text-align: center;">Store Consumption Report (' . $header_date_range . ')</th></tr>';
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

        // **Step 3: Populate Data**
        $sno = 1;
        foreach ($grouped_data as $medicine_name => $monthly_data) {
            $html .= '<tr>';
            $html .= '<td>' . $sno++ . '</td>';
            $html .= '<td class="item-name">' . $medicine_name . '</td>';

            $total_quantity = 0;
            foreach ($months as $month) {
                $quantity = isset($monthly_data[$month]) ? $monthly_data[$month] : 0;
                $html .= '<td>' . $quantity . '</td>';
                $total_quantity += $quantity;
            }

            $rate = isset($rates[$medicine_name]) ? $rates[$medicine_name] : 0;
            $amount = $total_quantity * $rate;
            $total_amount += $amount;

            $html .= '<td><strong>' . $total_quantity . '</strong></td>';
            $html .= '<td><strong>' . number_format($rate, 2) . '</strong></td>';
            $html .= '<td><strong>' . number_format($amount, 2) . '</strong></td>';
            $html .= '</tr>';
        }

        // **Step 4: Add Total Amount Row**
        $html .= '<tr>';
        $html .= '<td colspan="14" style="text-align: right; font-weight: bold;">Total Amount</td>';
        $html .= '<td colspan="2" style="text-align: right; font-weight: bold;">' . number_format($total_amount, 2) . '</td>';
        $html .= '</tr>';

        $html .= '</table>';

        // **Step 5: Generate PDF**
        $this->dompdf->set_paper("A4", "landscape");
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();

        // Add Footer (Time & Page Number)
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
            $this->dompdf->stream("Store_Consumption_Report.pdf", ["Attachment" => 1]);

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

            $dateString = $row['expiry_date'];
            $date = DateTime::createFromFormat('M/Y', $dateString);
            $grouped_data[$medicine_name]['batches'][$batch_number]['quantity'][$row['department_name']] = $row['approved_quantity'];

            if ($date !== false) {
                $grouped_data[$medicine_name]['batches'][$batch_number]['expiry_date'] = $date->format('Y-M-t');
            } else {
                // Handle the invalid date case (optional fallback)
                $grouped_data[$medicine_name]['batches'][$batch_number]['expiry_date'] = 'Invalid Date'; // or null
            }
            // Store batch-wise purchase price
            $grouped_data[$medicine_name]['batches'][$batch_number]['purchase_price'] = $row['purchase_price'];
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
        $html .= '<th colspan="6" class="table-header">MIMS Stock Addition Report Including Local Purchase (LP), Donation, and MSD Supplies <span class="date-range">' . $header_date_range . '</th>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table border="1" cellpadding="5">';
        $html .= '<tr>';
        $html .= '<th colspan="2">' . ucfirst($data['resultlist'][0]['hospital_name']) . '</th>';
        $html .= '<th colspan="' . (count($departments)) . '">Departments</th>';
        $html .= '<th colspan="5">QTY & AMOUNT</th>';
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

            foreach ($medicine_data['batches'] as $batch_number => $batch_data) {
                $html .= '<tr>';
                $html .= '<td>' . $sno++ . '</td>';
                $html .= '<td class="item-name">' . $medicine_name . ' (' . $batch_number . ')' . '</td>';
                $html .= '<td><strong>' . $supplier_name . '</strong></td>';
                $html .= '<td><strong>' . $batch_number . '</strong></td>';
                $html .= '<td><strong>' . (isset($batch_data['expiry_date']) ? date('d-M-Y', strtotime($batch_data['expiry_date'])) : 'N/A') . '</strong></td>';


                $total_quantity = 0;

                foreach ($departments as $dept) {
                    $quantity = isset($batch_data['quantity'][$dept]) ? $batch_data['quantity'][$dept] : 0;
                    $total_quantity += $quantity;
                }

                // Get the batch-specific purchase price
                $rate = isset($batch_data['purchase_price']) && $batch_data['purchase_price'] !== '' ? $batch_data['purchase_price'] : 1;

                $amount = $total_quantity * $rate;
                $total_amount += $amount;

                $html .= '<td><strong>' . $total_quantity . '</strong></td>';
                $html .= '<td><strong>' . number_format($rate, 2) . '</strong></td>';
                $html .= '<td><strong>' . number_format($amount, 2) . '</strong></td>';
                $html .= '</tr>';
            }
        }



        $html .= '<tr>';
        $html .= '<td colspan="' . (count($departments) + 4) . '" style="text-align: right; font-weight: bold;">Total Amount</td>';
        $html .= '<td colspan="3" style="text-align: right; font-weight: bold;">' . number_format($total_amount, 2) . '</td>';
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
        $this->dompdf->stream("Store_Consumption_Report.pdf", ["Attachment" => 1]);
    }

    public function storeConsumptionPdf($data)
    {
        $this->load->library('pdf');
        if (empty($data['resultlist'])) {
            $html = '<style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            h1 { text-align: center; color: #2c3e50; margin-bottom: 10px; }
            .no-records { text-align: center; font-size: 14px; font-weight: bold; color: #e74c3c; }
        </style>';
            $html .= '<h1>Store Opening Stock Report</h1>';
            $html .= '<div class="no-records">No records found</div>';

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->loadHtml($html);
            $this->dompdf->render();
            $this->dompdf->stream("Store_Consumption_Report.pdf", ["Attachment" => 1]);

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

            if (!isset($grouped_data[$medicine_name])) {
                $grouped_data[$medicine_name] = [];
            }

            $grouped_data[$medicine_name][$batch_number]['expiry_date'] = $expiry_date;
            $grouped_data[$medicine_name][$batch_number]['quantities'] = $grouped_data[$medicine_name][$batch_number]['quantities'] ?? [];
            $grouped_data[$medicine_name][$batch_number]['quantities'][$row['department_name']] = $row['approved_quantity'];
            $rates[$medicine_name] = $row['purchase_price'];
        }

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
        $html .= '<tr><th colspan="15" style="background-color: #2980b9; color: white; text-align: center;">Department-Wise Drug Transfer Report – Detailed Summary of Drugs Issued to Each Department (' . $header_date_range . ')</th></tr>';
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
        foreach ($grouped_data as $medicine_name => $batch_data) {
            foreach ($batch_data as $batch_number => $details) {
                $html .= '<tr>';
                $html .= '<td>' . $sno++ . '</td>';
                $html .= '<td class="item-name">' . $medicine_name . '</td>';
                $html .= '<td>' . $batch_number . '</td>';
                $html .= '<td>' . $details['expiry_date'] . '</td>';

                $total_quantity = 0;
                foreach ($departments as $dept) {
                    $quantity = $details['quantities'][$dept] ?? 0;
                    $html .= '<td>' . $quantity . '</td>';
                    $total_quantity += $quantity;
                }

                $rate = $rates[$medicine_name] ?? 0;
                $amount = $total_quantity * $rate;
                $total_amount += $amount;

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
                $data['from_date'] = date('Y', strtotime("-1 year"));
                break;
            case 'this_year':
                $data['from_date'] = date('Y');
                break;
            case 'all_time':
                $data['from_date'] = 'ALL'; // Set to 'ALL' for all-time reports
                break;
        }

        return $data;
    }

  public function productExpiryReport()
{
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
        medicine_batch_details.amount / NULLIF(medicine_batch_details.quantity, 0) AS purchase_price, 
        medicine_category.medicine_category';

    $join = array(
        'JOIN hospitals ON hospitals.id = supplier_bill_basic.hospital_id',
        'LEFT JOIN medicine_batch_details ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id',
        'JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id',
        'LEFT JOIN medicine_category ON medicine_batch_details.medicine_category_id = medicine_category.id'
    );

    $table_name = "supplier_bill_basic";

    $search_type = $this->input->post("search_type") ?? "this_week";
    $hospital_id = $this->hospital_id;
    $near_expiry_days = $this->input->post('near_expiry_days') ?? 30;

    $store_id = $this->store_id;
    $role = $this->session->userdata('hospital')['role'];

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

    if ($store_id && ($role === 'Store In-Charge' || $role === 'Chief Pharmacist')) {
        $additional_where[] = "medicine_batch_details.store_id = '$store_id'";
    }

    if ($store_id && $role === 'Department Pharmacist') {
        $additional_where[] = "medicine_batch_details.target_store_id = '$store_id'";
    }

    if ($search_type === 'this_week') {
        $start_date = date('Y-m-d H:i:s', strtotime('last monday 00:00:00'));
        $end_date = date('Y-m-d H:i:s', strtotime('next sunday 23:59:59'));
    } elseif ($search_type === 'today') {
        $start_date = $end_date = date('Y-m-d');
    } elseif ($search_type === 'this_month') {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t 23:59:59.993');
    } elseif ($search_type === 'last_month') {
        $start_date = date('Y-m-01', strtotime('first day of last month'));
        $end_date = date('Y-m-t 23:59:59.993', strtotime('last day of last month'));
    } elseif ($search_type === 'next_month') {
        $start_date = date('Y-m-01', strtotime('first day of next month'));
        $end_date = date('Y-m-t 23:59:59.993', strtotime('last day of next month'));
    } else {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+$near_expiry_days days"));
    }

    $group_by = "medicine_batch_details.pharmacy_id, medicine_batch_details.batch_no, hospitals.name, supplier_bill_basic.status, supplier_bill_basic.id, supplier_bill_basic.parent_request_id, supplier_bill_basic.created_at, pharmacy.medicine_name, medicine_category.medicine_category";
    $having = "MAX(medicine_batch_details.expiry_date_format) BETWEEN '$start_date' AND '$end_date'";

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

    $data["searchlist"] = $this->search_type;
    $data["search_type"] = $search_type;
    $data["resultlist"] = $resultlist['main_data'];
    $data['hospitals'] = $this->common_model->getRecord(null, 'hospitals', '');

    if ($this->input->post('search') == 'export_pdf') {
        $this->productExpiryPdf($data);
    } else {
        $this->load->view('layout/user/header');
        $this->load->view('store/report/product_expiry_report', $data);
        $this->load->view('layout/user/footer');
    }
}


    public function patientSummaryReport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'admin/report/storeconsumptionreport');
        $this->load->model('common_model');

        $search_type = $this->input->post("search_type") ?? "all";
        $ward_id = $this->input->post('ward_id');
        $patient_type = $this->input->post('patient_type');
        $gender = $this->input->post('gender');
        
        $selected_ward_id = isset($ward_id) && !empty($ward_id) ? $ward_id : '';        
        $selected_patient_type = isset($patient_type) && !empty($patient_type) ? $patient_type : '';        
        $selected_gender = isset($gender) && !empty($gender) ? $gender : '';        
        
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
            $start_date = date('Y-m-01', strtotime('first day of next month'));
            $end_date = date('Y-m-t 23:59:59.993', strtotime('last day of next month'));
        } else if($search_type == "period"){
            $start_date = date('Y-m-d', strtotime($this->input->post('date_from'))). ' 00:00:00'; 
            $end_date = date('Y-m-d', strtotime($this->input->post('date_to'))). ' 23:59:59';
        } 
         
        $this->db->select('*'); 
        $this->db->where('name', $_SESSION['hospital']['hospital_name']); 
        $hospital = $this->db->get('hospitals')->row_array();

        $this->db->select('*'); 
        $this->db->where('entity_id', $hospital['id']);
        if($_SESSION['hospital']['role'] != "Store In-Charge"){
            $this->db->where('entity_type', 'department');
            if(isset($ward_id) && !empty($ward_id)){
                $this->db->where('id', $ward_id);
            }else{
                $this->db->where('store_name',$_SESSION['hospital']['store_name']);
            }
        } 
        $wards = $this->db->get('main_stores')->result_array(); 

        $data = array();
        $count = 0;
        foreach($wards as $w=>$single_ward){
                
            $national =  $other_national =  $Male = $Female =  $Other = $IPD =  $OPD = array();

            if(isset($patient_type) && !empty($patient_type)) 
            {
                $this->db->select('Count(id) as count'); 
                $this->db->where('hospital_id', $single_ward['entity_id']); 
                $this->db->where('store_id', $single_ward['id']); 
                $this->db->where('other_nationality', ''); 
                if($patient_type == "national"){ 
                        $this->db->where('other_nationality', '');
                    }else{
                        $this->db->where('other_nationality !=', '');
                    }
                    if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                        $this->db->where('created_at >=', $start_date);
                        $this->db->where('created_at <=', $end_date);
                    }else if (isset($start_date)  && !empty($start_date)){
                        $this->db->where('created_at >=', $start_date);
                    }else if (isset($end_date)  && !empty($end_date)){
                        $this->db->where('created_at >=', $end_date);
                    }
                    
                    if($patient_type == "national"){ 
                        $national = $this->db->get('patients')->row_array();
                        $national['national'] = $national['count'];
                    }else if($patient_type == "other_national"){
                        $other_national = $this->db->get('patients')->row_array();
                        $other_national['other_national'] = $other_national['count'];
                    }  
            }else{
            
                $this->db->select('Count(id) as national'); 
                $this->db->where('hospital_id', $single_ward['entity_id']); 
                $this->db->where('store_id', $single_ward['id']); 
                $this->db->where('other_nationality', ''); 
                if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                    $this->db->where('created_at >=', $start_date);
                    $this->db->where('created_at <=', $end_date);
                }else if (isset($start_date)  && !empty($start_date)){
                    $this->db->where('created_at >=', $start_date);
                }else if (isset($end_date)  && !empty($end_date)){
                    $this->db->where('created_at >=', $end_date);
                }

                $national = $this->db->get('patients')->row_array();
                
                $this->db->select('Count(id) as other_national'); 
                $this->db->where('hospital_id', $single_ward['entity_id']); 
                $this->db->where('store_id', $single_ward['id']); 
                $this->db->where('other_nationality !=', ''); 
                if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                    $this->db->where('created_at >=', $start_date);
                    $this->db->where('created_at <=', $end_date);
                }else if (isset($start_date)  && !empty($start_date)){
                    $this->db->where('created_at >=', $start_date);
                }else if (isset($end_date)  && !empty($end_date)){
                    $this->db->where('created_at >=', $end_date);
                }
                $other_national = $this->db->get('patients')->row_array(); 
            }

            if(isset($gender) && !empty($gender)) 
            { 
                $this->db->select('Count(id) as count'); 
                $this->db->where('hospital_id', $single_ward['entity_id']); 
                $this->db->where('store_id', $single_ward['id']); 
                if($gender == "male"){ 
                    $this->db->where('gender', 'Male'); 
                }else if($gender == "female"){
                    $this->db->where('gender', 'Female'); 
                }else if($gender == "other"){
                    $this->db->where('gender', 'Other'); 
                }
                if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                    $this->db->where('created_at >=', $start_date);
                    $this->db->where('created_at <=', $end_date);
                }else if (isset($start_date)  && !empty($start_date)){
                    $this->db->where('created_at >=', $start_date);
                }else if (isset($end_date)  && !empty($end_date)){
                    $this->db->where('created_at >=', $end_date);
                }
                if($gender == "male"){ 
                    $Male = $this->db->get('patients')->row_array();
                    $Male['Male'] = $Male['count'];
                }else if($gender == "female"){
                    $Female = $this->db->get('patients')->row_array();
                    $Female['Female'] = $Female['count'];
                }else if($gender == "other"){
                    $Other = $this->db->get('patients')->row_array();
                    $Other['Other'] = $Other['count'];
                }
            }else{
                
                $this->db->select('Count(id) as Male'); 
                $this->db->where('hospital_id', $single_ward['entity_id']); 
                $this->db->where('store_id', $single_ward['id']); 
                $this->db->where('gender', 'Male'); 
                if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                    $this->db->where('created_at >=', $start_date);
                    $this->db->where('created_at <=', $end_date);
                }else if (isset($start_date)  && !empty($start_date)){
                    $this->db->where('created_at >=', $start_date);
                }else if (isset($end_date)  && !empty($end_date)){
                    $this->db->where('created_at >=', $end_date);
                }
                $Male = $this->db->get('patients')->row_array();
                    
                $this->db->select('Count(id) as Female'); 
                $this->db->where('hospital_id', $single_ward['entity_id']); 
                $this->db->where('store_id', $single_ward['id']); 
                $this->db->where('gender', 'Female');
                if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                    $this->db->where('created_at >=', $start_date);
                    $this->db->where('created_at <=', $end_date);
                }else if (isset($start_date)  && !empty($start_date)){
                    $this->db->where('created_at >=', $start_date);
                }else if (isset($end_date)  && !empty($end_date)){
                    $this->db->where('created_at >=', $end_date);
                } 
                $Female = $this->db->get('patients')->row_array();
                
                $this->db->select('Count(id) as Other'); 
                $this->db->where('hospital_id', $single_ward['entity_id']); 
                $this->db->where('store_id', $single_ward['id']); 
                $this->db->where('gender', 'Other');
                if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                    $this->db->where('created_at >=', $start_date);
                    $this->db->where('created_at <=', $end_date);
                }else if (isset($start_date)  && !empty($start_date)){
                    $this->db->where('created_at >=', $start_date);
                }else if (isset($end_date)  && !empty($end_date)){
                    $this->db->where('created_at >=', $end_date);
                }
                $Other = $this->db->get('patients')->row_array();
            }

            $this->db->select('Count(id) as IPD'); 
            $this->db->where('hospital_id', $single_ward['entity_id']); 
            $this->db->where('store_id', $single_ward['id']); 
            $this->db->where('is_ipd !=', '');
            if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                $this->db->where('created_at >=', $start_date);
                $this->db->where('created_at <=', $end_date);
            }else if (isset($start_date)  && !empty($start_date)){
                $this->db->where('created_at >=', $start_date);
            }else if (isset($end_date)  && !empty($end_date)){
                $this->db->where('created_at >=', $end_date);
            } 
            $IPD = $this->db->get('patients')->row_array();

            $this->db->select('Count(id) as OPD'); 
            $this->db->where('hospital_id', $single_ward['entity_id']); 
            $this->db->where('store_id', $single_ward['id']); 
            $this->db->where('is_ipd', ''); 
            if(isset($start_date)  && !empty($start_date) && isset($end_date) && !empty($end_date)){
                $this->db->where('created_at >=', $start_date);
                $this->db->where('created_at <=', $end_date);
            }else if (isset($start_date)  && !empty($start_date)){
                $this->db->where('created_at >=', $start_date);
            }else if (isset($end_date)  && !empty($end_date)){
                $this->db->where('created_at >=', $end_date);
            }
            $OPD = $this->db->get('patients')->row_array();

             
            
            $data[$count]['hospital_id'] = $hospital['id'];
            $data[$count]['hospital_name'] = $hospital['name'];
            $data[$count]['store_id'] = $single_ward['id'];
            $data[$count]['store_name'] = $single_ward['store_name']; 
            $data[$count]['national'] = $national['national'];
            $data[$count]['other_national'] = $other_national['other_national'];
            $data[$count]['Male'] = $Male['Male'];
            $data[$count]['Female'] = $Female['Female'];
            $data[$count]['Other'] = $Other['Other'];
            $data[$count]['IPD'] = $IPD['IPD'];
            $data[$count]['OPD'] = $OPD['OPD'];
            
            $count++;
        } 

        $data["resultlist"] = $data;
        $data["searchlist"] = $this->search_type;
        $data['from_date'] = isset($start_date) ? $start_date : '';
        $data['to_date'] = isset($end_date) ? $end_date : '';
        $data["search_type"] = $search_type;
        $data["selected_ward_id"] = $selected_ward_id;
        $data["selected_patient_type"] = $selected_patient_type;
        $data["selected_gender"] = $selected_gender;
        $data['hospitals'] = $this->common_model->getRecord(null, 'hospitals', '');
        if($_SESSION['hospital']['role'] == "Store In-Charge"){
            $where = "entity_type = 'department' AND entity_id = ". $hospital['id'];
        }else{
            $where = "store_name = '" .$_SESSION['hospital']['store_name'] . "'";
        }
        $data['main_stores'] = $this->common_model->getmultipleRecords('main_stores', $where); 
        
        if ($this->input->post('search') == 'export_pdf') {
            $this->productExpiryPdf($data);
        } else {
           $this->load->view('layout/user/header');
            $this->load->view('store/report/patient_summary_report', $data);
            $this->load->view('layout/user/footer');
        }
    }

}
