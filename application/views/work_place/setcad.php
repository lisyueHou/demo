<div class="pageTitle">作業區域維護-管線圖路徑設定</div>

<div class="pageContent">
    <div class="cadImgDiv">
        <div class="cadImgBox" id="cadImgBox">
            <img src="<?php echo $imgPath; ?>" style="width: <?php echo CADIMG_WIDTH; ?>px;height: <?php echo CADIMG_HEIGHT; ?>px;">
            <?php
            if ($coordinate != 'null') {
                $coordinate_obj = json_decode($coordinate);
                foreach ($coordinate_obj as $row) {
                    $markId = 'mark_' . $row->id;
                    echo '<div id="' . $markId . '" class="mark" style="left:' . $row->x_axis . 'px;top:' . $row->y_axis . 'px"><span class="markNo">' . $row->id . '</span></div>';
                }
            }
            ?>
        </div>
    </div>
    <div class="markList" id="markList">
        <div class="alertMsg">*請依序點選管線座標路徑，並填寫路徑位置距離</div>
        <table class="contentsTable textCenter" id="markListTable">
            <?php
            if ($coordinate != 'null') {
                $coordinate_obj = json_decode($coordinate);
                echo '<thead><tr>';
                echo '<th class="width_40px">編號</th>';
                echo '<th>X座標</th>';
                echo '<th>Y座標</th>';
                echo '<th>位置距離</th>';
                echo '<th>功能</th>';
                echo '</tr></thead>';
                foreach ($coordinate_obj as $row) {
                    echo '<tr id="tr_' . $row->id . '">';
                    echo '<td>' . $row->id . '</td>';
                    echo '<td>' . $row->x_axis . '</td>';
                    echo '<td>' . $row->y_axis . '</td>';
                    echo '<td><input type="number" id="meters_' . $row->id . '" value="' . $row->meters . '" min="0">米</td>';
                    echo '<td><button class="button removeRed" onclick="removeMark(' . $row->id . ')">移除標記</button></td>';
                }
            }
            ?>
        </table>
    </div>

    <div class="btnBox">
        <div class="alertMsg" id="errorMsg"></div>
        <button class='button' onclick='saveCadImg(<?php echo $id; ?>,<?php echo $searchData; ?>);'>儲存</button>
        <button class='button removeRed' onclick='gotoHome(<?php echo $searchData; ?>);'>取消</button>
    </div>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_place.js'></script>
<script>
    //新增標記
    $("#cadImgBox").click(function(e) {
        let xPos = Math.round(e.pageX - $(this).offset().left);
        let yPos = Math.round(e.pageY - $(this).offset().top);

        if (xPos < 0 || yPos < 0) {
            document.getElementById("errorMsg").innerHTML = "請重新點選座標";
            return;
        }

        //新增標記點位
        var no = $('#cadImgBox').children('div').length + 1; //取得標記點位數量
        creatMark(xPos, yPos, no);

        //新增資料列
        creatMarkData(xPos, yPos, no);
    })
</script>