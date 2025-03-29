<?php
class Form_sign_model extends CI_Model
{
	// 連接資料庫
	public function __construct()
	{
		$this->load->database();
	}

	//取得工單簽核資料 by formNo
	public function get_sign_by_formNo($formNo)
	{
		$sql = "SELECT * FROM `form_sign` WHERE formNo=? AND isDeleted= 0 ORDER BY signSort;";
		$query = $this->db->query($sql, array($formNo));
		$result = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new Form_sign_model();
				$obj->id = $row->id;
				$obj->formNo = $row->formNo;
				$obj->signSort = $row->signSort;
				$obj->settingPersonId = $row->settingPersonId;
				$obj->proxyPersonId = $row->proxyPersonId;
				$obj->signPersonId = $row->signPersonId;
				$obj->signTime = $row->signTime;
				$obj->emailTime = $row->emailTime;
				$obj->emailCancelTime = $row->emailCancelTime;
				$obj->proxyEmailTime = $row->proxyEmailTime;
				$obj->proxyEmailCancelTime = $row->proxyEmailCancelTime;
				array_push($result, $obj);
			}
		}
		return $result;
	}

	//新增簽核資料
	public function add_formsign($data)
	{
		$sql = "INSERT INTO `form_sign` (formNo, signSort, settingPersonId,proxyPersonId,signPersonId,signTime,emailTime,emailCancelTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
		$this->db->query($sql, array($data['formNo'], $data['signSort'], $data['settingPersonId'], $data['proxyPersonId'], NULL, NULL, NULL, NULL));
		$result = $this->db->insert_id();
		return $result;
	}

	//取得工單特定一筆簽核資料
	public function get_sign_by_formNo_signSort($formNo, $signSort)
	{
		$sql = "SELECT * FROM `form_sign` WHERE formNo=? AND signSort=? AND isDeleted= 0;";
		$query = $this->db->query($sql, array($formNo, $signSort));
		$result = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new Form_sign_model();
				$obj->id = $row->id;
				$obj->formNo = $row->formNo;
				$obj->signSort = $row->signSort;
				$obj->settingPersonId = $row->settingPersonId;
				$obj->proxyPersonId = $row->proxyPersonId;
				$obj->signPersonId = $row->signPersonId;
				$obj->signTime = $row->signTime;
				$obj->emailTime = $row->emailTime;
				$obj->emailCancelTime = $row->emailCancelTime;
				$obj->proxyEmailTime = $row->proxyEmailTime;
				$obj->proxyEmailCancelTime = $row->proxyEmailCancelTime;
				array_push($result, $obj);
			}
		}
		return $result;
	}

	//更新發送Email簽核通知時間
	public function update_emailTime_by_id($id)
	{
		$sql = "UPDATE `form_sign` SET emailTime = NOW() WHERE id = ?;";
		$query = $this->db->query($sql, array($id));
		return $query;
	}

	//更新發送Email簽核取消通知時間
	public function update_emailCancelTime_by_id($id)
	{
		$sql = "UPDATE `form_sign` SET emailCancelTime = NOW() WHERE id = ?;";
		$query = $this->db->query($sql, array($id));
		return $query;
	}

	//更新代理人員發送Email簽核通知時間
	public function update_proxyEmailTime_by_id($id)
	{
		$sql = "UPDATE `form_sign` SET proxyEmailTime = NOW() WHERE id = ?;";
		$query = $this->db->query($sql, array($id));
		return $query;
	}

	//更新代理人員發送Email簽核取消通知時間
	public function update_proxyEmailCancelTime_by_id($id)
	{
		$sql = "UPDATE `form_sign` SET proxyEmailCancelTime = NOW() WHERE id = ?;";
		$query = $this->db->query($sql, array($id));
		return $query;
	}

	//修改簽核人員
	public function update_formsign($data)
	{
		$sql = "UPDATE `form_sign` SET settingPersonId = ?,proxyPersonId = ? WHERE formNo = ? AND signSort = ? AND isDeleted= 0;";
		$query = $this->db->query($sql, array($data['settingPersonId'], $data['proxyPersonId'], $data['formNo'], $data['signSort']));
		return $query;
	}

	//刪除簽核資料 by id
	public function del_sign_by_id($id)
	{
		$sql = "UPDATE `form_sign` SET isDeleted=1,deleteTime = NOW() WHERE id = ?;";
		$query = $this->db->query($sql, array($id));
		return $query;
	}

	//刪除簽核資料 by formNo
	public function del_sign_by_formNo($formNo)
	{
		$sql = "UPDATE `form_sign` SET isDeleted=1,deleteTime = NOW() WHERE formNo = ? AND isDeleted= 0;";
		$query = $this->db->query($sql, array($formNo));
		return $query;
	}

	//刪除內部人員的簽核資料 by formNo
	public function del_depsign_by_formNo($formNo)
	{
		$sql = "UPDATE `form_sign` SET isDeleted=1,deleteTime = NOW() WHERE formNo = ? AND signSort<>4 AND isDeleted= 0;";
		$query = $this->db->query($sql, array($formNo));
		return $query;
	}

	//取得已發送簽核通知但尚未簽核 by formNo
	public function get_notSignYetButNotify_by_formNo($formNo)
	{
		$sql = "SELECT * FROM `form_sign` WHERE formNo=? 
		AND signPersonId IS NULL 
		AND ((emailTime IS NOT NULL AND emailCancelTime IS NULL) OR (proxyEmailTime IS NOT NULL AND proxyEmailCancelTime IS NULL)) 
		AND isDeleted= 0;";
		$query = $this->db->query($sql, array($formNo));
		$result = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new Form_sign_model();
				$obj->id = $row->id;
				$obj->formNo = $row->formNo;
				$obj->signSort = $row->signSort;
				$obj->settingPersonId = $row->settingPersonId;
				$obj->proxyPersonId = $row->proxyPersonId;
				$obj->signPersonId = $row->signPersonId;
				$obj->signTime = $row->signTime;
				$obj->emailTime = $row->emailTime;
				$obj->emailCancelTime = $row->emailCancelTime;
				$obj->proxyEmailTime = $row->proxyEmailTime;
				$obj->proxyEmailCancelTime = $row->proxyEmailCancelTime;
				array_push($result, $obj);
			}
		}
		return $result;
	}

	//更新簽核人員
	public function update_signPerson($data)
	{
		$sql = "UPDATE `form_sign` SET signPersonId = ?,signTime = NOW() WHERE formNo = ? AND signSort=? AND isDeleted= 0;";
		$query = $this->db->query($sql, array($data['personId'], $data['formNo'], $data['signSort']));
		return $query;
	}
}
