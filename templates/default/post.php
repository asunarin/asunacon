<?php $this->show_header($post->title);?>

		<div id="content">
        <div id="content-inner">

			<div class="hentry">

				<h2 class="entry-title"><a href="<?php __($this->getUrl('post',array('postid'=>$post->id)));?>" rel="bookmark"><?php __($post->title);?></a></h2>

				<div class="posted">
					<abbr>发表于 : <?php __(date('Y年m月d日',$post->posttime)); ?></abbr>
				</div>
				
				<div class="entry-content">
					
					<?php __($post->content);?>
					
					<?php if($post->getTagNames()):?>
						<div class="tags">标签: <?php __($post->getTagNames(', ',$this->getUrl('search',array('tagname'=>'{tagname}'),false)));?></div>
					<?php endif;?>

				</div>

				<div class="meta">
					<a href="<?php __($this->getUrl('post',array('postid'=>$post->id)));?>#comments" class="comments-link"><?php __($post->getCommentCount());?> 条评论</a>
				</div>
			</div>
			
			<?php 
			$this->show_pagenavbar(
				$newer?$this->getUrl('post',array('postid'=>$newer->id)):'',
				$older?$this->getUrl('post',array('postid'=>$older->id)):'',
				$newer?$newer->title:'',
				$older?$older->title:''
			); 
			?>

			<?php $this->show_comments(); ?>
			
        </div>
		</div>

<?php $this->show_sidebar(); ?>

<?php $this->show_footer(); ?>
