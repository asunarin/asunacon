<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin();

//评论的id
$commentId=SW::request('id','get')?SW::request('id','get'):0;

//获取页面使用的评论对象
$objComment=new SWComment();
$objComment->getById($commentId);

//获取该评论文章对象
$objPost=$objComment->getPost();

//网页头属性
SWAdmin::$js=array('pages.comment');
SWAdmin::$title=LANG('Edit Comment');
SWAdmin::$group='comments';

require('_header.php');
?>

<div id="main">

	<div class="center">

		<form name="form1">
		
        <textarea class="textarea_1 ui-corner-all" name="content" style="width:750px;height:200px;"><?php __($objComment->content);?></textarea>
		
		<div style="margin-top:10px;position:relative;height:10px;">
			<div id="option" class="dropbutton dropbutton_up ui-corner-all" style="top:0px;left:305px;"><?php __(LANG('Options')); ?></div>
		</div>

		<div id="option_box" class="dropbox dark_box ui-corner-all" style="padding:16px;padding-top:24px;display:none;margin-top:0px;">

			<table cellpadding="0" cellspacing="0" border="0" class="list_table_dark">
				<tr>
					<td width="100"><?php __(LANG('Comment_At')); ?></td>
					<td><span style="text-decoration:underline;">&laquo<?php __($objPost->title);?>&raquo</span></td>
				</tr>
				<tr>
					<td><?php __(LANG('Comment_Author')); ?></td>
					<td><input type="text" class="input_2" name="author" style="width:250px;" value="<?php __($objComment->author); ?>" /></td>
				</tr>
				<tr>
					<td><?php __(LANG('Comment_Email')); ?></td>
					<td><input type="text" class="input_2" name="email" style="width:250px;" value="<?php __($objComment->email); ?>" /></td>
				</tr>
				<tr>
					<td><?php __(LANG('Comment_Url')); ?></td>
					<td><input type="text" class="input_2" name="url" style="width:250px;" value="<?php __($objComment->url); ?>" /></td>
				</tr>
				<tr>
					<td><?php __(LANG('Comment_State')); ?></td>
					<td>
						<select name="state" style="width:150px;">
							<option value="0" <?php __($objComment->state==0?'selected="selected"':"");?> ><?php __(SWAdmin::getCommentState(0)); ?></option>
							<option value="1" <?php __($objComment->state==1?'selected="selected"':"");?> ><?php __(SWAdmin::getCommentState(1)); ?></option>
							<option value="2" <?php __($objComment->state==2?'selected="selected"':"");?> ><?php __(SWAdmin::getCommentState(2)); ?></option>
						</select>
					</td>
				</tr>
				<tr class="last">
					<td><?php __(LANG('Comment_Posttime')); ?></td>
					<td><input type="text" class="input_2" name="posttime" style="width:150px;" value="<?php __(date("Y-m-d H:i:s",$objComment->posttime)); ?>" /></td>
				</tr>
			</table>
				
		</div>
		
		<div style="margin-top:10px;text-align:right;">
			<input type="hidden" name="id" value="<?php __($objComment->id);?>" />
			<a href="#save" id="save" class="button"><?php __(LANG('Save')); ?></a>
			
		</div>

		<div style="clear:both;"></div>

		</form>

	</div>
</div>

<?php
require('_footer.php');

?>
