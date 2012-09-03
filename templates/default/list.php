<?php $this->show_header(); ?>

    <div id="content">
    <div id="content-inner">

<?php if($posts): ?>

<?php foreach($posts as $post): ?>

        <div class="hentry">

            <h2 class="entry-title"><a href="<?php __($this->getUrl('post',array('postid'=>$post->id)));?>" rel="bookmark"><?php __($post->title);?></a></h2>

            <div class="posted">
                <abbr>发表于 : <?php __(date('Y年m月d日',$post->posttime)); ?></abbr>
            </div>
            
            <div class="entry-content">
                
                <?php if($post->readmore()):?>
                    <?php __($post->content);?>
                    <p class="readmore"><a href="<?php __($this->getUrl('post',array('postid'=>$post->id)));?>">阅读全文 &raquo;</a></p>
                <?php else:?>
                    <?php __($post->content);?>
                <?php endif;?>
                
                <?php if($post->getTagNames()):?>
                    <div class="tags">标签: <?php __($post->getTagNames(', ',$this->getUrl('search',array('tagname'=>'{tagname}'),false)));?></div>
                <?php endif;?>

            </div>

            <div class="meta">
                <a href="<?php __($this->getUrl('post',array('postid'=>$post->id)));?>#comments"><?php __($post->getCommentCount());?> 条评论</a>
            </div>
        </div>

<?php endforeach; ?>

<?php $this->show_pagenavbar(); ?>

<?php else : ?>
        
        <h2>没有文章</h2>
        <?php $this->show_searchform(); ?>

<?php endif; ?>

    </div>
    </div>

<?php $this->show_sidebar(); ?>

<?php $this->show_footer(); ?>
