<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class Work_place extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("work_place_service");

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

    //作業地點首頁
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
                "areaId" => $searchData_obj->areaId,
                "workPlace" => $searchData_obj->workPlace,
                "company" => $searchData_obj->company
            );
        } else {
            $data = array(
                "page" => 1,
                "pageCount" => 10,
                "areaId" => "",
                "workPlace" => "",
                "company" => ""
            );
        }

        $result = $this->work_place_service->getWorkplace($data);
        $result['searchData'] = $data;

        //取得選單資料
        $select_r = $this->work_place_service->getSelectData();
        $result['area'] = $select_r['area'];

        $this->load->view('header');
        $this->load->view('work_place/index', $result);
        $this->load->view('work_place/note');
        $this->load->view('footer');
    }

    //設定CAD路徑
    public function setcad()
    {
        $data = array(
            'id' => $this->input->post("id"),
            'imgPath' => $this->input->post("imgPath"),
            'coordinate' => $this->input->post("coordinate"),
            'searchData' => $this->input->post("searchData")
        );

        $this->load->view('header');
        $this->load->view('work_place/setcad', $data);
        $this->load->view('footer');
    }

    //新增頁面
    public function add()
    {
        //取得選單資料
        $result = $this->work_place_service->getSelectData();
        $result['searchData'] = $this->input->post("searchData");

        $this->load->view('header');
        $this->load->view('work_place/add', $result);
        $this->load->view('footer');
    }

    //編輯頁面
    public function edit()
    {
        //取得選單資料
        $select_r = $this->work_place_service->getSelectData();
        $result['area'] = $select_r['area'];
        $result['client'] = $select_r['client'];

        $result['searchData'] = $this->input->post("searchData");

        //重新取得作業地區資料
        $data_obj = json_decode($this->input->post("data"));
        $newData = $this->work_place_service->getWorkplaceById($data_obj->id);
        $result['data'] = json_encode($newData['data'][0],true);

        $this->load->view('header');
        $this->load->view('work_place/edit', $result);
        $this->load->view('footer');
    }
}
