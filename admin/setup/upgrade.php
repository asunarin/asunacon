<?php
define('SW_',true);
define('SW_PAGE_UPGRADE',true);

require_once('../../framework/init.php');

//判断是否是非法请求
if(SW::$preAction!='upgrade') die('Access Error');

//初始化安装类
$objUpgrade=new SWUpgrade();

//执行升级操作
$succeed=$objUpgrade->upgrade();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Update To Swan <?php __(SW_VERSION);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" href="../css/setup.css" rel="stylesheet" />
</head>
<body>

<div class="title">Complete</div>

<?php if(!$succeed){?>
<div class="error">
<ul>无法升级！您的版本低于1.0.0，请重新安装。</ul>
</div>
<?php }else{?>
<div class="window">
	<h2>系统升级成功！</h2>
	<p>请<a href="<?php __(SW::getOption('url'));?>">点击这里</a>返回网站首页。</p>
	<div style="clear:both;"></div>
</div>
<?php }?>

</body>
</html>
