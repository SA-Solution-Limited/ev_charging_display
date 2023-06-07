<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of HTML-related helper functions.
 * @version 1.0.2.1
 */
class HtmlHelper {

	/**
	 * Convert key-value pairs to array of HTML attribute strings.
	 * @since 1.0.0
	 * @param array $array Key-value pairs to convert.
	 * @param boolean $asString Whether to return the converted array as string. Default to `false`.
	 * @return string|array HTML attribute string or array of HTML attribute strings.
	 */
	public static function array2attr($array, $asString = false) {
		$attr = array_map(function($key, $value) {
			return(is_bool($value) || $value === null ? $key : ($key.'="'.$value.'"'));
		}, array_keys($array), array_values($array));
		return($asString ? implode(' ', $attr) : $attr);
	}

	protected static $cssId = array();

	/**
	 * Render a HTML tag to include stylesheet.
	 * @since 1.0.0
	 * @param string|array $path Stylesheet or array of Stylesheets to include.
	 * @param array $attr Key-value pair of attributes to append to HTML tag.
	 * @param boolean $preventCache Whether to append query string to file path to prevent caching. Default to false.
	 * @param boolean $deferLoading Whether to use JavaScript to load stylesheets after page loading completed. Default to false.
	 */
	public static function includeCssFile($path, array $attr = array(), $preventCache = false, $deferLoading = false) {
		if (!is_array($path)) {
			$path = array($path);
		}
		array_walk($path, function($path) use ($attr, $preventCache, $deferLoading) {
			if (!$path) return;
			if (!preg_match('/^https?:\/\//', $path)) {
				$fspath = FileSystemHelper::fspath($path);
				if (!is_file($fspath)) {
					return;
				} else if ($preventCache) {
					$path = UrlHelper::addQueryString(array('mt' => filemtime($fspath)), $path);
				}
			}
			$attr = array_replace(array(
				'rel'   => 'stylesheet',
				'type'  => 'text/css',
				'media' => 'all',
				'href'  => $path,
			), ArrayHelper::isAssociative($attr) ? $attr : array());
			if (ArrayHelper::getValue($attr, 'id') && !preg_match('/-css$/', $attr['id'])) {
				$attr['id'] .= '-css';
			}
			if ($deferLoading) {
				if (!ArrayHelper::getValue($attr, 'id') || in_array($attr['id'], self::$cssId)) {
					$attr['id'] = StringHelper::generateUid(12, self::$cssId, array('numbers' => false)).'-css';
				}
				self::$cssId[] = $attr['id'];
				$attr_js = implode('', array_map(function($key, $value) {
					$value = preg_replace("/'/", "\'", $value);
					return("a['{$key}']='{$value}';");
				}, array_keys($attr), array_values($attr)));
				$js = <<<EOD
(function(){
	var a=document.createElement('link');{$attr_js}
	var n=document.getElementById('{$attr["id"]}-render-js');n.parentNode.insertBefore(a,n);n.parentNode.removeChild(n);
})();
EOD;
				self::includeJsScript($js, array('id' => $attr['id'].'-render-js'));
			} else {
				$attr = implode(' ', self::array2attr($attr));
				echo("<link {$attr} />\n");
			}
		});
	}

	/**
	 * Render a HTML tag to include JS files.
	 * @since 1.0.0
	 * @param string|array $path JS files or array of JS files to include.
	 * @param array $attr Key-value pair of attributes to append to HTML tag.
	 * @param boolean $preventCache Whether to append query string to file path to prevent caching. Default to false.
	 * @param boolean $deferLoading Whether add `defer` attribute to rendered tag. Default to false.
	 */
	public static function includeJsFile($path, array $attr = array(), $preventCache = false, $deferLoading = false) {
		if (!is_array($path)) {
			$path = array($path);
		}
		array_walk($path, function($path) use ($attr, $preventCache, $deferLoading) {
			if (!$path) return;
			if (!preg_match('/^https?:\/\//', $path)) {
				$fspath = FileSystemHelper::fspath($path);
				if (!is_file($fspath)) {
					return;
				} else if ($preventCache) {
					$path = UrlHelper::addQueryString(array('mt' => filemtime($fspath)), $path);
				}
			}
			$attr = array_replace(array(
				'type'  => 'text/javascript',
				'src'   => $path,
			), ArrayHelper::isAssociative($attr) ? $attr : array());
			if (($id = ArrayHelper::getValue($attr, 'id')) && !preg_match('/-js$/', $id)) {
				$attr['id'] .= '-js';
			}
			if ($deferLoading) {
				$attr['defer'] = true;
			}
			$attr = implode(' ', self::array2attr($attr));
			echo("<script {$attr}></script>\n");
		});
	}

	/**
	 * Render a HTML tag to include JS scripts.
	 * @since 1.0.0
	 * @param string $script JS scripts to include.
	 * @param array $attr Key-value pair of attributes to append to HTML tag.
	 */
	public static function includeJsScript($script, array $attr = array()) {
		$attr = array_replace(array(
			'type' => 'text/javascript',
		), ArrayHelper::isAssociative($attr) ? $attr : array());
		if (array_key_exists('src', $attr)) {
			unset($attr['src']);
		}
		if (($id = ArrayHelper::getValue($attr, 'id')) && !preg_match('/-js$/', $id)) {
			$attr['id'] .= '-js';
		}
		$attr = implode(' ', self::array2attr($attr));
		echo("<script {$attr}>{$script}</script>\n");
	}

	/**
	 * Render a HTML link tag to pre-connect to a given domain.
	 * @since 1.0.2
	 * @param string $origin Origin to pre-connect to.
	 */
	public static function preConnect($origin) {
		echo("<link rel=\"preconnect\" href=\"{$origin}\" crossorigin />\n");
	}

	/**
	 * Render a HTML meta tag to redirect to a given URL.
	 * @since 1.0.0
	 * @param string $url URL to redirect to.
	 * @param float $delay Time to wait in seconds before redirecting.
	 */
	public static function redirect($url, $delay = 0) {
		echo("<meta http-equiv=\"refresh\" content=\"{$delay}; url={$url}\" />\n");
	}
}
?>
