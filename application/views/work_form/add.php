<div class="pageTitle">工單報表管理-新增工單報表</div>

<div class="pageContent work_form">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-12 col-xl-4">
            <label><span class="alertMsg">*</span>設備名稱：</label>
            <select id="robotNo">
                <option value="0">請選擇設備</option>
                <?php
                foreach ($robot as $row) {
                    echo '<option value="' . $row->robotNo . '">' . $row->name . '[' . $row->robotNo . ']</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-xl-4">
            <label><span class="alertMsg">*</span>作業開始時間：</label>
            <input type="datetime-local" id="startTime" value="<?php echo $startTime; ?>" required="required">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>作業結束時間：</label>
            <input type="datetime-local" id="finishTime">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>業主：</label>
            <select id="clientId">
                <option value="0">請選擇業主</option>
                <?php
                foreach ($client as $row) {
                    echo '<option value="' . $row->id . '">' . $row->company . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>工作位置：</label>
            <select id="workPlaceId">
                <option value="0">請選擇工作位置</option>
            </select>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>協力廠商：</label>
            <input type="text" id="contractor" maxlength="30">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>專案編號：</label>
            <input type="text" id="projectNo" maxlength="50">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>工程專案名稱：</label>
            <input type="text" id="projectName" maxlength="50">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>分項工程名稱：</label>
            <input type="text" id="subProjectName" maxlength="50">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>檢查日期：</label>
            <input type="date" id="checkDate">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>管線編號：</label>
            <input type="text" id="pipingLineNo" maxlength="50">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>段數編號：</label>
            <input type="text" id="segmentsNo" maxlength="50">
        </div>
        <div class="col-md-12">
            <label>備註：</label>
            <textarea id="remark" maxlength="300"></textarea>
        </div>
    </div>
    <div class="btnBox">
        <div class="alertMsg" id="errorMsg"></div>
        <button class='button' onclick='saveAdd();'>新增</button>
        <button class='button removeRed' onclick='gotoHome(<?php echo $searchData; ?>);'>取消</button>
    </div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_form.js'></script>
<script>
    //顧客選單觸發作業地點選單
    $('#clientId').change(function() {
        var clientId = document.getElementById('clientId').value;
        getWorkPlaceList(clientId);
    });
</script>