<?php

defined('SW_') or die('Access Error');

class SWAttachment{
		
	/*=====================================================================
	* 列出目录下所有文件和文件夹
	======================================================================*/
	function getList($path){

		$file_list = array();

		//图片扩展名
		$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
		
		//目录必须存在
		if (is_dir($path)) {

			//遍历目录取得文件信息
			if ($handle = opendir($path)) {
				$i = 0;
				while (false !== ($filename = readdir($handle))) {
					if ($filename{0} == '.') continue;
					$file = $path . $filename;
					if (is_dir($file)) {
						$file_list[$i]['is_dir'] = true; //是否文件夹
						$file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
						$file_list[$i]['filesize'] = 0; //文件大小
						$file_list[$i]['is_photo'] = false; //是否图片
						$file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
					} else {
						$file_list[$i]['is_dir'] = false;
						$file_list[$i]['has_file'] = false;
						$file_list[$i]['filesize'] = filesize($file);
						$file_list[$i]['dir_path'] = '';
						$file_ext = strtolower(array_pop(explode('.', trim($file))));
						$file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
						$file_list[$i]['filetype'] = $file_ext;
					}
					$file_list[$i]['filename'] = $filename; //文件名，包含扩展名
					$file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
					$i++;
				}
				closedir($handle);
			}

		}

		usort($file_list, array('SWAttachment','cmp_func'));
		return $file_list;
		
	}

	/*=====================================================================
	* 对列出的文件进行排序
	======================================================================*/
	function cmp_func($a, $b) {
		$order='name';
		if ($a['is_dir'] && !$b['is_dir']) {
			return -1;
		} else if (!$a['is_dir'] && $b['is_dir']) {
			return 1;
		} else {
			if ($order == 'size') {
				if ($a['filesize'] > $b['filesize']) {
					return 1;
				} else if ($a['filesize'] < $b['filesize']) {
					return -1;
				} else {
					return 0;
				}
			} else if ($order == 'type') {
				return strcmp($a['filetype'], $b['filetype']);
			} else {
				return strcmp($a['filename'], $b['filename']);
			}
		}
	}
	
	/*=====================================================================
	* 创建文件夹
	======================================================================*/
	function createFolder($path,$folderName){
				
		//目录必须存在
		if (is_dir($path)) {

			//遍历目录取得文件信息
			if ($handle = opendir($path)) {
				$i = 0;
				$folder = $path . $folderName;
				while (false !== ($filename = readdir($handle))) {
					if ($filename{0} == '.') continue;
					if (is_dir($folder) && $filename==$folderName) return; //文件夹是否存在
				}
				
				if(mkdir($folder)) return true;
				
			}
			
		}
		
		return false;
		
	}
	
	/*=====================================================================
	* 上传文件方法
	* 上传失败返回false
	* 返回一个上传文件信息数组
	======================================================================*/
	function upload($max_size=1000000, $ext_arr=array('gif','jpg','jpeg','png','bmp','swf','flv','zip')){
		
		//获取文件请求信息
		if (empty($_FILES) === false) {
			
			//原文件名
			$file_name = $_FILES['file']['name'];
			//服务器上临时文件名
			$tmp_name = $_FILES['file']['tmp_name'];
			//文件大小
			$file_size = $_FILES['file']['size'];
			
		}else{
			
			return false;
		
		}
		
		//自动生成年份->月份目录
		$year=date('Y',time());
		$month=date('m',time());
		
		//创建年份目录
		if(!is_dir(SW::dirPath('attached.'.$year))){
			
			if(!$this->createFolder(SW::dirPath('attached'),$year)) return false;
			
		}
		
		//创建月份目录
		if(!is_dir(SW::dirPath('attached.'.$year.'.'.$month))){
			
			if(!$this->createFolder(SW::dirPath('attached.'.$year),$month)) return false;
			
		}
		
		//获取保存路径
		$requestPath = $year.'.'.$month;

		//文件保存目录路径
		$save_path = SW::dirPath('attached.'.$requestPath);

		//文件保存目录URL
		$save_url = SW::getOption('url').'attached/'.str_replace('.','/',$requestPath).'/';
			
		//检查文件名
		if (!$file_name) return false;
		
		//检查目录
		if (@is_dir($save_path) === false) return false;
		
		//检查目录写权限
		if (@is_writable($save_path) === false) return false;
		
		//检查是否已上传
		if (@is_uploaded_file($tmp_name) === false) return false;
		
		//检查文件大小
		if ($file_size > $max_size) return false;
		
		//获得文件扩展名
		$temp_arr = explode(".", $file_name);
		$file_ext = array_pop($temp_arr);
		$file_ext = trim($file_ext);
		$file_ext = strtolower($file_ext);
		
		//检查扩展名
		if (in_array($file_ext, $ext_arr) === false) return false;
		
		//新文件名
		$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
		
		//移动文件
		$file_path = $save_path . $new_file_name;
		if (move_uploaded_file($tmp_name, $file_path) === false) return false;
		@chmod($file_path, 0644);
		$file_url = $save_url . $new_file_name;
		
		//如果成功，返回一个数组
		$arrReturn=array(
			'name'=>$new_file_name,
			'path'=>$requestPath,
			'url'=>$file_url,
			'size'=>$file_size
		);
		return $arrReturn;
		
	}
	
}

?>
