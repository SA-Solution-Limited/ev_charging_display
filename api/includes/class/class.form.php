<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

class Form {
	public $data = array();
	public $forbidden = false;
	public $success = false;
	public $result = array();
	public $invalid = array();
	public $isAjax = 0;
	
	function __construct() {
	}

	/**
	 * @deprecated
	 */
	protected function requirePost() {
		return(HttpHelper::isPost());
	}
	
	protected function redirectFromView() {
		return(HttpHelper::getPostParam('form') == 'view');
	}
	
	protected function getId($key = 'id') {
		$id = HttpHelper::getRequestParam($key, HttpHelper::getRequestParam('id', null));
		return(is_string($id) ? rawurldecode($id) : $id);
	}
	
	protected function cloneEntity($entity) {
		$prop = get_object_vars($entity);
		foreach ($prop as $key => $val) {
			$this->$key = $val;
		}
	}
	
	protected function bindValues($array, $class = null) {
		if ($class == null) {
			$class = $this;
		}
		if (is_string($class)) {
			$class = new $class();
		}
		$prop = get_object_vars($class);
		foreach ($prop as $key => $val) {
			$class->$key = ArrayHelper::getValue((array)$array, $key, $class->$key);
			if (is_string($class->$key)) $class->$key = trim(stripslashes($class->$key));
		}
		return($class);
	}
	
	protected function cleanData(array $array = array()) {
		foreach ($array as $key) {
			$this->$key = $this->emptyToNull($this->$key);
		}
	}
	
	protected function emptyToNull($value) {
		return($value == '' ? null : $value);
	}
	
	protected function recaptcha() {
		global $site;
		if (!($key = $site->getSrvConfig('recaptcha', 'private')) || !($challenge = HttpHelper::getPostParam('g-recaptcha-response', null))) return(false);
		require_once('includes/class/class.proxy.php');
		$proxy = new Proxy('https://www.google.com/recaptcha/api/siteverify');
		$proxy->set('data', array(
			'secret'   => $key,
			'response' => $challenge,
			'remoteip' => $_SERVER['REMOTE_ADDR'],
		));
		$response = $proxy->execute();
		$response->data = json_decode($response->data);
		return($response->success && $response->data->success);
	}
	
	public static function requestToken() {
		global $site;
		if (!SessionHelper::get('anti_forgery_token')) {
			$array = array();
		}
		$token = base64_encode(md5(uniqid('', true)));
		$array[] = $token;
		$site->saveToSession('anti_forgery_token', $array);
		return($token);
	}
	
	public static function validateToken($token) {
		return(in_array($token, SessionHelper::get('token', array())));
	}
	
	public static function revokeToken($token) {
		global $site;
		if (!($array = SessionHelper::get('anti_forgery_token')) || !in_array($token, $array)) {
			return;
		}
		array_splice($array, array_search($token, $array), 1);
		$site->saveToSession('anti_forgery_token', $array);
	}
	
	protected function response($isSuccess = 1, $message = '', $anchor = null) {
		global $site;
		if ($isSuccess == null) $isSuccess = 0;
		$this->success = !!$isSuccess;
		SessionHelper::setTmpData('form_success', $isSuccess);
		SessionHelper::setTmpData('form_message', $message);
		if ($anchor) SessionHelper::setTmpData('form_anchor', $anchor);
	}
	
	public function loadMessage($anchor = null) {
		global $site;
		
		/* check for temp data */
		if ($anchor && $anchor != SessionHelper::get('tmp_form_anchor')) return(false);
		$response = SessionHelper::getTmpData('form_success');
		if (!in_array($response, array(0, 1, '0', '1', true, false), true)) return(false);
		
		$this->success = !!$response;
		$this->result = array(
			'success' => $response,
			'message' => SessionHelper::getTmpData('form_message'),
			'anchor'  => SessionHelper::getTmpData('form_anchor'),
		);
		return(true);
	}
	
	public function displayMessage($anchor = null) {
		if ((!ArrayHelper::getValue($this->result, 'message') || ArrayHelper::getValue($this->result, 'anchor', null) != $anchor) && !$this->loadMessage($anchor)) {
			return;
		}
		echo(TemplateHelper::bindParams('<div class="alert {{alertclass}}">{{message}}</div>', array(
			'alertclass' => $this->result['success'] ? 'alert-success' : 'alert-danger alert-error',
			'message' => $this->result['message'],
		), true));
	}
	
	public function isInvalid($name) {
		return(in_array($name, $this->invalid) || array_key_exists($name, $this->invalid));
	}
}
?>