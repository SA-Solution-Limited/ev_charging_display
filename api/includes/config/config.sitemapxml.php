<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/* STATIC SITEMAP */
$staticSitemap = array(
	/* 
	Records can be in string or array format
	array(
		'href'       => '', // required; href without leading slash
		'changefreq' => '', // optional; possible values: always|hourly|daily|weekly|monthly|yearly|never
		'priority'   => '', // optional; values ranged from 0.0 to 1.0
	),
	*/
);

/* DYNAMIC SITEMAP */
$dynamicSitemap = array(
	/* 
	Records must be in array format
	array(
		'table'    => '',      // required; database table
		'slug'     => '',      // required; table field storing value of slug
		'prefix'   => '',      // optional; prefix
		'suffix'   => '',      // optional; suffix
		'lastmod'  => array(), // optional; table fields storing value of last modified time, the later one will be used
		'priority' => array(), // optional; table fields storing value of priority or a decimal number, ranged from 0.0 to 1.0
		'filter'   => array(), // optional; where clause of sql statement
	),
	*/
);
?>
