<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class Robot extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("robot_service");

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

    //設備維護首頁
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
                "robotNo" => $searchData_obj->robotNo,
                "name" => $searchData_obj->name
            );
        } else {
            $data = array(
                "page" => 1,
                "pageCount" => 10,
                "robotNo" => "",
                "name" => ""
            );
        }

        $result = $this->robot_service->getRobot($data);
        $result['searchData'] = $data;

        $this->load->view('header');
        $this->load->view('robot/index', $result);
        $this->load->view('robot/note');
        $this->load->view('footer');
    }

    //新增頁面
    public function add()
    {
        $result['searchData'] = $this->input->post("searchData");

        $this->load->view('header');
        $this->load->view('robot/add', $result);
        $this->load->view('footer');
    }

    //編輯頁面
    public function edit()
    {
        $result['searchData'] = $this->input->post("searchData");

        //重新取得設備資料
        $data_obj = json_decode($this->input->post("data"));
        $newData = $this->robot_service->getRobotById($data_obj->id);
        $result['data'] =$newData['data'][0];

        $this->load->view('header');
        $this->load->view('robot/edit', $result);
        $this->load->view('footer');
    }
}
