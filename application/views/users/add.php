<div class="pageTitle">帳號維護-新增帳號</div>

<div class="pageContent users_add">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>帳號：</label>
            <input type="text" id="account" maxlength="20" placeholder="至少輸入4個英文或數字" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>密碼：</label>
            <input type="password" id="password" maxlength="50" placeholder="至少輸入4個英文或數字" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>確認密碼：</label>
            <input type="password" id="passwordCheck" maxlength="50" placeholder="至少輸入4個英文或數字" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>帳號狀態：</label>
            <input type="radio" class="width_20px" name="enable" id="enableOpen" value="1" checked><label for="enableOpen">啟用</label>
            <input type="radio" class="width_20px" name="enable" id="enableClose" value="0"><label for="enableClose">關閉</label>
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>群組：</label>
            <select id="groupId">
                <option value="0">請選擇群組</option>
                <?php
                foreach ($groups as $row) {
                    switch ($row->class) {
                        case 1:
                            $className = '內部群組';
                            break;
                        case 2:
                            $className = '顧客群組';
                            break;
                        default:
                            $className = '-';
                            break;
                    }
                    echo '<option value="' . $row->id . '">' . $row->name . '[' . $className . ']</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>使用人員：</label>
            <select id="personId">
                <option value="0">請選擇使用人員</option>
            </select>
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/users.js'></script>
<script>
    //取得使用者選單
    $('#groupId').change(function() {
        var groupId = document.getElementById('groupId').value;
        getUserList(groupId);
    });
</script>