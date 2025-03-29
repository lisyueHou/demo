<?php
class Sys_authority_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('sys_authority_model');
	}

	//新增帳號
	public function addAccound($data)
	{
		$r = $this->sys_authority_model->add_account($data['account'], $data['authority']);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//修改帳號
	public function editAccound($data)
	{
		$r = $this->sys_authority_model->update_account($data, $data['authority']);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//刪除帳號
	public function delAccound($data)
	{
		$r = $this->sys_authority_model->del_account($data['account'], $data['authority']);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}
}
