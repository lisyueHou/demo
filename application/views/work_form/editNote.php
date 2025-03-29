<div id="hidebg">
    <!-- 修改視窗 -->
    <div id="note_hidebox" class="popupbox"><a class="popupButton" onClick="hide('note_hidebox');">&times;</a>
        <div class="popupTitle">編輯清潔標記資料</div>
        <hr class="topBorder">
        <div class="popupContent">
            <div><label>項次：</label>
                <span id="frRemarkNo"></span>
                <input type="hidden" id="frRemarkId">
            </div>
            <div><label>管線內距離：</label><input class="width_80px" type="number" id="frMeters">m</div>
            <div><label>管線內檢查情形：</label><input class="width_280px" type="text" id="frContent" maxlength="100"></div>
            <div>
                <label>檢驗結果：</label>
                <input type="radio" id="frResultsY" name="frResults" value="Y"><label for="frResultsY">已清除</label>
                <input type="radio" id="frResultsN" name="frResults" value="N"><label for="frResultsN">未清除</label>
            </div>
            <div>
                <label>備考：</label>
                <textarea id="frRemark" maxlength="300"></textarea>
            </div>
        </div>
        <div class="btnBox">
            <div class="alertMsg" id="frErrorMsg"></div>
            <button class='button' onclick="editFormRemark();">儲存</button>
            <button class='button removeRed' onclick="hide('note_hidebox');">取消</button>
        </div>
    </div>
</div>