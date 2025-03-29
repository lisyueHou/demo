<div id="hidebg">
    <!-- 變更密碼視窗 -->
    <div id="pass_hidebox" class="popupbox"><a class="popupButton" onClick="hide('pass_hidebox');">&times;</a>
        <div class="popupTitle">變更密碼</div>
        <hr class="topBorder">
        <div class="popupContent">
            <div class="alertMsg">*為必填資料</div>
            <input type="hidden" id="passAccId">
            <div>
                <label><span class="alertMsg">*</span>密碼：</label>
                <input type="password" id="password" maxlength="50" placeholder="至少輸入4個英文或數字" required="required">
            </div>
            <div>
                <label><span class="alertMsg">*</span>確認密碼：</label>
                <input type="password" id="passwordCheck" maxlength="50" placeholder="至少輸入4個英文或數字" required="required">
            </div>
        </div>
        <div class="btnBox">
            <div class="alertMsg" id="errorMsgPass"></div>
            <button class='button' onclick='savePass();'>儲存</button>
            <button class='button removeRed' onclick="hide('pass_hidebox');">取消</button>
        </div>
    </div>
</div>