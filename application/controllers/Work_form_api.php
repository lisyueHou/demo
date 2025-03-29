<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseAPIController.php';

class Work_form_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("work_form_service");

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

    // 取得工單報表
    public function getWorkform_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'formNo' => $this->input->post("formNo"),
            'projectNo' => $this->input->post("projectNo"),
            'checkDate' => $this->input->post("checkDate"),
            'projectName' => $this->input->post("projectName"),
            'contractor' => $this->input->post("contractor"),
            'company' => $this->input->post("company"),
            'clientId' => $this->input->post("clientId"),
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
            $this->response($this->work_form_service->getWorkform($data), 200);
        }
    }

    //新增工單報表
    public function addWorkform_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'startTime' => $this->input->post("startTime"),
            'finishTime' => $this->input->post("finishTime"),
            'robotNo' => $this->input->post("robotNo"),
            'clientId' => $this->input->post("clientId"),
            'workPlaceId' => $this->input->post("workPlaceId"),
            'projectNo' => $this->input->post("projectNo"),
            'projectName' => $this->input->post("projectName"),
            'subProjectName' => $this->input->post("subProjectName"),
            'contractor' => $this->input->post("contractor"),
            'checkPlace' => $this->input->post("checkPlace"),
            'checkDate' => $this->input->post("checkDate"),
            'pipingLineNo' => $this->input->post("pipingLineNo"),
            'segmentsNo' => $this->input->post("segmentsNo"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("startTime", 'lang:「作業開始時間」', "trim|required|callback_datetime_validation");
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required");
        $this->form_validation->set_rules("finishTime", 'lang:「作業結束時間」', "trim|callback_datetime_validation");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_form_service->addWorkForm($data), 200);
        }
    }

    //編輯工單報表資料
    public function editWorkform_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'formNo' => $this->input->post("formNo"),
            'startTime' => $this->input->post("startTime"),
            'finishTime' => $this->input->post("finishTime"),
            'robotNo' => $this->input->post("robotNo"),
            'clientId' => $this->input->post("clientId"),
            'oldClientId' => $this->input->post("oldClientId"),
            'workPlaceId' => $this->input->post("workPlaceId"),
            'projectNo' => $this->input->post("projectNo"),
            'projectName' => $this->input->post("projectName"),
            'subProjectName' => $this->input->post("subProjectName"),
            'contractor' => $this->input->post("contractor"),
            'checkPlace' => $this->input->post("checkPlace"),
            'checkDate' => $this->input->post("checkDate"),
            'pipingLineNo' => $this->input->post("pipingLineNo"),
            'segmentsNo' => $this->input->post("segmentsNo"),
            'remark' => $this->input->post("remark")
        );
        $this->form_validation->set_rules("id", 'lang:「id」', "trim|required|numeric");
        $this->form_validation->set_rules("startTime", 'lang:「作業開始時間」', "trim|required|callback_datetime_validation");
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required");
        $this->form_validation->set_rules("finishTime", 'lang:「作業結束時間」', "trim|callback_datetime_validation");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_form_service->editWorkform($data), 200);
        }
    }

    //刪除工單報表資料
    public function delWorkform_post()
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
            $this->response($this->work_form_service->delWorkform($data), 200);
        }
    }

    //取得作業地點選單資料
    public function getWorkPlaceList_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'clientId' => $this->input->post("clientId")
        );
        $this->form_validation->set_rules("clientId", 'lang:「客戶id」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_form_service->getWorkPlaceList($data), 200);
        }
    }

    //儲存簽核設定
    public function addFormSignSet_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'formNo' => $this->input->post("formNo"),
            'old_subcontratorId' => $this->input->post("old_subcontratorId"),
            'old_proxy_subcontratorId' => $this->input->post("old_proxy_subcontratorId"),
            'subcontratorId' => $this->input->post("subcontratorId"),
            'proxy_subcontratorId' => $this->input->post("proxy_subcontratorId"),
            'constructionId' => $this->input->post("constructionId"),
            'proxy_constructionId' => $this->input->post("proxy_constructionId"),
            'qualityEngineerId' => $this->input->post("qualityEngineerId"),
            'proxy_qualityEngineerId' => $this->input->post("proxy_qualityEngineerId")
        );
        $this->form_validation->set_rules("formNo", 'lang:「表單編號」', "trim|required");
        $this->form_validation->set_rules("subcontratorId", 'lang:「施工單位簽核人員」', "trim|required");
        $this->form_validation->set_rules("constructionId", 'lang:「建造工程師單位簽核人員」', "trim|required");
        $this->form_validation->set_rules("qualityEngineerId", 'lang:「品管工程師單位簽核人員」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_form_service->addFormSignSet($data), 200);
        }
    }

    //執行人員簽核
    public function addFormSign_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'formNo' => $this->input->post("formNo"),
            'signSort' => $this->input->post("signSort"),
            'personId' => $this->input->post("personId")
        );
        $this->form_validation->set_rules("formNo", 'lang:「表單編號」', "trim|required");
        $this->form_validation->set_rules("signSort", 'lang:「簽核順序」', "trim|required|numeric");
        $this->form_validation->set_rules("personId", 'lang:「簽核人員id」', "trim|required|numeric");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->work_form_service->addFormSign($data), 200);
        }
    }

    //刪除工單標註資料
    public function delFormRemark_post()
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
            $this->response($this->work_form_service->delFormRemark($data), 200);
        }
    }

    //修改工單標註資料
    public function editFormRemark_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'id' => $this->input->post("id"),
            'meters' => $this->input->post("meters"),
            'content' => $this->input->post("content"),
            'results' => $this->input->post("results"),
            'remark' => $this->input->post("remark")
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
            $this->response($this->work_form_service->editFormRemark($data), 200);
        }
    }
}
