// 取得列表資料
function dataList(page, pageCount) {
    document.getElementById('errorMsg').value = "";
    var groupClass = document.getElementById('groupClass').value;
    var name = document.getElementById('name').value;

    var data_obj = {
        class: groupClass,
        name: name,
        page: page,
        pageCount: pageCount
    };

    var result = call_api('groups_api/getGroups', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        showDataList(data, data_obj);
    } else {
        var msg = '查無群組資料';
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
    var data = seachText['groups'];
    var count = json_count(data); // 資料筆數
    var tab = '';
    if (count != 0) {
        //顯示列表資料
        tab += '<table class="contentsTable">';
        tab += '<tr>';
        tab += '<th class="width_40px">No</th>';
        tab += '<th>群組名稱</th>';
        tab += '<th>群組類別</th>';
        tab += '<th class="width_80px">群組帳號</th>';
        tab += '<th class="width_80px">群組權限</th>';
        tab += '<th class="width_120px">功能</th>';
        tab += '</tr>';

        var no = pageStart;
        data.forEach(data => {
            no++;
            var rowStr = JSON.stringify(data);
            tab += '<tr>';
            tab += '<td class="textCenter">' + no + '</td>';
            tab += '<td>' + data['name'] + '</td>';

            //群組類別
            switch (data['class']) {
                case '1':
                    tab += '<td>內部群組</td>';
                    break;
                case '2':
                    tab += '<td>顧客群組</td>';
                    break;
                default:
                    tab += '<td>-</td>';
                    break;
            }

            //群組帳號
            var acclistBtn = "<button class='button gray' onclick='viewAcclist(" + rowStr + ");'>檢視</button>";
            tab += '<td class="textCenter">' + acclistBtn + '</td>';

            //群組權限
            var permBtn = "<button class='button gray' onclick='viewPrem(" + rowStr + ");'>檢視</button>";
            tab += '<td class="textCenter">' + permBtn + '</td>';


            var editBtn = "<button class='button modGreen' onclick='gotoEdit(" + rowStr + ");'>編輯</button>";
            var delBtn = "<button class='button removeRed' onclick='delGroup(" + rowStr + "," + count + ");'>刪除</button>";
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
        var msg = '查無群組資料';
        noDataList(msg);
    }
}

//檢視群組帳號
function viewAcclist(data) {
    document.getElementById('groupName').innerHTML = data['name'];

    //取得群組帳號清單
    var data_obj = {
        groupId: data['id'],
        groupClass: data['class']
    };
    var result = call_api('groups_api/getGroupAccList', data_obj);
    if (result['status']) {
        var accdata = result['data'];
        var count = json_count(accdata); // 資料筆數
        if (count != 0) {
            //顯示列表資料
            var tab = '';
            tab += '<table class="contentsTable">';
            tab += '<tr>';
            tab += '<th class="width_40px">No</th>';
            tab += '<th>帳號</th>';
            tab += '<th>使用人員[編號]</th>';
            tab += '<th class="width_80px">帳號狀態</th>';
            tab += '</tr>';

            var no = 0;
            accdata.forEach(row => {
                no++;
                tab += '<tr>';
                tab += '<td class="textCenter">' + no + '</td>';
                tab += '<td>' + row['account'] + '</td>';
                tab += '<td>' + row['userName'] + '[' + row['userNo'] + ']</td>';
                if (row['isEnable'] == 1) {
                    tab += '<td>啟用</td>';
                } else {
                    tab += '<td>關閉</td>';
                }
                tab += '</tr>';
            });
            tab += '</table>';
            $("#groupAccList").html(tab);
        } else {
            var tab = '<span class="alertMsg">無群組帳號資料<span>';
            $("#groupAccList").html(tab);
        }
    } else {
        var tab = '<span class="alertMsg">' + result['message'] + '<span>';
        $("#groupAccList").html(tab);
    }
    display_window("acclist_hidebox");
}

//檢視群組權限
function viewPrem(data) {
    document.getElementById('groupPremName').innerHTML = data['name'];

    //取得群組權限資料
    var data_obj = {
        groupId: data['id']
    };
    var result = call_api('groups_api/getGroupPremList', data_obj);

    var premdata = result['data'];
    var count = json_count(premdata); // 資料筆數
    if (count != 0) {
        //顯示列表資料
        var tab = '';
        var no = 0;
        var cMainFunction = '';
        premdata.forEach(row => {
            no++;
            if (row['isOption'] == 1) {
                if (row['cMainFunction'] != cMainFunction) {
                    if (cMainFunction.length != 0) {
                        tab += '<br>';
                    }
                    tab += '<div class="premTitle">' + row['cMainFunction'] + '</div>';
                    cMainFunction = row['cMainFunction'];
                }
                tab += '<div class="premText">' + row['cSubFunction'] + '</div>';
            }
        });

        $("#groupPremList").html(tab);
    } else {
        var tab = '<span class="alertMsg">無群組權限資料<span>';
        $("#groupPremList").html(tab);
    }

    display_window("prem_hidebox");
}

// 回功能首頁
function gotoHome(searchData) {
    var baseUrl = document.getElementById('base_url').value;
    searchData = JSON.stringify(searchData);
    var path = baseUrl + "groups/index";
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
        class: document.getElementById('groupClass').value,
        name: document.getElementById('name').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);

    var path = baseUrl + "groups/add";
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
        class: document.getElementById('groupClass').value,
        name: document.getElementById('name').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "groups/edit";
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
    document.getElementById('groupClass').value = '';
    document.getElementById('name').value = '';
    dataList(1, 10);
}

//新增員工資料
function saveAdd() {
    var baseUrl = document.getElementById('base_url').value;
    document.getElementById("errorMsg").innerHTML = "";
    var name = document.getElementById('name').value;
    var groupClass = document.getElementById('groupClass').value;

    // 取得選擇的功能id
    var obj = document.getElementsByClassName('premCheckbox');
    var len = obj.length;
    var authorization = [];
    for (i = 0; i < len; i++) {
        if (obj[i].checked == true) {
            authorization.push(obj[i].value);
        }
    };

    //欄位資料檢查
    if (name.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入群組名稱";
        return;
    }
    if (groupClass == "0") {
        document.getElementById("errorMsg").innerHTML = "請選擇群組類別";
        return;
    }

    var data_obj = {
        name: name,
        class: groupClass,
        authorization: authorization
    };

    var result = call_api('groups_api/addGroup', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.href = baseUrl + "groups/index";
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//編輯員工資料
function saveEdit(searchData) {
    document.getElementById("errorMsg").innerHTML = "";
    var id = document.getElementById('id').value;
    var name = document.getElementById('name').value;
    var groupClass = document.getElementById('groupClass').value;

    // 取得選擇的功能id
    var obj = document.getElementsByClassName('premCheckbox');
    var len = obj.length;
    var authorization = [];
    for (i = 0; i < len; i++) {
        if (obj[i].checked == true) {
            authorization.push(obj[i].value);
        }
    };

    //欄位資料檢查
    if (name.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入群組名稱";
        return;
    }
    if (groupClass == "0") {
        document.getElementById("errorMsg").innerHTML = "請選擇群組類別";
        return;
    }

    var data_obj = {
        id:id,
        name: name,
        class: groupClass,
        authorization: authorization
    };
    var result = call_api('groups_api/editGroup', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除員工資料
function delGroup(data, pageTotalCount) {
    if (!confirm("確定刪除「" + data['name'] + "」?")) {
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
        class: document.getElementById('groupClass').value,
        name: document.getElementById('name').value,
        page: page,
        pageCount: document.getElementById('listPageCount').value
    };

    var result = call_api('groups_api/delGroup', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}