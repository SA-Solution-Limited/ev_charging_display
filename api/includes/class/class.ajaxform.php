<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('class.form.php');

class AjaxForm extends Form {
	
	const HTTP_STATUS = array(
		400 => 'Missing required parameter(s).',
		401 => 'Authorization required prior to perform requests.',
		403 => 'Requested operation is not permitted.',
		404 => 'Requested resources not available.',
		405 => 'Request method not allowed.',
		408 => 'Request timeout.',
		409 => 'Conflict.',
		415 => 'Unsupported media type.',
		500 => 'The server encountered an unexpected condition.',
		503 => 'Service temporarily unavailable.',
	);
	
	protected function requirePost() {
		return(HttpHelper::isPost() ? true : $this->status(405));
	}
	
	protected function response($isSuccess = true, $message = '', $info = array()) {
		global $site;
		if ($isSuccess == null) $isSuccess = false;
		
		// prepare response body
		$result = array(
			'success' => $isSuccess,
			'message' => $message,
		);
		if (count($info) > 0) $result = array_merge($result, $info);
		
		header('Access-Control-Allow-Origin: '.ArrayHelper::getValue($_SERVER, 'HTTP_ORIGIN', '*'));
		header('Access-Control-Allow-Credentials: true');
		header('Content-Type: application/json');
		exit(json_encode($result));
	}
	
	protected function status($code, $message = null, $info = array()) {
		if (!array_key_exists($code, self::HTTP_STATUS)) {
			return;
		}
		HttpHelper::status($code);
		$this->response(false, $message ? $message : self::HTTP_STATUS[$code], $info);
		exit;
	}
}
?>