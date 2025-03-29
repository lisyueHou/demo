<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class History extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("history_service");
        
        //登入驗證
        $r = $this->checkAA();
        if ($r['status'] == 1){
            //Token合法並具有權限，將資料儲存在session
            $this->session->user_info = $r['data'][0];
        }elseif($r['status'] == 2){
            //Token合法，不具有此頁面權限：轉導到首頁
            redirect(WELCOME_PAGE);
        }else{
            //Token不合法，讓使用者執行登出
            redirect(LOGOUT_PAGE);
        }
    }

    public function index()
    {
        //取得選單資料 (機器人)
        $select_r = $this->history_service->getRobot();

        //載入頁面預設資料
        if ($this->input->post("searchData")) {
            //有搜尋條件時代入資料
            $searchData = $this->input->post("searchData");
            $searchData_obj = json_decode($searchData);
            $data = array(
                "page" => $searchData_obj->page,
                "pageCount" => $searchData_obj->pageCount,
                "robotNo" => $searchData_obj->robotNo,
                "startTime" => $searchData_obj->startTime,
                "endTime" => $searchData_obj->endTime
            );
        } else {
            $data = array(
                "page" => 1,
                "pageCount" => 10,
                "robotNo" => $select_r['data'][0]->robotNo,
                "startTime" => (date("Y-m").'-01 00:00:00'),
                "endTime" => (date("Y-m").'-'.date("t").' 23:59:59')
            );
        }

        $result = $this->history_service->getRobotHistory($data);
        $result['searchData'] = $data;

        //取得選單資料
        $result['robot'] = $select_r['data'];
        $this->load->view('header');
        $this->load->view('history/index',$result);
        $this->load->view('footer');
    }
}
