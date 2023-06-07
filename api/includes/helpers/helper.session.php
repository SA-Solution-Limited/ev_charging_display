<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of PHP session-related helper functions.
 * @version 1.0.0
 */
class SessionHelper {
	
	/**
	 * Delete data from PHP session with a given key.
	 * @since 1.0.0
	 * @global Site $site System configuration.
	 * @param string $key Key of data.
	 */
	public static function delete($key) {
		global $site;
		if ($site && $site->session && isset($_SESSION[$site->session])) {
			unset($_SESSION[$site->session][$key]);
		} else {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * Retrieve data from PHP session with a given key.
	 * @since 1.0.0
	 * @global Site $site System configuration.
	 * @param string $key Key of data.
	 * @param mixed|null $default Default value if key is not found.
	 * @return mixed Session data.
	 */
	public static function get($key, $default = null) {
		global $site;
		if ($site && $site->session && isset($_SESSION[$site->session])) {
			return(ArrayHelper::getValue($_SESSION[$site->session], $key, $default));
		} else {
			return(ArrayHelper::getValue($_SESSION, $key, $default));
		}
	}

	/**
	 * Retrive temporary data from PHP session where the data will be deleted immediately upon retrival.
	 * @since 1.0.0
	 * @param string $key Key of data.
	 * @param string $value Data to save.
	 */
	public static function getTmpData($key) {
		$key = "tmp_{$key}";
		$value = self::get($key);
		self::delete($key);
		return($value);
	}

	/**
	 * Reset PHP session.
	 * @since 1.0.0
	 * @global Site $site System configuration.
	 */
	public static function reset() {
		global $site;
		if ($site && $site->session) {
			$_SESSION[$site->session] = array();
		} else {
			session_destroy();
		}
	}

	/**
	 * Save data to PHP session.
	 * @since 1.0.0
	 * @global Site $site System configuration.
	 * @param string $key Key of data.
	 * @param string $value Data to save.
	 */
	public static function set($key, $value) {
		global $site;
		if ($site && $site->session) {
			if (!ArrayHelper::getValue($_SESSION, $site->session)) {
				$_SESSION[$site->session] = array();
			}
			$_SESSION[$site->session][$key] = $value;
		} else {
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Save temporary data to PHP session.
	 * @since 1.0.0
	 * @param string $key Key of data.
	 * @param string $value Data to save.
	 */
	public static function setTmpData($key, $value) {
		self::set('tmp_'.$key, $value);
	}
}
?>
