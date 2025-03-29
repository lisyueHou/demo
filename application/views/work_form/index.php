<div class="pageTitle">工單報表管理</div>
<input type="hidden" id="personId" value="<?php echo $personId;?>">
<div class="pageContent">
    <div class="row">
        <div class="col-md-6 col-lg-4">
            <label>表單編號：</label>
            <input type="text" id="formNo" maxlength="50" value="<?php if (isset($searchData['formNo'])) echo $searchData['formNo']; ?>">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>專案編號：</label>
            <input type="text" id="projectNo" maxlength="50" value="<?php if (isset($searchData['projectNo'])) echo $searchData['projectNo']; ?>">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>檢查日期：</label>
            <input type="date" id="checkDate" value="<?php if (isset($searchData['checkDate'])) echo $searchData['checkDate']; ?>">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>工程專案名稱：</label>
            <input type="text" id="projectName" maxlength="30" value="<?php if (isset($searchData['projectName'])) echo $searchData['projectName']; ?>">
        </div>
        <div class="col-md-6 col-lg-4">
            <label>協力廠商：</label>
            <input type="text" id="contractor" maxlength="30" value="<?php if (isset($searchData['contractor'])) echo $searchData['contractor']; ?>">
        </div>
        <?php
        $none = '';
        if ($searchData['clientId'] != NULL) {
            $none = 'none';
        }
        ?>
        <div class="col-md-6 col-lg-4 <?php echo $none; ?>">
            <label>業主名稱：</label>
            <input type="text" id="company" maxlength="30" value="<?php if (isset($searchData['company'])) echo $searchData['company']; ?>">
            <input type="hidden" id="clientId" value="<?php if (isset($searchData['clientId'])) echo $searchData['clientId']; ?>">
        </div>
    </div>
    <div class="btnBox">
        <button class='button' onclick='dataList(1, 10);'>搜尋</button>
        <button class='button removeRed' onclick='cancelSearch();'>取消</button>
    </div>
</div>

<div class="pageContent">
    <div class="addButBox">
        <?php
        if ($searchData['clientId'] == NULL) {
            echo '<button class="button" onclick="gotoAdd();">新增工單報表</button>';
        }
        ?>
    </div>
    <!-- 列表  -->
    <div class="contentsList" id="dataList">
        <?php
        $no = ($searchData['page'] - 1) * $searchData['pageCount'];
        $pageTotalCount = count($data['work_form']);
        if ($pageTotalCount != 0) {
            //表頭
            echo '<table class="contentsTable">';
            echo '<tr>';
            echo '<th class="width_40px">No</th>';
            echo '<th>表單編號</th>';
            echo '<th>作業狀態</th>';
            echo '<th>檢查日期</th>';
            echo '<th>專案編號</th>';
            echo '<th>工程專案名稱</th>';
            echo '<th>協力廠商</th>';
            echo '<th>業主</th>';
            echo '<th>簽核狀態</th>';
            echo '<th class="width_60px">備註</th>';
            if ($searchData['clientId'] == NULL) {
                echo '<th class="width_180px">功能</th>';
            } else {
                echo '<th class="width_60px">功能</th>';
            }
            echo '</tr>';

            //資料列
            foreach ($data['work_form'] as $row) {
                $no++;
                $rowStr = json_encode($row, True);

                echo '<tr>';
                echo '<td class="textCenter">' . $no . '</td>';
                echo "<td><a href='#' onclick='gotoDetail(" . $rowStr . ");'>" . $row->formNo . '</a></td>';

                //作業狀態
                if ($row->finishTime == '0000-00-00 00:00:00') {
                    echo '<td class="textCenter textGreen">進行中</td>';
                } else {
                    echo '<td class="textCenter">已完成</td>';
                }

                //檢查日期
                if ($row->checkDate == '0000-00-00') {
                    echo '<td></td>';
                } else {
                    echo '<td>' . $row->checkDate . '</td>';
                }
                echo '<td>' . $row->projectNo . '</td>';
                echo '<td>' . $row->projectName . '</td>';
                echo '<td>' . $row->contractor . '</td>';
                echo '<td>' . $row->company . '</td>';

                //簽核狀態
                $formsign = $row->formsign;
                $signSort = $row->signSort;
                $isComplete = $row->isComplete;
                $settingPersonId = $row->settingPersonId;
                $proxyPersonId = $row->proxyPersonId;
                $signSetBtn = "<button class='button orange' onclick='gotoSignSet(" . $rowStr . ");'>設定</button>";
                $signData = array(
                    "id" => $row->id,
                    "formNo" => $row->formNo,
                    "signSort" => $signSort,
                    "personId" => $personId
                );
                $signDataStr = json_encode($signData, True);
                if ($personId == $settingPersonId || $personId == $proxyPersonId) {
                    $signBtn = "<button class='button signBlue' onclick='gotoFormSign(" . $signDataStr . ");'>簽核</button>";
                } else {
                    $signBtn = '';
                }
                if (count($formsign) <= 1) {
                    if ($searchData['clientId'] == NULL) {
                        echo '<td class="textOrange textCenter">未設定' . $signSetBtn . '</td>';
                    } else {
                        echo '<td class="textCenter">設定中</td>';
                    }
                } else {
                    //判斷是否已進入簽核流程(施工單位是否已簽核)，如是則不能再修改設定
                    if ($isComplete == 1) { //簽核完成
                        echo '<td class="textCenter">簽核完成</td>';
                    } else {
                        switch ($signSort) {
                            case 1:
                                if ($searchData['clientId'] == NULL) {
                                    echo '<td class="textCenter">施工單位' . $signSetBtn . $signBtn . '</td>';
                                } else {
                                    echo '<td class="textCenter">簽核中</td>';
                                }
                                break;
                            case 2:
                                if ($searchData['clientId'] == NULL) {
                                    echo '<td class="textCenter">建造單位' . $signBtn . '</td>';
                                } else {
                                    echo '<td class="textCenter">簽核中</td>';
                                }
                                break;
                            case 3:
                                if ($searchData['clientId'] == NULL) {
                                    echo '<td class="textCenter">品管單位' . $signBtn . '</td>';
                                } else {
                                    echo '<td class="textCenter">簽核中</td>';
                                }
                                break;
                            case 4:
                                if ($searchData['clientId'] == NULL) {
                                    echo '<td class="textCenter">待業主簽核</td>';
                                } else {
                                    echo '<td class="textCenter">' . $signBtn . '</td>';
                                }
                                break;
                        }
                    }
                }

                //備註
                if ($row->remark == "") {
                    echo '<td class="textCenter">-</td>';
                } else {
                    $remarkBtn = "<button class='button gray' onclick='viewRemark(" . $rowStr . ");'>檢視</button>";
                    echo '<td class="textCenter">' . $remarkBtn . '</td>';
                }

                $qrcodeBtn = "<button class='button printBlue' onclick='openQRcode(" . $rowStr . ");'>QR碼</button>";
                $editBtn = '';
                $delBtn = '';
                if ($searchData['clientId'] == NULL) {
                    $editBtn = "<button class='button modGreen' onclick='gotoEdit(" . $rowStr . ");'>編輯</button>";
                    $delBtn = "<button class='button removeRed' onclick='delRobot(" . $rowStr . "," . $pageTotalCount . ");'>刪除</button>";
                }

                echo '<td class="textCenter">' . $qrcodeBtn . $editBtn . $delBtn . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="textRed textCenter">查無工單報表</div>';
        }
        ?>
    </div>

    <!-- 資料筆數  -->
    <input type="hidden" id="pageTotalCount" value="<?php echo $pageTotalCount; ?>">
    <div class="paginationBox">
        <div class="pageMsg" id="errorMsg"></div>
        <div class="totalCount" id="totalCount"><?php echo "資料筆數：" . $data['totalCount']; ?></div>
        <div class="pagination" id="pageBox">
            <!-- 筆數頁數  -->
            <div>
                <span>每頁</span>
                <select id="listPageCount">
                    <?php
                    for ($i = 10; $i <= 50; $i = $i + 10) {
                        $pageCount_selected = '';
                        if (isset($searchData['pageCount'])) {
                            if ($searchData['pageCount'] == $i) {
                                $pageCount_selected = 'selected';
                            }
                        }
                        echo '<option value="' . $i . '" ' . $pageCount_selected . '>' . $i . '</option>';
                    }
                    ?>
                </select>
                <span>筆</span>
                <button class="pageBtn" id="lastPage" onclick="lastPage('listPage','listPageCount');"><i class="fas fa-arrow-left"></i>上一頁</button>
                <span>第</span>
                <select id="listPage">
                    <?php
                    for ($i = 1; $i <= $data['totalPage']; $i++) {
                        $page_selected = '';
                        if (isset($searchData['page'])) {
                            if ($searchData['page'] == $i) {
                                $page_selected = 'selected';
                            }
                        }
                        echo '<option value="' . $i . '" ' . $page_selected . '>' . $i . '</option>';
                    }
                    ?>
                </select>
                <span>頁</span>
                <button class="pageBtn" id="nextPage" onclick="nextPage('listPage','listPageCount');">下一頁<i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_form.js'></script>
<script>
    //無資料時不顯示分頁功能
    var pageTotalCount = document.getElementById('pageTotalCount').value;
    if (pageTotalCount == 0) {
        $("#pageBox").css("display", "none");
    }

    //分頁-筆數函數
    $('#listPageCount').change(function() {
        var pageCount = document.getElementById('listPageCount').value;
        dataList(1, pageCount);
    });

    //分頁-頁數函數
    $('#listPage').change(function() {
        var page = document.getElementById('listPage').value;
        var pageCount = document.getElementById('listPageCount').value;
        dataList(page, pageCount);
    });
</script>