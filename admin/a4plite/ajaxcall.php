<?php
//
// ajaxcall.php - Handle PHP AJAX requests
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

if (!isset($_POST["state"]) || !isset($_POST["method"]) || !isset($_POST["param"]) || !isset($_POST["form"]) || !isset($_POST["call"]))
	exit();
$encrypted = $_POST["state"];
$method = $_POST["method"];
$param = json_decode($_POST["param"]);
$form = $_POST["form"];
$call = $_POST["call"];
$serial = $_POST["serial"];
$rerender = true;
$ajaxcall = true;

require_once "framework.inc.php";
require_once "nocache.inc.php";
require_once "routing.inc.php";
require_once "plugin.inc.php";

include_once "../plugin/plugin.php";

$state = json_decode(a4p_sec::decrypt($encrypted), true);
$controller = $state['controllername'];
$query =  $state['phpquery'];

include_once "controller/$controller.class.php";

$_class = new ReflectionClass(basename($controller));
$comment = $_class->getDocComment();
		
if (strpos($comment, "@ajaxenable") == false) {
	echo "Class not ajax enable";
	exit();
}

$_method = $_class->getMethod($method);
$comment = $_method->getDocComment();

if (strpos($comment, "@ajaxcall") == false) {
	echo "Not a ajax method";
	exit();
}

a4p::$serial = $serial;

$class = a4p::controller(basename($controller));

$polling = false;
if (isset($_POST["poll_id"])) {
	$poll_id = $_POST["poll_id"];
	if ($poll_id != '') {
		push::create($poll_id);
		$polling = true;
	}
}

try {
	$_POST = array();
	parse_str($form, $_POST);
	$_GET = array();
	parse_str($query, $_GET);
	$result = $class->{$method($param)};
	db::commit();
	if ($call == "true")
    	echo "@" . json_encode($result);
	else if (a4p::$redirect != null)
		echo "$" . a4p::$redirect;
	else if (a4p::$javascript != null)
		echo "#" . a4p::$javascript;
	else
		echo "@" . json_encode($result);
} catch (Exception $e) {
    echo $e;
}

if ($polling) {
	push::remove($poll_id);
}
