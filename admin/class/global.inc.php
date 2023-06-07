<?php 
class globalSetting {
	public static $template_path = '/resource/template';
}

class appconfig
{
	public static $web_locale = array('zh');
	
	public static $registration_upload_path;
	public static $photo_album_upload_path;
}

appconfig::$registration_upload_path = SITE_ROOT . '/upload/registration/';
appconfig::$photo_album_upload_path  = SITE_ROOT . '/upload/photo-album/';

// setup smtp constants
define('SMTP_HOST', '192.168.1.122');
define('SMTP_PORT', '25');
define('SMTP_SECURE', '');
define('SMTP_AUTH', false);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROMEMAIL', 'noreply@spn.edu.hk');
define('SMTP_FROMNAME', '天主教聖保祿幼兒園(大圍)');
