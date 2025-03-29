<?php
class Work_place_service extends MY_Service
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('work_place_model');
		$this->load->model('area_model');
		$this->load->model('client_model');
	}

	//取得作業地點
	public function getWorkplace($data)
	{
		$r = $this->work_place_model->get_workplace($data);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//更新CAD圖路徑
	public function updateCadImg($data)
	{
		$r = $this->work_place_model->update_cadImg($data);
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

	//取得選單資料
	public function getSelectData()
	{
		$area = $this->area_model->get_area(); // 取得作業區域資料
		$client = $this->client_model->get_client(); // 取得顧客資料
		$result = array(
			'area' => $area,
			'client' => $client
		);
		return $result;
	}

	//新增作業區域資料
	public function addWorkPlace($data)
	{
		//圖片上傳
		$data['cadImgName'] = null;
		$file_r = true;
		if (!empty($_FILES['cadImg']['name'])) {
			$filePath = CADIMG_PATH;
			if (!is_dir($filePath)) {
				mkdir($filePath);
			}
			//重新命名
			$newName = $this->_guidv4();

			$_FILES['file']['name'] = $_FILES['cadImg']['name'];
			$_FILES['file']['type'] = $_FILES['cadImg']['type'];
			$_FILES['file']['tmp_name'] = $_FILES['cadImg']['tmp_name'];
			$_FILES['file']['error'] = $_FILES['cadImg']['error'];
			$_FILES['file']['size'] = $_FILES['cadImg']['size'];

			$config['upload_path'] = $filePath;
			$config['allowed_types'] = 'jpg|jpeg|png';
			$config['file_name'] = $newName;
			$this->load->library('upload', $config);

			//執行上傳
			$this->upload->initialize($config);
			if ($this->upload->do_upload('file')) {
				$uploadData = $this->upload->data();
				$fileName = $uploadData['file_name']; //儲存檔名
				$fullPath = $uploadData['full_path']; //儲存路徑
				$data['cadImgName'] = $fileName;

				$this->_resizeImage($fullPath, $fullPath); //變更圖片大小
			} else {
				$file_r = false;
			}
		}

		if ($file_r) {
			$r = $this->work_place_model->add_workplace($data);
			if ($r) {
				$result = array(
					"status" => true,
					"message" => "新增成功"
				);
			} else {
				$result = array(
					"status" => false,
					"message" => "新增失敗"
				);
			}
		} else {
			$result = array(
				"status" => false,
				"message" => "新增失敗"
			);
		}

		return $result;
	}

	//自動產生GUID
	function _guidv4($data = null)
	{
		// Generate 16 bytes (128 bits) of random data or use the data passed into the function.
		$data = $data ?? random_bytes(16);
		assert(strlen($data) == 16);

		// Set version to 0100
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		// Set bits 6-7 to 10
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		// Output the 36 character UUID.
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

	//變更圖片大小
	function _resizeImage($srcFile, $dstFile)
	{
		$towidth = CADIMG_WIDTH;
		$toheight = CADIMG_HEIGHT;
		$quality = 80;
		$data = @GetImageSize($srcFile);
		switch ($data['2']) {
			case 1:
				$im = imagecreatefromgif($srcFile);
				break;
			case 2:
				$im = imagecreatefromjpeg($srcFile);
				break;
			case 3:
				$im = imagecreatefrompng($srcFile);
				break;
			case 6:
				$im = ImageCreateFromBMP($srcFile);
				break;
		}
		$srcW = @ImageSX($im);
		$srcH = @ImageSY($im);
		if ($toheight / $srcW > $towidth / $srcH) {
			$b = $toheight / $srcH;
		} else {
			$b = $towidth / $srcW;
		}
		$new_w = floor($srcW * $b);
		$new_h = floor($srcH * $b);
		$dstX = $new_w;
		$dstY = $new_h;
		$ni = @imageCreateTrueColor($dstX, $dstY);
		@ImageCopyResampled($ni, $im, 0, 0, 0, 0, $dstX, $dstY, $srcW, $srcH);
		@ImageJpeg($ni, $dstFile, $quality);
		@imagedestroy($im);
		@imagedestroy($ni);
	}

	//刪除管線圖
	public function delCadImg($data)
	{
		//刪除圖片
		$imgFilePath = CADIMG_PATH . $data['cadImg'];
		if (file_exists($imgFilePath)) {
			unlink($imgFilePath);
		}

		//刪除圖片資料庫資料
		$r = $this->work_place_model->del_cadImg($data);
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

	//取得作業地點 by id
	public function getWorkplaceById($id)
	{
		$r = $this->work_place_model->get_workplace_by_id($id);
		$result = array(
			"status" => true,
			"data" => $r
		);
		return $result;
	}

	//編輯作業區域資料
	public function editWorkPlace($data)
	{
		//圖片上傳
		// $data['cadImgName'] = null;
		$file_r = true;
		if (!empty($_FILES['cadImg']['name'])) {
			$filePath = CADIMG_PATH;
			if (!is_dir($filePath)) {
				mkdir($filePath);
			}
			//重新命名
			$newName = $this->guidv4();

			$_FILES['file']['name'] = $_FILES['cadImg']['name'];
			$_FILES['file']['type'] = $_FILES['cadImg']['type'];
			$_FILES['file']['tmp_name'] = $_FILES['cadImg']['tmp_name'];
			$_FILES['file']['error'] = $_FILES['cadImg']['error'];
			$_FILES['file']['size'] = $_FILES['cadImg']['size'];

			$config['upload_path'] = $filePath;
			$config['allowed_types'] = 'jpg|jpeg|png';
			$config['file_name'] = $newName;
			$this->load->library('upload', $config);

			//執行上傳
			$this->upload->initialize($config);
			if ($this->upload->do_upload('file')) {
				$uploadData = $this->upload->data();
				$fileName = $uploadData['file_name']; //儲存檔名
				$fullPath = $uploadData['full_path']; //儲存路徑
				$data['cadImgName'] = $fileName;

				$this->resizeImage($fullPath, $fullPath); //變更圖片大小
			} else {
				$file_r = false;
			}
		}

		if ($file_r) {
			$r = $this->work_place_model->update_workplace($data);
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
		} else {
			$result = array(
				"status" => false,
				"message" => "儲存失敗"
			);
		}

		return $result;
	}

	//刪除作業區域資料
	public function delWorkPlace($data)
	{
		$r = $this->work_place_model->del_workplace($data);
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
}
