<input type="hidden" id="id" value="<?php echo $data->id; ?>">
<div class="pageTitle">帳號維護-編輯帳號</div>

<div class="pageContent users_edit">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>帳號：</label>
            <input type="text" id="account" maxlength="20" placeholder="至少輸入4個英文或數字" value="<?php echo $data->account; ?>" required="required">
            <input type="hidden" id="oldAccount" value="<?php echo $data->account; ?>">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>密碼：</label>
            <button class="button modGreen" onclick="openPass(<?php echo $data->id; ?>);">修改密碼</button>
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>帳號狀態：</label>
            <?php
            $loginAccount = $this->session->user_info->account; //登入帳號
            $disabled = '';
            if ($data->account == $loginAccount) {
                $disabled = ' disabled';
            }
            $enableOpen = '';
            $enableClose = '';
            if ($data->isEnable == 0) {
                $enableClose = 'checked';
            } else {
                $enableOpen = 'checked';
            }
            echo '<input type="radio" class="width_20px" name="enable" id="enableOpen" value="1" ' . $enableOpen . $disabled . '><label for="enableOpen">啟用</label>';
            echo '<input type="radio" class="width_20px" name="enable" id="enableClose" value="0" ' . $enableClose . $disabled . '><label for="enableClose">關閉</label>';
            ?>
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
                    $groupChecked = '';
                    if ($data->groupId == $row->id) {
                        $groupChecked = 'selected';
                    }
                    echo '<option value="' . $row->id . '" ' . $groupChecked . '>' . $row->name . '[' . $className . ']</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-8">
            <label><span class="alertMsg">*</span>使用人員：</label>
            <select id="personId">
                <?php
                $userList = $data->userList;
                print_r($userList);
                foreach ($userList as $row) {
                    $userChecked = '';
                    if ($row->id == $data->personId) {
                        $userChecked = 'selected';
                    }
                    echo '<option value="' . $row->id . '" ' . $userChecked . '>' . $row->userName . '[' . $row->userNo . ']</option>';
                }
                ?>
            </select>
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/users.js'></script>
<script>
    //取得使用者選單
    $('#groupId').change(function() {
        var groupId = document.getElementById('groupId').value;
        getUserList(groupId);
    });
</script>