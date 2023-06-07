<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

// load helpers
require_once('includes/helpers/autoload.php');

// load plugins
if (is_file('includes/library/autoload.php')) {
	require_once('includes/library/autoload.php');
}

// load custom functions
if (is_file('includes/templates/functions.php')) {
	require_once('includes/templates/functions.php');
}

// initialize environment
$config  = require('includes/config/config.site.php');
$isSetup = preg_match('/\/setup\//', $_SERVER['REQUEST_URI']);
$isAdmin = $config['url']['adminNamespace'] && ($config['role']['isBackend'] || preg_match('/'.preg_quote("/{$config['url']['adminNamespace']}/", '/').'/', $_SERVER['REQUEST_URI']));
if (!$isSetup && $isAdmin) {
	require_once('admin/includes/class/class.adminpanel.php');
	$site = new AdminPanel();
	require_once('admin/includes/class/class.authentication.php');
	$auth = new AdminAuthentication();
} else {
	require_once('includes/class/class.site.php');
	$site = new Site();
	require_once('includes/class/class.authentication.php');
	$auth = new Authentication();
}
unset($config, $isSetup, $isAdmin);

// preflight check
$site->preflightCheck();

// load locale file
require_once('includes/locale/default.php');
if (is_file("includes/locale/{$site->locale}.php")) {
	require_once("includes/locale/{$site->locale}.php");
}

// serve request
$site->serve();
$site->redirect();
?>
