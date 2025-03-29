// 標記上一頁
function formRemarkLastPage(pageId, countId) {
    var data = checkLastPage(pageId, countId);
    formRemarkdataList(data['page'], data['pageCount']);
}

// 標記下一頁
function formRemarkNextPage(pageId, countId) {
    var data = checkNextPage(pageId, countId);
    formRemarkdataList(data['page'], data['pageCount']);
}

// 上傳檔案資料_上一頁
function formFileLastPage(pageId, countId) {
    var data = checkLastPage(pageId, countId);
    formFiledataList(data['page'], data['pageCount']);
}

// 上傳檔案資料_下一頁
function formFileNextPage(pageId, countId) {
    var data = checkNextPage(pageId, countId);
    formFiledataList(data['page'], data['pageCount']);
}

// 上傳檔案資料
function formFiledataList(page, pageCount) {
    document.getElementById('form_file_errorMsg').value = "";
    var formNo = document.getElementById('form_file_formNo').value;
    var robotNo = document.getElementById('robotNo').value;
    var data_obj = {
        robotNo: robotNo,
        formNo: formNo,
        page: page,
        pageCount: pageCount
    };
    var result = call_api('dashboard_api/formFiledata', data_obj);
    if (result['status']) {
        showForFiledata(result['data'], data_obj);
    }
}

// 上傳檔案資料_表格
function showForFiledata(seachText, data_obj) {

    document.getElementById('dataTime').innerHTML = "時間：" + seachText['realtimeData'][0]['dataTime'];
    document.getElementById('sensorData').innerHTML = "姿態儀浮仰值：" + seachText['realtimeData'][0]['accPitch'] + " / 姿態儀翻轉值：" + seachText['realtimeData'][0]['accRoll'];
    document.getElementById('metersData').innerHTML = "距離：" + seachText['realtimeData'][0]['meters'] + "米";

    var page = parseInt(data_obj['page']);
    var pageCount = parseInt(data_obj['pageCount']);
    var totalPage = seachText['total_page'];
    var totalCount = seachText['total_count'];

    var tab = '';
    var data = seachText['data'];
    //顯示列表資料
    tab += '<input type="hidden" id="form_file_formNo" value="' + data[0]['formNo'] + '" </input>';
    tab += '<table class=" contentsTable textCenter ">';
    tab += '<thead><tr>';
    tab += '<th class="width_60px">No</th>';
    tab += '<th>檔案內容</th>';
    tab += '</tr></thead>';
    var no = (seachText['page'] - 1) * seachText['page_count'];
    for (let i = 0; i < data.length; i++) {
        no++;
        tab += '<tr>';
        tab += '<td>' + no + '</td>';
        if (data[i]['fileType'] == "mp4") { //影片的話，使用超連結開啟，新分頁撥放影片
            tab += '<td>' + '<a href="' + data[i]['filePath'] + '" target="_blank">' + data[i]['fileName'] + '</a> ' + '</td>';
        } else if (data[i]['fileType'] == "jpg" || data[i]['fileType'] == "jpeg" || data[i]['fileType'] == "png") { //圖片顯示縮圖
            tab += '<td class="textCenter"><a href="' + data[i]['filePath'] + '" target="_blank"><img src="' + data[i]['filePath'] + '" class="img"></a>' + '</td>';
        } else {
            tab += '<td>-</td>';
        }
        tab += '</tr>';
    }
    tab += "</table>";
    $("#formfileTable").html(tab);

    // 清除筆數頁數select裡的所有option
    document.getElementById("form_file_listPageCount").innerHTML = "";
    document.getElementById("form_file_listPage").innerHTML = "";

    // 輸出資料筆數及頁數
    this_pageSelect(totalPage, page, pageCount, 'form_file_listPage', 'form_file_listPageCount');
    document.getElementById('form_file_totalCount').innerHTML = '資料筆數：' + totalCount;
}

// 標記資料
function formRemarkdataList(page, pageCount) {
    document.getElementById('errorMsg').value = "";
    var formNo = document.getElementById('formNo').value;
    var robotNo = document.getElementById('robotNo').value;
    var data_obj = {
        robotNo: robotNo,
        formNo: formNo,
        page: page,
        pageCount: pageCount
    };
    var result = call_api('dashboard_api/formRemarkdata', data_obj);
    if (result['status']) {
        showFormRemarkdata(result['data'], data_obj);
    }
}

// 標記資料_表格
function showFormRemarkdata(seachText, data_obj) {

    document.getElementById('dataTime').innerHTML = "時間：" + seachText['realtimeData'][0]['dataTime'];
    document.getElementById('sensorData').innerHTML = "姿態儀浮仰值：" + seachText['realtimeData'][0]['accPitch'] + " / 姿態儀翻轉值：" + seachText['realtimeData'][0]['accRoll'];
    document.getElementById('metersData').innerHTML = "距離：" + seachText['realtimeData'][0]['meters'] + "米";

    var page = parseInt(data_obj['page']);
    var pageCount = parseInt(data_obj['pageCount']);
    var totalPage = seachText['total_page'];
    var totalCount = seachText['total_count'];

    var tab = '';
    var markTab = '';
    var data = seachText['data'];
    //顯示列表資料
    tab += '<input type="hidden" id="formNo" value="' + data[0]['formNo'] + '" </input>';
    tab += '<table class=" contentsTable textCenter ">';
    tab += '<thead><tr>';
    tab += '<th class="width_80px">標記編號</th>';
    tab += '<th class="width_200px">時間</th>';
    tab += '<th>內容</th>';
    tab += '</tr></thead>';
    for (let i = 0; i < data.length; i++) {
        tab += '<tr>';
        tab += '<td>' + data[i]['id'] + '</td>';
        tab += '<td>' + data[i]['remarkTime'] + '</td>';
        tab += '<td>' + data[i]['content'] + '</td>';
        tab += '</tr>';
        var markId = 'mark_' + data[i]['id'];
        markTab += '<div name="markGroup" id="' + markId + '" class="mark" style="left:' + data[i]['x_axis'] + 'px;top:' + data[i]['y_axis'] + 'px"><span class="markNo">' + data[i]['id'] + '</span></div>';
    }
    tab += "</table>";
    $("#formRemarkTable").html(tab);
    $("#markDiv").html(markTab);

    // 清除筆數頁數select裡的所有option
    document.getElementById("listPageCount").innerHTML = "";
    document.getElementById("listPage").innerHTML = "";

    // 輸出資料筆數及頁數
    this_pageSelect(totalPage, page, pageCount, 'listPage', 'listPageCount');
    document.getElementById('totalCount').innerHTML = '資料筆數：' + totalCount;
}

//更換設備
function changeRobot_byRefresh() {
    var robotNo = document.getElementById('robotNo').value;

    var baseUrl = document.getElementById('base_url').value;
    var searchData = {
        robotNo: robotNo
    };
    searchData = JSON.stringify(searchData);
    var path = baseUrl + "dashboard/index";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

//更換設備
function getRobotRealTimeData() {
    var CADIMG_WIDTH = document.getElementById('CADIMG_WIDTH').value;
    var CADIMG_HEIGHT = document.getElementById('CADIMG_HEIGHT').value;

    var robotNo = document.getElementById('robotNo').value;
    var data_obj = {
        robotNo: robotNo
    };
    var result = call_api('dashboard_api/getRobotRealTimeData', data_obj);
    if (result['status']) {
        var data = result['data'];
        document.getElementById('workStatus').innerHTML = '作業中';
        document.getElementById("workStatus").style.color = "green";

        document.getElementById('workArea').innerHTML = data['formData'][0]['areaName'];
        document.getElementById("workArea").style.color = "black";

        document.getElementById('workPlace').innerHTML = data['formData'][0]['company'] + "(" + data['formData'][0]['wpName'] + ")";
        document.getElementById("workPlace").style.color = "black";

        document.getElementById('workUpdateTime').innerHTML = data['formData'][0]['startTime'];
        document.getElementById("workUpdateTime").style.color = "black";

        document.getElementById('formNo_label').innerHTML = data['formData'][0]['formNo'];
        document.getElementById("formNo_label").style.color = "black";

        //有工單
        if (data['formData'][0]) {
            document.getElementById("realTimeArea").classList.remove("none");

            document.getElementById('dataTime').innerHTML = '時間：' + data[0]['dataTime'];
            var tabImg = '';
            tabImg += '<img src="' + data['formData'][0]['cadImg'] + '"' + ' style="width:' + CADIMG_WIDTH + 'px;height:' + CADIMG_HEIGHT + 'px;"</img><div id="markDiv"></div>';

            $("#cadImgBox").html(tabImg);

            document.getElementById('sensorData').innerHTML = '姿態儀浮仰值：' + data[0]['accPitch'] + ' / ' + '姿態儀翻轉值：' + data[0]['accRoll'];
            document.getElementById('metersData').innerHTML = '距離：' + data[0]['meters'] + "米";


            //有標記資料
            if ((data['formData']['form_remark_data']['total_count']) > 0) {
                document.getElementById("formRemarkDiv").classList.remove("none");

                var tab = '';
                var markTab = '';
                var form_remark_data = data['formData']['form_remark_data']['data'];
                //顯示列表資料
                tab += '<input type="hidden" id="formNo" value="' + form_remark_data[0]['formNo'] + '" </input>';
                tab += '<table class=" contentsTable textCenter ">';
                tab += '<thead><tr>';
                tab += '<th class="width_80px">標記編號</th>';
                tab += '<th class="width_200px">時間</th>';
                tab += '<th>內容</th>';
                tab += '</tr></thead>';
                for (let i = 0; i < form_remark_data.length; i++) {
                    tab += '<tr>';
                    tab += '<td>' + form_remark_data[i]['id'] + '</td>';
                    tab += '<td>' + form_remark_data[i]['remarkTime'] + '</td>';
                    tab += '<td>' + form_remark_data[i]['content'] + '</td>';
                    tab += '</tr>';
                    var markId = 'mark_' + form_remark_data[i]['id'];
                    markTab += '<div name="markGroup" id="' + markId + '" class="mark" style="left:' + form_remark_data[i]['x_axis'] + 'px;top:' + form_remark_data[i]['y_axis'] + 'px"><span class="markNo">' + form_remark_data[i]['id'] + '</span></div>';
                }
                tab += "</table>";
                $("#formRemarkTable").html(tab);
                $("#markDiv").html(markTab);

                // 清除筆數頁數select裡的所有option
                document.getElementById("listPageCount").innerHTML = "";
                document.getElementById("listPage").innerHTML = "";
                // 輸出資料筆數及頁數
                this_pageSelect(data['formData']['form_remark_data']['total_page'], data['formData']['form_remark_data']['page'], data['formData']['form_remark_data']['page_count'], 'listPage', 'listPageCount');
                document.getElementById('totalCount').innerHTML = '資料筆數：' + data['formData']['form_remark_data']['total_count'];
            } else {
                document.getElementById("formRemarkDiv").classList.add("none");
            }

            //有上傳檔案
            if ((data['formData']['form_file']['total_count']) > 0) {
                document.getElementById("formfilekDiv").classList.remove("none");

                var tab = '';
                var form_file_data = data['formData']['form_file']['data'];
                //顯示列表資料
                tab += '<input type="hidden" id="form_file_formNo" value="' + form_file_data[0]['formNo'] + '" </input>';
                tab += '<table class=" contentsTable textCenter ">';
                tab += '<thead><tr>';
                tab += '<th class="width_60px">No</th>';
                tab += '<th >檔案內容</th>';
                tab += '</tr></thead>';
                var no = (data['formData']['form_file']['page'] - 1) * data['formData']['form_file']['page_count'];
                for (let i = 0; i < form_file_data.length; i++) {
                    no++;
                    tab += '<tr>';
                    tab += '<td>' + no + '</td>';
                    if (form_file_data[i]['fileType'] == "mp4") { //影片的話，使用超連結開啟，新分頁撥放影片
                        tab += '<td>' + '<a href="' + form_file_data[i]['filePath'] + '" target="_blank">' + form_file_data[i]['fileName'] + '</a> ' + '</td>';
                    } else if (form_file_data[i]['fileType'] == "jpg" || form_file_data[i]['fileType'] == "jpeg" || form_file_data[i]['fileType'] == "png") { //圖片顯示縮圖
                        tab += '<td class="textCenter"><a href="' + form_file_data[i]['filePath'] + '" target="_blank"><img src="' + form_file_data[i]['filePath'] + '" class="img"></a></td>';
                    } else {
                        tab += '<td>-</td>';
                    }
                    tab += '</tr>';
                }
                tab += "</table>";

                $("#formfileTable").html(tab);

                // 清除筆數頁數select裡的所有option
                document.getElementById("form_file_listPageCount").innerHTML = "";
                document.getElementById("form_file_listPage").innerHTML = "";
                // 輸出資料筆數及頁數
                this_pageSelect(data['formData']['form_file']['total_page'], data['formData']['form_file']['page'], data['formData']['form_file']['page_count'], 'form_file_listPage', 'form_file_listPageCount');
                document.getElementById('form_file_totalCount').innerHTML = '資料筆數：' + data['formData']['form_file']['total_count'];

            } else {
                document.getElementById("formfilekDiv").classList.add("none");
            }
        }

    } else { //無作業資料

        document.getElementById('workStatus').innerHTML = '無作業';
        document.getElementById("workStatus").style.color = "gray";

        document.getElementById('workArea').innerHTML = '無作業';
        document.getElementById("workArea").style.color = "gray";

        document.getElementById('workPlace').innerHTML = '無作業';
        document.getElementById("workPlace").style.color = "gray";

        document.getElementById('workUpdateTime').innerHTML = '無作業';
        document.getElementById("workUpdateTime").style.color = "gray";

        document.getElementById('formNo_label').innerHTML = '無作業';
        document.getElementById("formNo_label").style.color = "gray";

        document.getElementById("realTimeArea").classList.add("none");
    }
}

// 筆數頁數選單
function this_pageSelect(totalPage, page, pageCount, listPage, listPageCount) {
    var pageSelect = document.getElementById(listPage);
    var countSelect = document.getElementById(listPageCount);
    if (totalPage > 0) {
        for (var i = 1; i <= totalPage; i++) {
            addOption(pageSelect, i, i);
        }
    }
    $('#' + listPage).val(page);
    for (var i = 10; i <= 50; i = i + 10) {
        addOption(countSelect, i, i);
    }
    $('#' + listPageCount).val(pageCount);
}