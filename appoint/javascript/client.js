// 取得列表資料
function dataList(page, pageCount) {
    document.getElementById('errorMsg').value = "";
    var clientNo = document.getElementById('clientNo').value;
    var company = document.getElementById('company').value;
    var companyId = document.getElementById('companyId').value;

    var data_obj = {
        clientNo: clientNo,
        company: company,
        companyId: companyId,
        page: page,
        pageCount: pageCount
    };
    var result = call_api('client_api/getClient', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        showDataList(data, data_obj);
    } else {
        var msg = '查無顧客資料';
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
    var data = seachText['client'];
    var count = json_count(data); // 資料筆數
    var tab = '';
    if (count != 0) {
        //顯示列表資料
        tab += '<table class="contentsTable">';
        tab += '<tr>';
        tab += '<th class="width_40px">No</th>';
        tab += '<th>顧客編號</th>';
        tab += '<th>公司名稱</th>';
        tab += '<th>統一編號</th>';
        tab += '<th>顧客代表</th>';
        tab += '<th class="width_80px">聯絡資訊</th>';
        tab += '<th class="width_60px">備註</th>';
        tab += '<th class="width_120px">功能</th>';
        tab += '</tr>';

        var no = pageStart;
        data.forEach(data => {
            no++;
            var rowStr = JSON.stringify(data);
            tab += '<tr>';
            tab += '<td class="textCenter">' + no + '</td>';
            tab += '<td>' + data['clientNo'] + '</td>';
            tab += '<td>' + data['company'] + '</td>';
            tab += '<td>' + data['companyId'] + '</td>';
            tab += '<td>' + data['name'] + '</td>';

            //聯絡資訊
            var conBtn = "<button class='button gray' onclick='viewCon(" + rowStr + ");'>檢視</button>";
            tab += '<td class="textCenter">' + conBtn + '</td>';

            //備註
            if (!data['remark']) {
                tab += '<td class="textCenter">-</td>';
            } else {
                var remarkBtn = "<button class='button gray' onclick='viewRemark(" + rowStr + ");'>檢視</button>";
                tab += '<td class="textCenter">' + remarkBtn + '</td>';
            }

            var editBtn = "<button class='button modGreen' onclick='gotoEdit(" + rowStr + ");'>編輯</button>";
            var delBtn = "<button class='button removeRed' onclick='delClient(" + rowStr + "," + count + ");'>刪除</button>";
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
        var msg = '查無顧客資料';
        noDataList(msg);
    }
}

//聯絡資訊
function viewCon(data) {
    document.getElementById('name').innerHTML = data['name'];
    document.getElementById('phone').innerHTML = data['phone'];
    document.getElementById('email').innerHTML = data['email'];
    document.getElementById('conName').innerHTML = data['conName'];
    document.getElementById('conPhone').innerHTML = data['conPhone'];
    document.getElementById('conEmail').innerHTML = data['conEmail'];
    display_window("con_hidebox");
}

// 回功能首頁
function gotoHome(searchData) {
    var baseUrl = document.getElementById('base_url').value;
    searchData = JSON.stringify(searchData);
    var path = baseUrl + "client/index";
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
        clientNo: document.getElementById('clientNo').value,
        company: document.getElementById('company').value,
        companyId: document.getElementById('companyId').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);

    var path = baseUrl + "client/add";
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
        clientNo: document.getElementById('clientNo').value,
        company: document.getElementById('company').value,
        companyId: document.getElementById('companyId').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "client/edit";
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
    document.getElementById('clientNo').value = '';
    document.getElementById('company').value = '';
    document.getElementById('companyId').value = '';
    dataList(1, 10);
}

//新增顧客資料
function saveAdd() {
    var baseUrl = document.getElementById('base_url').value;
    document.getElementById("errorMsg").innerHTML = "";
    var clientNo = document.getElementById('clientNo').value;
    var company = document.getElementById('company').value;
    var companyId = document.getElementById('companyId').value;
    var address = document.getElementById('address').value;
    var name = document.getElementById('name').value;
    var phone = document.getElementById('phone').value;
    var email = document.getElementById('email').value;
    var conName = document.getElementById('conName').value;
    var conPhone = document.getElementById('conPhone').value;
    var conEmail = document.getElementById('conEmail').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (clientNo.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入顧客編號";
        return;
    }
    if (company.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入公司名稱";
        return;
    }

    var data_obj = {
        clientNo: clientNo,
        company: company,
        companyId: companyId,
        address: address,
        name: name,
        phone: phone,
        email: email,
        conName: conName,
        conPhone: conPhone,
        conEmail: conEmail,
        remark: remark
    };

    var result = call_api('client_api/addClient', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.href = baseUrl + "client/index";
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//編輯顧客資料
function saveEdit(searchData) {
    document.getElementById("errorMsg").innerHTML = "";
    var id = document.getElementById('id').value;
    var clientNo = document.getElementById('clientNo').value;
    var company = document.getElementById('company').value;
    var companyId = document.getElementById('companyId').value;
    var address = document.getElementById('address').value;
    var name = document.getElementById('name').value;
    var phone = document.getElementById('phone').value;
    var email = document.getElementById('email').value;
    var conName = document.getElementById('conName').value;
    var conPhone = document.getElementById('conPhone').value;
    var conEmail = document.getElementById('conEmail').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (clientNo.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入顧客編號";
        return;
    }
    if (company.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入公司名稱";
        return;
    }

    var data_obj = {
        id: id,
        clientNo: clientNo,
        company: company,
        companyId: companyId,
        address: address,
        name: name,
        phone: phone,
        email: email,
        conName: conName,
        conPhone: conPhone,
        conEmail: conEmail,
        remark: remark
    };
    var result = call_api('client_api/editClient', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除顧客資料
function delClient(data, pageTotalCount) {
    if (!confirm("確定刪除「" + data['company'] + "」?")) {
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
        clientNo: document.getElementById('clientNo').value,
        company: document.getElementById('company').value,
        companyId: document.getElementById('companyId').value,
        page: page,
        pageCount: document.getElementById('listPageCount').value
    };

    var result = call_api('client_api/delClient', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}