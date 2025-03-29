// 取得列表資料
function dataList(page, pageCount) {
    document.getElementById('errorMsg').value = "";
    var account = document.getElementById('account').value;
    var name = document.getElementById('userName').value;

    var data_obj = {
        account: account,
        userName: name,
        page: page,
        pageCount: pageCount
    };

    var result = call_api('users_api/getUser', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        showDataList(data, data_obj);
    } else {
        var msg = '查無帳號資料';
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
    var data = seachText['users'];
    var count = json_count(data); // 資料筆數
    var tab = '';
    if (count != 0) {
        //顯示列表資料
        tab += '<table class="contentsTable">';
        tab += '<tr>';
        tab += '<th class="width_40px">No</th>';
        tab += '<th>帳號</th>';
        tab += '<th>帳號群組</th>';
        tab += '<th>使用人員</th>';
        tab += '<th class="width_80px">帳號狀態</th>';
        tab += '<th class="width_60px">備註</th>';
        tab += '<th class="width_120px">功能</th>';
        tab += '</tr>';

        var no = pageStart;
        data.forEach(data => {
            no++;
            var rowStr = JSON.stringify(data);
            tab += '<tr>';
            tab += '<td class="textCenter">' + no + '</td>';
            tab += '<td>' + data['account'] + '</td>';
            tab += '<td>' + data['groupName'] + '</td>';
            tab += '<td>' + data['userName'] + '</td>';

            //帳號狀態
            if (data['isEnable'] == 1) {
                tab += '<td class="textCenter textGreen">開啟</td>';
            } else {
                tab += '<td class="textCenter">關閉</td>';
            }

            //備註
            if (!data['remark']) {
                tab += '<td class="textCenter">-</td>';
            } else {
                var remarkBtn = "<button class='button gray' onclick='viewRemark(" + rowStr + ");'>檢視</button>";
                tab += '<td class="textCenter">' + remarkBtn + '</td>';
            }

            var editBtn = "<button class='button modGreen' onclick='gotoEdit(" + rowStr + ");'>編輯</button>";
            var delBtn = "<button class='button removeRed' onclick='delUser(" + rowStr + "," + count + ");'>刪除</button>";
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
        var msg = '查無帳號資料';
        noDataList(msg);
    }
}

// 回功能首頁
function gotoHome(searchData) {
    var baseUrl = document.getElementById('base_url').value;
    searchData = JSON.stringify(searchData);
    var path = baseUrl + "users/index";
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
        account: document.getElementById('account').value,
        userName: document.getElementById('userName').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);

    var path = baseUrl + "users/add";
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
        account: document.getElementById('account').value,
        userName: document.getElementById('userName').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "users/edit";
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
    document.getElementById('account').value = '';
    document.getElementById('userName').value = '';
    dataList(1, 10);
}

//取得使用者選單
function getUserList(groupId) {
    if (groupId == '0') {
        var tab = '<option value="0">請選擇使用人員</option>';
        $("#personId").html(tab);
        return;
    }
    var data_obj = {
        groupId: groupId
    };
    var result = call_api('users_api/getUserList', data_obj);
    if (result['status']) {
        var data = result['data'];
        var tab = '';
        data.forEach(function (row) {
            tab += '<option value="' + row['id'] + '">' + row['userName'] + '[' + row['userNo'] + ']</td>';
        });
        $("#personId").html(tab);
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//新增帳號資料
function saveAdd() {
    var baseUrl = document.getElementById('base_url').value;
    document.getElementById("errorMsg").innerHTML = "";
    var account = document.getElementById('account').value;
    var password = document.getElementById('password').value;
    var passwordCheck = document.getElementById('passwordCheck').value;
    var enable = $("input[type=radio][name=enable]:checked").val();
    var groupId = document.getElementById('groupId').value;
    var personId = document.getElementById('personId').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (account.length < 4) {
        document.getElementById("errorMsg").innerHTML = "帳號至少輸入4個英文或數字";
        return;
    }
    if (password.length < 4) {
        document.getElementById("errorMsg").innerHTML = "密碼至少輸入4個英文或數字";
        return;
    }
    if (passwordCheck.length < 4) {
        document.getElementById("errorMsg").innerHTML = "確認密碼至少輸入4個英文或數字";
        return;
    }
    if (groupId == "0") {
        document.getElementById("errorMsg").innerHTML = "請選擇群組";
        return;
    }
    if (personId == "0") {
        document.getElementById("errorMsg").innerHTML = "請選擇使用人員";
        return;
    }
    if (password != passwordCheck) {
        document.getElementById("errorMsg").innerHTML = "密碼與確認密碼不相符";
        return;
    }

    var data_obj = {
        account: account,
        password: password,
        enable: enable,
        groupId: groupId,
        personId: personId,
        remark: remark
    };

    var result = call_api('users_api/addUser', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.href = baseUrl + "users/index";
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//編輯帳號資料
function saveEdit(searchData) {
    document.getElementById("errorMsg").innerHTML = "";
    var id = document.getElementById('id').value;
    var account = document.getElementById('account').value;
    var oldAccount = document.getElementById('oldAccount').value;
    var enable = $("input[type=radio][name=enable]:checked").val();
    var groupId = document.getElementById('groupId').value;
    var personId = document.getElementById('personId').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (account.length < 4) {
        document.getElementById("errorMsg").innerHTML = "帳號至少輸入4個英文或數字";
        return;
    }
    if (groupId == "0") {
        document.getElementById("errorMsg").innerHTML = "請選擇群組";
        return;
    }
    if (personId == "0") {
        document.getElementById("errorMsg").innerHTML = "請選擇使用人員";
        return;
    }

    var data_obj = {
        id: id,
        account: account,
        oldAccount: oldAccount,
        enable: enable,
        groupId: groupId,
        personId: personId,
        remark: remark
    };
    var result = call_api('users_api/editUser', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除帳號
function delUser(data, pageTotalCount) {
    if (!confirm("確定刪除「" + data['account'] + "」?")) {
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
        account: document.getElementById('account').value,
        userName: document.getElementById('userName').value,
        page: page,
        pageCount: document.getElementById('listPageCount').value
    };

    var result = call_api('users_api/delUser', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}

//開啟變更密碼視窗
function openPass(id) {
    document.getElementById('password').value = '';
    document.getElementById('passwordCheck').value = '';
    document.getElementById('errorMsgPass').innerHTML = '';
    document.getElementById('passAccId').value = id;
    display_window("pass_hidebox");
}

//變更密碼
function savePass() {
    var id = document.getElementById('passAccId').value;
    var password = document.getElementById('password').value;
    var passwordCheck = document.getElementById('passwordCheck').value;

    //欄位資料檢查
    if (password.length < 4) {
        document.getElementById("errorMsgPass").innerHTML = "密碼至少輸入4個英文或數字";
        return;
    }
    if (passwordCheck.length < 4) {
        document.getElementById("errorMsgPass").innerHTML = "確認密碼至少輸入4個英文或數字";
        return;
    }
    if (password != passwordCheck) {
        document.getElementById("errorMsgPass").innerHTML = "密碼與確認密碼不相符";
        return;
    }

    var data_obj = {
        id: id,
        password: password
    };
    var result = call_api('users_api/editPass', data_obj);
    if (result['status']) {
        document.getElementById("errorMsgPass").innerHTML = result['message'];
        sleep(1000).then(() => {
            hide('pass_hidebox');
        });
    } else {
        document.getElementById("errorMsgPass").innerHTML = result['message'];
    }
}
