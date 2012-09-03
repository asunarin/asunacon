<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin();

//文章的id，新建文章时为0
$postId=SW::request('id','get')?SW::request('id','get'):0;

//获取页面使用的文章对象
$objPost=new SWPost();
$objPost->getById($postId);

//网页头属性
SWAdmin::$js=array('pages.post','swjs_editor','swjs_attachment','jquery_ocupload','jquery_textboxlist');
SWAdmin::$css=array('textboxlist');
SWAdmin::$title=$postId>0?LANG('Edit Post'):LANG('Publish Post');
SWAdmin::$group=$postId?'posts':'post';

require('_header.php');
?>

<div id="main">

	<div class="center">

		<form name="form1">
		
		<input type="text" class="input_1 ui-corner-all" name="title" id="title" style="width:750px;font-weight:bold;" placeholder="<?php __(LANG('Post_Title'));?>" value="<?php __($objPost->title);?>" />

		<div id="editor_div" style="margin-top:10px;">
			<textarea name="content" style="display:none;"><?php __($objPost->content);?></textarea>
		</div>
		
		<div style="margin-top:10px;position:relative;height:20px;">

			<div id="optiontab" class="dropbutton dropbutton_up ui-corner-left" style="top:0px;left:229px;"><?php __(LANG('Options')); ?></div>

			<div id="attachmenttab" class="dropbutton dropbutton_up ui-corner-right" style="top:0px;left:380px;"><?php __(LANG('Attachment')); ?></div>

		</div>

		<div id="attachmenttab_box" class="dropbox dark_box ui-corner-all" style="padding:16px;padding-top:24px;display:none;margin-top:-10px;"></div>

		<div id="optiontab_box" class="dropbox dark_box ui-corner-all" style="padding:16px;padding-top:24px;display:none;margin-top:-10px;">
			<table cellpadding="0" cellspacing="0" border="0" class="list_table_dark">

				<tr>
					<td width="100"><?php __(LANG('Tag')); ?></td>
					<td>
                        <input type="text" id="tags" class="input_2" name="tag" style="width:250px;" value="<?php __($objPost->getTagNames());?>" />
                        <div id="tagadd" class="button_input">
                            <input type="text" class="input_2" /> 
                            <a href="javascript:void(0);"><img src="images/add.png" /></a>
                        </div>
                    </td>
				</tr>
                
                <tr>
					<td><?php __(LANG('Post_Alias')); ?></td>
					<td><input type="text" class="input_2" name="alias" style="width:250px;" value="<?php __($objPost->alias);?>" /></td>
				</tr>
                
                <tr>
					<td><?php __(LANG('Post_State')); ?></td>
					<td>
						<select name="state" style="width:150px;">
							<option value="0" <?php __($objPost->state==0?'selected="selected"':"");?> ><?php __(LANG('State_Draft')); ?></option>
							<option value="1" <?php __($objPost->state==1?'selected="selected"':"");?> ><?php __(LANG('State_Published')); ?></option>
						</select>
					</td>
				</tr>

				<tr>
					<td><?php __(LANG('Post_Allow_Comment')); ?></td>
					<td><input type="checkbox" name="allowcomment" value="1" <?php __($objPost->allowcomment==1?'checked="checked"':"");?> /></td>
				</tr>

				<tr class="last">
					<td><?php __(LANG('Post_Posttime')); ?></td>
					<td><input type="text" class="input_2" name="posttime" style="width:150px;" value="<?php __(date("Y-m-d H:i:s",$objPost->posttime)); ?>" /></td>
				</tr>

			</table>
		</div>

		<div style="margin-top:10px;text-align:right;">
			<input type="hidden" name="id" value="<?php __($objPost->id);?>" />
			<input type="hidden" name="flag" value="<?php __($objPost->flag);?>" />
			<?php if($objPost->id>0){?>
				<a href="#update" id="save" class="button"><?php __(LANG('Save')); ?></a>
				<?php if($objPost->state==0){?>
				<a href="#publish" id="publish" class="button"><?php __(LANG('Publish')); ?></a>
				<?php }?>
			<?php }else{?>
				<a href="#save" id="draft" class="button"><?php __(LANG('Draft')); ?></a>
				<a href="#publish" id="publish" class="button"><?php __(LANG('Publish')); ?></a>
			<?php }?>
		</div>

		<div style="clear:both;"></div>

		</form>
	</div>
</div>

<?php
require('_footer.php');

?>
