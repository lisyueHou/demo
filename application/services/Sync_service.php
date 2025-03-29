<?php
class Sync_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
		$this->load->model('remark_model');
		$this->load->model('work_place_model');
		$this->load->model('work_form_file_model');
		$this->load->model('work_form_model');
		$this->load->model('realtime_data_model');
		$this->load->model('history_model');
		$this->load->model('client_model');
		$this->load->model('robot_model');
		$this->load->model('form_remark_model');
		$this->load->service('common_service');
	}

	// 查詢使用者帳密
	public function getUsers()
	{
		$r = $this->users_model->get_users_staff();
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//查詢標記備註資料
	public function getRemark()
	{
		$r = $this->remark_model->get_remark();
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//查詢CAD圖資料
	public function getCadImg($clientNo)
	{
		$r = $this->work_place_model->get_cmdImg($clientNo);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//儲存上傳檔案資料
	public function addFile($data)
	{
		$r = $this->work_form_file_model->add_file($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//開始作業-建立工單報表
	public function addWorkForm($data)
	{
		//判斷設備編號是否存在，若不存在則新增設備資料
		$robotNo_r = $this->robot_model->check_robotNo($data['robotNo']);
		if (!$robotNo_r) {
			$robotData = array(
				"robotNo" => $data['robotNo'],
				"name" => $data['robotNo'],
				"state" => 0,
				"videoUrl" => '',
				"remark" => ''
			);
			$this->robot_model->add_robot($robotData);
		}

		//取得表單編號
		$formNo = $this->common_service->getFormNo($data['robotNo'], $data['startTime']);

		//開始作業-建立工單報表
		$data['formNo'] = $formNo;
		$data['startTime'] = date("Y-m-d H:i", strtotime($data['startTime']));
		$r = $this->work_form_model->add_workform($data);
		if ($r) {
			$result = array(
				"status" => true,
				"message" => '工單報表建立成功',
				"formNo" => $formNo
			);
		} else {
			$result = array(
				"status" => false,
				"message" => '工單報表建立失敗'
			);
		}
		return $result;
	}

	//結束作業-紀錄結束時間
	public function finishWork($data)
	{
		//檢查表單編號是否存在
		$check_r = $this->work_form_model->check_formNo($data['formNo']);
		if (!$check_r) {
			$result = array(
				"status" => false,
				"message" => '工單報表編號不存在'
			);
			return $result;
		}

		//判斷作業結束時間是否小於作業開始時間
		$workformData = $this->work_form_model->get_workform_by_formNo($data['formNo']); //取得作業開始時間
		$startTime = $workformData[0]->startTime;
		$data['finishTime'] = date("Y-m-d H:i", strtotime($data['finishTime'])) . ":00";
		if ($startTime > $data['finishTime']) {
			$result = array(
				"status" => false,
				"message" => '作業結束時間不可小於作業開始時間(作業開始時間：' . $startTime . ')'
			);
			return $result;
		}

		//結束作業-紀錄結束時間
		$r = $this->work_form_model->update_finishTime_by_formNo($data);
		if ($r) {
			$result = array(
				"status" => true,
				"message" => '作業結束時間記錄完成'
			);
		} else {
			$result = array(
				"status" => false,
				"message" => '作業結束時間記錄失敗'
			);
		}
		return $result;
	}

	//傳送即時數據
	public function addRealtimedata($data)
	{
		$robotNo = $data['robotNo'];
		$realtimedata = json_decode($data['data']);
		if(!is_array($realtimedata)){
			$result = array(
				"status" => false,
				"message" => '資料格式錯誤'
			);
			return $result;
		}
		if (count($realtimedata) <= 0) {
			$result = array(
				"status" => false,
				"message" => '請輸入數據資料'
			);
			return $result;
		}

		//儲存設備數據
		$faildata = array();
		foreach ($realtimedata as $robotData) {
			//儲存/更新到即時資料表
			$check_r = $this->realtime_data_model->get_data_by_robotNo($robotNo); //判斷設備是否已有數值
			if ($check_r) {
				$data_r = $this->realtime_data_model->update_data_by_robotNo($robotNo, $robotData);
			} else {
				$data_r = $this->realtime_data_model->add_data($robotNo, $robotData);
			}

			if (!$data_r) {
				array_push($faildata, $robotData);
			}

			//判斷歷史數據資料表是否存在，如不存在就建立資料表
			$checkTable_r = $this->history_model->check_table($robotNo);
			if ($checkTable_r == 0) {
				$this->history_model->creat_table($robotNo);
			}
			//儲存到歷史資料表
			$this->history_model->add_data($robotNo, $robotData);
		}

		$faildataCount = count($faildata);
		if ($faildataCount > 0) {
			$result = array(
				"status" => false,
				"message" => $faildataCount . '筆資料上傳失敗',
				"faildata" => $faildata
			);
		} else {
			$result = array(
				"status" => true,
				"message" => '資料上傳成功'
			);
		}
		return $result;
	}

	//查詢即時數據
	public function getRealtimedata($data)
	{
		$robotNo = $data['robotNo'];
		$r = $this->realtime_data_model->get_data_by_robotNo($robotNo);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//查詢歷史數據
	public function getHistorydata($data)
	{
		$r = $this->history_model->get_data_by_robotNo($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//查詢顧客資料
	public function getClient()
	{
		$r = $this->client_model->get_client_list_api();
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//查詢設備資料
	public function getRobot()
	{
		$r = $this->robot_model->get_robot_list_api();
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//上傳工單備註資訊
	public function uploadFormRemark($data)
	{
		$remarkData = json_decode($data['remarkData']);
		if(is_array($remarkData)){
			if (count($remarkData) != 0) {
				$successCount = 0;
				$failCount = 0;
				$msgArr = array();
				foreach ($remarkData as $row) {
					//判斷工單號是否存在
					$formNo_r = $this->work_form_model->check_formNo($row->formNo);
					if ($formNo_r) {
						$r = $this->form_remark_model->add_form_remark($row);
						if ($r) {
							$successCount++;
						} else {
							$failCount++;
						}
					} else {
						$failCount++;
						$msg = $row->formNo . "工單報表編號不存在";
						array_push($msgArr, $msg);
					}
				}
				if (count($msgArr) != 0) {
					$msgStr = implode(",", $msgArr);
					$message = "共" . $successCount . "筆資料上傳成功，" . $failCount . "筆資料上傳失敗(" . $msgStr . ")";
				} else {
					$message = "共" . $successCount . "筆資料上傳成功，" . $failCount . "筆資料上傳失敗";
				}
				$result = array(
					"status" => true,
					"message" => $message
				);
			} else {
				$result = array(
					"status" => true,
					"message" => "無備註資料"
				);
			}
		}else{
			$result = array(
				"status" => false,
				"message" => "資料型態錯誤"
			);
		}
		
		return $result;
	}

	//離線作業-建立並取得表單編號
	public function addTsForm($data)
	{
		//取得臨時表單編號
		$tsFormNoArr = explode("-", $data['tsFormNo']);
		if (!count($tsFormNoArr) == 3) {
			$result = array(
				"status" => false,
				"message" => "工單報表編號格式錯誤"
			);
			return $result;
		}

		//離線原因；NoNet01:表示網路突然中斷；NoNet02:表示直接離線操作
		$NoNetCode = $tsFormNoArr[0];
		switch ($NoNetCode) {
			case 'NoNet01':
				$NoNetReason = '網路突然中斷(代碼:NoNet01)';
				break;
			case 'NoNet02':
				$NoNetReason = '離線操作(代碼:NoNet02)';
				break;
			default:
				$NoNetReason = '離線原因不明(代碼:' . $NoNetCode . ')';
				break;
		}

		//判斷設備編號是否存在，若不存在則新增設備資料
		$robotNo = $tsFormNoArr[1];
		$robotNo_r = $this->robot_model->check_robotNo($robotNo);
		if (!$robotNo_r) {
			$robotData = array(
				"robotNo" => $robotNo,
				"name" => $robotNo,
				"state" => 0,
				"videoUrl" => '',
				"remark" => ''
			);
			$this->robot_model->add_robot($robotData);
		}

		//取得表單編號
		$formNo = $this->common_service->getFormNo($robotNo, $data['startTime']);

		//建立工單報表
		$data['formNo'] = $formNo;
		$data['robotNo'] = $robotNo;
		$data['startTime'] = date("Y-m-d H:i", strtotime($data['startTime']));
		$data['finishTime'] = date("Y-m-d H:i", strtotime($data['finishTime']));
		$data['remark'] = $NoNetReason;
		$data['clientId'] = '';
		$data['workPlaceId'] = '';
		$data['projectNo'] = '';
		$data['projectName'] = '';
		$data['contractor'] = '';
		$data['checkDate'] = '';
		$data['pipingLineNo'] = '';
		$data['segmentsNo'] = '';
		$r = $this->work_form_model->add_workform($data);
		if ($r) {
			$result = array(
				"status" => true,
				"message" => '工單報表建立成功',
				"formNo" => $formNo,
				"robotNo" => $robotNo
			);
		} else {
			$result = array(
				"status" => false,
				"message" => '工單報表建立失敗'
			);
		}
		return $result;
	}

	//離線作業-上傳表單即時資料及標記備註資料
	public function uploadOffline($data)
	{
		//上傳表單即時資料
		$realtimedata_r = array(
			"message" => '無上傳任何感測器資料'
		);
		if (isset($data['data'])) {
			if ($data['data'] != '') {
				$robotNo = $data['robotNo'];
				$realtimedata = json_decode($data['data']);
				if (is_array($realtimedata)) {
					if (count($realtimedata) > 0) {
						//儲存設備數據
						$faildata = array();
						foreach ($realtimedata as $robotData) {
							//儲存/更新到即時資料表
							$check_r = $this->realtime_data_model->get_data_by_robotNo($robotNo); //判斷設備是否已有數值
							if ($check_r) {
								$data_r = $this->realtime_data_model->update_data_by_robotNo($robotNo, $robotData);
							} else {
								$data_r = $this->realtime_data_model->add_data($robotNo, $robotData);
							}

							if (!$data_r) {
								array_push($faildata, $robotData);
							}

							//判斷歷史數據資料表是否存在，如不存在就建立資料表
							$checkTable_r = $this->history_model->check_table($robotNo);
							if ($checkTable_r == 0) {
								$this->history_model->creat_table($robotNo);
							}
							//儲存到歷史資料表
							$this->history_model->add_data($robotNo, $robotData);
						}

						$faildataCount = count($faildata);
						if ($faildataCount > 0) {
							$realtimedata_r = array(
								"message" => $faildataCount . '筆資料上傳失敗',
								"faildata" => $faildata
							);
						} else {
							$realtimedata_r = array(
								"message" => '資料上傳成功'
							);
						}
					}
				} else {
					$realtimedata_r = array(
						"message" => '資料型態錯誤'
					);
				}
			}
		}

		//標記備註資料
		$remarkData_r = array(
			"message" => "無備註資料"
		);
		if (isset($data['remarkData'])) {
			if ($data['remarkData'] != '') {
				$remarkData = json_decode($data['remarkData']);
				if (is_array($remarkData)) {
					if (count($remarkData) > 0) {
						$successCount = 0;
						$failCount = 0;
						foreach ($remarkData as $row) {
							$row->formNo = $data['formNo'];
							$r = $this->form_remark_model->add_form_remark($row);
							if ($r) {
								$successCount++;
							} else {
								$failCount++;
							}
						}
						$remarkData_r = array(
							"message" => "標記備註資料共" . $successCount . "筆上傳成功，" . $failCount . "筆上傳失敗"
						);
					}
				} else {
					$remarkData_r = array(
						"message" => "資料型態錯誤"
					);
				}
			}
		}

		$result = array(
			"status" => true,
			"data" => $realtimedata_r,
			"remarkData" => $remarkData_r
		);
		return $result;
	}
}
