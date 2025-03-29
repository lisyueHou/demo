<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="cache-control" content="no-cache">

    <?php
    $run = strtotime(date("Y-m-d H:i:s"));
    //引入css
    echo link_tag('vendor/twbs/bootstrap/dist/css/bootstrap.min.css?run=' . $run);
    echo link_tag('appoint/css/common_style.css?run=' . $run);
    echo link_tag('appoint/css/style.css?run=' . $run);

    //網頁icon
    echo link_tag('appoint/images/webicon.png?run=' . $run, 'icon', 'image/x-icon');
    ?>
    <!-- 引入js 套件 -->
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery-3.1.1.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/icon-v5.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery.qrcode.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/qrcode.js" crossorigin="anonymous"></script>

    <title>管線機器人管理系統工單報表-<?php echo $data->formNo; ?></title>
</head>

<body>

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
                <span><?php
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
                <span><?php
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
    </div>
    <script>
        //產生QR Code
        var formNo = document.getElementById('formNo').innerHTML;
        var baseUrl = document.getElementById('base_url').value;
        var url = baseUrl + 'work_form_qrcode/detail/' + formNo;
        jQuery(function() {
            jQuery('#qrcode').qrcode(url, 80, 80);
        });
    </script>
</body>

</html>
