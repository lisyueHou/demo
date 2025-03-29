<div class="pageTitle">工單報表詳細資料</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <label>表單編號：</label>
            <span id="formNo"><b><?php echo $data->formNo; ?></b></span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>設備[編號]：</label>
            <span><?php echo $data->robotName . "[" . $data->robotNo . "]"; ?></span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>作業時間：</label>
            <span>
                <?php
                $startTime = date("Y-m-d H:i", strtotime($data->startTime));
                if ($data->finishTime == '0000-00-00 00:00:00') {
                    $workTime = '作業中(' . $startTime . '開始作業)';
                } else {
                    $workTime = $startTime . '~' . date("Y-m-d H:i", strtotime($data->finishTime));
                }

                echo $workTime;
                ?>
            </span>
        </div>

        <div class="col-md-12">
            <hr>
        </div>

        <div class="col-md-6 col-xl-4">
            <label>專案編號：</label>
            <span><?php echo $data->projectNo; ?></span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>工程專案名稱：</label>
            <span><?php echo $data->projectName; ?></span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>分項工程名稱：</label>
            <span><?php echo $data->subProjectName; ?></span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>協力廠商：</label>
            <span><?php echo $data->contractor; ?></span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>工作位置：</label>
            <span><?php echo $data->workPlaceName; ?></span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>日期：</label>
            <span>
                <?php
                if ($data->checkDate != '0000-00-00') {
                    echo $data->checkDate;
                }
                ?>
            </span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>業主：</label>
            <span><?php echo $data->company; ?></span>
        </div>
        <div class="col-md-6 col-xl-4">
            <label>管線編號：</label>
            <span><?php echo $data->pipingLineNo; ?></span>
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
        <div class="col-md-6 col-xl-6">
            <label>施工單位：</label>
            <?php
            $checkSign = false;
            if (count($formSign) != 0) {
                foreach ($formSign as $row) {
                    $signSort = $row->signSort;
                    if ($signSort == 1) {
                        if ($row->signPersonNo != '') {
                            echo '<span>' . $row->signPersonName . '[' . $row->signPersonNo . ']';
                            echo '(date：' . date("Y-m-d", strtotime($row->signTime)) . ')</span>';
                            $checkSign = true;
                        }
                    }
                }
            }
            if (!$checkSign) {
                echo '<span class="textRed">未簽核</span>';
            }
            ?>
        </div>
        <div class="col-md-6 col-xl-6">
            <label>建造工程師：</label>
            <?php
            $checkSign = false;
            if (count($formSign) != 0) {
                foreach ($formSign as $row) {
                    $signSort = $row->signSort;
                    if ($signSort == 2) {
                        if ($row->signPersonNo != '') {
                            echo '<span>' . $row->signPersonName . '[' . $row->signPersonNo . ']';
                            echo '(date：' . date("Y-m-d", strtotime($row->signTime)) . ')</span>';
                            $checkSign = true;
                        }
                    }
                }
            }
            if (!$checkSign) {
                echo '<span class="textRed">未簽核</span>';
            }
            ?>
        </div>
        <div class="col-md-6 col-xl-6">
            <label>品管工程師：</label>
            <?php
            $checkSign = false;
            if (count($formSign) != 0) {
                foreach ($formSign as $row) {
                    $signSort = $row->signSort;
                    if ($signSort == 3) {
                        if ($row->signPersonNo != '') {
                            echo '<span>' . $row->signPersonName . '[' . $row->signPersonNo . ']';
                            echo '(date：' . date("Y-m-d", strtotime($row->signTime)) . ')</span>';
                            $checkSign = true;
                        }
                    }
                }
            }
            if (!$checkSign) {
                echo '<span class="textRed">未簽核</span>';
            }
            ?>
        </div>
        <div class="col-md-6 col-xl-6">
            <label>顧客代表：</label>
            <?php
            $checkSign = false;
            if (count($formSign) != 0) {
                foreach ($formSign as $row) {
                    $signSort = $row->signSort;
                    if ($signSort == 4) {
                        if ($row->signPersonNo != '') {
                            echo '<span>' . $row->signPersonName . '[' . $row->signPersonNo . ']';
                            echo '(date：' . date("Y-m-d", strtotime($row->signTime)) . ')</span>';
                            $checkSign = true;
                        }
                    }
                }
            }
            if (!$checkSign) {
                echo '<span class="textRed">未簽核</span>';
            }
            ?>
        </div>
        <div class="col-md-12">
            <label>備註：</label>
            <span><?php echo $data->remark; ?></span>
        </div>
    </div>

    <div class="btnBox" id="btnBox">
        <button class='button printBlue' onclick='openPrintPDF(<?php echo $data->id; ?>);'>匯出PDF</button>
        <button class='button gray' onclick='gotoHome(<?php echo $searchData; ?>);'>回上一頁</button>
    </div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_form.js'></script>
<script>
    //產生QR Code
    var formNo = document.getElementById('formNo').innerHTML;
    var baseUrl = document.getElementById('base_url').value;
    var url = baseUrl + 'work_form_qrcode/detail/' + formNo;
    jQuery(function() {
        jQuery('#qrcode').qrcode(url, 80, 80);
    });
</script>
