<?php

defined('SW_') or die('Access Error');

class SWScode{
	
	/*=====================================================================
	* 显示验证码
	======================================================================*/
	public static function show($width=60,$height=25,$num=4,$noise=100){

		@header("Content-Type:image/png");

		$srcstr="23456789ABCDEFGHIJKLMNPQRSTUVW";
		mt_srand();

		$strs="";
		for($i=0;$i<$num;$i++){ //生成多少个字符
		$strs.=$srcstr[mt_rand(0,25)];
		}

		//$str=strtoupper($strs); //转换成大写
		$str=$strs; //随机生成的字符串
		$width = $width; //验证码图片的宽度
		$height = $height; //验证码图片的高度

		$im=imagecreate($width,$height);
		//背景色
		$back=imagecolorallocate($im,255,255,255);
		//模糊点颜色
		$pix=imagecolorallocate($im,0,0,0);
		//字体色
		//$font=imagecolorallocate($im,0xFF,0x99,0x66);
		$font=imagecolorallocate($im,rand(0,200),rand(0,120),rand(0,120));
		
		//绘模糊作用的点
		mt_srand();

		//模糊点
		for($i=0;$i<$noise;$i++) imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pix);

		//干扰线
		for($i=0;$i<=5;$i++){
			$line_color = imagecolorallocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
			//imagearc -- 画椭圆弧
			imagearc($im,rand(-$width,$width),rand(-$height,$height),rand(30,$width*2),rand(20,$height*2),rand(0,360),rand(0,360),$line_color);
		}

		imagestring($im, 5, mt_rand(7,ceil($width-($num*9)-7)), ceil($height/2-8),$str, $font);
		//imagerectangle($im,0,0,$width-1,$height-1,$font);

		imagepng($im);
		imagedestroy($im);

		//设置验证码session
		SW::setSession('SW_scode',$str);

	}

	/*=====================================================================
	* 验证
	======================================================================*/
	public static function check($strScode){

		$scode=SW::getSession('SW_scode');
		SW::setSession('SW_scode',null);

		if($strScode && strtolower($strScode)==strtolower($scode)){
			return true;
		}else{
			return false;
		}
		
	}
	
    
}

?>
