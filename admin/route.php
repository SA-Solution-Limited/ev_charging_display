<?php
require_once "a4plite/routing.inc.php";
require_once "a4plite/plugin.inc.php";

routing::setup ( array (
/* Web */
		'home/?' => 'HomeController',
		'home/(logout)?' => 'HomeController',
		'login/?' => 'LoginController',

		'CMS/?' => 'admin/CMSController',
		'CMS/(query|edit|delete)?' => 'admin/CMSController',
		'simulator/?' => 'admin/SimulatorController',
		
		'api/media/getMediaFile' => 'api/MediaController@getMediaFile',
		'api/status/' => 'api/StatusController@getStatus',
		'api/updateStatus/' => 'api/StatusController@updateStatus',
		'api/slideshow/' => 'api/MediaController@slideshow',
) );
