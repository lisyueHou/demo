<?php
class Client_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('client_model');
		$this->load->model('users_model');
	}

	//取得顧客資料
	public function getClient($data)
	{
		$r = $this->client_model->get_client_limit($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//新增顧客資料
	public function addClient($data)
	{
		//檢查顧客編號是否重複
		$check_r = $this->client_model->check_clientNo($data['clientNo']);
		if (!$check_r) {
			$r = $this->client_model->add_client($data);
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
				"message" => "顧客編號已存在，請重新輸入"
			);
		}
		return $result;
	}

	//取得顧客資料 by id
	public function getClientById($id)
	{
		$r = $this->client_model->get_client_by_id($id);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//編輯顧客資料
	public function editClient($data)
	{
		//檢查顧客編號是否重複
		$check_r = $this->client_model->check_clientNo_by_id($data['clientNo'], $data['id']);
		if (!$check_r) {
			$r = $this->client_model->update_client($data);
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
				"message" => "顧客編號已存在，請重新輸入"
			);
		}
		return $result;
	}

	//刪除顧客資料
	public function delClient($data)
	{
		//檢查顧客資料是否已被帳號綁定
		$check_r = $this->users_model->check_account_isUsed(2, $data['id']);
		if (!$check_r) {
			$r = $this->client_model->del_client($data);
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
				"message" => "顧客資料已有綁定帳號，無法刪除"
			);
		}
		return $result;
	}
}
