<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	case 'search':

		$objComment=new SWComment();
		
		$arrCondition=array();

		if(SW::request('search','post')!=''){
			$arrCondition['search']=SW::request('search','post');
		}
		if(SW::request('state','post')!=''){
			$arrCondition['state']=SW::request('state','post');
		}
		if(SW::request('type','post')!=''){
			$arrCondition['type']=SW::request('type','post');
		}
		if(SW::request('postid','post')!=''){
			$arrCondition['postid']=SW::request('postid','post');
		}

		$arrPages=explode(',',SW::request('pages','post'));
		if($arrPages[1]-($arrPages[0]-1)>0){
			$arrPages[0]=($arrPages[0]-1>0)?$arrPages[0]-1:0;
			$arrPages[1]=$arrPages[1];
		}else{
			$arrPages[0]=0;
			$arrPages[1]=0;
		}
		
		//首先取得这个查询一共有多少条记录
		$maxPages=$objComment->getMoreCount($arrCondition);
		
		//然后根据记录数查询，并取得查询数目
		$arrTmp=$objComment->getMore($arrCondition,array('posttime'=>'desc'),$arrPages[0],$arrPages[1]);
		$thisPages=count($arrTmp);

		//循环对象数组
		$arrComment=array();
		foreach($arrTmp as $value){
			$arrComment[]=array(
				'id'=>$value->id,
				'postid'=>$value->postid,
				'posttitle'=>$value->getPost()->title, //获取父级文章对象的title属性
				'author'=>$value->author,
				'content'=>SWFunc::closeHtml($value->content),
				'email'=>$value->email,
				'url'=>$value->url,
				'ip'=>$value->ip,
				'posttime'=>date(LANG('time_datetime'),$value->posttime),
				'state'=>$value->state,
				'statename'=>SWAdmin::getCommentState($value->state),
				'type'=>$value->type,
				'typename'=>SWAdmin::getCommentType($value->type)
			);
		}
		
		SWAdmin::postJson('',0,array('comment'=>$arrComment,'maxpage'=>$maxPages,'thispage'=>$thisPages));

	break;
	
	case 'state':
		
		$objComment=new SWComment();
		
		$arrCondition=array();

		$arrCondition['ids']=SW::request('ids','post');

		$state=SWFunc::checkInt(SW::request('state','post'));

		//返回信息语句
		$jLang='';
		switch($state){
			case 1:
				$jLang=LANG('Selections_Approved');
			break;
			case 0:
				$jLang=LANG('Selections_Unapproved');
			break;
			case 2:
				$jLang=LANG('Selections_Spam');
			break;
		}

		if($jLang){
		
			$arrComment=$objComment->getMore($arrCondition,array('posttime'=>'desc'),0,0);

			//循环对象数组，进行approved操作
			foreach($arrComment as $value){
				$value->setState($state);
			}
			
			SWAdmin::postJson($jLang,0);

		}else{
			
			SWAdmin::postJson(LANG('Operating Fail'),1);
		
		}

	break;

	case 'delete':
		
		$objComment=new SWComment();
		
		$arrCondition=array();

		$arrCondition['ids']=SW::request('ids','post');
		
		$arrComment=$objComment->getMore($arrCondition,array('posttime'=>'desc'),0,0);

		//循环对象数组，进行approved操作
		foreach($arrComment as $value){
			$value->delete();
		}

		SWAdmin::postJson(LANG('Selections_Deleted'),0);

	break;
	
}

?>
