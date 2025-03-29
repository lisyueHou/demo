<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class Work_form_qrcode extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html'));
        $this->load->library(array('form_validation', 'session'));
        $this->load->service("work_form_service");
    }

    // 檢視工單詳細資料
    public function viewDetail($id)
    {
        //取得工單報表
        $newData = $this->work_form_service->getWorkformById($id);
        $result['data'] = $newData['data'][0];

        //取得工單報表標記備註資料
        $result['formRemark'] = array();
        $formRemark = $this->work_form_service->getFormRemarkByformNo($newData['data'][0]->formNo);
        if (count($formRemark['data']) > 0) {
            $result['formRemark'] = $formRemark['data'];
        }

        //取得表單簽核資料
        $formSign = $this->work_form_service->getFormSignByformNo($newData['data'][0]->formNo);
        $result['formSign'] = $formSign['data'];

        $this->load->view('work_form_qrcode/detail', $result);
    }
}
