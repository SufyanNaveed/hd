<?php

class Pharmacy_model extends CI_Model
{
    //---------------------------Server side code datatable--------------------------------------
    public $column_order  = array('medicine_name', 'medicine_company', 'medicine_composition', 'medicine_category', 'medicine_group', 'unit', 'total_qty'); //set column field database for datatable orderable
    public $column_search = array('medicine_name', 'medicine_company', 'medicine_composition', 'medicine_category', 'medicine_group', 'unit', 'barcode');
    public $columnbill_order  = array('pharmacy_bill_basic.bill_no', 'pharmacy_bill_basic.date', 'patients.patient_name', 'pharmacy_bill_basic.doctor_name', 'pharmacy_bill_basic.net_amount'); //set column field database for datatable orderable
    public $columnbill_search = array('pharmacy_bill_basic.bill_no', 'pharmacy_bill_basic.date', 'patients.patient_name', 'pharmacy_bill_basic.doctor_name', 'pharmacy_bill_basic.net_amount');
    //---------------------------Server side code datatable--------------------------------------

    public $stock_column_search = ['invoice_no', 'store_name', 'purchase_no', 'net_amount', 'total']; // Replace with actual column names
    public $stock_report_column_search = [
        'supplier_bill_basic.invoice_no',
        'supplier_bill_basic.store_name',       // or use `main_stores.store_name` if that's the one intended
        'supplier_bill_basic.purchase_no',
        'supplier_bill_basic.net_amount',
        'supplier_bill_basic.total',
        'pharmacy.medicine_name',
        'medicine_batch_details.batch_no'
    ];

    public function add($pharmacy)
    {
        $this->db->insert('pharmacy', $pharmacy);
        return $this->db->insert_id();
    }

    public function addImport($medicine_data)
    {
        $this->db->insert('pharmacy', $medicine_data);
        return $this->db->insert_id();
    }

    public function search_datatable($where_condition, $stock = null, $search_type='all')
    {
        $select = "";
        if (isset($where_condition['hospital_id'])) {
            $select .= " AND hospital_id = ".$where_condition['hospital_id']; 
        }
        if (isset($where_condition['store_id'])) {
            $select .= " AND store_id = ".$where_condition['store_id'];
        } 

        $this->db->select('
        pharmacy.*, 
        medicine_category.id as medicine_category_id, 
        medicine_category.medicine_category, 
        medicine_company.id as medicine_company_id,
        medicine_company.name as medicine_company_name,
        medicine_batch_details.batch_no as batch_no,
        (SELECT SUM(available_quantity) 
         FROM medicine_batch_details 
         WHERE pharmacy_id = pharmacy.id 
         AND bill_status = "final"'. $select. ') as total_qty,
        ROUND(SUM(medicine_batch_details.available_quantity * medicine_batch_details.sale_rate), 2) as total_sale,
        ROUND(SUM(medicine_batch_details.available_quantity * medicine_batch_details.purchase_price), 2) as total_purchase
        ');
        $this->db->from('pharmacy');

        // Joins
        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');
        $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'left');
        $this->db->join('medicine_company', 'pharmacy.medicine_company_id = medicine_company.id', 'left'); // New join for medicine_company
        $this->db->where('`pharmacy`.`medicine_category_id` = `medicine_category`.`id`');

        
        // Apply filters from $where_condition
        if (isset($where_condition['hospital_id'])) {
            $this->db->where('medicine_batch_details.hospital_id', $where_condition['hospital_id']);
            $this->db->where('medicine_batch_details.bill_status', 'final');
        }
        if (isset($where_condition['store_id'])) {
            $this->db->where('medicine_batch_details.store_id', $where_condition['store_id']);
        }
        if (isset($where_condition['user_id'])) {
            $this->db->where('pharmacy.user_id', $where_condition['user_id']);
        }

        $start_date = "";
        $end_date = "";
        if ($search_type === 'this_week') {
            // Monday to Sunday of this week
            $start_date = date('Y-m-d H:i:s', strtotime('last monday 00:00:00'));
            $end_date = date('Y-m-d H:i:s', strtotime('next sunday 23:59:59'));
        }  elseif ($search_type === 'this_month') {
            // First to last day of this month
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t 23:59:59');
        } elseif ($search_type === 'last_month') {
            // First to last day of this month
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t 23:59:59');
        } elseif ($search_type === 'next_month') {
            // First to last day of next month
            //      echo $search_type;
            // exit;
            $start_date = date('Y-m-01', strtotime('first day of next month'));
            $end_date = date('Y-m-t 23:59:59', strtotime('last day of next month'));
        } 
       
        if($search_type != 'all' && $search_type != ""){
            $this->db->where('pharmacy.created_at >=', $start_date);
            $this->db->where('pharmacy.created_at <=', $end_date);
        }


        // Add search condition
        if (!empty($where_condition['search'])) {
            $this->db->group_start();
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }

        // Add GROUP BY to avoid duplicates
        $this->db->group_by('pharmacy.id');

        if($stock != null && $stock == 1){
            $this->db->having('total_qty >', 0);
        } else if($stock != null && $stock == 0){
            $this->db->having('total_qty', 0);
        }

        // Apply limits for pagination
        // if($search_type != 'all' && $search_type != ""){
            $this->db->limit($_POST['length'], $_POST['start']);
        // }

        // Apply ordering
        $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);

        $query = $this->db->get();
        // echo '<pre>'; print_r($this->db->last_query()); exit;
        return $query->result();
    }







    public function search_pharmacy()
    {
        $this->db->select('pharmacy.*, 
                      medicine_category.id as medicine_category_id, 
                      medicine_category.medicine_category');

        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');

        if (!empty($_POST['search']['value'])) {
            // If there is a search parameter
            $this->db->group_start();

            foreach ($this->column_search as $colomn_key => $colomn_value) {
                $this->db->or_like($colomn_value, $_POST['search']['value']);
            }

            $this->db->group_end();
        }

        // Apply ordering
        $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);

        $query = $this->db->get('pharmacy');
        return $query->result();
    }

    public function search_datatable_count($where_condition, $stock = null, $search_type='all')
    {
        $select = "";
        if (isset($where_condition['hospital_id'])) {
            $select .= " AND hospital_id = ".$where_condition['hospital_id']; 
        }
        if (isset($where_condition['store_id'])) {
            $select .= " AND store_id = ".$where_condition['store_id'];
        }
        
        $this->db->select('pharmacy.id, (SELECT SUM(available_quantity) 
         FROM medicine_batch_details 
         WHERE pharmacy_id = pharmacy.id 
         AND bill_status = "final"' .$select.') as total_qty
         '); // Select only the pharmacy ID for counting
        $this->db->from('pharmacy');

        // Joins
        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');
        $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'left');

        // Apply filters from $where_condition
        if (isset($where_condition['hospital_id'])) {
            $this->db->where('medicine_batch_details.hospital_id', $where_condition['hospital_id']);
            $this->db->where('medicine_batch_details.bill_status', 'final'); // Ensure only final bills are considered
        }
        if (isset($where_condition['store_id'])) {
            $this->db->where('medicine_batch_details.store_id', $where_condition['store_id']);
        }
        if (isset($where_condition['user_id'])) {
            $this->db->where('pharmacy.user_id', $where_condition['user_id']);
        }

        $start_date = "";
        $end_date = "";
        if ($search_type === 'this_week') {
            // Monday to Sunday of this week
            $start_date = date('Y-m-d H:i:s', strtotime('last monday 00:00:00'));
            $end_date = date('Y-m-d H:i:s', strtotime('next sunday 23:59:59'));
        }  elseif ($search_type === 'this_month') {
            // First to last day of this month
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t 23:59:59');
        } elseif ($search_type === 'last_month') {
            // First to last day of this month
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t 23:59:59');
        } elseif ($search_type === 'next_month') {
            // First to last day of next month
            //      echo $search_type;
            // exit;
            $start_date = date('Y-m-01', strtotime('first day of next month'));
            $end_date = date('Y-m-t 23:59:59', strtotime('last day of next month'));
        } 
       
        if($search_type != 'all' && $search_type != ""){
            $this->db->where('pharmacy.created_at >=', $start_date);
            $this->db->where('pharmacy.created_at <=', $end_date);
        }

        // Add search condition
        if (!empty($where_condition['search'])) {
            $this->db->group_start();
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }

        // Add GROUP BY to avoid duplicate rows
        $this->db->group_by('pharmacy.id');

        if($stock != null && $stock == 1){
            $this->db->having('total_qty >', 0);
        }else if($stock != null && $stock == 0){
            $this->db->having('total_qty', 0);
        }
        // Count total results
        $query = $this->db->get();
        // echo $query->num_rows();exit;
        return $query->num_rows(); // Return the count of rows
    }



    public function searchFullText()
    {
        $this->db->select('pharmacy.*,medicine_category.id as medicine_category_id,medicine_category.medicine_category');
        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');
        $this->db->where('`pharmacy`.`medicine_category_id`=`medicine_category`.`id`');
        $this->db->order_by('pharmacy.medicine_name');
        $query = $this->db->get('pharmacy');
        return $query->result_array();
    }

    public function searchtestdata()
    {
        $this->db->select('pharmacy.*');
        $this->db->order_by('pharmacy.medicine_name');
        $query = $this->db->get('pharmacy');
        return $query->result_array();
    }

    public function check_medicine_exists($medicine_name, $medicine_category_id, $hospital_id = null, $store_id = null)
    {
        // Add conditions for medicine_name and medicine_category_id
        $this->db->where(array(
            'medicine_category_id' => $medicine_category_id,
            'medicine_name' => $medicine_name
        ));

        // Add conditions for hospital_id and store_id if provided
        if (!empty($hospital_id)) {
            $this->db->where('medicine_category.hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->db->where('medicine_category.store_id', $store_id);
        }

        // Join the medicine_category table to access hospital_id and store_id
        $query = $this->db
            ->join('medicine_category', 'medicine_category.id = pharmacy.medicine_category_id')
            ->get('pharmacy');

        // Check if any rows are returned
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function searchFullTextPurchase()
    {
        $this->db->select('supplier_bill_detail.*,supplier_bill_basic.supplier_id,supplier_bill_basic.supplier_name,supplier_bill_basic.total,supplier_bill_basic.net_amount,supplier_category.supplier_category,supplier_category.supplier_person,supplier_category.supplier_person,supplier_category.contact,supplier_category.supplier_person_contact,supplier_category.address,medicine_category,pharmacy.medicine_name');
        $this->db->join('supplier_bill_basic', 'supplier_bill_detail.supplier_bill_basic_id=supplier_bill_basic.id');
        $this->db->join('supplier_category', 'supplier_bill_basic.supplier_id=supplier_category.id');
        $this->db->join('medicine_category', 'supplier_bill_detail.medicine_category_id = medicine_category.id', 'left');
        $this->db->join('pharmacy', 'supplier_bill_detail.medicine_name = pharmacy.id', 'left');
        $query = $this->db->get('supplier_bill_detail');
        return $query->result_array();
    }

    public function getDetails($id)
    {
        $this->db->select('pharmacy.*,medicine_category.id as medicine_category_id,medicine_category.medicine_category');
        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'inner');
        $this->db->where('pharmacy.id', $id);
        $this->db->order_by('pharmacy.id', 'desc');
        $query = $this->db->get('pharmacy');
        return $query->row_array();
    }

    public function update($pharmacy)
    {
        $query = $this->db->where('id', $pharmacy['id'])
            ->update('pharmacy', $pharmacy);
    }

    public function delete($id)
    {
        $this->db->where("id", $id)->delete('pharmacy');
    }

    public function getPharmacy($id = null)
    {
        $query = $this->db->get('pharmacy');
        return $query->result_array();
    }

    public function medicineDetail($medicine_batch)
    {
        $this->db->insert('medicine_batch_details', $medicine_batch);
    }

    public function getMedicineBatch($pharm_id)
    {
        $this->db->select('medicine_batch_details.*, pharmacy.id as pharmacy_id, pharmacy.medicine_name');
        $this->db->join('pharmacy', 'medicine_batch_details.pharmacy_id = pharmacy.id', 'inner');
        $this->db->where('pharmacy.id', $pharm_id);
        $query = $this->db->get('medicine_batch_details');
        return $query->result();
    }

    public function getMedicineName()
    {
        $query = $this->db->select('pharmacy.id,pharmacy.medicine_name')->get('pharmacy');
        return $query->result_array();
    }

    public function getMedicineNamePat()
    {
        $query = $this->db->select('pharmacy.id,pharmacy.medicine_name')->get('pharmacy');
        return $query->result_array();
    }

    public function addBill($data)
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("pharmacy_bill_basic", $data);
        } else {
            $this->db->insert("pharmacy_bill_basic", $data);
            $Id = $this->db->insert_id();
            return $Id;
        }
    }

    public function addBillSupplier($data)
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("supplier_bill_basic", $data);
        } else {
            $this->db->insert("supplier_bill_basic", $data);
            return $this->db->insert_id();
        }
    }

    public function addReturbBill($data)
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("return_supplier_bill_basic", $data);
        } else {
            $this->db->insert("return_supplier_bill_basic", $data);
            return $this->db->insert_id();
        }
    }

    public function addBillBatch($data)
    {
        $query = $this->db->insert_batch('pharmacy_bill_detail', $data);
    }

    public function addBillBatchSupplier($data)
    {
        $query = $this->db->insert_batch('supplier_bill_detail', $data);
    }

    public function addBillMedicineBatchSupplier($data1)
    {
        $query = $this->db->insert_batch('medicine_batch_details', $data1);
    }
    public function addBillReturnMedicineBatchSupplier($data1)
    {
        $query = $this->db->insert_batch('returned_medicine_batches', $data1);
    }
    public function deleteBillMedicineBatchSupplier($id)
    {
        $this->db->where('supplier_bill_basic_id', $id);
        $query = $this->db->delete('medicine_batch_details');

        return $query; // Return the query result (true/false)
    }


    public function updateBillBatch($data)
    {
        $this->db->where('pharmacy_bill_basic_id', $data['id'])->update('pharmacy_bill_detail');
    }

    public function updateBillBatchSupplier($data)
    {
        $this->db->where('supplier_bill_basic_id', $data['id'])->update('supplier_bill_basic_id');
    }

    public function updateBillDetail($data)
    {
        $this->db->where('id', $data['id'])->update('pharmacy_bill_detail', $data);
    }

    public function updateBillSupplierDetail($data)
    {
        $this->db->where('id', $data['id'])->update('supplier_bill_detail', $data);
    }

    public function updateMedicineBatchDetail($data1)
    {
        $query = $this->db->where('id', $data1['id'])->update('medicine_batch_details', $data1);
        $this->db->last_query();
    }

    public function deletePharmacyBill($id)
    {
        $query = $this->db->where("pharmacy_bill_basic_id", $id)->delete("pharmacy_bill_detail");
        if ($query) {
            $this->db->where("id", $id)->delete("pharmacy_bill_basic");
        }
    }

    public function deleteSupplierBill($id)
    {
        $query = $this->db->where("supplier_bill_basic_id", $id)->delete("medicine_batch_details");
        if ($query) {
            $this->db->where("id", $id)->delete("supplier_bill_basic");
        }
    }

    public function getMaxId()
    {
        $query  = $this->db->select('max(id) as purchase_no')->get("supplier_bill_basic");
        $result = $query->row_array();
        return $result["purchase_no"];
    }

    public function getReturnMaxId()
    {
        $query  = $this->db->select('max(id) as purchase_no')->get("return_supplier_bill_basic");
        $result = $query->row_array();
        return $result["invoice_no"];
    }
    public function getindate($purchase_id)
    {
        $query = $this->db->select('supplier_bill_basic.*,')
            ->where('supplier_bill_basic.id', $purchase_id)
            ->get('supplier_bill_basic');
        return $query->row_array();
    }

    public function getdate($id)
    {
        $query = $this->db->select('pharmacy_bill_basic.*,')
            ->where('pharmacy_bill_basic.id', $id)
            ->get('pharmacy_bill_basic');
        return $query->row_array();
    }
    public function getSupplier($hospital_id = null, $store_id = null)
    {
        $this->db->select('supplier_bill_basic.*, supplier_category.supplier_category')
            ->from('supplier_bill_basic')
            ->join('supplier_category', 'supplier_category.id = supplier_bill_basic.supplier_id')
            ->order_by('supplier_bill_basic.id', 'desc');

        // Apply filters if hospital_id and store_id are provided
        if (!empty($hospital_id)) {
            $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->db->where('supplier_bill_basic.store_id', $store_id);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_supplier_stock_summary($hospital_id = null, $store_id = null, $role = "")
    {
        $this->db->select('SUM(supplier_bill_basic.total) as total_purchase_amount')
            ->from('supplier_bill_basic')
            ->join('supplier_category', 'supplier_category.id = supplier_bill_basic.supplier_id')
            ->order_by('supplier_bill_basic.id', 'desc');

        // Apply filters if hospital_id and store_id are provided
        if (!empty($hospital_id)) {
            $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->db->where('supplier_bill_basic.store_id', $store_id);
        }

        if ($store_id && ($role == 'Store In-Charge' || $role == 'Chief Pharmacist')) {
            $this->db->where('supplier_bill_basic.store_id', $store_id);
        }

        if ($store_id && $role == 'Department Pharmacist') {
            $this->db->where('supplier_bill_basic.target_store_id', $store_id);
        }

        $this->db->where_in('supplier_bill_basic.bill_status', ['final','partial']);

        // return $this->db->count_all_results();
        $result = $this->db->get()->row();
        return $result;
    }

    public function get_supplier_stock_summary_Admin()
    {
        $total_result = 0;
        for($i = 1; $i <= 8; $i++){
            $hospital_id = $i;

            $store = $this->db->query('SELECT id FROM main_stores WHERE entity_id = '.$hospital_id.' AND store_name LIKE "%Primary Pharmacy" LIMIT 1')->row();
            // echo '<pre>'; print_r($store); exit;


            $result = array();
            $this->db->select('SUM(supplier_bill_basic.total) as total_purchase_amount')
                ->from('supplier_bill_basic')
                ->join('supplier_category', 'supplier_category.id = supplier_bill_basic.supplier_id')
                ->order_by('supplier_bill_basic.id', 'desc');

            // Apply filters if hospital_id and store_id are provided
            if (!empty($hospital_id)) {
                $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
            } 

            if (!empty($store)) {
                $this->db->where_in('supplier_bill_basic.store_id', $store->id);
            }  

            
            // $this->db->where('supplier_bill_basic.bill_status', 'final');

            // return $this->db->count_all_results();
            $result = $this->db->get()->row_array();
            $total_result += $result['total_purchase_amount'];
            // echo '<pre>'; print_r($result['total_purchase_amount']); //exit;

        }
        // echo '<pre>'; print_r($total_result); exit;

        return $total_result;
    }
    

    public function getBillBasic($limit = "", $start = "")
    {
        $query = $this->db->select('pharmacy_bill_basic.*,patients.patient_name')
            ->order_by('pharmacy_bill_basic.id', 'desc')
            ->join('patients', 'patients.id = pharmacy_bill_basic.patient_id')
            ->where("patients.is_active", "yes")->limit($limit, $start)
            ->get('pharmacy_bill_basic');
        return $query->result_array();
    }

    public function searchbill_datatable()
    {
        $this->db->select('pharmacy_bill_basic.*,patients.patient_name');
        $this->db->join('patients', 'patients.id = pharmacy_bill_basic.patient_id');
        $this->db->where('patients.is_active', "yes");

        if (!empty($_POST['search']['value'])) {
            // if there is a search parameter
            $counter = true;
            $this->db->group_start();

            foreach ($this->columnbill_search as $colomn_key => $colomn_value) {
                if ($counter) {
                    $this->db->like($colomn_value, $_POST['search']['value']);
                    $counter = false;
                }
                $this->db->or_like($colomn_value, $_POST['search']['value']);
            }
            $this->db->group_end();
        }
        $this->db->limit($_POST['length'], $_POST['start']);
        //$this->db->order_by($this->columnbill_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        $this->db->order_by('pharmacy_bill_basic.id', 'DESC');
        $query = $this->db->get('pharmacy_bill_basic');
        $this->db->last_query();
        return $query->result();
    }

    public function searchbill_datatable_count()
    {
        $this->db->select('pharmacy_bill_basic.*,patients.patient_name');
        $this->db->join('patients', 'patients.id = pharmacy_bill_basic.patient_id');
        $this->db->where('patients.is_active', "yes");
        if (!empty($_POST['search']['value'])) {
            // if there is a search parameter
            $counter = true;
            $this->db->group_start();
            foreach ($this->columnbill_search as $colomn_key => $colomn_value) {
                if ($counter) {
                    $this->db->like($colomn_value, $_POST['search']['value']);
                    $counter = false;
                }
                $this->db->or_like($colomn_value, $_POST['search']['value']);
            }
            $this->db->group_end();
        }
        $query        = $this->db->from('pharmacy_bill_basic');
        $total_result = $query->count_all_results();
        return $total_result;
    }

    public function getBillBasicPat($patient_id)
    {
        $this->db->select('pharmacy_bill_basic.*,patients.patient_name');
        $this->db->join('patients', 'patients.id = pharmacy_bill_basic.patient_id');
        $this->db->where('patient_id', $patient_id);
        $query = $this->db->get('pharmacy_bill_basic');
        return $query->result_array();
    }

    public function get_medicine_name($medicine_category_id)
    {
        $this->db->select('pharmacy.*');
        $this->db->where('pharmacy.medicine_category_id', $medicine_category_id);
        $query = $this->db->get('pharmacy');
        return $query->result_array();
    }

    public function get_medicine_dosage($medicine_category_id)
    {
        $this->db->select('medicine_dosage.dosage,medicine_dosage.id');
        $this->db->where('medicine_dosage.medicine_category_id', $medicine_category_id);
        $query = $this->db->get('medicine_dosage');
        return $query->result_array();
    }

    public function get_supplier_name($supplier_category_id)
    {
        $query = $this->db->where("id", $supplier_category_id)->get("supplier_category");
        return $query->result_array();
    }

    public function getBillDetails($id)
    {
        $this->db->select('
            pharmacy_bill_basic.*, 
            staff.name, 
            staff.surname, 
            patients.patient_name, 
            patients.id as patientid, 
            patients.patient_unique_id,
            supplier_category.*  // Select all columns from supplier_category
        ');
        $this->db->join('patients', 'pharmacy_bill_basic.patient_id = patients.id');
        $this->db->join('staff', 'pharmacy_bill_basic.generated_by = staff.id');
        $this->db->join('pharmacy', 'pharmacy_bill_basic.pharmacy_id = pharmacy.id', 'left'); // Join pharmacy table
        $this->db->join('supplier_category', 'pharmacy.supplier_id = supplier_category.id', 'left'); // Join supplier_category table
        $this->db->where('pharmacy_bill_basic.id', $id);
        $query = $this->db->get('pharmacy_bill_basic');
        return $query->row_array();
    }



    public function getAllBillDetails($id)
    {
        $query = $this->db->select('pharmacy_bill_detail.*,pharmacy.medicine_name,pharmacy.unit,pharmacy.id as medicine_id')
            ->join('pharmacy', 'pharmacy_bill_detail.medicine_name = pharmacy.id')
            ->where('pharmacy_bill_basic_id', $id)
            ->get('pharmacy_bill_detail');
        return $query->result_array();
    }

    public function getSupplierDetails($id)
    {
        $this->db->select('supplier_bill_basic.*,supplier_category.supplier_category,supplier_category.supplier_person,supplier_category.contact,supplier_category.address');
        $this->db->join('supplier_category', 'supplier_category.id=supplier_bill_basic.supplier_id');
        $this->db->where('supplier_bill_basic.id', $id);
        $query = $this->db->get('supplier_bill_basic');
        return $query->row_array();
    }

    public function getAllSupplierDetails($id)
    {
        $query = $this->db->select('medicine_batch_details.*,pharmacy.medicine_name,pharmacy.unit,pharmacy.id as medicine_id,medicine_category.medicine_category')
            ->join('pharmacy', 'medicine_batch_details.pharmacy_id = pharmacy.id')
            ->join('medicine_category', 'medicine_batch_details.medicine_category_id = medicine_category.id')
            ->where('medicine_batch_details.supplier_bill_basic_id', $id)
            ->get('medicine_batch_details');
        return $query->result_array();
    }

    public function getReturnAllSupplierDetails($id)
    {
        $query = $this->db->select('returned_medicine_batches.*,pharmacy.medicine_name,pharmacy.unit,pharmacy.id as medicine_id,medicine_category.medicine_category')
            ->join('pharmacy', 'returned_medicine_batches.pharmacy_id = pharmacy.id')
            ->join('medicine_category', 'returned_medicine_batches.medicine_category_id = medicine_category.id')
            ->where('returned_medicine_batches.return_supplier_bill_basic_id', $id)
            ->get('returned_medicine_batches');
        return $query->result_array();
    }

    public function getBillDetailsPharma($id)
    {
        $this->db->select('pharmacy_bill_basic.*,patients.patient_name');
        $this->db->join('patients', 'patients.id = pharmacy_bill_basic.patient_id');
        $this->db->where('pharmacy_bill_basic.id', $id);
        $query = $this->db->get('pharmacy_bill_basic');
        return $query->row_array();
    }

    public function getAllBillDetailsPharma($id)
    {
        $query = $this->db->select('pharmacy_bill_detail.*,pharmacy.medicine_name,pharmacy.unit,pharmacy.id as medicine_id')
            ->join('pharmacy', 'pharmacy_bill_detail.medicine_name = pharmacy.id')
            ->where('pharmacy_bill_basic_id', $id)
            ->get('pharmacy_bill_detail');
        return $query->result_array();
    }

    public function getQuantity($batch_no = null, $med_id, $hospital_id = null, $store_id = null,$batch_id = null)
    {
        $this->db->select('medicine_batch_details.id, medicine_batch_details.available_quantity, medicine_batch_details.quantity, medicine_batch_details.purchase_price, medicine_batch_details.sale_rate')
            
            ->where('pharmacy_id', $med_id);

        if(!empty($batch_no)) {
            $this->db->where('batch_no', $batch_no);
        }

        if(!empty($batch_id)) {
            $this->db->where('id', $batch_id);
        }
        // Apply hospital filter if provided
        if (!empty($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

        // Apply store filter if provided
        if (!empty($store_id)) {
            $this->db->where('store_id', $store_id);
        }

        $query = $this->db->get('medicine_batch_details');
        return $query->row_array();
    }

    public function getDepPharQuantity($batch_no, $med_id, $hospital_id = null, $store_id = null, $batch_id = null, $role = null)
    {
        $this->db->select('medicine_batch_details.id, medicine_batch_details.available_quantity, medicine_batch_details.quantity, medicine_batch_details.purchase_price, medicine_batch_details.sale_rate')
            ->where('batch_no', $batch_no)
            ->where('pharmacy_id', $med_id);

        if ($batch_id) {
            $this->db->where('id', $batch_id);
        }

        if ($hospital_id) {
            $this->db->where([
                'hospital_id' => $hospital_id,
                'approved_quantity' => null,
            ]);
        }

        if ($store_id) {
            if ($role === 'Department Pharmacist') {
                $this->db->where('target_store_id', $store_id);
            } else {
                $this->db->where('store_id', $store_id);
            }
        }

        return $this->db->get('medicine_batch_details')->row_array();
    }


    public function getQuantityedit($batch_no, $hospital_id = null, $store_id = null)
    {
        $this->db->select('medicine_batch_details.id, medicine_batch_details.available_quantity, medicine_batch_details.quantity, medicine_batch_details.purchase_price, medicine_batch_details.sale_rate');
        $this->db->from('medicine_batch_details');
        $this->db->where('batch_no', $batch_no);

        // Check for hospital_id
        if (!empty($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

        // Check for store_id
        if (!empty($store_id)) {
            $this->db->where('store_id', $store_id);
            $this->db->where('bill_status', 'final');
        }

        $query = $this->db->get();
        return $query->row_array();
    }



    public function getStoreQuantityedit($med_id, $store_id)
    {
        $query = $this->db->select('SUM(available_quantity) as total_available_quantity')
            ->where('pharmacy_id', $med_id)
            ->where('target_store_id', $store_id)
            ->where('operation_type', null)
            ->where('bill_status', 'final')
            ->get('medicine_batch_details');

        $result = $query->row_array();
        return $result['total_available_quantity'] ?? 0; // Return 0 if no result    
    }

    public function checkvalid_medicine_exists($str)
    {
        $medicine_name = $this->input->post('medicine_name');
        if ($this->check_medicie_exists($medicine_name)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_medicie_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'medicine_name' => $name);
            $query = $this->db->where($data)->get('pharmacy');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('medicine_name', $name);
            $query = $this->db->get('pharmacy');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function availableQty($update_quantity)
    {
        $query = $this->db->where('id', $update_quantity['id'])
            ->update('medicine_batch_details', $update_quantity);
    }

    public function totalQuantity($pharmacy_id)
    {
        $query = $this->db->select('sum(available_quantity) as total_qty')
            ->where('pharmacy_id', $pharmacy_id)
            ->get('medicine_batch_details');
        return $query->row_array();
    }

    public function searchBillReport($date_from, $date_to)
    {
        $this->db->select('pharmacy_bill_basic.*');
        $this->db->where('date >=', $date_from);
        $this->db->where('date <=', $date_to);
        $query = $this->db->get("pharmacy_bill_basic");
        return $query->result_array();
    }

    public function delete_medicine_batch($id)
    {
        $this->db->where("id", $id)->delete("medicine_batch_details");
    }

    public function delete_bill_detail($delete_arr)
    {
        foreach ($delete_arr as $key => $value) {
            $id = $value["id"];
            $this->db->where("id", $id)->delete("prescription");
        }
    }

    public function getBillNo()
    {
        $query = $this->db->select("max(id) as id")->get('pharmacy_bill_basic');
        return $query->row_array();
    }

    public function getExpiryDate($batch_no, $med_id)
    {
        $query = $this->db->where("batch_no", $batch_no)
            ->where("pharmacy_id", $med_id)
            ->get('medicine_batch_details');
        return $query->row_array();
    }
    public function getExpireDate($batch_no)
    {
        $query = $this->db->where("batch_no", $batch_no)
            ->get('medicine_batch_details');
        return $query->row_array();
    }

    public function getBatchNoList($medicine, $hospital_id = null, $store_id = null)
    {
        $this->db->where('pharmacy_id', $medicine)
            ->where('available_quantity >', 0);

        if (!is_null($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

        if (!is_null($store_id)) {
            $this->db->where('store_id', $store_id);
        }
        $this->db->where('bill_status', 'final');

        $query = $this->db->get('medicine_batch_details');
        return $query->result_array();
    }

    public function getStoreBatchNoList($medicine, $hospital_id = null, $store_id = null)
    {
        $this->db->where('pharmacy_id', $medicine)
            ->where('available_quantity >', 0);

        if (!is_null($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

        if (!is_null($store_id)) {
            $this->db->where('store_id', $store_id);
        }
        $this->db->where('bill_status', 'final');

        $query = $this->db->get('medicine_batch_details');
        return $query->result_array();
    }

    public function addBadStock($data)
    {
        $this->db->insert("medicine_bad_stock", $data);
    }

    public function updateMedicineBatch($data)
    {
        $this->db->where("id", $data["id"])->update("medicine_batch_details", $data);
    }

    public function getMedicineBadStock($id)
    {
        $query = $this->db->where("pharmacy_id", $id)->get("medicine_bad_stock");
        return $query->result();
    }

    public function deleteBadStock($id)
    {
        $this->db->where("id", $id)->delete("medicine_bad_stock");
    }

    public function searchNameLike($category, $value)
    {
        $query = $this->db->where("medicine_category_id", $category)->like("medicine_name", $value)->get("pharmacy");
        return $query->result_array();
    }
    ///////////////////////////// function added by faraz/////////////////////////////////////////////////////
    public function getOptimizeExpiryDate($batch_no, $med_id)
    {
        $batch_no = explode('~', $batch_no);
        $query = $this->db->where("batch_no", $batch_no[1])
            ->where("pharmacy_id", $med_id)
            ->where("id", $batch_no[0])
            ->get('medicine_batch_details');
        return $query->row_array();
    }

    public function getOptimizeQuantity($batch_no, $med_id)
    {
        $batch_no = explode('~', $batch_no);
        $query = $this->db->select('medicine_batch_details.id,medicine_batch_details.available_quantity,medicine_batch_details.quantity,medicine_batch_details.purchase_price,medicine_batch_details.sale_rate')
            ->where('batch_no', $batch_no[1])
            ->where("id", $batch_no[0])
            ->where('pharmacy_id', $med_id)
            ->get('medicine_batch_details');
        return $query->row_array();
    }

    public function getOptimizeExpireDate($batch_no)
    {
        $batch_no = explode('~', $batch_no);
        $query = $this->db->where("batch_no", $batch_no[1])
            ->where("id", $batch_no[0])
            ->get('medicine_batch_details');
        return $query->row_array();
    }

    public function getOptimizeQuantityedit($batch_no)
    {
        $batch_no = explode('~', $batch_no);
        $query = $this->db->select('medicine_batch_details.id,medicine_batch_details.available_quantity,medicine_batch_details.quantity,medicine_batch_details.purchase_price,medicine_batch_details.sale_rate')
            ->where('batch_no', $batch_no[1])
            ->where('id', $batch_no[0])
            ->get('medicine_batch_details');
        return $query->row_array();
    }

    public function getHospitalMedicineStock($hospital_id, $pharmacy_id, $store_id)
    {
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('pharmacy_id', $pharmacy_id);
        $this->db->where('store_id', $store_id);
        return $this->db->get('hospital_medicine_stock')->row_array();
    }

    public function updateHospitalMedicineStock($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('hospital_medicine_stock', $data);
    }

    public function addHospitalMedicineStock($data)
    {
        $this->db->insert('hospital_medicine_stock', $data);
    }
    public function updateHospitalMedicineStockQuantity($hospital_id, $pharmacy_id, $store_id, $quantity_diff)
    {
        $this->db->set('available_qty', 'available_qty + ' . (int)$quantity_diff, false);
        $this->db->where('hospital_id', $hospital_id);
        $this->db->where('pharmacy_id', $pharmacy_id);
        $this->db->where('store_id', $store_id);
        $this->db->update('hospital_medicine_stock');
    }
    public function getStoreTransferStock($where_condition, $hospital_id = null, $store_id = null, $target_store_id = null)
    {
        $this->db->select('
            supplier_bill_basic.id,
            supplier_bill_basic.invoice_no,
            supplier_bill_basic.hospital_id,
            supplier_bill_basic.transfer_store_id,
            supplier_bill_basic.net_amount,
            supplier_bill_basic.date,
            supplier_bill_basic.purchase_no,
            supplier_bill_basic.store_name,
            supplier_bill_basic.total,
            supplier_bill_basic.bill_status,
            supplier_bill_basic.purchase_no
        ') // Select columns from main table
            ->from('supplier_bill_basic');

        // Apply filters if hospital_id is provided
        if (!empty($hospital_id)) {
            $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
        }

        $this->db->where('supplier_bill_basic.request_type', null);

        if (!empty($store_id)) {
            $this->db->where('supplier_bill_basic.transfer_store_id', $store_id);
        }

        if (!empty($target_store_id)) {
            // Join with main_store table when target_store_id is provided
            $this->db->where('supplier_bill_basic.transfer_store_id !=', '');
            // $this->db->join('main_stores', 'main_stores.id = supplier_bill_basic.target_store_id', 'left');
            $this->db->where('supplier_bill_basic.target_store_id', $target_store_id);
            $this->db->where('supplier_bill_basic.bill_status', 'final');
        }

        // Apply search filters if search condition exists
        if (!empty($where_condition['search']) && isset($this->stock_column_search)) {
            $this->db->group_start();
            foreach ($this->stock_column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }

        // Pagination
        $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
        $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        // Ordering
        $order_dir = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc', 'desc'])
            ? $_POST['order'][0]['dir']
            : 'desc';
        $this->db->order_by('supplier_bill_basic.id', $order_dir);

        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'Database error in getStoreTransferStock: ' . $this->db->last_query());
            return [];
        }

        return $query->result_array();
    }


    public function getStoreTransferStockCount($hospital_id = null, $store_id = null, $target_store_id = null)
    {
        $this->db->from('supplier_bill_basic');

        // Apply filters if hospital_id and store_id are provided
        if (!empty($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }
        $this->db->where('request_type', null);

        if (!empty($store_id)) {
            $this->db->where('transfer_store_id', $store_id);
        }
        if (!empty($target_store_id)) {
            $this->db->where('target_store_id', $target_store_id);
            $this->db->where('bill_status', 'final');
        }

        // Use count_all_results to get the total number of rows
        return $this->db->count_all_results();
    }


    public function store_medicine_search_datatable($where_condition, $hospital_id, $store_id)
    {
        $this->db->select('pharmacy.*, 
                      medicine_category.id as medicine_category_id, 
                      medicine_category.medicine_category, 
                      SUM(medicine_batch_details.available_quantity) as total_qty, 
                      ROUND(SUM(medicine_batch_details.available_quantity * medicine_batch_details.sale_rate), 2) as total_sale,
                      ROUND(SUM(medicine_batch_details.available_quantity * medicine_batch_details.purchase_price), 2) as total_purchase');
        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');
        $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'inner'); // Ensures only relevant batch details are included

        // Apply filters for hospital_id and target_store_id in medicine_batch_details table
        if (!empty($hospital_id)) {
            $this->db->where('medicine_batch_details.hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->db->where('medicine_batch_details.target_store_id', $store_id);
            $this->db->where('medicine_batch_details.bill_status', 'final');
        }

        // Apply search filters if search condition exists
        if (!empty($_POST['search']['value']) && isset($this->column_search)) {
            $this->db->group_start(); // Open group for OR conditions
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $_POST['search']['value']);
            }
            $this->db->group_end(); // Close group for OR conditions
        }

        // Group by pharmacy.id to avoid duplicates
        $this->db->group_by('pharmacy.id');

        // Apply pagination
        $length = isset($_POST['length']) ? (int)$_POST['length'] : 10; // Default length
        $start = isset($_POST['start']) ? (int)$_POST['start'] : 0; // Default start
        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        // Apply ordering
        $order_column = isset($_POST['order'][0]['column']) && isset($this->column_order[$_POST['order'][0]['column']])
            ? $this->column_order[$_POST['order'][0]['column']]
            : 'pharmacy.id'; // Default order column
        $order_dir = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc', 'desc'])
            ? $_POST['order'][0]['dir']
            : 'asc'; // Default order direction
        $this->db->order_by($order_column, $order_dir);

        // Execute the query
        $query = $this->db->get('pharmacy');
        // echo '<pre>'; print_r($this->db->last_query());exit; 
        // Check if query execution was successful
        if (!$query) {
            log_message('error', 'Database error in store_medicine_search_datatable: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }



    public function getBillsByStatus($status, $hospital_id, $store_id)
    {
        $this->db->select('id, target_store_id, transfer_store_id, total, status,date,purchase_no,store_name,bill_status');
        $this->db->from('supplier_bill_basic');

        // Filter by status
        $this->db->where('status', $status);

        // Check for hospital_id
        if (!empty($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

        // Check for store_id (target or transfer store)
        if (!empty($store_id)) {
            $this->db->group_start(); // Group conditions for store_id
            $this->db->where('target_store_id', $store_id); // Store requesting stock
            $this->db->group_end();
        }

        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();

        return $query->result(); // Return the result as an array of objects
    }



    public function getRequestById($id)
    {
        $this->db->select('id, purchase_no, target_store_id, transfer_store_id, total, status, date, note, store_name,remarks');
        $this->db->from('supplier_bill_basic');
        $this->db->where('id', $id);
        $query = $this->db->get();

        return $query->row(); // Return a single object
    }
    public function getRequestBatchDetails($bill_id)
    {
        $this->db->select('medicine_batch_details.*, pharmacy.medicine_name, medicine_category.medicine_category');
        $this->db->from('medicine_batch_details');
        $this->db->join('pharmacy', 'medicine_batch_details.pharmacy_id = pharmacy.id', 'left');
        $this->db->join('medicine_category', 'medicine_batch_details.medicine_category_id = medicine_category.id', 'left');
        $this->db->where('medicine_batch_details.supplier_bill_basic_id', $bill_id);
        $query = $this->db->get();

        return $query->result(); // Return as an array of objects
    }

    public function getStoresRequest($hospital_id, $store_id, $status = "")
    {
        $this->db->select('supplier_bill_basic.id, supplier_bill_basic.target_store_id, supplier_bill_basic.transfer_store_id, 
                           supplier_bill_basic.total, supplier_bill_basic.status, supplier_bill_basic.date, 
                           supplier_bill_basic.purchase_no, supplier_bill_basic.store_name AS target_store_name, 
                           main_stores.store_name AS transfer_store_name');
        $this->db->from('supplier_bill_basic');

        $this->db->where('request_type', 'request');

        // Join with main_stores to get transfer store name
        $this->db->join('main_stores', 'supplier_bill_basic.target_store_id = main_stores.id', 'left');

        // Check for hospital_id
        if (!empty($hospital_id)) {
            $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
        }

        // Check for store_id (transfer_store_id: the store receiving the request)
        if (!empty($store_id)) {
            $this->db->where('supplier_bill_basic.transfer_store_id', $store_id); // Store that received the request
        }
        $this->db->where('supplier_bill_basic.bill_status', 'final');

        if($status){
            $this->db->where('supplier_bill_basic.status', $status);
        }

        // Add ORDER BY clause to sort by id in descending order
        $this->db->order_by('supplier_bill_basic.id', 'DESC');

        $query = $this->db->get();

        return $query->result(); // Return the result as an array of objects
    }

    public function getStoreRequestBatchDetails($bill_id)
    {
        $this->db->select('medicine_batch_details.*, pharmacy.medicine_name, medicine_category.medicine_category');
        $this->db->from('medicine_batch_details');
        $this->db->join('pharmacy', 'medicine_batch_details.pharmacy_id = pharmacy.id', 'left');
        $this->db->join('medicine_category', 'medicine_batch_details.medicine_category_id = medicine_category.id', 'left');
        $this->db->where('medicine_batch_details.supplier_bill_basic_id', $bill_id);
        $query = $this->db->get();

        return $query->result_array(); // Return as an array
    }

    public function store_medicine_search_datatable_count($where_condition, $hospital_id, $store_id)
    {
        $this->db->select('COUNT(DISTINCT pharmacy.id) as total_records');
        $this->db->from('pharmacy');
        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');
        $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'inner'); // Ensures only relevant batch details are included

        // Apply filters for hospital_id and target_store_id in medicine_batch_details table
        if (!empty($hospital_id)) {
            $this->db->where('medicine_batch_details.hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->db->where('medicine_batch_details.target_store_id', $store_id);
        }

        // Apply search filters if search condition exists
        if (!empty($_POST['search']['value']) && isset($this->column_search)) {
            $this->db->group_start(); // Open group for OR conditions
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $_POST['search']['value']);
            }
            $this->db->group_end(); // Close group for OR conditions
        }

        // Execute the query
        $query = $this->db->get();

        // Check if query execution was successful
        if (!$query) {
            log_message('error', 'Database error in store_medicine_search_datatable_count: ' . $this->db->last_query());
            return 0;
        }

        $result = $query->row();
        return isset($result->total_records) ? $result->total_records : 0;
    }

    public function getBillDetail($id)
    {
        $this->db->select('supplier_bill_basic.*');
        $this->db->from('supplier_bill_basic');
        $this->db->where('supplier_bill_basic.id', $id);
        $query = $this->db->get();
        return $query->row_array(); // Return as an associative array
    }

    public function getBatchDetail($batch_id)
    {
        $this->db->select('*');
        $this->db->from('medicine_batch_details');
        $this->db->where('id', $batch_id);
        $query = $this->db->get();

        return $query->row_array(); // Return a single row as an associative array
    }
    public function updateBatchDetails($batch_id, $data)
    {
        $this->db->where('id', $batch_id); // Specify the batch ID to update
        $this->db->update('medicine_batch_details', $data); // Update the record with the provided data

        if ($this->db->affected_rows() > 0) {
            return true; // Successfully updated
        } else {
            return false; // No rows updated (e.g., batch ID not found)
        }
    }


    public function updateBillStatus($id, $data)
    {
        $query = $this->db->where('id', $id)
            ->update('supplier_bill_basic', $data);
    }

    public function store_medicine_qty($id, $hospital_id, $store_id)
    {
        $this->db->select('pharmacy.*, 
                  medicine_category.id as medicine_category_id, 
                  medicine_category.medicine_category, 
                  SUM(medicine_batch_details.available_quantity) as total_qty, 
                  ROUND(SUM(medicine_batch_details.available_quantity * medicine_batch_details.sale_rate), 2) as total_sale,
                  ROUND(SUM(medicine_batch_details.available_quantity * medicine_batch_details.purchase_price), 2) as total_purchase');
        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');
        $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'inner'); // Ensures only relevant batch details are included

        // Apply filters for hospital_id and target_store_id in medicine_batch_details table
        if (!empty($hospital_id)) {
            $this->db->where('medicine_batch_details.hospital_id', $hospital_id);
        }

        $this->db->where('medicine_batch_details.pharmacy_id', $id);

        if (!empty($store_id)) {
            $this->db->where('medicine_batch_details.target_store_id', $store_id);
            $this->db->where('medicine_batch_details.bill_status', 'final');
        }
        $this->db->where('pharmacy.id', $id); // Added grouping for medicine_category.id

        // Group by pharmacy.id to avoid duplicates
        $this->db->group_by('pharmacy.id, medicine_category.id'); // Added grouping for medicine_category.id

        $query = $this->db->get('pharmacy');

        // Check if query execution was successful
        if (!$query) {
            log_message('error', 'Database error in store_medicine_search_datatable: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }

    public function getBills($hospital_id, $store_id, $ipdid)
    {
        $this->db->select('pharmacy_bill_basic.*, 
                            patients.patient_name, 
                            hospitals.name as hospital_name, 
                            main_stores.store_name,departments.department_name')
            ->from('pharmacy_bill_basic')
            ->join('patients', 'patients.id = pharmacy_bill_basic.patient_id', 'left')
            ->join('hospitals', 'hospitals.id = pharmacy_bill_basic.hospital_id', 'left') // Left join for hospital
            ->join('main_stores', 'main_stores.id = pharmacy_bill_basic.store_id', 'left')
            ->join('departments', 'departments.id = main_stores.department_id', 'left'); // Left join for department

        // Apply filtering conditions

        if (!empty($ipdid)) {
            $this->db->where('pharmacy_bill_basic.opd_ipd_no', $ipdid);
        }

        // Order by latest records
        $this->db->order_by('pharmacy_bill_basic.id', 'desc');

        $query = $this->db->get();
        return $query->result(); // Return as object
    }

    public function getBill($id = null, $hospital_id, $store_id, $opd_id = null)
    {
        $this->db->select('pharmacy_bill_basic.*, patients.patient_name')
            ->join('patients', 'patients.id = pharmacy_bill_basic.patient_id')
            ->where('pharmacy_bill_basic.hospital_id', $hospital_id);
        if (!empty($id)) {
            $this->db->where('pharmacy_bill_basic.id', $id);
        }

        // ✅ Apply OPD ID condition only if it is not empty
        if (!empty($opd_id)) {
            $this->db->where('pharmacy_bill_basic.opd_ipd_no', $opd_id);
        }

        $this->db->order_by('pharmacy_bill_basic.id', 'desc');

        $query = $this->db->get('pharmacy_bill_basic');
        // echo '<pre>'; print_r($this->db->last_query()); exit;

        return $query->row(); // Return single object (first matched row)
    }


    public function getPharmacyBillDetails($id)
    {
        $this->db->select('pharmacy_bill_detail.*, 
                           medicine_category.medicine_category, 
                           pharmacy.medicine_name,
                           medicine_dosage.dosage AS dosage_name, 
                           medicin_instruction.instruction AS instruction_name')
            ->join('medicine_category', 'medicine_category.id = pharmacy_bill_detail.medicine_category_id')  // Join medicine_category table
            ->join('pharmacy', 'pharmacy.id = pharmacy_bill_detail.medicine_name')  // Join pharmacy table using medicine_name foreign key
            ->join('medicine_dosage', 'medicine_dosage.id = pharmacy_bill_detail.dosage_id', 'left') // Join medicine_dosage table
            ->join('medicin_instruction', 'medicin_instruction.id = pharmacy_bill_detail.instruction_id', 'left') // Join medicin_instruction table
            ->where('pharmacy_bill_detail.pharmacy_bill_basic_id', $id)  // Add condition for pharmacy_bill_basic_id  
            ->order_by('pharmacy_bill_detail.id', 'desc');

        $query = $this->db->get('pharmacy_bill_detail');

        return $query->result(); // Return multiple objects (array of results)
    }
 public function getBatchExpireDate($batch_id)
    {
       $query = $this->db->where("id", $batch_id)
            ->get('medicine_batch_details');
        return $query->row_array();
    }

    public function getOpeningStockBill($hospital_id = null, $store_id = null)
    {
        $this->db->select('supplier_bill_basic.*')
            ->from('supplier_bill_basic')
            ->order_by('supplier_bill_basic.id', 'desc');

        // Apply filters if hospital_id and store_id are provided
        if (!empty($hospital_id)) {
            $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->db->where('supplier_bill_basic.target_store_id', $store_id);
            $this->db->where('supplier_bill_basic.transfer_store_id', null);
        }

        $query = $this->db->get();
        // echo '<pre>'; print_r($this->db->last_query()); exit;
        return $query->result_array();
    }

    public function getReturnOpeningStockBill($hospital_id = null, $store_id = null)
    {
        $this->db->select('return_supplier_bill_basic.*, main_stores.store_name, main_stores.store_name')
            ->from('return_supplier_bill_basic')
            ->join('main_stores', 'main_stores.id = return_supplier_bill_basic.transfer_store_id', 'left')
            ->order_by('return_supplier_bill_basic.id', 'desc');

        // Apply filters if hospital_id and store_id are provided
        if (!empty($hospital_id)) {
            $this->db->where('return_supplier_bill_basic.hospital_id', $hospital_id);
        }

        if (!empty($store_id)) {
            $this->db->where('return_supplier_bill_basic.target_store_id', $store_id);
        }

        $query = $this->db->get();
        return $query->result_array();
    }


    public function getSingleReturnOpeningStockDetail($id)
    {
        $this->db->select('return_supplier_bill_basic.*');
        $this->db->where('return_supplier_bill_basic.id', $id);
        $query = $this->db->get('return_supplier_bill_basic');
        return $query->row_array();
    }

    public function getSingleOpeningStockDetail($id)
    {
        $this->db->select('supplier_bill_basic.*');
        $this->db->where('supplier_bill_basic.id', $id);
        $query = $this->db->get('supplier_bill_basic');
        return $query->row_array();
    }

    public function getDepartmentPharmasistBatchNoList($medicine, $hospital_id = null, $store_id = null, $role)
    {
        $this->db->select('
            medicine_batch_details.*, 
            supplier_bill_basic.status,
            supplier_bill_basic.request_type, 
            supplier_bill_basic.parent_request_id
        ');
        $this->db->from('medicine_batch_details');
        $this->db->join('supplier_bill_basic', 'medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id', 'left');

        $this->db->where([
            'medicine_batch_details.pharmacy_id' => $medicine,
        ]);
        $this->db->where('medicine_batch_details.available_quantity >', 0);

        if (!is_null($hospital_id)) {
            $this->db->where('medicine_batch_details.hospital_id', $hospital_id);
        }

        if (!is_null($store_id)) {
            if ($role === 'Chief Pharmacist' || $role === 'Store In-Charge') {
                $this->db->where('medicine_batch_details.store_id', $store_id);
            } else {
                $this->db->where('medicine_batch_details.target_store_id', $store_id);
            }
            $this->db->where('medicine_batch_details.bill_status', 'final');
        }

        return $this->db->get()->result_array();
    }



    public function generateBillNumber()
    {
        $this->db->select_max('bill_no');
        $query = $this->db->get('pharmacy_bill_basic');

        $lastBill = $query->row_array();
        return isset($lastBill['bill_no']) ? ($lastBill['bill_no'] + 1) : 1001; // Start from 1001 if no record exists
    }

    public function deleteBillDetail($bill_detail_id)
    {
        $this->db->where('id', $bill_detail_id);
        return $this->db->delete('medicine_batch_details'); // Replace with actual table name
    }

    public function get_admin_stock_summary()
    {
        // Group by pharmacy_id, sum quantities & values
        $this->db->select("
        pharmacy_id, 
        SUM(available_quantity) as total_quantity,
        SUM(available_quantity * purchase_price) as total_purchase_value
    ");
        $this->db->from('medicine_batch_details');
        $this->db->where('bill_status', 'final');
        $this->db->where('operation_type', null);
        $this->db->where('status', null);
        $this->db->where('approved_quantity', null);
        $this->db->group_by('pharmacy_id');

        $subquery = $this->db->get_compiled_select();

        // Query over grouped result
        $this->db->select("
        COUNT(*) as total_unique_pharmacy,
        COUNT(CASE WHEN total_quantity > 0 THEN 1 END) as in_stock_count,
        COUNT(CASE WHEN total_quantity = 0 THEN 1 END) as out_of_stock_count,
        ROUND(SUM(CASE WHEN total_quantity > 0 THEN total_purchase_value ELSE 0 END), 2) as in_stock_amount
    ");
        $this->db->from("($subquery) as grouped");

        $query = $this->db->get();
        return $query->row();
    }


    public function get_stock_summary($hospital_id = null, $store_id = null, $role = null)
    {
        // Step 1: Subquery to merge batches per pharmacy_id,
        // but only for pharmacy_ids that exist in the pharmacy table
        $this->db->select("
        medicine_batch_details.pharmacy_id,
        SUM(medicine_batch_details.available_quantity) as total_quantity,
        MAX(medicine_batch_details.expiry_date) as max_expiry,
        SUM(medicine_batch_details.available_quantity * medicine_batch_details.purchase_price) as purchase_total,
        SUM(medicine_batch_details.available_quantity * medicine_batch_details.sale_rate) as sale_total
    ");
        $this->db->from('medicine_batch_details');
        $this->db->join('pharmacy', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'inner'); // Ensures only valid pharmacy_ids

        $this->db->where('medicine_batch_details.operation_type', null);
        $this->db->where('medicine_batch_details.status', null);
        $this->db->where('medicine_batch_details.approved_quantity', null);
        $this->db->where('medicine_batch_details.bill_status', 'final');

        if (!is_null($hospital_id)) {
            $this->db->where('medicine_batch_details.hospital_id', $hospital_id);
        }

        if ($store_id && ($role == 'Store In-Charge' || $role == 'Chief Pharmacist')) {
            $this->db->where('medicine_batch_details.store_id', $store_id);
        }

        if ($store_id && $role == 'Department Pharmacist') {
            $this->db->where('medicine_batch_details.target_store_id', $store_id);
        }

        $this->db->group_by('medicine_batch_details.pharmacy_id');
        $subquery = $this->db->get_compiled_select();

        // Step 2: Final aggregation on top of the grouped data
        $this->db->select("
        COUNT(CASE WHEN total_quantity > 0 THEN 1 END) as in_stock_count,
        COUNT(CASE WHEN total_quantity = 0 THEN 1 END) as out_of_stock_count,
        ROUND(SUM(purchase_total), 2) as total_purchase_amount,
        ROUND(SUM(sale_total), 2) as total_sale_amount,
        COUNT(CASE WHEN max_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as near_expiry_count,
        COUNT(CASE WHEN max_expiry < CURDATE() THEN 1 END) as expired_count,
        ROUND(SUM(CASE WHEN total_quantity > 0 THEN purchase_total ELSE 0 END), 2) as in_stock_amount
    ");
        $this->db->from("($subquery) as stock_summary");

        $query = $this->db->get();
        return $query->row();
    }


    public function get_expiry_count_this_week($hospital_id = null, $store_id = null, $role = null)
    {
        $start_date = date('Y-m-d', strtotime('last monday 00:00:00'));
        $end_date = date('Y-m-d', strtotime('next sunday 23:59:59'));

        $this->db->select('COUNT(*) AS expiry_count_this_week');
        $this->db->from('medicine_batch_details mbd');
        $this->db->join('pharmacy p', 'p.id = mbd.pharmacy_id', 'inner');

        $this->db->where('mbd.operation_type', null);
        $this->db->where('mbd.status', null);
        $this->db->where('mbd.approved_quantity', null);
        $this->db->where('mbd.bill_status', 'final');
        $this->db->where('mbd.expiry_date_format IS NOT NULL', null, false);

        // Date filter: this week (Monday to Sunday)
        $this->db->where("mbd.expiry_date_format BETWEEN '$start_date' AND '$end_date'", null, false);

        if (!is_null($hospital_id)) {
            $this->db->where('mbd.hospital_id', $hospital_id);
        }

        if ($store_id && ($role === 'Store In-Charge' || $role === 'Chief Pharmacist')) {
            $this->db->where('mbd.store_id', $store_id);
        }

        if ($store_id && $role === 'Department Pharmacist') {
            $this->db->where('mbd.target_store_id', $store_id);
        }

        $query = $this->db->get();
        return $query->row();
    }





    public function get_issued_medicine_stats($hospital_id = null)
    {
        $this->db->select("
            COUNT(DISTINCT pharmacy_bill_detail.medicine_name) AS total_issued_medicines,
            SUM(pharmacy_bill_detail.quantity * pharmacy_bill_detail.sale_price) AS total_issued_price
        ");

        $this->db->from('pharmacy_bill_detail');
        $this->db->join('pharmacy', 'pharmacy_bill_detail.medicine_name = pharmacy.id', 'left');


        $query = $this->db->get();
        return $query->row(); // Returns single row with statistics
    }


    public function get_hospital_issued_medicine_stats($hospital_id = null, $store_id = null, $role = null,$date=null)
    {
        $this->db->select("
        COUNT(DISTINCT pharmacy_bill_detail.medicine_name) AS total_issued_medicines,
        SUM(pharmacy_bill_detail.quantity * pharmacy_bill_detail.sale_price) AS total_issued_price
        ");

        $this->db->from('pharmacy_bill_detail');
        $this->db->join('pharmacy', 'pharmacy_bill_detail.medicine_name = pharmacy.id', 'left');
        $this->db->join('pharmacy_bill_basic', 'pharmacy_bill_detail.pharmacy_bill_basic_id = pharmacy_bill_basic.id', 'left'); // ✅ Join with parent table
        $this->db->join('main_stores', 'pharmacy_bill_basic.store_id = main_stores.entity_id', 'left'); // ✅ Join with parent table
        // $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'left'); // ✅ Join with medicine_batch_details table

        // ✅ Apply Hospital ID filter from `pharmacy_bill_basic`
        if (!is_null($hospital_id)) {
            $this->db->where('pharmacy_bill_basic.hospital_id', $hospital_id);
        }

        if (!empty($date)) {
            $this->db->like('DATE(pharmacy_bill_basic.date)', $date);
            // OR use 'pharmacy_bill_basic.bill_date' depending on your column
        }

        // if (!is_null($store_id)) {
        //     $this->db->where('pharmacy_bill_basic.store_id', $store_id);
        // } 

        $query = $this->db->get();
        // echo '<pre>'; print_r($this->db->last_query());exit;
        return $query->row(); // Returns single row with statistics
    }


    public function get_hospital_issued_medicine_wards_stats($hospital_id = null, $store_id = null, $role = null)
    {
        $this->db->select("
        SUM(supplier_bill_basic.total) AS total_issued_price
        ");

        $this->db->from('supplier_bill_basic');
        // $this->db->join('pharmacy', 'pharmacy_bill_detail.medicine_name = pharmacy.id', 'left');
        // $this->db->join('pharmacy_bill_basic', 'pharmacy_bill_detail.pharmacy_bill_basic_id = pharmacy_bill_basic.id', 'left'); // ✅ Join with parent table
        $this->db->join('main_stores', 'supplier_bill_basic.target_store_id = main_stores.entity_id', 'left'); // ✅ Join with parent table
        // $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'left'); // ✅ Join with medicine_batch_details table

        // ✅ Apply Hospital ID filter from `pharmacy_bill_basic`
        if (!is_null($hospital_id)) {
            $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
        } 

        if (!is_null($store_id)) {
            $this->db->where('supplier_bill_basic.transfer_store_id', $store_id);
        } 

        $this->db->where_in('supplier_bill_basic.bill_status', ['final','partial']);

        $query = $this->db->get();
        // echo '<pre>'; print_r($this->db->last_query());exit;
        return $query->row(); // Returns single row with statistics
    }

    public function get_store_medicine_name($medicine_category_id, $target_store_id = null, $transfer_store_id = null, $supplier_id = null, $store_id = null)
    {
        $this->db->select('pharmacy.*, 
                           GROUP_CONCAT(medicine_batch_details.batch_no) as batch_numbers, 
                           GROUP_CONCAT(medicine_batch_details.expiry_date) as expiry_dates, 
                           GROUP_CONCAT(medicine_batch_details.target_store_id) as target_stores, 
                           GROUP_CONCAT(medicine_batch_details.transfer_store_id) as transfer_stores');
        $this->db->from('pharmacy');
        $this->db->join('medicine_batch_details', 'medicine_batch_details.pharmacy_id = pharmacy.id', 'left');
        $this->db->where('pharmacy.medicine_category_id', $medicine_category_id);
        $this->db->where('medicine_batch_details.target_store_id', $target_store_id);
        $this->db->where_in('medicine_batch_details.bill_status', ['final', 'partial']);
        $this->db->where('medicine_batch_details.transfer_store_id IS NOT NULL', null, false); // Ensure transfer_store_id is not null
        $this->db->group_by('pharmacy.id');

        $query = $this->db->get();
        return $query->result_array();
    }


    public function get_main_store_medicine_name($medicine_category_id, $target_store_id = null, $transfer_store_id = null, $supplier_id = null, $store_id = null)
    {
        $this->db->select('pharmacy.*, 
                       GROUP_CONCAT(medicine_batch_details.batch_no) as batch_numbers, 
                       GROUP_CONCAT(medicine_batch_details.expiry_date) as expiry_dates, 
                       GROUP_CONCAT(medicine_batch_details.target_store_id) as target_stores, 
                       GROUP_CONCAT(medicine_batch_details.transfer_store_id) as transfer_stores');

        $this->db->from('pharmacy');

        // Join with batch details
        $this->db->join('medicine_batch_details', 'medicine_batch_details.pharmacy_id = pharmacy.id', 'left');

        // Join with supplier_bill_basic (parent of medicine_batch_details)
        $this->db->join('supplier_bill_basic', 'supplier_bill_basic.id = medicine_batch_details.supplier_bill_basic_id', 'left');

        // Always filter by medicine_category_id
        $this->db->where('pharmacy.medicine_category_id', $medicine_category_id);


        if (!is_null($supplier_id)) {
            $this->db->where('supplier_bill_basic.supplier_id', $supplier_id);
        }

        if (!is_null($store_id)) {
            $this->db->where('medicine_batch_details.store_id', $store_id);
        }

        // Always apply these
        $this->db->where_in('medicine_batch_details.bill_status', ['final', 'partial']);

        $this->db->group_by('pharmacy.id');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getStorePharmasistBatchNoList($medicine, $hospital_id = null, $store_id = null, $transfer_store_id)
    {
        $this->db->where('pharmacy_id', $medicine)
            ->where('available_quantity >', 0);
        //  ->where('purchase_price >', 0);


        if (!is_null($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

        if (!is_null($store_id)) {
            $this->db->where('target_store_id', $store_id);
            $this->db->where('transfer_store_id', $transfer_store_id);
            $this->db->where('transfer_store_id IS NOT NULL', null, false);
            $this->db->where('bill_status', 'final');
        }

        $query = $this->db->get('medicine_batch_details');
        return $query->result_array();
    }

    public function store_pharmacy_medicine_qty($id, $hospital_id, $store_id, $transfer_store_id, $batch_id)
    {
        $this->db->select('pharmacy.*, 
                  medicine_category.id as medicine_category_id, 
                  medicine_category.medicine_category, 
                  SUM(medicine_batch_details.available_quantity) as total_qty, 
                  ROUND(SUM(medicine_batch_details.available_quantity * medicine_batch_details.sale_rate), 2) as total_sale,
                  ROUND(SUM(medicine_batch_details.available_quantity * medicine_batch_details.purchase_price), 2) as total_purchase');
        $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');
        $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'inner'); // Ensures only relevant batch details are included

        // Apply filters for hospital_id and target_store_id in medicine_batch_details table
        if (!empty($hospital_id)) {
            $this->db->where('medicine_batch_details.hospital_id', $hospital_id);
        }

        if (!empty($batch_id)) {
            $this->db->where('medicine_batch_details.id', $batch_id);
        }

        $this->db->where('medicine_batch_details.pharmacy_id', $id);

        if (!empty($store_id)) {
            $this->db->where('medicine_batch_details.target_store_id', $store_id);
            $this->db->where('medicine_batch_details.transfer_store_id', $transfer_store_id);
            $this->db->where('medicine_batch_details.bill_status', 'final');
            $this->db->where('medicine_batch_details.transfer_store_id IS NOT NULL', null, false);
        }
        $this->db->where('pharmacy.id', $id); // Added grouping for medicine_category.id

        // Group by pharmacy.id to avoid duplicates
        $this->db->group_by('pharmacy.id, medicine_category.id'); // Added grouping for medicine_category.id

        $query = $this->db->get('pharmacy');

        // Check if query execution was successful
        if (!$query) {
            log_message('error', 'Database error in store_medicine_search_datatable: ' . $this->db->last_query());
            return [];
        }

        return $query->result();
    }
    public function updateStoreStock($where_condition, $update_quantity)
    {
        // Extract supplier_id if present
        $supplier_id = null;
        if (isset($where_condition['supplier_id'])) {
            $supplier_id = $where_condition['supplier_id'];
            unset($where_condition['supplier_id']); // Remove to avoid ambiguity
        }

        // Start query with join if supplier_id is involved
        $this->db->select('medicine_batch_details.available_quantity');
        $this->db->from('medicine_batch_details');

        if ($supplier_id !== null) {
            $this->db->join(
                'supplier_bill_basic',
                'supplier_bill_basic.id = medicine_batch_details.supplier_bill_basic_id'
            );
            $this->db->where('supplier_bill_basic.supplier_id', $supplier_id);
        }

        // Apply the rest of the conditions from where_condition
        foreach ($where_condition as $key => $value) {
            $this->db->where("medicine_batch_details.$key", $value);
        }

        $query = $this->db->get();
        $row = $query->row();

        if ($row) {
            $current_qty = (int)$row->available_quantity;

            if ($update_quantity > $current_qty) {
                return ['status' => false, 'message' => 'Insufficient stock! Available: ' . $current_qty];
            }

            // Perform the update
            $this->db->set('available_quantity', 'available_quantity - ' . (int)$update_quantity, false);

            // Re-apply conditions for the update
            if ($supplier_id !== null) {
                $this->db->join(
                    'supplier_bill_basic',
                    'supplier_bill_basic.id = medicine_batch_details.supplier_bill_basic_id'
                );
                $this->db->where('supplier_bill_basic.supplier_id', $supplier_id);
            }

            foreach ($where_condition as $key => $value) {
                $this->db->where("medicine_batch_details.$key", $value);
            }

            $update_status = $this->db->update('medicine_batch_details');

            return ['status' => true, 'message' => 'Stock updated successfully'];
        }

        return ['status' => false, 'message' => 'Batch not found'];
    }


    public function updateMainStoreStock($where_condition, $update_quantity)
    {
        // Check and extract supplier_id if present
        $supplier_id = null;
        if (isset($where_condition['supplier_id'])) {
            $supplier_id = $where_condition['supplier_id'];
            unset($where_condition['supplier_id']); // Avoid ambiguity
        }

        $this->db->select('medicine_batch_details.available_quantity');
        $this->db->from('medicine_batch_details');

        // Join parent table only if supplier_id is passed
        if ($supplier_id !== null) {
            $this->db->join('supplier_bill_basic', 'supplier_bill_basic.id = medicine_batch_details.supplier_bill_basic_id');
            $this->db->where('supplier_bill_basic.supplier_id', $supplier_id);
        }

        // Avoid ambiguity by prefixing each where condition with correct table
        foreach ($where_condition as $key => $value) {
            $this->db->where("medicine_batch_details.$key", $value);
        }

        $query = $this->db->get();
        $row = $query->row();

        if ($row) {
            $current_qty = (int)$row->available_quantity;
            $new_qty = $current_qty + (int)$update_quantity;

            // Perform the update with same base conditions
            $this->db->set('available_quantity', $new_qty);
            foreach ($where_condition as $key => $value) {
                $this->db->where("medicine_batch_details.$key", $value);
            }

            $update_status = $this->db->update('medicine_batch_details');

            if ($update_status) {
                return ['status' => true, 'message' => 'Stock updated successfully. New quantity: ' . $new_qty];
            } else {
                return ['status' => false, 'message' => 'Database error: Unable to update stock.'];
            }
        }

        return ['status' => false, 'message' => 'Batch not found'];
    }

    public function getMainStoreReturnOpeningStockBill($hospital_id = null, $store_id = null)
    {
        $this->db->select('return_supplier_bill_basic.*, main_stores.store_name, main_stores.store_name')
            ->from('return_supplier_bill_basic')
            ->join('main_stores', 'main_stores.id = return_supplier_bill_basic.target_store_id', 'left')
            ->order_by('return_supplier_bill_basic.id', 'desc');

        // Apply filters if hospital_id and store_id are provided
        if (!empty($hospital_id)) {
            $this->db->where('return_supplier_bill_basic.hospital_id', $hospital_id);
        }
        if (!empty($store_id)) {
            $this->db->where('return_supplier_bill_basic.transfer_store_id', $store_id);
        }

        $query = $this->db->get();
        return $query->result_array();
    }
    public function getMainStoreSupplierReturnOpeningStockBill($hospital_id = null, $store_id = null)
    {
        $this->db->select('
        return_supplier_bill_basic.*, 
        main_stores.store_name, 
        supplier_category.supplier_category
    ')
            ->from('return_supplier_bill_basic')
            ->join('main_stores', 'main_stores.id = return_supplier_bill_basic.store_id', 'left')
            ->join('supplier_category', 'supplier_category.id = return_supplier_bill_basic.supplier_id', 'left') // Add this line
            ->order_by('return_supplier_bill_basic.id', 'desc');

        // Apply filters
        if (!empty($hospital_id)) {
            $this->db->where('return_supplier_bill_basic.hospital_id', $hospital_id);
        }
        if (!empty($store_id)) {
            $this->db->where('return_supplier_bill_basic.store_id', $store_id);
        }

        $query = $this->db->get();
        return $query->result_array();
    }



    public function getStoreTransferStockReport($where_condition, $hospital_id = null, $store_id = null)
    {
        $this->db->select('
        supplier_bill_basic.id,
        supplier_bill_basic.user_id,
        supplier_bill_basic.hospital_id,
        supplier_bill_basic.transfer_store_id,
        supplier_bill_basic.net_amount,
        supplier_bill_basic.date,
        supplier_bill_basic.purchase_no,
        supplier_bill_basic.store_name,
        supplier_bill_basic.total,
        supplier_bill_basic.bill_status,
        supplier_bill_basic.purchase_no,

        medicine_batch_details.*,
        pharmacy.medicine_name,
        pharmacy.unit,
        pharmacy.id as medicine_id,
        medicine_category.medicine_category,
        users.username, 
        main_stores.store_name as transfer_store_name
    ')
            ->from('supplier_bill_basic')
            ->join('medicine_batch_details', 'medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id', 'left')
            ->join('pharmacy', 'medicine_batch_details.pharmacy_id = pharmacy.id', 'left')
            ->join('medicine_category', 'medicine_batch_details.medicine_category_id = medicine_category.id', 'left')
            ->join('main_stores', 'main_stores.id = supplier_bill_basic.transfer_store_id', 'left')
            ->join('users', 'users.id = supplier_bill_basic.user_id', 'left'); // ✅ Added join with users table

        if (!empty($hospital_id)) {
            $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
        }

        $this->db->where('supplier_bill_basic.request_type', null);

        if (!empty($store_id)) {
            $this->db->where('supplier_bill_basic.transfer_store_id !=', '');
            $this->db->where('supplier_bill_basic.bill_status', 'final');
            $this->db->where('supplier_bill_basic.target_store_id', $store_id);
        }

        if (!empty($where_condition['search']) && isset($this->stock_report_column_search)) {
            $this->db->group_start();
            foreach ($this->stock_report_column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }

        $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
        $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        if ($length > 0) {
            $this->db->limit($length, $start);
        }

        $order_dir = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc', 'desc'])
            ? $_POST['order'][0]['dir']
            : 'desc';
        $this->db->order_by('supplier_bill_basic.id', $order_dir);

        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'Database error in getStoreTransferStockReport: ' . $this->db->last_query());
            return [];
        }

        return $query->result_array();
    }


    public function getStoreTransferStockReportCount($hospital_id = null, $store_id = null, $where_condition = [])
    {
        $this->db->from('supplier_bill_basic');
        $this->db->join('medicine_batch_details', 'medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id', 'left');
        $this->db->join('pharmacy', 'medicine_batch_details.pharmacy_id = pharmacy.id', 'left');
        $this->db->join('medicine_category', 'medicine_batch_details.medicine_category_id = medicine_category.id', 'left');

        if (!empty($hospital_id)) {
            $this->db->where('supplier_bill_basic.hospital_id', $hospital_id);
        }

        $this->db->where('supplier_bill_basic.request_type', null);

        if (!empty($store_id)) {
            $this->db->where('supplier_bill_basic.transfer_store_id !=', '');
            $this->db->where('supplier_bill_basic.bill_status', 'final');
            $this->db->where('supplier_bill_basic.target_store_id', $store_id);
        }

        if (!empty($where_condition['search']) && isset($this->stock_report_column_search)) {
            $this->db->group_start();
            foreach ($this->stock_report_column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }
    public function checkBillNoExists($bill_no, $hospital_id)
    {
        $this->db->where('invoice_no', $bill_no);
        $this->db->where('hospital_id', $hospital_id);
        $query = $this->db->get('supplier_bill_basic');

        return $query->num_rows() > 0;
    }
}
