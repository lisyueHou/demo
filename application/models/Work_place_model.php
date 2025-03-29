<?php
class Work_place_model extends CI_Model
{
    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url');
    }

    //查詢CAD圖資料-API
    public function get_cmdImg($clientNo)
    {

        $sql = "SELECT `work_place`.*,`client`.company as company,`client`.clientNo as clientNo FROM `work_place` 
        LEFT JOIN `client` ON `work_place`.clientId=`client`.id 
        WHERE `work_place`.isDeleted= 0 ";

        $sql_array = array();
        if ($clientNo != '') {
            $sql .= "AND client.clientNo = ?";
            array_push($sql_array, $clientNo);
        }

        $query = $this->db->query($sql, $sql_array);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $imgPath = base_url() . CADIMG_PATH . $row->cadImg;
                $obj = new Work_place_model();
                $obj->id = $row->id;
                $obj->clientNo = $row->clientNo;
                $obj->company = $row->company;
                $obj->workPlace = $row->name;
                $obj->cadImg = $row->cadImg;
                $obj->imgPath = $imgPath;
                $obj->coordinate = $row->coordinate;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //取得作業地點
    public function get_workplace($data)
    {
        $data['page'] = (int)$data['page'];
        $data['pageCount'] = (int)$data['pageCount'];
        $pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

        $all_sql = array(
            "select" => "SELECT `work_place`.*,`area`.`name` as areaName,`client`.company as company FROM `work_place`
            LEFT JOIN `area` ON `work_place`.areaId=`area`.id 
            LEFT JOIN `client` ON `work_place`.clientId=`client`.id 
            WHERE ",
            "where_areaId" => "`work_place`.areaId = ? AND ",
            "where_name" => "`work_place`.`name` LIKE ? AND ",
            "where_company" => "`client`.company LIKE ? AND ",
            "isDelete" => "`work_place`.isDeleted= 0 ORDER BY `work_place`.id DESC ",
            "LIMIT" => "LIMIT ?,?;"
        );

        $sql = $all_sql['select'];
        $sql_array = array();
        if (($data['areaId'])) {  // 作業區域id有值
            $sql = $sql . $all_sql['where_areaId'];
            array_push($sql_array, $data['areaId']);
        }
        if (($data['workPlace'])) {  // 作業地點有值
            $sql = $sql . $all_sql['where_name'];
            array_push($sql_array, '%' . $data['workPlace'] . '%');
        }
        if ($data['company']) {  // 公司名稱有值
            $sql = $sql . $all_sql['where_company'];
            array_push($sql_array, '%' . $data['company'] . '%');
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
        $result['work_place'] = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if ($row->cadImg) {
                    $imgPath = base_url() . CADIMG_PATH . $row->cadImg;
                } else {
                    $imgPath = NULL;
                }
                $obj = new Work_place_model();
                $obj->id = $row->id;
                $obj->areaId = $row->areaId;
                $obj->areaName = $row->areaName;
                $obj->clientId = $row->clientId;
                $obj->company = $row->company;
                $obj->workPlace = $row->name;
                $obj->cadImg = $row->cadImg;
                $obj->imgPath = $imgPath;
                $obj->latitude = $row->latitude;
                $obj->longitude = $row->longitude;
                $obj->coordinate = $row->coordinate;
                $obj->remark = $row->remark;
                array_push($result['work_place'], $obj);
            }
        }
        return $result;
    }

    //更新CAD圖路徑
    public function update_cadImg($data)
    {
        $coordinate = json_encode($data['coordinate'], True);
        $sql = "UPDATE `work_place` SET coordinate = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($coordinate, $data['id']));
        return $query;
    }

    //新增作業區域資料
    public function add_workplace($data)
    {
        if ($data['latitude'] == "") {
            $latitude = NULL;
        } else {
            $latitude = $data['latitude'];
        }
        if ($data['longitude'] == "") {
            $longitude = NULL;
        } else {
            $longitude = $data['longitude'];
        }
        $sql = "INSERT INTO `work_place` (areaId, clientId, `name`, cadImg, latitude, longitude, remark) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $query = $this->db->query($sql, array($data['areaId'], $data['clientId'], $data['name'], $data['cadImgName'], $latitude, $longitude, $data['remark']));
        return $query;
    }

    //刪除管線圖資料
    public function del_cadImg($data)
    {
        $sql = "UPDATE `work_place` SET cadImg = NULL,coordinate = NULL WHERE id = ?;";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }

    //取得作業地點 by id
    public function get_workplace_by_id($id)
    {
        $sql = "SELECT `work_place`.*,`area`.`name` as areaName,`client`.company as company FROM `work_place` 
        LEFT JOIN `area` ON `work_place`.areaId=`area`.id 
        LEFT JOIN `client` ON `work_place`.clientId=`client`.id 
        WHERE `work_place`.id=? AND `work_place`.isDeleted= 0;";
        $query = $this->db->query($sql, array($id));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if ($row->cadImg) {
                    $imgPath = base_url() . CADIMG_PATH . $row->cadImg;
                } else {
                    $imgPath = NULL;
                }
                $obj = new Work_place_model();
                $obj->id = $row->id;
                $obj->areaId = $row->areaId;
                $obj->areaName = $row->areaName;
                $obj->clientId = $row->clientId;
                $obj->company = $row->company;
                $obj->workPlace = $row->name;
                $obj->cadImg = $row->cadImg;
                $obj->imgPath = $imgPath;
                $obj->latitude = $row->latitude;
                $obj->longitude = $row->longitude;
                $obj->coordinate = $row->coordinate;
                $obj->remark = $row->remark;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //修改作業區域資料
    public function update_workplace($data)
    {
        if ($data['latitude'] == "") {
            $latitude = NULL;
        } else {
            $latitude = $data['latitude'];
        }
        if ($data['longitude'] == "") {
            $longitude = NULL;
        } else {
            $longitude = $data['longitude'];
        }
        $sql = "UPDATE `work_place` SET areaId = ?,clientId = ?,`name` = ?,cadImg = ?,latitude = ?,longitude = ?,remark = ? WHERE id = ?;";
        $query = $this->db->query($sql, array($data['areaId'], $data['clientId'], $data['name'], $data['cadImgName'], $latitude, $longitude, $data['remark'], $data['id']));
        return $query;
    }

    //刪除作業區域資料
    public function del_workplace($data)
    {
        $sql = "UPDATE `work_place` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
        $query = $this->db->query($sql, array($data['id']));
        return $query;
    }


    //取得顧客的作業地點 by clientId -工單報表選單使用
    public function get_workplace_by_clientId($clientId)
    {
        $sql = "SELECT id,`name` FROM `work_place` WHERE clientId=? AND `work_place`.isDeleted= 0;";
        $query = $this->db->query($sql, array($clientId));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Work_place_model();
                $obj->id = $row->id;
                $obj->name = $row->name;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
