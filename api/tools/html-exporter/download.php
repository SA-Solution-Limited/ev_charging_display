<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 * 
 * @var Site $this
 */

defined('TOOL_ROOT') || define('TOOL_ROOT', dirname(__FILE__));
defined('EXPORT_ROOT') || define('EXPORT_ROOT', $this->docRoot.'export');
 
require_once('includes/class/class.mime.php');

if (is_file(TOOL_ROOT.'/config.ini') && ($settings = parse_ini_file(TOOL_ROOT.'/config.ini', true))) {
	$settings = (object)ArrayHelper::getValue($settings, 'download', array());
} else {
	$settings = new stdClass();
}

$exclude = array(
	'\.git*',
	'\.ht(access|passwd)',
	'^config.ini',
	'^download.php',
	'^generate.php',
);
if (isset($settings->exclude)) {
	if (!is_array($settings->exclude)) {
		$settings->exclude = explode(',', $settings->exclude);
	}
	$exclude = array_merge($exclude, $settings->exclude);
}

$zipfile = FileSystemHelper::zipdir(EXPORT_ROOT, null, $exclude);

header('Content-Type: '.MIME::get('zip'));
header('Content-Length: '.filesize($zipfile));
header('Content-disposition: attachment; filename="'.ArrayHelper::getValue($settings, 'filename', 'export').'.zip"');
readfile($zipfile);
unlink($zipfile);
?>
