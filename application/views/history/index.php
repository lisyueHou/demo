<div class="pageTitle">歷史資料查詢</div>

<div class="pageContent">
    <div class="row">
        <div class="col-4">
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
        <div class="col">
            <label>作業時間：</label>
            <input type="datetime-local" step="1" id="startTime"  class="width_240px" value="<?php if (isset($searchData['startTime'])) echo $searchData['startTime']; ?>">
            至
            <input type="datetime-local" step="1" id="endTime" class="width_240px" value="<?php if (isset($searchData['endTime'])) echo $searchData['endTime']; ?>">
        </div>
    </div>
    <div class="btnBox">
        <button class='button' onclick='dataList(1, 10);'>搜尋</button>
        <button class='button removeRed' onclick='cancelSearch();'>取消</button>
    </div>
</div>

<div class="pageContent">
    <!-- 列表  -->
    <div class="contentsList" id="dataList">
        <?php
            $no = ($searchData['page'] - 1) * $searchData['pageCount'];
            $pageTotalCount = count($data['data']);
            if ($pageTotalCount != 0) {
                //表頭
                echo '<table class="contentsTable">';
                echo '<tr>';
                echo '<th class="width_60px">No</th>';
                echo '<th>姿態儀浮仰值</th>';
                echo '<th>姿態儀翻轉值</th>';
                echo '<th>計米輪數值</th>';
                echo '<th>GPS經緯度座標</th>';
                echo '<th>數據紀錄時間</th>';
                echo '</tr>';

                //資料列
                foreach ($data['data'] as $row) {
                    $no++;
                    $rowStr = json_encode($row, True);
                    echo '<tr>';
                    echo '<td class="textCenter">' . $no . '</td>';
                    echo '<td>' . $row->accPitch . '</td>';
                    echo '<td>' . $row->accRoll . '</td>';
                    echo '<td>' . $row->meters . '</td>';
                    $location = '-';
                    if($row->location){
                        $location = $row->location;
                    }
                    echo '<td>' . $location . '</td>';
                    echo '<td>' . $row->dataTime . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="textRed textCenter">查無歷史資料</div>';
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/history.js'></script>
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