/*=====================================================================
* 创建编辑器
======================================================================*/
SWJS.editor_create=function(strName, height, width){

	height=height?height:350;
	width=width?width:760;
	
	//将editor对象加入全局变量
	GLB['editor']=$('#editor_div textarea').xheditor({
		tools:'Source,Blocktag,|,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,Removeformat,|,Align,List,Outdent,Indent,Link,Unlink,Img,Flash,|,Fullscreen,Code,Readmore',
		editorRoot:SWJS.urlRoot+'admin/js/editor/',
		skin:'nostyle',
		width:width,
		height:height,
		plugins:{
			Code:{c:'btnCode',t:'插入代码',h:1,e:function(){
				var _this=this;
				var htmlCode='<div><select id="xheCodeType"><option value="php">PHP</option></select></div><div><textarea id="xheCodeValue" wrap="soft" spellcheck="false" style="width:300px;height:100px;" /></div><div style="text-align:right;"><input type="button" id="xheSave" value="确定" /></div>';
				var jCode=$(htmlCode),jType=$('#xheCodeType',jCode),jValue=$('#xheCodeValue',jCode),jSave=$('#xheSave',jCode);
				jSave.click(function(){
					_this.loadBookmark();
					_this.pasteHTML('<pre class="codeArea" value="'+jType.val()+'">'+_this.domEncode(jValue.val())+'</pre>');
					_this.hidePanel();
					return false;	
				});
				_this.showDialog(jCode);
			}},
			Readmore:{c:'btnReadmore',t:'分页',e:function(){
				this.pasteHTML('<div class="readmore_break"></div>');
			}}
		}
	});

};

/*=====================================================================
* 默认执行
======================================================================*/
$(function(){

	SWJS.editor_create('content',350,760);

});
