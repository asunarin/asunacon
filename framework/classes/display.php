<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 前端模板显示类从路由类继承，是模板文件的父类
* 模板中可以定义display.php继承这个类，用于添加新方法或重写原方法
======================================================================*/
class SWDisplay extends SWRouter{
	
	//模板路径
	public $templatePath='';
	
	//模板URL
	public $templateUrl='';
	
	//根网址
	public $baseUrl='';
	
	//模板对象
	public $template=null;
	
	/*=====================================================================
	* 初始化和验证
	======================================================================*/
	public function __construct(){
		
		//初始化
		$this->templatePath=SWTemplate::getTemplatePath();
		$this->templateUrl=SWTemplate::getTemplateUrl();
		$this->baseUrl=SW::getOption('url');
		$this->template=new SWTemplate();
		
		//是否登录
		$this->login=SWAdmin::checkLogin(false);
		
		//显示用的一些公共属性
		$this->pageSize=0;
		$this->recordCount=0;
		$this->posts=array();
		$this->post=null;
		$this->pages=array();
		$this->page=null;

	}
	
	/*=====================================================================
	* 获取这个类的实例，如果在模板中定义了display类继承于这个类，那么返回模板中子类的实例
	* 注意这是个静态方法
	======================================================================*/
	public static function getClass(){
		
		$objDisplay=new self();
		
		//首先判断文件是否存在
		if(file_exists(SW::path($objDisplay->templatePath.'.display'))){
			require_once(SW::path($objDisplay->templatePath.'.display'));
		}
		
		//判断是否存在子类TPLDisplay
		if(class_exists('TPLDisplay')){
			return new TPLDisplay();
		}else{
			return new self();
		}

	}
	

	/*=====================================================================
	* 执行模板
	======================================================================*/
	public function display(){

		//获取请求页面参数
		$this->getRequest();
		
		$method='';
		
		//检查是否是actions请求
		if($this->requestPage=='actions'){
			
			$method='action_'.$this->request['action'];
			
		}else{
		
			$method='show_'.$this->requestPage;
			
		}
			
		//执行函数可是为'show_'.$this->requestPage
		if(method_exists($this,$method)){
			
			$this->$method();
			
		}else{
			
			$this->show_404();
			
		}
			
	}
	
	/*=====================================================================
	* 显示错误信息并终止页面，这个是全局的，不依赖模板
	* 传入错误信息可以是字符串，也可以是数组
	======================================================================*/
	public function show_error($err){
		$strError='<ul>'."\n";
		if(is_array($err)){
			foreach($err as $value){
				$strError.='<li>'.$value.'</li>'."\n";
			}
		}else{
			$strError.='<li>'.$err.'</li>'."\n";
		}
		$strError.='<ul>';
		
		if(file_exists(SW::path($this->templatePath.'.error'))){
			
			require(SW::path($this->templatePath.'.error'));
			
		}else{
		
			$strHtml='
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Error</title>
			</head>
			<body style="margin:0;padding:0;font-size:12px;color:#333;font-family:Verdana;">
			<div style="width:420px;margin:40px auto 0 auto;background-color:#191919;color:#8CC165;padding:6px;font-weight:bold;">Error</div>
			<div style="width:420px;margin:0px auto;padding:6px;background-color:#eee;"><ul style="margin:0 4px;padding:0;list-style-type:none;line-height:200%;">'.$strError.'</ul></div>
			<div style="width:420px;margin:0px auto;padding:6px;text-align:center;"><a href="javascript:window.history.back();" style="color:#FF9999;">Back</a> | <a href="'.$this->getUrl('index',array()).'" style="color:#FF9999;">Home</a></div>
			<div style="width:420px;margin:20px auto;text-align:center;font-size:11px;">Swan '.SW_VERSION.'</div>
			</body>
			</html>
			';
			__($strHtml);
			exit();
			
		}
		
	}

	/*=====================================================================
	* 显示404页面
	======================================================================*/
	public function show_404(){
		
		if(file_exists(SW::path($this->templatePath.'.404'))){
			
			require(SW::path($this->templatePath.'.404'));
			
		}else{
			
			$this->show_error('404 Page Not Found!');
			
		}

	}
	
	/*=====================================================================
	* 显示主页，默认是调用show_list，用户可以重载这个方法
	======================================================================*/
	public function show_index(){
		
		if(file_exists(SW::path($this->templatePath.'.index'))){
			
			require(SW::path($this->templatePath.'.index'));
			
		}else{
			
			$this->show_list();
			
		}

	}
	
	/*=====================================================================
	* 显示文章列表页面
	======================================================================*/
	public function show_list($thePageSize=5){
		
		$objPost=new SWPost();

		//获取分页变量
		$intPSize=$thePageSize>0?$thePageSize:5;
		$intAPage=empty($this->request['pagenum'])?1:$this->request['pagenum'];
		
		//分页获取文章对象数组
		$posts=$objPost->getMore(array('state'=>1),array('posttime'=>'desc'),(($intAPage-1)*$intPSize),$intAPage*$intPSize);
		
		//设置posts属性
		$this->posts=$posts;
		
		//设置分页属性
		$this->pageSize=$intPSize;
		$this->recordCount=$objPost->getMoreCount(array('state'=>1));
		
		if(file_exists(SW::path($this->templatePath.'.list'))){
			
			require(SW::path($this->templatePath.'.list'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}

	}
	
	/*=====================================================================
	* 显示文章搜索页面
	======================================================================*/
	public function show_search($thePageSize=10){
		
		$objPost=new SWPost();
		
		//获取查询参数
		$arrCondition=array();
		$arrCondition['state']='1';
		
		//内容搜索
		if(!empty($this->request['searchstring'])){
			$arrCondition['search']=SWFunc::checkString($this->request['searchstring']);
			
		//日期归档
		}elseif(!empty($this->request['year'])){
			$arrCondition['year']=SWFunc::checkInt($this->request['year']);
			$arrCondition['month']=SWFunc::checkInt(empty($this->request['month'])?'':$this->request['month']);
			$arrCondition['day']=SWFunc::checkInt(empty($this->request['day'])?'':$this->request['day']);
			
		//根据标签搜索
		}elseif(!empty($this->request['tagname'])){
			$arrCondition['tag']=SWFunc::checkString($this->request['tagname']);
			
		}
		
		//获取分页变量
		$intPSize=$thePageSize>0?$thePageSize:10;
		$intAPage=empty($this->request['pagenum'])?1:$this->request['pagenum'];
				
		//分页获取文章对象数组
		$posts=$objPost->getMore($arrCondition,array('posttime'=>'desc'),(($intAPage-1)*$intPSize),$intAPage*$intPSize);
		
		//设置posts属性
		$this->posts=$posts;
		
		//设置分页属性
		$this->pageSize=$intPSize;
		$this->recordCount=$objPost->getMoreCount($arrCondition);

		if(file_exists(SW::path($this->templatePath.'.search'))){
			
			require(SW::path($this->templatePath.'.search'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}

	}
	
	/*=====================================================================
	* 显示文章页面
	======================================================================*/
	public function show_post(){
		
		//获取postid
		$postid=empty($this->request['postid'])?0:SWFunc::checkInt($this->request['postid']);
		if(!$postid) $this->show_404();
		
		$post=new SWPost();
		
		//如果没查到，错误页面
		if(!$post->getById($postid) || $post->flag==0 || $post->state==0) $this->show_404();
		
		//查询后一篇文章
		$newer=$post->getNewer();
		
		//查询前一篇文章
		$older=$post->getOlder();
		
		//设置pingback的http头
		//查看是否允许接受pingback
		if(SW::getOption('receivepingback') && $post->allowcomment) header('X-Pingback: '.$this->baseUrl.'api/xmlrpc.php');
		
		//设置post属性
		$this->post=$post;

		if(file_exists(SW::path($this->templatePath.'.post'))){
			
			require(SW::path($this->templatePath.'.post'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}

	}
	
	/*=====================================================================
	* 显示静态页面
	======================================================================*/
	public function show_page(){
		
		//获取pageid和alias
		$pageid=empty($this->request['pageid'])?0:SWFunc::checkInt($this->request['pageid']);
		$alias=empty($this->request['pagename'])?'':SWFunc::checkString($this->request['pagename']);
		if(!$pageid && !$alias) $this->show_404();
				
		$page=new SWPage();
		
		//根据不同的参数获取页面对象
		if($pageid){
			
			if(!$page->getById($pageid) || $page->flag==0 || $page->state==0) $this->show_404();
			
		}else{
			
			if(!$page->getByAlias($alias) || $page->flag==0 || $page->state==0) $this->show_404();

		}

		//设置page参数
		$this->page=$page;

		if(file_exists(SW::path($this->templatePath.'.page'))){
			
			require(SW::path($this->templatePath.'.page'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}
		
	}
	
	/*=====================================================================
	* 显示归档页面
	======================================================================*/
	public function show_archives(){

		if(file_exists(SW::path($this->templatePath.'.archives'))){
			
			require(SW::path($this->templatePath.'.archives'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}
		
	}
	
	/*=====================================================================
	* 显示评论
	======================================================================*/
	public function show_comments(){
		
		//获取post
		if(!$this->post) $this->show_404();
		$post=$this->post;
		
		if(file_exists(SW::path($this->templatePath.'.comments'))){
			
			require(SW::path($this->templatePath.'.comments'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}

	}
	
	/*=====================================================================
	* 显示评论和回复列表
	* 如果传入了commentid，那么就是显示评论下的回复
	======================================================================*/
	public function show_comments_list($commentid=0,$level=0){
		
		//获取post
		if(!$this->post) $this->show_404();
		$post=$this->post;
		
		$objComment=new SWComment();
		
		if(!$commentid){
			
			$comments=$objComment->getMore(array('onlyparent'=>true,'postid'=>$post->id,'ip'=>SWFunc::getIp()),array('posttime'=>'asc'),0,0);
		
		//显示回复
		}else{
			
			$comments=$objComment->getMore(array('replay'=>$commentid,'ip'=>SWFunc::getIp()),array('posttime'=>'asc'),0,0);
		$level++;
		
		}
		
		if(file_exists(SW::path($this->templatePath.'.comments_list'))){
			
			require(SW::path($this->templatePath.'.comments_list'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}

	}
	
	/*=====================================================================
	* 显示发评论表单
	======================================================================*/
	public function show_comments_form(){

		//获取post
		if(!$this->post) $this->show_404();
		$post=$this->post;
		
		//如果登录，获取昵称等信息
		$author=$this->login?SW::getOption('author'):'';
		$email=$this->login?SW::getOption('email'):'';
		$url=$this->login?SW::getOption('url'):'';
		
		if(file_exists(SW::path($this->templatePath.'.comments_form'))){
			
			require(SW::path($this->templatePath.'.comments_form'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}

	}
	
	/*=====================================================================
	* 显示sidebar
	======================================================================*/
	public function show_sidebar(){

		if(file_exists(SW::path($this->templatePath.'.sidebar'))){
			
			require(SW::path($this->templatePath.'.sidebar'));
			
		}else{
			
			$strHtml='';
			
			__($strHtml);
			
		}

	}
	
	/*=====================================================================
	* 显示header
	======================================================================*/
	public function show_header($title=''){

		if(file_exists(SW::path($this->templatePath.'.header'))){
			
			require(SW::path($this->templatePath.'.header'));
			
		}else{
			
			__('
			<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>'.SW::getOption('title').($title?' &raquo; '.$title:'').'</title>
				<meta name="generator" content="Swan '.SW_VERSION.'" />
				<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="'.$this->baseUrl.'api/feed.php?type=rss2.0" />
				<link rel="alternate" type="application/atom+xml" title="ATOM 1.0" href="'.$this->baseUrl.'api/feed.php?type=atom1.0" />
			');
			$this->show_header_info();
			__('
			</head>
			<body>
			');
			
		}

	}

	/*=====================================================================
	* 显示footer
	======================================================================*/
	public function show_footer(){

		if(file_exists(SW::path($this->templatePath.'.footer'))){
			
			require(SW::path($this->templatePath.'.footer'));
			
		}else{
			
			$strHtml='
			</body>
			</html>
			';
			
			__($strHtml);
			
		}

	}

	/*=====================================================================
	* 显示菜单
	======================================================================*/
	public function show_menu(){
		
		$objPage=new SWPage();
		$menus=$objPage->getMore(array('state'=>1,'flag'=>1,'onmenu'=>1),array('ordernum'=>'asc','id'=>'asc'));

		if(file_exists(SW::path($this->templatePath.'.menu'))){
			
			require(SW::path($this->templatePath.'.menu'));

		}else{
			
			$strHtml='
			<ul class="sw_menu">
				<li><a href="'.$this->getUrl('index',array()).'" '.($this->requestPage=='index' ? 'class="sw_menu_current"' : '').'>'.LANG('Display_Index').'</a></li>
			';
			foreach($menus as $menu){
			$strHtml.='
				<li><a href="'.$this->getUrl('page',array('pagename'=>$menu->alias)).'" '.(!empty($this->page) && $this->page->alias==$menu->alias ? 'class="sw_menu_current"' : '').'>'.$menu->title.'</a></li>
			';
			}
			$strHtml.='
				<li><a href="'.$this->getUrl('archives',array()).'" '.($this->requestPage=='archives' ? 'class="sw_menu_current"' : '').'>'.LANG('Display_Archives').'</a></li>
			</ul>
			';
			
			__($strHtml);
			
		}

	}

	/*=====================================================================
	* 显示页面分页信息
	======================================================================*/
	public function show_pagenavbar($prevUrl='',$nextUrl='',$prevName='',$nextName=''){
		
		$prevName=$prevName?$prevName:LANG('Display_Newer');
		$nextName=$nextName?$nextName:LANG('Display_Older');
		
		//如果没有传入链接，那么启用默认分页方法
		if(!$prevUrl || !$nextUrl){
		
			//分页操作
			$intPSize=empty($this->pageSize)?10:$this->pageSize;
			$intAPage=empty($this->request['pagenum'])?1:SWFunc::checkInt($this->request['pagenum']);
			$maxPage=ceil($this->recordCount/$intPSize)>0?ceil($this->recordCount/$intPSize):1;
			$intAPage=$intAPage>$maxPage?$maxPage:$intAPage;
			
			//设置页码参数
			if($intAPage<$maxPage){
				$this->request['pagenum']=$intAPage+1;
				$nextUrl=$this->getUrl();
			}
			if($intAPage>1){
				$this->request['pagenum']=$intAPage-1;
				$prevUrl=$this->getUrl();
			}
			
		}

		if(file_exists(SW::path($this->templatePath.'.pagenavbar')) && !empty($this->recordCount)){

			require(SW::path($this->templatePath.'.pagenavbar'));
		
		}else{
			
			$strHtml='
			<div class="sw_navigation">
				<div class="sw_navigation_prev">
					'.($nextUrl?'<a href="'.$nextUrl.'">&laquo; '.$nextName.'</a>':'').'
				</div>
				<div class="sw_navigation_next">
					'.($prevUrl?'<a href="'.$prevUrl.'">'.$prevName.' &raquo;</a>':'').'
				</div>
			</div>
			';
			
			__($strHtml);
			
		}

	}

	/*=====================================================================
	* 显示搜索框
	======================================================================*/
	public function show_searchform(){

		if(file_exists(SW::path($this->templatePath.'.searchform'))){
			
			require(SW::path($this->templatePath.'.searchform'));
			
		}else{
			
			$strHtml='
			<form class="sw_searchform" onsubmit="javascript:if(this.getElementsByTagName(\'input\')[0].value){window.location.href=\''.$this->getUrl('search',array('searchstring'=>"'+encodeURIComponent(this.getElementsByTagName('input')[0].value)+'"),false).'\';} return false;">
				<label for="searchstring">'.LANG('Display_Search').':</label>
				<input type="text" name="searchstring" value="'.(empty($this->request['searchstring'])?'':$this->request['searchstring']).'" />
				<input type="submit" value="'.LANG('Display_Search').'" />
			</form>
			';
			
			__($strHtml);
			
		}

	}
	
	/*=====================================================================
	* 获取最新文章列表
	======================================================================*/
	public function show_recent_posts($count=10){
		
		$objPost=new SWPost();
		$posts=$objPost->getMore(array('state'=>1),array('posttime'=>'desc'),0,$count);
		
		if(file_exists(SW::path($this->templatePath.'.recent_posts'))){
			
			require(SW::path($this->templatePath.'.recent_posts'));
			
		}else{
			
			$strHtml='
			<ul class="sw_recent_posts">
			';
			foreach($posts as $post){
			$strHtml.='
			<li><a href="'.$this->getUrl('post',array('postid'=>$post->id)).'">'.$post->title.'</a> <span>'.date('Y-m-d',$post->posttime).'</span></li>
			';
			}
			$strHtml.='
			</ul>
			';
			
			__($strHtml);
			
		}
		
	}
	
	/*=====================================================================
	* 获取最新评论列表
	======================================================================*/
	public function show_recent_comments($count=10,$words=24){
		
		$objComment=new SWComment();
		$comments=$objComment->getMore(array('state'=>1,'type'=>1),array('posttime'=>'desc'),0,$count);
		
		if(file_exists(SW::path($this->templatePath.'.recent_comments'))){
			
			require(SW::path($this->templatePath.'.recent_comments'));
			
		}else{
			
			$strHtml='
			<ul class="sw_recent_comments">
			';
			foreach($comments as $comment){
			$strHtml.='
			<li><a href="'.$this->getUrl('post',array('postid'=>$comment->postid)).'#comment-'.$comment->id.'">'.$comment->author.'</a>: <span>'.SWFunc::closeHtml($comment->content,$words,'...').'</span></li>
			';
			}
			$strHtml.='
			</ul>
			';
			
			__($strHtml);
			
		}
		
	}
	
	/*=====================================================================
	* 获取最新标签列表
	* count默认是0，即查询全部tags
	======================================================================*/
	public function show_tags_list($count=0){
		
		$tags=SWTag::getAll(array(),$count);
		
		if(file_exists(SW::path($this->templatePath.'.tags_list'))){
			
			require(SW::path($this->templatePath.'.tags_list'));
			
		}else{
			
			$strHtml='
			<ul class="sw_tags_list">
			';
			foreach($tags as $tag){
			$strHtml.='
			<li><a href="'.$this->getUrl('search',array('tagname'=>$tag['name'])).'" style="font-size:'.(15*($tag['weight']-1)+100).'%;">'.$tag['name'].'</a></li>
			';
			}
			$strHtml.='
			</ul>
			';
			
			__($strHtml);
			
		}
		
	}
	
	/*=====================================================================
	* 日期归档列表
	* count默认是0，即查询全部日期
	======================================================================*/
	public function show_dates_list($count=0){
		
		//获取归档日期
		$archives=SWPost::get_archives($count);
		
		if(file_exists(SW::path($this->templatePath.'.dates_list'))){
			
			require(SW::path($this->templatePath.'.dates_list'));
			
		}else{
			
			$strHtml='
			<ul class="sw_dates_list">
			';
			foreach($archives as $key=>$archive){
			$strHtml.='
			<li><a href="'.$this->getUrl('search',array('year'=>$archive['year'],'month'=>$archive['month'])).'">'.date('Y年m月',$archive['date']).'</a> ('.$archive['count'].')</li>
			';
			}
			$strHtml.='
			</ul>
			';
			
			__($strHtml);
			
		}
		
	}
	
	/*=====================================================================
	* 显示页面树形列表
	======================================================================*/
	public function show_pages_list(){
		
		//获取树状列表
		if(!$this->page){
			$objPage=new SWPage();
			$tmpLevel=0;
		}else{
			$objPage=$this->page;
			$tmpLevel=$objPage->getLevel()+1;
		}
		$pages=$objPage->getMoreTree($objPage->id,array('state'=>1,'flag'=>1),array('ordernum'=>'asc','id'=>'asc'));
		
		if(file_exists(SW::path($this->templatePath.'.pages_list'))){
			
			require(SW::path($this->templatePath.'.pages_list'));
		
		//如果没有获取子页面，则不显示
		}elseif(!$pages){
			
			__('');
			
		}else{
			
			$strHtml='
			<ul class="sw_pages_list">
			';
			foreach($pages as $page){
			$strHtml.='
			<li style="padding-left:'.(($page->getLevel()-$tmpLevel)*10).'px;"><a href="'.$this->getUrl('page',array('pagename'=>$page->alias)).'">'.$page->title.'</a></li>
			';
			}
			$strHtml.='
			</ul>
			';
			
			__($strHtml);
			
		}
		
	}
	
	/*=====================================================================
	* 显示验证码
	======================================================================*/
	public function show_scode(){
		
		if(file_exists(SW::path($this->templatePath.'.scode'))){
			
			require(SW::path($this->templatePath.'.scode'));
			
		}else{
		
			__("<img src=\"".$this->getUrl('actions',array('action'=>'show_scode'))."\" onclick=\"javascript:this.src='".$this->getUrl('actions',array('action'=>'show_scode'))."#'+Math.random();\" style=\"cursor: pointer;vertical-align:middle;\" />");
			
		}

	}
	
	/*=====================================================================
	* 显示gravatar头像
	======================================================================*/
	public function show_gravatar($email,$class='',$size=32){

		__('<img width="'.$size.'" height="'.$size.'" class="'.$class.'" src="http://www.gravatar.com/avatar/'.md5($email).'?s='.$size.'&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D32&amp;r=G" alt="">');

	}
	
	/*=====================================================================
	* 发表评论操作
	======================================================================*/
	public function action_add_comment(){
		
		//定义错误数组
		$arrErr=array();

		//获取postid
		$postid=SWFunc::checkInt(SW::request('postid','post'));
		if(!$postid) $this->show_404();
		
		//获取post对象
		$objPost=new SWPost();
		$objPost->getById($postid);
		if($objPost->flag==0 || $objPost->state==0) $this->show_404();

		//是否允许评论
		if(!$objPost->allowcomment) $this->show_404();
		
		//获取评论提交数据
		$parentid=SWFunc::checkInt(SW::request('parentid','post'));
		$author=SWFunc::checkString(SW::request('author','post'));
		$email=SWFunc::checkString(SW::request('email','post'));
		$url=SWFunc::checkUrl(SW::request('url','post'));
		$content=SWFunc::closeHtml(SW::request('content','post'));

		//验证输入数据
		if(!SWFunc::checkCharacter($author,1,1,12)) $arrErr[]=LANG('Display_Error_Author');
		if(!SWFunc::checkEmail($email)) $arrErr[]=LANG('Display_Error_Email');
		if(!$content) $arrErr[]=LANG('Display_Error_Content');
		if(!SWScode::check(SWFunc::checkString(SW::request('scode','post')))) $arrErr[]=LANG('Display_Error_Scode');
		
		//如果未登录使用博主昵称评论，则在昵称末尾加上"*"
		if(!$this->login && strtolower($author)==strtolower(SW::getOption('author'))) $author=$author.'*';
		
		//验证评论级数（限制于5级）
		if($parentid){
			$objTmp=new SWComment();
			$objTmp->getById($parentid);
			if($objTmp->getLevel()>=5) $arrErr[]=LANG('Display_Error_Replay');
		}
		
		//如果出错
		if($arrErr) $this->show_error($arrErr);

		//插入评论
		$objComment=new SWComment();
		$objComment->parentid=$parentid;
		$objComment->postid=$postid;
		$objComment->content=$content;
		$objComment->author=$author;
		$objComment->email=$email;
		$objComment->url=$url;
		$objComment->ip=SWFunc::getIp();
		$objComment->posttime=time();
		$objComment->state=SW::getOption('checkcomment')?0:1;
		$objComment->type=1;
		
		//保存
		$objComment->add();

		//发送评论通知邮件（只有未登录的用户评论才发送）
		if(!$this->login && !$objComment->parentid){
			$mailSubject=LANG('Mail_Comment_Subject',array('subject'=>$objPost->title));
			$mailBody=LANG('Mail_Comment_Body',array(
				'author'=>$objComment->author,
				'link'=>$this->getUrl('post',array('postid'=>$objComment->postid)).'#comment-'.$objComment->id,
				'subject'=>$objPost->title,
				'email'=>$objComment->email,
				'time'=>date('Y-m-d H:i:s',$objComment->posttime),
				'comment'=>$objComment->content

			));
			$objMail=new SWMail();
			$objMail->send($mailSubject,$mailBody,SW::getOption('email'),'',true);
		}
		
		//跳转到评论页
		header('Location: '.$this->getUrl('post',array('postid'=>$postid)).'#comment-'.$objComment->id);
		
	}
	
	/*=====================================================================
	* 输出验证码
	======================================================================*/
	public function action_show_scode(){
		
		SWScode::show(60,20);
		
	}

}

?>
