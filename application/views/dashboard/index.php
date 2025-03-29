<input type="hidden" id="CADIMG_WIDTH" value="<?php echo CADIMG_WIDTH ?>">
<input type="hidden" id="CADIMG_HEIGHT" value="<?php echo CADIMG_HEIGHT ?>">
<div class="pageTitle">即時資訊</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <label>選擇機器人：</label>
            <select id="robotNo" class='width_200px'>
                <?php
                    $robotNo = '';
                    if (isset($searchData['robotNo'])) {
                        $robotNo = $searchData['robotNo'];
                    }
                    foreach ($robot as $row) {
                        $robot_selected = '';
                        if ($row->robotNo == $robotNo) {
                            $robot_selected = 'selected';
                        }
                        echo '<option value="' . $row->robotNo . '" ' . $robot_selected . '>' . $row->name . '</option>';
                    }
                    ?>
            </select>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-2">
            <label>作業狀態：
                <?php
                    if ($status == false) {
                        echo '<label id="workStatus" style="color:gray">無作業</label>';
                    } else {
                        echo '<label id="workStatus" style="color:green">作業中</label>';
                    }
                ?>
            </label>
        </div>
        <div class="col-2">
            <label>作業區域：
                <?php
                    if ($status == false) {
                        echo '<label id="workArea" style="color:gray">無作業</label>';
                    } else {
                        echo '<label id="workArea" style="color:black">' . $data['formData'][0]->areaName . '</label>';
                    }
                ?>
            </label>
        </div>
        <div class="col">
            <label>作業地點：
                <?php
                    if ($status == false) {
                        echo '<label id="workPlace" style="color:gray">無作業</label>';
                    } else {
                        echo '<label id="workPlace" style="color:black">' . $data['formData'][0]->company . '(' . $data['formData'][0]->wpName . ')' . '</label>';
                    }
                ?>
            </label>
        </div>
        <div class="col-2">
            <label>作業時間：
                <?php
                    if ($status == false) {
                        echo '<label id="workUpdateTime" style="color:gray">無作業</label>';
                    } else {
                        echo '<label id="workUpdateTime" style="color:black">' . $data['formData'][0]->startTime . '</label>';
                    }
                ?>
            </label>
        </div>
        <div class="col">
            <label>作業表單：
                <?php
                    if ($status == false) {
                        echo '<label id="formNo_label" style="color:gray">無作業</label>';
                    } else {
                        echo '<label id="formNo_label" style="color:black">' . $data['formData'][0]->formNo . '</label>';
                    }
                ?>
            </label>
        </div>
    </div>
    <br>

    <div id='realTimeArea' class='row <?php if ($status == false) {echo 'none';}?>'>
        <div class="col col-3">
            <div class="row border border-dark" style='background:gray;color:white'>
                <div id="dataTime" class='col'>
                    <?php
                        if ($status == true) {
                            echo '時間：' . $data[0]->dataTime;
                        }
                    ?>
                </div>
            </div>

            <div class="row border border-dark">
                <div class="cadImgDiv textCenter">
                    <div class="cadImgBox" id="cadImgBox">
                        <?php
                            if ($status == true) {
                                echo '<img src="' . $data['formData'][0]->cadImg . '"' . ' style="width:' . CADIMG_WIDTH . 'px;height:' . CADIMG_HEIGHT . 'px;"</img>';
                                echo '<div id="markDiv">';
                                if (count($data['formData']['form_remark_data']['data']) > 0) {
                                    foreach ($data['formData']['form_remark_data']['data'] as $row) {
                                        $markId = 'mark_' . $row->id;
                                        echo '<div name="markGroup" id="' . $markId . '" class="mark" style="left:' . $row->x_axis . 'px;top:' . $row->y_axis . 'px"><span class="markNo">' . $row->id . '</span></div>';
                                    }
                                }
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div class="row border border-dark" style='background:gray;color:white'>
                <div id="sensorData" class='col col-9'>
                    <?php
                        if ($status == true) {
                            echo '姿態儀浮仰值：' . $data[0]->accPitch . ' / ' . '姿態儀翻轉值：' . $data[0]->accRoll;
                        }
                    ?>
                </div>
                <div id="metersData" class='col '>
                    <?php
                        if ($status == true) {
                            echo '距離：' . $data[0]->meters . '米';
                        }
                    ?>
                </div>
            </div>
        </div>

        <div id="formfilekDiv"
            class='col col border border-dark <?php if ($status == false) {echo ' none ';}?> <?php if (isset($data['formData']['form_file']['total_count']) && $data['formData']['form_file']['total_count'] == 0) {echo ' none ';}?>'>
            <!-- 資料  -->
            <div id="formfileTable">
                <table class='contentsTable textCenter '>
                    <?php
                        if ($status == true) {
                            if (($data['formData']['form_file']['total_count']) > 0) {
                                echo '<input type="hidden" id="form_file_formNo" value="' . $data['formData'][0]->formNo . '" </input>';
                                echo '<input type="hidden" id="form_file_pageTotalCount" value="' . ($data['formData']['form_file']['total_count']) . '" </input>';
                                echo '<thead><tr>';
                                echo '<th class="width_60px">No</th>';
                                echo '<th>檔案內容</th>';
                                echo '</tr></thead>';
                                $no = ($data['formData']['form_file']['page'] - 1) * $data['formData']['form_file']['page_count'];
                                foreach ($data['formData']['form_file']['data'] as $row) {
                                    $no++;
                                    echo '<tr>';
                                    echo '<td>' . $no . '</td>';
                                    if ($row->fileType == "mp4") { //影片的話，使用超連結開啟，新分頁撥放影片
                                        echo '<td>' . '<a href="'.$row->filePath.'" target="_blank">' . $row->fileName . '</a> ' . '</td>';
                                    } else if ($row->fileType == "jpg" || $row->fileType == "jpeg" || $row->fileType == "png") { //圖片顯示縮圖
                                        echo '<td class="textCenter"><a href="' . $row->filePath . '" target="_blank"><img src="' . $row->filePath . '" class="img"></a>' . '</td>';
                                    } else {
                                        echo '<td>-</td>';
                                    }
                                }
                            } else {
                                echo '<input type="hidden" id="form_file_pageTotalCount" value=0 </input>';
                                echo '<input type="hidden" id="form_file_formNo" </input>';
                            }
                        }
                    ?>
                </table>
            </div>

            <div class="paginationBox">
                <div class="pageMsg" id="form_file_errorMsg"></div>
                <div class="totalCount" id="form_file_totalCount">
                    <?php
                        if (isset($data['formData']['form_file']['total_count'])) {
                            echo "資料筆數：" . ($data['formData']['form_file']['total_count']);
                        } else {
                            echo "資料筆數：" . 0;
                        }
                    ?>
                </div>
                <div class="pagination" id="form_file_pageBox">
                    <!-- 筆數頁數  -->
                    <div>
                        <span>每頁</span>
                        <select id="form_file_listPageCount">
                            <?php
                                for ($i = 10; $i <= 50; $i = $i + 10) {
                                    $pageCount_selected = '';
                                    if (isset($data['formData']['form_file']['page_count'])) {
                                        if ($data['formData']['form_file']['page_count'] == $i) {
                                            $pageCount_selected = 'selected';
                                        }
                                    }
                                    echo '<option value="' . $i . '" ' . $pageCount_selected . '>' . $i . '</option>';
                                }
                            ?>
                        </select>
                        <span>筆</span>
                        <button class="pageBtn" id="form_file_lastPage"
                            onclick="formFileLastPage('form_file_listPage','form_file_listPageCount');"><i
                                class="fas fa-arrow-left"></i>上一頁</button>
                        <span>第</span>
                        <select id="form_file_listPage">
                            <?php
                                for ($i = 1; $i <= $data['formData']['form_file']['total_page']; $i++) {
                                    $page_selected = '';
                                    if (isset($data['formData']['form_file']['page'])) {
                                        if ($data['formData']['form_file']['page'] == $i) {
                                            $page_selected = 'selected';
                                        }
                                    }
                                    echo '<option value="' . $i . '" ' . $page_selected . '>' . $i . '</option>';
                                }
                            ?>
                        </select>
                        <span>頁</span>
                        <button class="pageBtn" id="form_file_nextPage"
                            onclick="formFileNextPage('form_file_listPage','form_file_listPageCount');">下一頁<i
                                class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div id="formRemarkDiv"
            class='col border border-dark <?php if ($status == false) {echo 'none';}?> <?php if (isset($data['formData']['form_remark_data']['total_count'])  && ($data['formData']['form_remark_data']['total_count']) == 0) {echo 'none';}?>'>
            <!-- 資料  -->
            <div id="formRemarkTable">
                <table class='contentsTable textCenter '>
                    <?php
                        if ($status == true) {
                            if (($data['formData']['form_remark_data']['total_count']) > 0) {
                                echo '<input type="hidden" id="formNo" value="' . $data['formData'][0]->formNo . '" </input>';
                                echo '<input type="hidden" id="pageTotalCount" value="' . ($data['formData']['form_remark_data']['total_count']) . '" </input>';
                                echo '<thead><tr>';
                                echo '<th class="width_80px">標記編號</th>';
                                echo '<th class="width_200px">時間</th>';
                                echo '<th>內容</th>';
                                echo '</tr></thead>';
                                foreach ($data['formData']['form_remark_data']['data'] as $row) {
                                    echo '<tr>';
                                    echo '<td>' . $row->id . '</td>';
                                    echo '<td>' . $row->remarkTime . '</td>';
                                    echo '<td>' . $row->content . '</td>';
                                }
                            } else {
                                echo '<input type="hidden" id="pageTotalCount" value=0 </input>';
                                echo '<input type="hidden" id="formNo" </input>';
                            }
                        }
                    ?>
                </table>
            </div>

            <div class="paginationBox">
                <div class="pageMsg" id="errorMsg"></div>
                <div class="totalCount" id="totalCount">
                    <?php
                        if (isset($data['formData']['form_remark_data']['total_count'])) {
                            echo "資料筆數：" . ($data['formData']['form_remark_data']['total_count']);
                        } else {
                            echo "資料筆數：" . 0;
                        }
                    ?>
                </div>
                <div class="pagination" id="pageBox">
                    <!-- 筆數頁數  -->
                    <div>
                        <span>每頁</span>
                        <select id="listPageCount">
                            <?php
                                for ($i = 10; $i <= 50; $i = $i + 10) {
                                    $pageCount_selected = '';
                                    if (isset($data['formData']['form_remark_data']['page_count'])) {
                                        if ($data['formData']['form_remark_data']['page_count'] == $i) {
                                            $pageCount_selected = 'selected';
                                        }
                                    }
                                    echo '<option value="' . $i . '" ' . $pageCount_selected . '>' . $i . '</option>';
                                }
                            ?>
                        </select>
                        <span>筆</span>
                        <button class="pageBtn" id="lastPage"
                            onclick="formRemarkLastPage('listPage','listPageCount');"><i
                                class="fas fa-arrow-left"></i>上一頁</button>
                        <span>第</span>
                        <select id="listPage">
                            <?php
                                for ($i = 1; $i <= $data['formData']['form_remark_data']['total_page']; $i++) {
                                    $page_selected = '';
                                    if (isset($data['formData']['form_remark_data']['page'])) {
                                        if ($data['formData']['form_remark_data']['page'] == $i) {
                                            $page_selected = 'selected';
                                        }
                                    }
                                    echo '<option value="' . $i . '" ' . $page_selected . '>' . $i . '</option>';
                                }
                            ?>
                        </select>
                        <span>頁</span>
                        <button class="pageBtn" id="nextPage"
                            onclick="formRemarkNextPage('listPage','listPageCount');">下一頁<i
                                class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/dashboard.js'></script>

<script>
//分頁-筆數函數
$('#listPageCount').change(function() {
    var pageCount = document.getElementById('listPageCount').value;
    formRemarkdataList(1, pageCount);
});

//分頁-頁數函數
$('#listPage').change(function() {
    var page = document.getElementById('listPage').value;
    var pageCount = document.getElementById('listPageCount').value;
    formRemarkdataList(page, pageCount);
});

//分頁-筆數函數_檔案上傳表格
$('#form_file_listPageCount').change(function() {
    var pageCount = document.getElementById('form_file_listPageCount').value;
    formFiledataList(1, pageCount);
});

//分頁-頁數函數_檔案上傳表格
$('#form_file_listPage').change(function() {
    var page = document.getElementById('form_file_listPage').value;
    var pageCount = document.getElementById('form_file_listPageCount').value;
    formFiledataList(page, pageCount);
});

//更換設備時抓取資料
$('#robotNo').change(function() {
    getRobotRealTimeData();
});
</script>