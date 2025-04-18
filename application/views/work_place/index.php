<div class="pageTitle">作業區域維護</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-4">
            <label>作業區域：</label>
            <select id="areaId">
                <?php
                $areaId = '';
                if (isset($searchData['areaId'])) {
                    $areaId = $searchData['areaId'];
                }
                echo '<option value="">請選擇作業區域</option>';
                foreach ($area as $row) {
                    $area_selected = '';
                    if ($row->id == $areaId) {
                        $area_selected = 'selected';
                    }
                    echo '<option value="' . $row->id . '" ' . $area_selected . '>' . $row->name . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>作業地點：</label>
            <input type="text" id="workPlace" maxlength="20" value="<?php if (isset($searchData['workPlace'])) echo $searchData['workPlace']; ?>">
        </div>
        <div class="col-md-4">
            <label>顧客公司名稱：</label>
            <input type="text" id="company" maxlength="30" value="<?php if (isset($searchData['company'])) echo $searchData['company']; ?>">
        </div>
    </div>
    <div class="btnBox">
        <button class='button' onclick='dataList(1, 10);'>搜尋</button>
        <button class='button removeRed' onclick='cancelSearch();'>取消</button>
    </div>
</div>

<div class="pageContent">
    <div class="addButBox">
        <button class="button" onclick="gotoAdd();">新增作業區域</button>
    </div>
    <!-- 列表  -->
    <div class="contentsList" id="dataList">
        <?php
        $no = ($searchData['page'] - 1) * $searchData['pageCount'];
        $pageTotalCount = count($data['work_place']);
        if ($pageTotalCount != 0) {
            //表頭
            echo '<table class="contentsTable">';
            echo '<tr>';
            echo '<th class="width_40px">No</th>';
            echo '<th>作業區域</th>';
            echo '<th>作業地點</th>';
            echo '<th>顧客名稱</th>';
            echo '<th class="width_160px">管線圖</th>';
            echo '<th class="width_80px">地圖位置</th>';
            echo '<th class="width_60px">備註</th>';
            echo '<th class="width_120px">功能</th>';
            echo '</tr>';

            //資料列
            foreach ($data['work_place'] as $row) {
                $no++;
                $rowStr = json_encode($row, True);

                echo '<tr>';
                echo '<td class="textCenter">' . $no . '</td>';
                echo '<td>' . $row->areaName . '</td>';
                echo '<td>' . $row->workPlace . '</td>';
                echo '<td>' . $row->company . '</td>';

                //管線圖
                if ($row->cadImg == "") {
                    echo '<td class="textCenter">-</td>';
                } else {
                    $cadSetBtn = "<button class='button orange' onclick='gotoCadSet(" . $rowStr . ");'>設定路徑</button>";
                    echo '<td class="textCenter"><a href="' . $row->imgPath . '" target="_blank"><img src="' . $row->imgPath . '" class="img"></a>' . $cadSetBtn . '</td>';
                }

                //地圖位置
                if ($row->latitude == "" || $row->longitude == "") {
                    echo '<td class="textCenter">-</td>';
                } else {
                    $mapBtn = "<button class='button gray' onclick='viewMap( " . $rowStr . ")'>檢視</button>";
                    echo '<td class="textCenter">' . $mapBtn . '</td>';
                }

                //備註
                if ($row->remark == "") {
                    echo '<td class="textCenter">-</td>';
                } else {
                    $remarkBtn = "<button class='button gray' onclick='viewRemark(" . $rowStr . ");'>檢視</button>";
                    echo '<td class="textCenter">' . $remarkBtn . '</td>';
                }

                $editBtn = "<button class='button modGreen' onclick='gotoEdit(" . $rowStr . ");'>編輯</button>";
                $delBtn = "<button class='button removeRed' onclick='delWorkPlace(" . $rowStr . "," . $pageTotalCount . ");'>刪除</button>";
                echo '<td class="textCenter">' . $editBtn . $delBtn . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="textRed textCenter">查無作業區域資料</div>';
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_place.js'></script>
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