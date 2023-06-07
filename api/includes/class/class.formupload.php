<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

class FormUpload {
	
	protected $uploadPath;
	protected $files = array();
	
	function __construct($name, $group = null) {
		if ($group) {
			if (!isset($_FILES[$group]) || !isset($_FILES[$group]['name'][$name])) {
				return(false);
			}
			$files = $_FILES[$group];
			if (is_array($files['name'][$name])) {
				$this->files = array_map(function($name, $type, $tmp_name, $error, $size) {
					return(new FormUploadModel(array(
						'name'     => $name,
						'type'     => $type,
						'tmp_name' => $tmp_name,
						'error'    => $error,
						'size'     => $size,
					)));
				}, $files['name'][$name], $files['type'][$name], $files['tmp_name'][$name], $files['error'][$name], $files['size'][$name]);
			} else {
				$this->files = array(new FormUploadModel(array(
					'name'     => $files['name'][$name],
					'type'     => $files['type'][$name],
					'tmp_name' => $files['tmp_name'][$name],
					'error'    => $files['error'][$name],
					'size'     => $files['size'][$name],
				)));
			}
		} else {
			if (!isset($_FILES[$name])) {
				return;
			}
			$files = $_FILES[$name];
			if (is_array($files['name'])) {
				$this->files = array_map(function($name, $type, $tmp_name, $error, $size) {
					return(new FormUploadModel(array(
						'name'     => $name,
						'type'     => $type,
						'tmp_name' => $tmp_name,
						'error'    => $error,
						'size'     => $size,
					)));
				}, $files['name'], $files['type'], $files['tmp_name'], $files['error'], $files['size']);
			} else {
				$this->files = array(new FormUploadModel($files));
			}
		}
	}
	
	public function getFiles() {
		return($this->files);
	}
	
	public function getFile($idx) {
		return(ArrayHelper::getValue($this->files, $idx, false));
	}
	
	public function setUploadPath($path) {
		$this->uploadPath = $path.(substr($path, -1) != '/' ? '/' : '');
		if (!is_dir($this->uploadPath)) {
			FileSystemHelper::mkdir($this->uploadPath, 0755, true);
		}
	}
	
	public function execute() {
		global $site;
		if (!$this->uploadPath) {
			$this->setUploadPath('uploads/media/'.date('Y/m/'));
		}
		$dir = $site->docRoot.$this->uploadPath;
		array_walk($this->files, function($file) use ($site) {
			$this->resolveConflict($file);
			$file->path = $this->uploadPath.$file->name;
			$file->full_path = $site->docRoot.$file->path;
			if (move_uploaded_file($file->tmp_name, $file->full_path)) {
				chmod($file->path, 0664);
				unset($file->tmp_name);
			} else {
				$file->error = UPLOAD_ERR_CANT_WRITE;
			}
		});
	}
	
	protected function resolveConflict(FormUploadModel &$file) {
		while (is_file($this->uploadPath.$file->name)) {
			$file->name = $this->upcountFilename($file->name);
		}
	}

	protected function upcountFilename($name) {
		return(preg_replace_callback('/(?:(?:-([\d]+))?(\.[^.]+))?$/', function($matches) {
			$index = isset($matches[1]) ? ((int)$matches[1]) + 1 : 1;
			$ext = isset($matches[2]) ? $matches[2] : '';
			return '-'.$index.''.$ext;
		}, $name, 1));
	}

}

class FormUploadModel {
	
	public $name;
	public $type;
	public $tmp_name;
	public $error;
	public $size;
	public $path;
	public $full_path;
	
	function __construct(array $file = array()) {
		$this->name     = ArrayHelper::getValue($file, 'name', null);
		$this->type     = ArrayHelper::getValue($file, 'type', null);
		$this->tmp_name = ArrayHelper::getValue($file, 'tmp_name', null);
		$this->error    = ArrayHelper::getValue($file, 'error', null);
		$this->size     = ArrayHelper::getValue($file, 'size', null);
	}
	
	public function getStatus() {
		return($this->error == UPLOAD_ERR_OK);
	}
	
	public function getMessage() {
		switch ($this->error) {
			case UPLOAD_ERR_INI_SIZE:
				return('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
			case UPLOAD_ERR_FORM_SIZE:
				return('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
			case UPLOAD_ERR_PARTIAL:
				return('The uploaded file was only partially uploaded.');
			case UPLOAD_ERR_NO_FILE:
				return('No file was uploaded.');
			case UPLOAD_ERR_NO_TMP_DIR:
				return('Missing a temporary folder.');
			case UPLOAD_ERR_CANT_WRITE:
				return('Failed to write file to disk.');
			case UPLOAD_ERR_EXTENSION:
				return('A PHP extension stopped the file upload.');
		}
	}
	
}
?>
