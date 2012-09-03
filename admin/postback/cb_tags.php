<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	case 'edit':

		$objTag=new SWTag();
		
        $name=SWFunc::checkString(SW::request('name','post'));
		$changeTo=SWFunc::checkString(SW::request('changeTo','post'));

		//更新操作
		if(!$name || !$changeTo || !$objTag->editByName($name,$changeTo)){
			SWAdmin::postJson(LANG('Tag Duplicate'),1);
		}else{
			SWAdmin::postJson('',0);
			SWAdmin::setMessage(LANG('Tag Edit Succeed'),0);
		}

	break;

	case 'delete':

		$objTag=new SWTag();
		
        $name=SWFunc::checkString(SW::request('name','post'));

		$objTag->deleteByName($name);
		SWAdmin::postJson('',0);
		SWAdmin::setMessage(LANG('Tag Delete Succeed'),0);

	break;
	
}

?>
