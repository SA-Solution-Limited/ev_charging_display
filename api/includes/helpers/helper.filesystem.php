<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of helper functions to perform file and directory operations.
 * @version 1.0.0
 */
class FileSystemHelper {

	/**
	 * Copy files. Enhanced from PHP's native function with recursive and exclusion filter support.
	 * @since 1.0.0
	 * @param string $src Source file or directory path.
	 * @param string $dst Destination file or directory path.
	 * @param boolean $recursive Whether to create directories recursively.
	 * @param string $exclude Regular expression of files to exclude.
	 * @param resource $context
	 * @return boolean True on success or false on failure.
	 */
	public static function copy($src, $dst, $recursive = false, $exclude = '^\.ht', $context = null) {
		if ($recursive && is_dir($src)) {
			$dir = opendir($src);
			if (!is_dir($dst)) {
				self::mkdir($dst, 0775, true);
			}
			while (($file = readdir($dir)) !== false) {
				if ($file == '.' || $file == '..' || preg_match("/{$exclude}/", $file)) continue;
				if (is_dir("{$src}/{$file}")) {
					self::copy("{$src}/{$file}", "{$dst}/{$file}", true, $exclude);
				} else if (!is_file("{$dst}/{$file}") || filemtime("{$src}/{$file}") > filemtime("{$dst}/{$file}")) { 
					copy("{$src}/{$file}", "{$dst}/{$file}"); 
				}
			}
			closedir($dir);
			return(true);
		} else {
			return(copy($src, $dst));
		}
	}

	/**
	 * Return the absolute file system path of a given file.
	 * @param string $path Path of the file.
	 * @return string|false Absolute file system path of the file, false if the given path is a URL.
	 */
	public static function fspath($path) {
		if (preg_match('/^https?:\/\//', $path)) return(false);
		if (preg_match('/^\//', $path)) {
			return($_SERVER['DOCUMENT_ROOT'].parse_url($path, PHP_URL_PATH));
		}
		$fspath = $_SERVER['DOCUMENT_ROOT'].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).$path;
		$fspath = preg_replace('/\/\.\//', '/', $fspath);
		while (preg_match('/[^\/\.]+?\/\.\.\//', $fspath)) {
			$fspath = preg_replace('/[^\/\.]+?\/\.\.\//', '', $fspath);
		}
		return($fspath);
	}

	/**
	 * Create a gzip file with the given source.
	 * @since 1.0.0
	 * @param string $src Source directory path.
	 * @param string|null $dst Destination file path.
	 * @param int{-1,0-9} $level Compression level. 0 for no compression up to 9 for maximum compression, -1 for the default compression of the zlib library.
	 * @return string Destination file path.
	 */
	public static function gzip($src, $dst = null, $level = 0) {
		if (!$dst) {
			$dst = $src.'.gz';
		}
		if (!is_file($src) || !($fh = fopen($src, 'rb'))) {
			return(false);
		}
		if (!($gzh = gzopen($dst, "wb{$level}"))) {
			fclose($fh);
			return(false);
		}
		while (!feof($fh)) {
			gzwrite($gzh, fread($fh, 1024 * 512));
		}
		fclose($fh);
		return($dst);
	}

	/**
	 * Create directory with the given path and change directory permission. Enhanced from PHP's native function by tackling [[chmod]] issue.
	 * @since 1.0.0
	 * @param string $path Directory path.
	 * @param int $mode Directory permission. Default is 0775.
	 * @param boolean $recursive Whether to create directories recursively.
	 * @param resource $context
	 * @return boolean True on success or false on failure.
	 */
	public static function mkdir($path, $mode = 0775, $recursive = false, $context = null) {
		$mask = umask(0);
		$result = @mkdir($path, $mode, $recursive);
		umask($mask);
		return($result);
	}

	/**
	 * Remove the given directory. Enhanced from PHP's native function with recursive support.
	 * @since 1.0.0
	 * @param string $path Directory path.
	 * @param boolean $recursive Whether to remove directories recursively.
	 * @param resource $context
	 * @return boolean True on success or false on failure.
	 */
	public static function rmdir($path, $recursive = false, $context = null) {
		if ($recursive) {
			$files = array_diff(scandir($path), array('.', '..'));
			foreach ($files as $file) {
				if (is_dir("$path/$file")) {
					self::rmdir("$path/$file", true);
				} else {
					unlink("$path/$file");
				}
			}
		}
		return(rmdir($path));
	}

	/**
	 * Returns an array of files and directories. Enhanced from PHP's native function with recursive support.
	 * @since 1.0.0
	 * @param string $path Directory path.
	 * @param int $sort Sort order. Default is to sort ascendingly.
	 * @param boolean $recursive Whether to scan recursively.
	 * @param resource $context
	 * @return array|false Array of file names on success, or false on failure.
	 */
	public static function scandir($path, $sort = 0, $recursive = false, $context = null) {
		$path = preg_replace('/\/$/', '', $path);
		$array = array_diff(scandir($path, $sort), array('.', '..'));
		if ($recursive) {
			foreach ($array as $file) {
				$scanpath = "{$path}/{$file}";
				if (is_dir($scanpath)) {
					$array = array_merge($array, array_map(function($file) use ($scanpath) {
						return("{$scanpath}/{$file}");
					}, self::scandir("{$scanpath}", $sort, true)));
				}
			}
		}
		$array = array_map(function($entry) use ($path) {
			return(preg_replace('/^'.addcslashes("{$path}/", './\\').'/', '', $entry));
		}, $array);
		switch ($sort) {
			case SCANDIR_SORT_ASCENDING:
				sort($array);
			break;
			case SCANDIR_SORT_DESCENDING:
				rsort($array);
			break;
		}
		return($array);
	}

	/**
	 * Compress a given directory.
	 * @since 1.0.0
	 * @param string $src Source directory path.
	 * @param string|null $dst Destination file path.
	 * @param array $exclude Array of excluded file paths.
	 * @return string Destination file path.
	 */
	public static function zipdir($src, $dst = null, $exclude = array()) {
		$src = preg_replace('/\/$/', '', $src);
		$filelist = array_filter(self::scandir($src, 0, true), function($entry) use ($src, $exclude) {
			return(!is_dir("{$src}/{$entry}") && array_sum(array_map(function($regex) use ($entry) {
				return(preg_match("/{$regex}/", $entry) ? 1 : 0);
			}, $exclude)) == 0);
		});
		
		$tmpfile = tempnam(sys_get_temp_dir(), '');
		$zip = new ZipArchive();
		$zip->open($tmpfile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		foreach ($filelist as $file) {
			$zip->addFile("{$src}/{$file}", $file);
		}
		$zip->close();
		
		if ($dst) {
			is_dir(dirname($dst)) || self::mkdir(dirname($dst), 0775, true);
			return(rename($tmpfile, $dst));
		}
		return($tmpfile);
	}

}
?>
