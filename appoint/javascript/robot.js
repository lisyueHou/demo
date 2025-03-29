// 取得列表資料
function dataList(page, pageCount) {
    document.getElementById('errorMsg').value = "";
    var robotNo = document.getElementById('robotNo').value;
    var name = document.getElementById('name').value;
    var data_obj = {
        robotNo: robotNo,
        name: name,
        page: page,
        pageCount: pageCount
    };
    var result = call_api('robot_api/getRobot', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        showDataList(data, data_obj);
    } else {
        var msg = '查無設備資料';
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
    var data = seachText['robot'];
    var count = json_count(data); // 資料筆數
    var tab = '';
    if (count != 0) {
        //顯示列表資料
        tab += '<table class="contentsTable">';
        tab += '<tr>';
        tab += '<th class="width_40px">No</th>';
        tab += '<th>設備編號</th>';
        tab += '<th>設備名稱</th>';
        tab += '<th class="width_80px">設備狀態</th>';
        tab += '<th class="width_100px">串流影像網址</th>';
        tab += '<th class="width_60px">備註</th>';
        tab += '<th class="width_120px">功能</th>';
        tab += '</tr>';

        var no = pageStart;
        data.forEach(data => {
            no++;
            var rowStr = JSON.stringify(data);
            tab += '<tr>';
            tab += '<td class="textCenter">' + no + '</td>';
            tab += '<td>' + data['robotNo'] + '</td>';
            tab += '<td>' + data['name'] + '</td>';

            //設備狀態
            if (data['state'] == 0) {
                tab += '<td class="textCenter textGreen">開啟</td>';
            } else {
                tab += '<td class="textCenter">關閉</td>';
            }

            //串流影像網址
            if (!data['videoUrl']) {
                tab += '<td class="textCenter">-</td>';
            } else {
                var videoUrlBtn = "<button class='button gray' onclick='viewVideoUrl(" + rowStr + ");'>檢視</button>";
                tab += '<td class="textCenter">' + videoUrlBtn + '</td>';
            }

            //備註
            if (!data['remark']) {
                tab += '<td class="textCenter">-</td>';
            } else {
                var remarkBtn = "<button class='button gray' onclick='viewRemark(" + rowStr + ");'>檢視</button>";
                tab += '<td class="textCenter">' + remarkBtn + '</td>';
            }

            var editBtn = "<button class='button modGreen' onclick='gotoEdit(" + rowStr + ");'>編輯</button>";
            var delBtn = "<button class='button removeRed' onclick='delRobot(" + rowStr + "," + count + ");'>刪除</button>";
            tab += '<td class="textCenter">' + editBtn + delBtn + '</td>';
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
        var msg = '查無設備資料';
        noDataList(msg);
    }
}

// 回功能首頁
function gotoHome(searchData) {
    var baseUrl = document.getElementById('base_url').value;
    searchData = JSON.stringify(searchData);
    var path = baseUrl + "robot/index";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

// 轉至新增頁面
function gotoAdd() {
    var baseUrl = document.getElementById('base_url').value;

    //頁面資料
    var searchData = {
        robotNo: document.getElementById('robotNo').value,
        name: document.getElementById('name').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);

    var path = baseUrl + "robot/add";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

// 轉至編輯頁面
function gotoEdit(data) {
    var baseUrl = document.getElementById('base_url').value;

    //頁面資料
    var searchData = {
        robotNo: document.getElementById('robotNo').value,
        name: document.getElementById('name').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "robot/edit";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'data', data);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

//串流影像網址
function viewVideoUrl(data) {
    document.getElementById('videoUrl').innerHTML = data['videoUrl'];
    display_window("url_hidebox");
}

//取消搜尋資料
function cancelSearch() {
    document.getElementById('robotNo').value = '';
    document.getElementById('name').value = '';
    dataList(1, 10);
}

//新增設備資料
function saveAdd() {
    var baseUrl = document.getElementById('base_url').value;
    document.getElementById("errorMsg").innerHTML = "";
    var robotNo = document.getElementById('robotNo').value;
    var name = document.getElementById('name').value;
    var state = $("input[type=radio][name=state]:checked").val();
    var videoUrl = document.getElementById('videoUrl').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (robotNo.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入設備編號";
        return;
    }
    if (name.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入設備名稱";
        return;
    }

    var data_obj = {
        robotNo: robotNo,
        name: name,
        state: state,
        videoUrl: videoUrl,
        remark: remark
    };

    var result = call_api('robot_api/addRobot', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.href = baseUrl + "robot/index";
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//編輯設備資料
function saveEdit(searchData) {
    document.getElementById("errorMsg").innerHTML = "";
    var id = document.getElementById('id').value;
    var robotNo = document.getElementById('robotNo').value;
    var name = document.getElementById('name').value;
    var state = $("input[type=radio][name=state]:checked").val();
    var videoUrl = document.getElementById('videoUrl').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (robotNo.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入設備編號";
        return;
    }
    if (name.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入設備名稱";
        return;
    }

    var data_obj = {
        id: id,
        robotNo: robotNo,
        name: name,
        state: state,
        videoUrl: videoUrl,
        remark: remark
    };
    var result = call_api('robot_api/editRobot', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除設備資料
function delRobot(data, pageTotalCount) {
    if (!confirm("確定刪除設備「" + data['name'] + "」?")) {
        return;
    }

    //判斷頁面是否為最後一筆，如果是頁數減1
    var page = document.getElementById('listPage').value;
    if (page > 1) {
        if (pageTotalCount == 1) {
            page = page - 1;
        }
    }

    //頁面資料
    var searchData = {
        robotNo: document.getElementById('robotNo').value,
        name: document.getElementById('name').value,
        page: page,
        pageCount: document.getElementById('listPageCount').value
    };

    var result = call_api('robot_api/delRobot', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}