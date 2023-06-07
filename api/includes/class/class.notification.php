<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('includes/config/config.smtp.php');
use PHPMailer\PHPMailer\PHPMailer;

class Notification {
	
	public $success;
	public $message;

	protected $defaults = array(
		'smtp'       => null, // null or associative array with keys "host", "port", "secure" [(empty)|ssl|tls], "auth" [true|false], "user", "pass"
		'template'   => null,
		'from_email' => SMTP_FROMEMAIL,
		'from_name'  => SMTP_FROMNAME,
		'to_email'   => array(),
		'to_name'    => array(),
		'subject'    => null,
		'replace'    => array(), // key-value pair
		'delimiter'  => array('{{', '}}'),
		'log_file'   => null,
	);
	
	function __construct($opts, $debug = false) {
		$opts = array_merge($this->defaults, $opts);
		
		if (is_array($opts['smtp'])) {
			$opts['smtp'] = (object)$opts['smtp'];
		} else {
			$opts['smtp'] = new stdClass();
			$opts['smtp']->host = SMTP_HOST;
			$opts['smtp']->port = SMTP_PORT;
			$opts['smtp']->secure = SMTP_SECURE;
			$opts['smtp']->auth = SMTP_AUTH;
			$opts['smtp']->user = SMTP_USER;
			$opts['smtp']->pass = SMTP_PASS;
		}
		if (!$opts['template'] || !is_file($opts['template'])) {
			$this->response(false, 'Invalid e-mail template.');
			return;
		};
		if (!$opts['to_email']) {
			$this->response(false, 'Invalid receipient address.');
			return;
		};
		if (!is_array($opts['to_email'])) {
			$opts['to_email'] = array($opts['to_email']);
		}
		if (!is_array($opts['to_name'])) {
			$opts['to_name'] = array($opts['to_name']);
		}
		
		try {
			$mail = new PHPMailer(true);
			$mail->SMTPDebug = $debug ? 4 : 0;
			$mail->IsSMTP();
			$mail->Host = $opts['smtp']->host;
			$mail->Port = $opts['smtp']->port;
			$mail->SMTPSecure = $opts['smtp']->secure;
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => true,
					'verify_peer_name' => false,
					'allow_self_signed' => false,
				),
			);	
					
			$mail->SMTPAuth = $opts['smtp']->auth;
			if ($mail->SMTPAuth) {
				$mail->Username = $opts['smtp']->user;
				$mail->Password = $opts['smtp']->pass;
			}
			
			$mail->SetFrom($opts['from_email'], $opts['from_name']);
			foreach ($opts['to_email'] as $idx => $email) {
				$mail->AddAddress($email, ArrayHelper::getValue($opts['to_name'], $idx, null));
			}
			
			$template = file_get_contents($opts['template']);
			if (!is_array($opts['delimiter'])) {
				$opts['delimiter'] = array($opts['delimiter'], $opts['delimiter']);
			}
			foreach ($opts['replace'] as $key => $val) {
				$regexp   = addcslashes($opts['delimiter'][0], '.{}[]()').$key.addcslashes($opts['delimiter'][1], '.{}[]()');
				$template = preg_replace("/{$regexp}/i", $val, $template);
			}
			
			if (!$opts['subject']) {
				preg_match('/\<title\>(?<subject>.*)\<\/title\>/', $template, $matches);
				$opts['subject'] = htmlspecialchars_decode($matches['subject']);
			}
			
			$mail->Subject = $opts['subject'];
			$mail->Body = $template;
		
			$mail->CharSet = 'utf-8';
			$mail->Encoding = 'base64';
			$mail->ContentType = 'text/html';
			$mail->IsHTML(true);
			
			if ($result = $mail->Send()) {
				$this->response(true);
			} else {
				throw new Exception('');
			}
		} catch(phpmailerException $e) {
			$this->response(false, $e->getMessage());
		} catch(Exception $e) {
			$this->response(false, 'Unable to send notification.');
		}
		
		if ($opts['log_file']) {
			$dir = dirname($opts['log_file']);
			is_dir($dir) || FileSystemHelper::mkdir($dir, 0775, true);
			$log = array(
				'To: '.implode('; ', array_map(function($receipient) {
					return($receipient[1] ? "{$receipient[1]} <{$receipient[0]}>" : $receipient[0]);
				}, $mail->getToAddresses())),
				'Subject: '.$mail->Subject,
				'Date: '.date('Y-m-d H:i:s'),
				'Status: '.($this->success ? 'Sent' : 'Failed'),
			);
			if ($this->message) {
				$log[] = 'Error: '.$this->message;
			}
			array_push($log, '-----', '');
			@file_put_contents($opts['log_file'], implode(PHP_EOL, $log), FILE_APPEND);
		}
	}
	
	protected function response($success, $message = '') {
		$this->success = !!$success;
		$this->message = $message;
	}
}
?>