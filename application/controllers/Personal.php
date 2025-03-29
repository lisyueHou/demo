<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class Personal extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("personal_service");

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

    //個人帳號維護首頁
    public function index()
    {
        //載入頁面預設資料
        $userId=$this->session->user_info->id;//使用者名稱
        $data = array(
            "id" => $userId
        );
        $result = $this->personal_service->getUserById($data);

        $this->load->view('header');
        $this->load->view('personal/index', $result);
        $this->load->view('personal/note');
        $this->load->view('footer');
    }
}
