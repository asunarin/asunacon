<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin();

//获取页面使用的文章对象
$objPost=new SWPost();

//如果是根据标签查询
$tag=SWFunc::checkString(SW::request('tagname','get'));

//如果是外部的搜索跳转
$search=SWFunc::checkString(SW::request('search','get'));

//网页头属性
SWAdmin::$js=array('pages.posts');
SWAdmin::$title=LANG('Post Manager');
SWAdmin::$group='posts';

require('_header.php');
?>

<div id="main">

	<div class="center">

		<div class="white_box ui-corner-all" style="padding:16px;">
			
			<div class="input_img_div ui-corner-all" style="float:left;">
				<input type="text" id="search" value="<?php __($search?$search:'');?>" style="width:330px;" /> 
				<img src="images/search.gif" />
			</div>
			
			<div class="dropmenu_box" style="float:left;margin-left:10px;">
				<ul class="dropmenu ui-corner-all" id="type">
					<?php if($tag){?>
						<li class="default ui-corner-top"><a href="#" name="tagname" class="active" style="color:yellow;" value="<?php __($tag);?>"><?php __(LANG('Tag').': '.$tag); ?></a></li>
						<li><a href="#" name="publish" class="active"><?php __(LANG('State_Published')); ?></a></li>
					<?php }else{?>
						<li class="default ui-corner-top"><a href="#" name="publish" class="active"><?php __(LANG('State_Published')); ?></a></li>
					<?php }?>
					<li><a href="#" name="draft" class="active"><?php __(LANG('State_Draft')); ?></a></li>
					<li class="ui-corner-bottom"><a href="#" name="trash"><?php __(LANG('State_Deleted')); ?></a></li>
				</ul>
			</div>
			
			<a href="post.php" class="button" style="float:right;"><?php __(LANG('Publish Post')); ?></a>
			
			<div style="clear:both"></div>
			
		</div>
		
		<div class="selectlist_nav">
			<div class="cell5"><input type="checkbox" id="selectall" /></div>
			<div class="cell20">
				<span id="amount"></span> 
				<?php __(LANG('Selections_selected')); ?>: <span id="select">0</span>
			</div>
			<div class="cell25">
				<div class="dropmenu_box">
					<ol class="dropmenu ui-corner-all" style="">
						<li class="default ui-corner-all"><a href="javascript:void(0);" id="trashsel"><?php __(LANG('Opt_Delete')); ?></a></li>
					</ol>
				</div>
			</div>
			<div class="pagenav" style="float:right;"></div>
			<div style="clear:both"></div>
		</div>
		
		<div class="white_box ui-corner-all">
			<ul id="itemlist" class="selectlist"></ul>
		</div>

		<div style="clear:both;"></div>

	</div>
</div>

<?php
require('_footer.php');

?>
