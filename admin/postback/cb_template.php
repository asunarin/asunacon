<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){

	case 'save':
		
		$objOption=new SWOption();
		
		$objOption->template=SWFunc::closeHtml(SW::request('template','post'));
		if(!$objOption->template) $objOption->template='default';

		$objOption->saveTemplate();
		
		//重新载入设置信息和登录信息
		//SW::getOption()->load();
		SWAdmin::postJson('',0);
		SWAdmin::setMessage(LANG('Template Setting Succeed'),0);

	break;
	
}

?>
