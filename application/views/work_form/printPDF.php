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
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <input type="hidden" id="formId" value="<?php echo $data->id; ?>">
    <div class="formTableDiv" style="border: 0px;">
        <?php
        $remarkTotal = count($formRemark);
        $page = 0;
        $pageCount = 10;
        if ($remarkTotal > 0) {
            $pageTotal = ceil($remarkTotal / $pageCount);
            for ($i = 0; $i < $pageTotal; $i++) {
        ?>
                <!-- 表頭 -->
                <table class="formTableHeader">
                    <tr>
                        <td colspan="2"><?php echo date('Y/m/d H:i'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><img src="<?php echo PDF_IMG_PATH . 'workFormlogo.jpg'; ?>" heigh="30"></td>
                    </tr>
                    <tr>
                        <td class="width_100px" rowspan="2">
                            <div id="qrcode"></div>
                        </td>
                        <td class="textCenter">
                            <span class="title">管線清潔作業檢查表</span><br>
                            <span class="subTitle">Pipeline Cleaning Operation Inspection Form</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="textRight">專案編號：<?php echo $data->projectNo; ?></td>
                    </tr>
                </table>

                <!-- 表格表頭 -->
                <table class="formTable">
                    <thead>
                        <tr>
                            <td colspan="2" class="label width_180px">
                                <p>工程專案名稱</p>
                                <p class="title_en">Engineering<br>Project Name</p>
                            </td>
                            <td colspan="10"><?php echo $data->projectName; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="label">
                                <p>分項工程名稱</p>
                                <p class="title_en">Subproject Name</p>
                            </td>
                            <td colspan="5" class="textLeft"><?php echo $data->subProjectName; ?></td>
                            <td colspan="2" class="label width_60px">
                                <p>協力廠商</p>
                                <p class="title_en">Contractor</p>
                            </td>
                            <td colspan="3" class="textLeft"><?php echo $data->contractor; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="label">
                                <p>工作位置</p>
                                <p class="title_en">Work Location</p>
                            </td>
                            <td colspan="5" class="textLeft"><?php echo $data->workPlaceName; ?></td>
                            <td colspan="2" class="label">
                                <p>日期</p>
                                <p class="title_en">Date</p>
                            </td>
                            <td colspan="3">
                                <?php
                                if ($data->checkDate != '0000-00-00') {
                                    echo $data->checkDate;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="label">
                                <p>業主</p>
                                <p class="title_en">Owner</p>
                            </td>
                            <td colspan="10"><?php echo $data->company; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="label">管線編號</td>
                            <td colspan="5"><?php echo $data->pipingLineNo; ?></td>
                            <td colspan="2" class="label">
                                <p>頁數</p>
                                <p class="title_en">Page</p>
                            </td>
                            <td colspan="3"><?php echo $i + 1 . ' of ' . $pageTotal; ?></td>
                        </tr>
                        <tr>
                            <td rowspan="2" class="label width_20px">
                                <p>項次</p>
                                <p class="title_en">NO.</p>
                            </td>
                            <td colspan="2" rowspan="2" class="label width_40px">
                                <p>管線內距離</p>
                                <p class="title_en">Distance</p>
                            </td>
                            <td colspan="5" rowspan="2" class="label">
                                <p>管內檢查情形</p>
                                <p class="title_en">Condition</p>
                            </td>
                            <td colspan="2" class="label">
                                <p>檢驗結果</p>
                                <p class="title_en">Results</p>
                            </td>
                            <td colspan="2" rowspan="2" class="label">備考</td>
                        </tr>
                        <tr>
                            <td class="label_s width_20px">
                                <p>已清除</p>
                                <p class="title_en">Yes</p>
                            </td>
                            <td class="label_s width_20px">
                                <p>未清除</p>
                                <p class="title_en">No</p>
                            </td>
                        </tr>
                    </thead>

                    <!-- 工單標記備註內容 -->
                    <tbody>
                        <?php
                        $resultsY = 0;
                        $resultsN = 0;
                        $pageStart = $page * $pageCount;
                        $no = $pageStart;
                        $pageEnd = $pageStart + $pageCount;
                        for ($j = $pageStart; $j < $pageEnd; $j++) {
                            $no++;
                            if ($no <= $remarkTotal) {
                                echo '<tr class="textCenter">';
                                echo '<td>' . $no . '</td>';
                                echo '<td colspan="2">' . $formRemark[$j]->meters . 'm</td>';
                                echo '<td colspan="5">' . $formRemark[$j]->content . '</td>';
                                if ($formRemark[$j]->results == 'Y') {
                                    echo '<td><i class="fas fa-check"></i></td>';
                                    echo '<td></td>';
                                    $resultsY++;
                                } else {
                                    echo '<td></td>';
                                    echo '<td><i class="fas fa-check"></i></td>';
                                    $resultsN++;
                                }
                                echo '<td colspan="2">' . $formRemark[$j]->remark . '</td>';
                                echo '</tr>';
                            } else {
                                echo '<tr class="textCenter">';
                                echo '<td>' . $no . '</td>';
                                echo '<td colspan="2"></td>';
                                echo '<td colspan="5"></td>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td colspan="2"></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>

                    <!-- 表格頁腳 -->
                    <tfoot>
                        <tr class="checkBox">
                            <td colspan="3" class="label">完成清潔檢查點</td>
                            <td colspan="3"><?php echo $resultsY; ?></td>
                            <td colspan="3" class="label">未完成清潔檢查點</td>
                            <td colspan="3"><?php echo $resultsN; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="slash"></td>
                            <td colspan="2" class="label">
                                <p>施工單位</p>
                                <p class="title_en">Subcontrator</p>
                            </td>
                            <td colspan="3" class="label">
                                <p>建造工程師</p>
                                <p class="title_en">Construction</p>
                            </td>
                            <td colspan="3" class="label">
                                <p>品管工程師</p>
                                <p class="title_en">Quality Engineer</p>
                            </td>
                            <td colspan="2" class="label">
                                <p>顧客代表</p>
                                <p class="title_en">Client</p>
                            </td>
                        </tr>
                        <tr class="date">
                            <td colspan="2" class="label textLeft">
                                <p>日期 Date</p>
                            </td>
                        <?php
                        echo '<td colspan="2">';
                        if (count($formSign) != 0) {
                            foreach ($formSign as $row) {
                                $signSort = $row->signSort;
                                if ($signSort == 1) {
                                    if ($row->signPersonNo != '') {
                                        echo '<p>' . date("Y-m-d", strtotime($row->signTime)) . '</p>';
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '<td colspan="3">';
                        if (count($formSign) != 0) {
                            foreach ($formSign as $row) {
                                $signSort = $row->signSort;
                                if ($signSort == 2) {
                                    if ($row->signPersonNo != '') {
                                        echo '<p>' . date("Y-m-d", strtotime($row->signTime)) . '</p>';
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '<td colspan="3">';
                        if (count($formSign) != 0) {
                            foreach ($formSign as $row) {
                                $signSort = $row->signSort;
                                if ($signSort == 3) {
                                    if ($row->signPersonNo != '') {
                                        echo '<p>' . date("Y-m-d", strtotime($row->signTime)) . '</p>';
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '<td colspan="2">';
                        if (count($formSign) != 0) {
                            foreach ($formSign as $row) {
                                $signSort = $row->signSort;
                                if ($signSort == 4) {
                                    if ($row->signPersonNo != '') {
                                        echo '<p>' . date("Y-m-d", strtotime($row->signTime)) . '</p>';
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '</tr>';

                        echo '<tr>';
                        echo '<td colspan="2" class="label textLeft">';
                        echo '<p>簽名 Signature</p>';
                        echo '</td>';
                        echo '<td colspan="2" class="signature">';
                        if (count($formSign) != 0) {
                            foreach ($formSign as $row) {
                                $signSort = $row->signSort;
                                if ($signSort == 1) {
                                    if ($row->signPersonNo != '') {
                                        echo '<p>' . $row->signPersonName . '</p>';
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '<td colspan="3" class="signature">';
                        if (count($formSign) != 0) {
                            foreach ($formSign as $row) {
                                $signSort = $row->signSort;
                                if ($signSort == 2) {
                                    if ($row->signPersonNo != '') {
                                        echo '<p>' . $row->signPersonName . '</p>';
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '<td colspan="3" class="signature">';
                        if (count($formSign) != 0) {
                            foreach ($formSign as $row) {
                                $signSort = $row->signSort;
                                if ($signSort == 3) {
                                    if ($row->signPersonNo != '') {
                                        echo '<p>' . $row->signPersonName . '</p>';
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '<td colspan="2" class="signature">';
                        if (count($formSign) != 0) {
                            foreach ($formSign as $row) {
                                $signSort = $row->signSort;
                                if ($signSort == 4) {
                                    if ($row->signPersonNo != '') {
                                        echo '<p>' . $row->signPersonName . '</p>';
                                    }
                                }
                            }
                        }
                        echo '</td>';
                        echo '</tr>';
                        echo '</tr>';
                        echo '</tfoot>';
                        echo '</table>';
                        echo '<div class="textRight">' . ($i + 1) . '/' . $pageTotal . '</div>';
                        echo '<p style="page-break-after:always"></p>';
                        $page++;
                    }
                } else {
                        ?>
                        <!-- 表頭 -->
                        <table class="formTableHeader">
                            <tr>
                                <td colspan="2"><?php echo date('Y/m/d H:i'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><img src="<?php echo PDF_IMG_PATH . 'workFormlogo.jpg'; ?>" heigh="30"></td>
                            </tr>
                            <tr>
                                <td class="width_100px" rowspan="2">
                                    <div id="qrcode"></div>
                                </td>
                                <td class="textCenter">
                                    <span class="title">管線清潔作業檢查表</span><br>
                                    <span class="subTitle">Pipeline Cleaning Operation Inspection Form</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="textRight">專案編號：<?php echo $data->projectNo; ?></td>
                            </tr>
                        </table>

                        <!-- 表格表頭 -->
                        <table class="formTable">
                            <thead>
                                <tr>
                                    <td colspan="2" class="label width_180px">
                                        <p>工程專案名稱</p>
                                        <p class="title_en">Engineering<br>Project Name</p>
                                    </td>
                                    <td colspan="10"><?php echo $data->projectName; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="label">
                                        <p>分項工程名稱</p>
                                        <p class="title_en">Subproject Name</p>
                                    </td>
                                    <td colspan="5" class="textLeft"><?php echo $data->subProjectName; ?></td>
                                    <td colspan="2" class="label width_60px">
                                        <p>協力廠商</p>
                                        <p class="title_en">Contractor</p>
                                    </td>
                                    <td colspan="3" class="textLeft"><?php echo $data->contractor; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="label">
                                        <p>工作位置</p>
                                        <p class="title_en">Work Location</p>
                                    </td>
                                    <td colspan="5" class="textLeft"><?php echo $data->workPlaceName; ?></td>
                                    <td colspan="2" class="label">
                                        <p>日期</p>
                                        <p class="title_en">Date</p>
                                    </td>
                                    <td colspan="3"><?php echo $data->checkDate; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="label">
                                        <p>業主</p>
                                        <p class="title_en">Owner</p>
                                    </td>
                                    <td colspan="10"><?php echo $data->company; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="label">管線編號</td>
                                    <td colspan="5"><?php echo $data->pipingLineNo; ?></td>
                                    <td colspan="2" class="label">
                                        <p>頁數</p>
                                        <p class="title_en">Page</p>
                                    </td>
                                    <td colspan="3">1 of 1</td>
                                </tr>
                                <tr>
                                    <td rowspan="2" class="label width_20px">
                                        <p>項次</p>
                                        <p class="title_en">NO.</p>
                                    </td>
                                    <td colspan="2" rowspan="2" class="label width_40px">
                                        <p>管線內距離</p>
                                        <p class="title_en">Distance</p>
                                    </td>
                                    <td colspan="5" rowspan="2" class="label">
                                        <p>管內檢查情形</p>
                                        <p class="title_en">Condition</p>
                                    </td>
                                    <td colspan="2" class="label">
                                        <p>檢驗結果</p>
                                        <p class="title_en">Results</p>
                                    </td>
                                    <td colspan="2" rowspan="2" class="label">備考</td>
                                </tr>
                                <tr>
                                    <td class="label_s width_20px">
                                        <p>已清除</p>
                                        <p class="title_en">Yes</p>
                                    </td>
                                    <td class="label_s width_20px">
                                        <p>未清除</p>
                                        <p class="title_en">No</p>
                                    </td>
                                </tr>
                            </thead>
                            <!-- 工單標記備註內容 -->
                            <tbody>
                                <tr>
                                    <?php
                                    for ($i = 1; $i <= $pageCount; $i++) {
                                        echo '<tr class="textCenter">';
                                        echo '<td>' . $i . '</td>';
                                        echo '<td colspan="2"></td>';
                                        echo '<td colspan="5"></td>';
                                        echo '<td></td>';
                                        echo '<td></td>';
                                        echo '<td colspan="2"></td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tr>
                            </tbody>
                            <!-- 表格頁腳 -->
                            <tfoot>
                                <tr class="checkBox">
                                    <td colspan="3" class="label">完成清潔檢查點</td>
                                    <td colspan="3">0</td>
                                    <td colspan="3" class="label">未完成清潔檢查點</td>
                                    <td colspan="3">0</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="slash"></td>
                                    <td colspan="2" class="label">
                                        <p>施工單位</p>
                                        <p class="title_en">Subcontrator</p>
                                    </td>
                                    <td colspan="3" class="label">
                                        <p>建造工程師</p>
                                        <p class="title_en">Construction</p>
                                    </td>
                                    <td colspan="3" class="label">
                                        <p>品管工程師</p>
                                        <p class="title_en">Quality Engineer</p>
                                    </td>
                                    <td colspan="2" class="label">
                                        <p>顧客代表</p>
                                        <p class="title_en">Client</p>
                                    </td>
                                </tr>
                                <tr class="date">
                                    <td colspan="2" class="label textLeft">
                                        <p>日期 Date</p>
                                    </td>
                                <?php
                                echo '<td colspan="2">';
                                if (count($formSign) != 0) {
                                    foreach ($formSign as $row) {
                                        $signSort = $row->signSort;
                                        if ($signSort == 1) {
                                            if ($row->signPersonNo != '') {
                                                echo '<p>' . date("Y-m-d", strtotime($row->signTime)) . '</p>';
                                            }
                                        }
                                    }
                                }
                                echo '</td>';
                                echo '<td colspan="3">';
                                if (count($formSign) != 0) {
                                    foreach ($formSign as $row) {
                                        $signSort = $row->signSort;
                                        if ($signSort == 2) {
                                            if ($row->signPersonNo != '') {
                                                echo '<p>' . date("Y-m-d", strtotime($row->signTime)) . '</p>';
                                            }
                                        }
                                    }
                                }
                                echo '</td>';
                                echo '<td colspan="3">';
                                if (count($formSign) != 0) {
                                    foreach ($formSign as $row) {
                                        $signSort = $row->signSort;
                                        if ($signSort == 3) {
                                            if ($row->signPersonNo != '') {
                                                echo '<p>' . date("Y-m-d", strtotime($row->signTime)) . '</p>';
                                            }
                                        }
                                    }
                                }
                                echo '</td>';
                                echo '<td colspan="2">';
                                if (count($formSign) != 0) {
                                    foreach ($formSign as $row) {
                                        $signSort = $row->signSort;
                                        if ($signSort == 4) {
                                            if ($row->signPersonNo != '') {
                                                echo '<p>' . date("Y-m-d", strtotime($row->signTime)) . '</p>';
                                            }
                                        }
                                    }
                                }
                                echo '</td>';
                                echo '</tr>';

                                echo '<tr>';
                                echo '<td colspan="2" class="label textLeft">';
                                echo '<p>簽名 Signature</p>';
                                echo '</td>';
                                echo '<td colspan="2" class="signature">';
                                if (count($formSign) != 0) {
                                    foreach ($formSign as $row) {
                                        $signSort = $row->signSort;
                                        if ($signSort == 1) {
                                            if ($row->signPersonNo != '') {
                                                echo '<p>' . $row->signPersonName . '</p>';
                                            }
                                        }
                                    }
                                }
                                echo '</td>';
                                echo '<td colspan="3" class="signature">';
                                if (count($formSign) != 0) {
                                    foreach ($formSign as $row) {
                                        $signSort = $row->signSort;
                                        if ($signSort == 2) {
                                            if ($row->signPersonNo != '') {
                                                echo '<p>' . $row->signPersonName . '</p>';
                                            }
                                        }
                                    }
                                }
                                echo '</td>';
                                echo '<td colspan="3" class="signature">';
                                if (count($formSign) != 0) {
                                    foreach ($formSign as $row) {
                                        $signSort = $row->signSort;
                                        if ($signSort == 3) {
                                            if ($row->signPersonNo != '') {
                                                echo '<p>' . $row->signPersonName . '</p>';
                                            }
                                        }
                                    }
                                }
                                echo '</td>';
                                echo '<td colspan="2" class="signature">';
                                if (count($formSign) != 0) {
                                    foreach ($formSign as $row) {
                                        $signSort = $row->signSort;
                                        if ($signSort == 4) {
                                            if ($row->signPersonNo != '') {
                                                echo '<p>' . $row->signPersonName . '</p>';
                                            }
                                        }
                                    }
                                }
                                echo '</td>';
                                echo '</tr>';
                                echo '</tr>';
                                echo '</tfoot>';
                                echo '</table>';
                            }
                                ?>
    </div>

    <script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
    <script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_form.js'></script>
    <script>
        //產生QR Code
        var formId = document.getElementById('formId').value;
        var baseUrl = document.getElementById('base_url').value;
        var url = baseUrl + 'work_form_qrcode/viewDetail/' + formId;
        jQuery(function() {
            jQuery('#qrcode').qrcode(url, 80, 80);
        });

        //列印
        setTimeout(function() {
            window.print();
        }, 100);
    </script>
</body>

</html>