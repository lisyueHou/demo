<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="cache-control" content="no-cache">

    <?php
    $run = strtotime(date("Y-m-d H:i:s"));
    //引入css
    echo link_tag('vendor/twbs/bootstrap/dist/css/bootstrap.min.css?run=' . $run);
    echo link_tag('appoint/css/common_style.css?run=' . $run);
    echo link_tag('appoint/css/style.css?run=' . $run);

    //網頁icon
    echo link_tag('appoint/images/webicon.png?run=' . $run, 'icon', 'image/x-icon');
    ?>
    <!-- 引入js 套件 -->
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery-3.1.1.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery.qrcode.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/qrcode.js" crossorigin="anonymous"></script>

    <title>管線機器人管理系統</title>
</head>

<body>
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <div class="qrCodeBox">
        <div class="qrCodeCon">
            <input type="hidden" id="formId" value="<?php echo $id;?>">
            <div class="qrCodeTitle">表單編號：<span id="formNo"><?php echo $formNo; ?></span></div>
            <div id="qrcode"></div>
            <div class="qrCodeTime">列印時間：<?php echo date('Y-m-d H:i:s'); ?></div>
        </div>

        <div class="btnBox" id="btnBox">
            <button class='button printBlue' onclick='printCode()'>列印</button>
            <button class='button removeRed' onclick='window.close();'>取消</button>
        </div>
    </div>
    <script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
    <script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_form.js'></script>

    <script>
        var formId = document.getElementById('formId').value;
        var baseUrl = document.getElementById('base_url').value;
        var url = baseUrl + 'work_form_qrcode/viewDetail/' + formId;
        jQuery(function() {
            jQuery('#qrcode').qrcode(url, 200, 200);
        });
    </script>
</body>

</html>