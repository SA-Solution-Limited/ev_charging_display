<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of form-related helper functions.
 * @version 1.0.0
 */
class FormHelper {

	/**
	 * Return whether the given value contains Chinese characters.
	 * @since 1.0.0
	 * @param string $value Value to check.
	 * @return boolean
	 */
	public static function hasHanCharacters($value, $exclude = null) {
		return(!!preg_match('/[\x{4e00}-\x{9fa5}]/u', $value));
	}

	/**
	 * Return whether the given value contains punctuations.
	 * @since 1.0.0
	 * @param string $value Value to check.
	 * @param string|null $exclude List of punctuation characters allowed.
	 * @return boolean
	 */
	public static function hasPunctuations($value, $exclude = null) {
		$punct = '!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~';
		if ($exclude) {
			$punct = preg_replace('/['.addcslashes(preg_quote($exclude), '/').']/', '', $punct);
		}
		return(strlen($punct) ? !!preg_match('/['.addcslashes(preg_quote($punct), '/').']/', $value) : false);
	}

	/**
	 * Return whether the given value is an empty value.
	 * @since 1.0.0
	 * @param mixed $value Value to check.
	 * @return boolean
	 */
	public static function isEmpty($value) {
		return(!!preg_match('/^$/', $value));
	}

	/**
	 * Return whether the given value is a valid telephone number.
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @return boolean
	 */
	public static function isValidContact($value) {
		return(!!preg_match('/^(\(\d+\)|\+\d+|\d*)[\d ]+\d+$/', $value));
	}

	/**
	 * Return whether the given value is a valid date with the given format.
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @param string $format Expected format of the value. Default to "Y-m-d"
	 * @return boolean
	 */
	public static function isValidDate($value, $format = 'Y-m-d') {
		return(!!DateTime::createFromFormat($format, $value));
	}

	/**
	 * Return whether the given value is a valid email address.
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @return boolean
	 */
	public static function isValidEmail($value) {
		return(!!preg_match('/^[\\w!#$%&\'*+\/=?^_`{|}~-]+(?:\\.[\\w!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[\\w](?:[\\w-]*[\\w])?\\.)+[\\w](?:[\\w-]*[\\w])?$/', $value));
	}

	/**
	 * Return whether the given value is a valid numeric value.
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @return boolean
	 */
	public static function isValidNumber($value) {
		return(!!preg_match('/^-?[0-9]+(.[0-9]+)?$/', $value));
	}

	/**
	 * Return whether the given value is a valid URL address.
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @return boolean
	 */
	public static function isValidUrl($value) {
		return(!!preg_match('/^https?:\/\/(.+@)?[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)*\.[A-Za-z]{2,4}(:[0-9]+)?(\/.*)?$/', $value));
	}

	/**
	 * Return whether the given value matches the given regular expression pattern.
	 * @since 1.0.0
	 * @param string $value Value to validate.
	 * @param string $pattern Regular expression to validate with.
	 * @param string $flag Flag of regular expression.
	 * @return boolean
	 */
	public static function isValidPattern($value, $pattern, $flag = '') {
		return(!!preg_match('/'.$pattern.'/'.$flag, $value));
	}

}
?>
