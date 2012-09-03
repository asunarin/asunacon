<?php

defined('SW_') or die('Access Error');

class SWPost extends SWTable{

	function __construct(){
		
		//设置对象数据表名
		$this->tableName=TBL_POST;
		
		//设置对象数据表
		$this->table=array(
			'id'=>array('type'=>'int','default'=>0),
			'title'=>array('type'=>'string','default'=>''),
			'content'=>array('type'=>'string','default'=>''),
			'state'=>array('type'=>'int','default'=>0),
			'allowcomment'=>array('type'=>'int','default'=>1),
			'posttime'=>array('type'=>'int','default'=>time()),
			'edittime'=>array('type'=>'int','default'=>time()),
			'alias'=>array('type'=>'string','default'=>''),
			'flag'=>array('type'=>'int','default'=>0),
			'log_pingback'=>array('type'=>'string','default'=>'')
		);
		
		//根据数据表获取对象属性
		$this->setAttr();

	}
	
	/*=====================================================================
	* 重写save方法，同时保存标签
	======================================================================*/
	function save($arrTag=array(),$pingback=array()){
		
		parent::save();
		
		//保存标签操作
		$objTag=new SWTag();
		$objTag->saveByPostId($this->id,$arrTag);

		return true;
				
	}

	/*=====================================================================
	* 重写delete方法
	* 同时删除标签和评论
	======================================================================*/
	function delete(){

		if($this->id>0){
			
			parent::delete();

			//同时删除所有标签
			$objTag=new SWTag();
			$objTag->deleteByPostid($this->id);

			//同时删除所有评论
			$objComm=new SWComment();
			$objComm->deleteByPostid($this->id);

		}

	}
	
	/*=====================================================================
	* 文章逻辑删除方法，其实就是修改flag属性
	* $resume为真时为恢复文章，即设置flag=1
	======================================================================*/
	function trash($resume=false){

		if($this->id>0){
			
			$this->flag=$resume?'1':'0';
			$this->editSingle('flag');

		}

	}
	
	/*=====================================================================
	* 重写condition方法
	* 设置查询语句的条件（where），具体见函数内逻辑
	======================================================================*/
	function condition($arr){
		
		//默认没有筛选的情况下，flag为1
		$strRe='flag=1';
		
		if(is_array($arr)){
						
			$arr1=array();

			//根据id筛选（多个）,这个是绝对的，设置了这个就忽略了其他条件
			if(isset($arr['ids'])){
				
				$arr['ids']=$this->sqlValue($arr['ids'],'int',',');
				$strRe=$this->tableName.".id in (".$arr['ids'].")";
				
			}else{
			
				//根据state筛选（多个）
				if(isset($arr['state'])){
					
					$arr['state']=$this->sqlValue($arr['state'],'int',',');
					$arr1[]=$this->tableName.".state in (".$arr['state'].")";
					
				}
				
				//根据搜索筛选
				if(isset($arr['search'])){
					
					$arr1[]="(".$this->tableName.".title like ('%".$arr['search']."%') or ".$this->tableName.".content like ('%".$arr['search']."%'))";
					
				}
				
				//根据标签筛选
				if(isset($arr['tag'])){
					
					$objTmp=new SWTag();
					$strTmp=$objTmp->getPostidsByName($arr['tag']);
					if($strTmp){
						$arr1[]=$this->tableName.".id in (".$strTmp.")";
					}else{
						$arr1[]=$this->tableName.".id in (0)";
					}
					
				}

                //根据日期筛选
                if(isset($arr['year'])){
                    $strY=SWFunc::checkInt($arr['year']);
                    $strM=SWFunc::checkInt(empty($arr['month'])?'':$arr['month']);
                    $strD=SWFunc::checkInt(empty($arr['day'])?'':$arr['day']);

                    //利用mktime的容错性，很方便
                    if($strD && $strM){
                        $arr1[]=$this->tableName.".posttime>=".mktime(0,0,0,$strM,$strD,$strY)." and ".$this->tableName.".posttime<".mktime(0,0,0,$strM,$strD+1,$strY)." ";
                    }elseif($strM){
                        $arr1[]=$this->tableName.".posttime>=".mktime(0,0,0,$strM,1,$strY)." and ".$this->tableName.".posttime<".mktime(0,0,0,$strM+1,1,$strY)." ";
                    }else{
                        $arr1[]=$this->tableName.".posttime>=".mktime(0,0,0,1,1,$strY)." and ".$this->tableName.".posttime<".mktime(0,0,0,1,1,$strY+1)." ";
                    }
                }

                //下一步，处理flag
				$arr2=array();
				
				//默认情况下全部都是flag=1的
				if(count($arr1)>0){
					$arr1[]=$this->tableName.".flag=1";
					$arr2[]="(".implode(' and ',$arr1).")";
				}
				
				//如果包含已删除的
				if(isset($arr['trash'])){
					
					$arr2[]="flag=0";
				
				}
				
				if(count($arr2)>0) $strRe="(".implode(' or ',$arr2).")";
				
			}
			
		}
		
		return $strRe;
		
	}

	/*=====================================================================
	* 匹配所有文章内容中的链接地址（一般用于发送pingback）
	* 返回链接数组
	======================================================================*/
	function getLinks(){

		//找到文章中所有链接
		preg_match_all('/<a[^>]+href=(?:"|\')((?=https?\:\/\/)[^"\'<>]+)(?:"|\')[^>]*>[^>]+<\/a>/is',stripslashes($this->content),$matches);
		$links = empty($matches[1])?array():$matches[1];
		$links = array_unique( $links );

		return $links;

	}

	/*=====================================================================
	* 取得该文章下的标签对象
	======================================================================*/
	function getTag(){
		$objTag=new SWTag();
		return $objTag->getMore(array('postid'=>$this->id),array('id'=>'asc'));
	}
	
	/*=====================================================================
	* 取得该文章下的标签，返回格式化的标签字符串
	* 如果传入url参数，那么会给标签加上连接（替换连接中的{tagname}为标签名）
	======================================================================*/
	function getTagNames($split=',',$url=''){
		
		$strRe='';
		$arrTag=$this->getTag();
		
		if(count($arrTag)>0){
			
			$arrTmp=array();
			
			foreach($arrTag as $value){
				
				if($url){
					//这里会对tagname进行urlencode编码
					$arrTmp[]=str_replace('{tagname}',urlencode($value->name),'<a href="'.$url.'">'.$value->name.'</a>');
				}else{
					$arrTmp[]=$value->name;
				}
			}
			
			$strRe=implode($split,$arrTmp);
			
		}
		
		return $strRe;
	}

	/*=====================================================================
	* 取得该文章下的评论对象
	* 默认获取state=1的评论
	======================================================================*/
	function getComment(){
		$objComment=new SWComment();
		return $objComment->getMore(array('postid'=>$this->id),array('posttime'=>'desc'));
	}

	/*=====================================================================
	* 获取文章下评论数量
	======================================================================*/
	function getCommentCount(){
		$objComment=new SWComment();
		return $objComment->getMoreCount(array('postid'=>$this->id));
	}
	
	/*=====================================================================
	* 检查pingback是否已经发过
	======================================================================*/
	function hasLogPingback($url){
		$arrLog=explode(',',$this->log_pingback);
		return in_array($url,$arrLog);
	}
	
	/*=====================================================================
	* 添加pingback发送日志
	* 如果返回false说明已经添加过
	======================================================================*/
	function addLogPingback($url){
		
		//检查是否已经发过
		if(!$this->hasLogPingback($url)){
		
			//保存发送列表
			$this->log_pingback=$this->log_pingback?$this->log_pingback.','.$url:$url;
			$this->editSingle('log_pingback');

		}

	}
	
	/*=====================================================================
	* 获取readmore信息
	* 如果文章中含有readmore标记则设置content属性为分割后的内容，并返回true
	* 如果不含，则返回false
	======================================================================*/
	public function readmore(){
		
		//首先替换标记
		$content=$this->content;
		$content=preg_replace('/<div[^<>]+readmore_break[^\`]+<\/div>/i','<readmore />',$content);
		$arrContent=explode('<readmore />',$content);
		if(count($arrContent)>1){
			//注意，这里需要关闭未关闭的标签
			$this->content=SWFunc::closeTags($arrContent[0]);
			return true;
		}else{
			return false;
		}

	}
	
	/*=====================================================================
	* 查询后一篇文章
	======================================================================*/
	public function getNewer(){
		
		$sql="select * from ".$this->tableName." where ".$this->tableName.".flag=1 and ".$this->tableName.".state=1 and ".$this->tableName.".posttime>".$this->posttime." order by posttime asc limit 0,1 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		if($res){
			$objTmp=new $this();
			$objTmp->setAttr($res[0]);
			return $objTmp;
		}else{
			return null;
		}

	}
	
	/*=====================================================================
	* 查询前一篇文章
	======================================================================*/
	public function getOlder(){
		
		$sql="select * from ".$this->tableName." where ".$this->tableName.".flag=1 and ".$this->tableName.".state=1 and ".$this->tableName.".posttime<".$this->posttime." order by posttime desc limit 0,1 ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		
		if($res){
			$objTmp=new $this();
			$objTmp->setAttr($res[0]);
			return $objTmp;
		}else{
			return null;
		}

	}
	
	/*=====================================================================
	* 根据月份归档文章列表
	* 返回一个日期的数组
	* 可以设置最多返回多少日期
	======================================================================*/
	public static function get_archives($num=0){

		$arrRe=array();

		//文章归档
		$sql="select posttime from ".TBL_POST." where ".TBL_POST.".flag=1 and ".TBL_POST.".state=1 order by posttime desc ;";
		$db=SW::getDb();
		$res=$db->query($sql);
		if($res){
			//获取文章的最大时间和最小时间
			$max=$res[0]['posttime'];
			$arrTmp=end($res);
			$min=$arrTmp['posttime'];

			//获取数组个数
			$start=0;
            $count=0;
			$end=count($res);
			
			$arrTmp=getdate($max);
						
			//循环月份，小于最小时间就跳出，顺序从大到小
			while(true){

				$y=$arrTmp['year'];
				$m=$arrTmp['mon'];

				$thisMonth=mktime(0,0,0,$m,1,$y);
				$lastMonth=mktime(0,0,0,$m-1,1,$y);
				$nextMonth=mktime(0,0,0,$m+1,1,$y);
				
				//查找这个时间段内有没有文章
				$postCount=0;//文章计数器
				for($i=$start;$i<$end;$i++){

					if($res[$i]['posttime']<$thisMonth) break;
					if($res[$i]['posttime']<$nextMonth) $postCount++;

				}
				$start=$i;

				//如果有文章，则添加数组
				if($postCount>0){
					$arrRe[]=array(
						'date'=>strtotime($y.'-'.$m.'-01'),
						'year'=>$y,
						'month'=>$m,
						'count'=>$postCount
					);
                    $count++;
				}
				
                if($thisMonth<$min) break;
                if($num>0 && $count>=$num) break;

				$arrTmp=getdate($lastMonth);

			}

		}

		return $arrRe;

	}
	
}

?>
