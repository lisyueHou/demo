<input type="hidden" id="id" value="<?php echo $data->id; ?>">
<div class="pageTitle">設備資料維護-編輯設備資料</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>設備編號：</label>
            <input id="robotNo" maxlength="20" value="<?php echo $data->robotNo; ?>" disabled="disabled">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>設備名稱：</label>
            <input id="name" maxlength="20" value="<?php echo $data->name; ?>" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>設備狀態：</label>
            <?php
            $openCheck = '';
            $closeCheck = '';
            if ($data->state == 0) {
                $openCheck = "checked";
            } else {
                $closeCheck = "checked";
            }
            ?>
            <input type="radio" class="width_20px" name="state" id="stateOpen" value="0" <?php echo $openCheck; ?>><label for="stateOpen">啟用</label>
            <input type="radio" class="width_20px" name="state" id="stateClose" value="1" <?php echo $closeCheck; ?>><label for="stateClose">關閉</label>
        </div>
        <div class="col-md-12">
            <label>串流影像網址：</label>
            <input type="url" class="width_400px" id="videoUrl" maxlength="255" value="<?php echo $data->videoUrl; ?>">
        </div>
        <div class="col-md-12">
            <label>備註：</label>
            <textarea id="remark" maxlength="300"><?php echo $data->remark; ?></textarea>
        </div>
    </div>
    <div class="btnBox">
        <div class="alertMsg" id="errorMsg"></div>
        <button class='button' onclick='saveEdit(<?php echo $searchData; ?>);'>儲存</button>
        <button class='button removeRed' onclick='gotoHome(<?php echo $searchData; ?>);'>取消</button>
    </div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/robot.js'></script>