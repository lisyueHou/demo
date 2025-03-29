<div id="hidebg">
    <!-- 備註視窗 -->
    <div id="note_hidebox" class="popupbox"><a class="popupButton" onClick="hide('note_hidebox');">&times;</a>
        <div class="popupTitle">備註</div>
        <hr class="topBorder">
        <div class="popupContent" id="note">
        </div>
        <div class="btnBox">
            <button class='button gray' onclick="hide('note_hidebox');">關閉</button>
        </div>
    </div>

    <!-- 聯絡資訊 -->
    <div id="con_hidebox" class="popupbox"><a class="popupButton" onClick="hide('con_hidebox');">&times;</a>
        <div class="popupTitle">聯絡資訊</div>
        <hr class="topBorder">
        <div class="popupContent">
            <div>顧客代表：<span id="name"></span></div>
            <div>聯絡電話：<span id="phone"></span></div>
            <div>E-mail：<span id="email"></span></div>
            <hr>
            <div>聯絡人：<span id="conName"></span></div>
            <div>聯絡電話：<span id="conPhone"></span></div>
            <div>E-mail：<span id="conEmail"></span></div>
        </div>
        <div class="btnBox">
            <button class='button gray' onclick="hide('con_hidebox');">關閉</button>
        </div>
    </div>
</div>