<?php
class Groups_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('groups_model');
		$this->load->model('users_model');
		$this->load->model('authorization_model');
	}

	//取得權限群組
	public function getGroups($data)
	{
		$r = $this->groups_model->get_groups($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//取得群組帳號
	public function getGroupAccList($data)
	{
		//判斷群組類別
		switch ($data['groupClass']) {
			case 1: //內部群組 -> staff
				$r = $this->users_model->get_staffAcc_by_groupId($data['groupId']);
				$result = array(
					"status" => true,
					"data" => $r
				);
				break;
			case 2: //顧客群組 -> client
				$r = $this->users_model->get_clientAcc_by_groupId($data['groupId']);
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

	//取得群組權限資料
	public function getGroupPremList($data)
	{
		$r = $this->authorization_model->get_auth_by_groupid($data['groupId']);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//取得系統功能權限
	public function getAuth()
	{
		$r = $this->authorization_model->get_auth();
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//新增權限群組
	public function addGroup($data)
	{
		//新增群組基本資料
		$groupId = $this->groups_model->add_group($data);

		//新增群組權限
		$authorization = array(); //基本預設權限
		if ($data['authorization']) {
			$authorization = array_merge($data['authorization'], BASIC_FUNCTION_ID);
		}
		$auth_data = array(
			'id' => $groupId,
			'authorization' => $authorization
		);
		$r = $this->_addAuth($auth_data);
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
		return $result;
	}

	// 編輯權限
	public function _addAuth($data)
	{
		// 刪除原先權限
		$this->authorization_model->delete_authorization($data);

		for ($i = 0; $i < count($data['authorization']); $i++) {
			// 取得大項及細項功能編號
			$functionNo_result = $this->authorization_model->get_functionNo($data['authorization'][$i]);

			// 取得相同編號的所有function
			$same_functionNo_result = $this->authorization_model->get_same_functionNo($functionNo_result[0]);
			for ($j = 0; $j < count($same_functionNo_result); $j++) {
				// 編輯權限
				$insert_authorization_data = array(
					'id' => $data['id'],
					'functionId' => $same_functionNo_result[$j]['id']
				);
				$this->authorization_model->add_authorization($insert_authorization_data);
			}
		}
		return true;
	}

	//取得權限群組 by id
	public function getGroupById($id)
	{
		$r = $this->groups_model->get_groups_by_id($id);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//編輯權限群組
	public function editGroup($data)
	{
		//更新群組資料
		$r = $this->groups_model->update_group($data);

		//新增群組權限
		$authorization = array(); //基本預設權限
		if ($data['authorization']) {
			$authorization = array_merge($data['authorization'], BASIC_FUNCTION_ID);
		}
		$auth_data = array(
			'id' => $data['id'],
			'authorization' => $authorization
		);
		$this->_addAuth($auth_data);

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
		return $result;
	}

	//刪除權限群組
	public function delGroup($data)
	{
		//檢查群組是否還有帳號資料
		$check = $this->users_model->check_users_by_groupId($data['id']);

		if (!$check) {
			$r = $this->groups_model->del_group($data);
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
				"message" => "權限群組尚有帳號資料，無法刪除"
			);
		}
		return $result;
	}
}
