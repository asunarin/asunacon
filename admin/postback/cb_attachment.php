<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	case 'search':

		$objAtt=new SWAttachment();
		
		//获取要列出的目录，如果为空则是附件根目录，这里永远是相对路径
        $strPath=SW::dirPath('attached'.(SW::request('path','post')?'.'.SW::request('path','post'):''));

		//获取目录下的子目录和文件
		$arrList=$objAtt->getList($strPath);

        SWAdmin::postJson('',0,array('list'=>$arrList,'path'=>$strPath));

	break;
	
}

?>
