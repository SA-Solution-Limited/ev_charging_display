<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/* ENVIRONMANT SETUP */
return(array(
	'production' => array(
		'host' => array(),      // array of hostnames for this environment
		'base' => '/',          // base path of the site with tailing slash; e.g. if the site is placed in a sub-directory, set to /subdirectory/
		'ssl'  => false,        // whether to force ssl
		'debug' => false,       // whether to enable debug mode
		'maintenance' => false, // whether to maintenance mode; to enable, set to an array of allowed ip addresses
	),
	'staging' => array(
		'host' => array(),
		'base' => '/',
		'ssl'  => false,
		'debug' => true,
		'maintenance' => false,
	),
	'development' => array(
		'host' => array('evcharge.fx-develop.com', 'localhost:9007', '192.168.1.187:9007'),
		'base' => '/api/',
		'ssl'  => false,
		'debug' => true,
		'maintenance' => false,
	),
));
?>
