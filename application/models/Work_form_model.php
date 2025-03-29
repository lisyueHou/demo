<?php
class Work_form_model extends CI_Model
{
    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    //檢查工單報表編號是否重複-API
    public function check_formNo($formNo)
    {
        $sql = "SELECT * FROM `work_form` WHERE formNo=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($formNo));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //更新作業完成時間-API
    public function update_finishTime_by_formNo($data)
    {
        $sql = "UPDATE `work_form` SET finishTime = ? WHERE formNo = ?;";
        $query = $this->db->query($sql, array($data['finishTime'], $data['formNo']));
        return $query;
    }

    //取得作業開始時間 by formNo -API
    public function get_workform_by_formNo($formNo)
    {
        $sql = "SELECT id,formNo,startTime FROM `work_form` WHERE formNo=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($formNo));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Work_form_model();
                $obj->id = $row->id;
                $obj->formNo = $row->formNo;
                $obj->startTime = $row->startTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //取得工單報表資料
    public function get_workform($data)
    {
        $data['page'] = (int)$data['page'];
        $data['pageCount'] = (int)$data['pageCount'];
        $pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

        $all_sql = array(
            "select" => "SELECT `work_form`.*,`client`.company as company FROM `work_form` 
            LEFT JOIN `client` ON `client`.id=`work_form`.clientId WHERE ",
            "where_formNo" => "formNo LIKE ? AND ",
            "where_checkDate" => "checkDate = ? AND ",
            "where_projectNo" => "projectNo LIKE ? AND ",
            "where_projectName" => "projectName LIKE ? AND ",
            "where_contractor" => "contractor LIKE ? AND ",
            "where_company" => "company LIKE ? AND ",
            "where_clientId" => "clientId = ? AND ",
            "isDelete" => "`work_form`.isDeleted = 0 ORDER BY `work_form`.id DESC ",
            "LIMIT" => "LIMIT ?,?;"
        );

        $sql = $all_sql['select'];
        $sql_array = array();
        if (($data['formNo'])) {  // 表單編號有值
            $sql = $sql . $all_sql['where_formNo'];
            array_push($sql_array, '%' . $data['formNo'] . '%');
        }
        if (($data['checkDate'])) {  // 檢查日期有值
            $sql = $sql . $all_sql['where_checkDate'];
            array_push($sql_array, $data['checkDate']);
        }
        if (($data['projectNo'])) {  // 專案編號有值
            $sql = $sql . $all_sql['where_projectNo'];
            array_push($sql_array, '%' . $data['projectNo'] . '%');
        }
        if (($data['projectName'])) {  // 工程專案名稱有值
            $sql = $sql . $all_sql['where_projectName'];
            array_push($sql_array, '%' . $data['projectName'] . '%');
        }
        if (($data['contractor'])) {  // 協力廠商有值
            $sql = $sql . $all_sql['where_contractor'];
            array_push($sql_array, '%' . $data['contractor'] . '%');
        }
        if (($data['company'])) {  // 業主有值
            $sql = $sql . $all_sql['where_company'];
            array_push($sql_array, '%' . $data['company'] . '%');
        }
        if (($data['clientId'])) {  // 顧客id有值
            $sql = $sql . $all_sql['where_clientId'];
            array_push($sql_array, $data['clientId']);
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
        $result['work_form'] = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Work_form_model();
                $obj->id = $row->id;
                $obj->formNo = $row->formNo;
                $obj->startTime = $row->startTime;
                $obj->finishTime = $row->finishTime;
                $obj->robotNo = $row->robotNo;
                $obj->clientId = $row->clientId;
                $obj->company = $row->company;
                $obj->workPlaceId = $row->workPlaceId;
                $obj->projectNo = $row->projectNo;
                $obj->projectName = $row->projectName;
                $obj->subProjectName = $row->subProjectName;
                $obj->contractor = $row->contractor;
                $obj->checkDate = $row->checkDate;
                $obj->pipingLineNo = $row->pipingLineNo;
                $obj->segmentsNo = $row->segmentsNo;
                $obj->remark = $row->remark;
                array_push($result['work_form'], $obj);
            }
        }
        return $result;
    }

    //取得作業開始日最後一張工單編號
    public function get_last_formNo($datetime, $robotNo)
    {
        $startTime = $datetime . ' 00:00:00';
        $endTime = $datetime . ' 23:59:59';
        $sql = "SELECT formNo FROM `work_form` WHERE startTime BETWEEN ? AND ? AND robotNo= ? AND isDeleted= 0 ORDER BY id DESC LIMIT 0,1;";
        $query = $this->db->query($sql, array($startTime, $endTime, $robotNo));
        return $query->result();
    }

    //新增工單報表
    public function add_workform($data)
    {
        $sql = "INSERT INTO `work_form` (formNo, robotNo, startTime,finishTime,clientId,workPlaceId,projectNo,projectName,contractor,checkDate,pipingLineNo,segmentsNo,remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $query = $this->db->query($sql, array($data['formNo'], $data['robotNo'], $data['startTime'], $data['finishTime'], $data['clientId'], $data['workPlaceId'], $data['projectNo'], $data['projectName'], $data['contractor'], $data['checkDate'], $data['pipingLineNo'], $data['segmentsNo'], $data['remark']));
        return $query;
    }

    //取得工單報表資料 by id
    public function get_workform_by_id($id)
    {
        $sql = "SELECT `work_form`.*,`robot`.`name` as robotName,`client`.company as company,`work_place`.`name` as workPlaceName FROM `work_form` 
        LEFT JOIN `robot` ON `work_form`.robotNo=`robot`.robotNo 
        LEFT JOIN `client` ON `work_form`.clientId=`client`.id 
        LEFT JOIN `work_place` ON `work_form`.workPlaceId=`work_place`.id 
        WHERE `work_form`.id=? AND `robot`.isDeleted=0 AND `work_form`.isDeleted= 0;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Work_form_model();
                $obj->id = $row->id;
                $obj->formNo = $row->formNo;
                $obj->startTime = $row->startTime;
                $obj->finishTime = $row->finishTime;
                $obj->robotNo = $row->robotNo;
                $obj->robotName = $row->robotName;
                $obj->clientId = $row->clientId;
                $obj->company = $row->company;
                $obj->workPlaceId = $row->workPlaceId;
                $obj->workPlaceName = $row->workPlaceName;
                $obj->projectNo = $row->projectNo;
                $obj->projectName = $row->projectName;
                $obj->subProjectName = $row->subProjectName;
                $obj->contractor = $row->contractor;
                $obj->checkDate = $row->checkDate;
                $obj->pipingLineNo = $row->pipingLineNo;
                $obj->segmentsNo = $row->segmentsNo;
                $obj->remark = $row->remark;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //檢查工單報表編號是否重複(修改)
    public function check_formNo_by_id($formNo, $id)
    {
        $sql = "SELECT * FROM `work_form` WHERE formNo=? AND id <> ? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($formNo, $id));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //修改工單報表
    public function update_workform($data)
    {
        $sql = "UPDATE `work_form` SET robotNo = ?,startTime = ?,finishTime = ?,clientId = ?,workPlaceId = ?,projectNo = ?,projectName = ?,subProjectName = ?,contractor = ?,checkDate = ?,pipingLineNo = ?,segmentsNo = ?,remark = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['robotNo'], $data['startTime'], $data['finishTime'], $data['clientId'], $data['workPlaceId'], $data['projectNo'], $data['projectName'],$data['subProjectName'], $data['contractor'], $data['checkDate'], $data['pipingLineNo'], $data['segmentsNo'], $data['remark'], $data['id']));
        return $query;
    }

    //刪除工單報表
    public function del_workform($data)
    {
        $sql = "UPDATE `work_form` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }

    //檢查設備是否正被表單資料使用中
    public function check_form_robotNo($robotNo)
    {
        $sql = "SELECT * FROM `work_form` WHERE robotNo=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($robotNo));
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //取得該設備目前的工單 (by DT、robotNo)
    public function get_fromData_byDT_byrobotNo ($data){
        $sql = "SELECT `work_form`.*,`area`.name as areaName,`client`.company,`work_place`.name as wpName,`work_place`.`cadImg`
                FROM `work_form` 
                LEFT JOIN `work_place` ON `work_form`.`workPlaceId` = `work_place`.id
                LEFT JOIN `area` ON `work_place`.`areaId` = `area`.id
                LEFT JOIN `client` ON `work_place`.`clientId` = `client`.id
                WHERE `work_form`.robotNo = ? AND `work_form`.startTime > ? AND ( `work_form`.`finishTime` is null or `work_form`.`finishTime`='0000-00-00 00:00:00') AND `work_form`.isDeleted= 0 ORDER BY id DESC LIMIT 1 ;";
        $query = $this->db->query($sql, array($data['robotNo'],$data['dataTime']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if ($row->cadImg) {
                    $cadImg = base_url() . CADIMG_PATH . $row->cadImg;
                } else {
                    $cadImg = NULL;
                }
                $obj = new Work_form_model();
                $obj->id = $row->id;
                $obj->formNo = $row->formNo;
                $obj->startTime = $row->startTime;
                $obj->areaName = $row->areaName;
                $obj->company = $row->company;
                $obj->wpName = $row->wpName;
                $obj->cadImg = $cadImg;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
