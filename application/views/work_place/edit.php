<?php $data_obj = json_decode($data);?>
<input type="hidden" id="id" value="<?php echo $data_obj->id;?>">
<div class="pageTitle">作業區域維護-編輯作業區域</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>作業區域：</label>
            <select id="areaId">
                <?php
                foreach ($area as $row) {
                    $area_selected = '';
                    if ($row->id == $data_obj->areaId) {
                        $area_selected = 'selected';
                    }
                    echo '<option value="' . $row->id . '" ' . $area_selected . '>' . $row->name . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>顧客公司名稱：</label>
            <select id="clientId" class="width_180px">
                <?php
                foreach ($client as $row) {
                    $client_selected = '';
                    if ($row->id == $data_obj->clientId) {
                        $client_selected = 'selected';
                    }
                    echo '<option value="' . $row->id . '" ' . $client_selected . '>' . $row->company . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>作業地點：</label>
            <input type="text" id="workPlace" maxlength="20" value="<?php echo $data_obj->workPlace; ?>" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>作業地點緯度：</label>
            <input type="number" id="latitude" max="90" min="-90" value="<?php if ($data_obj->latitude != 'null') echo $data_obj->latitude; ?>">度
        </div>
        <div class="col-md-6 col-lg-4">
            <label>作業地點經度：</label>
            <input type="number" id="longitude" max="180" min="-180" value="<?php if ($data_obj->longitude != 'null') echo $data_obj->longitude; ?>">度
        </div>
        <div class="col-md-6 col-lg-4">
            <label>管線圖片：</label>
            <?php
            if ($data_obj->cadImg) {
                $delImgBtn = "<button class='button removeRed' onclick='delCadImg(" . $data . "," . $searchData . ");'>刪除圖片</button>";
                echo '<a href="' . $data_obj->imgPath . '" target="_blank"><img src="' . $data_obj->imgPath . '" class="img"></a>' . $delImgBtn;
                echo '<input type="hidden" id="cadImg">';
                echo '<input type="hidden" id="cadImgName" value="'.$data_obj->cadImg.'">';
            } else {
                echo '<input type="file" id="cadImg">';
                echo '<input type="hidden" id="cadImgName">';
            }
            ?>
        </div>
        <div class="col-md-12">
            <label>備註：</label>
            <textarea id="remark" maxlength="300"><?php echo $data_obj->remark; ?></textarea>
        </div>
    </div>
    <div class="btnBox">
        <div class="alertMsg" id="errorMsg"></div>
        <button class='button' onclick='saveEdit(<?php echo $searchData; ?>);'>儲存</button>
        <button class='button removeRed' onclick='gotoHome(<?php echo $searchData; ?>);'>取消</button>
    </div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_place.js'></script>