<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of URL-related helper functions.
 * @version 1.0.1
 */
class UrlHelper {

	/**
	 * Add parameters to a given URL.
	 * @since 1.0.0
	 * @param array $params Key-value pair of parameters.
	 * @param string|null $url URL to add parameters to. Default to current URL.
	 * @return string URL with encoded query string.
	 */
	public static function addQueryString($params = array(), $url = null) {
		parse_str($url ? parse_url($url, PHP_URL_QUERY) : $_SERVER['QUERY_STRING'], $existing);
		$query = http_build_query(array_merge((array)$existing, $params));
		return(($url ? strtok($url, '?') : '').($query ? "?{$query}" : $query));
	}

	/**
	 * Return the host name of the current request.
	 * @since 1.0.0
	 * @return string Host name.
	 */
	public static function getHost() {
		return(ArrayHelper::getValue($_SERVER, 'HTTP_X_FORWARDED_HOST', $_SERVER['HTTP_HOST']));
	}

	/**
	 * Return the URL origin of the current request.
	 * @since 1.0.0
	 * @return string Host name with protocol.
	 */
	public static function getOrigin() {
		$proto = ArrayHelper::getValue($_SERVER, 'HTTPS', 'off') == 'off' && ArrayHelper::getValue($_SERVER, 'HTTP_X_FORWARDED_PROTO') != 'https' ? 'http://' : 'https://';
		return($proto.self::getHost());
	}

	/**
	 * Return the relative path based on provide base URL.
	 * @since 1.0.0
	 * @param string|null $url URL to compare from.
	 * @param string $base Base URL to compare against. Default to root (/).
	 * @return string Relative path.
	 */
	public static function getRelativePath($url = null, $base = '/') {
		$url = parse_url($url ? $url : $_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$url = explode('/', $url);
		$base = explode('/', $base);
		if (count($url) - count($base) <= 0) {
			return('./');
		}
		return(implode('', array_fill(0, count($url) - count($base), '../')));
	}

	/**
	 * Return the remote address of the current request.
	 * @since 1.0.0
	 * @return string IP address of client.
	 */
	public static function getRemoteAddr() {
		return(ArrayHelper::getValue($_SERVER, 'HTTP_X_FORWARDED_FOR', $_SERVER['REMOTE_ADDR']));
	}

	/**
	 * Remove parameters from a given URL.
	 * @since 1.0.0
	 * @param array $key Array of keys of parameters to remove.
	 * @param string|null $url URL to remove parameters from. Default to current URL.
	 * @return string URL with encoded query string.
	 */
	public static function removeQueryString($key = array(), $url = null) {
		parse_str($url ? parse_url($url, PHP_URL_QUERY) : $_SERVER['QUERY_STRING'], $existing);
		if (!is_array($key)) {
			$key = array($key);
		}
		for ($i = 0; $i < count($key); $i++) {
			if (array_key_exists($key[$i], $existing)) unset($existing[$key[$i]]);
		}
		return(($url ? strtok($url, '?') : '').(count($existing) ? '?'.http_build_query($existing) : ''));
	}
}
?>
