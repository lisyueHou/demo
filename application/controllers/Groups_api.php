<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Groups_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("groups_service");

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

    // 取得權限群組
    public function getGroups_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'class' => $this->input->post("class"),
            'name' => $this->input->post("name"),
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
            $this->response($this->groups_service->getGroups($data), 200);
        }
    }

    // 取得群組帳號
    public function getGroupAccList_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'groupId' => $this->input->post("groupId"),
            'groupClass' => $this->input->post("groupClass")
        );
        $this->form_validation->set_rules("groupId", 'lang:「群組id」', "trim|required|numeric");
        $this->form_validation->set_rules("groupClass", 'lang:「群組類別」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->groups_service->getGroupAccList($data), 200);
        }
    }

    // 取得群組權限
    public function getGroupPremList_post()
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
            $this->response($this->groups_service->getGroupPremList($data), 200);
        }
    }

    //新增權限群組
    public function addGroup_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'name' => $this->input->post("name"),
            'class' => $this->input->post("class"),
            'authorization' => $this->input->post("authorization")
        );
        $this->form_validation->set_rules("name", 'lang:「群組名稱」', "trim|required|max_length[30]|callback_str_validation");
        $this->form_validation->set_rules("class", 'lang:「群組類別」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->groups_service->addGroup($data), 200);
        }
    }

    //編輯權限群組
    public function editGroup_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'name' => $this->input->post("name"),
            'class' => $this->input->post("class"),
            'authorization' => $this->input->post("authorization")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("name", 'lang:「群組名稱」', "trim|required|max_length[30]|callback_str_validation");
        $this->form_validation->set_rules("class", 'lang:「群組類別」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->groups_service->editGroup($data), 200);
        }
    }

    //刪除權限群組
    public function delGroup_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->groups_service->delGroup($data), 200);
        }
    }
}
