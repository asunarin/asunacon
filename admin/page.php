<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin();

//页面的id，新建页面时为0
$pageId=SW::request('id','get')?SW::request('id','get'):0;

//获取页面使用的页面对象
$objPage=new SWPage();
$objPage->getById($pageId);

//获取可选的父页面
$parentsCanBe=$objPage->getMore(array('parentsCanBe'=>$pageId),array('posttime'=>'desc'));

//网页头属性
SWAdmin::$js=array('pages.page','swjs_editor','swjs_attachment','jquery_ocupload');
SWAdmin::$title=$pageId>0?LANG('Edit Page'):LANG('Create Page');
SWAdmin::$group='pages';

require('_header.php');
?>

<div id="main">

	<div class="center">

		<form name="form1">

		<div id="editor_div" style="margin-top:10px;">
			<textarea name="content" style="display:none;"><?php __($objPage->content);?></textarea>
		</div>

		<div style="margin-top:10px;position:relative;height:20px;">

			<div id="optiontab" class="dropbutton dropbutton_up ui-corner-left" style="top:0px;left:229px;"><?php __(LANG('Options')); ?></div>

			<div id="attachmenttab" class="dropbutton dropbutton_up ui-corner-right" style="top:0px;left:380px;"><?php __(LANG('Attachment')); ?></div>

		</div>
		
		<div id="attachmenttab_box" class="dropbox dark_box ui-corner-all" style="padding:16px;padding-top:24px;display:none;margin-top:-10px;"></div>

		<div id="optiontab_box" class="dropbox dark_box ui-corner-all" style="padding:16px;padding-top:24px;display:none;margin-top:-10px;">
			<table cellpadding="0" cellspacing="0" border="0" class="list_table_dark">

				<tr>
					<td width="100"><?php __(LANG('Page_Title')); ?></td>
					<td><input type="text" class="input_2" name="title" style="width:250px;" value="<?php __($objPage->title?$objPage->title:LANG('Page').'-'.($objPage->id?$objPage->id:$objPage->getNextId()));?>" /></td>
				</tr>

				<tr>
					<td><?php __(LANG('Page_Alias')); ?></td>
					<td><input type="text" class="input_2" name="alias" style="width:250px;" value="<?php __($objPage->alias?$objPage->alias:'page-'.($objPage->id?$objPage->id:$objPage->getNextId()));?>" /></td>
				</tr>
                
                <tr>
					<td><?php __(LANG('Page_State')); ?></td>
					<td>
						<select name="state" style="width:150px;">
							<option value="0" <?php __($objPage->state==0?'selected="selected"':"");?> ><?php __(LANG('State_Draft')); ?></option>
							<option value="1" <?php __($objPage->state==1?'selected="selected"':"");?> ><?php __(LANG('State_Published')); ?></option>
						</select>
					</td>
				</tr>
				
				<tr>
					<td><?php __(LANG('Page_Parent')); ?></td>
					<td>
						<select name="parentid" id="parentid" style="width:150px;">
							<option value="0" <?php __($objPage->parentid==0?'selected="selected"':"");?> >----</option>
							<?php foreach($parentsCanBe as $value){?>
							<option value="<?php __($value->id);?>" <?php __($value->id==$objPage->parentid?'selected="selected"':"");?> ><?php __($value->title);?></option>
							<?php }?>
						</select>
					</td>
				</tr>
				
				<tr id="col_onmenu">
					<td><?php __(LANG('Page_On_Menu')); ?></td>
					<td><input type="checkbox" name="onmenu" id="onmenu" value="1" <?php __($objPage->onmenu==1?'checked="checked"':"");?> /></td>
				</tr>

				<tr class="last">
					<td><?php __(LANG('Page_Order_Num')); ?></td>
					<td><input type="text" class="input_2" name="ordernum" style="width:150px;" value="<?php __($objPage->ordernum);?>" /></td>
				</tr>

			</table>
		</div>

		<div style="margin-top:10px;text-align:right;">
			<input type="hidden" name="id" value="<?php __($objPage->id);?>" />
			<input type="hidden" name="flag" value="<?php __($objPage->flag);?>" />
			<input type="hidden" name="posttime" value="<?php __(date("Y-m-d H:i:s",$objPage->posttime)); ?>" />
			<?php if($objPage->id>0){?>
				<a href="#update" id="save" class="button"><?php __(LANG('Save')); ?></a>
				<?php if($objPage->state==0){?>
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
