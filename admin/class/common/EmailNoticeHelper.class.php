<?php
require_once 'common/EmailSetting.class.php';
require_once 'lib/phpmailer/class.phpmailer.php';
class EmailNoticeHelper {
	/**
	 *
	 * @param string $template
	 *        	File path of the template
	 * @param array $param
	 *        	Associate array of name-value to the template
	 * @param string $receiver        	
	 * @param string $subject
	 *        	Email subject
	 * @param array $images
	 *        	Associate array for embedded images in the mail
	 * @param string $cc
	 *        	cc email address, spn.reg@gmail.com will be used if pass true.
	 * @param array $receivers
	 * 			other receivers
	 * @return boolean
	 */
	public static function sendNoticeMail($template, $param, $receiver, $subject, $images = null, $cc = null, $receivers = null, $pdf = null) {
		$filename = SITE_ROOT . '/' . $template;
		if(file_exists($filename)){
			$content = file_get_contents ( $filename );
		}else{
			$content = $template;
		}
		$keys = array_keys ( $param );
		foreach ( $keys as $key ) {
			$content = str_replace ( $key, $param [$key], $content );
		}
		
		// send email
		try {
			$mail = new PHPMailer ();
			$mail->SMTPDebug = 0;
			$mail->CharSet = "UTF-8";
			$mail->IsSMTP ();
			$mail->Host = EmailSetting::$host;
			$mail->Port = EmailSetting::$port;
			if (strlen ( EmailSetting::$username ) > 0) {
				$mail->SMTPAuth = true;
				$mail->Username = EmailSetting::$username;
				$mail->Password = EmailSetting::$password;
			}
			if (EmailSetting::$ssl) {
				$mail->SMTPSecure = "ssl";
			}
			$mail->From = EmailSetting::$from;
			$mail->FromName = EmailSetting::$from_name;
			$mail->AddAddress ( $receiver );
			if($receivers != null){
				foreach ($receivers as $re){
					$mail->AddAddress ( $re );
				}
			}
			if($cc != null){
				if($cc == true) {
					$mail->addCC(EmailSetting::$cc);
				}else{
					$mail->addCC($cc);
				}
			}
			// subject
			$mail->Subject = $subject;
			// content
			$mail->IsHTML ( true );
			$mail->Body = $content;
			if (isset($images) && is_array($images)) {
				$imageKeys = array_keys($images);
				foreach ($imageKeys as $imageKey) {
					$mail->AddEmbeddedImage(SITE_ROOT . '/' . $images[$imageKey], $imageKey);
				}
			}
			if (isset($pdf) && is_array($pdf)) {
				$pdfKeys = array_keys($pdf);
				foreach ($pdfKeys as $pdfKey) {
					$file = $pdf [$pdfKey];
					if(file_exists($file)){
						$finfo = finfo_open(FILEINFO_MIME_TYPE);
						$contentType = finfo_file($finfo, $file);
						finfo_close($finfo);
						
						$mail->addAttachment ( $file, $pdfKey, 'base64', $contentType );
					}
				}
			}
			$bool =  $mail->Send ();
			if(!$bool){
			    $logger = Logger::getLogger('EmailLogFileAppender');
			    $logger->error("Send email failed.".$mail->ErrorInfo);
			}
			return $bool;
		} catch ( Exception $e ) {
			$logger = Logger::getLogger('EmailLogFileAppender');
			$logger->error("Send email failed.", $e);
			
			return false;
		}
	}
}