<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/* SITE SETUP */
return(array(
	'role' => array(
		// set to true to allow authentication without valid credential, useful for prototyping
		'isPrototype' => false,
		// set to true if no front-end view is available
		'isBackend' => false,
	),
	'url' => array(
		// namespace for home, if set to 'home', accessing http://www.example.com/ will be redirected to http://www.example.com/home/
		'homeNamespace' => '',
		// namespace for admin panel, set to null if no admin panel is available
		'adminNamespace' => null,
		// list of directories needed to be accessed directly (e.g. images, library), accepts regular expression
		'reservedDirectories' => array(),
		// list of files supposed to be placed at root directory
		'reservedRootFiles' => array(),
		// whether to serve robots.txt
		'robots.txt' => true,
		// whether or not to serve sitemap.xml
		'sitemap.xml' => true,
	),
	'language' => array(
		// available languages
		'options' => array('zh-hk'),
		// whether to support multilingual
		'multilingual' => false,
		// whether to load template of default language when localized one is not available
		'masterLanguage' => true,
	),
	'privacy' => array(
		// whether to respsect user's tracking preference
		'doNotTrack' => true,
	),
	'caching' => array(
		// period to expire a resource, affecting "expires" and "cache-control" headers
		'expires' => 'now',
	),
	'errorHandling' => array(
		// whether to show customize 404 view (includes/templates/page.404.php)
		'custom404' => true,
		// whether to show customize 500 view (includes/templates/page.500.php)
		'custom500' => true,
	),
	'pwa' => array(
		// whether to enable progressive web app meta tags
		'enable' => false,
		// theme color
		'theme' => '#3367D6',
		// background color
		'background' => '#FFFFFF',
	),
	'opengraph' => array(
		// whether to enable open graph, facebook and twitter meta tags
		'enable' => false,
	),
));
?>
