// 取得列表資料
function dataList(page, pageCount) {
    document.getElementById('errorMsg').value = "";
    var areaId = document.getElementById('areaId').value;
    var workPlace = document.getElementById('workPlace').value;
    var company = document.getElementById('company').value;
    var data_obj = {
        areaId: areaId,
        workPlace: workPlace,
        company: company,
        page: page,
        pageCount: pageCount
    };
    var result = call_api('work_place_api/getWorkPlace', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        showDataList(data, data_obj);
    } else {
        var msg = '查無作業區域資料';
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
    var data = seachText['work_place'];
    var count = json_count(data); // 資料筆數
    var tab = '';
    if (count != 0) {
        //顯示列表資料
        tab += '<table class="contentsTable">';
        tab += '<tr>';
        tab += '<th class="width_40px">No</th>';
        tab += '<th>作業區域</th>';
        tab += '<th>作業地點</th>';
        tab += '<th>顧客名稱</th>';
        tab += '<th class="width_160px">管線圖</th>';
        tab += '<th class="width_80px">地圖位置</th>';
        tab += '<th class="width_60px">備註</th>';
        tab += '<th class="width_120px">功能</th>';
        tab += '</tr>';

        var no = pageStart;
        data.forEach(data => {
            no++;
            var rowStr = JSON.stringify(data);
            tab += '<tr>';
            tab += '<td class="textCenter">' + no + '</td>';
            tab += '<td>' + data['areaName'] + '</td>';
            tab += '<td>' + data['workPlace'] + '</td>';
            tab += '<td>' + data['company'] + '</td>';

            //管線圖
            if (!data['cadImg']) {
                tab += '<td class="textCenter">-</td>';
            } else {
                var cadSetBtn = "<button class='button orange' onclick='gotoCadSet(" + rowStr + ");'>設定路徑</button>";
                tab += '<td class="textCenter"><a href="' + data['imgPath'] + '" target="_blank"><img src="' + data['imgPath'] + '" class="img"></a>' + cadSetBtn + '</td>';
            }

            //地圖位置
            if (!data['latitude'] || !data['longitude']) {
                tab += '<td class="textCenter">-</td>';
            } else {
                var mapBtn = "<button class='button gray' onclick='viewMap( " + rowStr + ");'>檢視</button>";
                tab += '<td class="textCenter">' + mapBtn + '</td>';
            }

            //備註
            if (!data['remark']) {
                tab += '<td class="textCenter">-</td>';
            } else {
                var remarkBtn = "<button class='button gray' onclick='viewRemark(" + rowStr + ");'>檢視</button>";
                tab += '<td class="textCenter">' + remarkBtn + '</td>';
            }

            var editBtn = "<button class='button modGreen' onclick='gotoEdit(" + rowStr + ");'>編輯</button>";
            var delBtn = "<button class='button removeRed' onclick='delWorkPlace(" + rowStr + "," + count + ");'>刪除</button>";
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
        var msg = '查無作業區域資料';
        noDataList(msg);
    }
}

// 回功能首頁
function gotoHome(searchData) {
    var baseUrl = document.getElementById('base_url').value;
    searchData = JSON.stringify(searchData);
    var path = baseUrl + "work_place/index";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

// 轉至CAD路徑設定頁面
function gotoCadSet(data) {
    var baseUrl = document.getElementById('base_url').value;

    //頁面資料
    var searchData = {
        areaId: document.getElementById('areaId').value,
        workPlace: document.getElementById('workPlace').value,
        company: document.getElementById('company').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);

    var path = baseUrl + "work_place/setcad";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'id', data['id']);
    form = creatInput(form, 'imgPath', data['imgPath']);
    form = creatInput(form, 'coordinate', data['coordinate']);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

// 轉至新增頁面
function gotoAdd() {
    var baseUrl = document.getElementById('base_url').value;

    //頁面資料
    var searchData = {
        areaId: document.getElementById('areaId').value,
        workPlace: document.getElementById('workPlace').value,
        company: document.getElementById('company').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);

    var path = baseUrl + "work_place/add";
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
        areaId: document.getElementById('areaId').value,
        workPlace: document.getElementById('workPlace').value,
        company: document.getElementById('company').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "work_place/edit";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'data', data);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

//新增標記點位
function creatMark(x, y, no) {
    var markId = 'mark_' + no;
    var div = document.createElement('div');
    div.id = markId;
    div.className = 'mark';
    div.style.left = x + 'px';
    div.style.top = y + 'px';
    document.getElementById('cadImgBox').appendChild(div);
    var markStr = '<span class="markNo">' + no + '</span>';
    $('#' + markId).append(markStr);
}

//新增資料列
function creatMarkData(x, y, no) {
    var tab = '';
    if (no == 1) {
        tab += '<thead><tr>';
        tab += '<th class="width_40px">編號</th>';
        tab += '<th>X座標</th>';
        tab += '<th>Y座標</th>';
        tab += '<th>位置距離</th>';
        tab += '<th>功能</th>';
        tab += '</tr></thead>';
    }
    tab += '<tr id="tr_' + no + '">';
    tab += '<td>' + no + '</td>';
    tab += '<td>' + x + '</td>';
    tab += '<td>' + y + '</td>';
    tab += '<td><input type="number" id="meters_' + no + '" value="0" min="0">米</td>';
    tab += '<td><button class="button removeRed" onclick="removeMark(' + no + ')">移除標記</button></td>';
    tab += '</tr>';
    $("#markListTable").append(tab);
}

//移除標記
function removeMark(no) {
    var markId = 'mark_' + no;
    var trId = 'tr_' + no;
    document.getElementById(markId).style.display = 'none';//隱藏標記
    $('#' + trId).closest('tr').remove();//刪除表格列
}

//儲存CAD圖路徑
function saveCadImg(id, searchData) {
    document.getElementById("errorMsg").innerHTML = "";
    var table = document.getElementById('markListTable');
    var tbody = table.tBodies[0];
    if (typeof tbody === 'undefined') {
        document.getElementById("errorMsg").innerHTML = "請至少標記一個座標路徑";
        return;
    }
    var tr = tbody.rows;
    var coordinate = [];
    for (var i = 0; i < (tr.length); i++) {
        var no = tr[i].getElementsByTagName("td")[0].innerHTML;
        var metersId = 'meters_' + no;
        var meters = parseFloat(document.getElementById(metersId).value);
        if (meters < 0) {
            document.getElementById("errorMsg").innerHTML = "「編號" + no + "」位置距離請輸入正確數字";
            return;
        }
        var axisId = i + 1;
        coordinate.push({
            id: axisId,
            x_axis: tr[i].getElementsByTagName("td")[1].innerHTML,
            y_axis: tr[i].getElementsByTagName("td")[2].innerHTML,
            meters: meters
        });
    }

    if (coordinate.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請至少標記一個座標路徑";
        return;
    }
    var data_obj = {
        id: id,
        coordinate: coordinate
    };
    var result = call_api('work_place_api/updateCadImg', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//地圖位置
function viewMap(data) {
    var latitude = data['latitude'];//緯度
    var longitude = data['longitude'];//經度
    var workPlace = data['workPlace'];//作業地點
    document.getElementById('workPlaceName').innerHTML = workPlace;
    document.getElementById('iframeMap').src = "https://maps.google.com.tw/maps?f=q&hl=zh-TW&geocode=&q=" + latitude + "," + longitude + "&z=16&output=embed&t=&markers=color:blue>";
    display_window("viewMap_hidebox");
}

//取消搜尋資料
function cancelSearch() {
    document.getElementById('areaId').value = '';
    document.getElementById('workPlace').value = '';
    document.getElementById('company').value = '';
    dataList(1, 10);
}

//新增作業區域資料
function saveAdd() {
    var baseUrl = document.getElementById('base_url').value;
    document.getElementById("errorMsg").innerHTML = "";
    var areaId = document.getElementById('areaId').value;
    var clientId = document.getElementById('clientId').value;
    var workPlace = document.getElementById('workPlace').value;
    var latitude = document.getElementById('latitude').value;
    var longitude = document.getElementById('longitude').value;
    var cadImg = document.getElementById('cadImg').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (areaId == '0') {
        document.getElementById("errorMsg").innerHTML = "請選擇作業區域";
        return;
    }
    if (clientId == '0') {
        document.getElementById("errorMsg").innerHTML = "請選擇顧客公司名稱";
        return;
    }
    if (workPlace.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入作業地點";
        return;
    }

    //判斷是否要上傳圖片
    if (!cadImg) {
        var data_obj = {
            areaId: areaId,
            clientId: clientId,
            name: workPlace,
            cadImg: null,
            latitude: latitude,
            longitude: longitude,
            remark: remark
        };
        var result = call_api('work_place_api/addWorkPlace', data_obj);
    } else {
        var cadImgFile = $('#cadImg').prop('files')[0]; //取得上傳檔案屬性
        var data_obj = new FormData();
        data_obj.append('areaId', areaId);
        data_obj.append('clientId', clientId);
        data_obj.append('name', workPlace);
        data_obj.append('cadImg', cadImgFile);
        data_obj.append('latitude', latitude);
        data_obj.append('longitude', longitude);
        data_obj.append('remark', remark);
        var result = call_api_upload('work_place_api/addWorkPlace', data_obj);
    }

    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.href = baseUrl + "work_place/index";
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除管線圖
function delCadImg(data, searchData) {
    if (!confirm("確定刪除管線圖?")) {
        return;
    }

    var result = call_api('work_place_api/delCadImg', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            refreshEdit(data, searchData);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}

// 更新編輯頁面
function refreshEdit(data, searchData) {
    var baseUrl = document.getElementById('base_url').value;
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "work_place/edit";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'data', data);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

//編輯作業區域資料
function saveEdit(searchData) {
    document.getElementById("errorMsg").innerHTML = "";
    var id = document.getElementById('id').value;
    var areaId = document.getElementById('areaId').value;
    var clientId = document.getElementById('clientId').value;
    var workPlace = document.getElementById('workPlace').value;
    var latitude = document.getElementById('latitude').value;
    var longitude = document.getElementById('longitude').value;
    var cadImg = document.getElementById('cadImg').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (workPlace.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入作業地點";
        return;
    }

    //判斷是否要上傳圖片
    if (!cadImg) {
        var cadImgName = document.getElementById('cadImgName').value;
        var data_obj = {
            id: id,
            areaId: areaId,
            clientId: clientId,
            name: workPlace,
            cadImg: cadImg,
            cadImgName: cadImgName,
            latitude: latitude,
            longitude: longitude,
            remark: remark
        };
        var result = call_api('work_place_api/editWorkPlace', data_obj);
    } else {
        var cadImgFile = $('#cadImg').prop('files')[0]; //取得上傳檔案屬性
        var data_obj = new FormData();
        data_obj.append('id', id);
        data_obj.append('areaId', areaId);
        data_obj.append('clientId', clientId);
        data_obj.append('name', workPlace);
        data_obj.append('cadImg', cadImgFile);
        data_obj.append('cadImgName', null);
        data_obj.append('latitude', latitude);
        data_obj.append('longitude', longitude);
        data_obj.append('remark', remark);
        var result = call_api_upload('work_place_api/editWorkPlace', data_obj);
    }

    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除作業區域資料
function delWorkPlace(data, pageTotalCount) {
    if (!confirm("確定刪除「" + data['workPlace'] + "」作業地點?")) {
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
        areaId: document.getElementById('areaId').value,
        workPlace: document.getElementById('workPlace').value,
        company: document.getElementById('company').value,
        page: page,
        pageCount: document.getElementById('listPageCount').value
    };

    var result = call_api('work_place_api/delWorkPlace', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}