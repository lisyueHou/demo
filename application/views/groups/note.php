<div id="hidebg">
    <!-- 檢視群組帳號 -->
    <div id="acclist_hidebox" class="popupbox"><a class="popupButton" onClick="hide('acclist_hidebox');">&times;</a>
        <div class="popupTitle">檢視群組帳號</div>
        <hr class="topBorder">
        <div class="popupContent">
            <div>群組名稱：<span id="groupName"></span></div>
            <div id="groupAccList"></div>
        </div>
        <div class="btnBox">
            <button class='button gray' onclick="hide('acclist_hidebox');">關閉</button>
        </div>
    </div>

    <!-- 檢視群組權限 -->
    <div id="prem_hidebox" class="popupbox"><a class="popupButton" onClick="hide('prem_hidebox');">&times;</a>
        <div class="popupTitle">檢視群組權限</div>
        <hr class="topBorder">
        <div class="popupContent">
            <div>群組名稱：<span id="groupPremName"></span></div>
            <div id="groupPremList"></div>
        </div>
        <div class="btnBox">
            <button class='button gray' onclick="hide('prem_hidebox');">關閉</button>
        </div>
    </div>
</div>