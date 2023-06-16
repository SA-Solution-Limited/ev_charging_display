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
		//'CMS/edit?' => 'admin/CMSController@edit',
		
		'api/media/getMediaList' => 'api/MediaController@getMediaList',
		'api/media/getMediaFile' => 'api/MediaController@getMediaFile',
) );
