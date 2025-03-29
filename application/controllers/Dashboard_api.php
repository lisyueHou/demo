<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Dashboard_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("dashboard_service");

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

    // 取得上傳檔案資料
    public function formFiledata_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'robotNo' => $this->input->post("robotNo"),
            'formNo' => $this->input->post("formNo"),
            'page' => $this->input->post("page"),
            'pageCount' => $this->input->post("pageCount")
        );
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required");
        $this->form_validation->set_rules("formNo", 'lang:「表單編號」', "trim|required");
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
            $this->response($this->dashboard_service->formFiledata($data), 200);
        }
    }

    // 取得標記資料
    public function formRemarkdata_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'robotNo' => $this->input->post("robotNo"),
            'formNo' => $this->input->post("formNo"),
            'page' => $this->input->post("page"),
            'pageCount' => $this->input->post("pageCount")
        );
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required");
        $this->form_validation->set_rules("formNo", 'lang:「表單編號」', "trim|required");
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
            $this->response($this->dashboard_service->formRemarkdata($data), 200);
        }
    }

    // 更換設備時，抓取資料
    public function getRobotRealTimeData_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'robotNo' => $this->input->post("robotNo")
        );
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required");

        $data['dataTime'] = date('Y-m-d H:i:s', strtotime('-1000 minutes'));//判斷5分鐘內有沒有updateTime
        
        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->dashboard_service->getRobotRealTimeData($data), 200);
        }
    }
}
