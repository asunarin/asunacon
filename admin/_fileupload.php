<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin(true,true);

//上传
$objAttr=new SWAttachment();
$res=$objAttr->upload();

//成功，发送响应
if($res){

	header('Content-type: text/html; charset=UTF-8');
	echo json_encode(array('error' => 0, 'url' => $res['url'], 'size'=> $res['size'], 'path'=>$res['path'], 'name'=>$res['name']));

//发送错误响应代码
}else{

	header('Content-type: text/html; charset=UTF-8');
	echo json_encode(array('error' => 1, 'message' => LANG('Upload_Error')));
	
}

?>
