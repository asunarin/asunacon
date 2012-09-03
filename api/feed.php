<?php
define('SW_',true);
require_once('_common.php');

//获取feed模式
$type=SWFunc::checkString(SW::request('type','get'));

//查询条数
$strLimit=0;

//构建文章对象，获取对象数组
$objPost=new SWPost();
$arrPost=$objPost->getMore(array('state'=>'1'),array('posttime'=>'desc'),0,SW::getOption('feedcount'));

//构建feed对象
$rss = new UniversalFeedCreator();

//feed元素
$rss->title=SW::getOption('title');
$rss->description=SW::getOption('description');
$rss->link=SW::getOption('url');
$rss->syndicationURL=SW::getOption('url').'api/feed.php?type='.$type;

//文章item元素
//创建路由对象，用于获取文章地址
$objRouter=new SWRouter();

foreach($arrPost as $value){
	$item = new FeedItem();
	$item->title = $value->title;
	$item->link = $objRouter->getUrl('post',array('postid'=>$value->id));
	$item->description = SWFunc::closeHtml($value->content,500,' [...]');
	$item->author = SW::getOption('email');
	$item->date = $value->posttime;
	
	$rss->addItem($item);
}


$rss->outputFeed($type); 

?>
