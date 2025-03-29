<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Client_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("client_service");

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

    // 取得顧客資料
    public function getClient_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'clientNo' => $this->input->post("clientNo"),
            'company' => $this->input->post("company"),
            'companyId' => $this->input->post("companyId"),
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
            $this->response($this->client_service->getClient($data), 200);
        }
    }

    //新增顧客資料
    public function addClient_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'clientNo' => $this->input->post("clientNo"),
            'company' => $this->input->post("company"),
            'companyId' => $this->input->post("companyId"),
            'address' => $this->input->post("address"),
            'name' => $this->input->post("name"),
            'phone' => $this->input->post("phone"),
            'email' => $this->input->post("email"),
            'conName' => $this->input->post("conName"),
            'conPhone' => $this->input->post("conPhone"),
            'conEmail' => $this->input->post("conEmail"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("clientNo", 'lang:「顧客編號」', "trim|required|max_length[20]|callback_str_validation");
        $this->form_validation->set_rules("company", 'lang:「公司名稱」', "trim|required|max_length[30]|callback_str_validation");
        $this->form_validation->set_rules("email", 'lang:「顧客代表E-mail」', "trim|valid_email|max_length[100]");
        $this->form_validation->set_rules("conEmail", 'lang:「聯絡人E-mail」', "trim|valid_email|max_length[100]");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->client_service->addClient($data), 200);
        }
    }

    //編輯顧客資料
    public function editClient_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'clientNo' => $this->input->post("clientNo"),
            'company' => $this->input->post("company"),
            'companyId' => $this->input->post("companyId"),
            'address' => $this->input->post("address"),
            'name' => $this->input->post("name"),
            'phone' => $this->input->post("phone"),
            'email' => $this->input->post("email"),
            'conName' => $this->input->post("conName"),
            'conPhone' => $this->input->post("conPhone"),
            'conEmail' => $this->input->post("conEmail"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("clientNo", 'lang:「顧客編號」', "trim|required|max_length[20]|callback_str_validation");
        $this->form_validation->set_rules("company", 'lang:「公司名稱」', "trim|required|max_length[30]|callback_str_validation");
        $this->form_validation->set_rules("email", 'lang:「顧客代表E-mail」', "trim|valid_email|max_length[100]");
        $this->form_validation->set_rules("conEmail", 'lang:「聯絡人E-mail」', "trim|valid_email|max_length[100]");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->client_service->editClient($data), 200);
        }
    }

    //刪除顧客資料
    public function delClient_post()
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
            $this->response($this->client_service->delClient($data), 200);
        }
    }
}
