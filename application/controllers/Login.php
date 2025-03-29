<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class Login extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("Login_service");
    }

    //登入
    public function index()
    {
        //判斷頁面是否有送出帳密
        $submit = $this->security->xss_clean($this->input->post("submit"));

        if ($submit == null) {
            $this->logout(); //執行登出，並回到登入頁面
        } else {
            //驗證表單必填欄位是否空值
            $account = $this->security->xss_clean($this->input->post("account"));
            $password = $this->security->xss_clean($this->input->post("password"));
            $system = $this->security->xss_clean($this->input->post("system"));
            $this->form_validation->set_rules('account', 'lang:「帳號」', 'required');
            $this->form_validation->set_rules('password', 'lang:「密碼」', 'required');

            if ($this->form_validation->run() === false) {
                $data = array(
                    "status" => false,
                    "message" => "請輸入帳號密碼",
                    "account" => $account,
                    "password" => $password,
                    "system" => $system
                );
                $this->load->view(LOGIN_PAGE, $data);
            } else {
                if ($system == 0) {
                    $data = array(
                        "status" => false,
                        "message" => "請選擇登入系統",
                        "account" => $account,
                        "password" => $password,
                        "system" => $system
                    );
                    $this->load->view(LOGIN_PAGE, $data);
                } else {
                    //登入驗證
                    $data = array(
                        "account" => $account,
                        "password" => $password,
                        "system" => $system
                    );
                    $result = $this->login_service->login($data);
                    if ($result['status']) {
                        //更新Token
                        $new_Token = $this->renewToken($result['data'][0]->id, $result['data'][0]->account);
                        if ($new_Token['status']) {
                            //重新取得使用者資訊
                            $check_r = $this->common_service->checkToken($new_Token['token']);

                            //紀錄帳號資料到SESSION
                            if ($check_r['status']) {
                                $this->session->user_info = $check_r['data'][0];
                                redirect(WELCOME_PAGE);
                            } else {
                                $data['status'] = $check_r['status'];
                                $data['message'] = $check_r['message'];
                            }
                        } else {
                            $data['status'] = $new_Token['status'];
                            $data['message'] = $new_Token['message'];
                        }
                    } else {
                        $data['status'] = $result['status'];
                        $data['message'] = $result['message'];
                    }
                    $this->load->view(LOGIN_PAGE, $data);
                }
            }
        }
    }

    //登出
    public function logout()
    {
        session_unset(); //清除所有SESSION
        $this->load->view(LOGIN_PAGE); //回登入頁面
    }
}
