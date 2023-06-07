<?php 
//
// framework.inc - A simple PHP AJAX Toolkit
//
require_once "common.inc.php";
require_once "config.inc.php";
require_once "session.inc.php";
require_once "env.inc.php";
require_once "form.inc.php";
require_once "push.inc.php";
require_once "security.inc.php";
require_once "controller.inc.php";
require_once "model.inc.php";
require_once "template.inc.php";
require_once "language.inc.php";
require_once "db.inc.php";
require_once "orm.inc.php";
require_once "entity.inc.php";
require_once "arr.inc.php";
require_once "debug.inc.php";
require_once dirname(__FILE__).'/../class/lib/log4php/Logger.php';
require_once dirname(__FILE__).'/../class/log4php.config.php';

// optional plugin
include_once "uploadify.inc.php";

$configurator = new SPNConfigurator();
Logger::configure($configurator->configuration, $configurator);

session_cache_limiter("nocache");
session_start();

// validate session
if (isset($_SESSION['EXPIRES']) && time() > $_SESSION['EXPIRES']) {
	// session invalid or expired
	session_unset();
	session_destroy();
	session_start();
}

// set expiration time
$_SESSION['EXPIRES'] = time() + config::$session_timeout;

if (config::$tmp_path == null)
	config::$tmp_path = session_save_path();

a4p_session::$sid = session_id();
a4p_session::$global = $_SESSION;
a4p_session::init();

if (!isset($_SESSION["a4p._key"]))
	$_SESSION["a4p._key"] = a4p_sec::randomString(32);

a4p_sec::$key = $_SESSION["a4p._key"];
a4p_sec::$auth = isset($_SESSION["a4p._auth"]) && ($_SESSION["a4p._auth"] == true);

session_write_close();
$_SESSION = array();

class a4p
{
	private static $requestscopestack = array();
	private static $viewscopestack = array();

	public static function controller($classpath)
	{
		$classname = basename($classpath);
		if (!class_exists($classpath))
			require_once "controller/$classpath.class.php";

		if (!isset(a4p::$requestscopestack["a4p." . $classname])) {
			$instance = new $classname();
			if (property_exists($instance, 'name'))
				$instance->name = $classpath;
			a4p::$requestscopestack["a4p." . $classname] = $instance;
		} else
			$instance = a4p::$requestscopestack["a4p." . $classname];
		
		return $instance;
	}
	
	public static $viewvariables = array();
	
	public static function assign($name, &$value) {
		a4p::$viewvariables[$name] = &$value;
	}

	public static function view($viewpath, $env = array())
	{
		if (a4p::isAjaxCall())
			ob_start();
		
		global $controller;
		foreach (a4p::$viewvariables as $name => &$value)
			$$name = &$value;
		require SITE_ROOT . "/view/" . $viewpath;
		
		if (a4p::isAjaxCall()) {
			$buffer = ob_get_clean();
			return a4p::postProcess($buffer);
		}
	}

	public static function model($classpath, $defaults = null)
	{
		$classname = basename($classpath);
		if (!class_exists($classpath))
			require_once "model/$classpath.class.php";

		$class = new ReflectionClass($classname);
		$comment = $class->getDocComment();
		
		$scope = "request";
		if (strpos($comment, "@viewscope") !== false)
			$scope = "view";
		if (strpos($comment, "@sessionscope") !== false)
			$scope = "session";

		if ($scope == "request") {
			if (!isset(a4p::$requestscopestack["a4p." . $classname])) {
				if ($defaults != null)
					$instance = new $classname($defaults);
				else
					$instance = new $classname();
				a4p::$requestscopestack["a4p." . $classname] = $instance;
			} else
				$instance = a4p::$requestscopestack["a4p." . $classname];
		}

		if ($scope == "view") {
			if (!a4p::isPostBack() && !a4p::isAjaxCall() && !in_array($classname, a4p::$viewscopestack)) {
				if ($defaults != null)
					$instance = new $classname($defaults);
				else
					$instance = new $classname();
				a4p_session::set("a4p." . $classname, $instance);
				a4p::$viewscopestack[] = $classname;
			}
		}

		if ($scope == "view" || $scope == "session") {
			if (!a4p_session::exists("a4p." . $classname))	{
				if ($defaults != null)
					$instance = new $classname($defaults);
				else
					$instance = new $classname();
				a4p_session::set("a4p." . $classname, $instance);
			} else
				$instance = a4p_session::get("a4p." . $classname);
		}

		return $instance;
	}
	
	public static function Reset($classpath)
	{
		$classname = basename($classpath);
		
		if (isset(a4p::$requestscopestack["a4p." . $classname]))
			unset(a4p::$requestscopestack["a4p." . $classname]);

		if (isset(a4p::$viewscopestack["a4p." . $classname]))
			unset(a4p::$viewscopestack["a4p." . $classname]);
		
		a4p_session::remove("a4p." . $classname);
	}

	public static function loadScript()
	{
		$prefix = "/" . str_replace("\\", "/", dirname(substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]) + 1)));
		
		// $prefix = realpath($_SERVER["DOCUMENT_ROOT"]);
		
		$state = array();

		global $routed;
		if (isset($routed) && $routed == true)
			$state['phpself'] = $_SERVER["REQUEST_URI"];
		else
			$state['phpself'] = $_SERVER["PHP_SELF"];
		
		$state['phpquery'] = $_SERVER["QUERY_STRING"];

		global $controller;
		if (isset($controller->name))
			$state['controllername'] = $controller->name;
		else
			$state['controllername'] = "";

		$encrypted = a4p_sec::encrypt(json_encode($state));

		print <<< END
<script type="text/javascript" src="$prefix/framework.js"></script>
<script type="text/javascript">
a4p.init('$prefix', '$encrypted');
</script>
END;
	}
	
	public static function setAuth($param)
	{
		$session_started = isset($_SESSION["a4p._map"]);

		if (!$session_started)
			session_start();

		if ($param == true)
			$_SESSION["a4p._auth"] = a4p_sec::$auth = true;
		else {
			a4p_sec::$auth = false;
			unset($_SESSION["a4p._auth"]);
		}

		if (!$session_started) {
			session_write_close();
			$_SESSION = array();
		}
	}
	
	public static function isLoggedIn()
	{
		return a4p_sec::$auth == true;
	}

	public static function isPostBack()
	{
		global $rerender;
		if (isset($rerender) && $rerender == true)
			return true;
		return false;
	}

	public static function isAjaxCall()
	{
		global $ajaxcall;
		if (isset($ajaxcall) && $ajaxcall == true)
			return true;
		return false;
	}

	public static function &postProcess(&$buffer)
	{
		$buffer = &language::process($buffer);
		return a4p::finalize($buffer);
	}

	public static function &finalize(&$buffer)
	{
		a4p_session::flush();
		return $buffer;
	}

	public static function requireSSL($ssl = true)
	{
		$isHTTPS = isset($_SERVER["HTTPS"]) ? ($_SERVER["HTTPS"] == "on") : false;
		$host = isset($_SERVER["HTTP_X_FORWARDED_HOST"]) ? $_SERVER["HTTP_X_FORWARDED_HOST"] : $_SERVER["SERVER_NAME"];

		if($ssl == true && $isHTTPS == false) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: https://" . $host . ":" . config::$ssl_port . $_SERVER["REQUEST_URI"]);
			exit();
		}

		if($ssl == false && $isHTTPS == true) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: http://" . $host . ":" . config::$http_port . $_SERVER["REQUEST_URI"]);
			exit();
		}
	}

	public static function requireAuth()
	{
		if (!a4p::isLoggedIn())
		{
			header("Location: index.php");
			exit();
		}
	}

	public static function callBack($func, $param = null, $timeout = 0)
	{
		$json = json_encode($param);
		return push::execJS("$func($json);", $timeout);
	}

	public static $redirect = null;

	public static function redirect($url)
	{
		return a4p::$redirect = $url;
	}

	public static $javascript = null;

	public static function javascript($js)
	{
		return a4p::$javascript = $js;
	}

	public static function saveState($obj)
	{
		return base64_encode(gzcompress(serialize($obj), 9));
	}
	
	public static function loadState($state)
	{
		return unserialize(gzuncompress(base64_decode($state)));
	}
	
	public static function http404()
	{
		header("HTTP/1.0 404 Not Found");
		echo "<h1>Not Found</h1>";
		exit();
	}

	public static $serial = "";
}

if (isset($serial))
	a4p::$serial = $serial;

if (!a4p::isAjaxCall())
	ob_start("a4p::postProcess");
else
	ob_start("a4p::finalize");

// load global
include_once "global.inc.php";
