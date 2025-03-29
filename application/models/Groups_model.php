<?php
class Groups_model extends CI_Model
{
    public $id = '';
    public $name = '';
    public $class = '';

    public function __construct()
    {
        $this->load->database();
    }

    // 查詢群組資料 by id
    public function get_groups_by_id($id)
    {
        $sql = "SELECT * FROM `groups` WHERE id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Groups_model();
                $obj->id = $row->id;
                $obj->name = $row->name;
                $obj->class = $row->class;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //取得權限群組
    public function get_groups($data)
    {
        $data['page'] = (int)$data['page'];
        $data['pageCount'] = (int)$data['pageCount'];
        $pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

        $all_sql = array(
            "select" => "SELECT * FROM `groups` WHERE ",
            "where_class" => "`class` = ? AND ",
            "where_name" => "`name` LIKE ? AND ",
            "isDelete" => "isDeleted= 0 ORDER BY id DESC ",
            "LIMIT" => "LIMIT ?,?;"
        );

        $sql = $all_sql['select'];
        $sql_array = array();
        if (($data['class'])) {  // 群組類別有值
            $sql = $sql . $all_sql['where_class'];
            array_push($sql_array, $data['class']);
        }
        if (($data['name'])) {  // 群組名稱有值
            $sql = $sql . $all_sql['where_name'];
            array_push($sql_array, '%' . $data['name'] . '%');
        }

        // 加上JOIN及isDelete篩選條件
        $sql = $sql . $all_sql['isDelete'];

        // 取得總數
        $query = $this->db->query($sql, $sql_array);
        $result['totalCount'] = $query->num_rows();
        $total_page = $result['totalCount'] / $data['pageCount'];
        $result['page'] = $data['page'];
        $result['pageCount'] = $data['pageCount'];
        $result['totalPage'] = ceil($total_page);

        // 取得限制筆數資料
        $sql = $sql . $all_sql['LIMIT'];
        array_push($sql_array, $pageStart);
        array_push($sql_array, $data['pageCount']);
        $query = $this->db->query($sql, $sql_array);
        $result['groups'] = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Groups_model();
                $obj->id = $row->id;
                $obj->class = $row->class;
                $obj->name = $row->name;
                array_push($result['groups'], $obj);
            }
        }
        return $result;
    }

    // 新增群組並取得ID
    public function add_group($data)
    {
        $sql = "INSERT INTO `groups` (`name`, class)VALUES (?, ?)";
        $this->db->query($sql, array($data['name'], $data['class']));
        $result = $this->db->insert_id();
        return $result;
    }

    //修改群組資料
    public function update_group($data)
    {
        $sql = "UPDATE `groups` SET `name` = ?,class = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['name'], $data['class'], $data['id']));
        return $query;
    }

    //刪除群組資料
    public function del_group($data)
    {
        $sql = "UPDATE `groups` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }

    // 取得群組資料 =>帳號維護選單使用
    public function get_groups_list()
    {
        $sql = "SELECT * FROM `groups` WHERE isDeleted = 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Groups_model();
                $obj->id = $row->id;
                $obj->name = $row->name;
                $obj->class = $row->class;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
