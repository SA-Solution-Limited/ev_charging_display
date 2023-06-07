<?php

class uploadify
{
	public static function getUploadFiles($id) {
		if (config::$tmp_path == null)
			config::$tmp_path = session_save_path();

		$filename = config::$tmp_path . DIRECTORY_SEPARATOR . "uploadify_" . a4p::$serial . ".dat";
		if (!is_file($filename)) 
			return array();

		$files = unserialize(file_get_contents($filename));

		return isset($files[$id]) ? $files[$id] : array();
	}

	public static function removeUploadFile($id, $n) {
		if (config::$tmp_path == null)
			config::$tmp_path = session_save_path();

		$filename = config::$tmp_path . DIRECTORY_SEPARATOR . "uploadify_" . a4p::$serial . ".dat";
		if (!is_file($filename)) 
			return;

		$files = unserialize(file_get_contents($filename));

		if (isset($files[$id])) {
			if (isset($files[$id][$n])) {
				unset($files[$id][$n]);
				$files[$id] = array_values($files[$id]);
				file_put_contents($filename, serialize($files));
			}
		}
	}
}