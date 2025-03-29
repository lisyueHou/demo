<?php
class History_model extends CI_Model
{
    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    //判斷資料表是否存在
    public function check_table($robotNo)
    {
        $tableName = 'history_' . strtolower($robotNo);
        $sql = "SHOW TABLES LIKE '$tableName';";
        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    //自動建立資料表
    public function creat_table($robotNo)
    {
        $tableName = 'history_' . strtolower($robotNo);
        $sql = "CREATE TABLE $tableName (
            `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
            `connected` tinyint(1) DEFAULT NULL COMMENT '連線狀態(1代表已連線)',
            `mode` tinyint(1) DEFAULT NULL COMMENT '模式',
            `accPitch` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姿態儀浮仰值',
            `accRoll` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姿態儀翻轉值',
            `meters` float DEFAULT NULL COMMENT '計米輪數值',
            `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'GPS經緯度座標',
            `dataTime` datetime DEFAULT NULL COMMENT '數據紀錄時間',
            `createTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'insert 到資料庫的時間',
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='設備數據歷史資料';";
        $query = $this->db->query($sql);
        return $query;
    }

    //刪除資料表
    public function del_table($robotNo)
    {
        $tableName = 'history_' . strtolower($robotNo);
        $sql = "DROP TABLE $tableName ;";
        $query = $this->db->query($sql);
        return $query;
    }

    // 新增數據
    public function add_data($robotNo, $data)
    {
        //判斷變數是否存在
        $connected = NULL;
        $mode = NULL;
        $accPitch = NULL;
        $accRoll = NULL;
        $meters = NULL;
        $location = NULL;
        $dataTime = NULL;
        if (isset($data->connected)) {
            $connected = $data->connected;
        }
        if (isset($data->mode)) {
            $mode = $data->mode;
        }
        if (isset($data->accPitch)) {
            $accPitch = $data->accPitch;
        }
        if (isset($data->accRoll)) {
            $accRoll = $data->accRoll;
        }
        if (isset($data->meters)) {
            $meters = $data->meters;
        }
        if (isset($data->location)) {
            $location = $data->location;
        }
        if (isset($data->dataTime)) {
            $dataTime = $data->dataTime;
        }

        $tableName = 'history_' . $robotNo;
        $sql = "INSERT INTO $tableName (connected, mode, accPitch, accRoll, meters, `location`, dataTime) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $query = $this->db->query($sql, array($connected, $mode, $accPitch, $accRoll, $meters, $location, $dataTime));
        return $query;
    }

    //查詢歷史數據 by robotNo
    public function get_data_by_robotNo($data)
    {
        $tableName = 'history_' . $data['robotNo'];

        if ($data['startTime'] != '') {
            $startTime = $data['startTime'];
        } else {
            $startTime = '2023-01-01 00:00:00';
        }

        if ($data['endTime'] != '') {
            $endTime = $data['endTime'];
        } else {
            $endTime = date("Y-m-d H:i:s");
        }

        $sql = "SELECT * FROM $tableName WHERE dataTime >= ? AND dataTime <= ?;";
        $query = $this->db->query($sql, array($startTime, $endTime));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new History_model();
                $obj->id = $row->id;
                $obj->connected = $row->connected;
                $obj->mode = $row->mode;
                $obj->accPitch = $row->accPitch;
                $obj->accRoll = $row->accRoll;
                $obj->meters = $row->meters;
                $obj->location = $row->location;
                $obj->dataTime = $row->dataTime;
                $obj->createTime = $row->createTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //取得設備歷史資料
	public function query_all($data)
	{
        $tableName = 'history_' . $data['lower_robotNo'];

		$data['page'] = (int)$data['page'];
		$data['pageCount'] = (int)$data['pageCount'];
		$pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

		$all_sql = array(
			"select" => "SELECT * FROM $tableName 
                         WHERE  ",
            "where_dataTime" => " dataTime >= ? AND dataTime <= ? AND ",
            "isDelete" => " 1=1 ORDER BY `dataTime` ASC ",
			"LIMIT" => "LIMIT ?,?;"
		);

		$sql = $all_sql['select'];
        $sql_array = array();

        if(!empty($data['startTime']) && !empty($data['endTime'])){
            $sql = $sql . $all_sql['where_dataTime'];
			array_push($sql_array, $data['startTime'],$data['endTime']);
        }

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
		$result['data'] = array();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new History_model();
				$obj->id = $row->id;
                $obj->connected = $row->connected;
                $obj->mode = $row->mode;
                $obj->accPitch = $row->accPitch;
                $obj->accRoll = $row->accRoll;
                $obj->meters = $row->meters;
                $obj->location = $row->location;
                $obj->dataTime = $row->dataTime;
                $obj->createTime = $row->createTime;
				array_push($result['data'], $obj);
			}
		}
		return $result;
	}
}
