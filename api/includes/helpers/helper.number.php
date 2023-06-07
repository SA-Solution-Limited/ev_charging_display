<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of number-related helper functions.
 * @version 1.0.0
 */
class NumberHelper {

	/**
	 * Convert float decimal number to fraction
	 * @since 1.0.0
	 * @param float $num Decimal number to convert.
	 * @param float $tolerance
	 * @return string Fraction
	 */
	public static function float2rat($num, $tolerance = 1.e-6) {
		if ($num == 0) return(0);
		$h1 = 1; $h2 = 0;
		$k1 = 0; $k2 = 1;
		$b = 1 / $num;
		do {
			$b = 1 / $b;
			$a = floor($b);
			$aux = $h1; $h1 = $a * $h1 + $h2; $h2 = $aux;
			$aux = $k1; $k1 = $a * $k1 + $k2; $k2 = $aux;
			$b = $b - $a;
		} while (abs($num - $h1 / $k1) > $num * $tolerance);
		return("$h1/$k1");
	}

}
?>
