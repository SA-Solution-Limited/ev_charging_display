<?php
require_once 'common/Path.class.php';

class Helper {
	/**
	 * content of parse_ini_file setting.ini
	 * @var array
	 */
	private static $settings;
	
	/**
	 * 
	 * @param mixed $message Will auto var_export if is not a string
	 * @param string $prefix Optional. Prefix with "[]"
	 * @param string $filename Optional. Full path to file
	 * @param boolean $return Optional. True to return serialized message without output to log. Default faluse
	 * @return string Serialized message with prefix
	 */
	public static function log($message, $prefix = null, $filename = null, $return = false)
	{
		// use var_export if not string
		if (!is_string($message))
			$message = @var_export($message, true);
		
		$output = '['.date('Y-m-d H:i:s').'] '.
				($prefix != null ? '['.$prefix.'] ' : null).
				$message;
		
		if ($return)
			return $output;
		
		if ($filename == null)
			$filename = Path::Combine(SITE_ROOT, 'logs', 'debug.txt');
		
		@file_put_contents($filename, $output."\r\n", \FILE_APPEND);
		
		return $output;
	}
	
	/**
	 * Copy object variables from source to target
	 * @param mixed $source
	 * @param mixed $target
	 */
	public static function bind($source, &$target, $emptyToNullFields = array())
	{
		if ($source == null || $target == null) return;
		if (is_array($source))
			$properties = $source;
		else
			$properties = get_object_vars($source);
		foreach ($properties as $key => $value)
		{
			if (is_array($target))
			{
				if (array_key_exists($key, $target)) {
					$target[$key] = $value;
					if ($emptyToNullFields != null && array_search($key, $emptyToNullFields) !== false) {
						$target[$key] = $target[$key] == '' ? null : $target[$key];
					}
				}
			}
			else if (property_exists($target, $key)) {
				$target->$key = $value;
				if ($emptyToNullFields != null && array_search($key, $emptyToNullFields) !== false) {
					$target->$key = $target->$key == '' ? null : $target->$key;
				}
			}
		}
	}

	public static function toDisplayModels($model, $className) {
		require_once 'model/DisplayModel.class.php';
		$models = array();
		$_class = new ReflectionClass(basename($className));
		$_properties = $_class->getProperties();
		foreach ($_properties as $_prop) {
			$obj = new DisplayModel();
			$obj->name = $_prop->getName();
			$obj->value = $_prop->getValue($model);
			$comment = $_prop->getDocComment();
			$pos = strpos($comment, '@display[');
			if ($pos != false && $pos >= 0) {
				$str = substr($comment, $pos + strlen('@display['));
				$str = substr($str, 0, strpos($str, ']'));
				$obj->display = $str;
			} else {
				$obj->display = $obj->name;
			}
			$models[] = $obj;
		}
		return $models;
	}
	
	/**
	 * Remove html tags
	 * @param string $input
	 * @return string
	 */
	public static function safe($input)
	{
		return strip_tags($input);
	}
	
	/**
	 * safe url decode
	 * @param unknown $var
	 * @return mixed
	 */
	public static function urldecode($var)
	{
		return self::safe(urldecode($var));
	}
	
	/**
	 * Get text from resource/setting.ini parsed by parse_ini_file with section
	 * @param string $section
	 * @param string $key
	 * @return string
	 */
	public static function Setting($section, $key)
	{
		if (self::$settings == null)
		{
			$ini_file = Path::Combine(SITE_ROOT, 'class', 'setting.ini');
			self::$settings = parse_ini_file($ini_file, true);
		}
		
		if (self::$settings === false)
			return null;
		else
			return isset(self::$settings[$section][$key]) ? self::$settings[$section][$key] : null;
	}
	
	/**
	 * Detect empty with trim
	 * @param string $str
	 * @return boolean
	 */
	public static function isEmpty($str)
	{
		return $str == null || trim($str . '') == '';
	}
	
	public static function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
	
		if (is_array($d)) {
			/*
				* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(array("Helper","objectToArray"), $d);
		}
		else {
			// Return array
			return $d;
		}
	}
	
	/**
	 * Recursively remove directory
	 *
	 * @param string $dir        	
	 */
	public static function rrmdir($dir) {
		foreach ( glob ( $dir . '/*' ) as $file ) {
			if (is_dir ( $file )) {
				self::rrmdir ( $file );
			} else {
				unlink ( $file );
			}
		}
		rmdir ( $dir );
	}
	
	public static function createTempFile()
	{
		return tempnam(sys_get_temp_dir(), 'a4p');
	}
	
	public static function fopen_utf8($filename) {
		$encoding = '';
		$handle = fopen ( $filename, 'r' );
		$bom = fread ( $handle, 2 );
		// fclose($handle);
		rewind ( $handle );
		
		if ($bom === chr ( 0xff ) . chr ( 0xfe ) || $bom === chr ( 0xfe ) . chr ( 0xff )) {
			// UTF16 Byte Order Mark present
			$encoding = 'UTF-16';
		} else {
			$file_sample = fread ( $handle, 1000 ) + 'e'; // read first 1000 bytes
			                                           // + e is a workaround for mb_string bug
			rewind ( $handle );
			
			$encoding = mb_detect_encoding ( $file_sample, 'UTF-8, UTF-7, ASCII, EUC-JP,SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP' );
		}
		if ($encoding) {
			stream_filter_append ( $handle, 'convert.iconv.' . $encoding . '/UTF-8' );
		}
		return ($handle);
	}
	
	public static function getMimeType($filename) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$contentType = finfo_file($finfo, $file);
		finfo_close($finfo);
		return $contentType;
	}
	
	/** encode the string the handle ie chinese file name problem
	 * 	@param string $fileName return {$encodedFileName}
	 * 	@param boolean $returnHeader return 'Content-Disposition: attachment; filename="{$encodedFileName}"'
	 *	@return String */
	public static function handleIEFileNameEncode($fileName, $returnHeader)
	{
		$ua = $_SERVER ["HTTP_USER_AGENT"];
		$encodeFileName = '';
		if(preg_match("/MSIE/", $ua) || preg_match("/Trident\/7.0/", $ua)){
			$encodeFileName = str_replace("+", " ", urlencode($fileName));
		} else if (preg_match("/Firefox/", $ua)) {
			$encodeFileName = 'utf8\'\'' . $fileName ;
		} else {
			$encodeFileName = $fileName;
		}
		
		if($returnHeader){
			return 'Content-Disposition: attachment; filename="' . $encodeFileName . '"';
		}else{
			return $encodeFileName;
		}
	}
	
	public static function ExcelToArray($filename, $isAssociative = true, &$array_headers = NULL){
	
		require_once 'lib/phpexcel/PHPExcel.php';
	
		$ret = array();
	
		//load excel
		$inputFileType = PHPExcel_IOFactory::identify($filename);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	
		// 		$objReader = new PHPExcel_Reader();
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($filename);
	
		$activeSheet = $objPHPExcel->getActiveSheet();
	
		$rows = $activeSheet->getRowIterator();
	
		foreach($rows as $rowNum => $row){
				
			$record = array();
				
			$columns = $row->getCellIterator();
				
			foreach($columns as $colNum => $column){
				if($isAssociative && $rowNum == 1){
					$array_headers[] = $column->getValue();
				} else {
					if($isAssociative){
						$key = $array_headers[$colNum];
					} else {
						$key = $colNum;
					}
					$record[$key] = $column->getValue();
				}
			}
				
			if(!$isAssociative || $rowNum != 1){
				$ret[] = $record;
			}
		}
	
		return $ret;
	
	}
	
	public static function number_format_clean($number, $precision=0, $dec_point='.', $thousands_sep=',')
	{
		$text = number_format($number, $precision, $dec_point, $thousands_sep);
		
		// remove trailing zeros
		if (strpos($text, '.') !== false) {
			while ($text[strlen($text) - 1] == "0" || $text[strlen($text) - 1] == ".") {
				if ($text[strlen($text) - 1] == ".") {
					$text = substr($text, 0, strlen($text) - 1);
					break;
				}
				else {
					$text = substr($text, 0, strlen($text) - 1);
				}
			}
		}
		return $text;
	}
	
	public static function getLocalizeWeek($date) {
		$dt = new DateTime($date);
		$week = $dt->format("w");
		switch ($week) {
			case 0:
				return '星期日';
			case 1:
				return '星期一';
			case 2:
				return '星期二';
			case 3:
				return '星期三';
			case 4:
				return '星期四';
			case 5:
				return '星期五';
			case 6:
				return '星期六';
		}
	}
	
	/**
	 * Calculate the difference in months between two dates (v1 / 18.11.2013)
	 * http://stackoverflow.com/questions/2681548/find-month-difference-in-php
	 *
	 * @param DateTime $date1
	 * @param DateTime $date2
	 * @return int
	 */
	public static function diffInMonths(DateTime $date1, DateTime $date2)
	{
		$diff =  $date1->diff($date2);
	
		$months = $diff->y * 12 + $diff->m + $diff->d / 30;
	
		return (int) round($months);
	}
	
	public static function diffInDaysInclude(DateTime $date1, DateTime $date2)
	{
		$diff = $date1->diff($date2);
		return (int)$diff->format('%a') + 1;
	}

	// https://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation
	// https://stackoverflow.com/a/21797668
	public static function image_fix_orientation($filename, $destFilename) {
    $exif = exif_read_data($filename);
    if (!empty($exif['Orientation'])) {
        $image = imagecreatefromjpeg($filename);
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }

				imagejpeg($image, $destFilename, 90);
				return true;
		}
		return false;
	}
}