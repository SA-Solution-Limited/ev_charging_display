<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/* DATABASE CONFIGURATION */
/* leave empty if database is not required */
return(array(
	'production' => array(
		'host' => '',   // hostname/ip of mysql server
		'user' => '',   // username
		'pass' => '',   // password
		'name' => '',   // default database
		'prefix' => '', // table prefix, useful when different prefixes are used in different environments
	),
	'staging' => array(
		'host' => '',
		'user' => '',
		'pass' => '',
		'name' => '',
		'prefix' => '',
	),
	'development' => array(
		'host' => '',
		'user' => '',
		'pass' => '',
		'name' => '',
		'prefix' => '',
	),
	'timezone' => 'Asia/Hong_Kong', // default timezone, possible formats are +8:00 (preferred) and Asia/Hong_Kong
	'charset' => 'utf8',            // default charset
	'aes_key' => 'bAz65TqXS01aT0qO1ZiU2Z350V66pQzx', // 32 bytes string
));
?>
