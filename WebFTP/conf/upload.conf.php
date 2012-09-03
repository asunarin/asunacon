<?php 
if(!defined('INC_ROOT')){die('Forbidden Access');}
return array(
	'UPLOAD_MAX_SIZE'           => 1024*1024*1024,
	'UPLOAD_ALLOW_TYPE'         => array(
		'*',
		'zip'
	),
);
?>