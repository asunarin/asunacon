<?php
define('SW_',true);
require_once('_common.php');

//网页头属性
SWAdmin::$js=array('pages.login');
SWAdmin::$title=LANG('Login');

require('_systemheader.php');
?>

<div id="system">
		
	<h1><?php __(SW::getOption('title'));?></h1>
	
	<div class="loginForm">
		
		<form name="form1" onsubmit="login();return false;">
						
			<input type="text" class="input_1 ui-corner-all" name="account" id="title" style="width:240px;" placeholder="<?php __(LANG('Account'));?>" />
			
			<input type="password" class="input_1 ui-corner-all" name="password" id="title" style="width:240px;margin-top:16px;" placeholder="<?php __(LANG('Password'));?>" />
			
			<input type="submit" class="button" style="float:right;margin-top:16px;" value="<?php __(LANG('Login'));?>" />
		
		</form>
		
	</div>
	
</div>

<?php
require('_systemfooter.php');

?>
