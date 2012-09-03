<?php
define('SW_',true);
define('SW_PAGE_SETUP',true);

require_once('../../framework/init.php');

//判断是否是非法请求
if(SW::$preAction!='install') die('Access Error');

//初始化安装类
$objInstall=new SWInstall();
$arrErr=array();
$succeed=false;

//首先判断config目录是否可写
if(!is_writable(SW::dirPath(''))) $arrErr[]='安装目录不可写, 请设置安装目录为可写';

$formpost=SW::request('formpost','post');

//如果用户提交了
if(!empty($formpost) && !$arrErr){
	
	//验证注册信息
	$objInstall->title=SWFunc::closeHtml(SW::request('title','post'));
	if(!$objInstall->title) $arrErr[]='标题不能空';
	
	//获取程序地址
	$objInstall->url=SWFunc::checkUrl(SW::request('url','post'),true);
	if(!$objInstall->url) $arrErr[]='程序地址错误';

	$objInstall->description=SWFunc::closeHtml(SW::request('description','post'));

	$objInstall->author=SWFunc::closeHtml(SW::request('author','post'));
	if(!$objInstall->author) $arrErr[]='用户昵称不能为空';
	
	$objInstall->email=SWFunc::checkString(SW::request('email','post'));
	if(!SWFunc::checkEmail($objInstall->email)) $arrErr[]='邮箱地址格式不符合要求';
	
	$objInstall->account=SWFunc::checkString(SW::request('account','post'));
	if(!SWFunc::checkCharacter($objInstall->account,3)) $arrErr[]='账户名格式错误';

	//判断两次输入是否一样，同时进行认证
	$objInstall->password=SWFunc::checkString(SW::request('password','post'));
	if(!$objInstall->password || $objInstall->password!=SW::request('repassword','post') || !SWFunc::checkCharacter($objInstall->password,2,4,16)) $arrErr[]='密码格式错误或两次输入不同';
	
	$objInstall->language=SWFunc::checkString(SW::request('language','post'));
	if(!$objInstall->language) $arrErr[]='请选择语言/语系';
	
	$objInstall->timezone=SWFunc::checkString(SW::request('timezone','post'));
	if(!$objInstall->timezone) $arrErr[]='请选择时区';

	$objInstall->dbprefix=SWFunc::checkString(SW::request('dbprefix','post'));
	if($objInstall->dbprefix && !SWFunc::checkCharacter($objInstall->dbprefix,3)) $arrErr[]='表前缀格式错误';
	
	//如果以上都没有错误，获取数据库设置
	if(!$arrErr){
		$objInstall->dbhost=SWFunc::checkString(SW::request('dbhost','post'));
		$objInstall->dbname=SWFunc::checkString(SW::request('dbname','post'));
		$objInstall->dbuser=SWFunc::checkString(SW::request('dbuser','post'));
		$objInstall->dbpass=SWFunc::checkString(SW::request('dbpass','post'));
		if(!$objInstall->checkDatabase()) $arrErr[]='MySQL数据库链接设置错误';
	}

	//如果以上都没有错误
	if(!$arrErr){

		//生成config文件
		$objInstall->createConfig();

		//把数据导入数据库，完成安装
		$objInstall->importData();

		/*
		//插入默认文章
		$objPost=new SWPost();
		$objPost->id=0;
		$objPost->title='欢迎使用Swan';
		$objPost->content='<p>这是一篇系统默认文章，您可以修改或删除它，当您看到这篇文章时，说明Swan的安装已经完成了。<br />同时您可以访问<a href="http://www.svoo.org/" target="_blank">Swan官方网站</a>获得更多帮助。</p>';
		$objPost->state=1;
		$objPost->allowcomment=1;
		$objPost->posttime=time();
		$objPost->edittime=time();
		$objPost->alias='';
		$objPost->flag=1;
		$objPost->save();

		//插入默认评论
		if($objPost->id>0){
			$objComm=new SWComment();
			$objComm->id=0;
			$objComm->postid=$objPost->id;
			$objComm->content='感谢您加入Swan大家庭！';
			$objComm->author='VAL';
			$objComm->email='jizhaoyi@gmail.com';
			$objComm->url='http://www.svoo.org/';
			$objComm->ip=SWFunc::getIp();
			$objComm->posttime=time();
			$objComm->state=1;
			$objComm->type=1;
			$objComm->save();
		}
		*/		

		$succeed=true;

	}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Install Swan <?php __(SW_VERSION);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" href="../css/setup.css" rel="stylesheet" />
</head>
<body>

<?php if(!$succeed){?>

<div class="title">Swan <?php __(SW_VERSION);?></div>

<?php if($arrErr){?>
<div class="error">
<ul><?php foreach($arrErr as $err){__('<li>'.$err.'</li>');}?></ul>
</div>
<?php }else{?>
<div class="info">
<ul><li>配置检查通过，您现在可以进行安装</li></ul>
</div>
<?php }?>

<div class="window">
	<form name="form1" method="post" action="">
		<h1>站点设置</h1>
		<p>
			<label>博客名称</label>
			<input type="text" value="<?php __($objInstall->title);?>" name="title" />
		</p>
		<p>
			<label>博客地址</label>
			<input type="text" value="<?php __($objInstall->getRootUrl());?>" name="url" />
		</p>
		<p>
			<label>博客描述</label>
			<textarea cols="40" rows="2" name="description"><?php __($objInstall->description);?></textarea>
		</p>
		<p>
			<label>语言/语系</label>
			<select name="language">
				<?php
				foreach(SWLanguage::getAllLanguages() as $value){
					__('<option value="'.$value.'" '.($objInstall->language==$value?'selected="selected"':'').'>'.$value.'</option>');
				}
				?>
			</select>
		</p>
		<p>
			<label>系统时区</label>
			<select name="timezone">
				<?php
				foreach(SWFunc::timezoneList() as $value){
					__('<option value="'.$value.'" '.($objInstall->timezone==$value?'selected="selected"':'').'>'.$value.'</option>');
				}
				?>
			</select>
		</p>
		<h1>用户设置</h1>
		<p>
			<label>用户昵称</label>
			<input type="text" value="<?php __($objInstall->author);?>" name="author" />
		</p>
		<p>
			<label>用户邮箱</label>
			<input type="text" value="<?php __($objInstall->email);?>" name="email" />
		</p>
		<p>
			<label>帐号 <span class="sub">(管理登录帐号)</span></label>
			<input type="text" value="<?php __($objInstall->account);?>" name="account" />
		</p>
		<p>
			<label>密码 <span class="sub">(管理登录密码)</span></label>
			<input type="password" value="" name="password" />
		</p>
		<p>
			<label>确认密码 <span class="sub">(请再输入一次密码)</span></label>
			<input type="password" value="" name="repassword" />
		</p>
		<h1>数据库设置</h1>
		<p>
			<label>服务器</label>
			<input type="text" value="<?php __($objInstall->dbhost);?>" name="dbhost" />
		</p>
		<p>
			<label>数据库</label>
			<input type="text" value="<?php __($objInstall->dbname);?>" name="dbname" />
		</p>
		<p>
			<label>数据库帐号</label>
			<input type="text" value="<?php __($objInstall->dbuser);?>" name="dbuser" />
		</p>
		<p>
			<label>数据库密码</label>
			<input type="password" value="<?php __($objInstall->dbpass);?>" name="dbpass" />
		</p>
		<p>
			<label>表前缀</label>
			<input type="text" value="<?php __($objInstall->dbprefix);?>" name="dbprefix" />
		</p>
		<input type="hidden" value="1" name="formpost" />
		<button type="submit">安装 &raquo;</button>
	</form>
	<div style="clear:both;"></div>
</div>

<?php }else{?>

<div class="title">Complete</div>
<div class="window">
	<h2>安装成功！</h2>
	<p><a href="<?php __(SW::urlPath('admin.login'));?>">进入控制面板</a></p>
	<p><a href="<?php __(SW::urlPath('',true));?>">访问网站首页</a></p>
	<p><a href="http://www.svoo.org" target="_blank">访问官方网站获取帮助</p>
	<div style="clear:both;"></div>
</div>

<?php }?>

</body>
</html>
