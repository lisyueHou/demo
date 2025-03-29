<?php
class Work_form_file_model extends CI_Model
{
    // 連接資料庫
    public function __construct()
    {
        $this->load->database();
    }
    //新增作業檔案資料-API
    public function add_file($data)
    {
        $sql = "INSERT INTO `work_form_file` (formNo, fileType, `fileName`, mltContent) VALUES (?, ?, ?, ?);";
        $query = $this->db->query($sql, array($data['formNo'], $data['fileType'], $data['fileName'], $data['mltContent']));
        return $query;
    }

    //取得工單內容 by formNo
    public function get_file_by_formNo($formNo)
    {
        $sql = "SELECT * FROM `work_form_file` WHERE formNo=? AND isDeleted= 0;";
        $query = $this->db->query($sql, array($formNo));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Work_form_file_model();
                $obj->id = $row->id;
                $obj->formNo = $row->formNo;
                $obj->fileType = $row->fileType;
                $obj->fileName = $row->fileName;
                $obj->mltContent = $row->mltContent;
                array_push($result, $obj);
            }
        }
        return $result;
    }

    //刪除工單相關檔案 by formNo
    public function del_file_by_formNo($formNo)
    {
        $sql = "UPDATE `work_form_file` SET isDeleted = 1,deleteTime = NOW() WHERE formNo = ?;";
        $query = $this->db->query($sql, array($formNo));
        return $query;
    }

    //取得工單內容 by formNo (no mlt)
    public function get_file_by_formNo_notmlt($formNo)
    {
        $sql = "SELECT * FROM `work_form_file` WHERE formNo = ? AND fileType!='mlt' AND isDeleted= 0;";
        $query = $this->db->query($sql, array($formNo));
        $result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $obj = new Work_form_file_model();
                $obj->id = $row->id;
                $obj->formNo = $row->formNo;
                $obj->fileType = $row->fileType;
                $obj->fileName = $row->fileName;
                $obj->filePath = base_url() . 'appoint/uploads/' . $row->formNo .'/' . $row->fileName;
                array_push($result, $obj);
            }
        }
        return $result;
    }
}
