<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class Groups extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("groups_service");

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

    //權限群組維護首頁
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
                "class" => $searchData_obj->class,
                "name" => $searchData_obj->name
            );
        } else {
            $data = array(
                "page" => 1,
                "pageCount" => 10,
                "class" => "",
                "name" => ""
            );
        }

        $result = $this->groups_service->getGroups($data);
        $result['searchData'] = $data;

        $this->load->view('header');
        $this->load->view('groups/index', $result);
        $this->load->view('groups/note');
        $this->load->view('footer');
    }

    //新增頁面
    public function add()
    {
        //取得功能權限
        $auth_r = $this->groups_service->getAuth();
        $result['auth'] = $auth_r['data'];
        $result['searchData'] = $this->input->post("searchData");

        $this->load->view('header');
        $this->load->view('groups/add', $result);
        $this->load->view('footer');
    }

    //編輯頁面
    public function edit()
    {
        $result['searchData'] = $this->input->post("searchData");

        //取得系統功能權限
        $auth_r = $this->groups_service->getAuth();
        $result['auth'] = $auth_r['data'];

        //重新取得資料
        $data_obj = json_decode($this->input->post("data"));
        $group_r = $this->groups_service->getGroupById($data_obj->id); //取得群組基本資料
        $result['group'] = $group_r['data'][0];
        $dataArr = array(
            "groupId" => $data_obj->id
        );
        $authCheck_r = $this->groups_service->getGroupPremList($dataArr); //取得群組權限
        $result['authCheck'] = $authCheck_r['data'];

        $this->load->view('header');
        $this->load->view('groups/edit', $result);
        $this->load->view('footer');
    }
}
