<?php

class Store_model extends CI_Model
{
    protected $column_search = ['main_stores.store_name'];

    // Add Store
    public function addStore($data)
    {
        return $this->db->insert('main_stores', $data);
    }

    // Get Last Store
    public function getLastStore()
    {
        $this->db->select('store_unique_id');
        $this->db->from('main_stores');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    public function search_store_datatable($where_condition, $columnName, $columnSortOrder, $start, $length)
    {
        $this->db->select('
            main_stores.*,
            CASE 
                WHEN main_stores.entity_type = \'hospital\' THEN hospitals.name
                WHEN main_stores.entity_type = \'department\' THEN departments.department_name
                ELSE NULL
            END AS entity_name
            
        ');
        $this->db->from('main_stores');
        // Adjust JOIN conditions based on entity_type
        $this->db->join('hospitals', 'main_stores.entity_id = hospitals.id', 'left');
        $this->db->join('departments', 'main_stores.entity_type = \'department\' AND main_stores.department_id = departments.id', 'left');
    
        
        if (isset($where_condition['hospital_id'])) {
            $this->db->where('main_stores.entity_id', $where_condition['hospital_id']); 
        } 

        // Apply search conditions
        if (!empty($where_condition['search'])) {
            $this->db->group_start();
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }

        // Apply sorting
        if (!empty($columnName) && !empty($columnSortOrder)) {
            $this->db->order_by($columnName, $columnSortOrder);
        } else {
            $this->db->order_by('main_stores.id', 'DESC');
        }
    
        // Apply pagination
        $this->db->limit($length, $start);
    
        $query = $this->db->get(); 
        return $query->result();
    }
    


    // Count Total Stores
    public function search_store_datatable_count($where_condition)
    {
        $this->db->from('main_stores'); 
        // Adjust JOIN conditions based on entity_type
        $this->db->join('hospitals', 'main_stores.entity_id = hospitals.id', 'left');
        //$this->db->join('departments', 'main_stores.entity_type = \'department\' AND main_stores.department_id = departments.id', 'left');
    
        if (isset($where_condition['hospital_id'])) {
            $this->db->where('main_stores.entity_id', $where_condition['hospital_id']); 
        } 
        // Apply search conditions
        if (!empty($where_condition['search'])) {
            $this->db->group_start();
            foreach ($this->column_search as $column) {
                $this->db->or_like($column, $where_condition['search']);
            }
            $this->db->group_end();
        }
    
        // Return the count of all matching records
        return $this->db->count_all_results();
    }


    // Get Store Details
    // Get Store Details
    public function getStoreDetails($id)
    {
        $this->db->select('
        main_stores.*,
        CASE 
            WHEN main_stores.entity_type = \'hospital\' THEN hospitals.name
            WHEN main_stores.entity_type = \'department\' THEN departments.department_name
        END AS entity_name
    ');
        $this->db->from('main_stores');

        // Join with hospitals and departments based on entity_type
        $this->db->join('hospitals', 'main_stores.entity_type = \'hospital\' AND main_stores.entity_id = hospitals.id', 'left');
        $this->db->join('departments', 'main_stores.entity_type = \'department\' AND main_stores.entity_id = departments.id', 'left');

        // Filter by store ID
        $this->db->where('main_stores.id', $id);

        $query = $this->db->get();
        return $query->row();
    }


    // Delete Store
    public function deleteStoreById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('main_stores');
    }
    public function updateStore($id, $data)
    {
        $this->db->where('id', $id); // Match the store ID
        return $this->db->update('main_stores', $data); // Update the store details
    }

    public function getAllStores()
    {
        $this->db->select('id, store_name');
        $this->db->from('main_stores');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getStoresByEntity($entityType, $entityId)
    {
        $this->db->select('id, store_name, store_unique_id');
        $this->db->from('main_stores');
        $this->db->where('entity_type', $entityType);

        // Apply condition on department_id if entity type is department
        if ($entityType === 'department' && $entityId !== null) {
            $this->db->where_in('department_id', $entityId);
        } else {
            $this->db->where('entity_id', $entityId);
        }

        $query = $this->db->get();

        return $query->result_array();
    }


    public function getStoresByDepartment($departmentId)
    {
        $this->db->select('id, store_name');
        $this->db->from('stores');
        $this->db->where('department_id', $departmentId); // Assuming `department_id` exists in the `stores` table
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array(); // Return stores as an array
        }
        return []; // Return an empty array if no stores are found
    }

    public function getHospitalDepartmentStores($entityType = null, $entityId)
    {
        $this->db->select('id, store_name, store_unique_id');
        $this->db->from('main_stores');
        if($entityType){
            $this->db->where('entity_type', $entityType);
        }

        // Apply condition on department_id if entity type is department
        if ($entityType === 'department' && $entityId !== null) {
            $this->db->where('hospital_id', $entityId);
        } else {
            $this->db->where('entity_id', $entityId);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

public function getStoreEntityType($storeId)
{
    $this->db->select('id, store_name, store_unique_id, entity_type, entity_id');
    $this->db->from('main_stores');
    $this->db->where('id', $storeId); // Filter by store ID
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->row_array(); // Return store details including entity_type
    }

    return null; // Return null if no store is found
}
}
