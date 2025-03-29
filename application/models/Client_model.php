<?php
class Client_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    // 取得客戶資料 by id
    public function get_client_by_id($id)
    {
        $sql = "SELECT * FROM `client` WHERE id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Client_model();
                $obj->id = $row->id;
                $obj->clientNo = $row->clientNo;
                $obj->companyId = $row->companyId;
                $obj->company = $row->company;
                $obj->address = $row->address;
                $obj->name = $row->name;
                $obj->phone = $row->phone;
                $obj->email = $row->email;
                $obj->conName = $row->conName;
                $obj->conPhone = $row->conPhone;
                $obj->conEmail = $row->conEmail;
                $obj->remark = $row->remark;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得客戶資料
    public function get_client()
    {
        $sql = "SELECT * FROM `client` WHERE isDeleted = 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Client_model();
                $obj->id = $row->id;
                $obj->clientNo = $row->clientNo;
                $obj->companyId = $row->companyId;
                $obj->company = $row->company;
                $obj->address = $row->address;
                $obj->name = $row->name;
                $obj->phone = $row->phone;
                $obj->email = $row->email;
                $obj->conName = $row->conName;
                $obj->conPhone = $row->conPhone;
                $obj->conEmail = $row->conEmail;
                $obj->remark = $row->remark;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得客戶資料 限制筆數
    public function get_client_limit($data)
    {
        $data['page'] = (int)$data['page'];
        $data['pageCount'] = (int)$data['pageCount'];
        $pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

        $all_sql = array(
            "select" => "SELECT * FROM `client` WHERE ",
            "where_clientNo" => "clientNo LIKE ? AND ",
            "where_company" => "company LIKE ? AND ",
            "where_companyId" => "companyId LIKE ? AND ",
            "isDelete" => "isDeleted = 0 ORDER BY id DESC ",
            "LIMIT" => "LIMIT ?,?;"
        );

        $sql = $all_sql['select'];
        $sql_array = array();
        if (($data['clientNo'])) {  // 顧客編號有值
            $sql = $sql . $all_sql['where_clientNo'];
            array_push($sql_array, '%' . $data['clientNo'] . '%');
        }
        if (($data['company'])) {  // 公司名稱有值
            $sql = $sql . $all_sql['where_company'];
            array_push($sql_array, '%' . $data['company'] . '%');
        }
        if (($data['companyId'])) {  // 統一編號有值
            $sql = $sql . $all_sql['where_companyId'];
            array_push($sql_array, '%' . $data['companyId'] . '%');
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
        $result['client'] = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Client_model();
                $obj->id = $row->id;
                $obj->clientNo = $row->clientNo;
                $obj->companyId = $row->companyId;
                $obj->company = $row->company;
                $obj->address = $row->address;
                $obj->name = $row->name;
                $obj->phone = $row->phone;
                $obj->email = $row->email;
                $obj->conName = $row->conName;
                $obj->conPhone = $row->conPhone;
                $obj->conEmail = $row->conEmail;
                $obj->remark = $row->remark;
                array_push($result['client'], $obj);
            }
        }
        return $result;
    }

    //檢查顧客編號是否重複
    public function check_clientNo($clientNo)
    {
        $sql = "SELECT * FROM `client` WHERE clientNo=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($clientNo));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //檢查顧客編號是否重複(修改)
    public function check_clientNo_by_id($clientNo, $id)
    {
        $sql = "SELECT * FROM `client` WHERE clientNo=? AND id <> ? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($clientNo, $id));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //新增顧客資料
    public function add_client($data)
    {
        $sql = "INSERT INTO `client` (clientNo, company, companyId, `address`, `name`, phone, email, conName, conPhone, conEmail, remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql, array($data['clientNo'], $data['company'], $data['companyId'], $data['address'], $data['name'], $data['phone'], $data['email'], $data['conName'], $data['conPhone'], $data['conEmail'], $data['remark']));
        return $query;
    }

    //修改顧客資料
    public function update_client($data)
    {
        $sql = "UPDATE `client` SET clientNo = ?,company = ?,companyId = ?,`address` = ?,`name` = ?,phone = ?,email = ?, conName = ?,conPhone = ?,conEmail = ?,remark = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['clientNo'], $data['company'], $data['companyId'], $data['address'], $data['name'], $data['phone'], $data['email'], $data['conName'], $data['conPhone'], $data['conEmail'], $data['remark'], $data['id']));
        return $query;
    }

    //刪除顧客資料
    public function del_client($data)
    {
        $sql = "UPDATE `client` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }

    // 取得客戶資料->帳號維護選單使用
    public function get_client_list()
    {
        $sql = "SELECT * FROM `client` WHERE isDeleted = 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Client_model();
                $obj->id = $row->id;
                $obj->userNo = $row->clientNo;
                $obj->userName = $row->company;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得客戶資料->API顧客選單使用
    public function get_client_list_api()
    {
        $sql = "SELECT * FROM `client` WHERE isDeleted = 0;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Client_model();
                $obj->clientNo = $row->clientNo;
                $obj->company = $row->company;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得客戶資料 by id->帳號維護選單使用
    public function get_client_list_by_id($id)
    {
        $sql = "SELECT * FROM `client` WHERE id=? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Client_model();
                $obj->id = $row->id;
                $obj->userNo = $row->clientNo;
                $obj->userName = $row->company;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
