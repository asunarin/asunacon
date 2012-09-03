<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	case 'search':

		$objPage=new SWPage();
		
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

		$arrPages=explode(',',SW::request('pages','post'));
		if($arrPages[1]-($arrPages[0]-1)>0){
			$arrPages[0]=($arrPages[0]-1>0)?$arrPages[0]-1:0;
			$arrPages[1]=$arrPages[1];
		}else{
			$arrPages[0]=0;
			$arrPages[1]=0;
		}
		
		//首先取得这个查询一共有多少条记录
		$maxPages=$objPage->getMoreCount($arrCondition);
		
		//然后根据记录数查询，并取得查询数目（树状结构）
		//$arrTmp=$objPage->getMore($arrCondition,array('ordernum'=>'asc','id'=>'asc'),$arrPages[0],$arrPages[1]);
		$arrTmp=$objPage->getMoreTree(0,$arrCondition,array('ordernum'=>'asc','id'=>'asc'),$arrPages[0],$arrPages[1]);
		$thisPages=count($arrTmp);

		//循环对象数组
		$arrPage=array();
		foreach($arrTmp as $value){
			
			//获取父对象
			$objParent=$value->getParent();

			$arrPage[]=array(
				'id'=>$value->id,
				'title'=>$value->title,
				'content'=>SWFunc::closeHtml($value->content,200,'[...]'),
				'alias'=>$value->alias,
				'posttime'=>date(LANG('time_datetime'),$value->posttime),
				'state'=>$value->state,
				'statename'=>$value->flag>0?SWAdmin::getPageState($value->state):LANG('State_Deleted'),
				'flag'=>$value->flag,
				'parentname'=>$objParent?$objParent->title:'',
				'onmenu'=>$value->onmenu,
				'level'=>$value->getLevel()
			);

		}
		
		SWAdmin::postJson('',0,array('page'=>$arrPage,'maxpage'=>$maxPages,'thispage'=>$thisPages));

	break;
	
	case 'trash':
		
		$objPage=new SWPage();
		
		$arrCondition=array();

		$arrCondition['ids']=SW::request('ids','post');
		
		$arrPage=$objPage->getMore($arrCondition,array('ordernum'=>'asc'),0,0);

		//循环对象数组，进行trash操作
		foreach($arrPage as $value){
			$value->trash();
		}
		
		SWAdmin::postJson(LANG('Selections_Deleted'),0);

	break;

	case 'resume':
		
		$objPage=new SWPage();
		
		$id=SW::request('id','post');
		
		$objPage->getById($id);

		$objPage->trash(true);
		
		SWAdmin::postJson(LANG('Selections_Resume'),0);

	break;

	case 'delete':
		
		$objPage=new SWPage();
		
		$id=SW::request('id','post');
		
		$objPage->getById($id);

		$objPage->delete();

		SWAdmin::postJson(LANG('Selections_Deleted'),0);

	break;
	
}

?>
