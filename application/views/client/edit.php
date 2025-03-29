<input type="hidden" id="id" value="<?php echo $data->id; ?>">
<div class="pageTitle">顧客資料維護-編輯顧客資料</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>顧客編號：</label>
            <input type="text" id="clientNo" maxlength="20" value="<?php echo $data->clientNo; ?>" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>公司名稱：</label>
            <input type="text" id="company" maxlength="30" value="<?php echo $data->company; ?>" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>統一編號：</label>
            <input type="text" id="companyId" maxlength="10" value="<?php echo $data->companyId; ?>">
        </div>
        <div class="col-md-12">
            <label>公司地址：</label>
            <input type="text" id="address" class="width_400px" maxlength="100" value="<?php echo $data->address; ?>">
        </div>
        <br><br>
        <div><span class="conTitle">聯絡資訊</span></div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <label>顧客代表：</label>
            <input type="text" id="name" maxlength="20" value="<?php echo $data->name; ?>">
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <label>聯絡電話：</label>
            <input type="text" id="phone" maxlength="50" value="<?php echo $data->phone; ?>">
        </div>
        <div class="col-md-12 col-xl-6">
            <label>E-mail：</label>
            <input type="email" class="width_400px" id="email" maxlength="100" value="<?php echo $data->email; ?>">
        </div>
        <br><br>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <label>聯絡人：</label>
            <input type="text" id="conName" maxlength="20" value="<?php echo $data->conName; ?>">
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <label>聯絡電話：</label>
            <input type="text" id="conPhone" maxlength="50" value="<?php echo $data->conPhone; ?>">
        </div>
        <div class="col-md-12 col-xl-6">
            <label>E-mail：</label>
            <input type="email" class="width_400px" id="conEmail" maxlength="100" value="<?php echo $data->conEmail; ?>">
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/client.js'></script>