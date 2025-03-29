<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'controllers/BaseAPIController.php';

class Sync_api extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("form_validation");
        $this->load->service("sync_service");
    }

    //檔案上傳
    public function upload_post()
    {
        $folderName = $this->input->post("folderName"); //資料夾名稱(表單編號)
        $this->form_validation->set_rules('folderName', '資料夾名稱', 'trim|required|callback_str_validation');

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        }

        //判斷是否有要上傳的檔案
        if(!isset($_FILES['uploadFile'])){
            $result = array(
                "status" => false,
                "message" => "請選擇檔案"
            );
            $this->response($result, 200);
        }
        $fileCount = count($_FILES['uploadFile']['name']);
        if (($fileCount > 1) || (($fileCount == 1) && ($_FILES['uploadFile']['name'][0] != ""))) {

            //判斷資料夾是否存在
            $filePath = UPLOADS_PATH . "/" . $folderName; //檔案存放路徑
            if (!is_dir($filePath)) {
                mkdir($filePath);
            }

            //取得檔案資料，並執行上傳
            $data = array();
            $successCount = 0;
            for ($i = 0; $i < $fileCount; $i++) {
                if (!empty($_FILES['uploadFile']['name'][$i])) {
                    $_FILES['file']['name'] = $_FILES['uploadFile']['name'][$i];
                    $_FILES['file']['type'] = $_FILES['uploadFile']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['uploadFile']['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES['uploadFile']['error'][$i];
                    $_FILES['file']['size'] = $_FILES['uploadFile']['size'][$i];

                    $config['upload_path'] = $filePath;
                    $config['allowed_types'] = 'mp4|mlt|jpg|jpeg|png';
                    $this->load->library('upload', $config);

                    //執行上傳
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('file')) {
                        $uploadData = $this->upload->data();
                        $origName = $uploadData['orig_name']; //原始檔名
                        $fileName = $uploadData['file_name']; //儲存檔名
                        $fullPath = $uploadData['full_path']; //儲存路徑
                        $fileData = array(
                            'result' => 'success',
                            'origName' => $origName,
                            'fileName' => $fileName,
                            'fullPath' => $fullPath
                        );
                        $successCount++;

                        //儲存上傳的檔案資料
                        $fileExt = substr($uploadData['file_ext'], 1); //檔案型態
                        $mltContent = NULL;
                        if ($fileExt == 'mlt') { //取得mlt檔案內容
                            $fp  = fopen($fullPath, "r");
                            $str = fread($fp, filesize($fullPath));
                            $xml = simplexml_load_string($str);
                            $mltContent = json_encode($xml, JSON_UNESCAPED_UNICODE);
                        }
                        $addFile = array(
                            'formNo' => $folderName,
                            'fileType' => $fileExt,
                            'fileName' => $fileName,
                            'mltContent' => $mltContent
                        );
                        $this->sync_service->addFile($addFile);
                    } else {
                        $fileData = array(
                            'result' => 'fail',
                            'fileName' => $_FILES['uploadFile']['name'][$i],
                            'errorMessage' => $this->upload->display_errors('', '')
                        );
                    }
                    $data[] = $fileData;
                }
            }
            $result = array(
                "status" => true,
                "message" => "檔案數量:" . $fileCount . "(上傳成功:" . $successCount . "，上傳失敗:" . ($fileCount - $successCount) . ")",
                "data" => $data
            );
        } else {
            $result = array(
                "status" => false,
                "message" => "請選擇檔案"
            );
        }
        $this->response($result, 200);
    }


    //查詢已上傳的檔案
    public function get_file_post()
    {
        $folderName = $this->input->post("folderName"); //資料夾名稱
        $filePath = UPLOADS_PATH; //檔案存放路徑
        if ($folderName != "") {
            $filePath = UPLOADS_PATH . "/" . $folderName;
        }

        $file = array();
        if (is_dir($filePath)) {
            foreach (scandir($filePath) as $item) {
                if (!is_dir($item)) {
                    array_push($file, $item);
                }
            }
            $result = array(
                "status" => true,
                "data" => $file
            );
        } else {
            $result = array(
                "status" => false,
                "message" => '查無此資料夾'
            );
        }

        $this->response($result, 200);
    }

    //同步(查詢)所有使用者資料
    public function get_users_post()
    {
        $r = $this->sync_service->getUsers();
        if ($r['status']) {
            $result = array(
                "status" => true,
                "data" => $r['data']
            );
        } else {
            $result = array(
                "status" => false,
                "message" => "查詢失敗"
            );
        }

        $this->response($result, 200);
    }

    //同步(查詢)標記備註資料
    public function get_remark_post()
    {
        $r = $this->sync_service->getRemark();
        if ($r['status']) {
            $result = array(
                "status" => true,
                "data" => $r['data']
            );
        } else {
            $result = array(
                "status" => false,
                "message" => "查詢失敗"
            );
        }

        $this->response($result, 200);
    }

    //同步(查詢)CAD圖資料
    public function get_cad_post()
    {
        $clientNo = $this->input->post("clientNo");
        $r = $this->sync_service->getCadImg($clientNo);
        if ($r['status']) {
            $result = array(
                "status" => true,
                "data" => $r['data']
            );
        } else {
            $result = array(
                "status" => false,
                "message" => "查詢失敗"
            );
        }

        $this->response($result, 200);
    }

    //開始作業
    public function start_work_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'startTime' => $this->input->post("startTime"),
            'robotNo' => $this->input->post("robotNo")
        );
        $this->form_validation->set_rules("startTime", 'lang:「作業開始時間」', "trim|required|callback_datetime_validation");
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $data['finishTime'] = '';
            $data['clientId'] = '';
            $data['workPlaceId'] = '';
            $data['projectNo'] = '';
            $data['projectName'] = '';
            $data['contractor'] = '';
            $data['checkDate'] = '';
            $data['pipingLineNo'] = '';
            $data['segmentsNo'] = '';
            $data['remark'] = '';
            $this->response($this->sync_service->addWorkForm($data), 200);
        }
    }

    //結束作業
    public function finish_work_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'finishTime' => $this->input->post("finishTime"),
            'formNo' => $this->input->post("formNo")
        );
        $this->form_validation->set_rules("finishTime", 'lang:「作業結束時間」', "trim|required|callback_datetime_validation");
        $this->form_validation->set_rules("formNo", 'lang:「工單報表編號」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->sync_service->finishWork($data), 200);
        }
    }

    //傳送即時數據
    public function upload_realtimedata_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'robotNo' => $this->input->post("robotNo"),
            'data' => $this->input->post("data")
        );
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required");
        $this->form_validation->set_rules("data", 'lang:「數據資料」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->sync_service->addRealtimedata($data), 200);
        }
    }

    //查詢即時數據
    public function get_realtimedata_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'robotNo' => $this->input->post("robotNo"),
        );
        $this->response($this->sync_service->getRealtimedata($data), 200);
    }

    //查詢歷史數據
    public function get_historydata_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'robotNo' => $this->input->post("robotNo"),
            'startTime' => $this->input->post("startTime"),
            'endTime' => $this->input->post("endTime")
        );
        $this->form_validation->set_rules("robotNo", 'lang:「設備編號」', "trim|required");
        $this->form_validation->set_rules("startTime", 'lang:「開始時間」', "trim|callback_datetime_validation");
        $this->form_validation->set_rules("endTime", 'lang:「結束時間」', "trim|callback_datetime_validation");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->sync_service->getHistorydata($data), 200);
        }
    }

    //查詢顧客資料
    public function get_client_post()
    {
        $this->response($this->sync_service->getClient(), 200);
    }

    //查詢設備資料
    public function get_robot_post()
    {
        $this->response($this->sync_service->getRobot(), 200);
    }

    //上傳工單備註資訊
    public function upload_form_remark_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'remarkData' => $this->input->post("remarkData")
        );
        $this->form_validation->set_rules("remarkData", 'lang:「備註資料」', "trim|required");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            $this->response($this->sync_service->uploadFormRemark($data), 200);
        }
    }


    //離線作業-上傳表單資料
    public function upload_offline_post()
    {
        //驗證表單必填欄位是否空值
        $data = array(
            'tsFormNo' => $this->input->post("formNo"),
            'startTime' => $this->input->post("startTime"),
            'finishTime' => $this->input->post("finishTime"),
            'data' => $this->input->post("data"),
            'remarkData' => $this->input->post("remarkData")
        );
        $this->form_validation->set_rules("formNo", 'lang:「工單報表編號」', "trim|required");
        $this->form_validation->set_rules("startTime", 'lang:「作業開始時間」', "trim|required|callback_datetime_validation");
        $this->form_validation->set_rules("finishTime", 'lang:「作業結束時間」', "trim|required|callback_datetime_validation");

        //判斷規則是否成立
        if ($this->form_validation->run() === FALSE) {
            $result = array(
                "status" => false,
                "message" => $this->form_validation->error_string()
            );
            $this->response($result, 200);
        } else {
            //建立並取得表單編號
            $formNo_r = $this->sync_service->addTsForm($data);
            if(!$formNo_r['status']){
                $this->response($formNo_r, 200);
            }else{
                $formNo=$formNo_r['formNo'];
                $robotNo=$formNo_r['robotNo'];
            }

            //判斷是否有要上傳的檔案
            if(isset($_FILES['uploadFile'])){
                $fileCount = count($_FILES['uploadFile']['name']);
                if (($fileCount > 1) || (($fileCount == 1) && ($_FILES['uploadFile']['name'][0] != ""))) {
    
                    //判斷資料夾是否存在
                    $folderName=$formNo;
                    $filePath = UPLOADS_PATH . "/" . $folderName; //檔案存放路徑
                    if (!is_dir($filePath)) {
                        mkdir($filePath);
                    }
    
                    //取得檔案資料，並執行上傳
                    $uploadFileData = array();
                    $successCount = 0;
                    for ($i = 0; $i < $fileCount; $i++) {
                        if (!empty($_FILES['uploadFile']['name'][$i])) {
                            $_FILES['file']['name'] = $_FILES['uploadFile']['name'][$i];
                            $_FILES['file']['type'] = $_FILES['uploadFile']['type'][$i];
                            $_FILES['file']['tmp_name'] = $_FILES['uploadFile']['tmp_name'][$i];
                            $_FILES['file']['error'] = $_FILES['uploadFile']['error'][$i];
                            $_FILES['file']['size'] = $_FILES['uploadFile']['size'][$i];
    
                            $config['upload_path'] = $filePath;
                            $config['allowed_types'] = 'mp4|mlt|jpg|jpeg|png';
                            $this->load->library('upload', $config);
    
                            //執行上傳
                            $this->upload->initialize($config);
                            if ($this->upload->do_upload('file')) {
                                $uploadData = $this->upload->data();
                                $origName = $uploadData['orig_name']; //原始檔名
                                $fileName = $uploadData['file_name']; //儲存檔名
                                $fullPath = $uploadData['full_path']; //儲存路徑
                                $fileData = array(
                                    'result' => 'success',
                                    'origName' => $origName,
                                    'fileName' => $fileName,
                                    'fullPath' => $fullPath
                                );
                                $successCount++;
    
                                //儲存上傳的檔案資料
                                $fileExt = substr($uploadData['file_ext'], 1); //檔案型態
                                $mltContent = NULL;
                                if ($fileExt == 'mlt') { //取得mlt檔案內容
                                    $fp  = fopen($fullPath, "r");
                                    $str = fread($fp, filesize($fullPath));
                                    $xml = simplexml_load_string($str);
                                    $mltContent = json_encode($xml, JSON_UNESCAPED_UNICODE);
                                }
                                $addFile = array(
                                    'formNo' => $folderName,
                                    'fileType' => $fileExt,
                                    'fileName' => $fileName,
                                    'mltContent' => $mltContent
                                );
                                $this->sync_service->addFile($addFile);
                            } else {
                                $fileData = array(
                                    'result' => 'fail',
                                    'fileName' => $_FILES['uploadFile']['name'][$i],
                                    'errorMessage' => $this->upload->display_errors('', '')
                                );
                            }
                            $uploadFileData[] = $fileData;
                        }
                    }
                    $uploadFile_r = array(
                        "message" => "檔案數量:" . $fileCount . "(上傳成功:" . $successCount . "，上傳失敗:" . ($fileCount - $successCount) . ")",
                        "data" => $uploadFileData
                    );
                } else {
                    $uploadFile_r = array(
                        "message" => "無上傳任何檔案"
                    );
                }
            }else{
                $uploadFile_r = array(
                    "message" => "無上傳任何檔案"
                );
            }

            //上傳即時資料及標記備註資料
            $data['formNo']=$formNo;
            $data['robotNo']=$robotNo;
            $result=$this->sync_service->uploadOffline($data);
            $result['uploadFile']=$uploadFile_r;

            $this->response($result, 200);
        }
    }
}
