<?php
class Form_remark_model extends CI_Model
{
    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    //新增表單備註資料-API
    public function add_form_remark($data)
    {
        $formNo = $data->formNo;
        //判斷變數是否存在
        $meters = NULL;
        $content = NULL;
        $x_axis = NULL;
        $y_axis = NULL;
        $remarkTime = NULL;
        $results = NULL;
        $remark = NULL;
        $picName = NULL;
        if (isset($data->meters)) {
            $meters = $data->meters;
        }
        if (isset($data->content)) {
            $content = $data->content;
        }
        if (isset($data->x_axis)) {
            $x_axis = $data->x_axis;
        }
        if (isset($data->y_axis)) {
            $y_axis = $data->y_axis;
        }
        if (isset($data->remarkTime)) {
            $remarkTime = $data->remarkTime;
        }
        if (isset($data->results)) {
            $results = $data->results;
        }
        if (isset($data->remark)) {
            $remark = $data->remark;
        }
        if (isset($data->picName)) {
            $picName = $data->picName;
        }
        $sql = "INSERT INTO `form_remark` (formNo, meters, content, x_axis, y_axis, remarkTime, results, remark, picName) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql, array($formNo, $meters, $content, $x_axis, $y_axis, $remarkTime, $results, $remark, $picName));
        return $query;
    }


    //取得工單標記備註資料 by formNo
    public function get_remark_by_formNo($formNo)
    {
        $sql = "SELECT * FROM `form_remark` WHERE formNo=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($formNo));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Form_remark_model();
                $obj->id = $row->id;
                $obj->formNo = $row->formNo;
                $obj->meters = $row->meters;
                $obj->content = $row->content;
                $obj->x_axis = $row->x_axis;
                $obj->y_axis = $row->y_axis;
                $obj->remarkTime = $row->remarkTime;
                $obj->results = $row->results;
                $obj->remark = $row->remark;
                $obj->picName = $row->picName;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //刪除工單標記備註資料 by formNo
    public function del_remark_by_formNo($formNo)
    {
        $sql = "UPDATE `form_remark` SET isDeleted = 1,deleteTime = NOW() WHERE formNo = ?;";
        $query = $this->db->query($sql, array($formNo));
        return $query;
    }

    //刪除工單標記備註資料 by id
    public function del_remark_by_id($id)
    {
        $sql = "UPDATE `form_remark` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
        $query = $this->db->query($sql, array($id));
        return $query;
    }

    //修改工單標記備註資料 by id
    public function update_remark_by_id($data)
    {
        $sql = "UPDATE `form_remark` SET meters = ?,content = ?,results = ?,remark = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['meters'],$data['content'],$data['results'],$data['remark'],$data['id']));
        return $query;
    }
}
