<?php

require_once "db.inc.php";

ini_set('date.timezone', 'Asia/Hong_Kong');

class config
{
	// server port settings for SSL
	public static $http_port = 80;
	public static $ssl_port = 443;
	
	// turn on/off debug message 
	public static $debug = true;

	// temp directory for working files, default use session_save_path() if null
	public static $tmp_path = TMP_PATH;

	// expire time for working files
	public static $tmp_expire_time = "-1 days";
	
	public static $session_timeout = 1800;

	public static $copyright = '&copy; 2017 Fuji Xerox (Hong Kong) Limited. All rights reserved.';
}

class db extends _db
{
	// db connection settings
	//*
	public static $connect_string = "mysql:host=192.168.1.122;dbname=ev_charging_display";
	public static $user = "root";
	public static $pass = "wt2004";
	/*/
	public static $connect_string = "mysql:host=localhost:13306;dbname=spn";
	public static $user = "spn";
	public static $pass = "spn";
	//*/
}
