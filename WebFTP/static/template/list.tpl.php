<?php if(!defined('INC_ROOT')){die('Forbidden Access');} ?>
<div id="list">
  <div id="list_head"><span id="list_head_left"></span><span id="list_head_center"></span><span id="list_head_right"></span>
    <div class="clean"></div>
  </div>
  <div id="list_main"><span id="list_main_left"></span>
    <div id="list_main_center">
	<!--             -->
	<table class="tree-browser" cellpadding="0" cellspacing="0" >
        <!--<thead>
          <tr class="header">
            <th width="32" class="dir-file-check" ><a title="全部选中" href="javascript:selectAll();">全选</a></th>
            <th width="18" class="dir-file-ico">.</th>
            <th width="auto" class="dir-file-name" style="overflow:hidden;">文件名</th>
            <th width="180" class="dir-file-time">修改时间</th>
            <th width="80" class="dir-file-size">文件大小</th>
            <th width="80" class="dir-file-chmod">文件权限</th>
          </tr>
        </thead>
		-->
        <tbody id="dirs-files-list">
            <th><font color="green">文件列表加载中...</font></th>
			<!--     -->


			<!--     -->
        </tbody>
      </table>
	  <!--        -->
      
    </div>
    <span id="list_main_right"></span>
    <div class="clean"></div>
  </div>
  <div id="list_foot"><span id="list_foot_left"></span><span id="list_foot_center"></span><span id="list_foot_right"></span>
    <div class="clean"></div>
  </div>
</div>



	  
	  
	  
	  
	  
<!---             隐藏       toobar        ----------->
<!-- apptools begin -->
<div id="apptools" style="display:none;">
<div class="apptools">
  <!-- apptools-inner begin -->
  <div class="clearfix apptools-inner">
    <!-- -->
	<a id="tool_select" class="btn" href="javascript:selectAll();" title="选择"><span><img src="static/images/toolbar/select.gif" alt="" width="16" height="16" />选择</span></a>
    <span class="edge">|</span>
    <!-- -->
    <a id="toolback" class="btn" href="javascript:refresh(true);"><span><img src="static/images/toolbar/folder_up.gif" alt="" width="16" height="16" />刷新目录</span></a> <span class="edge">|</span>
    <!-- -->
    <a id="toolNewDir" class="btn" href="javascript:newbuild('dir',{});"><span><img src="static/images/toolbar/folder_add.gif" alt="" width="16" height="16" />新建目录</span></a><span class="edge">|</span>
    <!-- 
    <a id="toolReNameDiskDirectory" class="btn" href="javascript:void(0);"><span><img src="static/images/toolbar/folder_edit.gif" alt="" width="16" height="16" />重命名</span></a><span class="edge">|</span>
	  -->
	<a id="toolCut" class="btn" href="javascript:cut(3,{});"><span><img src="static/images/toolbar/cut.gif" alt="" width="16" height="16" />剪切</span></a><span class="edge">|</span>
	<a id="toolCopy" class="btn" href="javascript:copy(3,{});"><span><img src="static/images/toolbar/share.gif" alt="" width="16" height="16" />复制</span></a><span class="edge">|</span>
    <a id="toolPaste" class="btn" href="javascript:paste(3,{});"><span><img src="static/images/toolbar/paste.gif" alt="" width="16" height="16" />粘贴</span></a><span class="edge">|</span>
    <!-- -->
    <a id="toolUploadFile" class="btn" href="javascript:upload();"><span><img src="static/images/toolbar/file_up.gif" alt="" width="16" height="16" />上传</span></a> <span class="edge">|</span>
    <!-- -->
    <a id="toolDelete" class="btn" href="javascript:del(3, {});"><span><img src="static/images/toolbar/file_del.gif" alt="" width="16" height="16" />删除</span></a><span class="edge">|</span>
    <!-- -->
    <a class="btn"  id="toolListView" href="javascript:switchStyle();" title="切换视图"><span><img src="static/images/toolbar/view_thumb.gif" alt="" width="16" height="16" />切换视图</span></a><span class="edge">|</span>
    <!-- -->
    <!-- 选择 begin -->
	<!--
    <div class="dropdowndock"> <a id="tool_select" class="btn btn-dropdown" href="javascript:void(0);" title="选择"><span><img src="static/images/toolbar/select.gif" alt="" width="16" height="16" />选择</span></a>
      <div class="dropdownmenu-wrap" id="drop_select" style="display:none;">
        <div class="dropdownmenu">
          <ul class="dropdownmenu-list">
            <li><a href="javascript:selectAll();">全选</a></li>
            <li><a href="javascript:selectReverse();">反选</a></li>
          </ul>
        </div>
      </div>
    </div>
	-->
	
    <!-- 选择  END -->
    <!-- 文件排列方式 begin -->
    <div class="dropdowndock"> <a id="tool_sort" class="btn btn-dropdown" href="javascript:void(0);" title="文件排列方式"><span><img src="static/images/toolbar/order_asc.gif" alt="" width="16" height="16" />排列</span></a>
      <div class="dropdownmenu-wrap" id="drop_sort" style="display:none;">
        <div class="dropdownmenu">
          <ul class="dropdownmenu-list">
            <li><a  href="javascript:void(0);" id="list_order_type_name" class="list_order_type" type="name">文件名称</a></li>
            <li><a  href="javascript:void(0);" id="list_order_type_size" class="list_order_type" type="size">文件大小</a></li>         
            <li><a  href="javascript:void(0);" id="list_order_type_ext" class="list_order_type" type="ext">文件类型</a></li>
			<li><a  href="javascript:void(0);" id="list_order_type_mtime" class="list_order_type" type="mtime">创建时间</a></li>
            <li class="dropmenu-split">-</li>
            <li><a  href="javascript:void(0);" id="list_order_sort_asc"  class="list_order_sort" sort="asc" >顺序排列</a></li>
            <li><a  href="javascript:void(0);" id="list_order_sort_desc" class="list_order_sort" sort="desc">倒序排列</a></li>
          </ul>
        </div>
      </div>
    </div>
    <!-- 文件排列方式 END -->
  </div>
  <!-- apptools-inner END -->
</div>
<!-- apptools END -->
</div>