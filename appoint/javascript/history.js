// 取得列表資料
function dataList(page, pageCount) {
    document.getElementById('errorMsg').innerHTML = "";
    var robotNo = document.getElementById('robotNo').value;
    var startTime = document.getElementById('startTime').value;
    var endTime = document.getElementById('endTime').value;
    if(startTime && endTime){

    }else{
        document.getElementById('errorMsg').innerHTML = "請確認作業時間";
        return;
    }
    var data_obj = {
        robotNo: robotNo,
        startTime: startTime,
        endTime: endTime,
        page: page,
        pageCount: pageCount
    };
    var result = call_api('history_api/getRobotHistory', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        showDataList(data, data_obj);
    } else {
        var msg = '查無歷史資料';
        noDataList(msg);
    }
}

// 顯示列表資料
function showDataList(seachText, data_obj) {
    var page = parseInt(data_obj['page']);
    var pageCount = parseInt(data_obj['pageCount']);
    var pageStart = (page - 1) * pageCount;
    var totalPage = seachText['totalPage'];
    var totalCount = seachText['totalCount'];

    var hideobj = document.getElementById("pageBox");//分頁區塊
    var data = seachText['data'];
    var count = json_count(data); // 資料筆數
    var tab = '';
    if (count != 0) {
        //顯示列表資料
        tab += '<table class="contentsTable">';
        tab += '<tr>';
        tab += '<th class="width_60px">No</th>';
        tab += '<th>姿態儀浮仰值</th>';
        tab += '<th>姿態儀翻轉值</th>';
        tab += '<th>計米輪數值</th>';
        tab += '<th>GPS經緯度座標</th>';
        tab += '<th>數據紀錄時間</th>';
        tab += '</tr>';

        var no = pageStart;
        data.forEach(data => {
            no++;
            var rowStr = JSON.stringify(data);
            tab += '<tr>';
            tab += '<td class="textCenter">' + no + '</td>';
            tab += '<td>' + data['accPitch'] + '</td>';
            tab += '<td>' + data['accRoll'] + '</td>';
            tab += '<td>' + data['meters'] + '</td>';
            var location = '-';
            if(data['location']){
                location = data['location'];
            }
            tab += '<td>' + location + '</td>';
            tab += '<td>' + data['dataTime'] + '</td>';
            tab += '</tr>';
        });
        hideobj.style.display = "inline-block"; //顯示筆數頁數層
        tab += "</table>";
        $("#dataList").html(tab);

        // 清除筆數頁數select裡的所有option
        document.getElementById("listPageCount").innerHTML = "";
        document.getElementById("listPage").innerHTML = "";

        // 輸出資料筆數及頁數
        pageSelect(totalPage, page, pageCount);
        document.getElementById('totalCount').innerHTML = '資料筆數：' + totalCount;
    } else {
        var msg = '查無歷史資料';
        noDataList(msg);
    }
}

//取消搜尋資料
function cancelSearch() {
    $("#robotNo").val($("#robotNo option:first").val());//讓選單改成選第一個
    document.getElementById('startTime').value = '';
    document.getElementById('endTime').value = '';
    dataList(1, 10);
}
