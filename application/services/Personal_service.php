<?php
class Personal_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
	}

	//取得帳號資料
	public function getUserById($data)
	{
		$r = $this->users_model->get_user_by_id($data['id']);
		$result = array(
			"status" => true,
			"data" => $r
		);
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
