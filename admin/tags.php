<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin();

//获取标签数组
$arrTag=SWTag::getAll();

//网页头属性
SWAdmin::$js=array('pages.tags');
SWAdmin::$title=LANG('Tag');
SWAdmin::$group='tags';

require('_header.php');
?>

<div id="main">

	<div class="center">

		<div class="white_box ui-corner-all" style="padding:16px;">

			<ul class="taglist">
				<?php 
				foreach($arrTag as $key => $value){
					$fontsize=8+$value['weight']*2;
				?>
				<li>
					<a href="javascript:void(0);" onclick="showEdit('<?php __($value['name']);?>');" style="font-size:<?php __($fontsize);?>px;"><?php __($value['name']);?></a>
					<sup><a href="posts.php?tagname=<?php __($value['name']);?>"><?php __($value['num']);?></a></sup>
				</li>
				<?php }?>
			</ul>
			
		</div>
		
		<div id="edittag" class="gray_box ui-corner-all" style="margin-top:10px;display:none;">
		
			<input type="hidden" value="" id="tagname" />
			<input type="text" style="width:300px;float:left;" id="tagchange" class="input_near_button" value="" />
			<a href="javascript:edit();" class="button" style="float:left;"><?php __(LANG('Edit')); ?></a>
			<a href="javascript:del();" class="button" style="float:left;"><?php __(LANG('Delete')); ?></a>
			<a href="javascript:closeEdit();" class="button" style="float:left;"><?php __(LANG('Cancel')); ?></a>
			
			<div style="clear:both;"></div>
		</div>

		<div style="clear:both;"></div>

	</div>
</div>

<?php
require('_footer.php');

?>
