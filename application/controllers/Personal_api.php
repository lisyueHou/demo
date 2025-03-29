<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Personal_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("personal_service");

        // 登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1) {
            //Token合法並具有權限，將資料儲存在session
            $this->session->user_info = $r['data'][0];
        } elseif ($r['status'] == 2) {
            //Token合法，不具有此頁面權限
            exit("Without Authorization");
        } else {
            //Token不合法或逾時
            exit("Invalid Token");
        }
    }

    //變更密碼
    public function editPass_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'password' => $this->input->post("password")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("password", 'lang:「密碼」', "trim|required|max_length[50]|min_length[4]|alpha_numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->personal_service->editPass($data), 200);
        }
    }
}
