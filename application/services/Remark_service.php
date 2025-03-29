<?php
class Remark_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('remark_model');
	}

	//取得標記備註資料
	public function getRemark($data)
	{
		$r = $this->remark_model->get_remark_limit($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//新增標記備註資料
	public function addRemark($data)
	{
		//檢查標記備註資料是否重複
		$check_r = $this->remark_model->check_remark($data['content']);
		if (!$check_r) {
			$r = $this->remark_model->add_remark($data);
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
				"message" => "標記備註內容已存在，請重新輸入"
			);
		}
		return $result;
	}

	//取得標記備註資料 by id
	public function getRemarkById($id)
	{
		$r = $this->remark_model->get_remark_by_id($id);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//編輯標記備註資料
	public function editRemark($data)
	{
		//檢查標記備註資料是否重複
		$check_r = $this->remark_model->check_remark_by_id($data['content'], $data['id']);
		if (!$check_r) {
			$r = $this->remark_model->update_remark($data);
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
				"message" => "標記備註內容已存在，請重新輸入"
			);
		}
		return $result;
	}

	//刪除標記備註資料
	public function delRemark($data)
	{
		$r = $this->remark_model->del_remark($data);
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
		return $result;
	}
}
