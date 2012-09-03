<div id="sidebar">
	<div id="sidebar-inner">
		<ul>

			<li>
				<?php $this->show_searchform(); ?>
			</li>
			
			<li>
				<h2>最新文章</h2>
				<?php $this->show_recent_posts(10);?>
			</li>

			<li>
				<h2>最新评论</h2>
				<?php $this->show_recent_comments(10);?>
			</li>

			<li>
				<h2>标签云</h2>
				<?php $this->show_tags_list(0);?>
			</li>

			<li>
				<h2>信息</h2>
				<ul>

				<li>
					<?php if(!$this->login):?>
						<a href="<?php __($this->baseUrl.'admin/login.php');?>">登录</a>
					<?php else:?>
						<a href="<?php __($this->baseUrl.'admin/index.php');?>">控制面板</a>
					<?php endif;?>
				</li>
				<li><a href="<?php __(SW::getOption('url'));?>api/feed.php?type=rss2.0">订阅 (RSS 2.0)</a></li>
				<li><a href="<?php __(SW::getOption('url'));?>api/feed.php?type=atom1.0">订阅 (ATOM 1.0)</a></li>

				</ul>
			</li>

		</ul>
	</div>
</div>
