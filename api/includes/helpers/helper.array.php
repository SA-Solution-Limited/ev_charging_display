<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of array-related helper functions.
 * @version 1.0.0
 */
class ArrayHelper {

	/**
	 * Extract the value of an array element or object property with the given key or property name.
	 * @since 1.0.0
	 * @param array|object $array Array or object to extract value from.
	 * @param string $key Key name of array element.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getValue($array, $key, $default = null) {
		$array = is_object($array) ? (array)$array : $array;
		if (!is_array($array)) return(false);
		return(array_key_exists($key, $array) ? (is_string($array[$key]) ? trim($array[$key]) : $array[$key]) : $default);
	}

	/**
	 * Return whether this is an associative array.
	 * @since 1.0.0
	 * @param array $array Array to check.
	 * @return boolean Whether the given array is an associative array.
	 */
	public static function isAssociative($array) {
		return(is_array($array) && count(array_filter(array_keys($array), 'is_string')) > 0);
	}

	/**
	 * Map values from source objects to destination objects with a given mapping.
	 * @since 1.0.0
	 * @param array|object $src Object or array of objects to map properties from.
	 * @param array $map Key-value pair of mapping with key as source key name and value as destination key name.
	 * @param string|object|null $dstClass Model class to map values to. Default to stdClass.
	 * @return array|object Object or array of objects.
	 */
	public static function mapProperties($src, array $map, $dstClass = null) {
		if (!($isArray = is_array($src))) {
			$src = array($src);
		}
		if (is_object($dstClass)) {
			$dstClass = get_class($dstClass);
		} else if (!$dstClass) {
			$dstClass = 'stdClass';
		}
		$dstArray = array();
		foreach ($src as $row) {
			$class = new $dstClass();
			foreach ($map as $srcProp => $dstProp) {
				if (!isset($row->$srcProp)) continue;
				$dstClass = $class;
				if (preg_match('/\./', $dstProp)) {
					$dstProp = explode('.', $dstProp);
					while (count($dstProp) > 1) {
						$prop = array_shift($dstProp);
						if (!self::getValue($dstClass, $prop)) {
							$dstClass->{$prop} = new stdClass();
						}
						$dstClass = $dstClass->{$prop};
					}
					$dstProp = $dstProp[0];
				}
				if (is_numeric($row->$srcProp)) {
					$dstClass->$dstProp = floatval($row->$srcProp);
				} else {
					$dstClass->$dstProp = $row->$srcProp;
				}
			}
			$dstArray[] = $class;
		}
		return($isArray ? $dstArray : $dstArray[0]);
	}

	/**
	 * Build an associative array (key-value pairs) from a multidimensional array or an array of objects.
	 * @since 1.0.0
	 * @param array $array Source array.
	 * @param string $keyColumn Name of the column to map as key.
	 * @param string $valueColumn Name of the column to map as value.
	 * @return array Associative array (key-value pairs).
	 */
	public static function toKeyValuePair(array $array, $keyColumn, $valueColumn) {
		return(array_combine(array_map(function($row) use ($keyColumn) {
			return(((array)$row)[$keyColumn]);
		}, $array), array_map(function($row) use ($valueColumn) {
			return(((array)$row)[$valueColumn]);
		}, $array)));
	}

}
?>
