<div class="pageTitle">員工資料維護-新增員工資料</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>員工編號：</label>
            <input type="text" id="staffNo" maxlength="20" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>員工姓名：</label>
            <input type="text" id="name" maxlength="20" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>部門單位：</label>
            <select id="depId">
                <?php
                echo '<option value="0">請選擇部門單位</option>';
                foreach ($department as $row) {
                    echo '<option value="' . $row->id . '">' . $row->name . '[' . $row->depNo . ']</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-4">
            <label>職稱：</label>
            <input type="text" id="position" maxlength="20">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>聯絡電話：</label>
            <input type="text" id="phone" maxlength="50">
        </div>
        <div class="col-md-12">
            <label>E-mail：</label>
            <input type="email" class="width_400px" id="email" maxlength="100">
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/staff.js'></script>