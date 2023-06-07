<?php
require_once 'extlib/vendor/autoload.php';
class Controller
{
	public $name;

	public function pageLoad()
	{
		global $uri;
		$arr = explode("/", $uri);
		a4p::view($arr[0] . ".php");
	}
}

class _defaultController extends Controller
{
}