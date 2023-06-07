<?php
//
// rerender.php - Rerender components
//

if (!$_SERVER['REQUEST_METHOD'] === 'POST')
	exit();

if (!isset($_POST["state"]) || !isset($_POST["id"]))
	exit();

$encrypted = $_POST["state"];
$ids = $_POST["id"];
$serial = $_POST["serial"];
$rerender = true;

require_once "security.inc.php";

session_start();
a4p_sec::$key = $_SESSION["a4p._key"];
session_write_close();

$state = json_decode(a4p_sec::decrypt($encrypted), true);
$page = $state['phpself'];
$query =  $state['phpquery'];

$_GET = array();
parse_str($query, $_GET);

$_SERVER["REQUEST_URI"] = $page;

ob_start();
require dirname(dirname(__FILE__)) . "/route.php";;
ob_end_flush();
$html = ob_get_contents();
ob_end_clean();

$doctype = "<!DOCTYPE HTML";
if (strncasecmp($html, $doctype, strlen($doctype)) != 0) {
	$html4doctype = <<< END
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
END;
	$html = $html4doctype . $html;
}

libxml_use_internal_errors(true);

$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
$dom = new DOMDocument("1.0", "utf-8");
$dom->preserveWhiteSpace = false;
$dom->loadHTML($html);

$contents = array();
$arr = explode(",", $ids);
foreach ($arr as $id) {
	$tag = $dom->getElementById($id);
	$contents[$id] = str_replace('&#13;', '', $dom->saveXML($tag));
}

$json = json_encode($contents);
echo "@" . $json;
