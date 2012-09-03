<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin();

//获取所有模板
$arrTemplate=SWTemplate::getTemplate();

//网页头属性
SWAdmin::$js=array('pages.template');
SWAdmin::$title=LANG('Template Setting');
SWAdmin::$group='template';

require('_header.php');
?>

<script type="text/javascript">

</script>


<div id="main">

	<div class="center">

		<form name="form1">
		
		<h2><?php __(LANG('Choose Template'));?></h2>
		<div class="white_box ui-corner-all">
			
			<?php if(count($arrTemplate)>0){?>
				
			<ul class="selectlist">
			
				<?php 
				$i=0;
				foreach($arrTemplate as $key=>$value){
				?>
				<li class="selectitem" <?php __($i==0?'style="border-top:0;"':'');?>>

						<div class="cell30">
							<?php __($value['screenshot']?'<img src="../templates/'.$key.'/screenshot.png" width="200" height="150" />':'');?>
						</div>
						<div class="cell60" style="line-height:150%;">
							<strong><?php __($value['name']);?></strong> (<?php __($value['version']);?>)<br />
							<?php __(LANG('Template_Author'));?>: <a href="<?php __($value['url']);?>" target="_blank"><?php __($value['author']);?></a><br />
							<?php __(LANG('Template_Language'));?>: <?php __($value['language']);?><br />
							<?php __(LANG('Template_Description'));?>: <?php __($value['description']);?>
							<div style="clear:both"></div>
						</div>
						<div class="cell10" style="text-align:right;">
							<?php if($key!=SW::getOption('template')){?>
								<a href="javascript:void(0);" onclick="save('<?php __($key);?>');" title="<?php __(LANG('Enable')); ?>"><img src="images/enable.png" /></a>
							<?php }else{?>
								<img src="images/enabled.png" title="<?php __(LANG('Enabled')); ?>" />
							<?php }?>
						</div>

						<div style="clear:both"></div>
				</li>
				<?php
					$i++;
				}
				?>
				
			</ul>
			
			<?php }?>
			
		</div>
		
		<div style="clear:both;"></div>

		</form>

	</div>
</div>

<?php
require('_footer.php');

?>
