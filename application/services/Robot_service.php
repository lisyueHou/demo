<?php
class Robot_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('robot_model');
		$this->load->model('history_model');
		$this->load->model('work_form_model');
	}

	//取得設備資料
	public function getRobot($data)
	{
		$r = $this->robot_model->get_robot($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//新增設備資料
	public function addRobot($data)
	{
		//檢查設備編號是否重複
		$check_r = $this->robot_model->check_robotNo($data['robotNo']);
		if (!$check_r) {
			$r = $this->robot_model->add_robot($data);

			//新增設備歷史資料表
			$history_r = $this->history_model->check_table($data['robotNo']);
			if (!$history_r) {
				$this->history_model->creat_table($data['robotNo']);
			}

			if ($r) {
				$result = array(
					"status" => true,
					"message" => "新增成功"
				);
			} else {
				$result = array(
					"status" => false,
					"message" => "新增失敗"
				);
			}
		} else {
			$result = array(
				"status" => false,
				"message" => "設備編號已存在，請重新輸入"
			);
		}
		return $result;
	}

	//取得設備資料 by id
	public function getRobotById($id)
	{
		$r = $this->robot_model->get_robot_by_id($id);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//編輯設備資料
	public function editRobot($data)
	{
		//檢查設備編號是否重複
		$check_r = $this->robot_model->check_robotNo_by_id($data['robotNo'], $data['id']);
		if (!$check_r) {

			//判斷歷史數據資料表是否存在，如不存在則新增資料表
			$history_r = $this->history_model->check_table($data['robotNo']);
			if (!$history_r) {
				$this->history_model->creat_table($data['robotNo']);
			}

			$r = $this->robot_model->update_robot($data);
			if ($r) {
				$result = array(
					"status" => true,
					"message" => "儲存成功"
				);
			} else {
				$result = array(
					"status" => false,
					"message" => "儲存失敗"
				);
			}
		} else {
			$result = array(
				"status" => false,
				"message" => "設備編號已存在，請重新輸入"
			);
		}
		return $result;
	}

	//刪除設備資料
	public function delRobot($data)
	{
		$robotData = $this->robot_model->get_robot_by_id($data['id']);
		$robotNo = $robotData[0]->robotNo;

		//判斷設備資料是否使用中
		$check_robotNo = $this->work_form_model->check_form_robotNo($robotNo);
		if ($check_robotNo) {
			$result = array(
				"status" => false,
				"message" => "此設備已有工單報表資料，無法刪除"
			);
		} else {
			//刪除歷史資料表
			$this->history_model->del_table($robotData[0]->robotNo);

			$r = $this->robot_model->del_robot($data);

			if ($r) {
				$result = array(
					"status" => true,
					"message" => "刪除成功"
				);
			} else {
				$result = array(
					"status" => false,
					"message" => "刪除失敗"
				);
			}
		}
		return $result;
	}
}
