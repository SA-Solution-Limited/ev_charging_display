<?php
require_once "a4plite/routing.inc.php";
require_once "a4plite/plugin.inc.php";

routing::setup ( array (
/* Web */
		'home/?' => 'HomeController',
		'login/?' => 'LoginController',
		'logout?' => 'HomeController@logout',

		'CMS/?' => 'admin/CMSController',
		
) );
