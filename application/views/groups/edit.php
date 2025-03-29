<input type="hidden" id="id" value="<?php echo $group->id; ?>">
<div class="pageTitle">權限群組維護-編輯權限群組</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6">
            <label><span class="alertMsg">*</span>群組名稱：</label>
            <input type="text" id="name" class="width_240px" maxlength="30" value="<?php echo $group->name; ?>" required="required">
        </div>
        <div class="col-md-6">
            <label><span class="alertMsg">*</span>群組類別：</label>
            <select id="groupClass">
                <?php
                $staffClass = "";
                $clientClass = "";
                
                switch ($group->class) {
                    case 1:
                        $staffClass = "selected";
                        break;
                    case 2:
                        $clientClass = "selected";
                        break;
                    default:
                        echo '<option value="0">請選擇群組類別</option>';
                        break;
                }
                echo '<option value="1" ' . $staffClass . '>內部群組</option>';
                echo '<option value="2" ' . $clientClass . '>顧客群組</option>';
                ?>
            </select>
        </div>
        <div class="col-md-12">
            <label><span class="alertMsg">*</span>系統功能權限：</label>
            <div>
                <?php
                $cMainFunction = '';
                foreach ($auth as $row) {
                    if ($row->cMainFunction != $cMainFunction) {
                        if (!empty($cMainFunction)) {
                            echo '<br>';
                        }
                        echo '<div class="premTitle">' . $row->cMainFunction . '</div>';
                        $cMainFunction = $row->cMainFunction;
                    }
                    $defaultAuth = '';

                    //勾選權限
                    $disabled = '';
                    $checked = '';
                    if ($row->id == 1) {
                        $disabled = 'disabled';
                    }
                    foreach ($authCheck as $rowCheck) {
                        if ($row->id == $rowCheck->id) {
                            $checked = 'checked';
                        }
                    }
                    echo '<div class="premText"><input type="checkbox" class="premCheckbox" name="prem" value="' . $row->id . '" ' . $checked . ' ' . $disabled . '><label>' . $row->cSubFunction . '</label></div>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="btnBox">
        <div class="alertMsg" id="errorMsg"></div>
        <button class='button' onclick='saveEdit(<?php echo $searchData; ?>);'>儲存</button>
        <button class='button removeRed' onclick='gotoHome(<?php echo $searchData; ?>);'>取消</button>
    </div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/groups.js'></script>