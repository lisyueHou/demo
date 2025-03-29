<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class Remark extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("remark_service");

        //登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1) {
            //Token合法並具有權限，將資料儲存在session
            $this->session->user_info = $r['data'][0];
        } elseif ($r['status'] == 2) {
            //Token合法，不具有此頁面權限：轉導到首頁
            redirect(WELCOME_PAGE);
        } else {
            //Token不合法，讓使用者執行登出
            redirect(LOGOUT_PAGE);
        }
    }

    //首頁
    public function index()
    {
        //載入頁面預設資料
        if ($this->input->post("searchData")) {
            //有搜尋條件時代入資料
            $searchData = $this->input->post("searchData");
            $searchData_obj = json_decode($searchData);
            $data = array(
                "page" => $searchData_obj->page,
                "pageCount" => $searchData_obj->pageCount,
                "content" => $searchData_obj->content
            );
        } else {
            $data = array(
                "page" => 1,
                "pageCount" => 10,
                "content" => ""
            );
        }

        $result = $this->remark_service->getRemark($data);
        $result['searchData'] = $data;

        $this->load->view('header');
        $this->load->view('remark/index', $result);
        $this->load->view('footer');
    }

    //新增頁面
    public function add()
    {
        $result['searchData'] = $this->input->post("searchData");

        $this->load->view('header');
        $this->load->view('remark/add', $result);
        $this->load->view('footer');
    }

    //編輯頁面
    public function edit()
    {
        $result['searchData'] = $this->input->post("searchData");

        //重新取得設備資料
        $data_obj = json_decode($this->input->post("data"));
        $newData = $this->remark_service->getRemarkById($data_obj->id);
        $result['data'] =$newData['data'][0];

        $this->load->view('header');
        $this->load->view('remark/edit', $result);
        $this->load->view('footer');
    }
}
