<?php $this->show_header(); ?>

		<div id="content">
        <div id="content-inner">

		<?php if($posts): ?>

			<h1 class="search-title">
				<?php if(!empty($this->request['searchstring'])):?>
				搜索 <span class="search-subtitle"><?php __($this->request['searchstring']); ?></span>
				<?php elseif(!empty($this->request['year'])):?>
				归档 <span class="search-subtitle"><?php __($this->request['year']); ?> 年 <?php __($this->request['month']); ?> 月</span>
				<?php elseif(!empty($this->request['tagname'])):?>
				标签 <span class="search-subtitle"><?php __($this->request['tagname']); ?></span>
				<?php endif;?>
			</h1>
		
			<?php foreach($posts as $post): ?>

			<div  class="hentry">

				<h2 class="entry-title-search"><a href="<?php __($this->getUrl('post',array('postid'=>$post->id)));?>" rel="bookmark"><?php __($post->title);?></a></h2>

                <div class="posted">
					<abbr>发表于 : <?php __(date('Y年m月d日',$post->posttime)); ?></abbr>
				</div>
                
				<div class="entry-content-search">

					[...] <?php __(SWFunc::closeHtml($post->content,255,''));?> [...<a href="<?php __($this->getUrl('post',array('postid'=>$post->id)));?>">more</a>]

				</div>

			</div>

			<?php endforeach; ?>

			<?php $this->show_pagenavbar(); ?>
	
		<?php else : ?>

			<h1 class="search-title">未找到内容</h1>
			<?php $this->show_searchform(); ?>

		<?php endif; ?>
		
        </div>
		</div>

<?php $this->show_sidebar(); ?>

<?php $this->show_footer(); ?>
