<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	case 'search':

		$objPost=new SWPost();
		
		$arrCondition=array();

		if(SW::request('search','post')!=''){
			$arrCondition['search']=SW::request('search','post');
		}
		if(SW::request('state','post')!=''){
			$arrCondition['state']=SW::request('state','post');
		}
		if(SW::request('trash','post')!=''){
			$arrCondition['trash']=SW::request('trash','post');
		}
		if(SW::request('tag','post')!=''){
			$arrCondition['tag']=SW::request('tag','post');
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
		$maxPages=$objPost->getMoreCount($arrCondition);
		
		//然后根据记录数查询，并取得查询数目
		$arrTmp=$objPost->getMore($arrCondition,array('posttime'=>'desc'),$arrPages[0],$arrPages[1]);
		$thisPages=count($arrTmp);

		//循环对象数组
		$arrPost=array();
		foreach($arrTmp as $value){

			$arrPost[]=array(
				'id'=>$value->id,
				'title'=>$value->title,
				'content'=>SWFunc::closeHtml($value->content,200,'[...]'),
				'state'=>$value->state,
				'statename'=>$value->flag>0?SWAdmin::getPostState($value->state):LANG('State_Deleted'),
				'posttime'=>date(LANG('time_datetime'),$value->posttime),
				'flag'=>$value->flag,
				'comment_count'=>$value->getCommentCount(),
				'tag'=>$value->getTagNames()
			);

		}
		
		SWAdmin::postJson('',0,array('post'=>$arrPost,'maxpage'=>$maxPages,'thispage'=>$thisPages));

	break;
	
	case 'trash':
		
		$objPost=new SWPost();
		
		$arrCondition=array();

		$arrCondition['ids']=SW::request('ids','post');
		
		$arrPost=$objPost->getMore($arrCondition,array('posttime'=>'desc'),0,0);

		//循循环对象数组，进行trash操作
		foreach($arrPost as $value){
			$value->trash();
		}
		
		SWAdmin::postJson(LANG('Selections_Deleted'),0);

	break;

	case 'resume':
		
		$objPost=new SWPost();
		
		$id=SW::request('id','post');
		
		$objPost->getById($id);

		$objPost->trash(true);
		
		SWAdmin::postJson(LANG('Selections_Resume'),0);

	break;

	case 'delete':
		
		$objPost=new SWPost();
		
		$id=SW::request('id','post');
		
		$objPost->getById($id);

		$objPost->delete();

		SWAdmin::postJson(LANG('Selections_Deleted'),0);

	break;
	
}

?>
