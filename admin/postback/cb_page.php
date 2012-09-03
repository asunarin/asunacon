<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){

	case 'save':

		$objPage=new SWPage();

		$strErr='';

		$objPage->id=SWFunc::checkInt(SW::request('id','post'));
		
		//注意这里，如果有id，那么要先获取对象getById，防止默认值被篡改
		if($objPage->id) $objPage->getById($objPage->id);
		
		$objPage->parentid=SWFunc::checkInt(SW::request('parentid','post'));
		
		$objPage->title=SWFunc::closeHtml(SW::request('title','post'));

		$objPage->content=SWFunc::checkString(SW::request('content','post'));

		$objPage->alias=SWFunc::closeHtml(SW::request('alias','post'));

		$objPage->posttime=SWFunc::checkToTimestamp(SW::request('posttime','post'));

		$objPage->edittime=time();

		$objPage->state=SWFunc::checkInt(SW::request('state','post'));

		$objPage->flag=1;

		$objPage->ordernum=SWFunc::checkInt(SW::request('ordernum','post'));
		
		$objPage->onmenu=$objPage->parentid ? 0 : SWFunc::checkInt(SW::request('onmenu','post'));

		if(!$objPage->title && $objPage->state==1){
			$strErr=LANG('Invalid Page_Title');
		}

		if(!$objPage->alias && $objPage->state==1){
			$strErr=LANG('Invalid Page_Alias');
		}
		
		//检查父页面是否存在
		if($objPage->parentid && !$objPage->getParent()){
			$strErr=LANG('Invalid Page_Parent');
		}

		if(!$strErr){
		
			$objPage->save();

			if($objPage->id>0){

				SWAdmin::postJson('',0,array('id'=>$objPage->id));
				if(!SW::request('autosave','post')) SWAdmin::setMessage(LANG('Save Page Succeed'),0);
			
			//如果不成功（alias重复）
			}else{
				
				$strErr=LANG('Page_Alias Duplicate');
				SWAdmin::postJson($strErr,1);

			}

		}else{
			
			SWAdmin::postJson($strErr,1);

		}

	break;
	
}

?>
