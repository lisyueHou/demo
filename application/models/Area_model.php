<?php
class Area_model extends CI_Model
{
    public $id = '';
    public $name = '';

    public function __construct()
    {
        $this->load->database();
    }

    // 取得作業區域資料
    public function get_area()
    {
        $sql = "SELECT * FROM `area` WHERE isDeleted = 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Area_model();
                $obj->id = $row->id;
                $obj->name = $row->name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
