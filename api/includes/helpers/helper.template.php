<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of helper functions to prepare a template.
 * @version 1.0.0
 */
class TemplateHelper {
	
	/**
	 * Bind parameters to a given template.
	 * @since 1.0.0
	 * @param string $template Template to bind parameters to. Parameters should be enclosed by {{ and }} in template.
	 * @param array $params Key-value pairs of parameters to bind.
	 * @param boolean $finalize Whether to finalize the template.
	 * @return string Template binded with parameters.
	 */
	public static function bindParams($template, $params = array(), $finalize = false) {
		foreach ($params as $key => $value) {
			$template = preg_replace('/\{\{'.strtolower($key).'\}\}/', addcslashes($value, '$'), $template);
		}
		return($finalize ? self::finalize($template) : $template);
	}
	
	/**
	 * Finalize a template by removing unbinded placeholders and extra lines and spaces.
	 * @since 1.0.0
	 * @param string $template Template to finalize.
	 * @return string Finalized template.
	 */
	public static function finalize($template) {
		// remove unsubsituted values
		$template = preg_replace('/( ?)\{\{([a-z-]+?)\}\}( ?)/', '$1$3', $template);
		// remove extra lines
		while (preg_match('/(\r|\n|\r\n)([ \t]*)(\r|\n|\r\n)/', $template)) {
			$template = preg_replace('/(\r|\n|\r\n)[ \t]*(\r|\n|\r\n)/', '$1', $template);
		}
		// remove double spaces
		while (preg_match('/  /', $template)) {
			$template = preg_replace('/  /', ' ', $template);
		}
		return($template."\n");
	}
	
}
?>
