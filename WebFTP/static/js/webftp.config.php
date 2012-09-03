<?php

// +----------------------------------------------------------------------
// | Copyright (C) 浩天科技 www.ihotte.com admin@ihotte.com
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 文件$ID ： webftp.aconfig.php
 +------------------------------------------------------------------------------
 * 路径$ID ： static/js/webftp.aconfig.php
 +------------------------------------------------------------------------------
 * 程序版本： 浩天 WebFTP V1.0.0 2011-10-01
 +------------------------------------------------------------------------------
 * 功能简介： JS全局环境变量初始配置文件
 +------------------------------------------------------------------------------
 * 注意事项： 请勿私自删除此版权信息！
 +------------------------------------------------------------------------------
 **/
require(dirname(__FILE__).'/../../inc/init.php');
$config = array(
    'app_debug'        => C('APP_DEBUG')?'1':'0',
    'root_path'        => C('ROOT_PATH'),
	
	'type_conf'        => json_encode(C('TYPE_CONF')),
	'edit_conf'        => json_encode(C('EDIT_CONF')),
	'upload_type'      => json_encode(C('UPLOAD_CONF.UPLOAD_ALLOW_TYPE')),
	'search_type'      => json_encode(C('SEARCH_CONF.SEARCH_ALLOW_TYPE')),
	
	'cache_cut_time'   => C('CACHE_CUT_TIME'),
	'cache_main_on'    => C('CACHE_MAIN_ON')?'1':'0',
	'cache_main_time'  => C('CACHE_MAIN_TIME'),
	'zh_path_view'     => C('ZH_PATH_VIEW')?'1':'0',
	'img_file_view'    => C('IMG_FILE_VIEW')?'1':'0',
	
	'list_view_on'     => C('LIST_CONF.LIST_VIEW_ON')?'1':'0',
	'list_order_sort'  => C('LIST_CONF.LIST_ORDER_SORT'),
	'list_order_type'  => C('LIST_CONF.LIST_ORDER_TYPE'),
);
print <<<END
var debug            = {$config['app_debug']};
var root_path        = '{$config['root_path']}';
var parent_path      = '{$config['root_path']}';
var current_path     = '{$config['root_path']}';

var type_conf        = {$config['type_conf']};
var edit_conf        = {$config['edit_conf']};
var search_type      = {$config['search_type']};
var upload_type      = {$config['upload_type']};

var cache_cut_time   = {$config['cache_cut_time']};
var cache_main_on    = {$config['cache_main_on']};
var cache_main_time  = {$config['cache_main_time']};
var zh_path_view     = {$config['zh_path_view']};
var img_file_view    = {$config['img_file_view']};


var list_view_on     = {$config['list_view_on']};
var list_order_sort  = '{$config['list_order_sort']}';
var list_order_type  = '{$config['list_order_type']}';
var property_view_on = 0;
var poweredby        = 'WebFTP Powered by iHotte.com';
END;
?>