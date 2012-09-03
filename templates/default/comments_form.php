<?php if($post->allowcomment):?>
	
	<h3>发表评论</h3>
	
	<div id="commentform">
		
		<form method="post" action="<?php __($this->getUrl('actions',array('action'=>'add_comment')));?>">
			
		<p><input type="text" size="30" value="<?php __($author);?>" name="author"> <span class="required">称呼 *</span></p>
		
		<p><input type="text" size="30" value="<?php __($email);?>" name="email"> <span class="required">邮箱 *</span></p>
		
		<p><input type="text" size="30" value="<?php __($url);?>" name="url"> <span class="required">网址</span></p>
				
		<p><textarea rows="8" cols="45" name="content"></textarea></p>

		<p>
			<label for="scode">验证码：</label> <input type="text" size="10" value="" name="scode" style="width:100px;"> 
			
			<?php $this->show_scode();?>
			
			<input type="submit" value="发表评论" name="submit">
			<input type="hidden" value="0" name="parentid">
			<input type="hidden" value="<?php __($post->id);?>" name="postid">
		</p>
		
		</form>
		
	</div>
			
<?php endif; ?>
