<?php
define('SW_',true);
require_once('_common.php');

$postPage=SW::request('postpage','get');

//判断用户登录
if($postPage!='login') SWAdmin::checkLogin(true,true);

//根据postpage参数引入postback页面
if($postPage) require(SW::path('admin.postback.cb_'.$postPage));

?>
