<?php
class History_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('robot_model');
        $this->load->model('history_model');
    }

    public function getRobotHistory($data)
    {
        if (!empty($data['startTime']) && !empty($data['endTime'])) {
            $data['startTime'] = date('Y-m-d H:i:s', strtotime($data['startTime']));
            $data['endTime'] = date('Y-m-d H:i:s', strtotime($data['endTime']));
        }
        $data['lower_robotNo'] = strtolower($data['robotNo']); //轉換成小寫

        $r = $this->history_model->query_all($data);
        $result = array(
            "status" => true,
            "data" => $r,
        );
        return $result;
    }

    //取得設備資料
    public function getRobot()
    {
        $r = $this->robot_model->get_robot_list_api();
        $result = array(
            "status" => true,
            "data" => $r,
        );
        return $result;
    }
}
