<?php
class Staff_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('staff_model');
		$this->load->model('department_model');
		$this->load->model('users_model');
	}

	//取得員工資料
	public function getStaff($data)
	{
		$r = $this->staff_model->get_staff($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//取得部門資料
	public function getDepartment()
	{
		$r = $this->department_model->get_department();
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//新增員工資料
	public function addStaff($data)
	{
		//檢查員工編號是否重複
		$check_r = $this->staff_model->check_staffNo($data['staffNo']);
		if (!$check_r) {
			$r = $this->staff_model->add_staff($data);
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
				"message" => "員工編號已存在，請重新輸入"
			);
		}
		return $result;
	}

	//取得員工資料 by id
	public function getStaffById($id)
	{
		$r = $this->staff_model->get_staff_by_id($id);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//編輯員工資料
	public function editStaff($data)
	{
		//檢查員工編號是否重複
		$check_r = $this->staff_model->check_staffNo_by_id($data['staffNo'], $data['id']);
		if (!$check_r) {
			$r = $this->staff_model->update_staff($data);
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
				"message" => "員工編號已存在，請重新輸入"
			);
		}
		return $result;
	}

	//刪除員工資料
	public function delStaff($data)
	{
		//檢查員工資料是否已被帳號綁定
		$check_r = $this->users_model->check_account_isUsed(1,$data['id']);
		if (!$check_r) {
			$r = $this->staff_model->del_staff($data);
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
		} else {
			$result = array(
				"status" => false,
				"message" => "員工資料已有綁定帳號，無法刪除"
			);
		}
		return $result;
	}
}
