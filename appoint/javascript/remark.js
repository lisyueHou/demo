// 取得列表資料
function dataList(page, pageCount) {
    document.getElementById('errorMsg').value = "";
    var content = document.getElementById('content').value;
    var data_obj = {
        content: content,
        page: page,
        pageCount: pageCount
    };
    var result = call_api('remark_api/getRemark', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        showDataList(data, data_obj);
    } else {
        var msg = '查無標記備註資料';
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
    var data = seachText['remark'];
    var count = json_count(data); // 資料筆數
    var tab = '';
    if (count != 0) {
        //顯示列表資料
        tab += '<table class="contentsTable">';
        tab += '<tr>';
        tab += '<th class="width_40px">No</th>';
        tab += '<th>標記備註內容</th>';
        tab += '<th class="width_120px">功能</th>';
        tab += '</tr>';

        var no = pageStart;
        data.forEach(data => {
            no++;
            var rowStr = JSON.stringify(data);
            tab += '<tr>';
            tab += '<td class="textCenter">' + no + '</td>';
            tab += '<td>' + data['content'] + '</td>';

            var editBtn = "<button class='button modGreen' onclick='gotoEdit(" + rowStr + ");'>編輯</button>";
            var delBtn = "<button class='button removeRed' onclick='delRemark(" + rowStr + "," + count + ");'>刪除</button>";
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
        var msg = '查無標記備註資料';
        noDataList(msg);
    }
}

// 回功能首頁
function gotoHome(searchData) {
    var baseUrl = document.getElementById('base_url').value;
    searchData = JSON.stringify(searchData);
    var path = baseUrl + "remark/index";
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
        content: document.getElementById('content').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);

    var path = baseUrl + "remark/add";
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
        content: document.getElementById('content').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "remark/edit";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'data', data);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

//取消搜尋資料
function cancelSearch() {
    document.getElementById('content').value = '';
    dataList(1, 10);
}

//新增標記備註資料
function saveAdd() {
    var baseUrl = document.getElementById('base_url').value;
    document.getElementById("errorMsg").innerHTML = "";
    var content = document.getElementById('content').value;

    //欄位資料檢查
    if (content.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入標記備註內容";
        return;
    }

    var data_obj = {
        content: content
    };

    var result = call_api('remark_api/addRemark', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.href = baseUrl + "remark/index";
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//編輯標記備註資料
function saveEdit(searchData) {
    document.getElementById("errorMsg").innerHTML = "";
    var id = document.getElementById('id').value;
    var content = document.getElementById('content').value;

    //欄位資料檢查
    if (content.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入標記備註內容";
        return;
    }

    var data_obj = {
        id: id,
        content: content
    };
    var result = call_api('remark_api/editRemark', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除標記備註資料
function delRemark(data, pageTotalCount) {
    if (!confirm("確定刪除?")) {
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
        content: document.getElementById('content').value,
        page: page,
        pageCount: document.getElementById('listPageCount').value
    };

    var result = call_api('remark_api/delRemark', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}