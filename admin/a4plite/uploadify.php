<?php

require_once "security.inc.php";
require_once "config.inc.php";

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

if (config::$tmp_path == null)
	config::$tmp_path = session_save_path();

$yesterday = strtotime(config::$tmp_expire_time);
foreach (glob(config::$tmp_path . DIRECTORY_SEPARATOR . "uploadify_*") as $oldfile) {
	if (filemtime($oldfile) < $yesterday)
		unlink($oldfile);
}

$id = $_POST['id'];
$serial = $_POST['serial'];

$src = $_FILES['Filedata']['tmp_name'];
$dest = config::$tmp_path . DIRECTORY_SEPARATOR . "uploadify_" . a4p_sec::randomString(32);
move_uploaded_file($src, $dest);
$_FILES['Filedata']['tmp_name'] = $dest;

$filename = config::$tmp_path . DIRECTORY_SEPARATOR . "uploadify_" . $serial . ".dat";
if (is_file($filename)) {
	$files = unserialize(file_get_contents($filename));
} else
	$files = array();

$files[$id][] = $_FILES;

file_put_contents($filename, serialize($files));

echo 1;
