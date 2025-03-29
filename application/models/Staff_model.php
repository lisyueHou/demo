<?php
class Staff_model extends CI_Model
{
	public $id = '';
	public $staffNo = '';
	public $name = '';
	public $depId = '';
	public $position = '';
	public $phone = '';
	public $email = '';
	public $remark = '';

	public function __construct()
	{
		$this->load->database();
	}

	// 查詢員工資料 by id
	public function get_staff_by_id($id)
	{
		$sql = "SELECT * FROM `staff` WHERE id = ? AND isDeleted = 0;";
		$query = $this->db->query($sql, array($id));
		$result = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new Staff_model();
				$obj->id = $row->id;
				$obj->staffNo = $row->staffNo;
				$obj->name = $row->name;
				$obj->depId = $row->depId;
				$obj->position = $row->position;
				$obj->phone = $row->phone;
				$obj->email = $row->email;
				$obj->remark = $row->remark;
				array_push($result, $obj);
			}
		}
		return $result;
	}

	//取得員工資料
	public function get_staff($data)
	{
		$data['page'] = (int)$data['page'];
		$data['pageCount'] = (int)$data['pageCount'];
		$pageStart = ($data['page'] - 1) * $data['pageCount']; //本頁起始紀錄筆數

		$all_sql = array(
			"select" => "SELECT `staff`.*,`department`.`name` as depName FROM `staff` 
            LEFT JOIN `department` ON `staff`.depId=`department`.id 
            WHERE ",
			"where_depId" => "depId = ? AND ",
			"where_staffNo" => "staffNo LIKE ? AND ",
			"where_name" => "`staff`.`name` LIKE ? AND ",
			"isDelete" => "`staff`.isDeleted= 0 ORDER BY `staff`.id DESC ",
			"LIMIT" => "LIMIT ?,?;"
		);

		$sql = $all_sql['select'];
		$sql_array = array();
		if (($data['depId'])) {  // 部門id有值
			$sql = $sql . $all_sql['where_depId'];
			array_push($sql_array, $data['depId']);
		}
		if (($data['staffNo'])) {  // 員工編號有值
			$sql = $sql . $all_sql['where_staffNo'];
			array_push($sql_array, '%' . $data['staffNo'] . '%');
		}
		if (($data['name'])) {  // 員工姓名有值
			$sql = $sql . $all_sql['where_name'];
			array_push($sql_array, '%' . $data['name'] . '%');
		}

		// 加上JOIN及isDelete篩選條件
		$sql = $sql . $all_sql['isDelete'];

		// 取得總數
		$query = $this->db->query($sql, $sql_array);
		$result['totalCount'] = $query->num_rows();
		$total_page = $result['totalCount'] / $data['pageCount'];
		$result['page'] = $data['page'];
		$result['pageCount'] = $data['pageCount'];
		$result['totalPage'] = ceil($total_page);

		// 取得限制筆數資料
		$sql = $sql . $all_sql['LIMIT'];
		array_push($sql_array, $pageStart);
		array_push($sql_array, $data['pageCount']);
		$query = $this->db->query($sql, $sql_array);
		$result['staff'] = array();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new Staff_model();
				$obj->id = $row->id;
				$obj->staffNo = $row->staffNo;
				$obj->name = $row->name;
				$obj->depId = $row->depId;
				$obj->depName = $row->depName;
				$obj->position = $row->position;
				$obj->phone = $row->phone;
				$obj->email = $row->email;
				$obj->remark = $row->remark;
				array_push($result['staff'], $obj);
			}
		}
		return $result;
	}

	//檢查員工編號是否重複
	public function check_staffNo($staffNo)
	{
		$sql = "SELECT * FROM `staff` WHERE staffNo=? AND isDeleted= 0;";
		$query = $this->db->query($sql, array($staffNo));
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	//檢查員工編號是否重複(修改)
	public function check_staffNo_by_id($staffNo, $id)
	{
		$sql = "SELECT * FROM `staff` WHERE staffNo=? AND id <> ? AND isDeleted= 0;";
		$query = $this->db->query($sql, array($staffNo, $id));
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	//新增員工資料
	public function add_staff($data)
	{
		$sql = "INSERT INTO `staff` (staffNo, `name`, depId, position, phone, email, remark) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$query = $this->db->query($sql, array($data['staffNo'], $data['name'], $data['depId'], $data['position'], $data['phone'], $data['email'], $data['remark']));
		return $query;
	}

	//修改員工資料
	public function update_staff($data)
	{
		$sql = "UPDATE `staff` SET staffNo = ?,`name` = ?,depId = ?,position = ?,phone = ?,email = ?,remark = ? WHERE id = ?;";
		$query = $this->db->query($sql, array($data['staffNo'], $data['name'], $data['depId'], $data['position'], $data['phone'], $data['email'], $data['remark'], $data['id']));
		return $query;
	}

	//刪除員工資料
	public function del_staff($data)
	{
		$sql = "UPDATE `staff` SET isDeleted = 1,deleteTime = NOW() WHERE id = ?;";
		$query = $this->db->query($sql, array($data['id']));
		return $query;
	}

	// 取得員工資料 ->帳號維護選單使用
	public function get_staff_list()
	{
		$sql = "SELECT id,staffNo,`name` FROM `staff` WHERE isDeleted = 0;";
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new Staff_model();
				$obj->id = $row->id;
				$obj->userNo = $row->staffNo;
				$obj->userName = $row->name;
				array_push($result, $obj);
			}
		}
		return $result;
	}

	// 取得員工資料 by id ->帳號維護選單使用
	public function get_staff_list_by_id($id)
	{
		$sql = "SELECT * FROM `staff` WHERE id=? AND isDeleted = 0;";
		$query = $this->db->query($sql, array($id));
		$result = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new Staff_model();
				$obj->id = $row->id;
				$obj->userNo = $row->staffNo;
				$obj->userName = $row->name;
				array_push($result, $obj);
			}
		}
		return $result;
	}

	// 取得各部門人員清單資料 by depId
	public function get_staff_by_depId($depId)
	{
		$sql = "SELECT * FROM `staff` WHERE depId = ? AND isDeleted = 0;";
		$query = $this->db->query($sql, array($depId));
		$result = array();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$obj = new Staff_model();
				$obj->id = $row->id;
				$obj->staffNo = $row->staffNo;
				$obj->name = $row->name;
				$obj->depId = $row->depId;
				$obj->position = $row->position;
				$obj->phone = $row->phone;
				$obj->email = $row->email;
				$obj->remark = $row->remark;
				array_push($result, $obj);
			}
		}
		return $result;
	}
}
