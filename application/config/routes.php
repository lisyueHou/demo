<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

// 歷史資料查詢
$route['history/(:any)'] = 'history/index';

// 工單報表管理
$route['work_form/formSign'] = 'work_form/formSign';// 簽核頁面
$route['work_form/signSet'] = 'work_form/signSet';// 設定簽核頁面
$route['work_form/printPDF'] = 'work_form/printPDF';// 列印工單報表
$route['work_form/viewDetail'] = 'work_form/viewDetail';// 工單報表詳細資料
$route['work_form/viewQrcode'] = 'work_form/viewQrcode';// QR Code視窗
$route['work_form/add'] = 'work_form/add';// 新增工單報表
$route['work_form/edit'] = 'work_form/edit';// 編輯工單報表
$route['work_form/(:any)'] = 'work_form/index';
$route['work_form_qrcode/(:any)'] = 'work_form_qrcode/viewDetail';// 工單報表詳細資料-不需權限

// 作業區域維護
$route['work_place/add'] = 'work_place/add'; // 新增作業區域
$route['work_place/edit'] = 'work_place/edit'; // 編輯作業區域
$route['work_place/setcad'] = 'work_place/setcad'; // 管線圖路徑設定
$route['work_place/(:any)'] = 'work_place/index';

// 設備資料維護
$route['robot/add'] = 'robot/add'; // 新增設備資料
$route['robot/edit'] = 'robot/edit'; // 編輯設備資料
$route['robot/(:any)'] = 'robot/index';

// 常用標記備註
$route['remark/add'] = 'remark/add'; // 新增常用標記備註資料
$route['remark/edit'] = 'remark/edit'; // 編輯常用標記備註資料
$route['remark/(:any)'] = 'remark/index';

// 員工資料維護
$route['staff/add'] = 'staff/add'; // 新增員工資料
$route['staff/edit'] = 'staff/edit'; // 編輯員工資料
$route['staff/(:any)'] = 'staff/index';

// 顧客資料維護
$route['client/add'] = 'client/add'; // 新增顧客資料
$route['client/edit'] = 'client/edit'; // 編輯顧客資料
$route['client/(:any)'] = 'client/index';

// 權限群組維護
$route['groups/add'] = 'groups/add'; // 新增權限群組
$route['groups/edit'] = 'groups/edit'; // 編輯權限群組
$route['groups/(:any)'] = 'groups/index';

// 帳號維護
$route['users/add'] = 'users/add'; // 新增帳號
$route['users/edit'] = 'users/edit'; // 編輯帳號
$route['users/(:any)'] = 'users/index';

// 個人帳號維護
$route['personal/(:any)'] = 'personal/index';

$route['default_controller'] = 'dashboard/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
