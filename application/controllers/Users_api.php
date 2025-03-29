<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Users_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("users_service");

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

    // 取得帳號資料
    public function getUser_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'account' => $this->input->post("account"),
            'userName' => $this->input->post("userName"),
            'page' => $this->input->post("page"),
            'pageCount' => $this->input->post("pageCount")
        );
        $this->form_validation->set_rules("page", 'lang:「頁數」', "trim|required|numeric");
        $this->form_validation->set_rules("pageCount", 'lang:「筆數」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->users_service->getUser($data), 200);
        }
    }

    //取得使用者選單 by groupId
    public function getUserList_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'groupId' => $this->input->post("groupId")
        );
        $this->form_validation->set_rules("groupId", 'lang:「群組id」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->users_service->getUserList($data), 200);
        }
    }

    //新增帳號資料
    public function addUser_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'account' => $this->input->post("account"),
            'password' => $this->input->post("password"),
            'enable' => $this->input->post("enable"),
            'groupId' => $this->input->post("groupId"),
            'personId' => $this->input->post("personId"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("account", 'lang:「帳號」', "trim|required|max_length[20]|min_length[4]|alpha_numeric");
        $this->form_validation->set_rules("password", 'lang:「密碼」', "trim|required|max_length[50]|min_length[4]|alpha_numeric");
        $this->form_validation->set_rules("enable", 'lang:「帳號狀態」', "trim|required|numeric");
        $this->form_validation->set_rules("groupId", 'lang:「群組」', "trim|required|numeric");
        $this->form_validation->set_rules("personId", 'lang:「使用人員」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->users_service->addUser($data), 200);
        }
    }

    //編輯帳號資料
    public function editUser_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'account' => $this->input->post("account"),
            'oldAccount' => $this->input->post("oldAccount"),
            'enable' => $this->input->post("enable"),
            'groupId' => $this->input->post("groupId"),
            'personId' => $this->input->post("personId"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("account", 'lang:「帳號」', "trim|required|max_length[20]|min_length[4]|alpha_numeric");
        $this->form_validation->set_rules("enable", 'lang:「帳號狀態」', "trim|required|numeric");
        $this->form_validation->set_rules("groupId", 'lang:「群組」', "trim|required|numeric");
        $this->form_validation->set_rules("personId", 'lang:「使用人員」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->users_service->editUser($data), 200);
        }
    }

    //刪除帳號資料
    public function delUser_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'account' => $this->input->post("account")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("account", 'lang:「帳號」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->users_service->delUser($data), 200);
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
            $this->response($this->users_service->editPass($data), 200);
        }
    }
}
