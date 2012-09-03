<?php

defined('SW_') or die('Access Error');

/*=====================================================================
* 发送邮件类
======================================================================*/
class SWMail{

	function __construct(){
		
		//发送mail的类型，为空则不发送
		$this->type=SW::getOption('mail_type');
		
		//发送参数
		$this->address=SW::getOption('mail_address');
		$this->server=SW::getOption('mail_server');
		$this->port=SW::getOption('mail_port');
		$this->account=SW::getOption('mail_account');
		$this->password=SW::getOption('mail_password');

	}
	
	/*=====================================================================
	* 发送邮件方法
	======================================================================*/
	function send($subject,$body,$sendTo,$sendToName='',$isHtml=false){

		switch($this->type){
			
			//如果是smtp发送模式
			case 'smtp':
				
				//检查传入信息
				if(!$subject || !$body || !SWFunc::checkEmail($sendTo)) return false;

				//设置日志文件路径
				PHPMailer_Log::$path = SW::path('logs.mail','log');
				
				//创建对象
				$mail = new PHPMailer();
				$mail->IsSMTP();

				//设置发信参数
				$mail->SMTPDebug  = 4; // enables SMTP debug information
				$mail->Host = $this->server; // SMTP server
				$mail->SMTPAuth = true; // enable SMTP authentication
				$mail->Port = $this->port; // set the SMTP port for the GMAIL server
				$mail->Username = $this->account; // SMTP account username
				$mail->Password = $this->password; // SMTP account password
				$mail->CharSet = 'utf-8'; // set the charset
				
				//设置发件人信息
				$mail->SetFrom($this->address, SW::getOption('title'));
				
				//设置标题
				$mail->Subject = SWFunc::checkString($subject);
				
				//设置内容
				$body = SWFunc::checkString($body);
				if($isHtml){
					$mail->MsgHTML($body);
				}else{
					$mail->Body = $body;
					$mail->IsHTML(false);
				}
				
				//设置收件人
				$mail->AddAddress($sendTo, $sendToName);

				if(!$mail->Send()) {
					return false;
				} else {
					return true;
				}

			break;

		}

	}
    
}

?>
