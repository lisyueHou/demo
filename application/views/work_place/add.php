<div class="pageTitle">作業區域維護-新增作業區域</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>作業區域：</label>
            <select id="areaId">
                <?php
                echo '<option value="0">請選擇作業區域</option>';
                foreach ($area as $row) {
                    echo '<option value="' . $row->id . '">' . $row->name . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>顧客公司名稱：</label>
            <select id="clientId" class="width_180px">
                <?php
                echo '<option value="0">請選擇顧客公司名稱</option>';
                foreach ($client as $row) {
                    echo '<option value="' . $row->id . '">' . $row->company . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>作業地點：</label>
            <input type="text" id="workPlace" maxlength="20" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>作業地點緯度：</label>
            <input type="number" id="latitude" max="90" min="-90">度
        </div>
        <div class="col-md-6 col-lg-4">
            <label>作業地點經度：</label>
            <input type="number" id="longitude" max="180" min="-180">度
        </div>
        <div class="col-md-6 col-lg-4">
            <label>管線圖片：</label>
            <input type="file" id="cadImg">
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_place.js'></script>