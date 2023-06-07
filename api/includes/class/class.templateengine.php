<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * @deprecated Use TemplateHelper instead.
 * @see TempalteHelper
 */
class TemplateEngine {
	
	public static function bindValues($tpl, $valueArray = array(), $finalize = false) {
		foreach ($valueArray as $key => $value) {
			$tpl = preg_replace('/_'.strtoupper($key).'_/', addcslashes($value, '$'), $tpl);
		}
		return($finalize ? self::finalize($tpl) : $tpl);
	}
	
	public static function finalize($tpl) {
		// remove unsubsituted values
		$tpl = preg_replace('/( ?)_([A-Z-]+?)_( ?)/', '$1$3', $tpl);
		// remove extra lines
		while (preg_match('/(\r|\n|\r\n)([ \t]*)(\r|\n|\r\n)/', $tpl)) {
			$tpl = preg_replace('/(\r|\n|\r\n)[ \t]*(\r|\n|\r\n)/', '$1', $tpl);
		}
		// remove double spaces
		while (preg_match('/  /', $tpl)) {
			$tpl = preg_replace('/  /', ' ', $tpl);
		}
		return($tpl."\n");
	}
	
}
?>
