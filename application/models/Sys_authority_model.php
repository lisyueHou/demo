<?php
class Sys_authority_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    // 取得帳號系統權限
    public function get_authority($data)
    {
        $sql = "SELECT authority FROM `sys_authority` WHERE account=? AND authority=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($data['account'],$data['system']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Sys_authority_model();
                $obj->authority = $row->authority;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //新增帳號
    public function add_account($account, $authority)
    {
        $sql = "INSERT INTO `sys_authority` (account, authority) VALUES (?, ?)";
        $query = $this->db->query($sql, array($account, $authority));
        return $query;
    }

    //修改帳號
    public function update_account($data, $authority)
    {
        $sql = "UPDATE `sys_authority` SET account = ? WHERE account=? AND authority = ? AND isDeleted=0;";
        $query = $this->db->query($sql, array($data['account'], $data['oldAccount'], $authority));
        return $query;
    }

    //刪除帳號
    public function del_account($account, $authorit)
    {
        $sql = "UPDATE `sys_authority` SET isDeleted = 1,deleteTime = NOW() WHERE account = ? AND authority = ?;";
        $query = $this->db->query($sql, array($account,$authorit));
        return $query;
    }
}
