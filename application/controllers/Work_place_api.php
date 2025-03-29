<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Work_place_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("work_place_service");

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

    // 取得作業區域資料
    public function getWorkPlace_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'areaId' => $this->input->post("areaId"),
            'workPlace' => $this->input->post("workPlace"),
            'company' => $this->input->post("company"),
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
            $this->response($this->work_place_service->getWorkPlace($data), 200);
        }
    }

    //更新CAD圖路徑
    public function updateCadImg_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'coordinate' => $this->input->post("coordinate")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("coordinate[]", 'lang:「座標路徑」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_place_service->updateCadImg($data), 200);
        }
    }

    //新增作業區域資料
    public function addWorkPlace_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'areaId' => $this->input->post("areaId"),
            'clientId' => $this->input->post("clientId"),
            'name' => $this->input->post("name"),
            'cadImg' => $this->input->post("cadImg"),
            'latitude' => $this->input->post("latitude"),
            'longitude' => $this->input->post("longitude"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("areaId", 'lang:「作業區域」', "trim|required|numeric");
        $this->form_validation->set_rules("clientId", 'lang:「顧客公司名稱」', "trim|required|numeric");
        $this->form_validation->set_rules("name", 'lang:「作業地點」', "trim|required|max_length[20]|callback_str_validation");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_place_service->addWorkPlace($data), 200);
        }
    }

    //刪除管線圖
    public function delCadImg_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'cadImg' => $this->input->post("cadImg")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("cadImg", 'lang:「管線圖檔名」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_place_service->delCadImg($data), 200);
        }
    }

    //編輯作業區域資料
    public function editWorkPlace_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'areaId' => $this->input->post("areaId"),
            'clientId' => $this->input->post("clientId"),
            'name' => $this->input->post("name"),
            'cadImg' => $this->input->post("cadImg"),
            'cadImgName' => $this->input->post("cadImgName"),
            'latitude' => $this->input->post("latitude"),
            'longitude' => $this->input->post("longitude"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("areaId", 'lang:「作業區域」', "trim|required|numeric");
        $this->form_validation->set_rules("clientId", 'lang:「顧客公司名稱」', "trim|required|numeric");
        $this->form_validation->set_rules("name", 'lang:「作業地點」', "trim|required|max_length[20]|callback_str_validation");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_place_service->editWorkPlace($data), 200);
        }
    }

    //刪除作業區域資料
    public function delWorkPlace_post()
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
            $this->response($this->work_place_service->delWorkPlace($data), 200);
        }
    }
}
