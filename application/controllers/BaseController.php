<?php

require APPPATH . 'libraries/CreatorJwt.php';

class BaseController extends CI_Controller
{
    // 連接資料庫
    public function __construct()
    {
        parent::__construct();
        $this->load->service("Common_service");
        $this->load->library('session');
        $this->objOfJwt = new CreatorJwt();
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
                "token" => $jwtToken['Token']
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
        $controller_name = $this->router->class; //取得Controller名稱
        $action_name = $this->router->method; //取得Action名稱
        $received_Token = "";
        if (isset($this->session->user_info)) { //從 session 取得 token
            $received_Token = $this->session->user_info->token;
        }else{
            log_message('ERROR', 'Token不合法:SESSION不存在');
        }

        //檢查token是否合法(存在於database)；
        $r = $this->common_service->checkToken($received_Token);
        if ($r['status']) {
            //正常，更新Token的T_UpdateDT
            $this->common_service->renewTokenUpdateDT($received_Token);

            // 檢查權限，是否可使用目前的controller/action；無權限則導到首頁
            if ($this->CheckAuthorize($r, $controller_name, $action_name)) {
                //有權限
                return array("status" => 1, "data" => $r['data']);
            } else {
                //無權限
                return array("status" => 2, "data" => $r['data']);
            }
        } else { //token 不合法，導到登入頁面
            log_message('ERROR', 'Token不合法:'.$received_Token);
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
}
