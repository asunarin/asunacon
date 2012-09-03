<?php
if(!defined('INC_ROOT')){die('Forbidden Access');}
return array(
	'UPLOAD_MAX_SIZE'           => 32*1000*1000,
	'SEARCH_ALLOW_TYPE'         => array(
		'php','php3','asp','txt','jsp','inc',
		'ini','pas','cpp','bas','in','lang',
		'out','htm','html','cs','config','js',
		'htc','css','c','sql','bat','vbs','cgi',
		'dhtml','shtml','xml','xsl','aspx','tpl',
		'ihtml','htaccess','dwt','lib','lbi',
	),
);
?>