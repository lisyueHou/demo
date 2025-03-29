<?php
class Dashboard_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('robot_model');
        $this->load->model('realtime_data_model');
        $this->load->model('work_form_model');
        $this->load->model('work_form_file_model');
        $this->load->model('form_remark_model');
    }

    //取得即時資訊
    public function getRobotRealTimeData($data)
    {
        //取得目前設備的資訊
        $r = $this->realtime_data_model->get_data_byDT_byrobotNo($data);
        if ($r) {
            //取得設備的工單
            $formData = $this->work_form_model->get_fromData_byDT_byrobotNo($data);
            if ($formData) {
                //取得工單標記資訊
                $form_remark = $this->form_remark_model->get_remark_by_formNo($formData[0]->formNo);

                //取得工單上傳的檔案(ex:圖片、影片)
                $form_file = $this->work_form_file_model->get_file_by_formNo_notmlt($formData[0]->formNo);

                $resultData_formfile['total_count'] = count($form_file);
                $resultData_formfile['page'] = 1;
                $resultData_formfile['page_count'] = 10;
                $resultData_formfile['total_page'] = ceil($resultData_formfile['total_count'] / $resultData_formfile['page_count']);
                //判斷目前有沒有超過
                if ($resultData_formfile['page'] > $resultData_formfile['total_page']) {
                    $resultData_formfile['page'] = $resultData_formfile['total_page'];
                    $resultData_formfile['page'] = $resultData_formfile['total_page'];
                }
                $resultData_formfile['data'] = array_slice($form_file, ($resultData_formfile['page'] - 1) * $resultData_formfile['page_count'], $resultData_formfile['page_count']);
                $resultData_formfile['data'] = array_values($resultData_formfile['data']);


                $resultData['total_count'] = count($form_remark);
                $resultData['page'] = 1;
                $resultData['page_count'] = 10;
                $resultData['total_page'] = ceil($resultData['total_count'] / $resultData['page_count']);
                //判斷目前有沒有超過
                if ($resultData['page'] > $resultData['total_page']) {
                    $resultData['page'] = $resultData['total_page'];
                    $resultData['page'] = $resultData['total_page'];
                }
                $resultData['data'] = array_slice($form_remark, ($resultData['page'] - 1) * $resultData['page_count'], $resultData['page_count']);
                $resultData['data'] = array_values($resultData['data']);

                $formData['form_remark_data'] = $resultData;
                $formData['form_file'] = $resultData_formfile;
                $r['formData'] = $formData;
                $result = array(
                    "status" => true,
                    "data" => $r,
                );
            } else { //查無工單
                $result = array(
                    "status" => false,
                );
            }
        } else { //設備超過5分鐘沒有更新資料
            $result = array(
                "status" => false,
            );
        }
        return $result;
    }

    //取得設備資料
    public function getRobot()
    {
        $r = $this->robot_model->get_robot_list_api();
        $result = array(
            "status" => true,
            "data" => $r,
        );
        return $result;
    }

	//取得標記資料
    public function formRemarkdata($data)
    {
        //取得工單標記資訊
        $form_remark = $this->form_remark_model->get_remark_by_formNo($data['formNo']);
        if ($form_remark) {
            $resultData['total_count'] = count($form_remark);
            $resultData['page'] = $data['page'];
            $resultData['page_count'] = $data['pageCount'];
            $resultData['total_page'] = ceil($resultData['total_count'] / $resultData['page_count']);
            //判斷目前有沒有超過
            if ($resultData['page'] > $resultData['total_page']) {
                $resultData['page'] = $resultData['total_page'];
                $resultData['page'] = $resultData['total_page'];
            }
            $resultData['data'] = array_slice($form_remark, ($resultData['page'] - 1) * $resultData['page_count'], $resultData['page_count']);
            $resultData['data'] = array_values($resultData['data']);
            $resultData['realtimeData'] = $this->realtime_data_model->get_data_by_robotNo($data['robotNo']);

            $result = array(
                "status" => true,
                "data" => $resultData,
            );

        } else {
            $result = array(
                "status" => false,
            );
        }
        return $result;
    }

    //取得上傳檔案資料
    public function formFiledata($data)
    {
        //取得工單上傳的檔案(ex:圖片、影片)
        $form_file = $this->work_form_file_model->get_file_by_formNo_notmlt($data['formNo']);
        if ($form_file) {
            $resultData['total_count'] = count($form_file);
            $resultData['page'] = $data['page'];
            $resultData['page_count'] = $data['pageCount'];
            $resultData['total_page'] = ceil($resultData['total_count'] / $resultData['page_count']);
            //判斷目前有沒有超過
            if ($resultData['page'] > $resultData['total_page']) {
                $resultData['page'] = $resultData['total_page'];
                $resultData['page'] = $resultData['total_page'];
            }
            $resultData['data'] = array_slice($form_file, ($resultData['page'] - 1) * $resultData['page_count'], $resultData['page_count']);
            $resultData['data'] = array_values($resultData['data']);
            $resultData['realtimeData'] = $this->realtime_data_model->get_data_by_robotNo($data['robotNo']);

            $result = array(
                "status" => true,
                "data" => $resultData,
            );

        } else {
            $result = array(
                "status" => false,
            );
        }
        return $result;
    }
}
