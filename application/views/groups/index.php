<div class="pageTitle">權限群組維護</div>

<div class="pageContent">
    <div class="row">
        <div class="col-md-6">
            <label>群組類別：</label>
            <select id="groupClass">
                <?php
                $class = '';
                $staff_selected = '';
                $client_selected = '';
                if (isset($searchData['class'])) {
                    $class = $searchData['class'];
                }
                echo '<option value="">請選擇群組類別</option>';
                switch ($class) {
                    case 1:
                        $staff_selected = 'selected';
                        break;
                    case 2:
                        $client_selected = 'selected';
                        break;
                }
                echo '<option value="1" ' . $staff_selected . '>內部群組</option>';
                echo '<option value="2" ' . $client_selected . '>顧客群組</option>';
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label>群組名稱：</label>
            <input type="text" id="name" maxlength="30" value="<?php if (isset($searchData['name'])) echo $searchData['name']; ?>">
        </div>
    </div>
    <div class="btnBox">
        <button class='button' onclick='dataList(1, 10);'>搜尋</button>
        <button class='button removeRed' onclick='cancelSearch();'>取消</button>
    </div>
</div>

<div class="pageContent">
    <div class="addButBox">
        <button class="button" onclick="gotoAdd();">新增權限群組</button>
    </div>
    <!-- 列表  -->
    <div class="contentsList" id="dataList">
        <?php
        $no = ($searchData['page'] - 1) * $searchData['pageCount'];
        $pageTotalCount = count($data['groups']);
        if ($pageTotalCount != 0) {
            //表頭
            echo '<table class="contentsTable">';
            echo '<tr>';
            echo '<th class="width_40px">No</th>';
            echo '<th>群組名稱</th>';
            echo '<th>群組類別</th>';
            echo '<th class="width_80px">群組帳號</th>';
            echo '<th class="width_80px">群組權限</th>';
            echo '<th class="width_120px">功能</th>';
            echo '</tr>';

            //資料列
            foreach ($data['groups'] as $row) {
                $no++;
                $rowStr = json_encode($row, True);

                echo '<tr>';
                echo '<td class="textCenter">' . $no . '</td>';
                echo '<td>' . $row->name . '</td>';

                //群組類別
                switch ($row->class) {
                    case 1:
                        echo '<td>內部群組</td>';
                        break;
                    case 2:
                        echo '<td>顧客群組</td>';
                        break;
                    default:
                        echo '<td>-</td>';
                        break;
                }
                //群組帳號
                $acclistBtn = "<button class='button gray' onclick='viewAcclist(" . $rowStr . ");'>檢視</button>";
                echo '<td class="textCenter">' . $acclistBtn . '</td>';

                //群組權限
                $permBtn = "<button class='button gray' onclick='viewPrem(" . $rowStr . ");'>檢視</button>";
                echo '<td class="textCenter">' . $permBtn . '</td>';

                $editBtn = "<button class='button modGreen' onclick='gotoEdit(" . $rowStr . ");'>編輯</button>";
                $delBtn = "<button class='button removeRed' onclick='delGroup(" . $rowStr . "," . $pageTotalCount . ");'>刪除</button>";
                echo '<td class="textCenter">' . $editBtn . $delBtn . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="textRed textCenter">查無群組資料</div>';
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
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/groups.js'></script>
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