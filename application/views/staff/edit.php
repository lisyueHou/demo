<input type="hidden" id="id" value="<?php echo $data->id; ?>">
<div class="pageTitle">員工資料維護-編輯員工資料</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>員工編號：</label>
            <input type="text" id="staffNo" maxlength="20" value="<?php echo $data->staffNo; ?>" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>員工姓名：</label>
            <input type="text" id="name" maxlength="20" value="<?php echo $data->name; ?>" required="required">
        </div>
        <div class="col-md-6 col-lg-4">
            <label><span class="alertMsg">*</span>部門單位：</label>
            <select id="depId">
                <?php
                foreach ($department as $row) {
                    $dep_selected = '';
                    if ($row->id == $data->depId) {
                        $dep_selected = 'selected';
                    }
                    echo '<option value="' . $row->id . '" ' . $dep_selected . '>' . $row->name . '[' . $row->depNo . ']</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-4">
            <label>職稱：</label>
            <input type="text" id="position" maxlength="20" value="<?php echo $data->position; ?>">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>聯絡電話：</label>
            <input type="text" id="phone" maxlength="50" value="<?php echo $data->phone; ?>">
        </div>
        <div class="col-md-12">
            <label>E-mail：</label>
            <input type="email" class="width_400px" id="email" maxlength="100" value="<?php echo $data->email; ?>">
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/staff.js'></script>