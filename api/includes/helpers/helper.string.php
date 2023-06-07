<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of helper functions to format string.
 * @version 1.0.2
 */
class StringHelper {

	/**
	 * Check if a string ends with a given suffix, if not, append it.
	 * @since 1.0.2
	 * @param string $string The string to check with.
	 * @param string $suffix The substring to append.
	 * @return string String ends with the givne suffix.
	 */
	public static function endsWith($string, $suffix = '') {
		if (!$suffix || substr($string, strlen($string) - strlen($suffix)) === $suffix) {
			return($string);
		}
		return($string.$suffix);
	}

	/**
	 * Format number to a accounting format.
	 * @since 1.0.0
	 * @global float $value Number to format
	 * @param int $decimals Number of decimal digits.
	 * @param string $decimalSep Separator for the decimal point.
	 * @param string $thousandSep Thousands separator.
	 * @return string Number in accounting format, i.e. negative numbers are wrapped with parentheses.
	 */
	public static function formatAccounting($value, $decimals = '2', $decimalSep = '.', $thousandSep = ',') {
		return(str_replace('{value}', number_format(abs($value), $decimals, $decimalSep, $thousandSep), $value >= 0 ? '{value}' : '({value})'));
	}

	/**
	 * Combine pieces of address information into a full address.
	 * @since 1.0.0
	 * @param array $address Lines of the address including house number, street, etc.
	 * @param string|null $city City of the address.
	 * @param string|null $state State or province of the address.
	 * @param string|null $postalCode Postal code or zip code of the address.
	 * @param string|null $country Country of the address.
	 * @return string Multi-line address.
	 */
	public static function formatAddress($address, $city = null, $state = null, $postalCode = null, $country = null) {
		if (!is_array($address)) {
			$address = array($address);
		}
		if ($postalCode) {
			if ($state) {
				$address[] = $city;
				$address[] = "{$state}, {$postalCode}";
			} else if ($city) {
				$address[] = "{$city}, {$postalCode}";
			} else {
				$address[] = $postalCode;
			}
		} else {
			$address[] = $city;
			$address[] = $state;
		}
		$address[] = $country;
		return(implode("\n", array_filter($address)));
	}

	/**
	 * Format datetime to a pre-defined format.
	 * @since 1.0.0
	 * @global array $locale
	 * @param int $value Datetime to format.
	 * @param string $output Name of foramt defined in locale files. Valid values are "date", "time" and "datetime".
	 * @return string Datetime string.
	 */
	public static function formatDatetime($value, $output = 'datetime') {
		global $locale;
		$date = strtotime($value);
		if (!$date) return('Invalid date/time');
		if (!isset($locale['format'][$output])) return('Invalid output format');
		return(date($locale['format'][$output], $date));
	}

	/**
	 * Format time duration in seconds to a human-readable string.
	 * @since 1.0.2
	 * @param int $duration Duration in seconds.
	 * @param array $units Key-value pair where key is the divisor and value is an array of singular and plural form of unit.
	 * @param string $separator Separator of the string. Default to a single space.
	 * @return string Human-readable duration.
	 */
	public static function formatDuration($duration, array $units = array(), $separator = ' ') {
		$units = array_replace_recursive(array(
			0 => array('second', 'seconds'),
			60 => array('minute', 'minutes'),
			3600 => array('hour', 'hours'),
			86400 => array('day', 'days'),
		), $units);
		if ($duration == 0) {
			return($duration.$separator.$units[0][0]);
		}
		krsort($units);
		$pieces = array();
		foreach ($units as $divisor => $unit) {
			if ($divisor > $duration) continue;
			if ($divisor > 0) {
				$quotient = floor($duration / $divisor);
				$pieces[] = $quotient.$separator.$unit[min($quotient - 1, 1)];
				$duration -= $quotient * $divisor;
			} else if ($duration > 0) {
			    $pieces[] = $duration.$separator.$unit[min($duration - 1, 1)];
		    }
		}
		return(implode(' ', $pieces));
	}

	/**
	 * Format file size as a human-readable string.
	 * @since 1.0.0
	 * @param int $value File size to format.
	 * @param array $unit Array of units. Default from byte (B) to terabyte (TB).
	 * @return string Human-readable file size.
	 */
	public static function formatFilesize(int $value, array $unit = array('B', 'KB', 'MB', 'GB', 'TB')) {
		if ($value == 0) return($value.$unit[0]);
		$i = 0;
		while ($value >= 1024) {
			$value /= 1024;
			$i++;
		}
		return(round($value, $i == 0 ? 0 : 2).$unit[$i]);
	}

	/**
	 * Generate a random string.
	 * @since 1.0.0
	 * @param int $length Length of the generated string.
	 * @param array $opts Array of options. Default to ['alphabets' => true, 'numbers' => true, 'case_sensitive' => true].
	 * @return string|false Random string on success or false on failure.
	 */
	public static function generateRandomString(int $length = 32, array $opts = array()) {
		$opts = array_merge(array(
			'alphabets' => true,
			'numbers' => true,
			'case_sensitive' => true,
		), $opts);
		$char = implode('', [
			$opts['alphabets'] ? 'abcdefghijklmnopqrstuvwxyz' : '',
			$opts['numbers'] ? '0123456789' : '',
			$opts['alphabets'] && $opts['case_sensitive'] ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' : '',
		]);
		if (strlen($char) == 0) return(false);
		return(implode('', array_map(function() use ($char) {
			return($char[rand(0, strlen($char) - 1)]);
		}, array_fill(0, $length, ''))));
	}

	/**
	 * Generate a unique ID.
	 * @since 1.0.0
	 * @see StringHelper::generateRandomString()
	 * @param int $length Length of the generated unique ID.
	 * @param array $exclude Array of existing IDs to exclude.
	 * @param array $opts Array of options. Default to ['alphabets' => true, 'numbers' => true, 'case_sensitive' => true].
	 * @return string|false Unique ID on success or false on failure.
	 */
	public static function generateUid(int $length = 32, array $exclude = array(), array $opts = array()) {
		do {
			$uid = self::generateRandomString($length, $opts);
		} while (in_array($uid, $exclude));
		return($uid);
	}

	/**
	 * Check if a string starts with a given prefix, if not, prepend it.
	 * @since 1.0.2
	 * @param string $string The string to check with.
	 * @param string $prefix The substring to prepend.
	 * @return string String starts with the givne suffix.
	 */
	public static function startsWith($string, $prefix = '') {
		if (!$prefix || substr($string, 0, strlen($prefix)) === $prefix) {
			return($string);
		}
		return($prefix.$string);
	}
}
?>
