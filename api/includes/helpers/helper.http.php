<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of helper functions related to HTTP request and response.
 * @version 1.0.4
 */
class HttpHelper {

	const HTTP_STATUS = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		103 => 'Early Hints',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Content Too Large',
		414 => 'URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a Teapot',
		421 => 'Misdirected Request',
		422 => 'Unprocessable Content',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Too Early',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		451 => 'Unavailable For Legal Reasons',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
	);
	
	/**
	 * Issue an AJAX response prepared by `prepareAjaxResponse()`.
	 * @since 1.0.0
	 * @see HttpHelper::prepareAjaxResponse()
	 * @param boolean $success Whether the request is success or not
	 * @param string|null $message Message to response.
	 * @param mixed|null $data Data to deliver.
	 * @param string|null $redirect Redirect URL if redirection is needed.
	 */
	public static function ajaxResponse($success = true, $message = null, $data = null, $redirect = null) {
		header('Content-Type: application/json');
		exit(json_encode(self::prepareAjaxResponse($success, $message, $data, $redirect)));
	}
	
	/**
	 * Issue a `Content-Disposition` header to force an attachment download of a give content.
	 * @since 1.0.3
	 * @see HttpHelper::prepareAjaxResponse()
	 * @param string $content Content to download.
	 * @param string $filename Name of file to output.
	 * @param string $mimeType MIME type of content.
	 */
	public static function downloadContent($content, $filename, $mimeType = 'application/octet-stream') {	
		header('Content-Description: File Transfer');
		header("Content-Type: {$mimeType}");
		header('Content-Length: '.strlen($content));
		header("Content-Disposition: attachment; filename=\"{$filename}\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		echo($content);
		exit;
	}
	
	/**
	 * Issue a `Content-Disposition` header to force an attachment download of a give source file.
	 * @since 1.0.3
	 * @see HttpHelper::downloadContent()
	 * @param string $src Path of source file.
	 * @param string|null $filename Name of file to output.
	 */
	public static function downloadFile($src, $filename = null) {
		if (!is_file($src)) return(false);
		self::downloadContent(file_get_contents($src), $filename ? $filename : basename($src), mime_content_type($src));
	}

	/**
	 * Retrieve a cookie collection parameter with a given key.
	 * @since 1.0.0
	 * @param string $key Key name of cookie.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return string Value of the element if found, default value otherwise.
	 */
	public static function getCookie($key, $default = null) {
		return(ArrayHelper::getValue($_COOKIE, $key, $default));
	}

	/**
	 * Retrieve a DELETE parameter with a given key.
	 * @since 1.0.0
	 * @global array $_DELETE Array of parameters populated from PHP input stream.
	 * @param string $key Key name of request parameters.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getDeleteParam($key, $default = null) {
		global $_DELETE;
		return(ArrayHelper::getValue($_DELETE, $key, $default));
	}

	/**
	 * Retrieve a GET parameter with a given key.
	 * @since 1.0.0
	 * @param string $key Key name of request parameters.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getGetParam($key, $default = null) {
		return(ArrayHelper::getValue($_GET, $key, $default));
	}

	/**
	 * Retrieve a header value with a given key.
	 * @since 1.0.0
	 * @param string $key Key name of request parameters.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getHeader($key, $default = null) {
		$headers = getallheaders();
		return(ArrayHelper::getValue($headers, $key, ArrayHelper::getValue($headers, strtolower($key), $default)));
	}

	/**
	 * Retrieve a POST parameter with a given key.
	 * @since 1.0.0
	 * @param string $key Key name of request parameters.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getPostParam($key, $default = null) {
		return(ArrayHelper::getValue($_POST, $key, $default));
	}

	/**
	 * Retrieve a PUT parameter with a given key.
	 * @since 1.0.0
	 * @global array $_PUT Array of parameters populated from PHP input stream.
	 * @param string $key Key name of request parameters.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getPutParam($key, $default = null) {
		global $_PUT;
		return(ArrayHelper::getValue($_PUT, $key, $default));
	}

	/**
	 * Retrieve a REQUSET parameter with a given key.
	 * @since 1.0.0
	 * @param string $key Key name of request parameters.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getRequestParam($key, $default = null) {
		return(ArrayHelper::getValue($_REQUEST, $key, $default));
	}

	/**
	 * Retrieve a server parameter with a given key.
	 * @since 1.0.3
	 * @param string $key Key name of request parameters.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getServerParam($key, $default = null) {
		return(ArrayHelper::getValue($_SERVER, $key, $default));
	}

	/**
	 * Retrieve a session parameter with a given key.
	 * @since 1.0.0
	 * @param string $key Key name of request parameters.
	 * @param mixed|null $default Default value if the key does not exist.
	 * @return mixed Value of the element if found, default value otherwise.
	 */
	public static function getSessionParam($key, $default = null) {
		return(SessionHelper::get($key, $default));
	}

	/**
	 * Return whether this is an AJAX (XMLHttpRequest) request.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isAjax() {
		return(strtoupper(ArrayHelper::getValue($_SERVER, 'HTTP_X_REQUESTED_WITH')) == 'XMLHTTPREQUEST');
	}

	/**
	 * Return whether this is a DELETE request.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isDelete() {
		return(strtoupper($_SERVER['REQUEST_METHOD']) == 'DELETE');
	}

	/**
	 * Return whether this is a GET request.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isGet() {
		return(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET');
	}

	/**
	 * Return whether this is a HEAD request.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isHead() {
		return(strtoupper($_SERVER['REQUEST_METHOD']) == 'HEAD');
	}

	/**
	 * Return whether this is a OPTIONS request.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isOptions() {
		return(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS');
	}

	/**
	 * Return whether this is a PATCH request.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isPatch() {
		return(strtoupper($_SERVER['REQUEST_METHOD']) == 'PATCH');
	}

	/**
	 * Return whether this is a POST request.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isPost() {
		return(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST');
	}

	/**
	 * Return whether this is a PUT request.
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function isPut() {
		return(strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT');
	}

	/**
	 * Prepare an AJAX response model.
	 * @since 1.0.0
	 * @param boolean $success Whether the request is success or not
	 * @param string|null $message Message to response.
	 * @param mixed|null $data Data to deliver.
	 * @param string|null $redirect Redirect URL if redirection is needed.
	 * @return stdClass Response model.
	 */
	public static function prepareAjaxResponse($success = true, $message = null, $data = null, $redirect = null) {
		$model = new stdClass();
		$model->success = !!$success;
		if ($message !== null) {
			$model->message = $message;
		}
		if ($data !== null) {
			$model->data = $data;
		}
		if ($redirect !== null) {
			$model->redirect = $redirect;
		}
		$model->timestamp = date('c');
		return($model);
	}

	/**
	 * Issue a Location response header with the URL to redirect.
	 * @since 1.0.0
	 * @param string $path URL to redirect to.
	 * @param int $statusCode HTTP 3xx status code.
	 * @param boolean $exit Whether to terminate the application immediately. Default to true.
	 */
	public static function redirect($path, $statusCode = 302, $exit = true) {
		self::status($statusCode);
		header("Location: {$path}");
		$exit && exit;
	}

	/**
	 * Issue a HTTP status code.
	 * @since 1.0.0
	 * @param int $code HTTP status code
	 */
	public static function status(int $code) {
		if (!array_key_exists($code, self::HTTP_STATUS)) {
			$code = 200;
		}
		header("HTTP/2.0 {$code} ".self::HTTP_STATUS[$code]);
	}

}
?>
