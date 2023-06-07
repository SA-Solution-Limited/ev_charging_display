<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * @version 1.0.5.1
 */
class Proxy {
	
	protected $defaults = array(
		'url'    => null,
		'method' => 'post', // (get|post|put|delete|patch)
		'ajax'   => false,
		'header' => array(),
		'data'   => array(),
	);
	protected $opts = array();
	protected $ch;
	
	function __construct($url = null) {
		$this->ch = curl_init();
		$this->opts = array_replace($this->opts, $this->defaults);
		$this->opts['url'] = $url;
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Technetium PHP Framework Proxy');
	}
	
	public function set($name, $value) {
		$this->opts[$name] = $value;
	}
	
	public function setopt($key, $value) {
		curl_setopt($this->ch, $key, $value);
	}
	
	public function setAuth($username, $password) {
		$this->setopt(CURLOPT_USERPWD, "$username:$password");
	}
	
	public function setCookie($cookie) {
		$this->setopt(CURLOPT_COOKIE, $cookie);
	}
	
	public function setReferrer($referrer) {
		$this->setopt(CURLOPT_REFERER, $referrer);
	}
	
	public function setUserAgent($userAgent) {
		$this->setopt(CURLOPT_USERAGENT, $userAgent);
	}
	
	public function execute($stdClass = true) {
		// final configuration
		switch (strtolower($this->opts['method'])) {
			case 'post':
				$opts = array(
					CURLOPT_URL        => $this->opts['url'],
					CURLOPT_POST       => true,
					CURLOPT_POSTFIELDS => is_array($this->opts['data']) ? http_build_query($this->opts['data']) : $this->opts['data'],
				);
				break;
			case 'put':
			case 'patch':
			case 'delete':
				$opts = array(
					CURLOPT_URL           => $this->opts['url'],
					CURLOPT_CUSTOMREQUEST => strtoupper($this->opts['method']),
					CURLOPT_POSTFIELDS    => is_array($this->opts['data']) ? http_build_query($this->opts['data']) : $this->opts['data'],
				);
				break;
			case 'get':
			default:
				$opts = array(
					CURLOPT_URL     => $this->opts['url'].(count($this->opts['data']) ? '?'.http_build_query($this->opts['data']) : ''),
					CURLOPT_HTTPGET => true,
				);
		}
		if ($this->opts['ajax']) {
			$this->opts['header'][] = 'X-Requested-With: XMLHttpRequest';
		}
		if (count($this->opts['header']) > 0) {
			$opts[CURLOPT_HTTPHEADER] = $this->opts['header'];
		}
		curl_setopt_array($this->ch, $opts);
		
		// execute request
		$result = curl_exec($this->ch);
		if ($result === false) {
			$response = array(
				'success' => false,
				'statuscode' => 500,
				'message' => curl_error($this->ch),
			);
		} else {
			$statuscode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
			$response = array(
				'success' => true,
				'statuscode' => $statuscode,
			);
			if (in_array($statuscode, array(301, 302, 303))) {
				$response['redirect'] = curl_getinfo($this->ch, CURLINFO_REDIRECT_URL);
			} else {
				$response['data'] = $result;
			}
		}
		
		return($stdClass ? (object)$response : $response);
	}
	
	public function reset($url = null) {
		$url = $url ? $url : $this->opts['url'];
		curl_close($this->ch);
		$this->__construct($url);
	}
}
?>
