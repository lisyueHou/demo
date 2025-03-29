//呼叫API
function call_api(api_name, data_obj) {
    var baseUrl = document.getElementById('base_url').value;
    var result = [];
    $.ajax({
        cache: false,
        async: false,
        url: baseUrl + api_name,
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
        },
        type: "POST",
        data: data_obj,
        success: function(json) {
            json = JSON.stringify(json);
            json = JSON.parse(json);
            result = json;
        },
        error: function(xhr, status, error) {
            result['status'] = 0;
            result['message'] = "連線失敗";
            console.log("Error    ==================    API Response    ==================");
            console.log(xhr.responseText);
            console.log(error);
        }
    });
    return result;
}

//呼叫API-有上傳檔案
function call_api_upload(api_name, data_obj) {
    var baseUrl = document.getElementById('base_url').value;
    var result = [];
    $.ajax({
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        url: baseUrl + api_name,
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
        },
        type: "POST",
        data: data_obj,
        success: function(json) {
            json = JSON.stringify(json);
            json = JSON.parse(json);
            result = json;
        },
        error: function(xhr, status, error) {
            result['status'] = 0;
            result['message'] = "連線失敗";
            console.log("Error    ==================    API Response    ==================");
            console.log(xhr.responseText);
            console.log(error);
        }
    });
    return result;
}

// 列表上一頁
function lastPage(pageId, countId) {
    var data = checkLastPage(pageId, countId);
    dataList(data['page'], data['pageCount']);
}

// 檢查列表上一頁
function checkLastPage(pageId, countId) {
    var page = document.getElementById(pageId).value;
    var pageCount = document.getElementById(countId).value;
    page = parseInt(page);
    // 檢查是否已在第1頁
    if (page > 1) {
        page = page - 1;
    }
    var data = {
        'page': page,
        'pageCount': pageCount
    };
    return data;
}

// 列表下一頁
function nextPage(pageId, countId) {
    var data = checkNextPage(pageId, countId);
    dataList(data['page'], data['pageCount']);
}

// 檢查列表下一頁
function checkNextPage(pageId, countId) {
    var page = document.getElementById(pageId).value;
    var pageCount = document.getElementById(countId).value;
    var array = new Array();
    // 取得所有頁數
    $("#" + pageId + " option").each(function() {
        var txt = $(this).val();
        if (txt != '') {
            txt = parseInt(txt);
            array.push(txt);
        }
    });

    var maxPage = Math.max.apply(null, array); // 取得最後一頁
    page = parseInt(page);
    // 檢查是否已在最終頁
    if (page < maxPage) {
        page = page + 1;
    }
    var data = {
        'page': page,
        'pageCount': pageCount
    };
    return data;
}

//無列表資料
function noDataList(msg) {
    var msg = '<div class="pageMsg">' + msg + '<div>';
    $("#dataList").html(msg);
    document.getElementById('totalCount').innerHTML = '資料筆數：0';
    document.getElementById("pageBox").style.display = "none"; //隱藏筆數頁數
}

// 函式-取得陣列or物件元素數量
function json_count(x) {
    var t = typeof x;
    if (t == 'string') {
        return x.length;
    } else if (t == 'object') {
        var n = 0;
        for (var i in x) {
            n++;
        }
        return n;
    }
    return false;
}

// 筆數頁數選單
function pageSelect(totalPage, page, pageCount) {
    var pageSelect = document.getElementById('listPage');
    var countSelect = document.getElementById('listPageCount');
    if (totalPage > 0) {
        for (var i = 1; i <= totalPage; i++) {
            addOption(pageSelect, i, i);
        }
    }
    $('#listPage').val(page);
    for (var i = 10; i <= 50; i = i + 10) {
        addOption(countSelect, i, i);
    }
    $('#listPageCount').val(pageCount);
}

// 動態新增option函式
function addOption(pageSelect, text, value) {
    var option = document.createElement("option");
    option.text = text;
    option.value = value;
    pageSelect.options.add(option);
}

// 建立input欄位
function creatInput(form, inputName, inputValue) {
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", inputName);
    hiddenField.setAttribute("value", inputValue);
    form.appendChild(hiddenField);
    return form;
}

//延遲執行(秒)
function sleep(time) {
    return new Promise((resolve) => setTimeout(resolve, time));
}

// 顯示視窗及隱藏層
function display_window(hidebox_id) {
    document.getElementById("hidebg").style.display = "block"; //顯示隱藏層 
    document.getElementById(hidebox_id).style.display = "inline-block"; //顯示彈出層
    var height = document.getElementById('hidebg').offsetHeight;
    document.getElementById("mainBody").style.height = height + "px";
}

//去除隱藏層和視窗 
function hide(hidebox_id) {
    document.getElementById("hidebg").style.display = "none";
    document.getElementById(hidebox_id).style.display = "none";
    document.getElementById("mainBody").style.height = "100";
}

//備註訊息
function viewRemark(data) {
    var remark = data['remark'].replaceAll(/(?:\r\n|\r|\n)/g, '<br/>');
    document.getElementById('note').innerHTML = remark;
    display_window("note_hidebox");
}

//判斷是否為NULL
function isNull(data) {
    if (data != null) {
        return data;
    } else {
        return '';
    }
}