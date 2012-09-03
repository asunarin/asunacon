<?php
if(!defined('INC_ROOT')){die('Forbidden Access');}
return array(
	'LIST_VIEW_ON'         => true,
	'LIST_ORDER_SORT'  	   => 'asc',//asc:顺序 desc:倒序
	'LIST_ORDER_TYPE' 	   => 'name',//1:name 2:size 3:ext 4:mtime
	'DISPLAY_NOTALLOW'       => array(
		'./../WebFTP/',
		'./../WebSQL/',
	
	),
);
?>