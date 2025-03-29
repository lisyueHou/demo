<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'controllers/BaseAPIController.php';

class Sys_authority_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("form_validation");
        $this->load->service("sys_authority_service");
    }

    //新增帳號
    public function add_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'account' => $this->input->post("account"),
            'authority' => $this->input->post("authority")
        );
        $this->form_validation->set_rules("account", 'lang:「帳號」', "trim|required");
        $this->form_validation->set_rules("authority", 'lang:「系統id」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->sys_authority_service->addAccound($data), 200);
        }
    }

    //修改帳號
    public function edit_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'account' => $this->input->post("account"),
            'oldAccount' => $this->input->post("oldAccount"),
            'authority' => $this->input->post("authority")
        );
        $this->form_validation->set_rules("account", 'lang:「帳號」', "trim|required");
        $this->form_validation->set_rules("oldAccount", 'lang:「舊帳號」', "trim|required");
        $this->form_validation->set_rules("authority", 'lang:「系統id」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->sys_authority_service->editAccound($data), 200);
        }
    }

    //刪除帳號
    public function del_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'account' => $this->input->post("account"),
            'authority' => $this->input->post("authority")
        );
        $this->form_validation->set_rules("account", 'lang:「帳號」', "trim|required");
        $this->form_validation->set_rules("authority", 'lang:「系統id」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->sys_authority_service->delAccound($data), 200);
        }
    }
}
