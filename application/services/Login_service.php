<?php
class Login_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
		$this->load->model('sys_authority_model');
	}

	// 前台-登入檢查
	public function loginFront($data)
	{
		$r = $this->users_model->check_login_staff($data);
		if ($r) {
			$result = array(
				"status" => true,
				"message" => "登入成功",
				"data" => $r
			);
		} else {
			$result = array(
				"status" => false,
				"message" => "帳號或密碼錯誤"
			);
		}
		return $result;
	}

	// 後台-登入檢查
	public function login($data)
	{
		// 取得帳號系統權限
		$sys_r = $this->sys_authority_model->get_authority($data);
		if (!$sys_r) {
			$result = array(
				"status" => false,
				"message" => "帳號不存在"
			);
			return $result;
		} else {
			switch ($data['system']) {
				case 1: //轉至液壓系統
					$url = CSU_LOGIN_PAGE;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("account" => $data['account'], "password" => $data['password'])));
					$output = curl_exec($ch);
					curl_close($ch);

					$r = json_decode($output, true);
					if ($r['status']) {
						$this->session->user_info=$r['data'];
						redirect(CSU_WELCOME_PAGE);
					} else {
						$result = array(
							"status" => false,
							"message" => $r['message']
						);
					}

					break;
				default: //預設系統-管線系統
					$r = $this->users_model->check_login($data);
					if ($r) {
						$result = array(
							"status" => true,
							"message" => "登入成功",
							"data" => $r
						);
					} else {
						$result = array(
							"status" => false,
							"message" => "帳號或密碼錯誤"
						);
					}
					break;
			}
			return $result;
		}
	}

	public function _loginAPI($data)
	{
		$url = "http://localhost/login_api/login";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("account" => $data['account'], "password" => $data['password'])));
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}
