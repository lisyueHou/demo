<div id="hidebg">
    <!-- 地圖位置視窗 -->
    <div id="viewMap_hidebox" class="popupbox"><a class="popupButton" onClick="hide('viewMap_hidebox');">&times;</a>
        <div class="popupTitle">地圖位置</div>
        <hr class="topBorder">
        <div class="popupContent">
            <div>作業地點：<span id="workPlaceName"></span></div>
            <iframe id="iframeMap"></iframe>
        </div>
        <div class="btnBox">
            <button class='button gray' onclick="hide('viewMap_hidebox');">關閉</button>
        </div>
    </div>

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
</div>