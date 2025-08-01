<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Item_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getItemByCategory($item_category_id)
    {
        $this->db->select('item.id,item.name,item.item_category_id,item_category.item_category,item_category.id as `item_category_id`');
        $this->db->from('item');
        $this->db->join('item_category', 'item_category.id = item.item_category_id');
        $this->db->where('item.item_category_id', $item_category_id);
        $this->db->order_by('item.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getItemunit($id)
    {
        $this->db->select('item.id,item.name,item.unit,item.item_category_id');
        $this->db->from('item');
        $this->db->where('item.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function valid_check_exists($str)
    {
        $name             = $this->input->post('name');
        $id               = $this->input->post('id');
        $item_category_id = $this->input->post('item_category_id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_data_exists($name, $item_category_id, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_data_exists($name, $item_category_id, $id)
    {
        $this->db->where('name', $name);
        $this->db->where('item_category_id', $item_category_id);
        $this->db->where('id !=', $id);
        $query = $this->db->get('item');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get($id = null, $hospital_id = null, $store_id = null)
{
    $query = "SELECT item.*, 
                     item_category.item_category, 
                     item_store.item_store, 
                     item_store.code, 
                     item_supplier.item_supplier, 
                     item_supplier.phone, 
                     item_supplier.email, 
                     item_supplier.address, 
                     IFNULL(item_issues.issued, 0) as `issued`, 
                     IFNULL(item_issues.returned, 0) as `returned`, 
                     IFNULL(item_stock.item_stock_quantity, 0) as added_stock 
              FROM `item` 
              LEFT JOIN item_category ON item.item_category_id = item_category.id 
              LEFT JOIN item_store ON item.item_store_id = item_store.id 
              LEFT JOIN item_supplier ON item.item_supplier_id = item_supplier.id 
              LEFT JOIN (
                  SELECT item_stock.item_id, SUM(quantity) AS item_stock_quantity 
                  FROM `item_stock` 
                  GROUP BY item_stock.item_id
              ) AS item_stock ON item_stock.item_id = item.id 
              LEFT JOIN (
                  SELECT m.item_id AS `issue_item_id`, 
                         IFNULL((SELECT SUM(quantity) FROM item_issue WHERE item_issue.item_id = m.item_id AND item_issue.is_returned = 1), 0) AS `issued`, 
                         IFNULL((SELECT SUM(quantity) FROM item_issue WHERE item_issue.item_id = m.item_id AND item_issue.is_returned = 0), 0) AS `returned` 
                  FROM item_issue m 
                  GROUP BY item_id
              ) AS item_issues ON item_issues.issue_item_id = item.id";

    // Add conditions
    $conditions = [];
    if ($id != null) {
        $conditions[] = "item.id = " . $id;
    }
    if ($hospital_id != null) {
        $conditions[] = "item.hospital_id = " . $hospital_id;
    }
    if ($store_id != null) {
        $conditions[] = "item.store_id = " . $store_id;
    }

    // Append conditions to query
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Execute query
    $query = $this->db->query($query);

    // Return results
    if ($id != null) {
        return $query->row_array();
    }
    return $query->result_array();
}


    public function getItemAvailable($item_id = null)
    {
        $where = "";
        $query = "SELECT item.*,item_category.item_category,item_store.item_store,item_store.code,item_supplier.item_supplier,item_supplier.phone,item_supplier.email,item_supplier.address,IFNULL(item_issues.issued,0) as `issued`,IFNULL(item_issues.returned,0) as `returned`,IFNULL(item_stock.item_stock_quantity,0) added_stock FROM `item` left JOIN item_category on item.item_category_id=item_category.id left JOIN item_store on item.item_store_id=item_store.id left JOIN item_supplier on item.item_supplier_id=item_supplier.id left JOIN (SELECT item_stock.item_id,sum(quantity) item_stock_quantity FROM `item_stock` group by item_stock.item_id) as item_stock on item_stock.item_id=item.id left JOIN (SELECT m.item_id as `issue_item_id`, IFNULL((SELECT SUM(quantity) FROM item_issue WHERE item_issue.item_id = m.item_id and item_issue.is_returned =1),0) as `issued` ,IFNULL((SELECT SUM(quantity) FROM item_issue WHERE item_issue.item_id = m.item_id and item_issue.is_returned =0),0) as `returned` FROM item_issue m GROUP BY item_id) as item_issues on item_issues.issue_item_id=item.id where item.id= " . $item_id;
        $query = $this->db->query($query);
        if ($item_id != null) {
            return $query->row_array();
        }
    }

    public function remove($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('item');
    }

    public function add($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('item', $data);
        } else {
            $this->db->insert('item', $data);
            return $this->db->insert_id();
        }
    }
}
