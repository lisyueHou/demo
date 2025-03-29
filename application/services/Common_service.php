<?php
class Common_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('authorization_model');
        $this->load->model('users_model');
        $this->load->model('groups_model');
        $this->load->model('staff_model');
        $this->load->model('client_model');
        $this->load->model('work_form_model');
        $this->load->library('phpmailer_lib');
    }

    // 更新Token
    public function renewTokenById($user_id, $token)
    {
        $r = $this->users_model->update_Token_by_id($user_id, $token);
        if ($r) {
            $result = array(
                "status" => true,
                "message" => "Token更新成功"
            );
        } else {
            $result = array(
                "status" => false,
                "message" => "Token更新失敗"
            );
        }
        return $result;
    }

    // 檢查資料庫是否有使用者 Token
    public function checkToken($token)
    {
        $r = $this->users_model->get_user_by_token($token);
        if ($r) {
            $groupId = $r[0]->groupId;
            $personId = $r[0]->personId;

            //取得群組權限
            $auth_r = $this->authorization_model->get_auth_by_groupid($groupId);
            $r[0]->authorization = $auth_r;

            //取得群組類別，判斷帳號是員工還是客戶的
            $group_r = $this->groups_model->get_groups_by_id($groupId);
            $groupClass = $group_r[0]->class;
            if ($groupClass == 1) {
                //取得員工資料
                $staff_r = $this->staff_model->get_staff_by_id($personId);
                $r[0]->user = array(
                    "userNo" => $staff_r[0]->staffNo,
                    "userName" => $staff_r[0]->name
                );
            } else {
                //取得客戶資料
                $client_r = $this->client_model->get_client_by_id($personId);
                $r[0]->user = array(
                    "userNo" => $client_r[0]->clientNo,
                    "userName" => $client_r[0]->company
                );
            }

            $result = array(
                "status" => true,
                "data" => $r,
            );
        } else {
            $result = array(
                "status" => false,
                "message" => "查無Token",
            );
        }
        return $result;
    }

    // 更新Token 的 update time
    public function renewTokenUpdateDT($token)
    {
        $r = $this->users_model->update_TUpdateDT_by_token($token);
        if ($r) {
            $result = array(
                "status" => true,
                "data" => $r
            );
        } else {
            $result = array(
                "status" => false,
                "message" => "Token更新失敗"
            );
        }
        return $result;
    }

    // 檢查使用者是否有權限
    public function checkIfAuthorized($group_id, $controller_name, $action_name)
    {
        $r = $this->authorization_model->get_function_by_groupId($group_id, $controller_name, $action_name);
        if ($r) {
            $result = array(
                "status" => true,
                "data" => $r,
            );
        } else {
            $result = array(
                "status" => false,
                "message" => "無功能權限",
            );
        }
        return $result;
    }

    //取得工單編號
    public function getFormNo($robotNo, $datetime)
    {
        //取得作業開始日最後一張工單編號
        $date = date(('Ymd'), strtotime($datetime));
        $workDate = date(('Y-m-d'), strtotime($datetime));
        $lastForm = $this->work_form_model->get_last_formNo($workDate, $robotNo);
        if (count($lastForm) == 0) {
            $formNo = $robotNo . '-' . $date . '001';
        } else {
            $no = (int)substr($lastForm[0]->formNo, -3);
            $no++;
            switch (strlen($no)) {
                case 1:
                    $no = '00' . $no;
                    break;
                case 2:
                    $no = '0' . $no;
                    break;
                case 3:
                    $no = $no;
                    break;
            }
            $formNo = $robotNo . '-' . $date . $no;
        }

        //檢查工單編號是否存在
        $check_r = $this->work_form_model->check_formNo($formNo);
        if (!$check_r) {
            return $formNo;
        } else {
            $this->getFormNo($robotNo, $datetime);
        }
    }


    //發送信件
    /*
    SMTP、寄件者等資訊在 application/libraries/Phpmailer_lib.php 裡面設定
     */
    public function sendMail($to, $cc, $subject, $isHTML, $content, $path = false, $filename = false)
    {
        $EmailFormatIsCorrect = true;

        // PHPMailer object
        $mail = $this->phpmailer_lib->load();

        $mail_send = "";
        $to = explode(";", $to);
        for ($i = 0; $i < count($to); $i++) {
            $mail_send = $to[$i];
            //判斷email是否符合格式，如果不符合回傳錯誤訊息
            if (preg_match("/^([\w\.\-]){1,64}\@([\w\.\-]){1,64}$/", $mail_send)) {
                $mail->AddAddress("$mail_send", "$mail_send");
            } else {
                $EmailFormatIsCorrect = false;
            }
        }

        $mail_send = "";
        if (trim($cc) != "") {
            $cc = explode(";", $cc);
            for ($i = 0; $i < count($cc); $i++) {
                $mail_send = $cc[$i];
                //判斷email是否符合格式，如果不符合回傳錯誤訊息
                if (preg_match("/^([\w\.\-]){1,64}\@([\w\.\-]){1,64}$/", $mail_send)) {
                    $mail->AddAddress("$mail_send", "$mail_send");
                } else {
                    $EmailFormatIsCorrect = false;
                }
            }
        }

        if (!$EmailFormatIsCorrect) {
            $result = array(
                "status" => false,
                "message" => "Email格式錯誤",
            );
            return $result;
        }

        // Email subject
        $mail->Subject = $subject;

        // Set email format to HTML
        $mail->isHTML($isHTML);

        // Email body content
        $mailContent = $content;
        $mail->Body = $mailContent;

        // 判斷有無附件檔
        if ($filename) {
            $mail->AddAttachment($path . $filename, $filename); // 設定附件檔檔名
        }

        // Send email
        if (!$mail->send()) {
            $result = array(
                "status" => false,
                "message" => $mail->ErrorInfo,
            );
            return $result;
        } else {
            $result = array(
                "status" => true,
                "message" => "Email發送成功",
            );
            return $result;
        }
    }
}
