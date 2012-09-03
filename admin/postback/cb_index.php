<?php
defined('SW_') or die('Access Error');

switch(SW::request('act','get')){
	
	case 'show':
		
		SWAdmin::postJson('',0);

	break;

}

?>