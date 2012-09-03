<?php if ( $comments ) : ?>

<ol class="commentlist">

<?php foreach($comments as $comment):?>

    <li id="comment-<?php __($comment->id);?>" class="comment" style="<?php __($level%2==0?'background-color:#ffffff;':'background-color:#fafafa;');?>">

		<div class="comment-author">

			<?php $this->show_gravatar($comment->email,'avatar',32);?>

			<cite id="comment_author_<?php __($comment->id);?>">
			
				<?php if(!$comment->url):?>

					<?php __($comment->author);?>
				
				<?php else:?>
				
					<a class="url" rel="external nofollow" href="<?php __($comment->url);?>"><?php __($comment->author);?></a>
				
				<?php endif;?>

			</cite>

			<span id="comment_time_<?php __($comment->id);?>" class="posted">
				
				<?php __(date('Y年m月d日 H时i分',$comment->posttime));?> 
				
			</span>

		</div>

		<?php if($comment->state==0):?><em class="comment-awaiting-moderation">您的评论正在等待审核</em><?php endif;?>

		<p id="comment_content_<?php __($comment->id);?>"><?php __(SWFunc::transText($comment->content));?></p>
		
		<div class="clear"></div>
		
		<?php if($post->allowcomment && $level<5):?><div class="replaylink"><a href="#commentform" onclick="showReplay(<? __($comment->id);?>)">回复</a></div><?php endif;?>
				
		<?php $this->show_comments_list($comment->id,$level); //递归显示回复?>

    </li>

<?php endforeach;?>

</ol>

<?php endif; ?>
