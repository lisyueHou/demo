<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/CreatorJwt.php';

class BaseAPIController extends RestController
{
    //連接指定的model檔案
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Common_service");
        $this->objOfJwt = new CreatorJwt();
        header('Content-Type: application/json');
    }

    //取得Token
    public function GenToken($user_id, $user_account)
    {
        $tokenData['id'] = $user_id;
        $tokenData['account'] = $user_account;
        $tokenData['timeStamp'] = date('Y-m-d H:i:s');
        $jwtToken = $this->objOfJwt->GenerateToken($tokenData);
        return array('Token' => $jwtToken);
    }

    //更新Token
    public function renewToken($user_id, $user_account)
    {
        $jwtToken = $this->GenToken($user_id, $user_account); //取得Token
        $r = $this->common_service->renewTokenById($user_id, $jwtToken['Token']);
        if ($r['status']) {
            $result = array(
                "status" => true,
                "message" => $r['message'],
                "data" => $jwtToken['Token']
            );
        } else {
            $result = array(
                "status" => false,
                "message" => $r['message']
            );
        }
        return $result;
    }

    // 檢查登入狀態 Authentication/使用權限 Authorization
    public function checkAA()
    {
        $bearer = array("Bearer ", "bearer ", "BEARER ");
        $controller_name = $this->router->class; //取得Controller名稱
        $action_name = $this->router->method; //取得Action名稱
        $received_Token = "";
        $headers = $this->input->request_headers('Authorization');
        if (array_key_exists('Authorization', $headers) && $headers['Authorization'] != '') {
            $received_Token = str_replace($bearer, "", $headers['Authorization']); //取得Token
        }
        //檢查token是否合法(存在於database)；
        $r = $this->common_service->checkToken($received_Token);
        if ($r['status']) {
            //正常，更新Token的T_UpdateDT
            $this->common_service->renewTokenUpdateDT($received_Token);

            //檢查權限，是否可使用目前的controller/action；無權限則導到首頁
            if ($this->CheckAuthorize($r, $controller_name, $action_name)) {
                //有權限
                return array("status" => 1, "data" => $r['data']);
            } else {
                //無權限
                return array("status" => 2, "data" => $r['data']);
            }
        } else { //token 不合法或逾時，導到登入頁面
            return array("status" => 0, "message" => "token 不合法");
        }
    }

    // 檢查使用者是否有權限
    public function CheckAuthorize($user_info, $controller_name, $action_name)
    {
        $isAuthorized = false;
        $group_id = $user_info['data'][0]->groupId;
        $result = $this->common_service->checkIfAuthorized($group_id, $controller_name, $action_name);
        if ($result['status']) {
            $isAuthorized = true;
        }
        return $isAuthorized;
    }

    //檢查是否包含特殊字元
    public function str_validation($data)
    {
        $re = "&,',\",<,>,!,%,$,=,/,[,],{,},+,*,..,:,|,?";
        $re = explode(',', $re);
        $re[] = ",";
        foreach ($re as $v) {
            if (strpos($data, $v) !== false) {
                $this->form_validation->set_message('str_validation', '{field}不可包含特殊字元');
                return false;
            }
        }
        return true;
    }

    //檢查是否為日期時間格式
    public function datetime_validation($date)
    {
        if ($date != '') {
            if (date('Y-m-d H:i:s', strtotime($date)) == $date) {
                return true;
            } else {
                $this->form_validation->set_message('datetime_validation', '{field}日期時間格式不正確');
                return false;
            }
        } else {
            return true;
        }
    }
}
