<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Login_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("form_validation");
        $this->load->service("login_service");
    }

    //前台登入
    public function login_front_post()
    {
        //取得IP
        $ip = $this->input->ip_address();

        //驗證表單必填欄位是否空值
        $account = $this->input->post("account");
        $password = $this->input->post("password");
        $this->form_validation->set_rules('account', '帳號', 'trim|required');
        $this->form_validation->set_rules('password', '密碼', 'trim|required');

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => "請輸入帳號及密碼"
            );
            $msg = "帳號:" . $account . "/時間:" . date('Y-m-d H:i:s') . "/IP:" . $ip . "/登入狀態:登入失敗(帳號或密碼空白)";
            log_message('error', $msg);
            $this->response($result, 200);
        } else {
            // 登入檢查
            $data = array(
                "account" => $account,
                "password" => $password
            );
            $r = $this->login_service->loginFront($data);
            if ($r['status']) {
                $result = array(
                    "status" => true,
                    "message" => $r['message'],
                    "data" => $r['data']
                );
                
                // 更新token
                $this->renewToken($r['data'][0]->id, $r['data'][0]->account);
                $statusMsg = "登入成功";
            } else {
                $result = array(
                    "status" => false,
                    "message" => $r['message']
                );
                $statusMsg = "登入失敗(" . $r['message'] . ")";
            }
            $msg = "帳號:" . $account . "/時間:" . date('Y-m-d H:i:s') . "/IP:" . $ip . "/登入狀態:" . $statusMsg;
            log_message('error', $msg);
            $this->response($result, 200);
        }
    }
}
