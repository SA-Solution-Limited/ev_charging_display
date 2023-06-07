<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of helper functions to covert strings.
 * @version 1.0.1
 */
class Inflector {

	/**
	 * Converts a slug into a camel case name.
	 * @since 1.0.1
	 * @param string $slug The slug to convert.
	 * @param string $separator The separator of the given slug.
	 * @return string The converted string.
	 */
	public static function slug2camel($slug, $separator = '-') {
		return(lcfirst(self::slug2pascal($slug, $separator)));
	}
	
	/**
	 * Converts a slug into a pascal name.
	 * @since 1.0.1
	 * @param string $slug The slug to convert.
	 * @param string $separator The separator of the given slug.
	 * @return string The converted string.
	 */
	public static function slug2pascal($slug, $separator = '-') {
		return(str_replace($separator, '', ucwords($slug, $separator)));
	}
	
	/**
	 * Return a string with all spaces converted to given replacement, non word characters removed and the rest of characters transliterated.
	 * @since 1.0.0
	 * @param string $slug The slug to convert.
	 * @param string $separator The separator of the given slug.
	 * @param boolean $ucwords Whether to capitalize every word of the converted string. Default to true.
	 * @return string The converted string.
	 */
	public static function slug2str($slug, $separator = '-', $ucwords = true) {
		$str = str_replace($separator, ' ', $slug);
		return($ucwords ? ucwords($str) : $str);
	}

	/**
	 * Return a string with all spaces converted to given replacement, non word characters removed and the rest of characters transliterated.
	 * @since 1.0.0
	 * @param string $string The string to convert.
	 * @param string $replacement The replacement to use for spaces.
	 * @param boolean $lowercase Whether to return the string in lowercase. Default to true.
	 * @return string|false The converted string if the convertion is successful, false otherwise.
	 */
	public static function str2slug($string, $replacement, $lowercase = true) {
		$slug = preg_replace('~[^\pL]+~u', $replacement, $string); // replace non letters
		$slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug); // transliterate
		$slug = preg_replace('~[^-\w]+~', '', $slug); // remove unwanted characters
		$slug = trim($slug, $replacement);
		$slug = preg_replace('~-+~', '-', $slug); // remove duplicated separators
		if ($lowercase) {
			$slug = strtolower($slug);
		}
		return($slug ? $slug : false);
	}
}
?>
