// 回功能首頁
function gotoHome() {
    var baseUrl = document.getElementById('base_url').value;
    location.href = baseUrl + "dashboard/index";
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
    var result = call_api('personal_api/editPass', data_obj);
    if (result['status']) {
        document.getElementById("errorMsgPass").innerHTML = result['message'];
        sleep(1000).then(() => {
            hide('pass_hidebox');
        });
    } else {
        document.getElementById("errorMsgPass").innerHTML = result['message'];
    }
}
