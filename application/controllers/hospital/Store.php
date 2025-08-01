<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Store extends Hospital_Controller
{

    public function __construct()
    {
        parent::__construct();


        // Get session data
        $session_data = $this->session->userdata('hospital');
        $user_id = isset($session_data['id']) ? $session_data['id'] : null;
        $this->load->model('store_model'); // Load the user model if not already loaded
        $this->marital_status       = $this->config->item('marital_status');
        $this->blood_group          = $this->config->item('bloodgroup');
        // Fetch user details from the database
        $user_details = $this->user_model->getUserById($user_id); // Assuming getUserById() returns an object

        // Initialize class properties
        $this->hospital_id = isset($user_details->hospital_id) ? $user_details->hospital_id : null;
        $this->store_id = isset($user_details->store_id) ? $user_details->store_id : null;
        $this->user_id = $user_id;
    }
    public function pharmacy()
    {
        // if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
        //     access_denied();
        // }
        // $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        // $data["medicineCategory"] = $medicineCategory;
        // $resultlist               = $this->pharmacy_model->searchFullText();
        // $i                        = 0;
        // foreach ($resultlist as $value) {
        //     $pharmacy_id                 = $value['id'];
        //     $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
        //     $totalAvailableQty           = $available_qty['total_qty'];
        //     $resultlist[$i]["total_qty"] = $totalAvailableQty;
        //     $i++;
        // }
        // $result             = $this->pharmacy_model->getPharmacy();
        // $data['resultlist'] = $resultlist;
        // $data['result']     = $result;
        //   echo "<pre>";  print_r($data);exit;
        $this->load->view('layout/user/header');
        $this->load->view('store/storeStock/index');
        $this->load->view('layout/user/footer');
    }
    public function dt_search()
    {

        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $hospital_id = $this->hospital_id;
        $store_id    = $this->store_id;

        $resultlist   = $this->pharmacy_model->store_medicine_search_datatable($where_condition, $hospital_id, $store_id);
        // echo $this->db->last_query();exit;
        $total_result = $this->pharmacy_model->store_medicine_search_datatable_count($where_condition, $hospital_id, $store_id);
        $data         = array();
        $total_qty = 0;
        $total_purchase = 0;
        $total_sale = 0;
        foreach ($resultlist as $result_key => $result_value) {
            $status = "";


            $nestedData = array();
            $action     = "<div class='rowoptionview'>";
            // $action .= "<a href='#' onclick='viewDetail(" . $result_value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
            // if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_add')) {
            //     $action .= "<a href='#' class='btn btn-default btn-xs' onclick='addbadstock(" . $result_value->id . ")' data-toggle='tooltip' title='" . $this->lang->line('add') . ' ' . $this->lang->line('bad') . ' ' . $this->lang->line('stock') . "' > <i class='fas fa-minus-square'></i> </a>";
            // }


            $action .= "<div'>";

            $nestedData[] = $result_value->medicine_name . $action;
            $nestedData[] = $result_value->medicine_company;
            $nestedData[] = $result_value->medicine_category;
            $nestedData[] = $result_value->total_qty;


            $data[]       = $nestedData;
        }





        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }

    public function medicineRequest()
    {
        $user_status = $this->input->get('type'); // Get the user-friendly status from the UI

        // // Map user-friendly status to database status
        $status_map = [
            'pending' => 'requested',
            'approved' => 'approved',
            'partial' => 'partial',
            'rejected' => 'rejected',
        ];

        if (!array_key_exists($user_status, $status_map)) {
            echo json_encode(['error' => 'Invalid status']);
            return;
        }

        $db_status = $status_map[$user_status]; // Get the corresponding database status
        // 
        // Fetch bills with the mapped database status
        $data['requests'] = $this->pharmacy_model->getBillsByStatus($db_status, $this->hospital_id, $this->store_id);
        // Return the data as a JSON response
        // if (!$this->rbac->hasPrivilege('medicine', 'can_add')) {
        //     access_denied();
        // }
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $this->load->view('layout/user/header');
        $this->load->view('store/request/index', $data);
        $this->load->view('layout/user/footer');
    }

    public function createRequest()
    {
        // if (!$this->rbac->hasPrivilege('medicine', 'can_add')) {
        //     access_denied();
        // }
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['hospitalStores'] = $this->store_model->getHospitalDepartmentStores('Hospital', $this->hospital_id);
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $this->load->view('layout/user/header');
        $this->load->view('store/request/create.php', $data);
        $this->load->view('layout/user/footer');
    }



    public function saveRequest()
    {
        $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
        $status = $isDraft ? 'draft' : 'final';
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('store_id', $this->lang->line('store'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'quantity'             => form_error('quantity[]'),
                'store_id'             => form_error('store_id'),
                'date'                 => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $store_id   = $this->input->post('store_id'); // Target store ID
            $store_name = $this->input->post('store_name');
            $bill_date  = $this->input->post('date');
            $purchase_no = $this->pharmacy_model->getMaxId() ?? 0;
            $purchase = $purchase_no + 1;

            // Insert request in `supplier_bill_basic`
            $data = array(
                'date'             => date('Y-m-d H:i:s', strtotime($bill_date)),
                'target_store_id'  => $this->store_id, // Target store requesting stock
                'store_name'       => $store_name,
                'invoice_no'       => $this->input->post('bill_no'),
                'purchase_no'      => $purchase,
                'note'             => $this->input->post('note'),
                'hospital_id'      => $this->hospital_id,
                'user_id'          => $this->user_id,
                'transfer_store_id' => $store_id, // Source store
                'request_type'     => 'request', // Request type
                'status'           => 'requested', // Initial status
                'bill_status' => $status
            );
            $insert_id = $this->pharmacy_model->addBillSupplier($data);

            if ($insert_id) {
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $quantity             = $this->input->post('quantity');
                $data1 = array();
                $j     = 0;

                // Add medicine details to `medicine_batch_details`
                foreach ($medicine_name as $key => $mvalue) {
                    $details = array(
                        'inward_date'         => date('Y-m-d H:i:s', strtotime($bill_date)),
                        'pharmacy_id'         => $medicine_name[$j],
                        'supplier_bill_basic_id' => $insert_id, // Link to the bill
                        'medicine_category_id' => $medicine_category_id[$j],
                        'quantity'            => $quantity[$j], // Requested quantity
                        'available_quantity'  => 0, // No stock transferred yet
                        'operation_type'      => 'request', // Request type
                        'status'              => 'requested', // Requested status
                        'hospital_id'         => $this->hospital_id,
                        'user_id'             => $this->user_id,
                        'target_store_id'     => $store_id,
                        'transfer_store_id'   => $this->store_id,
                        'bill_status' => $status

                    );
                    $data1[] = $details;
                    $j++;
                }

                // Insert batch details
                $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
        }
        echo json_encode($array);
    }

    public function updateRequest()
    {
        // print_r($_POST);exit;
        $isDraft = $this->input->post('is_draft'); // Check if the request is for draft
        $status = $isDraft ? 'draft' : 'final';
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('store_id', $this->lang->line('store'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'quantity'             => form_error('quantity[]'),
                'store_id'             => form_error('store_id'),
                'date'                 => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $bill_id   = $this->input->post('basic_bill_id'); // Target store ID

            $store_id   = $this->input->post('store_id'); // Target store ID
            $store_name = $this->input->post('store_name');
            $bill_date  = $this->input->post('date');
            $purchase_no = $this->pharmacy_model->getMaxId() ?? 0;
            $purchase = $purchase_no + 1;

            // Insert request in `supplier_bill_basic`
            $data = array(
                'id' => $bill_id,
                'date'             => date('Y-m-d H:i:s', strtotime($bill_date)),
                'target_store_id'  => $this->store_id, // Target store requesting stock
                'store_name'       => $store_name,
                'invoice_no'       => $this->input->post('bill_no'),
                'purchase_no'      => $purchase,
                'note'             => $this->input->post('note'),
                'hospital_id'      => $this->hospital_id,
                'user_id'          => $this->user_id,
                'transfer_store_id' => $store_id, // Source store
                'request_type'     => 'request', // Request type
                'status'           => 'requested', // Initial status
                'bill_status' => $status
            );
            $insert_id = $this->pharmacy_model->addBillSupplier($data);
            if ($bill_id) {
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $quantity             = $this->input->post('quantity');
                $data1 = array();
                $j     = 0;
                $this->pharmacy_model->deleteBillMedicineBatchSupplier($bill_id);                // Add medicine details to `medicine_batch_details`
                foreach ($medicine_name as $key => $mvalue) {
                    $details = array(
                        'inward_date'         => date('Y-m-d H:i:s', strtotime($bill_date)),
                        'pharmacy_id'         => $medicine_name[$j],
                        'supplier_bill_basic_id' => $bill_id, // Link to the bill
                        'medicine_category_id' => $medicine_category_id[$j],
                        'quantity'            => $quantity[$j], // Requested quantity
                        'available_quantity'  => 0, // No stock transferred yet
                        'operation_type'      => 'request', // Request type
                        'status'              => 'requested', // Requested status
                        'hospital_id'         => $this->hospital_id,
                        'user_id'             => $this->user_id,
                        'target_store_id'     => $store_id,
                        'transfer_store_id'   => $this->store_id,
                        'bill_status' => $status

                    );
                    $data1[] = $details;
                    $j++;
                }

                // Insert batch details
                $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
        }
        echo json_encode($array);
    }

    public function request_list()
    {

        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $hospital_id = $this->hospital_id;
        $store_id    = $this->store_id;

        $resultlist   = $this->pharmacy_model->store_medicine_search_datatable($where_condition, $hospital_id, $store_id);
        // echo $this->db->last_query();exit;
        $total_result = $this->pharmacy_model->store_medicine_search_datatable_count($where_condition, $hospital_id, $store_id);
        $data         = array();
        $total_qty = 0;
        $total_purchase = 0;
        $total_sale = 0;
        print_r($resultlist);
        exit;
        foreach ($resultlist as $result_key => $result_value) {
            $status = "";


            $nestedData = array();
            $action     = "<div class='rowoptionview'>";
            // $action .= "<a href='#' onclick='viewDetail(" . $result_value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
            // if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_add')) {
            //     $action .= "<a href='#' class='btn btn-default btn-xs' onclick='addbadstock(" . $result_value->id . ")' data-toggle='tooltip' title='" . $this->lang->line('add') . ' ' . $this->lang->line('bad') . ' ' . $this->lang->line('stock') . "' > <i class='fas fa-minus-square'></i> </a>";
            // }


            $action .= "<div'>";

            $nestedData[] = $result_value->medicine_name;
            $nestedData[] = $result_value->medicine_company;
            $nestedData[] = $result_value->medicine_category;
            $nestedData[] = $result_value->total_qty;


            $data[]       = $nestedData;
        }





        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }


    public function viewRequest()
    {
        // Get the request ID from the query string
        $id = $this->input->get('id');

        // Validate the ID
        if (empty($id) || !is_numeric($id)) {
            show_error('Invalid request ID.');
        }

        // Fetch the main request details
        $data['request'] = $this->pharmacy_model->getRequestById($id);

        // Fetch the detailed batch items related to this request
        $data['batch_details'] = $this->pharmacy_model->getRequestBatchDetails($id);

        // Check if the request exists
        if (empty($data['request'])) {
            show_error('Request not found.');
        }

        // Load the view with the fetched data
        $this->load->view('layout/user/header');
        $this->load->view('store/request/view', $data);
        $this->load->view('layout/user/footer');
    }


    public function requests($status = "")
    {

        $data['requests'] = $this->pharmacy_model->getStoresRequest($this->hospital_id, $this->store_id, $status);
        //    echo "<pre>"; print_r($data);exit;
        // Load the view with the fetched data
        $this->load->view('layout/user/header');
        $this->load->view('storeRequests/index', $data);
        $this->load->view('layout/user/footer');
    }

    public function assignMedicine()
    {
        $id = $this->input->get('id');
        $medicineCategory             = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"]     = $medicineCategory;
        // $medicine_category_id         = $this->input->post("medicine_category_id");
        // $data['medicine_category_id'] = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        // $data['medicine_category_id'] = $medicine_category_id;
        $result                       = $this->pharmacy_model->getBillDetail($id);
        $data['result']               = $result;
        // $detail                       = $this->pharmacy_model->getAllBillDetails($id);
        // $data['detail']               = $detail;

        $result         = $this->pharmacy_model->getStoreRequestBatchDetails($id);
        $data['detail'] = $result;
        //    echo "<pre>"; print_r($data);exit;
        // $this->load->view("admin/pharmacy/editPharmacyBill", $data);

        $this->load->view('layout/user/header');
        $this->load->view('storeRequests/assignMedicine', $data);
        $this->load->view('layout/user/footer');
    }
    public function convertMonthToNumber($monthName)
    {
        return date('m', strtotime($monthName));
    }

    public function assignMedicineToStore()
    {
        // print_r($_POST);exit;
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('pharmacy_id[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', 'Purchase Price', 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        $this->form_validation->set_rules('store_id', $this->lang->line('store'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'pharmacy_id'        => form_error('pharmacy_id[]'),
                'batch_no'             => form_error('batch_no[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'amount'               => form_error('amount[]'),
                'total'                => form_error('total'),
                'store_id'             => form_error('store_id'),
                'date'                 => form_error('date'),
            );
            echo json_encode(['status' => 'fail', 'error' => $msg, 'message' => '']);
            return;
        }

        $bill_id = $this->input->post('bill_id'); // Original request bill ID
        $requestDetail = $this->pharmacy_model->getRequestById($bill_id);
        if ($requestDetail->status == 'approved') {
            echo json_encode(['status' => 'fail', 'message' => 'Request has already been approved']);
            return;
        }

        $store_id = $this->input->post('store_id'); // Target store ID
        $bill_date = $this->input->post('date');

        // Fetch request batch details
        $original_batches = $this->pharmacy_model->getStoreRequestBatchDetails($bill_id);
        if (empty($original_batches)) {
            echo json_encode(['status' => 'fail', 'message' => 'No batch details found for the request']);
            return;
        }

        // Fetch input data
        $medicine_name        = $this->input->post('pharmacy_id');
        $medicine_category_id = $this->input->post('medicine_category_id');
        $expiry_date          = $this->input->post('expiry_date');
        $batch_no             = $this->input->post('batch_no');
        $batch_id             = $this->input->post('batch_id'); // Now this contains multiple rows per batch
        $quantity             = $this->input->post('quantity');
        $purchase_price       = $this->input->post('purchase_price');
        $amount               = $this->input->post('amount');
        $total_quantity       = $this->input->post('available_quantity');
        $medicine_batch_details_id = $this->input->post('id');

        $remaining_batches = 0;
        // ✅ Track cumulative approved quantities for each batch ID
        $batch_approved_quantities = [];
        $batch_details_cache = []; // ✅ Store fetched batch details
        
        foreach ($medicine_name as $key => $mvalue) {
            $batchKey = $batch_id[$key]; // Batch ID as key
            $approved_quantity = $quantity[$key] ?? 0;
        
            // Fetch batch details once per batch_id (cache it)
            if (!isset($batch_details_cache[$batchKey])) {
                $singleBatch = $this->pharmacy_model->getBatchDetail($batchKey);
        
                if (!$singleBatch) {
                    echo json_encode(['status' => 'fail', 'message' => 'Invalid batch ID: ' . $batchKey]);
                    return;
                }
        
                // Store batch details in cache
                $batch_details_cache[$batchKey] = $singleBatch;
            }
        
            // ✅ Initialize total approved quantity for this batch if not set
            if (!isset($batch_approved_quantities[$batchKey])) {
                // ✅ Start with the existing approved quantity
                $batch_approved_quantities[$batchKey] = $batch_details_cache[$batchKey]['approved_quantity'] ?? 0;
            }
        
            // ✅ Accumulate approved quantity for the same batch (increment)
            $batch_approved_quantities[$batchKey] += $approved_quantity;
        }
        
        $isPartial = false;
        
        // ✅ Update batch details only once per batch_id
        foreach ($batch_approved_quantities as $batchKey => $totalApprovedQty) {
            $singleBatch = $batch_details_cache[$batchKey]; // ✅ Use cached batch details
        
            // ✅ Ensure remaining quantity calculation is correct
            $remaining_quantity = $singleBatch['quantity'] - $totalApprovedQty;
            $batch_status = ($remaining_quantity > 0) ? 'partially_transferred' : 'fully_transferred';
            $isPartial = $remaining_quantity > 0 ? true : false;
        
            // ✅ Update batch details once per batch with incremented approved quantity
            $this->pharmacy_model->updateBatchDetails($batchKey, [
                'approved_quantity' => $totalApprovedQty, // ✅ Incremented quantity
                'status' => $batch_status,
                'operation_type' => 'request',
            ]);
        }
        
        // ✅ **Mark the bill as approved**
        $updateData = ['status' => $isPartial ? 'partial' : 'approved'];
        $this->pharmacy_model->updateBillStatus($bill_id, $updateData);

        // ✅ **Create a new supplier bill**
        $purchase_no = $this->pharmacy_model->getMaxId() ?? 0;
        $purchase = $purchase_no + 1;

        $new_bill_data = array(
            'date' => date('Y-m-d H:i:s', strtotime($bill_date)),
            'target_store_id' => $store_id,
            'invoice_no' => $this->input->post('bill_no'),
            'purchase_no' => $purchase,
            'total' => $this->input->post('total'),
            'net_amount' => 0,
            'note' => $this->input->post('note'),
            'hospital_id' => $this->hospital_id,
            'user_id' => $this->user_id,
            'transfer_store_id' => $this->store_id,
            'request_type' => 'transfer',
            'parent_request_id' => $bill_id,
            'bill_status' => 'final'
        );

        $new_bill_id = $this->pharmacy_model->addBillSupplier($new_bill_data);
        
        if ($new_bill_id) {
            $data1 = array();
            $j = 0;
            $updateArray = [];
            foreach ($medicine_name as $key => $mvalue) {
                // ✅ Ensure all required indices exist before accessing them
                $expdate = isset($expiry_date[$j]) ? $expiry_date[$j] : null;
                $med_category_id = isset($medicine_category_id[$j]) ? $medicine_category_id[$j] : null;
                $batch_no_value = isset($batch_no[$j]) ? $batch_no[$j] : null;
                $qty = isset($quantity[$j]) ? $quantity[$j] : 0;
                $amount_value = isset($amount[$j]) ? $amount[$j] : 0;
                $med_batch_details_id = isset($medicine_batch_details_id[$j]) ? $medicine_batch_details_id[$j] : null;
                $total_qty = isset($total_quantity[$j]) ? $total_quantity[$j] : 0;
            
                $details = array(
                    'inward_date'          => date('Y-m-d H:i:s', strtotime($bill_date)),
                    'pharmacy_id'          => isset($medicine_name[$j]) ? $medicine_name[$j] : null,
                    'supplier_bill_basic_id' => isset($new_bill_id) ? $new_bill_id : null,
                    'medicine_category_id' => $med_category_id,
                    'expiry_date'          => $expdate,
                    'batch_no'             => $batch_no_value,
                    'quantity'             => $qty,
                    'available_quantity'   => $qty, // ✅ Set available quantity safely
                    'amount'               => $amount_value,
                    'purchase_price'=>isset($purchase_price[$j]) ? $purchase_price[$j] : 0,
                    'hospital_id'          => isset($this->hospital_id) ? $this->hospital_id : null,
                    'user_id'              => isset($this->user_id) ? $this->user_id : null,
                    'target_store_id'      => isset($store_id) ? $store_id : null,
                    'transfer_store_id'    => isset($this->store_id) ? $this->store_id : null,
                    'bill_status'          => ($qty > 0) ? 'final' : 'draft' // ✅ Ensure bill_status logic is applied correctly
                );
            
                                // ✅ Ensure available quantity is updated properly
                // Ensure total quantity and quantity are numeric
                $total_qty = isset($total_quantity[$j]) && is_numeric($total_quantity[$j]) ? (float) $total_quantity[$j] : 0;
                $qty = isset($quantity[$j]) && is_numeric($quantity[$j]) ? (float) $quantity[$j] : 0;

                // ✅ Now subtraction will always work without errors
                $available_qty = $total_qty - $qty;
                $update_quantity = array(
                    'id'                 => $med_batch_details_id,
                    'available_quantity' => $available_qty,
                );
                $updateArray[] = $update_quantity;
              
                // ✅ Update available quantity safely
                if (!is_null($med_batch_details_id)) {
                    $this->pharmacy_model->availableQty($update_quantity);
                }
            
                $data1[] = $details;
                $j++;
            }
            // print_r($updateArray);exit;
            

            $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
        }

        echo json_encode(['status' => 'success', 'message' => 'Stock transferred successfully', 'new_bill_id' => $new_bill_id]);
    }






    // public function assignMedicineToStore(){

    //     $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine') . " " . $this->lang->line('category'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine') . " " . $this->lang->line('name'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch') . " " . $this->lang->line('no'), 'required');
    //     $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
    //     $this->form_validation->set_rules('purchase_price[]', 'Purchase Price', 'required|numeric');
    //     $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
    //     $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
    //     $this->form_validation->set_rules('store_id', $this->lang->line('store'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required');

    //     if ($this->form_validation->run() == false) {
    //         $msg = array(
    //             'medicine_category_id' => form_error('medicine_category_id[]'),
    //             'medicine_name'        => form_error('medicine_name[]'),
    //             'batch_no'             => form_error('batch_no[]'),
    //             'quantity'             => form_error('quantity[]'),
    //             'purchase_price'           => form_error('purchase_price[]'),
    //             'amount'               => form_error('amount[]'),
    //             'total'                => form_error('total'),
    //             'store_id'             => form_error('store_id'),
    //             'date'                 => form_error('date'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
    //     } else {
    //         $store_id   = $this->input->post('store_id'); // Target store ID
    //         $bill_date  = $this->input->post('date');
    //         $bill_id  = $this->input->post('bill_id');

    //         $purchase_no = $this->pharmacy_model->getMaxId() ?? 0;

    //         $purchase = $purchase_no + 1;

    //         $data = array(
    //             'date'             => date('Y-m-d H:i:s', strtotime($bill_date)),
    //             'target_store_id'  => $store_id,
    //             'invoice_no'       => $this->input->post('bill_no'),
    //             'purchase_no'      => $purchase,
    //             'total'            => $this->input->post('total'),
    //             'net_amount'       => $this->input->post('net_amount'),
    //             'note'             => $this->input->post('note'),
    //             'hospital_id'      => $this->hospital_id,
    //             'user_id'          => $this->user_id,
    //             'transfer_store_id'=> $this->store_id, // Source store ID
    //             'request_type'=>'transfered',
    //             'parent_request_id'=>$bill_id
    //         );
    //         $insert_id = $this->pharmacy_model->addBillSupplier($data);

    //         if ($insert_id) {
    //             $medicine_category_id = $this->input->post('medicine_category_id');
    //             $medicine_name        = $this->input->post('medicine_name');
    //             $expiry_date          = $this->input->post('expire_date');
    //             $batch_no             = $this->input->post('batch_no');
    //             $quantity             = $this->input->post('quantity');
    //             $amount               = $this->input->post('amount');
    //             $total_quantity       = $this->input->post('available_quantity');
    //             $medicine_batch_details_id = $this->input->post('id');
    //             $data1 = array();
    //             $j     = 0;
    //             foreach ($medicine_name as $key => $mvalue) {
    //                 if (!isset($expiry_date[$j]) || empty($expiry_date[$j])) {
    //                     $msg = 'Expiry date is missing for medicine: ' . $mvalue;
    //                     $array = array('status' => 'fail', 'error' => '', 'message' => $msg);
    //                     echo json_encode($array);
    //                     return; // Stop further processing
    //                 }

    //                 $expdate = $expiry_date[$j];
    //                 $explore = explode("/", $expdate);

    //                 if (count($explore) !== 2) {
    //                     $msg = 'Invalid expiry date format for medicine: ' . $mvalue;
    //                     $array = array('status' => 'fail', 'error' => '', 'message' => $msg);
    //                     echo json_encode($array);
    //                     return; // Stop further processing
    //                 }

    //                 $month_number = $this->convertMonthToNumber($explore[0]);
    //                 $insert_date = $explore[1] . "-" . $month_number . "-01";


    //                 $details = array(
    //                     'inward_date'         => date('Y-m-d H:i:s', strtotime($bill_date)),
    //                     'pharmacy_id'         => $medicine_name[$j],
    //                     'supplier_bill_basic_id' => $insert_id,
    //                     'medicine_category_id'=> $medicine_category_id[$j],
    //                     'expiry_date'         => $expiry_date[$j],
    //                     'expiry_date_format'  => $insert_date,
    //                     'batch_no'            => $batch_no[$j],
    //                     'quantity'            => $quantity[$j],
    //                     'available_quantity'  => $quantity[$j],
    //                     'amount'              => $amount[$j],
    //                     'hospital_id'         => $this->hospital_id,
    //                     'user_id'             => $this->user_id,
    //                     'target_store_id'     => $store_id,
    //                     'transfer_store_id'   => $this->store_id,
    //                 );

    //                 $available_quantity[$j] = $total_quantity[$j] - $quantity[$j];
    //                 $update_quantity = array(
    //                     'id'                 => $medicine_batch_details_id[$j],
    //                     'available_quantity' => $available_quantity[$j],
    //                 );
    //                 $this->pharmacy_model->availableQty($update_quantity);

    //                 $data1[] = $details;
    //                 $j++;
    //             }

    //             $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
    //         }

    //         $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
    //     }
    //     echo json_encode($array);
    // }


    public function addOpeningStock()
    {
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory(null, $this->hospital_id, $this->store_id);
        $data["supplierCategory"] = $supplierCategory;

        $resultlist = $this->pharmacy_model->getSupplier();
        $i          = 0;
        foreach ($resultlist as $value) {
            $pharmacy_id                 = $value['id'];
            $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
            $totalAvailableQty           = $available_qty['total_qty'];
            $resultlist[$i]["total_qty"] = $totalAvailableQty;
            $i++;
        }
        $result             = $this->pharmacy_model->getPharmacy();
        // $data['resultlist'] = $resultlist;
        $data['result']     = $result;
        $this->load->view('layout/user/header');
        $this->load->view('store/storeStock/openingStock.php', $data);
        $this->load->view('layout/user/footer');
    }
    public function openingStockList()
    {
        // if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
        //     access_denied();
        // }

        $resultlist = $this->pharmacy_model->getOpeningStockBill($this->hospital_id, $this->store_id);


        $data['resultlist'] = $resultlist;
        $this->load->view('layout/user/header');
        $this->load->view('store/storeStock/openingStockList.php', $data);
        $this->load->view('layout/user/footer');
    }

    public function editMedicineRequest()
    {
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['hospitalStores'] = $this->store_model->getHospitalDepartmentStores('Hospital', $this->hospital_id);
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $id = $this->input->get('id');

        // Validate the ID
        if (empty($id) || !is_numeric($id)) {
            show_error('Invalid request ID.');
        }

        // Fetch the main request details
        $data['request'] = $this->pharmacy_model->getRequestById($id);

        // Fetch the detailed batch items related to this request
        $data['batch_details'] = $this->pharmacy_model->getRequestBatchDetails($id);
        // echo "<pre>"; print_r($data);exit;
        // Check if the request exists
        if (empty($data['request'])) {
            show_error('Request not found.');
        }
        $this->load->view('layout/user/header');
        $this->load->view('store/request/edit.php', $data);
        $this->load->view('layout/user/footer');
    }

    public function editSupplierBillPage($id)
    {
        // Fetch necessary data
        $medicineCategory = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"]     = $medicineCategory;
        $medicine_category_id         = $this->input->post("medicine_category_id");
        $data['medicine_category_id'] = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        $data['medicine_category_id'] = $medicine_category_id;

        $supplierCategory         = $this->medicine_category_model->getSupplierCategory(null, $this->hospital_id, $this->store_id);
        $data["supplierCategory"]     = $supplierCategory;
        $supplier_category_id         = $this->input->post("supplier_category_id");
        $data['supplier_category_id'] = $this->pharmacy_model->get_supplier_name($supplier_category_id);
        $data['supplier_category_id'] = $supplier_category_id;
        $result         = $this->pharmacy_model->getSingleOpeningStockDetail($id, $this->hospital_id, $this->store_id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail'] = $detail;
// echo "<pre>"; print_r($data);exit;
        // Load the edit view (full page)
        $this->load->view('layout/user/header');
        $this->load->view("store/storeStock/editOpeningStock", $data);
        $this->load->view('layout/user/footer');
    }

    // Store List Page
    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/store');
        //$data['hospitals'] = $this->hospital_model->getAllHospitals();
        $data['title'] = 'Stores';
        $this->load->view('layout/user/header', $data);
        $this->load->view('store/store/index', $data);
        $this->load->view('layout/user/footer', $data);
    }

    // Fetch Stores for DataTable
    public function store_list()
    {
        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length'];
        $columnIndex     = $_POST['order'][0]['column'];
        $columnName      = $_POST['columns'][$columnIndex]['data'];
        $columnSortOrder = $_POST['order'][0]['dir'];

        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
 
        $where_condition['hospital_id'] = $this->hospital_id;
        // Fetch data
        $resultlist   = $this->store_model->search_store_datatable($where_condition, $columnName, $columnSortOrder, $row, $rowperpage);
        $total_result = $this->store_model->search_store_datatable_count($where_condition);
        
        // echo '<pre>'; print_r($resultlist); 
        // echo '<pre>'; print_r($total_result); 
        // exit;

        $data = array();
        // print_r($resultlist);exit;

        foreach ($resultlist as $result_value) {
            $entityTypeLabel = ucfirst($result_value->entity_type); // Capitalize the entity type (Hospital/Department)
            //$entityName      = $result_value->entity_name;         // Fetch the associated entity name

            $action = "<div class='btn-group' style='margin-left:2px;'>";
            $action .= "<a href='#' style='width: 20px;border-radius: 2px;' class='btn btn-default btn-xs'  data-toggle='dropdown' title='Options'><i class='fa fa-ellipsis-v'></i></a>";
            $action .= "<ul class='dropdown-menu dropdown-menu2' role='menu'>";
            $action .= "<li><a href='#' onclick='editRecord(" . $result_value->id . ")'>Edit</a></li>";
            $action .= "<li><a href='#' onclick='deleteRecord(" . $result_value->id . ")'>Delete</a></li>";
            $action .= "</ul>";
            $action .= "</div>";

            // Build associative array
            $nestedData = array(
                'store_unique_id' => $result_value->store_unique_id,
                'store_name'      => "<a href='#' onclick='getStoreData(" . $result_value->id . ")'>" . $result_value->store_name . "</a>",
                'entity_type'     => $entityTypeLabel, // Display entity type (Hospital/Department)
                // 'entity_name'     => $entityName,     // Display the associated entity name
                'action'          => $action,
            );

            $data[] = $nestedData;
        }

        // JSON response for DataTable
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_result),
            "recordsFiltered" => intval($total_result),
            "data"            => $data,
        );

        echo json_encode($json_data);
    }
}
