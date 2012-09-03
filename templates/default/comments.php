<div id="comments">

<h3 id="comments-title"><em>共有 <?php __($post->getCommentCount()); ?> 条评论</em></h3>

<?php $this->show_comments_list();?>

</div>

<?php $this->show_comments_form(); ?>
