<?php
class Users_model extends CI_Model
{
    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    // 登入檢查-員工
    public function check_login_staff($data)
    {
        $sql = "SELECT `users`.*,`staff`.staffNo as userNo,`staff`.`name` as userName FROM `users` 
        LEFT JOIN `groups` ON `users`.groupId=`groups`.id 
        LEFT JOIN `staff` ON `users`.personId=`staff`.id 
        WHERE account=? AND `password`=? AND `groups`.class= 1 AND `users`.isDeleted= 0 AND `users`.isEnable= 1;";
        $query = $this->db->query($sql, array($data['account'], $data['password']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->id;
                $obj->account = $row->account;
                $obj->password = $row->password;
                $obj->userNo = $row->userNo;
                $obj->userName = $row->userName;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 登入檢查-後台
    public function check_login($data)
    {
        $sql = "SELECT * FROM `users` WHERE account=? AND `password`=? AND `users`.isDeleted= 0 AND `users`.isEnable= 1;";
        $query = $this->db->query($sql, array($data['account'], $data['password']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->id;
                $obj->account = $row->account;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新Token
    public function update_Token_by_id($user_id, $token)
    {
        $sql = "UPDATE `users` SET token = ?,  tokenCreateTime = ?, tokenUpdateTime = ? WHERE id = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array($token, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user_id));
        return $query;
    }

    // 檢查 Token 是否存在
    public function get_user_by_token($token)
    {
        $sql = "SELECT * FROM `users` WHERE token = ? AND isDeleted= 0 AND isEnable= 1;";
        $query = $this->db->query($sql, array($token));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->id;
                $obj->account = $row->account;
                $obj->groupId = $row->groupId;
                $obj->personId = $row->personId;
                $obj->token = $row->token;
                $obj->tokenCreateTime = $row->tokenCreateTime;
                $obj->tokenUpdateTime = $row->tokenUpdateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 更新使用者token的T_UpdateDT
    public function update_TUpdateDT_by_token($token)
    {
        $sql = "UPDATE `users` SET tokenUpdateTime = ? WHERE token = ? AND isDeleted = 0;";
        $query = $this->db->query($sql, array(date('Y-m-d H:i:s'), $token));
        return $query;
    }

    // 取得所有員工帳號密碼
    public function get_users_staff()
    {
        $sql = "SELECT `users`.*,`staff`.staffNo as userNo,`staff`.`name` as userName FROM `users` 
            LEFT JOIN `groups` ON `users`.groupId=`groups`.id 
            LEFT JOIN `staff` ON `users`.personId=`staff`.id 
            WHERE `groups`.class= 1 AND `users`.isDeleted= 0 AND `users`.isEnable= 1;";
        $query = $this->db->query($sql);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->id;
                $obj->account = $row->account;
                $obj->password = $row->password;
                $obj->userNo = $row->userNo;
                $obj->userName = $row->userName;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得員工帳號 by groupId ->權限群組功能使用
    public function get_staffAcc_by_groupId($groupId)
    {
        $sql = "SELECT `users`.*,`staff`.staffNo as userNo,`staff`.`name` as userName FROM `users` 
            LEFT JOIN `groups` ON `users`.groupId=`groups`.id 
            LEFT JOIN `staff` ON `users`.personId=`staff`.id 
            WHERE `groups`.id = ? AND `groups`.isDeleted = 0;";
        $query = $this->db->query($sql, array($groupId));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->id;
                $obj->account = $row->account;
                $obj->isEnable = $row->isEnable;
                $obj->userNo = $row->userNo;
                $obj->userName = $row->userName;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得顧客帳號 by groupId ->權限群組功能使用
    public function get_clientAcc_by_groupId($groupId)
    {
        $sql = "SELECT `users`.*,`client`.clientNo as userNo,`client`.company as userName FROM `users` 
            LEFT JOIN `groups` ON `users`.groupId=`groups`.id 
            LEFT JOIN `client` ON `users`.personId=`client`.id 
            WHERE `groups`.id = ? AND `groups`.isDeleted = 0;";
        $query = $this->db->query($sql, array($groupId));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->id;
                $obj->account = $row->account;
                $obj->isEnable = $row->isEnable;
                $obj->userNo = $row->userNo;
                $obj->userName = $row->userName;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 取得帳號資料
    public function get_users($data)
    {
        $data['page'] = (int)$data['page'];
        $data['pageCount'] = (int)$data['pageCount'];
        $pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

        $all_sql = array(
            "sqlStaff" => "SELECT `users`.*, `groups`.class as class, `groups`.`name` as groupName, `staff`.staffNo as userNo, `staff`.`name` as userName FROM `users` 
            LEFT JOIN `groups` ON `users`.groupId=`groups`.id 
            LEFT JOIN `staff` ON `users`.personId=`staff`.id 
            WHERE `groups`.class=1 AND `users`.isDeleted= 0 ",
            "where_account" => "AND `users`.account LIKE ? ",
            "where_staffName" => "AND `staff`.`name` LIKE ? ",
            "sqlUNION" => " UNION ",
            "sqlClinet" => "SELECT `users`.*, `groups`.class as class, `groups`.`name` as groupName, `client`.clientNo as userNo,`client`.company as userName FROM `users` 
            LEFT JOIN `groups` ON `users`.groupId=`groups`.id 
            LEFT JOIN `client` ON `users`.personId=`client`.id 
            WHERE `groups`.class=2 AND `users`.isDeleted= 0 ",
            "where_company" => "AND `client`.`company` LIKE ? ",
            "isDelete" => "ORDER BY `users`.id DESC ",
            "LIMIT" => "LIMIT ?,?;"
        );

        $sql = $all_sql['sqlStaff'];
        $sql_array = array();
        if (($data['account'])) {  // 帳號有值
            $sql = $sql . $all_sql['where_account'];
            array_push($sql_array, '%' . $data['account'] . '%');
        }
        if (($data['userName'])) {  // 使用人員有值
            $sql = $sql . $all_sql['where_staffName'];
            array_push($sql_array, '%' . $data['userName'] . '%');
        }

        $sql = $sql . $all_sql['sqlUNION'] . $all_sql['sqlClinet'];

        if (($data['account'])) {  // 帳號有值
            $sql = $sql . $all_sql['where_account'];
            array_push($sql_array, '%' . $data['account'] . '%');
        }
        if (($data['userName'])) {  // 使用人員有值
            $sql = $sql . $all_sql['where_company'];
            array_push($sql_array, '%' . $data['userName'] . '%');
        }

        // 取得總數
        $sqlCount = "SELECT COUNT(*) AS 'total' FROM ({$sql}) AS `usersTable`";
        $query = $this->db->query($sqlCount, $sql_array);
        $total = $query->result_array();
        $result['totalCount'] = $total[0]['total'];
        $total_page = $result['totalCount'] / $data['pageCount'];
        $result['page'] = $data['page'];
        $result['pageCount'] = $data['pageCount'];
        $result['totalPage'] = ceil($total_page);

        // 取得限制筆數資料
        $sqlData = "SELECT * FROM ({$sql}) AS `usersTable` ORDER BY `usersTable`.id DESC LIMIT {$data['pageCount']} OFFSET {$pageStart}";
        $query = $this->db->query($sqlData, $sql_array);
        $result['users'] = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->id;
                $obj->account = $row->account;
                $obj->groupId = $row->groupId;
                $obj->groupName = $row->groupName;
                $obj->class = $row->class;
                $obj->userNo = $row->userNo;
                $obj->userName = $row->userName;
                $obj->personId = $row->personId;
                $obj->isEnable = $row->isEnable;
                $obj->remark = $row->remark;
                array_push($result['users'], $obj);
            }
        }
        return $result;
    }

    //檢查帳號是否重複
    public function check_account($account)
    {
        $sql = "SELECT * FROM `users` WHERE account=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($account));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //檢查帳號是否重複(修改)
    public function check_account_by_id($account, $id)
    {
        $sql = "SELECT * FROM `users` WHERE account=? AND id <> ? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($account, $id));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //新增帳號資料
    public function add_user($data)
    {
        $sql = "INSERT INTO `users` (account, `password`, isEnable, groupId, personId, remark) VALUES (?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql, array($data['account'], $data['password'], $data['enable'], $data['groupId'], $data['personId'], $data['remark']));
        return $query;
    }

    // 查詢帳號 by id
    public function get_user_by_id($id)
    {
        $sql = "SELECT `users`.*,`groups`.`name` as groupName,`groups`.class as class FROM `users` 
        LEFT JOIN `groups` ON `users`.groupId=`groups`.id 
        WHERE `users`.id = ? AND `users`.isDeleted = 0;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Users_model();
                $obj->id = $row->id;
                $obj->account = $row->account;
                $obj->groupId = $row->groupId;
                $obj->groupName = $row->groupName;
                $obj->class = $row->class;
                $obj->personId = $row->personId;
                $obj->isEnable = $row->isEnable;
                $obj->remark = $row->remark;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //修改帳號資料
    public function update_user($data)
    {
        $sql = "UPDATE `users` SET account = ?,isEnable = ?,groupId = ?,personId = ?,remark = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['account'], $data['enable'], $data['groupId'], $data['personId'], $data['remark'], $data['id']));
        return $query;
    }

    //刪除帳號
    public function del_user($data)
    {
        $sql = "UPDATE `users` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }

    //變更密碼
    public function update_pass($data)
    {
        $sql = "UPDATE `users` SET `password` = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['password'], $data['id']));
        return $query;
    }

    //檢查群組是否還有帳號資料 by groupId->刪除權限群組使用
    public function check_users_by_groupId($id)
    {
        $sql = "SELECT * FROM `users` WHERE groupId=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($id));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // 檢查員工/客戶資料是否已被帳號綁定 ->刪除員工/客戶資料時使用
    public function check_account_isUsed($class, $staffId)
    {
        $sql = "SELECT `users`.* FROM `users` LEFT JOIN `groups` ON `users`.groupId=`groups`.id 
            WHERE `groups`.class = ? AND `users`.personId=? AND `users`.isDeleted = 0;";
        $query = $this->db->query($sql, array($class, $staffId));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
