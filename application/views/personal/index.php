<?php $data_obj = $data[0]; ?>
<input type="hidden" id="id" value="<?php echo $data_obj->id; ?>">
<div class="pageTitle">個人帳號維護</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <label>帳號：</label><span><?php echo $data_obj->account; ?></span>
        </div>
        <div class="col-md-6 col-lg-3">
            <label>群組：</label><span><?php echo $data_obj->groupName; ?></span>
        </div>
        <div class="col-md-6 col-lg-3">
            <label>使用人員：</label>
            <span>
                <?php
                $userName = $this->session->user_info->user['userName']; //使用者名稱
                echo $userName; ?>
            </span>
        </div>
        <div class="col-md-6 col-lg-3">
            <label>密碼：</label>
            <button class="button modGreen" onclick="openPass(<?php echo $data_obj->id; ?>);">修改密碼</button>
        </div>
    </div>
    <div class="btnBox">
        <div class="alertMsg" id="errorMsg"></div>
        <button class='button' onclick='gotoHome();'>回首頁</button>
    </div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/personal.js'></script>
<script>
    //取得使用者選單
    $('#groupId').change(function() {
        var groupId = document.getElementById('groupId').value;
        getUserList(groupId);
    });
</script>