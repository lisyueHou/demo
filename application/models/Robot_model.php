<?php
class Robot_model extends CI_Model
{
    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    //取得設備資料
    public function get_robot($data)
    {
        $data['page'] = (int)$data['page'];
        $data['pageCount'] = (int)$data['pageCount'];
        $pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

        $all_sql = array(
            "select" => "SELECT * FROM `robot` WHERE ",
            "where_robotNo" => "robotNo LIKE ? AND ",
            "where_name" => "`name` LIKE ? AND ",
            "isDelete" => "isDeleted = 0 ORDER BY id DESC ",
            "LIMIT" => "LIMIT ?,?;"
        );

        $sql = $all_sql['select'];
        $sql_array = array();
        if (($data['robotNo'])) {  // 設備編號有值
            $sql = $sql . $all_sql['where_robotNo'];
            array_push($sql_array, '%' . $data['robotNo'] . '%');
        }
        if (($data['name'])) {  // 設備名稱有值
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
        $result['robot'] = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Robot_model();
                $obj->id = $row->id;
                $obj->robotNo = $row->robotNo;
                $obj->name = $row->name;
                $obj->state = $row->state;
                $obj->videoUrl = $row->videoUrl;
                $obj->remark = $row->remark;
                array_push($result['robot'], $obj);
            }
        }
        return $result;
    }

    //新增設備資料
    public function add_robot($data)
    {
        $sql = "INSERT INTO `robot` (robotNo, `name`, `state`, videoUrl, remark) VALUES (?, ?, ?, ?, ?)";
        $query = $this->db->query($sql, array($data['robotNo'], $data['name'], $data['state'], $data['videoUrl'], $data['remark']));
        return $query;
    }

    //檢查設備編號是否重複
    public function check_robotNo($robotNo)
    {
        $sql = "SELECT * FROM `robot` WHERE robotNo=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($robotNo));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //檢查設備編號是否重複(修改)
    public function check_robotNo_by_id($robotNo, $id)
    {
        $sql = "SELECT * FROM `robot` WHERE robotNo = ? AND id <> ? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($robotNo, $id));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //取得設備資料 by id
    public function get_robot_by_id($id)
    {
        $sql = "SELECT * FROM `robot` WHERE id=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Robot_model();
                $obj->id = $row->id;
                $obj->robotNo = $row->robotNo;
                $obj->name = $row->name;
                $obj->state = $row->state;
                $obj->videoUrl = $row->videoUrl;
                $obj->remark = $row->remark;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //修改設備資料
    public function update_robot($data)
    {
        $sql = "UPDATE `robot` SET robotNo = ?,`name` = ?,`state` = ?,videoUrl = ?,remark = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['robotNo'], $data['name'], $data['state'], $data['videoUrl'], $data['remark'], $data['id']));
        return $query;
    }

    //刪除設備資料
    public function del_robot($data)
    {
        $sql = "UPDATE `robot` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }

    //取得設備清單-工單報表使用
    public function get_robot_list()
    {
        $sql = "SELECT id,robotNo,`name` FROM `robot` WHERE `state` = 0 AND isDeleted= 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Robot_model();
                $obj->id = $row->id;
                $obj->robotNo = $row->robotNo;
                $obj->name = $row->name;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //取得設備清單-API使用
    public function get_robot_list_api()
    {
        $sql = "SELECT id,robotNo,`name` FROM `robot` WHERE `state` = 0 AND isDeleted= 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Robot_model();
                $obj->robotNo = $row->robotNo;
                $obj->name = $row->name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
