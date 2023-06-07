<?php
//
// routing.inc - Routing
//
require_once "common.inc.php";
class routing {
	private static $routes = array ();
	
	public static function add($_routes) {
		self::$routes = array_merge ( self::$routes, $_routes );
	}
	
	private static function loginRequired($controllername, $action) {
		$_class = new ReflectionClass ( basename ( $controllername ) );
		$comment = $_class->getDocComment ();
		$action_auth = false;
		
		$pos = strpos ( $comment, "@Authorize" );
		if ($pos != false && $pos >= 0) {
			$action_auth = true;
		}
		
		$_method = $_class->getMethod ( $action );
		$comment = $_method->getDocComment ();
		$pos = strpos ( $comment, "@AllowAnonymous" );
		if ($pos != false && $pos >= 0) {
			$action_auth = false;
		}
		
		return $action_auth;
	}
	
	private static function requireRights($controllername, $action = null) {
		$_class = new ReflectionClass ( basename ( $controllername ) );
		$comment = $_class->getDocComment ();
		$arr = array ();
		
		$pos = strpos ( $comment, "@AccessRight" );
		if ($pos != false && $pos >= 0) {
			$str = substr ( $comment, $pos + strlen ( "@AccessRight[" ) );
			$str = substr ( $str, 0, strpos ( $str, "]" ) );
			$arr = explode ( ",", $str );
		}
		
		if (isset ( $action )) {
			$_method = $_class->getMethod ( $action );
			$comment = $_method->getDocComment ();
			$pos = strpos ( $comment, "@AccessRight" );
			if ($pos != false && $pos >= 0) {
				$str = substr ( $comment, $pos + strlen ( "@AccessRight[" ) );
				$str = substr ( $str, 0, strpos ( $str, "]" ) );
				$arr2 = explode ( ",", $str );
				foreach ( $arr2 as $right ) {
					if (! in_array ( $right, $arr )) {
						$arr [] = $right;
					}
				}
			}
		}
		
		return $arr;
	}
	
	public static function setup($_routes) {
		self::$routes = array_merge ( self::$routes, $_routes );
		
		$prefix = dirname ( $_SERVER ["PHP_SELF"] );
		
		global $rerender;
		if ($rerender == true)
			$prefix = dirname ( $prefix );
		
		if ($prefix == "." || $prefix == "\\" || $prefix == "/")
			$prefix = "";
		
		global $uri;
		$uri_parts = explode ( "?", $_SERVER ["REQUEST_URI"], 2 );
		$uri = substr ( $uri_parts [0], strlen ( $prefix ) + 1 );
		$match = false;
		foreach ( self::$routes as $route => $classpath ) {
			if (preg_match ( '/^' . addcslashes ( $route, '/' ) . '(\?.*)*$/', $uri, $parts )) {
				if (! endsWith ( $classpath, '.php' )) {
					$param = explode ( "@", $classpath );
					$classname = $param [0];
					if (isset ( $param [1] )) {
						global $ajaxcall;
						$ajaxcall = true;
						require_once "framework.inc.php";
						global $controller;
						$controller = a4p::controller ( $classname );
						$method = $param [1];
						try {
							$action_auth = self::loginRequired ( $classname, $method );
							if ($action_auth) {
								$controller->requireLogin ();
							}
							
							$require_rights = self::requireRights ( $classname, $method );
							if (count ( $require_rights ) > 0) {
								$controller->requireRights ( $require_rights );
							}
							
							echo $controller->{$method} ( $parts );
							db::commit ();
						} catch ( Exception $e ) {
							echo $e;
						}
					} else {
						global $routed;
						$routed = true;
						require_once "framework.inc.php";
						global $controller;
						$controller = a4p::controller ( $classname );
						try {
							$require_rights = self::requireRights ( $classname );
							if (count ( $require_rights ) > 0) {
								$controller->requireRights ( $require_rights );
							}
							$controller->pageLoad ( $parts );
							db::commit ();
						} catch ( Exception $e ) {
							echo $e;
						}
					}
				} else {
					require_once $classpath;
				}
				$match = true;
				break;
			}
		}
		
		if (! $match) {
			global $rerender;
			if (isset ( $rerender ) && $rerender == true)
				require $_SERVER ["DOCUMENT_ROOT"] . $_SERVER ["REQUEST_URI"];
			else {
				if (endsWith ( $classpath, '.php' )) {
					require_once $classpath;
				} else {
					$action = basename ( $uri );
					$controller_path = rtrim ( preg_replace ( '/' . $action . '$/', "", $uri ), '/' );
					if (file_exists ( $_SERVER ["DOCUMENT_ROOT"] . "/class/controller/${controller_path}Controller.class.php" )) {
						
						global $routed;
						$routed = true;
						require_once "framework.inc.php";
						global $controller;
						
						$controller = a4p::controller ( $controller_path . 'Controller' );
						$action_auth = self::loginRequired ( $controller_path . 'Controller', $action );
						
						try {
							if ($action_auth) {
								$controller->requireLogin ();
							}
							
							echo $controller->{$action} ();
							db::commit ();
						} catch ( Exception $e ) {
							echo $e;
						}
					} else {
						header ( "HTTP/1.0 404 Not Found" );
						echo "<h1>Not Found</h1>";
					}
				}
			}
		}
	}
}
