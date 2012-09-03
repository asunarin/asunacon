<?php
defined('SW_') or die('Access Error');
require_once('_systemheader.php');
?>

<div id="header">
	<div id="top-menu">
		<ul id="mainmenu">

			<li <?php __(SWAdmin::$group=='post'?'class="default"':''); ?>><a href="post.php"><?php __(LANG('Publish')); ?></a></li>
			<li <?php __(SWAdmin::$group=='posts'?'class="default"':''); ?>><a href="posts.php"><?php __(LANG('Post')); ?></a></li>
			<li <?php __(SWAdmin::$group=='pages'?'class="default"':''); ?>><a href="pages.php"><?php __(LANG('Page')); ?></a></li>
            <li <?php __(SWAdmin::$group=='comments'?'class="default"':''); ?>><a href="comments.php"><?php __(LANG('Comment')); ?></a></li>
            <li <?php __(SWAdmin::$group=='tags'?'class="default"':''); ?>><a href="tags.php"><?php __(LANG('Tag')); ?></a></li>
            <li <?php __(SWAdmin::$group=='option'?'class="default"':''); ?>><a href="option.php"><?php __(LANG('Setting')); ?></a></li>
			<li <?php __(SWAdmin::$group=='template'?'class="default"':''); ?>><a href="template.php"><?php __(LANG('Template')); ?></a></li>
			
		</ul>

		<div class="item2" id="menu_icon">
			<a href="javascript:void(0);" id="logout"><img src="images/logout.png" /></a>
			<a href="../" target="_blank"><img src="images/home.png" /></a>
			<a id="img_state"></a>
		</div>
	</div>
	
	<div style="clear:both;"></div>
</div>
