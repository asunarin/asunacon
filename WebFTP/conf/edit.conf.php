<?php
if(!defined('INC_ROOT')){die('Forbidden Access');}
return array(
    'EDITOR_CONF'     => array('WIDTH'=>900, 'HEIGHT'=>600),
	'EDIT_ALLOW_TYPE' => array(
		'高亮语法'    => array('扩展名1', '扩展名2', '扩展名N'),
		'asp' 		  => array('asp', 'aspx'),
		'autoit' 	  => array('autoit'),
		'csharp' 	  => array('csharp'),
		'css'		  => array('css'),
		'generic' 	  => array('generic'),
		'html' 		  => array('htm', 'html', 'tpl'),
		'java' 		  => array('jar'),
		'javascript'  => array('js'),
		'perl' 		  => array('perl'),
		'php' 		  => array('php', 'php3'),
		'ruby' 		  => array('ruby'),
		'sql' 		  => array('sql'),
		'vbscript'	  => array('vbs'),
		'xsl'	      => array('php'),
		'text'	      => array('log', 'txt', 'ini'),		
	),
);
?>