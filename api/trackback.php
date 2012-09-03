<?php
define('SW_',true);
require_once('_common.php');

//评论对象
$objTb=new SWTrackback();

//获取页面传递的参数
$objTb->postid=SWFunc::checkInt(SW::request('id','get'));
$objTb->blog_name=SWFunc::closeHtml(SW::request('blog_name','post'),250,'...');
$objTb->url=SWFunc::checkUrl(SW::request('url','post'));
$objTb->title=SWFunc::closeHtml(SW::request('title','post'),250,'...');
$objTb->expert=SWFunc::closeHtml(SW::request('expert','post'),250,'...');

//首先判断系统是否允许接收trackback
if(!SW::getOption('receivetrackback')) $objTb->sendResponse(false,'Sorry, trackbacks are closed for this item.');

//是否有ID
if(!$objTb->postid) $objTb->sendResponse(false,'I really need an ID for this to work.');

//判断信息是否完整
if(!$objTb->blog_name || !$objTb->url || !$objTb->title) $objTb->sendResponse(false,'There is no infomation.');

//该文章是否存在或者允许评论
$objPost=$objTb->getPost();
if(!$objPost->id || !$objPost->allowcomment || $objPost->state!=1 || $objPost->flag!=1) $objTb->sendResponse(false,'The ID is invalid.');

//判断是否已经发送过了
if($objTb->isPinged()) $objTb->sendResponse(false,'We already have a ping from that URL for this post.');

//如果以上判断都正确，那么保存trackback
$objTb->id=0;
$objTb->ip=SWFunc::getIp();
$objTb->posttime=time();
$objTb->state=SW::getOption('checkcomment')?0:1;
$objTb->add();

//发送响应
$objTb->sendResponse(true,'');

?>