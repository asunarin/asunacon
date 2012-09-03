<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){

	case 'save':

		$objPost=new SWPost();

		$strErr='';

		$objPost->id=SWFunc::checkInt(SW::request('id','post'));
		
		$objPost->title=SWFunc::closeHtml(SW::request('title','post'));

		$objPost->content=SWFunc::checkString(SW::request('content','post'));

		$objPost->state=SWFunc::checkInt(SW::request('state','post'));

		$objPost->allowcomment=SWFunc::checkInt(SW::request('allowcomment','post'))>0?1:0;

		$objPost->posttime=SWFunc::checkToTimestamp(SW::request('posttime','post'));

		$objPost->edittime=time();

		$objPost->alias=SWFunc::closeHtml(SW::request('alias','post'));

		$objPost->flag=1;

		if(!$objPost->title && $objPost->state==1){
			$strErr=LANG('Invalid Post_Title');
		}
		
		$arrTag=array();
		$strTmp=SWFunc::closeHtml(SW::request('tag','post'));
		if($strTmp){
			$arrTag=explode(',',$strTmp);

			//验证标签是否都正确
			foreach($arrTag as $value){

				if(!SWFunc::checkString($value)){
					$strErr=LANG('Invalid Tag');
					break;
				}

			}
		}

		if(!$strErr){
		
			$objPost->save($arrTag);
			
			$sent=array();

			//获取文章地址
			$objRouter=new SWRouter();
			$source=$objRouter->getUrl('post',array('postid'=>$objPost->id));
			
			//发送pingback，验证发送条件
			if(SW::getOption('pingback') && $objPost->state==1 && $objPost->flag==1){
				$sent=$objPost->getLinks();
			}

			SWAdmin::postJson('',0,array('id'=>$objPost->id,'source'=>$source,'pingback'=>$sent));
			if(!SW::request('autosave','post')) SWAdmin::setMessage(LANG('Save Post Succeed'),0);

		}else{
			
			SWAdmin::postJson($strErr,1);

		}

	break;
	
}

?>
