<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('class.db.php');
require_once('class.mime.php');
require_once('class.session.php');
require_once('includes/service/service.routing.php');

/**
 * @property string $defaultLocale
 * @property string $docRoot
 * @property boolean $doNotTrack
 * @property string $env
 * @property string $host
 * @property array $languageOptions
 * @property string $locale
 * @property boolean|array $maintenance
 * @property boolean $multilingual
 * @property string $origin
 * @property string $os
 * @property string $session
 * @property boolean $ssl
 * @property string $urlBase Base URL
 * @property string $urlCurrent URL without base URL and locale prefix
 * @property string $urlLocaleBase Base URL with locale prefix
 * @property string $urlLocaleResx URL to locale-specific resources
 * @property string $urlQuery URL without base URL and locale prefix and with query string
 * @property string $urlRequest URL without base URL
 * 
 * @property stdClass $config
 * @property stdClass $request
 * @property array $responseHeader
 */
class Site {

	public $defaultLocale;
	public $docRoot;
	public $doNotTrack;
	public $env;
	public $host;
	public $languageOptions;
	public $locale;
	public $maintenance;
	public $multilingual;
	public $origin;
	public $os;
	public $session;
	public $ssl;
	public $urlBase;
	public $urlCurrent;
	public $urlLocaleBase;
	public $urlLocaleResx;
	public $urlQuery;
	public $urlRequest;

	protected $config;
	protected $request;
	protected $responseHeader = array();

	function __construct() {
		global $resources, $srvConfig;
		
		date_default_timezone_set('Asia/Hong_Kong');
		
		$url = parse_url($_SERVER['REQUEST_URI']);
		$this->request = new stdClass();
		$this->request->path  = ArrayHelper::getValue($url, 'path');
		$this->request->query = ArrayHelper::getValue($url, 'query');

		/* load config files */
		$this->config = new stdClass;
		$this->config->env  = require('includes/config/config.env.php');
		$this->config->site = require('includes/config/config.site.php');
		$this->config->srv  = require('includes/config/config.service.php');

		/* $this->origin & $this->host */
		$this->origin = UrlHelper::getOrigin();
		$this->host   = UrlHelper::getHost();
		
		/* $this->env & $this->maintenance & $this->ssl & $this->urlBase */
		foreach ($this->config->env as $name => $env) {
			$matchHost = in_array($this->host, $env['host']);
			$matchBase = !!preg_match('/^'.preg_quote($env['base'], '/').'/', $this->request->path);
			if ($matchHost && $matchBase) {
				$this->env = $name;
				$this->urlBase = $env['base'];
				$this->ssl = ArrayHelper::getValue($env, 'ssl', true);
				$this->maintenance = ArrayHelper::getValue($env, 'maintenance', false);
				if (ArrayHelper::getValue($env, 'debug', false)) {
					error_reporting(E_ALL);
					ini_set('display_errors', 'on');
				} else {
					error_reporting(0);
					ini_set('display_errors', 'off');
				}
				break;
			}
		}
		if ($this->env == null) {
			$this->http500('500.01');
			return;
		}
		
		/* $this->os */
		if (preg_match('/win/', strtolower(PHP_OS))) {
			$this->os = 'windows';
		} else {
			$this->os = 'linux';
		}

		/* $this->docRoot */
		$this->docRoot = StringHelper::endsWith(realpath(dirname(__FILE__).'/../..'), '/');
		if ($this->os == 'windows') {
			$this->docRoot = str_replace('/', '\\', $this->docRoot);
		}
		
		/* prepare request breadcrumb */
		$this->request->pieces = explode('/', preg_replace('/^'.preg_quote($this->urlBase, '/').'/', '/', $this->request->path));

		/* $this->languageOptions */
		$this->languageOptions = $this->getSiteConfig('language', 'options');

		/* $this->defaultLocale */
		$this->defaultLocale = ArrayHelper::getValue($this->languageOptions, 0, 'en-us');

		/* $this->multilingual & $this->locale */
		if ($this->multilingual = $this->getSiteConfig('language', 'multilingual')) {
			if ($this->isValidLocale($this->request->pieces[1])) {
				$this->locale = $this->request->pieces[1];
			} else {
				$this->locale = $this->defaultLocale;
			}
		} else if (($code = HttpHelper::getGetParam('locale')) && $this->isValidLocale($code)) {
			$this->locale = $code;
		} else {
			$this->locale = $this->defaultLocale;
		}
		
		/* $this->urlRequest */
		$this->urlRequest = implode('/', array_slice($this->request->pieces, 1));

		/* $this->urlLocaleBase */
		$this->urlLocaleBase = $this->urlBase.($this->multilingual ? "{$this->locale}/" : '');
		
		/* $this->urlLocaleResx */
		$this->urlLocaleResx = $this->multilingual ? $this->urlLocaleBase : $this->urlBase.$this->locale.'/';
		
		/* $this->urlCurrent */
		$this->urlCurrent = implode('/', array_slice($this->request->pieces, $this->multilingual ? 2 : 1));
		
		/* $this->urlQuery */
		$this->urlQuery = $this->urlCurrent.($this->request->query ? StringHelper::startsWith($this->request->query, '?') : '');
		
		// $this->doNotTrack
		if ($this->getSiteConfig('privacy', 'doNotTrack')) {
			$this->doNotTrack = HttpHelper::getHeader('DNT', 0) == 1;
		} else {
			$this->doNotTrack = false;
		}
		
		/* $this->responseHeader */
		if (!($expires = $this->getSiteConfig('caching', 'expires'))) {
			$expires = date('Y-m-d H:i:s', strtotime('+2 days'));
		}
		$localTime = strtotime($expires);
		$this->responseHeader['Expires'] = gmdate('D, d M Y H:i:s \G\M\T', $localTime);
		if ($localTime > time()) {
			$this->responseHeader['Cache-Control'] = 'no-transform, public, max-age='.($localTime - time());
		} else {
			$this->responseHeader['Cache-Control'] = 'no-cache';
		}
		
		/* $this->config->srv */
		$this->config->srv = array_replace_recursive(ArrayHelper::getValue($this->config->srv, '*', array()), ArrayHelper::getValue($this->config->srv, $this->env, array()));
		
		/* DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PREFIX, DB_AESKEY */
		$database = require('includes/config/config.mysql.php');
		if (!empty($database[$this->env]['host']) && !empty($database[$this->env]['user']) && !empty($database[$this->env]['name'])) {
			define('DB_HOST', $database[$this->env]['host']);
			define('DB_USER', $database[$this->env]['user']);
			define('DB_PASS', $database[$this->env]['pass']);
			define('DB_NAME', $database[$this->env]['name']);
			define('DB_PREFIX', $database[$this->env]['prefix']);
			define('DB_AESKEY', hash('sha512', $database['aes_key']));
			define('DB_CHARSET', $database['charset']);
			define('DB_TIMEZONE', $database['timezone']);
			try {
				if (Db::connect() === false) {
					$this->http500('500.02');	
				}
			} catch (Exception $e) {
				$this->http500('500.02');
			}
		}
		
		/* initiate session */
		$this->session = md5($this->urlBase);
		if (interface_exists('SessionHandlerInterface') && Db::tableExists('session')) {
			$handler = new Session();
			session_set_save_handler($handler);
		}
		session_start();

		/* handle data of different request methods */
		if (HttpHelper::isPost()) {
			$this->parseRequestBody('post');
		} else if (HttpHelper::isPut()) {
			$this->parseRequestBody('put');
		} else if (HttpHelper::isDelete()) {
			$this->parseRequestBody('delete');
		}
	}

	/**
	 * Get site configuration.
	 * @param string $group
	 * @param string|null $key
	 * @return mixed|null
	 */
	public function getSiteConfig($group, $key = null) {
		if (!isset($this->config->site[$group])) {
			return(null);
		}
		return($key ? ArrayHelper::getValue($this->config->site[$group], $key) : $this->config->site[$group]);
	}

	/**
	 * Get service configuration.
	 * @param string $provider
	 * @param string|null $key
	 * @return mixed|null
	 */
	public function getSrvConfig($provider, $key = null) {
		if (!isset($this->config->srv[$provider])) {
			return(null);
		}
		return($key ? ArrayHelper::getValue($this->config->srv[$provider], $key) : $this->config->srv[$provider]);
	}
	
	/**
	 * Pre-flight checking before actually serve the requested resource.
	 */
	public function preflightCheck() {
		/* ssl check */
		if ($this->ssl && strtolower(HttpHelper::getServerParam('HTTPS', 'off')) != 'on' && HttpHelper::getServerParam('HTTP_X_FORWARDED_PROTO') != 'https') {
			HttpHelper::redirect("https://{$this->host}{$_SERVER['REQUEST_URI']}", 301);
		}

		/* url check: ensure path has a tailing slash */
		if (!preg_match('/\/$/', $this->request->path) && !preg_match('/\.[a-zA-Z0-9]+$/', $this->request->path)) {
			HttpHelper::redirect(StringHelper::endsWith($this->request->path, '/').($this->request->query ? StringHelper::startsWith($this->request->query, '?') : ''), 301);
		}

		/* home namespace check */
		if ($this->request->path == $this->urlBase && ($namespace = $this->getSiteConfig('url', 'homeNamespace'))) {
			if ($this->multilingual) {
				$location = "./{$this->defaultLocale}/";
			} else {
				$location = "./{$namespace}/";
			}
			HttpHelper::redirect($location, 301);
		}
	}

	/**
	 * Serve the requested resource.
	 */
	public function serve() {
		$request = array_slice($this->request->pieces, 1);
		
		$this->localization();
		
		/* handle root files request */
		$files = array('crossdomain.xml', 'changelog.md', 'favicon.ico', 'humans.txt', 'manifest.json', 'robots.txt', 'sitemap.xml');
		$files = array_merge($files, $this->getSiteConfig('url', 'reservedRootFiles'));
		$files = implode('|', array_map(function($f) {
			return(preg_quote($f, '/'));
		}, $files));
		if (preg_match("/^({$files})$/", $this->urlRequest, $matches)) {
			$file = rawurldecode("resx/{$matches[1]}");
			if (is_file($file) && ArrayHelper::getValue($this->getSiteConfig('url'), $file, true)) {
				$this->loadResource($file, true);
			} else {
				$this->http404();
			}
			return;
		}
		
		/* handle asset request */
		$directories = array('images', 'includes', 'library', '_?modules', 'setup', 'tools', 'uploads');
		$directories = array_merge($directories, $this->getSiteConfig('url', 'reservedDirectories'));
		$directories = implode('|', $directories);
		if (preg_match("/^({$directories})\//", $this->urlRequest, $matches) && !preg_match('/^api\//', $this->urlRequest)) {
			$file = rawurldecode($this->urlRequest);
			if (is_dir($file)) {
				$file .= 'index.php';
			}
			if (is_file($file)) {
				$forceParse = in_array($matches[1], array('includes', 'setup', 'tools'));
				$this->loadResource($file, !!$forceParse);
			} else {
				$this->http404();
			}
			return;
		}
		
		/* handle maintenance mode */
		if ($this->maintenance === true || (is_array($this->maintenance) && !in_array(HttpHelper::getServerParam('HTTP_X_FORWARDED_FOR', $_SERVER['REMOTE_ADDR']), $this->maintenance))) {
			HttpHelper::status(503);
			$this->config->responseHeader = array(
				'Retry-After' => 900,
			);
			$this->outputHeader();
			if (is_file('includes/templates/page.maintenance.php')) {
				$this->loadRequest('includes/templates/page.maintenance.php');
			} else {
				echo('Maintenance in progress.');
			}
			exit;
		}
		
		/* handle api request */
		if (preg_match('/^api\//', $this->urlRequest)) {
			if ($languages = HttpHelper::getHeader('Accept-Language')) {
				/* parse header */
				$languages = array_filter(array_map(function($l) {
					if (preg_match('/^(?<code>[a-z]{2}(?:-[a-z]{2})?)(?:;q=(?<weight>[0-9.]+))?$/', $l, $matches)) {
						return(array(
							'code' => $matches['code'],
							'weight' => ArrayHelper::getValue($matches, 'weight', 1),
						));
					}
					return(false);
				}, explode(',', strtolower(str_replace(' ', '', $languages)))));
				usort($languages, function($a, $b) {
					if ($a['weight'] == $b['weight']) return(0);
					return ($a['weight'] > $b['weight']) ? -1 : 1;
				});

				/* override locale settings */
				array_walk($languages, function($l) {
					if ($this->isValidLocale($l['code'])) {
						$this->locale = $l['code'];
						$this->urlLocaleBase = $this->urlBase.($this->multilingual ? $l['code'].'/' : '');
						$this->urlLocaleResx = $this->multilingual ? $this->urlLocaleBase : $this->urlBase.$l['code'].'/';
						return(false);
					}
				});
			}
			$path = preg_replace('/\/$/', '', $this->urlRequest);
			if (!$this->loadDefaultRoute($path)) {
				$this->http404();
			}
			return;
		}
		
		/* handle html page request */
		$pieces = array_slice($this->request->pieces, 1, -1);
		if ($this->multilingual) {
			if ($this->request->path == $this->urlBase) {
				$this->redirect();
			} else if ($this->request->path == $this->urlLocaleBase && $this->getSiteConfig('url', 'homeNamespace') == '') {
				array_push($pieces, 'home');
			}
		} else {
			array_unshift($pieces, $this->defaultLocale);
			if ($this->request->path == $this->urlBase && $this->getSiteConfig('url', 'homeNamespace') == '') {
				array_push($pieces, 'home');
			}
		}
		
		/* load request in localized language */
		$this->loadDefaultRoute(implode('/', $pieces));
		
		/* load request in master language */
		if ($this->multilingual && $this->getSiteConfig('language', 'masterLanguage') && $this->locale != $this->defaultLocale) {
			$this->localization(null, $this->defaultLocale);
			array_splice($pieces, 0, 1, $this->defaultLocale);
			$this->loadDefaultRoute(implode('/', $pieces));
		}
		
		$this->http404();
	}

	protected function isValidLocale($code) {
		return(in_array($code, $this->languageOptions));
	}
	
	protected function parseRequestBody($method) {
		if (in_array($method, array('post', 'put', 'delete'))) {
			$method = '_'.strtoupper($method);
		} else {
			return;
		}
		global $$method;
		$input = file_get_contents('php://input');
		if (!($array = json_decode($input, true))) {
			parse_str($input, $array);
		}
		$$method = $array;
		$_REQUEST = array_replace_recursive($_REQUEST, $$method);
	}

	protected function outputHeader() {
		ksort($this->config->responseHeader);
		foreach ($this->config->responseHeader as $key => $value) {
			header("{$key}: $value");
		}
	}

	protected function localization($module = null, $locale = null) {
		$path = 'includes/locale/'.($module ? $module.'/' : '').($locale ? $locale : $this->locale).'.ini';
		if (!is_file($path) || !($translation = parse_ini_file($path, false, INI_SCANNER_RAW))) {
			return;
		}
		foreach ($translation as $key => $val) {
			defined($key) || define($key, $val);
		}
	}
	
	protected function loadDefaultRoute($path) {
		/* static routing */
		if ($route = $this->loadStaticRoute($path)) {
			$this->loadRequest($route['route'], $route['params']);
			return(true);
		}
		
		/* dynamic routing (allow to pass parameters) */
		if ($route = $this->loadDynamicRoute($path)) {
			$this->loadRequest($route['route'], $route['params']);
			return(true);
		}
		
		/* database routing */
		if ($route = $this->loadDatabaseRoute($path)) {
			$this->loadRequest($route['route'], $route['params']);
			return(true);
		}
		
		return(false);
	}
	
	protected function loadDatabaseRoute($path) {	
		if (!Db::connect() || !Db::query('SELECT 1 FROM [table:routing]')) {
			return(false);
		}
		$pieces = array_slice(explode('/', $path), 1);
		$slug = implode('/', $pieces);
		if (!($route = RoutingService::getBySlug($slug, $this->locale, array('enabledOnly' => true)))) {
			if (!$this->getSiteConfig('language', 'masterLanguage') || $this->locale == $this->defaultLocale) {
				return(false);
			}
			if (!($route = RoutingService::getBySlug($slug, $this->defaultLocale, array('enabledOnly' => true)))) {
				return(false);
			}
		}
		if ($route->rouType == 'REDIRECT') {
			RoutingService::recordClick($route->rouId);
			HttpHelper::redirect($route->rouTarget, 301);
			return;
		}
		$file = "{$route->rouLocale}/{$route->rouTarget}";
		if (is_file($file)) {
			return(array(
				'route' => $file,
				'params' => $pieces,
			));
		}
		return(false);
	}
	
	protected function loadDynamicRoute($path) {
		$pieces = explode('/', $path);
		for ($i = count($pieces); $i > 2; $i--) {
			$path = implode('/', array_slice($pieces, 0, $i - 1));
			$guess = array(
				// locale/section.php
				$path.'.php',
				// locale/section/index.php
				$path.'/index.php',
			);
			foreach ($guess as $file) {
				if (is_file($file)) {
					return(array(
						'route' => $file,
						'params' => array_slice($pieces, $i - 1),
					));
				}
			}
		}
		return(false);
	}
	
	protected function loadStaticRoute($path) {
		$guess = array(
			// locale/section.php
			$path.'.php',
			// locale/section/index.php
			$path.'/index.php',
			// locale/section/home.php
			$path.'/home.php',
			// locale/section/home/index.php
			$path.'/home/index.php',
		);
		foreach ($guess as $file) {
			if (is_file($file)) {
				return(array(
					'route' => $file,
					'params' => null,
				));
			}
		}
		return(false);
	}
	
	protected function loadResource($file, $forceParse = false) {
		global $locale;

		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		$this->config->responseHeader['Access-Control-Allow-Origin'] = $this->origin;
		$this->config->responseHeader['Content-Type'] = MIME::get($ext);
		$this->config->responseHeader['Content-Length'] = filesize($file);

		if ($forceParse || (preg_match('/^css|js|html|php|xml$/', $ext) && isset($_GET['parse']) && !isset($_GET['noparse']))) {
			$this->outputHeader();
			require($file);
			exit;
		}

		$mtime = $this->config->responseHeader['Last-Modified'] = gmdate('D, d M Y H:i:s \G\M\T', filemtime($file));
		$etag = $this->config->responseHeader['Etag'] = crypt(md5_file($file), $mtime);;
		if (HttpHelper::getHeader('If-None-Match') == $etag && HttpHelper::getHeader('If-Modified-Since') == $mtime) {
			HttpHelper::status(304);
			$this->outputHeader();
		} else {
			readfile($file);
		}
		exit;
	}
	
	protected function loadRequest($route, $params = null) {
		global $auth, $locale, $resources;
		$this->config->responseHeader['Access-Control-Allow-Origin'] = $this->origin;
		$this->config->responseHeader['X-Frame-Options'] = 'SAMEORIGIN';
		$this->outputHeader();
		require($route);
		exit;
	}

	public function http404() {
		global $auth, $locale, $resources;
		HttpHelper::status(404);
		if ($this->config->site['errorHandling']['custom404']) {
			require('includes/templates/page.404.php');
		}
		exit;
	}
	
	public function http500($errorCode = null) {
		global $auth, $locale, $resources;
		HttpHelper::status(500);
		if ($this->config->site['errorHandling']['custom500']) {
			require('includes/templates/page.500.php');
		}
		exit;
	}
	
	public function redirect() {
		$location = $this->urlBase;
		if ($this->multilingual) {
			$location .= $this->defaultLocale.'/';
		}
		if ($namespace = $this->getSiteConfig('url', 'homeNamespace')) {
			$location .= $namespace.'/';
		}
		if ($location != $this->urlBase) {
			HttpHelper::redirect($location, 301);
		}
		exit;
	}
}

function _r($key, $transform = null, $replace = array()) {
	$string = defined($key) ? constant($key) : $key;
	foreach ($replace as $key => $value) {
		$string = preg_replace('/_'.strtoupper($key).'_/', $value, $string);
	}
	switch ($transform) {
		case 'l':
			return(strtolower($string));
		case 'u':
			return(strtoupper($string));
		default:
			return($string);
	}
}
function _e($key, $transform = null, $replace = array()) {
	echo(_r($key, $transform, $replace));
}
function loadTemplate($path, array $variables = array()) {
	global $auth, $locale, $resources, $site, $srvConfig;
	foreach ($variables as $name => $value) {
		$$name = $value;
	}
	$guesses = array(
		$path,
		$site->locale.'/'.$path,
		$site->defaultLocale.'/'.$path,
	);
	foreach ($guesses as $src) {
		if (!is_file($src)) continue;
		require($src);
		break;
	}
}
?>
