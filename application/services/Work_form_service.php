<?php
class Work_form_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('work_form_model');
		$this->load->model('robot_model');
		$this->load->model('client_model');
		$this->load->model('staff_model');
		$this->load->model('work_place_model');
		$this->load->model('form_remark_model');
		$this->load->model('form_sign_model');
		$this->load->model('users_model');
		$this->load->model('work_form_file_model');
		$this->load->service('common_service');
	}

	//取得工單報表及簽核資料
	public function getWorkform($data)
	{
		$workform_r = $this->work_form_model->get_workform($data);

		//取得工單簽核資料
		foreach ($workform_r['work_form'] as $row) {
			$formsign_r = $this->form_sign_model->get_sign_by_formNo($row->formNo);
			$row->formsign = $formsign_r;

			$row->signSort = 0; //目前應簽核的順序
			$row->isComplete = 0; //是否完成簽核
			$row->settingPersonId = NULL;
			$row->proxyPersonId = NULL;
			if (count($formsign_r) > 1) {
				foreach ($formsign_r as $formsign) {
					if ($formsign->signPersonId == NULL) {
						$row->signSort = $formsign->signSort;
						$row->settingPersonId = $formsign->settingPersonId;
						$row->proxyPersonId = $formsign->proxyPersonId;
						break;
					}
				}
				if ($row->signSort == 0) {
					$row->isComplete = 1;
				}
			}
		}

		$result = array(
			"status" => true,
			"data" => $workform_r
		);
		return $result;
	}

	//判斷登入帳號是否為顧客，若是則加上篩選條件(顧客只能看到自己的工單)
	public function checkLoginAcc($userId)
	{
		$user_r = $this->users_model->get_user_by_id($userId);
		$groupClass = $user_r[0]->class;
		if ($groupClass == 2) { //登入為顧客帳號
			return $user_r[0]->personId;
		} else {
			return;
		}
	}

	//取得選單資料
	public function getSelectData()
	{
		$robot = $this->robot_model->get_robot_list();
		$client = $this->client_model->get_client();
		$result = array(
			'robot' => $robot,
			'client' => $client
		);
		return $result;
	}

	//取得作業地點選單資料
	public function getWorkPlaceList($data)
	{
		$clientId = $data['clientId'];
		$r = $this->work_place_model->get_workplace_by_clientId($clientId);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//新增工單報表
	public function addWorkform($data)
	{
		//取得工單編號
		$formNo = $this->common_service->getFormNo($data['robotNo'], $data['startTime']);

		//開始作業-建立工單報表
		$data['formNo'] = $formNo;
		$r = $this->work_form_model->add_workform($data);

		//判斷是否有設定業主，若有則新增簽核資料
		if ($data['clientId']) {
			$formsignData = array(
				"formNo" => $formNo,
				"signSort" => 4,
				"settingPersonId" => $data['clientId'],
				"proxyPersonId" => NULL
			);
			$this->form_sign_model->add_formsign($formsignData);
		}

		if ($r) {
			$result = array(
				"status" => true,
				"message" => '工單報表建立成功'
			);
		} else {
			$result = array(
				"status" => false,
				"message" => '工單報表建立失敗'
			);
		}
		return $result;
	}

	//取得工單報表 by id
	public function getWorkformById($id)
	{
		$r = $this->work_form_model->get_workform_by_id($id);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//取得工單報表 by formNo
	public function getWorkformByFormNo($formNo)
	{
		$r = $this->work_form_model->get_workform_by_formNo($formNo);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//編輯工單報表資料
	public function editWorkform($data)
	{
		$formNo = $data['formNo'];
		//判斷是否有設定clientId
		//--若有:則判斷要新增或修改簽核資料
		//----新增(oldClientId=0):判斷品管單位是否已簽核，如品管單位已簽核，則發送簽核通知信件給顧客
		//----修改(oldClientId!=0):判斷是否有發過通知信，若有，則發送簽核通知信件給新顧客，並發送取消簽核通知信件給舊顧客
		//--若無:則判斷oldClientId是否為0
		//----有舊顧客資料(oldClientId!=0):代表將原設定的顧客資料移除，判斷是否有發過通知信，若有，則發送取消簽核通知信件給舊顧客
		$clientId = $data['clientId'];
		$oldClientId = $data['oldClientId'];
		if ($clientId) {
			if ($oldClientId == 0) { //新增
				//新增簽核資料
				$formsignData = array(
					"formNo" => $formNo,
					"signSort" => 4,
					"settingPersonId" => $clientId,
					"proxyPersonId" => NULL
				);
				$signId = $this->form_sign_model->add_formsign($formsignData);

				//判斷品管單位是否已簽核，如已簽核發送簽核通知信件
				$depQSign = $this->form_sign_model->get_sign_by_formNo_signSort($formNo, 3);
				if (count($depQSign) != 0) {
					if ($depQSign[0]->signPersonId) {
						//發送簽核通知
						$client = $this->client_model->get_client_by_id($clientId);
						if ($client[0]->email) { //顧客代表
							$signData = array(
								"formNo" => $formNo,
								"subjectType" => 'sign_client',
								"email" => $client[0]->email
							);
							$email_r = $this->signEmailNotitfy($signData);
							//更新發送Email時間
							if ($email_r['status']) {
								$this->form_sign_model->update_emailTime_by_id($signId);
							}
						}
					}
				}
			} elseif ($oldClientId != 0 && $oldClientId != $clientId) { //修改
				//取得舊的顧客簽核資料
				$clientSign = $this->form_sign_model->get_sign_by_formNo_signSort($formNo, 4);
				$oldSignId = $clientSign[0]->id;
				$emailTime = $clientSign[0]->emailTime;

				//判斷簽核資料是否已發過通知，若有則代表品管單位已簽核完畢，則發送取消簽核通知舊顧客，並通知新顧客
				if ($emailTime != NULL) {
					//發送取消簽核通知
					$oldClient = $this->client_model->get_client_by_id($oldClientId);
					if ($oldClient[0]->email) { //顧客代表
						$signData = array(
							"formNo" => $formNo,
							"subjectType" => 'cancelSign_client',
							"email" => $oldClient[0]->email
						);
						$email_r = $this->signEmailNotitfy($signData);
						//更新發送Email簽核取消通知時間
						if ($email_r['status']) {
							$this->form_sign_model->update_emailCancelTime_by_id($oldSignId);
						}
					}

					// 刪除舊的顧客簽核資料
					$this->form_sign_model->del_sign_by_id($oldSignId);

					//新增新的顧客簽核資料
					$formsignData = array(
						"formNo" => $formNo,
						"signSort" => 4,
						"settingPersonId" => $clientId,
						"proxyPersonId" => NULL
					);
					$signId = $this->form_sign_model->add_formsign($formsignData);

					//發送簽核通知給新設定的顧客
					$client = $this->client_model->get_client_by_id($clientId);
					if ($client[0]->email) { //顧客代表
						$signData = array(
							"formNo" => $formNo,
							"subjectType" => 'sign_client',
							"email" => $client[0]->email
						);
						$email_r = $this->signEmailNotitfy($signData);
						//更新發送Email時間
						if ($email_r['status']) {
							$this->form_sign_model->update_emailTime_by_id($signId);
						}
					}
				} else {
					//簽核資料沒有發送email通知時間有可能是當初顧客沒有設定email或發送email失敗
					//=>不需要發送取消簽核通知
					//=>但需判斷品管單位是否已簽核，若已簽核則通知新設定的顧客

					// 刪除舊的顧客簽核資料
					$this->form_sign_model->del_sign_by_id($oldSignId);

					//新增新的顧客簽核資料
					$formsignData = array(
						"formNo" => $formNo,
						"signSort" => 4,
						"settingPersonId" => $clientId,
						"proxyPersonId" => NULL
					);
					$signId = $this->form_sign_model->add_formsign($formsignData);
					$depQSign = $this->form_sign_model->get_sign_by_formNo_signSort($formNo, 3);
					if (count($depQSign) != 0) {
						if ($depQSign[0]->signPersonId) {
							//發送簽核通知
							$client = $this->client_model->get_client_by_id($clientId);
							if ($client[0]->email) { //顧客代表
								$signData = array(
									"formNo" => $formNo,
									"subjectType" => 'sign_client',
									"email" => $client[0]->email
								);
								$email_r = $this->signEmailNotitfy($signData);
								//更新發送Email時間
								if ($email_r['status']) {
									$this->form_sign_model->update_emailTime_by_id($signId);
								}
							}
						}
					}
				}
			}
		} else {
			//判斷oldClientId是否為0，若已有設定過客戶，且發送過簽核通知時，則發送取消簽核通知
			if ($oldClientId != 0) {
				//取得簽核資料
				$clientSign = $this->form_sign_model->get_sign_by_formNo_signSort($formNo, 4);
				$signId = $clientSign[0]->id;
				$settingPersonId = $clientSign[0]->settingPersonId;
				$emailTime = $clientSign[0]->emailTime;

				//發送取消簽核通知
				if ($emailTime != NULL && $oldClientId == $settingPersonId) {
					$oldClient = $this->client_model->get_client_by_id($oldClientId);
					if ($oldClient[0]->email) { //顧客代表
						$signData = array(
							"formNo" => $formNo,
							"subjectType" => 'cancelSign_client',
							"email" => $oldClient[0]->email
						);
						$email_r = $this->signEmailNotitfy($signData);
						//更新發送Email簽核取消通知時間
						if ($email_r['status']) {
							$this->form_sign_model->update_emailCancelTime_by_id($signId);
						}
					}
				}

				//刪除簽核資料
				$this->form_sign_model->del_sign_by_id($signId);
			}
		}

		//修改表單資料
		$r = $this->work_form_model->update_workform($data);
		if ($r) {
			$result = array(
				"status" => true,
				"message" => "儲存成功"
			);
		} else {
			$result = array(
				"status" => false,
				"message" => "儲存失敗"
			);
		}
		return $result;
	}

	//刪除工單報表
	public function delWorkform($data)
	{
		//取得工單編號
		$workform = $this->work_form_model->get_workform_by_id($data['id']);
		$formNo = $workform[0]->formNo;

		//刪除工單相關檔案
		$this->work_form_file_model->del_file_by_formNo($formNo);

		//刪除工單相關備註資料
		$this->form_remark_model->del_remark_by_formNo($formNo);

		//已發送簽核通知但尚未簽核，需發送取消簽核通知
		$notSignYetButNotify = $this->form_sign_model->get_notSignYetButNotify_by_formNo($formNo);
		if (count($notSignYetButNotify) > 0) {
			foreach ($notSignYetButNotify as $row) {
				$signId = $row->id;
				if ($row->signSort == 4) { //顧客
					//簽核人員
					if ($row->emailTime) {
						$client = $this->client_model->get_client_by_id($row->settingPersonId);
						if ($client[0]->email) { //顧客代表
							$signData = array(
								"formNo" => $formNo,
								"subjectType" => 'cancelSign_client',
								"email" => $client[0]->email
							);
							$email_r = $this->signEmailNotitfy($signData);
							//更新發送Email簽核取消通知時間
							if ($email_r['status']) {
								$this->form_sign_model->update_emailCancelTime_by_id($signId);
							}
						}
					}
				} else { //內部人員
					//簽核人員
					if ($row->emailTime) {
						$staff = $this->staff_model->get_staff_by_id($row->settingPersonId);
						if ($staff[0]->email) {
							$signData = array(
								"formNo" => $formNo,
								"subjectType" => 'cancelSign',
								"email" => $staff[0]->email
							);
							$email_r = $this->signEmailNotitfy($signData);
							//更新發送Email簽核取消通知時間
							if ($email_r['status']) {
								$this->form_sign_model->update_emailCancelTime_by_id($signId);
							}
						}
					}
					//代理人員
					if ($row->proxyEmailTime) {
						$staff = $this->staff_model->get_staff_by_id($row->proxyPersonId);
						if ($staff[0]->email) {
							$signData = array(
								"formNo" => $formNo,
								"subjectType" => 'cancelSign',
								"email" => $staff[0]->email
							);
							$email_r = $this->signEmailNotitfy($signData);
							//更新發送Email簽核取消通知時間
							if ($email_r['status']) {
								$this->form_sign_model->update_proxyEmailCancelTime_by_id($signId);
							}
						}
					}
				}
			}
		}
		//刪除工單簽核資料
		$this->form_sign_model->del_sign_by_formNo($formNo);

		//刪除工單
		$r = $this->work_form_model->del_workform($data);

		if ($r) {
			$result = array(
				"status" => true,
				"message" => "刪除成功"
			);
		} else {
			$result = array(
				"status" => false,
				"message" => "刪除失敗"
			);
		}
		return $result;
	}

	//取得工單標記備註資料
	public function getFormRemarkByformNo($formNo)
	{
		$r = $this->form_remark_model->get_remark_by_formNo($formNo);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//取得工單報表簽核資料
	public function getFormSignByformNo($formNo)
	{
		$formSign = $this->form_sign_model->get_sign_by_formNo($formNo);

		//取得人員姓名
		foreach ($formSign as $row) {
			$settingPersonId = $row->settingPersonId;
			$proxyPersonId = $row->proxyPersonId;
			$signPersonId = $row->signPersonId;
			if ($row->signSort != 4) { //員工
				//簽核人員
				if ($settingPersonId != '' && $settingPersonId != NULL) {
					$staff = $this->staff_model->get_staff_by_id($settingPersonId);
					$row->settingPersonName = $staff[0]->name;
					$row->settingPersonNo = $staff[0]->staffNo;
				} else {
					$row->settingPersonName = null;
					$row->settingPersonNo = null;
				}

				//簽和代理人
				if ($proxyPersonId != '' && $proxyPersonId != NULL) {
					$staff = $this->staff_model->get_staff_by_id($proxyPersonId);
					$row->proxyPersonName = $staff[0]->name;
					$row->proxyPersonNo = $staff[0]->staffNo;
				} else {
					$row->proxyPersonName = '';
					$row->proxyPersonNo = '';
				}

				//實際簽核人員
				if ($signPersonId != '' && $signPersonId != NULL) {
					$staff = $this->staff_model->get_staff_by_id($signPersonId);
					$row->signPersonName = $staff[0]->name;
					$row->signPersonNo = $staff[0]->staffNo;
				} else {
					$row->signPersonName = '';
					$row->signPersonNo = '';
				}
			} else { //顧客
				if ($settingPersonId != '' && $settingPersonId != NULL) {
					$client = $this->client_model->get_client_by_id($settingPersonId);
					$row->settingPersonName = $client[0]->company;
					$row->settingPersonNo = $client[0]->clientNo;
				} else {
					$row->settingPersonName = '';
					$row->settingPersonNo = '';
				}

				//實際簽核人員
				if ($signPersonId != '' && $signPersonId != NULL) {
					$client = $this->client_model->get_client_by_id($signPersonId);
					$row->signPersonName = $client[0]->company;
					$row->signPersonNo = $client[0]->clientNo;
				} else {
					$row->signPersonName = '';
					$row->signPersonNo = '';
				}
			}
		}
		$result = array(
			"status" => true,
			"data" => $formSign
		);
		return $result;
	}

	//Email通知
	public function signEmailNotitfy($data)
	{
		$email = $data['email'];
		$subjectType = $data['subjectType'];
		$formNo = $data['formNo'];

		//E-mail主旨
		switch ($subjectType) {
			case 'sign':
				$subject = '表單簽核通知';
				$msg = '系統中，尚有表單待您進行簽核，其編號為「' . $formNo . '」，請儘速至系統確認。';
				break;
			case 'sign_client':
				$subject = '表單核閱通知';
				$msg = '系統中，尚有表單待您進行核閱，其編號為「' . $formNo . '」，請儘速至系統確認。';
				break;
			case 'cancelSign':
				$subject = '表單簽核取消通知';
				$msg = '您的表單簽核已被取消，表單編號為「' . $formNo . '」。';
				break;
			case 'cancelSign_client':
				$subject = '表單核閱權限取消通知';
				$msg = '您的表單核閱權限已被取消，表單編號為「' . $formNo . '」。';
				break;
			default:
				$subject = '表單簽核通知';
				$msg = '系統中，尚有表單待您進行簽核，其編號為「' . $formNo . '」，請儘速至系統確認。';
				break;
		}

		$to = $email;
		$cc = "";
		$subject = "[管線清潔機器人管理系統] $subject";
		$isHTML = true;

		$content = "使用者 您好， <br>";
		$content = $content . "<br>";
		$content = $content . "$msg<br>";
		$content = $content . "<br>";
		$content = $content . "連結：<a href='" . base_url() . "work_form" . "'>表單管理</a>";
		$content = $content . " <br>";
		$content = $content . " <br>";
		$content = $content . " <br>";
		$content = $content . " <br>";
		$content = $content . " <br>";
		$content = $content . "管線清潔機器人管理系統 敬上 <br><br><br>";
		$content = $content . '<font color="#3C3C3C" size="1">*此為系統自動通知信件，請勿回覆*</font><br>';

		//send mail
		return $this->common_service->sendMail($to, $cc, $subject, $isHTML, $content);
	}

	//取得各部門人員清單資料 & 簽核人員資料
	public function getFormDepStaff($formNo)
	{
		$result = array();
		//施工單位人員清單
		$staff = $this->staff_model->get_staff_by_depId(2);
		$result['subcontrator']['staff'] = $staff;
		$formSign = $this->form_sign_model->get_sign_by_formNo_signSort($formNo, 1);
		$result['isSign'] = false;
		if (count($formSign) > 0) {
			$result['subcontrator']['settingPersonId'] = $formSign[0]->settingPersonId;
			$result['subcontrator']['proxyPersonId'] = $formSign[0]->proxyPersonId;

			//判斷是否已經進入簽核程序
			if ($formSign[0]->signPersonId) {
				$result['isSign'] = true;
			}
		} else {
			$result['subcontrator']['settingPersonId'] = NULL;
			$result['subcontrator']['proxyPersonId'] = NULL;
		}

		//建造單位人員清單
		$staff = $this->staff_model->get_staff_by_depId(3);
		$result['construction']['staff'] = $staff;
		$formSign = $this->form_sign_model->get_sign_by_formNo_signSort($formNo, 2);
		if (count($formSign) > 0) {
			$result['construction']['settingPersonId'] = $formSign[0]->settingPersonId;
			$result['construction']['proxyPersonId'] = $formSign[0]->proxyPersonId;
		} else {
			$result['construction']['settingPersonId'] = NULL;
			$result['construction']['proxyPersonId'] = NULL;
		}

		//品管單位人員清單
		$staff = $this->staff_model->get_staff_by_depId(4);
		$result['qualityEngineer']['staff'] = $staff;
		$formSign = $this->form_sign_model->get_sign_by_formNo_signSort($formNo, 3);
		if (count($formSign) > 0) {
			$result['qualityEngineer']['settingPersonId'] = $formSign[0]->settingPersonId;
			$result['qualityEngineer']['proxyPersonId'] = $formSign[0]->proxyPersonId;
		} else {
			$result['qualityEngineer']['settingPersonId'] = NULL;
			$result['qualityEngineer']['proxyPersonId'] = NULL;
		}
		return $result;
	}

	//儲存簽核設定
	public function addFormSignSet($data)
	{
		$formNo = $data['formNo'];

		//判斷施工單位簽核人員是否有修改，如果有則判斷是否需要發送取消簽核/簽核通知信件，再刪除舊設定
		$subcontrator_isSend = false;
		$proxy_subcontrator_isSend = false;
		if ($data['old_subcontratorId'] != NULL) {
			$depSign = $this->form_sign_model->get_sign_by_formNo_signSort($formNo, 1);
			$signId = $depSign[0]->id;

			//施工單位簽核人員-發送取消簽核通知
			if ($data['old_subcontratorId'] != $data['subcontratorId']) {
				if ($depSign[0]->emailTime) {
					$staff = $this->staff_model->get_staff_by_id($data['old_subcontratorId']);
					if ($staff[0]->email) {
						$signData = array(
							"formNo" => $formNo,
							"subjectType" => 'cancelSign',
							"email" => $staff[0]->email
						);
						$email_r = $this->signEmailNotitfy($signData);
						//更新發送Email簽核取消通知時間
						if ($email_r['status']) {
							$this->form_sign_model->update_emailCancelTime_by_id($signId);
						}
					}
				}
				$subcontrator_isSend = true; //需發送簽核通知
			}

			//施工單位簽核代理人員-發送取消簽核通知
			if ($data['old_proxy_subcontratorId'] != $data['proxy_subcontratorId']) {
				if ($depSign[0]->proxyEmailTime) {
					$staff = $this->staff_model->get_staff_by_id($data['old_proxy_subcontratorId']);
					if ($staff[0]->email) {
						$signData = array(
							"formNo" => $formNo,
							"subjectType" => 'cancelSign',
							"email" => $staff[0]->email
						);
						$email_r = $this->signEmailNotitfy($signData);
						//更新發送Email簽核取消通知時間
						if ($email_r['status']) {
							$this->form_sign_model->update_proxyEmailCancelTime_by_id($signId);
						}
					}
				}
				$proxy_subcontrator_isSend = true; //需發送簽核通知
			}
		} else {
			$subcontrator_isSend = true;
			$proxy_subcontrator_isSend = true;
		}
		//刪除舊設定
		$this->form_sign_model->del_depsign_by_formNo($formNo);

		//施工單位
		$satffId = $data['subcontratorId'];
		$proxy_satffId = $data['proxy_subcontratorId'];
		if ($satffId) {
			if (!$proxy_satffId) {
				$proxy_satffId = NULL;
			}
			$formsignData = array(
				"formNo" => $formNo,
				"signSort" => 1,
				"settingPersonId" => $satffId,
				"proxyPersonId" => $proxy_satffId
			);
			$signId = $this->form_sign_model->add_formsign($formsignData);

			//發送簽核通知-簽核人員
			if ($subcontrator_isSend) {
				$staff = $this->staff_model->get_staff_by_id($satffId);
				if ($staff[0]->email) {
					$signData = array(
						"formNo" => $formNo,
						"subjectType" => 'sign',
						"email" => $staff[0]->email
					);
					$email_r = $this->signEmailNotitfy($signData);
					//更新發送Email簽核通知時間
					if ($email_r['status']) {
						$this->form_sign_model->update_emailTime_by_id($signId);
					}
				}
			}

			//發送簽核通知-簽核代理人員
			if ($proxy_subcontrator_isSend) {
				if ($proxy_satffId) {
					$staff = $this->staff_model->get_staff_by_id($proxy_satffId);
					if ($staff[0]->email) {
						$signData = array(
							"formNo" => $formNo,
							"subjectType" => 'sign',
							"email" => $staff[0]->email
						);
						$email_r = $this->signEmailNotitfy($signData);
						//更新發送Email簽核通知時間
						if ($email_r['status']) {
							$this->form_sign_model->update_proxyEmailTime_by_id($signId);
						}
					}
				}
			}
		}

		//建造單位
		$satffId = $data['constructionId'];
		$proxy_satffId = $data['proxy_constructionId'];
		if ($satffId) {
			if (!$proxy_satffId) {
				$proxy_satffId = NULL;
			}
			$formsignData = array(
				"formNo" => $formNo,
				"signSort" => 2,
				"settingPersonId" => $satffId,
				"proxyPersonId" => $proxy_satffId
			);
			$this->form_sign_model->add_formsign($formsignData);
		}

		//品管單位
		$satffId = $data['qualityEngineerId'];
		$proxy_satffId = $data['proxy_qualityEngineerId'];
		if ($satffId) {
			if (!$proxy_satffId) {
				$proxy_satffId = NULL;
			}
			$formsignData = array(
				"formNo" => $formNo,
				"signSort" => 3,
				"settingPersonId" => $satffId,
				"proxyPersonId" => $proxy_satffId
			);
			$this->form_sign_model->add_formsign($formsignData);
		}

		$result = array(
			"status" => true,
			"message" => '儲存成功'
		);

		return $result;
	}


	//執行人員簽核
	public function addFormSign($data)
	{
		$r = $this->form_sign_model->update_signPerson($data);
		$formNo = $data['formNo'];

		//通知下一位簽核人員
		if ($data['signSort'] < 4) {
			$nextSignSort = (int)$data['signSort'] + 1;
			$formSign = $this->form_sign_model->get_sign_by_formNo_signSort($data['formNo'], $nextSignSort);
			$signId = $formSign[0]->id;
			if ($nextSignSort < 4) { //通知內部簽核人員
				//發送簽核通知-簽核人員
				$satffId = $formSign[0]->settingPersonId;
				$staff = $this->staff_model->get_staff_by_id($satffId);
				if ($staff[0]->email) {
					$signData = array(
						"formNo" => $formNo,
						"subjectType" => 'sign',
						"email" => $staff[0]->email
					);
					$email_r = $this->signEmailNotitfy($signData);
					//更新發送Email簽核通知時間
					if ($email_r['status']) {
						$this->form_sign_model->update_emailTime_by_id($signId);
					}
				}

				//發送簽核通知-簽核代理人員
				$proxy_satffId = $formSign[0]->proxyPersonId;
				if ($proxy_satffId) {
					$staff = $this->staff_model->get_staff_by_id($proxy_satffId);
					if ($staff[0]->email) {
						$signData = array(
							"formNo" => $formNo,
							"subjectType" => 'sign',
							"email" => $staff[0]->email
						);
						$email_r = $this->signEmailNotitfy($signData);
						//更新發送Email簽核通知時間
						if ($email_r['status']) {
							$this->form_sign_model->update_proxyEmailTime_by_id($signId);
						}
					}
				}
			} else { //通知業主
				$clientId = $formSign[0]->settingPersonId;
				$client = $this->client_model->get_client_by_id($clientId);
				if ($client[0]->email) { //顧客代表
					$signData = array(
						"formNo" => $formNo,
						"subjectType" => 'sign_client',
						"email" => $client[0]->email
					);
					$email_r = $this->signEmailNotitfy($signData);
					//更新發送Email時間
					if ($email_r['status']) {
						$this->form_sign_model->update_emailTime_by_id($signId);
					}
				}
			}
		}

		if ($r) {
			$result = array(
				"status" => true,
				"message" => '簽核成功'
			);
		} else {
			$result = array(
				"status" => false,
				"message" => '簽核失敗'
			);
		}
		return $result;
	}

	//刪除工單標註資料
	public function delFormRemark($id)
	{
		$r = $this->form_remark_model->del_remark_by_id($id);
		if ($r) {
			$result = array(
				"status" => true,
				"message" => '刪除成功'
			);
		} else {
			$result = array(
				"status" => false,
				"message" => '刪除失敗'
			);
		}
		return $result;
	}

	//修改工單標註資料
	public function editFormRemark($id)
	{
		$r = $this->form_remark_model->update_remark_by_id($id);
		if ($r) {
			$result = array(
				"status" => true,
				"message" => '儲存成功'
			);
		} else {
			$result = array(
				"status" => false,
				"message" => '儲存失敗'
			);
		}
		return $result;
	}
}
