<?php
defined('SW_') or die('Access Error');
?>
<div id="msg_container"></div>
<div id="loading_container"></div>
<div id="humanMsgLog"><p class="ui-corner-top">Message Log</p><ul></ul></div>

<script type="text/javascript" charset="utf-8">
$(function(){
	//初始化所有下拉菜单
	SWJS.dropMenu();
	
	//初始化下拉块按钮
	SWJS.dropButton();
	
	//初始化主菜单
	SWJS.mainMenu('<?php __(SWAdmin::$title?SWAdmin::$title:'');?>');
	
	//初始化human message
	SWJS.setupMessage();
	
	//注销操作
	$('#logout').click(function(){
		SWJS.ajaxRequest('login','logout',{},function(obj){
			window.location.href='login.php';
		});
	});
});
</script>

</body>
</html>
