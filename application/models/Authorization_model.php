<?php
class Authorization_model extends CI_Model
{
    public $id = '';
    public $mainFunction = '';
    public $cMainFunction = '';
    public $subFunction = '';
    public $cSubFunction = '';
    public $isOption = '';
    public $mainFunctionNo = '';
    public $subFunctionNo = '';

    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    //取得群組權限 by groupid
    public function get_auth_by_groupid($id)
    {
        $sql = "SELECT * FROM `authorization` LEFT JOIN `functions` ON `authorization`.functionsId=`functions`.id WHERE groupId=?;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Authorization_model();
                $obj->id = $row->id;
                $obj->mainFunction = $row->mainFunction;
                $obj->cMainFunction = $row->cMainFunction;
                $obj->subFunction = $row->subFunction;
                $obj->cSubFunction = $row->cSubFunction;
                $obj->isOption = $row->isOption;
                $obj->mainFunctionNo = $row->mainFunctionNo;
                $obj->mainFunctionNo = $row->subFunctionNo;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 檢查使用者是否有權限
    public function get_function_by_groupId($group_id, $controller_name, $action_name)
    {
        $sql = "SELECT func.id as id FROM authorization AS auth,functions AS func
            WHERE auth.groupId=? AND auth.functionsId= func.Id AND func.mainFunction = ? AND func.subFunction LIKE ?";
        $query = $this->db->query($sql, array($group_id, $controller_name, '%' . $action_name . '%'));
        $result = $query->result_array();
        return $result;
    }

    //取得群組權限
    public function get_auth()
    {
        $sql = "SELECT * FROM `functions` WHERE isOption=1;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Authorization_model();
                $obj->id = $row->id;
                $obj->mainFunction = $row->mainFunction;
                $obj->cMainFunction = $row->cMainFunction;
                $obj->subFunction = $row->subFunction;
                $obj->cSubFunction = $row->cSubFunction;
                $obj->isOption = $row->isOption;
                $obj->mainFunctionNo = $row->mainFunctionNo;
                $obj->mainFunctionNo = $row->subFunctionNo;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 刪除原先權限
    public function delete_authorization($data)
    {
        $sql = "DELETE FROM authorization WHERE groupId = ? ";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }

    // 取得大項及細項功能編號
    public function get_functionNo($data)
    {
        $sql = "SELECT mainFunctionNo,subFunctionNo FROM functions WHERE id = ? ";
        $query = $this->db->query($sql, array($data));
        $result = $query->result_array();
        return $result;
    }

    // 取得相同編號的所有function
    public function get_same_functionNo($data)
    {
        $sql = "SELECT id FROM functions WHERE mainFunctionNo = ? AND subFunctionNo = ?";
        $query = $this->db->query($sql, array($data['mainFunctionNo'], $data['subFunctionNo']));
        $result = $query->result_array();
        return $result;
    }

    // 新增權限
    public function add_authorization($data)
    {
        $sql = "INSERT INTO authorization (groupId, functionsId )VALUES (?, ?)";
        $query = $this->db->query($sql, array($data['id'], $data['functionId']));
        return $query;
    }
}
