<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of date-related helper functions.
 * @version 1.0.1
 */
class DateHelper {

	/**
	 * Get the first day of current month.
	 * @since 1.0.0
	 * @param string $format Date format. Default to "Y-m-d".
	 * @return string Date of the first day of current month.
	 */
	public static function getFirstDayOfMonth($format = 'Y-m-d') {
		return(date($format, strtotime(date('Y-m-01'))));
	}

	/**
	 * Get the first day of current week.
	 * @since 1.0.0
	 * @param string $format Date format. Default to "Y-m-d".
	 * @return string Date of the first day of current week.
	 */
	public static function getFirstDayOfWeek($format = 'Y-m-d') {
		return(date($format, strtotime('-'.date('w').' days')));
	}

	/**
	 * Get the first day of current year with a given starting month.
	 * @since 1.0.0
	 * @param string $format Date format. Default to "Y-m-d".
	 * @param int $startMonth Starting month of year. Useful when getting the start date of a financial year. Default to January.
	 * @return string Date of the first day of current year.
	 */
	public static function getFirstDayOfYear($format = 'Y-m-d', $startMonth = 1) {
		return(date($format, strtotime((date('Y') - (date('n') >= $startMonth ? 0 : 1))."-$startMonth-01")));
	}

	/**
	 * Get the last day of current month.
	 * @since 1.0.0
	 * @param string $format Date format. Default to "Y-m-d".
	 * @return string Date of the last day of current month.
	 */
	public static function getLastDayOfMonth($format = 'Y-m-d') {
		return(date($format, strtotime(date('Y-m-t'))));
	}

	/**
	 * Get the last day of current week.
	 * @since 1.0.0
	 * @param string $format Date format. Default to "Y-m-d".
	 * @return string Date of the last day of current week.
	 */
	public static function getLastDayOfWeek($format = 'Y-m-d') {
		return(date($format, strtotime('+'.(6-date('w')).' days')));
	}

	/**
	 * Get the last day of current year with a given starting month.
	 * @since 1.0.0
	 * @param string $format Date format. Default to "Y-m-d".
	 * @param int $startMonth Starting month of year. Useful when getting the last date of a financial year. Default to January.
	 * @return string Date of the last day of current year.
	 */
	public static function getLastDayOfYear($format = 'Y-m-d', $startMonth = 1) {
		$date = DateTime::createFromFormat('Y-m-d', self::getFirstDayOfYear('Y-m-d', $startMonth));
		return($date->add(new DateInterval('P1Y'))->sub(new DateInterval('P1D'))->format($format));
	}

	/**
	 * Get the UTC time of a give timestamp.
	 * @since 1.0.0
	 * @param int|null $timestamp UNIX timestamp
	 * @return string Formatted datetime in UTC timezone.
	 */
	public static function getUtcTime($timestamp = null) {
		return(gmdate('Y-m-d H:i:s', $timestamp ? $timestamp : time()));
	}

	/**
	 * Get the current timestamp in UTC timezone.
	 * @since 1.0.0
	 * @return int UNIX timestamp in UTC timezone.
	 */
	public static function getUtcTimestamp() {
		return(time() - date('Z'));
	}
}
?>
