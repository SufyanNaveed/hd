<?php

class Department_model extends CI_model
{

    protected $column_search = ['department_name', 'department_unique_id', 'store_name'];

    public function valid_department($str)
    {
        $type = $this->input->post('type');
        $id = $this->input->post('departmenttypeid');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_department_exists($type, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function getall()
    {
        $this->datatables->select('id,department_name,is_active');
        $this->datatables->from('department');
        if ($this->rbac->hasPrivilege('department', 'can_edit')) {
            $edit = '<a onclick="get($1)" class="btn btn-default btn-xs" data-target="#editmyModal" data-toggle="tooltip" title="" data-original-title=' . $this->lang->line('edit') . '> <i class="fa fa-pencil"></i></a>';
        } else {
            $edit = '';
        }

        if ($this->rbac->hasPrivilege('department', 'can_delete')) {
            $delete = '<a  class="btn btn-default btn-xs" onclick="deleterecord($1)" data-toggle="tooltip" title=""  data-original-title=' . $this->lang->line('delete') . '><i class="fa fa-trash"></i></a>';
        } else {
            $delete = '';
        }

        $this->datatables->add_column('view', $edit . $delete, 'id,is_active');
        return $this->datatables->generate();
    }

    function check_department_exists($name, $id)
    {
        if ($id != 0) {
            $data = array('id != ' => $id, 'department_name' => $name);
            $query = $this->db->where($data)->get('department');
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {

            $this->db->where('department_name', $name);
            $query = $this->db->get('department');
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    function deleteDepartment($id)
    {
        $this->db->where("id", $id)->delete("department");
    }

    function getDepartmentType($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('department');
            return $query->row_array();
        } else {
            $query = $this->db->get("department");
            return $query->result_array();
        }
    }

    public function addDepartmentType($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('department', $data);
        } else {
            $this->db->insert('department', $data);
            return $this->db->insert_id();
        }
    }

    public function deleteDepartmentById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('departments');
    }
    public function getDepartmentDetails($id)
    {
        $this->db->select('departments.*, main_stores.store_name');
        $this->db->from('departments');
        $this->db->join('main_stores', 'departments.store_id = main_stores.id', 'left');
        $this->db->where('departments.id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    public function addDepartment($data)
    {
        return $this->db->insert('departments', $data);
    }
    public function getLastDepartment()
    {
        $this->db->select('department_unique_id');
        $this->db->from('departments');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }
    public function search_department_datatable($where_condition, $columnName, $columnSortOrder, $start, $length)
    {
        // Update the select statement to include hospitals table
        $this->db->select('departments.id, departments.department_unique_id, departments.department_name, hospitals.name AS hospital_name');
        $this->db->from('departments');
        $this->db->join('hospitals', 'departments.hospital_id = hospitals.id', 'left'); // Change the join to hospitals

        // Apply search conditions
        if (!empty($where_condition['search'])) {
            $this->db->group_start();
            $this->db->like('departments.department_name', $where_condition['search']);
            $this->db->or_like('departments.department_unique_id', $where_condition['search']);
            $this->db->or_like('hospitals.name', $where_condition['search']); // Adjust for hospitals table
            $this->db->group_end();
        }

        if (isset($where_condition['hospital_id'])) {
            $this->db->where('hospitals.id', $where_condition['hospital_id']); 
        } 

        // Sorting
        if (!empty($columnName) && !empty($columnSortOrder)) {
            $this->db->order_by($columnName, $columnSortOrder);
        } else {
            $this->db->order_by('departments.id', 'DESC');
        }

        // Limit and fetch the results
        $this->db->limit($length, $start);
        $query = $this->db->get();
        return $query->result();
    }

    public function search_department_datatable_count($where_condition)
    {
        // Update the select statement
        $this->db->select('departments.id');
        $this->db->from('departments');
        $this->db->join('hospitals', 'departments.hospital_id = hospitals.id', 'left'); // Change the join to hospitals

        // Apply search conditions
        if (!empty($where_condition['search'])) {
            $this->db->group_start();
            $this->db->like('departments.department_name', $where_condition['search']);
            $this->db->or_like('departments.department_unique_id', $where_condition['search']);
            $this->db->or_like('hospitals.name', $where_condition['search']); // Adjust for hospitals table
            $this->db->group_end();
        }

        if (isset($where_condition['hospital_id'])) {
            $this->db->where('hospitals.id', $where_condition['hospital_id']); 
        } 

        return $this->db->count_all_results();
    }


    public function updateDepartment($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update('departments', array(
            'department_name' => $data['department_name'],
            'hospital_id'       => $data['hospital_id'],
        ));
    }

    public function getAllDepartments($hospitalId)
    {
        return $this->db->select('id, department_name AS name,hospital_id')->from('departments')->where('hospital_id',$hospitalId)->get()->result_array();
    }

    public function getDepartmentsByHospital($hospitalId) {
        $this->db->select('id, department_name');
        $this->db->from('departments');
        $this->db->where('hospital_id', $hospitalId);
        $query = $this->db->get();
    
        return $query->result_array();
    }
    
}
