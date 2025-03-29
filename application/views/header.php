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
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/icon-v5.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery.qrcode.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/qrcode.js" crossorigin="anonymous"></script>

    <title>管線機器人管理系統</title>
</head>

<body>
    <?php
    $authorization = $this->session->user_info->authorization; //帳號權限
    $userName = $this->session->user_info->user['userName']; //使用者名稱

    //選單預設項目為關閉
    $mu_history = "none"; //歷史資料查詢
    $mu_ = "none"; //工單報表管理
    $mu_basicData = "none"; //基本資料管理
    $mu_workPlace = "none"; //作業區域維護
    $mu_robot = "none"; //設備資料維護
    $mu_remark = "none"; //常用備註資料
    $mu_staff = "none"; //員工資料維護
    $mu_client = "none"; //顧客資料維護
    $mu_accManage = "none"; //帳號管理
    $mu_groups = "none"; //權限群組維護
    $mu_users = "none"; //帳號維護

    //判斷是否有權限
    foreach ($authorization as $row) :
        $row = (array)$row;
        $subFunction = explode(",", $row['subFunction']);
        switch (strtolower($row['mainFunction'])) {
            case 'history': //歷史資料查詢
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_history = "block";
                    }
                endforeach;
                break;
            case 'work_form': //工單報表管理
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_workForm = "block";
                    }
                endforeach;
                break;
            case 'work_place': //作業區域維護
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_basicData = "block";
                        $mu_workPlace = "block";
                    }
                endforeach;
                break;
            case 'robot': //設備資料維護
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_basicData = "block";
                        $mu_robot = "block";
                    }
                endforeach;
                break;
            case 'remark': //常用備註資料
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_basicData = "block";
                        $mu_remark = "block";
                    }
                endforeach;
                break;
            case 'staff': //員工資料維護
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_basicData = "block";
                        $mu_staff = "block";
                    }
                endforeach;
                break;
            case 'client': //顧客資料維護
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_basicData = "block";
                        $mu_client = "block";
                    }
                endforeach;
                break;
            case 'groups': //權限群組維護
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_accManage = "block";
                        $mu_groups = "block";
                    }
                endforeach;
                break;
            case 'users': //帳號維護
                foreach ($subFunction as $subFunction) :
                    if (strtolower($subFunction) === 'index') {
                        $mu_accManage = "block";
                        $mu_users = "block";
                    }
                endforeach;
                break;
        }
    endforeach;

    ?>

    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <div class="mainBody" id="mainBody">
        <nav>
            <div class="navMenu js_mobileMenuIcon">
                <div class="navMenuIcon"><i class="fas fa-bars fa-lg"></i></div>
            </div>
            <div class="navUser">
                <div class="navUserIcon"><a href="<?php echo base_url("personal"); ?>"><i class="fas fa-user"></i></a></div>
                <div class="navUserBox"><a href="<?php echo base_url("personal"); ?>"><?php echo $userName; ?></a></div>
            </div>
            <div class="navTitleBox">
                <div class="navTitle">管線機器人管理系統</div>
            </div>
            <div class="navLogout" onclick="logout()">
                <div class="navLogoutBox">登出</div>
                <div class="navLogoutIcon"><i class="fas fa-sign-out-alt fa-lg"></i></div>
            </div>
        </nav>
        <div class="mobileMenu js_mobileMenu">
            <ul>
                <li class="mobileMenuTitle js_menuTitle1">作業資訊<i class="fas fa-sort-down"></i></li>
                <li class="mobileMenuItem js_menuItem1"><a href="<?php echo base_url("dashboard"); ?>">即時資訊</a></li>
                <li class="mobileMenuItem js_menuItem1 <?php echo $mu_history; ?>"><a href="<?php echo base_url("history"); ?>">歷史資料查詢</a></li>
                <li class="mobileMenuTitle <?php echo $mu_workForm; ?>"><a href="<?php echo base_url("work_form"); ?>">工單報表管理</a></li>
                <li class="mobileMenuTitle js_menuTitle2 <?php echo $mu_basicData; ?>">基本資料管理<i class="fas fa-sort-down"></i></li>
                <li class="mobileMenuItem js_menuItem2 <?php echo $mu_workPlace; ?>"><a href="<?php echo base_url("work_place"); ?>">作業區域維護</a></li>
                <li class="mobileMenuItem js_menuItem2 <?php echo $mu_robot; ?>"><a href="<?php echo base_url("robot"); ?>">設備資料維護</a></li>
                <li class="mobileMenuItem js_menuItem2 <?php echo $mu_remark; ?>"><a href="<?php echo base_url("remark"); ?>">常用備註資料</a></li>
                <li class="mobileMenuItem js_menuItem2 <?php echo $mu_staff; ?>"><a href="<?php echo base_url("staff"); ?>">員工資料維護</a></li>
                <li class="mobileMenuItem js_menuItem2 <?php echo $mu_client; ?>"><a href="<?php echo base_url("client"); ?>">顧客資料維護</a></li>
                <li class="mobileMenuTitle js_menuTitle3 <?php echo $mu_accManage; ?>">帳號管理<i class="fas fa-sort-down"></i></li>
                <li class="mobileMenuItem js_menuItem3 <?php echo $mu_groups; ?>"><a href="<?php echo base_url("groups"); ?>">權限群組維護</a></li>
                <li class="mobileMenuItem js_menuItem3 <?php echo $mu_users; ?>"><a href="<?php echo base_url("users"); ?>">帳號維護</a></li>
            </ul>
        </div>

        <script>
            localStorage.setItem('token', '<?php echo $this->session->user_info->token; ?>');

            //登出
            function logout() {
                if (confirm("確定登出系統?")) {
                    document.location.href = '<?php echo base_url("login/logout"); ?>';
                }
            }

            //選單
            $(document).ready(function() {

                // 第一層收放
                $(".js_mobileMenuIcon").click(function() {
                    $(".js_mobileMenu").slideToggle();
                });

                // 第二層收放
                $(".js_menuTitle1").click(function() {
                    $(".js_menuItem1").slideToggle();
                });
                $(".js_menuTitle2").click(function() {
                    $(".js_menuItem2").slideToggle();
                });
                $(".js_menuTitle3").click(function() {
                    $(".js_menuItem3").slideToggle();
                });
            });
        </script>