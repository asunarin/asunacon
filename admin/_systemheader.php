<?php
defined('SW_') or die('Access Error');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php __((SWAdmin::$title?SWAdmin::$title.' - ':'').SW::getOption('title'));?></title>
<meta name="Generator" content="Swan">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
<link type="text/css" href="_loadcss.php?load=reset,style" rel="stylesheet" />
<?php

//需要引入的css文件
if(SWAdmin::$css){
	__('<link type="text/css" href="_loadcss.php?load='.implode(',',SWAdmin::$css).'" rel="stylesheet" />'."\n");
}

?>
<script src="_loadjs.php?load=jquery,jquery_json" type="text/javascript" charset="utf-8"></script>
<script src="_loadjs.php?load=swjs" type="text/javascript" charset="utf-8"></script>
<?php

//是否需要导入指定文件
if(SWAdmin::$js){
	__('<script src="_loadjs.php?load='.implode(',',SWAdmin::$js).'" type="text/javascript" charset="utf-8"></script>'."\n");
}

//显示session提示信息
SWAdmin::showMessage();

?>

</head>
<body>
