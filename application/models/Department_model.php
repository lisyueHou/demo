<?php
class Department_model extends CI_Model
{
    public $id = '';
    public $name = '';

    public function __construct()
    {
        $this->load->database();
    }

    // 取得部門資料
    public function get_department()
    {
        $sql = "SELECT * FROM `department` WHERE isDeleted = 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Department_model();
                $obj->id = $row->id;
                $obj->depNo = $row->depNo;
                $obj->name = $row->name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
