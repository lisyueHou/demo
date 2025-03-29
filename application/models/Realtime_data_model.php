<?php
class Realtime_data_model extends CI_Model
{
    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }

    //查詢即時數據 by robotNo
    public function get_data_by_robotNo($robotNo)
    {
        $sql = "SELECT * FROM `realtime_data` ";

        $sql_array = array();
        if ($robotNo) {
            $sql .= "WHERE robotNo=?";
            array_push($sql_array, $robotNo);
        }

        $query = $this->db->query($sql, $sql_array);
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Realtime_data_model();
                $obj->id = $row->id;
                $obj->robotNo = $row->robotNo;
                $obj->connected = $row->connected;
                $obj->mode = $row->mode;
                $obj->accPitch = $row->accPitch;
                $obj->accRoll = $row->accRoll;
                $obj->meters = $row->meters;
                $obj->location = $row->location;
                $obj->dataTime = $row->dataTime;
                $obj->updateTime = $row->updateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    // 新增即時數據
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
        $sql = "INSERT INTO `realtime_data` (robotNo, connected, mode, accPitch, accRoll, meters,`location`, dataTime,updateTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $query = $this->db->query($sql, array($robotNo, $connected, $mode, $accPitch, $accRoll, $meters,$location, $dataTime,date('Y-m-d H:i:s')));
        return $query;
    }

    //修改即時數據
    public function update_data_by_robotNo($robotNo, $data)
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
        $sql = "UPDATE `realtime_data` SET connected = ?,mode = ?,accPitch = ?,accRoll = ?,meters = ?,`location` = ?,dataTime = ?,updateTime=? WHERE robotNo = ?;";
        $query = $this->db->query($sql, array($connected, $mode, $accPitch, $accRoll, $meters, $location, $dataTime, date('Y-m-d H:i:s'), $robotNo));
        return $query;
    }

    //查詢即時數據
    public function get_data($data)
    {

        $sql = "SELECT * FROM `realtime_data` WHERE robotNo=?;";
        $query = $this->db->query($sql, array($data['robotNo']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Realtime_data_model();
                $obj->id = $row->id;
                $obj->robotNo = $row->robotNo;
                $obj->connected = $row->connected;
                $obj->mode = $row->mode;
                $obj->accPitch = $row->accPitch;
                $obj->accRoll = $row->accRoll;
                $obj->meters = $row->meters;
                $obj->location = $row->location;
                $obj->dataTime = $row->dataTime;
                $obj->updateTime = $row->updateTime;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //查詢即時數據
    public function get_data_byDT_byrobotNo($data)
    {

        $sql = "SELECT `realtime_data`.*,`robot`.`videoUrl` 
                FROM `realtime_data` 
                LEFT JOIN `robot` ON `robot`.`robotNo` = `realtime_data`.`robotNo`
                WHERE `realtime_data`.robotNo = ? AND `realtime_data`.updateTime > ? ;";
        $query = $this->db->query($sql, array($data['robotNo'],$data['dataTime']));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Realtime_data_model();
                $obj->id = $row->id;
                $obj->robotNo = $row->robotNo;
                $obj->connected = $row->connected;
                $obj->mode = $row->mode;
                $obj->accPitch = $row->accPitch;
                $obj->accRoll = $row->accRoll;
                $obj->meters = $row->meters;
                $obj->location = $row->location;
                $obj->dataTime = $row->dataTime;
                $obj->updateTime = $row->updateTime;
                $obj->videoUrl = $row->videoUrl;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
