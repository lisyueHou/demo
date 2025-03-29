<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'controllers/BaseController.php';

class Work_form extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'html'));
		$this->load->library(array('form_validation', 'session'));
		$this->load->service("work_form_service");

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

	//工單報表管理首頁
	public function index()
	{
		//判斷登入帳號是否為顧客，若是則加上篩選條件(顧客只能看到自己的工單)
		$userId = $this->session->user_info->id; //帳號id
		$clientId = $this->work_form_service->checkLoginAcc($userId);

		//載入頁面預設資料
		if ($this->input->post("searchData")) {
			//有搜尋條件時代入資料
			$searchData = $this->input->post("searchData");
			$searchData_obj = json_decode($searchData);
			$data = array(
				"page" => $searchData_obj->page,
				"pageCount" => $searchData_obj->pageCount,
				"formNo" => $searchData_obj->formNo,
				"projectNo" => $searchData_obj->projectNo,
				"checkDate" => $searchData_obj->checkDate,
				"projectName" => $searchData_obj->projectName,
				"contractor" => $searchData_obj->contractor,
				"company" => $searchData_obj->company,
				"clientId" => $clientId
			);
		} else {
			$data = array(
				"page" => 1,
				"pageCount" => 10,
				"formNo" => "",
				"projectNo" => "",
				"checkDate" => "",
				"projectName" => "",
				"contractor" => "",
				"company" => "",
				"clientId" => $clientId
			);
		}

		$result = $this->work_form_service->getWorkform($data);
		$result['searchData'] = $data;
		$result['personId'] = $this->session->user_info->personId;

		$this->load->view('header');
		$this->load->view('work_form/index', $result);
		$this->load->view('work_form/note');
		$this->load->view('footer');
	}

	//新增頁面
	public function add()
	{
		$result['searchData'] = $this->input->post("searchData");

		//選單資料
		$select_r = $this->work_form_service->getSelectData();
		$result['robot'] = $select_r['robot'];
		$result['client'] = $select_r['client'];
		$result['startTime'] = date('Y-m-d H:i');

		$this->load->view('header');
		$this->load->view('work_form/add', $result);
		$this->load->view('footer');
	}

	//編輯頁面
	public function edit()
	{
		$result['searchData'] = $this->input->post("searchData");

		//重新取得工單報表
		$data_obj = json_decode($this->input->post("data"));
		$newData = $this->work_form_service->getWorkformById($data_obj->id);
		$result['data'] = $newData['data'][0];

		//選單資料
		$select_r = $this->work_form_service->getSelectData();
		$result['robot'] = $select_r['robot'];
		$result['client'] = $select_r['client'];

		//取得作業地點選單
		if ($result['data']->clientId) {
			$data = array(
				"clientId" => $result['data']->clientId
			);
			$work_place_r = $this->work_form_service->getWorkPlaceList($data);
			$result['work_place'] = $work_place_r['data'];
		} else {
			$result['work_place'] = NULL;
		}

		//取得工單報表標記備註資料
		$result['formRemark'] = array();
		$formRemark = $this->work_form_service->getFormRemarkByformNo($data_obj->formNo);
		if (count($formRemark['data']) > 0) {
			$result['formRemark'] = $formRemark['data'];
		}

		$this->load->view('header');
		$this->load->view('work_form/edit', $result);
		$this->load->view('work_form/editNote');
		$this->load->view('footer');
	}

	// 檢視QR Codr
	public function viewQrcode($formNo)
	{
		//取得工單id
		$newData = $this->work_form_service->getWorkformByFormNo($formNo);
		$data = array(
			'id' => $newData['data'][0]->id,
			'formNo' => $formNo
		);
		$this->load->view('work_form/qrcode', $data);
	}

	// 檢視工單詳細資料
	public function viewDetail()
	{
		$result['searchData'] = $this->input->post("searchData");

		//取得工單報表
		$data_obj = json_decode($this->input->post("data"));
		$newData = $this->work_form_service->getWorkformById($data_obj->id);
		$result['data'] = $newData['data'][0];

		//取得工單報表標記備註資料
		$result['formRemark'] = array();
		$formRemark = $this->work_form_service->getFormRemarkByformNo($data_obj->formNo);
		if (count($formRemark['data']) > 0) {
			$result['formRemark'] = $formRemark['data'];
		}

		//取得表單簽核資料
		$formSign = $this->work_form_service->getFormSignByformNo($data_obj->formNo);
		$result['formSign'] = $formSign['data'];

		$this->load->view('header');
		$this->load->view('work_form/detail', $result);
		$this->load->view('footer');
	}

	// 列印工單
	public function printPDF($formId)
	{
		//取得工單報表
		$newData = $this->work_form_service->getWorkformById($formId);
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

		$this->load->view('work_form/printPDF', $result);
	}

	//簽核設定頁面
	public function signSet()
	{
		$result['searchData'] = $this->input->post("searchData");

		//重新取得工單報表
		$data_obj = json_decode($this->input->post("data"));
		$newData = $this->work_form_service->getWorkformById($data_obj->id);
		$result['data'] = $newData['data'][0];

		//取得各部門人員清單資料 & 簽核人員資料
		$formDepStaff = $this->work_form_service->getFormDepStaff($data_obj->formNo);
		$result['formDepStaff'] = $formDepStaff;

		$this->load->view('header');
		$this->load->view('work_form/signSet', $result);
		$this->load->view('footer');
	}

	// 簽核頁面
	public function formSign()
	{
		$result['searchData'] = $this->input->post("searchData");
		$result['signData'] = $this->input->post("signData");

		//取得工單報表
		$data_obj = json_decode($result['signData']);
		$newData = $this->work_form_service->getWorkformById($data_obj->id);
		$result['data'] = $newData['data'][0];

		//取得工單報表標記備註資料
		$result['formRemark'] = array();
		$formRemark = $this->work_form_service->getFormRemarkByformNo($data_obj->formNo);
		if (count($formRemark['data']) > 0) {
			$result['formRemark'] = $formRemark['data'];
		}

		//取得表單簽核資料
		$formSign = $this->work_form_service->getFormSignByformNo($data_obj->formNo);
		$result['formSign'] = $formSign['data'];

		$this->load->view('header');
		$this->load->view('work_form/formSign', $result);
		$this->load->view('footer');
	}
}
