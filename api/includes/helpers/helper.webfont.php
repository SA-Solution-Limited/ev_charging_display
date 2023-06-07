<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of helper functions for embedding web fonts.
 * @version 1.0.0
 */
class WebFontHelper {

	/**
	 * Render HTML tags to embed Adobe Fonts.
	 * @since 1.0.0
	 * @param string $projectId Project ID of Adobe Fonts Web Project.
	 * @param boolean $deferLoading Whether to use JavaScript to load stylesheets after page loading completed. Default to false.
	 */
	public static function embedAdobeFonts($projectId, $deferLoading = false) {
		HtmlHelper::preConnect('https://use.typekit.net');
		HtmlHelper::includeCssFile("https://use.typekit.net/{$projectId}.css", array('id' => 'adobe-fonts'), false, $deferLoading);
	}

	/**
	 * Render HTML tags to embed Google Fonts.
	 * @since 1.0.0
	 * @param array $fonts Key-value pair of font settings where the key refers to font name and the value refers to font weight and font style settings.
	 * @param boolean $deferLoading Whether to use JavaScript to load stylesheets after page loading completed. Default to false.
	 */
	public static function embedGoogleFonts($fonts, $deferLoading = false) {
		$fonts = array_map(function($family, $variation) {
			$italic = strpos($variation, 'i') !== false;
			$pieces = array(
				'family='.str_replace(' ', '+', $family).':',
				$italic ? 'ital,' : '',
				'wght@',
			);
			$variation = array_unique(explode(',', $variation));
			if ($italic) {
				$variation = array_map(function($v) {
					return((strpos($v, 'i') === false ? 0 : 1).preg_replace('/^(\d+)(?:i(?:talic)?)?$/', ',$1', $v));
				}, $variation);
			}
			sort($variation);
			$pieces[] = implode(';', $variation);
			return(implode('', $pieces));
		}, array_keys($fonts), array_values($fonts));
		HtmlHelper::preConnect('https://fonts.googleapis.com');
		HtmlHelper::preConnect('https://fonts.gstatic.com');
		HtmlHelper::includeCssFile('https://fonts.googleapis.com/css2?'.implode('&', $fonts), array('id' => 'google-fonts'), false, $deferLoading);
	}
}
?>
