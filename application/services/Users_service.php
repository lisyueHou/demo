<?php
class Users_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
		$this->load->model('groups_model');
		$this->load->model('staff_model');
		$this->load->model('client_model');
		$this->load->model('sys_authority_model');
	}

	//取得帳號資料
	public function getUser($data)
	{
		$r = $this->users_model->get_users($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//取得群組資料
	public function getGroups()
	{
		$r = $this->groups_model->get_groups_list();
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//取得使用者選單 by groupId
	public function getUserList($data)
	{
		//取得群組資料
		$group_r = $this->groups_model->get_groups_by_id($data['groupId']);
		switch ($group_r[0]->class) {
			case 1: //內部群組 -> staff
				$r = $this->staff_model->get_staff_list();
				$result = array(
					"status" => true,
					"data" => $r
				);
				break;
			case 2: //顧客群組 -> client
				$r = $this->client_model->get_client_list();
				$result = array(
					"status" => true,
					"data" => $r
				);
				break;
			default:
				$result = array(
					"status" => false,
					"message" => "群組類別不存在"
				);
				break;
		}
		return $result;
	}

	//新增帳號資料
	public function addUser($data)
	{
		//檢查帳號是否重複
		$check_r = $this->users_model->check_account($data['account']);
		if (!$check_r) {
			$r = $this->users_model->add_user($data);
			$this->sys_authority_model->add_account($data['account'], 2);
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
				"message" => "帳號已存在，請重新輸入"
			);
		}
		return $result;
	}

	//取得帳號 by id
	public function getUserById($id)
	{
		$r = $this->users_model->get_user_by_id($id);
		//取得帳號使用者資料
		switch ($r[0]->class) {
			case 1: //內部群組 -> staff
				$staffList_r = $this->staff_model->get_staff_list();
				$r[0]->userList = $staffList_r;

				$staff_r = $this->staff_model->get_staff_list_by_id($r[0]->personId);
				$r[0]->userId = $staff_r[0]->id;
				$r[0]->userNo = $staff_r[0]->userNo;
				$r[0]->userName = $staff_r[0]->userName;
				$result = array(
					"status" => true,
					"data" => $r
				);
				break;
			case 2: //顧客群組 -> client
				$clientList_r = $this->client_model->get_client_list();
				$r[0]->userList = $clientList_r;

				$client_r = $this->client_model->get_client_list_by_id($r[0]->personId);
				$r[0]->userId = $client_r[0]->id;
				$r[0]->userNo = $client_r[0]->userNo;
				$r[0]->userName = $client_r[0]->userName;
				$result = array(
					"status" => true,
					"data" => $r
				);
				break;
			default:
				$result = array(
					"status" => false,
					"message" => "群組類別不存在"
				);
				break;
		}
		return $result;
	}

	//編輯員工資料
	public function editUser($data)
	{
		//檢查帳號是否重複
		$check_r = $this->users_model->check_account_by_id($data['account'], $data['id']);
		if (!$check_r) {
			//檢查帳號是否有修改
			if ($data['account'] != $data['oldAccount']) {
				$this->sys_authority_model->update_account($data, 2);
			}
			$r = $this->users_model->update_user($data);
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
				"message" => "帳號已存在，請重新輸入"
			);
		}
		return $result;
	}

	//刪除帳號
	public function delUser($data)
	{
		$r = $this->users_model->del_user($data);
		$this->sys_authority_model->del_account($data['account'], 2);
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

	//變更密碼
	public function editPass($data)
	{
		$r = $this->users_model->update_pass($data);

		if ($r) {
			$result = array(
				"status" => true,
				"message" => "密碼變更成功"
			);
		} else {
			$result = array(
				"status" => false,
				"message" => "密碼變更失敗"
			);
		}
		return $result;
	}
}
