<?php
define('SW_',true);
require_once('_common.php');

//判断用户登录
SWAdmin::checkLogin();

//获取页面使用的option对象
$objOption=new SWOption();

//网页头属性
SWAdmin::$js=array('pages.option');
SWAdmin::$title=LANG('System Setting');
SWAdmin::$group='option';

require('_header.php');
?>

<script type="text/javascript">

</script>


<div id="main">

	<div class="center">

		<form name="form1">
			
			<h2><?php __(LANG('Basic_Setting'));?></h2>
			<div class="white_box ui-corner-all">
				<ul class="selectlist">
					<li class="selectitem" style="border-top:0;">
						<div class="cell20"><?php __(LANG('Option_Title'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="title" style="width:250px;" value="<?php __($objOption->title);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Description'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="description" style="width:250px;" value="<?php __($objOption->description);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Url'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="url" style="width:250px;" value="<?php __($objOption->url);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Author'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="author" style="width:250px;" value="<?php __($objOption->author);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Email'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="email" style="width:250px;" value="<?php __($objOption->email);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Account'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="account" style="width:250px;" value="<?php __($objOption->account);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Password'));?></div>
						<div class="cell80">
							<input type="password" class="input_2" name="password" style="width:120px;" value="" /> 
							<span class="des"><?php __(LANG('Option_Password_Description'));?></span>
						</div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Repassword'));?></div>
						<div class="cell80"><input type="password" class="input_2" name="repassword" style="width:120px;" value="" /></div>
						<div style="clear:both"></div>
					</li>
				</ul>
			</div>
			
			<h2><?php __(LANG('Variable_Setting'));?></h2>
			<div class="white_box ui-corner-all">
				<ul class="selectlist">
					<li class="selectitem" style="border-top:0;">
						<div class="cell20"><?php __(LANG('Option_Language'));?></div>
						<div class="cell80">
							<select name="language" style="width:250px;">
								<?php
								foreach(SWLanguage::getAllLanguages() as $value){
									__('<option value="'.$value.'" '.($objOption->language==$value?'selected="selected"':'').'>'.$value.'</option>');
								}
								?>
							</select>
						</div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Timezone'));?></div>
						<div class="cell80">
							<select name="timezone" style="width:250px;">
								<?php
								foreach(SWFunc::timezoneList() as $value){
									__('<option value="'.$value.'" '.($objOption->timezone==$value?'selected="selected"':'').'>'.$value.'</option>');
								}
								?>
							</select>
						</div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Rewrite'));?></div>
						<div class="cell80">
							<input type="checkbox" value="1" name="rewrite" <?php __($objOption->rewrite?'checked="checked"':'');?>> 
							<span class="des"><?php __(LANG('Option_Rewrite_Description'));?></span>
						</div>
						<div style="clear:both"></div>
					</li>
				</ul>
			</div>
			
			<h2><?php __(LANG('Release_And_Receive'));?></h2>
			<div class="white_box ui-corner-all">
				<ul class="selectlist">
					<li class="selectitem" style="border-top:0;">
						<div class="cell20"><?php __(LANG('Option_Pingback'));?></div>
						<div class="cell80">
							<input type="checkbox" name="pingback" value="1" <?php __($objOption->pingback==1?'checked="checked"':"");?> /> 
							<span class="des"><?php __(LANG('Option_Pingback_Description'));?></span>
						</div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Check_Comment'));?></div>
						<div class="cell80"><input type="checkbox" name="checkcomment" value="1" <?php __($objOption->checkcomment==1?'checked="checked"':"");?> /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Receive_Pingback'));?></div>
						<div class="cell80"><input type="checkbox" name="receivepingback" value="1" <?php __($objOption->receivepingback==1?'checked="checked"':"");?> /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Receive_Trackback'));?></div>
						<div class="cell80"><input type="checkbox" name="receivetrackback" value="1" <?php __($objOption->receivetrackback==1?'checked="checked"':"");?> /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Feed_Count'));?></div>
						<div class="cell80">
							<input type="text" class="input_2" name="feedcount" style="width:60px;" value="<?php __($objOption->feedcount);?>" />
							<span class="des"><?php __(LANG('Option_Feed_Count_Description',array('feeds'=>'RSS2.0, RSS1.0, RSS0.91, PIE0.1, MBOX, OPML, ATOM1.0, ATOM0.3')));?></span>
						</div>
						<div style="clear:both"></div>
					</li>
				</ul>
			</div>

			<h2><?php __(LANG('Send_Mail'));?></h2>
			<div class="white_box ui-corner-all">
				<ul class="selectlist">
					<li class="selectitem" style="border-top:0;">
						<div class="cell20"><?php __(LANG('Option_Mail_Type'));?></div>
						<div class="cell80">
							<select name="mail_type" style="width:60px;">
								<option value="" <?php __($objOption->mail_type==''?'selected="selected"':'');?>><?php __(LANG('No'));?></option>
								<option value="smtp" <?php __($objOption->mail_type=='smtp'?'selected="selected"':'');?>>SMTP</option>
							</select>
							<span class="des"><?php __(LANG('Option_Mail_Type_Description'));?></span>
						</div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Mail_Address'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="mail_address" style="width:250px;" value="<?php __($objOption->mail_address);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Mail_Server'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="mail_server" style="width:250px;" value="<?php __($objOption->mail_server);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Mail_Port'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="mail_port" style="width:60px;" value="<?php __($objOption->mail_port);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Mail_Account'));?></div>
						<div class="cell80"><input type="text" class="input_2" name="mail_account" style="width:250px;" value="<?php __($objOption->mail_account);?>" /></div>
						<div style="clear:both"></div>
					</li>
					<li class="selectitem">
						<div class="cell20"><?php __(LANG('Option_Mail_Password'));?></div>
						<div class="cell80"><input type="password" class="input_2" name="mail_password" style="width:250px;" value="<?php __($objOption->mail_password);?>" /></div>
						<div style="clear:both"></div>
					</li>
				</ul>
			</div>

			<div style="margin-top:10px;text-align:right;">
				<a href="javascript:void(0);" id="save" class="button"><?php __(LANG('Save')); ?></a>
			</div>

			<div style="clear:both;"></div>

		</form>

	</div>
</div>

<?php
require('_footer.php');

?>
