/*
 * jquery.fm.js
 * Date: 2010-06-02
 * (c) 2009-2010 fumin
 */
 
 
document.onmousemove = mouseMove; 
function mouseMove(ev){
	ev = ev || window.event; 
	mousePos = mouseCoords(ev); 
} 
function mouseCoords(ev){ 
	if(ev.pageX || ev.pageY){ 
		return {x:ev.pageX, y:ev.pageY}; 
	}else{
		return { 
			x:ev.clientX + document.body.scrollLeft - document.body.clientLeft, 
			y:ev.clientY + document.body.scrollTop - document.body.clientTop 
		}; 
	}
}

(function($){
	//好评插件
	$.fn.fmTuopiao = function(options){
		var defaults = {
			value:"未评/很差/较差/一般/较好/很好",//默认的选择项
			img:"images/tuopiao_no.gif",//默认的图片
			imgHover:"images/tuopiao_yes.gif"//鼠标经过时的图片
		}
		var options = $.extend(defaults, options);
		this.each(function(){
			sAttay=options.value.split("/");//把选择项转化成图片形式插入到页面中
			img="<img src="+options.img+" />";
			text="<span>"+sAttay[0]+"</span>";
			for(i=0;i<sAttay.length-1;i++){
				$(this).append(img);
			}
			var n=0;
			
			$(this).append(text);
			$(this).children("img").bind('mouseover',function(){//鼠标经过的时候 更换图片 并显示相应的文字
				
				g=$(this).parent().children("img").index($(this))+1;
				$(this).parent().children("img").attr('src',options.img);
				$(this).parent().children("img").slice(0,g).attr('src',options.imgHover);
				$(this).parent().children("span").html(options.value.split("/")[g]);
			});
			$(this).children("img").bind('mouseout',function(){//鼠标离开后 还原成点击前的状态
				$(this).parent().children("img").attr('src',options.img);
				$(this).parent().children("img").slice(0,n).attr('src',options.imgHover);
				$(this).parent().children("span").html(options.value.split("/")[n]);
			});
			$(this).children("img").bind('click',function(){ //鼠标点击 更改选择点的位置
				var g=$(this).parent().children("img").index($(this))+1;
				n=g;
				$(this).parent().children("img").attr('src',options.img);
				$(this).parent().children("img").slice(0,g).attr('src',options.imgHover);
				$(this).parent().children("span").html(options.value.split("/")[g]);
			});
		});
	};
	
	//提示框插件
	$.fn.fmTitle = function(options){
		var defaults = {
			style:"yellow",//提示框的风格 yellow,red
			color:"#757168",//提示框里面字体颜色
			size:"12px"//提示框里面字体大小
		}
		var options = $.extend(defaults, options);
		var path    = 'jQuery/plugins/TitleEdit/images/';
		this.each(function(){
			$(this).hover(function(e){
				//鼠标经过图片 把DIV插入到鼠标的位置上
				var xx=(e.originalEvent.x+Math.max(document.documentElement.scrollLeft,document.body.scrollLeft)||e.originalEvent.layerX||0)+10;
				var yy=(e.originalEvent.y+Math.max(document.documentElement.scrollTop,document.body.scrollTop)||e.originalEvent.layerY||0)-10;
				//alert((e.originalEvent.y||e.originalEvent.layerY||0));
				xx = mousePos.x+10;
				yy = mousePos.y+10;
				var c=$(this).attr('content');
				var title_content="<div id='titleC' class='titleC' style='position:absolute; top:"+yy+"px; left:"+xx+"px;'><table  border="+0+" cellspacing="+0+" cellpadding="+0+"><tr><td><img src='" +path + options.style+"01.gif' /></td><td class='"+options.style+"_bg02'></td><td><img src='" +path + options.style+"03.gif' /></td></tr><tr><td class='"+options.style+"_bg04' valign='top'><div class='title_div'><div class='title_left'><img src='" +path + options.style+"_left.gif' /></div></div></td><td class='"+options.style+"_cont' style='color:"+options.color+";font-size:"+options.size+"'>"+c+"</td><td class='"+options.style+"_bg05'></td></tr><tr><td><img src='" +path + options.style+"06.gif' width="+6+" height="+7+" /></td><td class='"+options.style+"_bg07'></td><td><img src='" +path + options.style+"08.gif' width="+7+" height="+7+" /></td></tr></table></div>";
				if(1 == parseInt(property_view_on)){
					$("body").append(title_content);
				}
			},function(){//移除DIV
				$('.titleC').remove();
			});
		});
	};
	
	//编辑保存插件	
	$.fn.fmEdit = function(options){
		var defaults = {
			targetClass:"n",//改变CLASS为target的控件
			inputClass:"input_text_link",//文本框的样式
			inputClassHover:"input_text_hover",//文本框经过的样式
			textareaClass:"input_textarea_link",//文本域的样式
			textareaClassHover:"input_textarea_hover"//文本域经过的样式
		}
		var options = $.extend(defaults, options);
		this.each(function(){
			var id=$(this).attr("id");
			$(this).toggle(function(){
				$(this).attr("value","保存");
				$("#"+id+"_content ."+options.targetClass).each(function(){
					//把文字变成文本框
					if($(this).attr("name")=="text"){
						var input="<input type='text' name='"+id+"_"+$("#"+id+"_content ."+options.targetClass).index($(this))+"'  class="+options.inputClass+" onmouseover=\"this.className=\'"+options.inputClassHover+"\'\"  onmouseout=\"this.className=\'"+options.inputClass+"\'\"  value="+$(this).html()+" />";
						$(this).html(input);
					}
					//把文字变成文本域
					else if($(this).attr("name")=="textarea"){
						var textarea="<textarea  name='"+id+"_"+$("#"+id+"_content ."+options.targetClass).index($(this))+"'  class="+options.textareaClass+" onmouseover=\"this.className=\'"+options.textareaClassHover+"\'\"  onmouseout=\"this.className=\'"+options.textareaClass+"\'\" >"+$(this).html()+"</textarea>";
						$(this).html(textarea);
					}
					//把文字变成下拉框，并且把select属性的内容变成下拉形式，默认是当前文本。
					else if($(this).attr("name")=="select"){
						var s=$(this).attr("select").split("/");
						var select1="<select name='"+id+"_"+$("#"+id+"_content ."+options.targetClass).index($(this))+"' >";
						for(var i=0;i<s.length;i++){
							if(s[i]==$(this).html()) select1+="<option value="+s[i]+" selected='selected'>"+s[i]+"</option>";
							else select1+="<option value="+s[i]+">"+s[i]+"</option>";
						}
						select1+="</select>";
						$(this).html(select1);
					}
					//把文字变成checkbox，并且把select属性的内容变成备选内容，默认勾选是当前文本出现的内容。										
					else if($(this).attr("name")=="checkbox"){
						var s=$(this).attr("select").split("/");
						var v=$(this).html().split(" ");
						var checkbox="";
						for(var i=0;i<s.length;i++){
							if(jQuery.inArray(s[i], v)!=-1) checkbox+="<input name='"+id+"_"+$("#"+id+"_content ."+options.targetClass).index($(this))+"' type='checkbox' checked='checked' value="+s[i]+" />"+s[i];
							else checkbox+="<input name='"+id+"_"+$("#"+id+"_content ."+options.targetClass).index($(this))+"' type='checkbox'  value="+s[i]+" />"+s[i];
						}
						$(this).html(checkbox);
					}
					//把文字变成radio，并且把select属性的内容变成备选内容，默认是当前文本。	
					else if($(this).attr("name")=="radio"){
						var s=$(this).attr("select").split("/");
						var radio="";
						for(var i=0;i<s.length;i++){
							if(s[i]==$(this).html()) radio+="<input name='"+id+"_"+$("#"+id+"_content ."+options.targetClass).index($(this))+"' type='radio' checked='checked' value="+s[i]+" />"+s[i];
							else radio+="<input name='"+id+"_"+$("#"+id+"_content ."+options.targetClass).index($(this))+"' type='radio'  value="+s[i]+" />"+s[i];
						}
						$(this).html(radio);
					}
				});
			},function(){
				//保存输入值和勾选值，并转移成文本形式显示在页面中。
				$(this).attr("value","编辑");
				$("#"+id+"_content ."+options.targetClass).each(function(){
					if($(this).attr("name")=="text") $(this).html($(this).children().attr("value"));
					else if($(this).attr("name")=="textarea") $(this).html($(this).children().attr("value"));
					else if($(this).attr("name")=="select") $(this).html($("select option:selected",this).html());
					else if($(this).attr("name")=="checkbox"){
						var a=[];
						$("input:checked",this).each(function(){
							a.push($(this).attr("value"));
						})
						$(this).html(a.join(" "));
					}
					else if($(this).attr("name")=="radio") $(this).html($("input:checked",this).attr("value"));
				});
			})
		});
	};
})(jQuery);