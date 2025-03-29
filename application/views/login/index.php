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
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/icon-v5.js" crossorigin="anonymous"></script>

    <title>監測管理系統單一登入口</title>
</head>

<body class="loginBody">
    <?php echo form_open('login/index'); ?>
    <div class="loginTitle">監測管理系統單一登入口</div>
    <div class="loginBg">
        <div class="loginBox">
            <div class="loginSubTitle">登入</div>
            <hr>
            <div class="loginLabel"><i class="fas fa-database"></i><label for="account">登入系統</label></div>
            <div>
                <select name="system">
                    <?php
                    $select_0 = '';
                    $select_1 = '';
                    $select_2 = '';
                    if (isset($system)) {
                        switch ($system) {
                            case 0:
                                $select_0 = 'selected';
                                break;
                            case 1: //液壓監測及管理系統
                                $select_1 = 'selected';
                                break;
                            case 2: //管線清潔機器人管理系統
                                $select_2 = 'selected';
                                break;
                            default:
                                $select_0 = 'selected';
                                break;
                        }
                    };
                    ?>
                    <option value="0" <?php echo $select_0; ?>>請選擇登入系統</option>
                    <option value="1" <?php echo $select_1; ?>>液壓監測及管理系統</option>
                    <option value="2" <?php echo $select_2; ?>>管線清潔機器人管理系統</option>
                </select>
            </div>
            <div class="loginLabel"><i class="fas fa-user"></i><label for="account">使用者帳號</label></div>
            <div><input type="text" name="account" maxlength="20" value="<?php if (isset($account)) echo $account; ?>" placeholder="請輸入帳號"></div>
            <div class="loginLabel"><i class="fas fa-lock"></i><label for="password">使用者密碼</label></div>
            <div><input type="password" name="password" maxlength="50" value="<?php if (isset($password)) echo $password; ?>" placeholder="請輸入密碼"></div>
            <div class="loginKeep"><input type="checkbox" id="loginKeep"><label for="loginKeep">保持登入</label></div>
            <?php
            if (isset($message)) echo '<div class="loginMsg">' . $message . '</div>';
            ?>
            <div class="loginBtn"><input type="submit" class="button" name="submit" value="登入"></div>
        </div>
        <div class="version">v1.0.0</div>
    </div>
    </form>
</body>

</html>