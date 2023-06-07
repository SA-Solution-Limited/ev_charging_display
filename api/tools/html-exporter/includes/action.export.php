<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

defined('SITE_ROOT') || define('SITE_ROOT', realpath(dirname(__FILE__).'/../../..'));
defined('TOOL_ROOT') || define('TOOL_ROOT', realpath(dirname(__FILE__).'/..'));
defined('EXPORT_ROOT') || define('EXPORT_ROOT', $this->docRoot.'export');
 
require_once('includes/class/class.proxy.php');

/**
 * @version 1.0
 */
class Export {
	
	protected $msg = array();
	protected $exportDir;
	protected $configFile = 'config.ini';
	protected $config;
	
	function __construct() {
		$this->configFile = TOOL_ROOT.'/'.$this->configFile;
		if (!is_file($this->configFile)) {
			echo('Configuration file is missing');
			exit;
		}
		
		if ($this->config = parse_ini_file($this->configFile, true)) {
			$this->config = (object)ArrayHelper::getValue($this->config, 'generate', array());
		} else {
			$this->config = new stdClass();
		}
		
		if ($dir = ArrayHelper::getValue($this->config, 'export_dir')) {
			$this->exportDir = EXPORT_ROOT.'/'.$dir;
		} else {
			$this->exportDir = EXPORT_ROOT;
		}
		preg_match('/\/$/', $this->exportDir) || ($this->exportDir .= '/');
		is_dir($this->exportDir) || FileSystemHelper::mkdir($this->exportDir, 0775, true);
		is_file($this->exportDir.'.htaccess') || file_put_contents($this->exportDir.'.htaccess', 'RewriteEngine Off');
		
		$this->fetchHTML();
		$this->cloneResx();
	}
	
	protected function fetchHTML() {
		global $site;
		
		if (!isset($this->config->url)) {
			$this->msg[] = 'No URLs specified.';
			return;
		}
		
		$count = 0;
		$baseUrl = preg_replace('/\/$/', '', $site->origin.$site->urlBase);
		
		$proxy = new Proxy();
		$proxy->set('method', 'get');
		
		if (ArrayHelper::getValue($this->config, 'require_auth', false)) {
			$username = ArrayHelper::getValue($this->config, 'username');
			$password = ArrayHelper::getValue($this->config, 'password');
			curl_setopt($proxy->ch, CURLOPT_USERPWD, "{$username}:{$password}");
		}
		
		foreach ($this->config->url as $path) {
			$msg = "Generating <code>{$path}</code> ... ";
			$proxy->set('url', $baseUrl.$path);
			$result = $proxy->execute();
			if ($result->statuscode == 200) {
				$count++;
				$this->outputHtml($path, $result->data);
				$msg .= '<span class="text-success">Success</span>';
			} else {
				$msg .= '<span class="text-danger">Failed</span>';
			}
			$this->msg[] = $msg;
		}
		$this->msg[] = "{$count} files generated.";
		$this->msg[] = '';
	}
	
	protected function outputHtml($url, $code) {
		global $site;

		$urlPath = parse_url($url, PHP_URL_PATH);
		
		$reserved = array('crossdomain.xml', 'changelog.txt', 'favicon.ico', 'humans.txt', 'manifest.json', 'robots.txt', 'service-worker.js', 'sitemap.xml');
		if (preg_match('/\/('.implode('|', $reserved).')/', $urlPath, $matches)) {
			@file_put_contents($this->exportDir.$matches[1], $code);
			return;
		}
		
		if ($urlPath == '/') {
			$filepath = $this->exportDir.'index';
		} else {
			$filepath = $this->exportDir.preg_replace('/\/$/', '', $urlPath);
		}
		if ($query = parse_url($url, PHP_URL_QUERY)) {
			$filepath .= '--'.str_replace('&', '--', str_replace('=', '-', $query));
		}
		$filepath .= '.html';
		
		if (!is_dir(dirname($filepath))) {
			FileSystemHelper::mkdir(dirname($filepath), 0775, true);
		}
		
		$urlRel = UrlHelper::getRelativePath($site->urlBase.preg_replace('/^\//', '', $url), $site->urlBase);
		$replace = array(
			'(href|src|action)="(\.\.\/)+"' => '$1="$2index.html"', // ../ => ../index.html
			'(href|src|action)="(.+?)\/"' => '$1="$2.html"', // ../something/ => ../something.html
			'((?:href|src|action)=")'.addcslashes($site->origin, './') => '$1',
			'((?:href|src|action)=")'.addcslashes($site->urlBase, './') => '$1'.$urlRel,
			'((?:href|src|action)=")'.addcslashes($urlRel, './')  => '$1'.preg_replace('/\.\.\/$/', '', $urlRel),
			'(var url(?:.+?)=(?:.+?))'.addcslashes($site->urlBase, './') => '$1'.$urlRel,
		);
		foreach ($replace as $from => $to) {
			$code = preg_replace("/{$from}/", $to, $code);
		}
		@file_put_contents($filepath, $code);
	}
	
	protected function cloneResx() {
		global $site;

		if (!isset($this->config->resx_dir)) return;
		if (!is_array($this->config->resx_dir)) {
			$this->config->resx_dir = explode(',', $this->config->resx_dir);
		}
		foreach ($this->config->resx_dir as $dir) {
			if (is_dir($site->docRoot.$dir)) {
				$this->msg[] = "Cloning <code>{$dir}</code> ...";
				FileSystemHelper::copy($site->docRoot.$dir, $this->exportDir.$dir, true);
			} else {
				$this->msg[] = "Skipped <code>{$dir}</code> , not a directory.";
			}
		}
		$this->msg[] = '';
	}
	
	public function getMessages() {
		return($this->msg);
	}
}
