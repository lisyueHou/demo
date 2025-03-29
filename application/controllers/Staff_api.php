<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Staff_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("staff_service");

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

    // 取得員工資料
    public function getStaff_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'depId' => $this->input->post("depId"),
            'staffNo' => $this->input->post("staffNo"),
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
            $this->response($this->staff_service->getStaff($data), 200);
        }
    }

    //新增員工資料
    public function addStaff_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'staffNo' => $this->input->post("staffNo"),
            'name' => $this->input->post("name"),
            'depId' => $this->input->post("depId"),
            'position' => $this->input->post("position"),
            'phone' => $this->input->post("phone"),
            'email' => $this->input->post("email"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("staffNo", 'lang:「員工編號」', "trim|required|max_length[20]|callback_str_validation");
        $this->form_validation->set_rules("name", 'lang:「員工姓名」', "trim|required|max_length[20]|callback_str_validation");
        $this->form_validation->set_rules("depId", 'lang:「部門單位」', "trim|required|numeric");
        $this->form_validation->set_rules("email", 'lang:「E-mail」', "trim|valid_email|max_length[100]");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->staff_service->addStaff($data), 200);
        }
    }

    //編輯員工資料
    public function editStaff_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'staffNo' => $this->input->post("staffNo"),
            'name' => $this->input->post("name"),
            'depId' => $this->input->post("depId"),
            'position' => $this->input->post("position"),
            'phone' => $this->input->post("phone"),
            'email' => $this->input->post("email"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("staffNo", 'lang:「員工編號」', "trim|required|max_length[20]|callback_str_validation");
        $this->form_validation->set_rules("name", 'lang:「員工姓名」', "trim|required|max_length[20]|callback_str_validation");
        $this->form_validation->set_rules("depId", 'lang:「部門單位」', "trim|required|numeric");
        $this->form_validation->set_rules("email", 'lang:「E-mail」', "trim|valid_email|max_length[100]");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->staff_service->editStaff($data), 200);
        }
    }

    //刪除員工資料
    public function delStaff_post()
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
            $this->response($this->staff_service->delStaff($data), 200);
        }
    }
}
