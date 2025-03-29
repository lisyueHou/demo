<input type="hidden" id="id" value="<?php echo $data->id; ?>">
<div class="pageTitle">工單報表管理-編輯工單報表</div>

<div class="pageContent work_form">
    <div class="row">
        <div class="col-md-12 alertMsg">*為必填資料</div>
        <div class="col-md-6 col-xl-4">
            <label>表單編號：</label>
            <b><span id="formNo"><?php echo $data->formNo; ?></span></b>
        </div>
        <div class="col-md-6 col-xl-8">
            <label><span class="alertMsg">*</span>設備名稱：</label>
            <select id="robotNo">
                <?php
                foreach ($robot as $row) {
                    $select = '';
                    if ($row->robotNo == $data->robotNo) {
                        $select = 'selected';
                    }
                    echo '<option value="' . $row->robotNo . '" ' . $select . '>' . $row->name . '[' . $row->robotNo . ']</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-xl-4">
            <label><span class="alertMsg">*</span>作業開始時間：</label>

            <input type="datetime-local" id="startTime" value="<?php echo date("Y-m-d H:i", strtotime($data->startTime)); ?>" required="required">
        </div>
        <div class="col-md-6 col-xl-8">
            <label>作業結束時間：</label>
            <?php
            if (($data->finishTime == NULL) || ($data->finishTime == '0000-00-00 00:00:00')) {
                $finishTime = '';
            } else {
                $finishTime = date("Y-m-d H:i", strtotime($data->finishTime));
            }
            ?>
            <input type="datetime-local" id="finishTime" value="<?php echo $finishTime; ?>">
        </div>

        <div class="col-md-6 col-xl-4">
            <label>業主：</label>
            <input type="hidden" id="oldClientId" value="<?php echo $data->clientId; ?>">
            <select id="clientId">
                <?php
                if ($data->clientId == 0) {
                    echo '<option value="0">請選擇業主</option>';
                }
                foreach ($client as $row) {
                    $select = '';
                    if ($row->id == $data->clientId) {
                        $select = 'selected';
                    }
                    echo '<option value="' . $row->id . '" ' . $select . '>' . $row->company . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>工作位置：</label>
            <select id="workPlaceId">
                <?php
                if (count($work_place) == 0) {
                    echo '<option value="0">請選擇工作位置</option>';
                }
                foreach ($work_place as $row) {
                    $select = '';
                    if ($row->id == $data->workPlaceId) {
                        $select = 'selected';
                    }
                    echo '<option value="' . $row->id . '" ' . $select . '>' . $row->name . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>協力廠商：</label>
            <input type="text" id="contractor" maxlength="30" value="<?php echo $data->contractor; ?>">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>專案編號：</label>
            <input type="text" id="projectNo" maxlength="50" value="<?php echo $data->projectNo; ?>">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>工程專案名稱：</label>
            <input type="text" id="projectName" maxlength="50" value="<?php echo $data->projectName; ?>">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>分項工程名稱：</label>
            <input type="text" id="subProjectName" maxlength="50" value="<?php echo $data->subProjectName; ?>">
        </div>

        <div class="col-md-6 col-xl-4">
            <label>檢查日期：</label>
            <?php
            if (($data->checkDate == NULL) || ($data->checkDate == '0000-00-00')) {
                $checkDate = '';
            } else {
                $checkDate = $data->checkDate;
            }
            ?>
            <input type="date" id="checkDate" value="<?php echo $checkDate; ?>">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>管線編號：</label>
            <input type="text" id="pipingLineNo" maxlength="50" value="<?php echo $data->pipingLineNo; ?>">
        </div>
        <div class="col-md-6 col-xl-4">
            <label>段數編號：</label>
            <input type="text" id="segmentsNo" maxlength="50" value="<?php echo $data->segmentsNo; ?>">
        </div>

        <div class="col-md-12">
            <?php
            $resultsY = 0;
            $resultsN = 0;
            if (count($formRemark) > 0) {
                echo '<table class="contentsTable">';
                echo '<tr>';
                echo '<th rowspan="2" class="label width_60px">項次</th>';
                echo '<th rowspan="2" class="label width_100px">管線內距離</th>';
                echo '<th rowspan="2" class="label">管內檢查情形</th>';
                echo '<th colspan="2" class="label width_160px">檢驗結果</th>';
                echo '<th rowspan="2" class="label width_100px">備考</th>';
                echo '<th rowspan="2" class="label width_100px">功能</th>';
                echo '</tr>';
                echo '<tr>';
                echo '<th class="label width_80px">已清除</th>';
                echo '<th class="label width_80px">未清除</th>';
                echo '</tr>';
                $no = 0;
                foreach ($formRemark as $row) {
                    $no++;
                    echo '<tr class="textCenter">';
                    echo '<td>' . $no . '</td>';
                    echo '<td>' . $row->meters . 'm</td>';
                    echo '<td>' . $row->content . '</td>';
                    if ($row->results == 'Y') {
                        echo '<td><i class="fas fa-check"></i></td>';
                        echo '<td></td>';
                        $resultsY++;
                    } else {
                        echo '<td></td>';
                        echo '<td><i class="fas fa-check"></i></td>';
                        $resultsN++;
                    }
                    echo '<td>' . $row->remark . '</td>';


                    $rowStr = json_encode($row, True);
                    $editBtn = "<button class='button modGreen' onclick='viewFormRemark(" . $rowStr . "," . $no . ");'>編輯</button>";
                    $delBtn = "<button class='button removeRed' onclick='delFormRemark(" . $rowStr . ");'>刪除</button>";
                    echo '<td>' . $editBtn . $delBtn . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<label>清潔標記資料：</label><span class="textRed">無標記資料</span>';
            }
            ?>

        </div>
        <div class="col-md-6 col-xl-6">
            <label>完成清潔檢查點：</label>
            <span><?php echo $resultsY; ?></span>
        </div>
        <div class="col-md-6 col-xl-6">
            <label>未完成清潔檢查點：</label>
            <span><?php echo $resultsN; ?></span>
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_form.js'></script>
<script>
    //顧客選單觸發作業地點選單
    $('#clientId').change(function() {
        var clientId = document.getElementById('clientId').value;
        getWorkPlaceList(clientId);
    });
</script>