<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_model extends CI_Model
{
    protected $column_search = [
        'users.username',
        'users.mobileno',
        'users.employee_code',
        'users.cnic',
        'hospitals.name',
        'departments.department_name',
        'main_stores.store_name'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('users', $data);
        } else {
            $this->db->insert('users', $data);
            return $this->db->insert_id();
        }
    }

    public function updateUser($id, $status)
    {
        $query     = $this->db->select("users.id")->where("user_id", $id)->get("users");
        $result    = $query->row_array();
        $user_data = array(
            'id'        => $result['id'],
            'is_active' => $status,
        );
        $this->add($user_data);
    }

    public function addNewParent($data_parent_login, $student_data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert('users', $data_parent_login);
        $insert_id                 = $this->db->insert_id();
        $student_data['parent_id'] = $insert_id;
        $this->student_model->add($student_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function checkLogin($data)
    {
        $this->db->select('id, username, password,role,is_active');
        $this->db->from('users');
        $this->db->where('username', $data['username']);
        $this->db->where('password', ($data['password']));
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function checkLoginParent($data)
    {
        $sql   = "SELECT users.*,students.admission_no,students.admission_no ,students.guardian_name, students.roll_no,students.admission_date,students.firstname, students.lastname,students.image,students.father_pic,students.mother_pic,students.guardian_pic,students.guardian_relation, students.mobileno, students.email ,students.state , students.city , students.pincode , students.religion, students.dob ,students.current_address, students.permanent_address FROM `users` INNER JOIN (select * from students) as students on students.parent_id= users.id WHERE `username` = " . $this->db->escape($data['username']) . " AND `password` = " . $this->db->escape($data['password']) . " LIMIT 0,1";
        $query = $this->db->query($sql);
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function read_user_information($users_id)
    {
        $this->db->select('users.*,patients.patient_name,patients.image,patients.guardian_name,patients.patient_type,languages.id as lang_id,languages.language');
        $this->db->from('users');
        $this->db->join('patients', 'patients.id = users.user_id');
        $this->db->join('languages', 'patients.lang_id = languages.id', 'left');
        $this->db->where('users.id', $users_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function read_teacher_information($users_id)
    {
        $this->db->select('users.*,teachers.name');
        $this->db->from('users');
        $this->db->join('teachers', 'teachers.id = users.user_id');
        $this->db->where('users.id', $users_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function read_accountant_information($users_id)
    {
        $this->db->select('users.*,accountants.name');
        $this->db->from('users');
        $this->db->join('accountants', 'accountants.id = users.user_id');
        $this->db->where('users.id', $users_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function read_librarian_information($users_id)
    {
        $this->db->select('users.*,librarians.name');
        $this->db->from('users');
        $this->db->join('librarians', 'librarians.id = users.user_id');
        $this->db->where('users.id', $users_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function checkOldUsername($data)
    {
        $this->db->where('id', $data['user_id']);
        $this->db->where('username', $data['username']);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkOldPass($data)
    {
        $this->db->where('id', $data['user_id']);
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            $user = $query->row_array();

            // Verify hashed password
            if ($this->enc_lib->passHashDyc($data['current_pass'], $user['password'])) {
                return true;
            }
        }
        return false;
    }


    public function checkUserNameExist($data)
    {
        $this->db->where('role', $data['role']);
        $this->db->where('username', $data['new_username']);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function saveNewPass($data)
    {
        $this->db->where('id', $data['id']);
        $query = $this->db->update('users', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function changeStatus($data)
    {
        $this->db->where('id', $data['id']);
        $query = $this->db->update('users', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function saveNewUsername($data)
    {
        $this->db->where('id', $data['id']);
        $query = $this->db->update('users', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function read_user()
    {
        $this->db->select('*');
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function read_single_child($child_id)
    {
        $this->db->select('*');
        $this->db->where('childs', $child_id);
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function getLoginDetails($student_id)
    {
        $sql   = "SELECT * FROM (select * from users where find_in_set('$student_id',childs) <> 0 union SELECT * FROM `users` WHERE `user_id` = " . $this->db->escape($student_id) . " AND `role` != 'teacher' AND `role` != 'librarian' AND `role` != 'accountant') a order by a.role desc";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getStudentLoginDetails($student_id)
    {
        $sql   = "SELECT users.* FROM users WHERE id in (select students.parent_id from users INNER JOIN students on students.id =users.user_id WHERE users.user_id=" . $this->db->escape($student_id) . " AND users.role ='student') UNION select users.* from users INNER JOIN students on students.id =users.user_id WHERE users.user_id=" . $this->db->escape($student_id) . " AND users.role ='student'";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTeacherLoginDetails($teacher_id)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('user_id', $teacher_id);
        $this->db->where('role', 'teacher');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getLibrarianLoginDetails($librarian_id)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('user_id', $librarian_id);
        $this->db->where('role', 'librarian');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getAccountantLoginDetails($accountant_id)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('user_id', $accountant_id);
        $this->db->where('role', 'accountant');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function updateVerCode($data)
    {
        $this->db->where('id', $data['id']);
        $query = $this->db->update('users', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserByEmail($table, $role, $email)
    {
        $this->db->select($table . '.*,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`');
        $this->db->from($table);
        $this->db->join('users', 'users.user_id = ' . $table . '.id', 'left');
        $this->db->where('users.role', $role);
        if ($role == 'patient') {
            $this->db->where($table . '.email', $email);
        }
        $query = $this->db->get();
        if ($email != null) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function getUserValidCode($table, $role, $code)
    {
        $this->db->select($table . '.*,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`');
        $this->db->from($table);
        $this->db->join('users', 'users.user_id = ' . $table . '.id', 'left');
        $this->db->where('users.role', $role);
        $this->db->where('users.verification_code', $code);
        $query = $this->db->get();
        if ($code != null) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function forgotPassword($usertype, $email)
    {
        $result = false;
        if ($usertype == 'patient') {
            $table  = "patients";
            $role   = "patient";
            $result = $this->getUserByEmail($table, $role, $email);
        }
        return $result;
    }

    public function getUserByCodeUsertype($usertype, $code)
    {
        $result = false;
        if ($usertype == 'patient') {
            $table  = "patients";
            $role   = "patient";
            $result = $this->getUserValidCode($table, $role, $code);
        }
        return $result;
    }

    public function reset_password($usertype, $code, $password)
    {
        echo "string";
        exit();
    }

    public function getUserLoginDetails($student_id)
    {
        $sql   = "SELECT users.* FROM users WHERE user_id =" . $student_id . " and role = 'student'";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function getParentLoginDetails($student_id)
    {
        $sql   = "SELECT users.* FROM `users` join students on students.parent_id = users.id WHERE students.id = " . $student_id;
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function add_user($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    // Check if Employee Code is Unique
    public function is_employee_code_unique($employee_code)
    {
        $this->db->where('employee_code', $employee_code);
        $query = $this->db->get('users');
        return $query->num_rows() === 0; // Returns true if unique
    }
    public function searchuser_datatable($where_condition, $columnName, $columnSortOrder, $start, $length, $hospital_id = null, $user_id = null)
    {
        $this->db->select("users.*, 
                            roles.name as role_name, 
                            hospitals.name as hospital_name, 
                            departments.department_name, 
                            main_stores.store_name")
            ->from('users')
            ->join('roles', 'users.role_id = roles.id', 'left')
            ->join('hospitals', 'users.hospital_id = hospitals.id', 'left')
            ->join('departments', 'users.department_id = departments.id', 'left') // Joining departments table
            ->join('main_stores', 'users.store_id = main_stores.id', 'left'); // Joining stores table

        // Optional hospital_id filter
        if (!empty($hospital_id)) {
            $this->db->where('users.hospital_id', $hospital_id);
        }

        if (!empty($user_id)) {
            $this->db->where('users.created_by', $user_id);
        }

        // Handle search filter
        if (!empty($where_condition['search'])) {
            $this->db->group_start();
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }

        // Handle ordering
        if (!empty($columnName) && !empty($columnSortOrder)) {
            $this->db->order_by($columnName, $columnSortOrder);
        } else {
            $this->db->order_by('users.id', 'DESC');
        }

        // Handle pagination
        if (!empty($length) && $length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        return $query->result();
    }



    public function searchuser_datatable_count($where_condition = [], $hospital_id = null, $user_id = null)
    {
        $this->db->from('users')
            ->join('roles', 'users.role_id = roles.id', 'left')
            ->join('hospitals', 'users.hospital_id = hospitals.id', 'left')
            ->join('departments', 'users.department_id = departments.id', 'left')
            ->join('main_stores', 'users.store_id = main_stores.id', 'left');

        // Optional hospital_id filter
        if (!empty($hospital_id)) {
            $this->db->where('users.hospital_id', $hospital_id);
        }

        // Optional user_id filter
        if (!empty($user_id)) {
            $this->db->where('users.created_by', $user_id);
        }

        // Handle search filter
        if (!empty($where_condition) && isset($where_condition['search']) && !empty($where_condition['search'])) {
            $this->db->group_start();
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }




    public function getUserById($id)
    {
        $this->db->select('
            users.id as userId, 
            users.username, 
            users.father_name, 
            users.employee_code, 
            users.cnic, 
            users.mobileno, 
            users.role_id, 
            roles.name as role_name, 
            users.shift_start_time, 
            users.shift_end_time, 
            users.hospital_id, 
            users.department_id, 
            users.store_id
        ');
        $this->db->from('users');
        $this->db->join('roles', 'users.role_id = roles.id', 'left');
        $this->db->where('users.id', $id);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    public function update_user($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }
    public function delete_user($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }
    public function updateUserAssignments($userId, $hospitalId, $departmentId, $storeId)
    {
        // Data to update
        $data = [
            'hospital_id' => $hospitalId,
            'department_id' => $departmentId,
            'store_id' => $storeId
        ];

        // Update query
        $this->db->where('id', $userId);
        return $this->db->update('users', $data); // Returns TRUE on success
    }


    /**
     * Authenticate user with email and password
     * @param string $email
     * @param string $password
     * @return array|bool
     */
    public function loginUser($username, $password)
    {
        // Fetch user, role, hospital, store, and department data
        $this->db->select('
            users.id, 
            users.username, 
            users.password, 
            roles.name as role_name, 
            hospitals.id as hospital_id, 
            hospitals.name as hospital_name, 
            hospitals.address as hospital_address,
            main_stores.id as store_id, 
            main_stores.store_name, 
            departments.id as department_id, 
            departments.department_name
        ');
        $this->db->from('users');
        $this->db->join('roles', 'users.role_id = roles.id', 'left'); // Join with roles table
        $this->db->join('hospitals', 'users.hospital_id = hospitals.id', 'left'); // Join with hospitals table
        $this->db->join('main_stores', 'users.store_id = main_stores.id', 'left'); // Join with main_stores table
        $this->db->join('departments', 'main_stores.department_id = departments.id', 'left'); // Join with departments table
        $this->db->where('users.cnic', $username);

        $query = $this->db->get();

        // Check if a user exists
        if ($query->num_rows() == 1) {
            $user = $query->row_array(); // Get user details as an array

            // Verify the password
            $pass_verify = $this->enc_lib->passHashDyc($password, $user['password']);
            if ($pass_verify) {
                unset($user['password']); // Remove the password from the result
                return $user; // Return the user details including store and department info
            } else {
                return false; // Password mismatch
            }
        } else {
            return false; // User not found
        }
    }

    public function ByPassloginUser($username)
    {
        // Fetch user, role, hospital, store, and department data
        $this->db->select('
            users.id, 
            users.username, 
            users.password, 
            roles.name as role_name, 
            hospitals.id as hospital_id, 
            hospitals.name as hospital_name, 
            hospitals.address as hospital_address,
            main_stores.id as store_id, 
            main_stores.store_name, 
            departments.id as department_id, 
            departments.department_name
        ');
        $this->db->from('users');
        $this->db->join('roles', 'users.role_id = roles.id', 'left'); // Join with roles table
        $this->db->join('hospitals', 'users.hospital_id = hospitals.id', 'left'); // Join with hospitals table
        $this->db->join('main_stores', 'users.store_id = main_stores.id', 'left'); // Join with main_stores table
        $this->db->join('departments', 'main_stores.department_id = departments.id', 'left'); // Join with departments table
        $this->db->where('users.cnic', $username);

        $query = $this->db->get();

        // Check if a user exists
        if ($query->num_rows() == 1) {
            $user = $query->row_array(); // Get user details as an array

            // Verify the password
            // $pass_verify = $this->enc_lib->passHashDyc($password, $user['password']);
            // if ($pass_verify) {
                unset($user['password']); // Remove the password from the result
                return $user; // Return the user details including store and department info
            // } else {
                // return false; // Password mismatch
            // }
        } else {
            return false; // User not found
        }
    }
    
    public function is_username_exists($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('users'); // Replace 'users' with your actual table name
        return $query->num_rows() > 0;
    }

    public function get_hospital_count()
    {
        $query = $this->db->select('COUNT(*) as hospital_count')
            ->get('hospitals');

        $result = $query->row_array();
        return $result['hospital_count'] ?? 0; // Return 0 if no hospitals found

    }

    public function get_products_count()
    {
        $query = $this->db->select('COUNT(*) as pharmacy_count')
            ->get('pharmacy');

        $result = $query->row_array();
        return $result['pharmacy_count'] ?? 0; // Return 0 if no hospitals found

    }

    public function get_hospital_products_count()
    {
        $query = $this->db->select('COUNT(*) as pharmacy_count')
            ->get('pharmacy');

        $result = $query->row_array();
        return $result['pharmacy_count'] ?? 0; // Return 0 if no hospitals found

    }

    public function get_pharmacies_count($type, $hospital_id = null)
    {
        $this->db->select('COUNT(*) as pharmacies_count');
        $this->db->where('entity_type', $type);
    
        // ✅ Apply Hospital ID filter if provided
        if (!is_null($hospital_id)) {
            $this->db->where('entity_id', $hospital_id);
        }
    
        $query = $this->db->get('main_stores');
        $result = $query->row_array();
    
        return $result['pharmacies_count'] ?? 0; // Return 0 if no records found
    }
    

    public function is_cnic_exists($cnic)
    {
        $query = $this->db->get_where('users', ['cnic' => $cnic]);
        return $query->num_rows() > 0;
    }
    public function total_requested($hospital_id = null,$store_id = null,$role=null)
    {
        $this->db->select('COUNT(*) as total_requested');
        $this->db->from('supplier_bill_basic');
        $this->db->where('status', 'requested');
    
        // ✅ Apply Hospital ID filter if provided
        if (!is_null($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

        if($store_id && ($role =='Store In-Charge' || $role =='Chief Pharmacist')){
            $this->db->where('transfer_store_id', $store_id);
        }


        if($store_id && $role =='Department Pharmacist'){
            $this->db->where('target_store_id', $store_id);
        }


    
        $query = $this->db->get();
        $result = $query->row();
    
        return $result ? $result->total_requested : 0; // Return 0 if no records found
    }
    
  
    public function total_approved($status, $hospital_id = null,$store_id = null ,$role = null)
    {
        $this->db->select('COUNT(*) as total_requested');
        $this->db->from('supplier_bill_basic');
        $this->db->where('status', $status);
    
        // ✅ Apply Hospital ID filter if provided
        if (!is_null($hospital_id)) {
            $this->db->where('hospital_id', $hospital_id);
        }

         if($store_id && ($role =='Store In-Charge' || $role =='Chief Pharmacist')){
            $this->db->where('transfer_store_id', $store_id);
        }


        if($store_id && $role =='Department Pharmacist'){
            $this->db->where('target_store_id', $store_id);
        }
    
        $query = $this->db->get();
        $result = $query->row();
    
        return $result ? $result->total_requested : 0; // Return 0 if no records found
    }
    

    public function total_patients($type = null,$hospital_id=null)
    {
        $this->db->from('patients');
 
        if($hospital_id){
            $this->db->where('hospital_id', $hospital_id);
        }
        if ($type === 'ipd') {
            $this->db->where('is_ipd', 'yes');
        } elseif ($type === 'opd') {
            $this->db->where('is_ipd', ''); // Assuming 'no' represents OPD patients
        }

        return $this->db->count_all_results();
    }

    public function get_medicine_count($where_condition = [])
{
    $this->db->select('COUNT(DISTINCT pharmacy.id) AS total_medicines');

    $this->db->from('pharmacy');

    // Joins
    $this->db->join('medicine_category', 'pharmacy.medicine_category_id = medicine_category.id', 'left');
    $this->db->join('medicine_batch_details', 'pharmacy.id = medicine_batch_details.pharmacy_id', 'left');
    $this->db->join('medicine_company', 'pharmacy.medicine_company_id = medicine_company.id', 'left');

    // Ensure we are correctly matching the medicine category
    $this->db->where('`pharmacy`.`medicine_category_id` = `medicine_category`.`id`');

    // ✅ Apply Hospital & Store Filters from `$where_condition`
    if (!empty($where_condition['hospital_id'])) {
        $this->db->where('medicine_batch_details.hospital_id', $where_condition['hospital_id']);
        $this->db->where('medicine_batch_details.bill_status', 'final');
    }


    if(!empty($where_condition['store_id']) && $where_condition['role'] =='Department Pharmacist'){
        $this->db->where('medicine_batch_details.target_store_id', $where_condition['store_id']);
    }

    if (!empty($where_condition['store_id']) && $where_condition['role'] =='Store In-Charge') {
        $this->db->where('medicine_batch_details.store_id', $where_condition['store_id']);
    }

    $query = $this->db->get();
    return $query->row()->total_medicines ?? 0; // Returns total medicine count
}

    public function getUserDepartments($id)
    {
        $this->db->select('department_id');
        $this->db->from('user_departments'); 
        $this->db->where('user_id', $id);
        $query = $this->db->get()->result_array(); 
        $all_ids = array_column($query, 'department_id');
        return $all_ids; // Return an array of department IDs
    }

}
