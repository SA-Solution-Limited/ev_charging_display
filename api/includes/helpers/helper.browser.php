<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of user-agent-related helper functions.
 * @version 1.0.0
 */
class BrowserHelper {

	/**
	 * Return whether the current operating system is Android OS with version equal to or new than the given value.
	 * @since 1.0.0
	 * @param int|null $version Version to check.
	 * @return boolean
	 */
	public static function isAndroid($version = null) {
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		return(!!preg_match('/android/', $ua) && ($version == null || floatval(substr($ua, strpos($ua, 'android ')+8, 3)) >= $version));
	}

	/**
	 * Return whether the current browser is Google Chrome with version equal to or new than the given value.
	 * @since 1.0.0
	 * @param int|null $version Version to check.
	 * @return boolean
	 */
	public static function isChrome($version = null) {
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		return(!!preg_match('/chrome/', $ua) && ($version == null || floatval(substr($ua, strpos($ua, 'chrome/')+7, 3)) >= $version));
	}

	/**
	 * Return whether the current browser is Mozilla Firefox with version equal to or new than the given value.
	 * @since 1.0.0
	 * @param int|null $version Version to check.
	 * @return boolean
	 */
	public static function isFirefox($version = null) {
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		return(!!preg_match('/firefox/', $ua) && ($version == null || floatval(substr($ua, strpos($ua, 'firefox/')+8, 3)) >= $version));
	}

	/**
	 * Return whether the current browser is Microsoft Internet Explorer with version equal to or new than the given value.
	 * @since 1.0.0
	 * @param int|null $version Version to check.
	 * @return boolean
	 */
	public static function isIE($version = null) {
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		return(!!preg_match('/msie/', $ua) && ($version == null || intval(substr($ua, strpos($ua, 'msie ')+5, 1)) >= $version));
	}

	/**
	 * Return whether the current operating system is iOS for iPad with version equal to or new than the given value.
	 * @since 1.0.0
	 * @param int|null $version Version to check.
	 * @return boolean
	 */
	public static function isIpadOS($version = null) {
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		return(!!preg_match('/ipad/', $ua) && ($version == null || intval(substr($ua, strpos($ua, 'os ')+3, 1)) >= $version));
	}

	/**
	 * Return whether the current operating system is iOS for iPhone with version equal to or new than the given value.
	 * @since 1.0.0
	 * @param array $version Version to check.
	 * @return boolean
	 */
	public static function isIphoneOS($version = null) {
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		return(!!preg_match('/(iphone|ipod)/', $ua) && ($version == null || intval(substr($ua, strpos($ua, 'os ')+3, 1)) >= $version));
	}

	/**
	 * Return whether the current device is a mobile device.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isMobile() {
		return(!!preg_match('/(ipad|iphone|ipod|android|iemobile|opera mini|blackberry|pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i', $_SERVER['HTTP_USER_AGENT']));
	}

}
?>
