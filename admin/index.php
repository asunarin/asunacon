<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin();

//自动跳转到发布文章页面
header('Location: post.php');
exit();
?>
