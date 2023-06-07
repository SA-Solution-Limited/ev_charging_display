<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * Util
 * - Array values retrieval / mapping
 * - Browser detection
 * - Date functions
 * - File manipulation
 * - Form validation
 * - HTML coding
 * - HTTP request identification
 * - HTTP status code
 * - Miscellaneous
 * - Number functions
 * - String formatting
 * - URI functions
 * @version 1.0.9.3
 * @deprecated Use corresponding helper instead.
 * @see ArrayHelper
 * @see BrowserHelper
 * @see DateHelper
 * @see FileSystemHelper
 * @see FormHelper
 * @see HtmlHelper
 * @see HttpHelper
 * @see Inflector
 * @see NumberHelper
 * @see StringHelper
 * @see TemplateHelper
 * @see UrlHelper
 */
class Util {
	
	/* Array values retrieval / mapping */
	public static function arrayInput($array, $key, $default = '') {
		return(ArrayHelper::getValue($array, $key, $default));
	}
	public static function objectInput($object, $key, $default = '') {
		return(ArrayHelper::getValue($object, $key, $default));
	}
	public static function requestInput($key, $default = '') {
		return(HttpHelper::getRequestParam($key, $default));
	}
	public static function getInput($key, $default = '') {
		return(HttpHelper::getGetParam($key, $default));
	}
	public static function postInput($key, $default = '') {
		return(HttpHelper::getPostParam($key, $default));
	}
	public static function putInput($key, $default = '') {
		return(HttpHelper::getPutParam($key, $default));
	}
	public static function deleteInput($key, $default = '') {
		return(HttpHelper::getDeleteParam($key, $default));
	}
	public static function cookieInput($key, $default = '') {
		return(HttpHelper::getCookie($key, $default));
	}
	public static function sessionInput($key, $default = '') {
		return(SessionHelper::get($key, $default));
	}
	public static function headerInput($key, $default = '') {
		return(HttpHelper::getHeader($key, $default));
	}
	public static function isAssocArray($array) {
		return(ArrayHelper::isAssociative($array));
	}
	public static function mapProperties($src, array $map, $dstClass = null) {
		return(ArrayHelper::mapProperties($src, $map, $dstClass));
	}
	
	/* Browser detection */
	/* Return true if browser version is equal to or newer than given value */
	public static function isIE($v = null) {
		return(BrowserHelper::isIE($v));
	}
	public static function isFirefox($v = null) {
		return(BrowserHelper::isFirefox($v));
	}
	public static function isChrome($v = null) {
		return(BrowserHelper::isChrome($v));
	}
	public static function isMobile() {
		return(BrowserHelper::isMobile());
	}
	public static function isAndroid($v = null) {
		return(BrowserHelper::isAndroid($v));
	}
	public static function isIpadOS($v = null) {
		return(BrowserHelper::isIpadOS($v));
	}
	public static function isIphoneOS($v = null) {
		return(BrowserHelper::isIphoneOS($v));
	}
	
	/* Date functions */
	public static function getFirstDayOfWeek($format = 'Y-m-d') {
		return(DateHelper::getFirstDayOfWeek($format));
	}
	public static function getLastDayOfWeek($format = 'Y-m-d') {
		return(DateHelper::getLastDayOfWeek($format));
	}
	public static function getFirstDayOfMonth($format = 'Y-m-d') {
		return(DateHelper::getFirstDayOfMonth($format));
	}
	public static function getLastDayOfMonth($format = 'Y-m-d') {
		return(DateHelper::getLastDayOfMonth($format));
	}
	public static function getFirstDayOfYear($format = 'Y-m-d', $startMonth = 1) {
		return(DateHelper::getFirstDayOfYear($format, $startMonth));
	}
	public static function getLastDayOfYear($format = 'Y-m-d', $startMonth = 1) {
		return(DateHelper::getLastDayOfYear($format, $startMonth));
	}
	
	/* File manipulation */
	public static function mkdir($path, $mode = 0775, $recursive = false) {
		return(FileSystemHelper::mkdir($path, $mode, $recursive));
	}
	public static function scandir($path, $sort = 0, $recursive = false) {
		return(FileSystemHelper::scandir($path, $sort, $recursive));
	}
	public static function rmdir($path, $recursive = false) {
		return(FileSystemHelper::rmdir($path, $recursive));
	}
	public static function removeDirRecursive($path) {
		return(FileSystemHelper::rmdir($path, true));
	}
	public static function copy($src, $dst, $recursive = false, $exclude = '^\.ht') {
		return(FileSystemHelper::copy($src, $dst, $recursive, $exclude));
	}
	public static function gzip($src, $dst = null, $level = 9) {
		return(FileSystemHelper::gzip($src, $dst, $level));
	}
	public static function zipDirectory($src, $dst = null, $exclude = array()) {
		return(FileSystemHelper::zipdir($src, $dst, $exclude));
	}
	
	/* Form validation */
	public static function isEmpty($value) {
		return(FormHelper::isEmpty($value));
	}
	public static function isValidContact($value) {
		return(FormHelper::isValidContact($value));
	}
	public static function isValidEmail($value) {
		return(FormHelper::isValidEmail($value));
	}
	public static function isValidNumber($value) {
		return(FormHelper::isValidNumber($value));
	}
	public static function isValidUrl($value) {
		return(FormHelper::isValidUrl($value));
	}
	public static function isValidDate($value, $format = 'd/m/Y') {
		return(FormHelper::isValidDate($value, $format));
	}
	public static function isValidPattern($value, $pattern, $flag = '') {
		return(FormHelper::isValidPattern($value, $pattern, $flag));
	}
	public static function isExcludePunctuation($value, $include = '') {
		return(FormHelper::hasPunctuations($value, $include));
	}
	public static function hasChineseCharacters($value) {
		return(FormHelper::hasHanCharacters($value));
	}
	
	/* HTML coding */
	public static $cssId = array();
	public static function includeCSS($path, $attr = array(), $preventCache = false, $deferLoading = false) {
		HtmlHelper::includeCssFile($path, $attr, $preventCache, $deferLoading);
	}
	public static function includeJS($path = null, $js = null, $attr = array(), $preventCache = false) {
		if ($path) {
			HtmlHelper::includeJsFile($path, $attr, $preventCache);
		} else if ($js) {
			HtmlHelper::includeJsScript($js, $attr);
		}
	}
	public static function additionalCSS($array, $preventCache = false, $deferLoading = false) {
		if (is_string($array)) {
			echo($array."\n");
			return;
		}
		foreach ($array as $css) {
			if (is_string($css)) {
				HtmlHelper::includeCssFile($css, array(), $preventCache, $deferLoading);
			} else if (is_array($css)) {
				HtmlHelper::includeCssFile(self::arrayInput($css, 'href'), self::arrayInput($css, 'attr', array()), $preventCache, $deferLoading);
			}
		}
	}
	public static function additionalJS($array, $preventCache = false) {
		if (is_string($array)) {
			echo($array."\n");
			return;
		}
		foreach ($array as $val) {
			if (is_string($val)) {
				HtmlHelper::includeJsFile($val, array(), $preventCache);
			} else if (is_array($val)) {
				self::includeJS(self::arrayInput($val, 'src', null), self::arrayInput($val, 'js', null), self::arrayInput($val, 'attr', array()), $preventCache);
			}
		}
	}
	public static function array2attr($array) {
		return(HtmlHelper::array2attr($array));
	}
	public static function metaRefresh($delay = 0, $url = null) {
		HtmlHelper::redirect($url, $delay);
	}
	public static function getFileSystemPath($path) {
		return(FileSystemHelper::fspath($path));
	}
	
	/* HTTP request identification */
	public static function isPostRequest() {
		return(HttpHelper::isPost());
	}
	public static function isPutRequest() {
		return(HttpHelper::isPut());
	}
	public static function isDeleteRequest() {
		return(HttpHelper::isDelete());
	}
	public static function isAjaxRequest() {
		return(HttpHelper::isAjax());
	}
	
	/* HTTP status code */
	public static function http301($path, $exit = false) {
		HttpHelper::redirect($path, 301, $exit);
	}
	public static function http302($path, $exit = false) {
		HttpHelper::redirect($path, 302, $exit);
	}
	public static function http303($path, $exit = false) {
		HttpHelper::redirect($path, 303, $exit);
	}
	public static function http400($exit = false) {
		HttpHelper::status(400);
		$exit && exit;
	}
	public static function http401($exit = false) {
		HttpHelper::status($exit);
		$exit && exit;
	}
	public static function http403($exit = false) {
		HttpHelper::status(403);
		$exit && exit;
	}
	public static function http404($exit = false) {
		HttpHelper::status(404);
		$exit && exit;
	}
	public static function http405($exit = false) {
		HttpHelper::status(405);
		$exit && exit;
	}
	public static function http408($exit = false) {
		HttpHelper::status(408);
		$exit && exit;
	}
	public static function http409($exit = false) {
		HttpHelper::status(409);
		$exit && exit;
	}
	public static function http415($exit = false) {
		HttpHelper::status(415);
		$exit && exit;
	}
	public static function http500($exit = false) {
		HttpHelper::status(500);
		$exit && exit;
	}
	public static function http503($exit = false) {
		HttpHelper::status(503);
		$exit && exit;
	}
	public static function getAjaxResponse($success = true, $message = null, $data = null, $redirect = null) {
		return(HttpHelper::prepareAjaxResponse($success, $message, $data, $redirect));
	}
	public static function ajaxResponse($success = true, $message = null, $data = null, $redirect = null) {
		HttpHelper::ajaxResponse($success, $message, $data, $redirect);
	}
	
	/* Miscellaneous */
	public static function toBool($value) {
		return(filter_var($value, FILTER_VALIDATE_BOOLEAN));
	}
	public static function generateUid($length = 8, $exclude = array(), array $opts = array()) {
		return(StringHelper::generateUid($length, $exclude, $opts));
	}
	
	/* Number functions */
	public static function mapRange($value, $fromLow, $fromHigh, $toLow, $toHigh) {
		$factor = ($toHigh - $toLow) / ($fromHigh - $fromLow);
		return(($value - $fromLow) * $factor + $toLow);
	}
	
	/* String formatting */
	public static function formatFilesize($value, $unit = array('B', 'KB', 'MB', 'GB', 'TB')) {
		return(StringHelper::formatFilesize($value, $unit));
	}
	public static function formatDatetime($value, $output = 'datetime') {
		return(StringHelper::formatDatetime($value, $output));
	}
	public static function formatDuration($value, $units, $separator) {
		return(StringHelper::formatDuration($value, $units, $separator));
	}
	public static function formatAccountingAmt($val, $decimals = '2', $decPt = '.', $thousandSep = ',') {
		return(StringHelper::formatAccounting($val, $decimals, $decPt, $thousandSep));
	}
	public static function formatAddress($firstname, $lastname, $addr1, $addr2 = null, $addr3 = null, $pobox = null, $city = null, $province = null, $zip = null, $country) {
		return("{$firstname} {$lastname}\n".StringHelper::formatAddress([$addr1, $addr2, $addr3], $city, $province, $zip, $country));
	}
	public static function float2rat($n, $tolerance = 1.e-6) {
		return(NumberHelper::float2rat($n, $tolerance));
	}
	
	/* URI functions */
	public static function getOrigin() {
		return(UrlHelper::getOrigin());
	}
	public static function getHost($withProto = false) {
		return($withProto ? UrlHelper::getOrigin() : UrlHelper::getHost());
	}
	public static function getRemoteAddr() {
		return(UrlHelper::getRemoteAddr());
	}
	public static function pathCalc($url = '', $base = '/') {
		return(UrlHelper::getRelativePath($url, $base));
	}
	public static function addQueryString($query = array(), $url = null) {
		return(UrlHelper::addQueryString($query, $url));
	}
	public static function removeQueryString($key = array(), $url = null) {
		return(UrlHelper::removeQueryString($key, $url));
	}
	public static function str2slug($str, $sep = '-') {
		return(Inflector::str2slug($str, $sep));
	}
	public static function slug2str($slug, $sep = '-') {
		return(Inflector::slug2str($slug, $sep));
	}
}
?>
