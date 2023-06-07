<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

class MIME {
	public static function get($type) {
		if (preg_match('/\//', $type)) {
			$array = array_flip(get_class_vars('MIME'));
			return(preg_replace('/^_/', '', ArrayHelper::getValue($array, $type, '')));
		} else {
			$type = preg_replace('/^(\d)/', '_$1', strtolower($type));
			return(isset(self::${$type}) ? self::${$type} : '');
		}
	}
	// application
	protected static $_7z   = 'application/x-7z-compressed';
	protected static $apk   = 'application/vnd.android.package-archive';
	protected static $doc   = 'application/msword';
	protected static $docx  = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
	protected static $eot   = 'application/vnd.ms-fontobject';
	protected static $exe   = 'application/x-msdownload';
	protected static $json  = 'application/json';
	protected static $otf   = 'application/x-font-otf';
	protected static $pdf   = 'application/pdf';
	protected static $ppt   = 'application/vnd.ms-powerpoint';
	protected static $ppsx  = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
	protected static $pptx  = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
	protected static $rtf   = 'application/rtf';
	protected static $swf   = 'application/x-shockwave-flash';
	protected static $ttf   = 'application/x-font-ttf';
	protected static $woff  = 'application/x-font-woff';
	protected static $xls   = 'application/vnd.ms-excel';
	protected static $xlsx  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	protected static $zip   = 'application/zip';
	// audio
	protected static $aac   = 'audio/x-aac';
	protected static $mp3   = 'audio/mpeg';
	protected static $wav   = 'audio/x-wav';
	protected static $weba  = 'audio/webm';
	protected static $wma   = 'audio/x-ms-wma';
	// image
	protected static $bmp   = 'image/bmp';
	protected static $gif   = 'image/gif';
	protected static $ico   = 'image/x-icon';
	protected static $jpeg  = 'image/jpeg';
	protected static $jpg   = 'image/jpeg';
	protected static $png   = 'image/png';
	protected static $svg   = 'image/svg+xml';
	protected static $tiff  = 'image/tiff';
	// text
	protected static $css   = 'text/css';
	protected static $csv   = 'text/csv';
	protected static $html  = 'text/html';
	protected static $js    = 'text/javascript';
	protected static $php   = 'text/html';
	protected static $txt   = 'text/plain';
	protected static $xml   = 'text/xml';
	// video
	protected static $_3gp  = 'video/3gpp';
	protected static $avi   = 'video/x-msvideo';
	protected static $f4v   = 'video/x-f4v';
	protected static $flv   = 'video/x-flv';
	protected static $h263  = 'video/h263';
	protected static $h264  = 'video/h264';
	protected static $mp4   = 'video/mp4';
	protected static $mpeg  = 'video/mpeg';
	protected static $m4v   = 'video/x-m4v';
	protected static $mkv   = 'video/x-matroska';
	protected static $ogg   = 'video/ogg';
	protected static $ogv   = 'video/ogg';
	protected static $webm  = 'video/webm';
	protected static $wmv   = 'video/x-ms-wmv';
}
?>
