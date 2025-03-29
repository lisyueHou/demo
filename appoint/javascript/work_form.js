// 取得列表資料
function dataList(page, pageCount) {
    document.getElementById('errorMsg').value = "";
    var formNo = document.getElementById('formNo').value;
    var projectNo = document.getElementById('projectNo').value;
    var checkDate = document.getElementById('checkDate').value;
    var projectName = document.getElementById('projectName').value;
    var contractor = document.getElementById('contractor').value;
    var company = document.getElementById('company').value;
    var clientId = document.getElementById('clientId').value;
    var data_obj = {
        formNo: formNo,
        projectNo: projectNo,
        checkDate: checkDate,
        projectName: projectName,
        contractor: contractor,
        company: company,
        clientId: clientId,
        page: page,
        pageCount: pageCount
    };
    var result = call_api('work_form_api/getWorkform', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        showDataList(data, data_obj);
    } else {
        var msg = '查無工單報表';
        noDataList(msg);
    }
}

// 顯示列表資料
function showDataList(seachText, data_obj) {
    var page = parseInt(data_obj['page']);
    var pageCount = parseInt(data_obj['pageCount']);
    var clientId = data_obj['clientId'];
    var pageStart = (page - 1) * pageCount;
    var totalPage = seachText['totalPage'];
    var totalCount = seachText['totalCount'];

    var hideobj = document.getElementById("pageBox"); //分頁區塊
    var data = seachText['work_form'];
    var count = json_count(data); // 資料筆數
    var tab = '';
    if (count != 0) {
        //顯示列表資料
        tab += '<table class="contentsTable">';
        tab += '<tr>';
        tab += '<th class="width_40px">No</th>';
        tab += '<th>表單編號</th>';
        tab += '<th>作業狀態</th>';
        tab += '<th>檢查日期</th>';
        tab += '<th>專案編號</th>';
        tab += '<th>工程專案名稱</th>';
        tab += '<th>協力廠商</th>';
        tab += '<th>業主</th>';
        tab += '<th>簽核狀態</th>';
        tab += '<th class="width_60px">備註</th>';
        if (clientId == '') {
            tab += '<th class="width_180px">功能</th>';
        } else {
            tab += '<th class="width_60px">功能</th>';
        }

        tab += '</tr>';

        var no = pageStart;
        data.forEach(data => {
            no++;
            var rowStr = JSON.stringify(data);
            tab += '<tr>';
            tab += '<td class="textCenter">' + no + '</td>';
            tab += "<td><a href='#' onclick='gotoDetail(" + rowStr + ");'>" + data['formNo'] + "</a></td>";
            var checkDate = isNull(data['checkDate']);
            var projectNo = isNull(data['projectNo']);
            var projectName = isNull(data['projectName']);
            var contractor = isNull(data['contractor']);
            var company = isNull(data['company']);
            var finishTime = isNull(data['finishTime']);

            //作業狀態
            if (finishTime == '0000-00-00 00:00:00') {
                tab += '<td class="textCenter textGreen">進行中</td>';
            } else {
                tab += '<td class="textCenter">已完成</td>';
            }

            //檢查日期
            if (checkDate == '0000-00-00') {
                tab += '<td></td>';
            } else {
                tab += '<td>' + checkDate + '</td>';
            }

            tab += '<td>' + projectNo + '</td>';
            tab += '<td>' + projectName + '</td>';
            tab += '<td>' + contractor + '</td>';
            tab += '<td>' + company + '</td>';

            //簽核狀態
            var formsign = data['formsign'];
            var signSort = parseInt(data['signSort']);
            var isComplete = parseInt(data['isComplete']);
            var settingPersonId = data['settingPersonId'];
            var proxyPersonId = data['proxyPersonId'];
            var personId = document.getElementById('personId').value;
            var signSetBtn = "<button class='button orange' onclick='gotoSignSet(" + rowStr + ");'>設定</button>";
            var signData = {
                id: data['id'],
                formNo: data['formNo'],
                signSort: signSort,
                personId: personId
            };
            var signDataStr = JSON.stringify(signData);
            if (personId == settingPersonId || personId == proxyPersonId) {
                var signBtn = "<button class='button signBlue' onclick='gotoFormSign(" + signDataStr + ");'>簽核</button>";
            } else {
                var signBtn = '';
            }
            if (formsign.length <= 1) {
                if (clientId == '') {
                    tab += '<td class="textOrange textCenter">未設定' + signSetBtn + '</td>';
                } else {
                    tab += '<td class="textCenter">設定中</td>';
                }
            } else {
                //判斷是否已進入簽核流程(施工單位是否已簽核)，如是則不能再修改設定
                if (isComplete == 1) { //簽核完成
                    tab += '<td class="textCenter">簽核完成</td>';
                } else {
                    switch (signSort) {
                        case 1:
                            if (clientId == '') {
                                tab += '<td class="textCenter">施工單位' + signSetBtn + signBtn + '</td>';
                            } else {
                                tab += '<td class="textCenter">簽核中</td>';
                            }
                            break;
                        case 2:
                            if (clientId == '') {
                                tab += '<td class="textCenter">建造單位' + signBtn + '</td>';
                            } else {
                                tab += '<td class="textCenter">簽核中</td>';
                            }
                            break;
                        case 3:
                            if (clientId == '') {
                                tab += '<td class="textCenter">品管單位' + signBtn + '</td>';
                            } else {
                                tab += '<td class="textCenter">簽核中</td>';
                            }
                            break;
                        case 4:
                            if (clientId == '') {
                                tab += '<td class="textCenter">待業主簽核</td>';
                            } else {
                                tab += '<td class="textCenter">' + signBtn + '</td>';
                            }
                            break;
                    }
                }
            }

            //備註
            if (!data['remark']) {
                tab += '<td class="textCenter">-</td>';
            } else {
                var remarkBtn = "<button class='button gray' onclick='viewRemark(" + rowStr + ");'>檢視</button>";
                tab += '<td class="textCenter">' + remarkBtn + '</td>';
            }

            var qrcodeBtn = "<button class='button printBlue' onclick='openQRcode(" + rowStr + ");'>QR碼</button>";
            var editBtn = '';
            var delBtn = '';
            if (clientId == '') {
                editBtn = "<button class='button modGreen' onclick='gotoEdit(" + rowStr + ");'>編輯</button>";
                delBtn = "<button class='button removeRed' onclick='delRobot(" + rowStr + "," + count + ");'>刪除</button>";
            }

            tab += '<td class="textCenter">' + qrcodeBtn + editBtn + delBtn + '</td>';
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
        var msg = '查無工單報表';
        noDataList(msg);
    }
}

// 回功能首頁
function gotoHome(searchData) {
    var baseUrl = document.getElementById('base_url').value;
    searchData = JSON.stringify(searchData);
    var path = baseUrl + "work_form/index";
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
        formNo: document.getElementById('formNo').value,
        projectNo: document.getElementById('projectNo').value,
        checkDate: document.getElementById('checkDate').value,
        projectName: document.getElementById('projectName').value,
        contractor: document.getElementById('contractor').value,
        company: document.getElementById('company').value,
        clientId: document.getElementById('clientId').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);

    var path = baseUrl + "work_form/add";
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
        formNo: document.getElementById('formNo').value,
        projectNo: document.getElementById('projectNo').value,
        checkDate: document.getElementById('checkDate').value,
        projectName: document.getElementById('projectName').value,
        contractor: document.getElementById('contractor').value,
        company: document.getElementById('company').value,
        clientId: document.getElementById('clientId').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "work_form/edit";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'data', data);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

// 轉至設定簽核頁面
function gotoSignSet(data) {
    var baseUrl = document.getElementById('base_url').value;

    //頁面資料
    var searchData = {
        formNo: document.getElementById('formNo').value,
        projectNo: document.getElementById('projectNo').value,
        checkDate: document.getElementById('checkDate').value,
        projectName: document.getElementById('projectName').value,
        contractor: document.getElementById('contractor').value,
        company: document.getElementById('company').value,
        clientId: document.getElementById('clientId').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "work_form/signSet";
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
    document.getElementById('formNo').value = '';
    document.getElementById('projectNo').value = '';
    document.getElementById('checkDate').value = '';
    document.getElementById('projectName').value = '';
    document.getElementById('contractor').value = '';
    document.getElementById('company').value = '';
    dataList(1, 10);
}

//工作位置選單資料
function getWorkPlaceList(clientId) {
    document.getElementById('errorMsg').innerHTML = '';
    if (clientId == 0) {
        var tab = '<option value="0">請選擇工作位置</option>';
    } else {
        var tab = '';
        var data_obj = {
            clientId: clientId
        };
        var result = call_api('work_form_api/getWorkPlaceList', data_obj);
        if (result['status']) {
            if (result['data'].length == 0) {
                tab += '<option value="0">請選擇工作位置</option>';
                document.getElementById('errorMsg').innerHTML = '查無該業主工作位置資料';
            } else {
                result['data'].forEach(data => {
                    tab += '<option value="' + data.id + '">' + data.name + '</option>';
                });
            }
        } else {
            tab += '<option value="0">請選擇工作位置</option>';
        }
    }
    $("#workPlaceId").html(tab);
}

//新增工單報表
function saveAdd() {
    var baseUrl = document.getElementById('base_url').value;
    document.getElementById("errorMsg").innerHTML = "";
    var startTime = document.getElementById('startTime').value;
    var finishTime = document.getElementById('finishTime').value;
    var robotNo = document.getElementById('robotNo').value;
    var clientId = document.getElementById('clientId').value;
    var workPlaceId = document.getElementById('workPlaceId').value;
    var projectNo = document.getElementById('projectNo').value;
    var projectName = document.getElementById('projectName').value;
    var subProjectName = document.getElementById('subProjectName').value;
    var contractor = document.getElementById('contractor').value;
    var checkDate = document.getElementById('checkDate').value;
    var pipingLineNo = document.getElementById('pipingLineNo').value;
    var segmentsNo = document.getElementById('segmentsNo').value;
    var remark = document.getElementById('remark').value;

    //欄位資料檢查
    if (startTime.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入作業開始時間";
        return;
    }
    startTime = startTime.replace('T', ' ') + ':00'; //時間格式轉換
    if (finishTime.length != 0) {
        if (startTime > finishTime) {
            document.getElementById("errorMsg").innerHTML = "「作業結束時間」須大於「作業開始時間」";
            return;
        }
        finishTime = finishTime.replace('T', ' ') + ':00'; //時間格式轉換
    } else {
        finishTime = null;
    }
    if (robotNo == 0) {
        document.getElementById("errorMsg").innerHTML = "請選擇設備";
        return;
    }
    if (clientId == 0) {
        clientId = null;
    }
    if (workPlaceId == 0) {
        workPlaceId = null;
    }
    if (checkDate.length == 0) {
        checkDate = null;
    }

    var data_obj = {
        startTime: startTime,
        finishTime: finishTime,
        robotNo: robotNo,
        clientId: clientId,
        workPlaceId: workPlaceId,
        projectNo: projectNo,
        projectName: projectName,
        subProjectName: subProjectName,
        contractor: contractor,
        checkDate: checkDate,
        pipingLineNo: pipingLineNo,
        segmentsNo: segmentsNo,
        remark: remark
    };
    var result = call_api('work_form_api/addWorkform', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.href = baseUrl + "work_form/index";
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//編輯工單報表資料
function saveEdit(searchData) {
    document.getElementById("errorMsg").innerHTML = "";
    var id = document.getElementById('id').value;
    var formNo = document.getElementById('formNo').innerHTML;
    var startTime = document.getElementById('startTime').value;
    var finishTime = document.getElementById('finishTime').value;
    var robotNo = document.getElementById('robotNo').value;
    var clientId = document.getElementById('clientId').value;
    var oldClientId = document.getElementById('oldClientId').value;
    var workPlaceId = document.getElementById('workPlaceId').value;
    var projectNo = document.getElementById('projectNo').value;
    var projectName = document.getElementById('projectName').value;
    var subProjectName = document.getElementById('subProjectName').value;
    var contractor = document.getElementById('contractor').value;
    var checkDate = document.getElementById('checkDate').value;
    var pipingLineNo = document.getElementById('pipingLineNo').value;
    var segmentsNo = document.getElementById('segmentsNo').value;
    var remark = document.getElementById('remark').value;

    if (startTime.length == 0) {
        document.getElementById("errorMsg").innerHTML = "請輸入作業開始時間";
        return;
    }
    startTime = startTime.replace('T', ' ') + ':00'; //時間格式轉換
    if (finishTime.length != 0) {
        if (startTime > finishTime) {
            document.getElementById("errorMsg").innerHTML = "「作業結束時間」須大於「作業開始時間」";
            return;
        }
        finishTime = finishTime.replace('T', ' ') + ':00'; //時間格式轉換
    } else {
        finishTime = null;
    }
    if (robotNo == 0) {
        document.getElementById("errorMsg").innerHTML = "請選擇設備";
        return;
    }
    if (clientId == 0) {
        clientId = null;
    }
    if (workPlaceId == 0) {
        workPlaceId = null;
    }
    if (checkDate.length == 0) {
        checkDate = null;
    }

    var data_obj = {
        id: id,
        formNo: formNo,
        startTime: startTime,
        finishTime: finishTime,
        robotNo: robotNo,
        clientId: clientId,
        oldClientId: oldClientId,
        workPlaceId: workPlaceId,
        projectNo: projectNo,
        projectName: projectName,
        subProjectName: subProjectName,
        contractor: contractor,
        checkDate: checkDate,
        pipingLineNo: pipingLineNo,
        segmentsNo: segmentsNo,
        remark: remark
    };
    var result = call_api('work_form_api/editWorkform', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除工單報表資料
function delRobot(data, pageTotalCount) {
    if (!confirm("確定刪除工單編號「" + data['formNo'] + "」?")) {
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
        formNo: document.getElementById('formNo').value,
        projectNo: document.getElementById('projectNo').value,
        checkDate: document.getElementById('checkDate').value,
        projectName: document.getElementById('projectName').value,
        contractor: document.getElementById('contractor').value,
        company: document.getElementById('company').value,
        clientId: document.getElementById('clientId').value,
        page: page,
        pageCount: document.getElementById('listPageCount').value
    };

    var result = call_api('work_form_api/delWorkform', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}

//開啟QR Code視窗
function openQRcode(data) {
    var baseUrl = document.getElementById('base_url').value;
    window.open(baseUrl + "work_form/viewQrcode/" + data['formNo'], 'img', config = 'height=768,width=1024');
}

//列印QR Code
function printCode() {
    document.getElementById("btnBox").style.display = "none";
    window.print();
    document.getElementById("btnBox").style.display = "block";
}

// 轉至詳細資料頁面
function gotoDetail(data) {
    var baseUrl = document.getElementById('base_url').value;

    //頁面資料
    var searchData = {
        formNo: document.getElementById('formNo').value,
        projectNo: document.getElementById('projectNo').value,
        checkDate: document.getElementById('checkDate').value,
        projectName: document.getElementById('projectName').value,
        contractor: document.getElementById('contractor').value,
        company: document.getElementById('company').value,
        clientId: document.getElementById('clientId').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    data = JSON.stringify(data);

    var path = baseUrl + "work_form/viewDetail";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'data', data);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

// 開啟列印PDF
function openPrintPDF(formId) {
    var baseUrl = document.getElementById('base_url').value;
    var url = baseUrl + 'work_form/printPDF/' + formId;
    window.open(url);
}

//列印PDF
function printPDF() {
    document.getElementById("btnBox").style.display = "none";
    window.print();
    document.getElementById("btnBox").style.display = "block";
}

//已選取項目的不能再選取-簽核人員
function signFormSelChange(select) {
    var selectId = select.id;
    var proxySelectId = 'proxy_' + selectId;
    $("#" + proxySelectId + " option").css("background-color", "rgb(255,255,255)");
    $("#" + proxySelectId + " option").attr("disabled", false);

    var id = $('#' + selectId).val();
    var proxyId = '#' + proxySelectId + '_' + id;
    $(proxyId).css("background-color", "red");
    $(proxyId).attr('disabled', true);
}

//已選取項目的不能再選取-簽核代理人員
function proxySignFormSelChange(select) {
    var selectId = select.id;
    var selectIdArr = selectId.split("_");
    var mainSelectId = selectIdArr[1];
    $("#" + mainSelectId + " option").css("background-color", "rgb(255,255,255)");
    $("#" + mainSelectId + " option").attr("disabled", false);

    var id = $('#' + selectId).val();
    var proxyId = '#' + mainSelectId + '_' + id;
    $(proxyId).css("background-color", "red");
    $(proxyId).attr('disabled', true);
}

//儲存簽核設定
function saveFormSignSet(searchData) {
    document.getElementById('errorMsg').value = "";
    var formNo = document.getElementById('formNo').innerHTML;
    var old_subcontratorId = document.getElementById('old_subcontratorId').value;
    var old_proxy_subcontratorId = document.getElementById('old_proxy_subcontratorId').value;
    var subcontratorId = document.getElementById('subcontrator').value;
    var proxy_subcontratorId = document.getElementById('proxy_subcontrator').value;
    var constructionId = document.getElementById('construction').value;
    var proxy_constructionId = document.getElementById('proxy_construction').value;
    var qualityEngineerId = document.getElementById('qualityEngineer').value;
    var proxy_qualityEngineerId = document.getElementById('proxy_qualityEngineer').value;

    //資料檢查
    if (old_subcontratorId == '') {
        old_subcontratorId = null;
    }
    if (old_proxy_subcontratorId == '') {
        old_proxy_subcontratorId = null;
    }
    if (subcontratorId == 0) {
        document.getElementById("errorMsg").innerHTML = "請選擇施工單位簽核人員";
        return;
    }
    if (proxy_subcontratorId == 0) {
        proxy_subcontratorId = null;
    }
    if (constructionId == 0) {
        document.getElementById("errorMsg").innerHTML = "請選擇建造工程師單位簽核人員";
        return;
    }
    if (proxy_constructionId == 0) {
        proxy_constructionId = null;
    }
    if (qualityEngineerId == 0) {
        document.getElementById("errorMsg").innerHTML = "請選擇品管工程師單位簽核人員";
        return;
    }
    if (proxy_qualityEngineerId == 0) {
        proxy_qualityEngineerId = null;
    }

    var data_obj = {
        formNo: formNo,
        old_subcontratorId: old_subcontratorId,
        old_proxy_subcontratorId: old_proxy_subcontratorId,
        subcontratorId: subcontratorId,
        proxy_subcontratorId: proxy_subcontratorId,
        constructionId: constructionId,
        proxy_constructionId: proxy_constructionId,
        qualityEngineerId: qualityEngineerId,
        proxy_qualityEngineerId: proxy_qualityEngineerId
    };
    var result = call_api('work_form_api/addFormSignSet', data_obj);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

// 轉至簽核頁面
function gotoFormSign(signData) {
    var baseUrl = document.getElementById('base_url').value;

    //頁面資料
    var searchData = {
        formNo: document.getElementById('formNo').value,
        projectNo: document.getElementById('projectNo').value,
        checkDate: document.getElementById('checkDate').value,
        projectName: document.getElementById('projectName').value,
        contractor: document.getElementById('contractor').value,
        company: document.getElementById('company').value,
        clientId: document.getElementById('clientId').value,
        page: document.getElementById('listPage').value,
        pageCount: document.getElementById('listPageCount').value
    };
    searchData = JSON.stringify(searchData);
    signData = JSON.stringify(signData);

    var path = baseUrl + "work_form/formSign";
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);
    form = creatInput(form, 'signData', signData);
    form = creatInput(form, 'searchData', searchData);
    document.body.appendChild(form);
    form.submit();
}

//人員簽核
function saveFormSign(signData, searchData) {
    var result = call_api('work_form_api/addFormSign', signData);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            gotoHome(searchData);
        });
    } else {
        document.getElementById('errorMsg').innerHTML = result['message'];
    }
}

//刪除工單標註資料
function delFormRemark(data) {
    if (!confirm("確定刪除?")) {
        return;
    }
    var result = call_api('work_form_api/delFormRemark', data);
    if (result['status']) {
        document.getElementById("errorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.reload(true);
        });
    } else {
        document.getElementById("errorMsg").innerHTML = result['message'];
    }
}

//打開編輯工單標註資料視窗
function viewFormRemark(data, no) {
    document.getElementById('frRemarkNo').innerHTML = no;
    document.getElementById('frRemarkId').value = data['id'];
    document.getElementById('frMeters').value = data['meters'];
    document.getElementById('frContent').value = data['content'];
    if (data['results'] == "Y") {
        document.getElementById('frResultsY').checked = true;
    } else {
        document.getElementById('frResultsN').checked = true;
    }
    document.getElementById('frRemark').innerHTML = data['remark'];
    display_window("note_hidebox");
}

//修改工單標註資料
function editFormRemark() {
    var id = document.getElementById('frRemarkId').value;
    var meters = document.getElementById('frMeters').value;
    var content = document.getElementById('frContent').value;
    var results = $("input[name=frResults]:checked").val() ;
    var remark = document.getElementById('frRemark').value;

    var data_obj = {
        id: id,
        meters: meters,
        content: content,
        results: results,
        remark: remark
    };
    var result = call_api('work_form_api/editFormRemark', data_obj);
    if (result['status']) {
        document.getElementById("frErrorMsg").innerHTML = result['message'];
        sleep(1000).then(() => {
            location.reload(true);
        });
    } else {
        document.getElementById('frErrorMsg').innerHTML = result['message'];
    }
}