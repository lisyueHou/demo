<input type="hidden" id="id" value="<?php echo $data->id; ?>">
<div class="pageTitle">常用標記備註-編輯標記備註</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-12">
            <label><span class="alertMsg">*</span>標記備註內容：</label>
            <input id="content" maxlength="100" class="width_400px" value="<?php echo $data->content; ?>" required="required">
        </div>
    </div>
    <div class="btnBox">
        <div class="alertMsg" id="errorMsg"></div>
        <button class='button' onclick='saveEdit(<?php echo $searchData; ?>);'>儲存</button>
        <button class='button removeRed' onclick='gotoHome(<?php echo $searchData; ?>);'>取消</button>
    </div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/remark.js'></script>