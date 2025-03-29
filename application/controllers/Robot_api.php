<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Robot_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("robot_service");

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

    // 取得設備資料
    public function getRobot_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'robotNo' => $this->input->post("robotNo"),
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
            $this->response($this->robot_service->getRobot($data), 200);
        }
    }

    //新增設備資料
    public function addRobot_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'robotNo' => $this->input->post("robotNo"),
            'name' => $this->input->post("name"),
            'state' => $this->input->post("state"),
            'videoUrl' => $this->input->post("videoUrl"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required|max_length[20]|alpha_numeric");
        $this->form_validation->set_rules("name", 'lang:「設備名稱」', "trim|required|max_length[20]|callback_str_validation");
        $this->form_validation->set_rules("state", 'lang:「設備狀態」', "trim|required|numeric");
        $this->form_validation->set_rules("videoUrl", 'lang:「串流影像網址」', "trim|valid_url|max_length[255]");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->robot_service->addRobot($data), 200);
        }
    }

    //編輯設備資料
    public function editRobot_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'robotNo' => $this->input->post("robotNo"),
            'name' => $this->input->post("name"),
            'state' => $this->input->post("state"),
            'videoUrl' => $this->input->post("videoUrl"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required|max_length[20]|alpha_numeric");
        $this->form_validation->set_rules("name", 'lang:「設備名稱」', "trim|required|max_length[20]|callback_str_validation");
        $this->form_validation->set_rules("state", 'lang:「設備狀態」', "trim|required|numeric");
        $this->form_validation->set_rules("videoUrl", 'lang:「串流影像網址」', "trim|valid_url|max_length[255]");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->robot_service->editRobot($data), 200);
        }
    }

    //刪除設備資料
    public function delRobot_post()
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
            $this->response($this->robot_service->delRobot($data), 200);
        }
    }
}
