<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php __(SW::getOption('title').($title?' &raquo; '.$title:''));?></title>
	
	<meta name="generator" content="Swan <?php __(SW_VERSION);?>" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php __($this->baseUrl);?>api/feed.php?type=rss2.0" />
	<link rel="alternate" type="application/atom+xml" title="ATOM 1.0" href="<?php __($this->baseUrl);?>api/feed.php?type=atom1.0" />
		
	<script src="<?php __($this->templateUrl);?>js/jquery.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php __($this->templateUrl);?>js/jquery.snippet.js" type="text/javascript"></script>
	
	<link rel="stylesheet" href="<?php __($this->templateUrl);?>css/style.css" type="text/css" />
	<link rel="stylesheet" href="<?php __($this->templateUrl);?>css/snippet.css" type="text/css" />
		
	<script type="text/javascript">
	
	$(function(){
		
		$("pre.codeArea").each(function(){
			
			var codeType=$(this).attr('value');
			if(codeType) $(this).snippet(codeType,{style:'acid',menu:false});
			
		});
		
	});
	
	//回复评论
	function showReplay(commentid){
			
		var author=$('#comment_author_'+commentid).html();
		var time=$('#comment_time_'+commentid).html();
		var content=$('#comment_content_'+commentid).html();
		
		var strReplay='<div class=\"sw_replay_preview\">\n';
		strReplay+='<strong>回复: '+author+'</strong>\n';
		strReplay+='<p>'+content+'</p>\n';
		strReplay+='<div><a href="javascript:void(0);" onclick="closeReplay();">取消回复</a></div>\n';
		strReplay+='</div>\n';
		
		$('.sw_replay_preview').remove();
		$('textarea[name=content]').before(strReplay);
		$('form input[name=parentid]').val(commentid);
			
	}
	
	function closeReplay(){
		
		$('.sw_replay_preview').remove();
		$('form input[name=parentid]').val('0');
			
	}
	
	</script>

</head>

<body>
<div id="container">
	<div id="header">
		<div id="header-inner">
			
		<h1><span><a href="<?php __(SW::getOption('url'));?>" rel="home"><?php __(SW::getOption('title'));?></a></span></h1>
		<h2><?php __(SW::getOption('description'));?></h2>
		
		<div class="clear"></div>
		<?php $this->show_menu();?>
		
		</div>
	</div>
	
	<div class="clear"></div>
	<div id="content-wrapper">
