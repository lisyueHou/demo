<?php
class Remark_model extends CI_Model
{
    public $id = '';
    public $content = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    // 取得所有標記備註資料 API
    public function get_remark()
    {
        $sql = "SELECT * FROM `remark` WHERE isDeleted= 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Remark_model();
                $obj->id = $row->id;
                $obj->content = $row->content;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //取得標記備註資料
    public function get_remark_limit($data)
    {
        $data['page'] = (int)$data['page'];
        $data['pageCount'] = (int)$data['pageCount'];
        $pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

        $all_sql = array(
            "select" => "SELECT * FROM `remark` WHERE ",
            "where_content" => "content LIKE ? AND ",
            "isDelete" => "isDeleted = 0 ORDER BY id DESC ",
            "LIMIT" => "LIMIT ?,?;"
        );

        $sql = $all_sql['select'];
        $sql_array = array();
        if (($data['content'])) {  //關鍵字有值
            $sql = $sql . $all_sql['where_content'];
            array_push($sql_array, '%' . $data['content'] . '%');
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
        $result['remark'] = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Remark_model();
                $obj->id = $row->id;
                $obj->content = $row->content;
                array_push($result['remark'], $obj);
            }
        }
        return $result;
    }

    //刪除標記備註資料
    public function del_remark($data)
    {
        $sql = "UPDATE `remark` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }

    //檢查標記備註資料是否重複
    public function check_remark($content)
    {
        $sql = "SELECT * FROM `remark` WHERE content=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($content));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //檢查設備編號是否重複(修改)
    public function check_remark_by_id($content, $id)
    {
        $sql = "SELECT * FROM `remark` WHERE content = ? AND id <> ? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($content, $id));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //新增標記備註資料
    public function add_remark($data)
    {
        $sql = "INSERT INTO `remark` (content) VALUES (?)";
        $query = $this->db->query($sql, array($data['content']));
        return $query;
    }

    //取得標記備註資料 by id
    public function get_remark_by_id($id)
    {
        $sql = "SELECT * FROM `remark` WHERE id=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Remark_model();
                $obj->id = $row->id;
                $obj->content = $row->content;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //修改標記備註資料
    public function update_remark($data)
    {
        $sql = "UPDATE `remark` SET content = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['content'],$data['id']));
        return $query;
    }
}
