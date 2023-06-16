<?php

/** @table media */
class Media extends Entity
{
	/** @id id */
	public $id;

	/** @column fileType */
	public $fileType;

	/** @column fileSize */
	public $fileSize;

	/** @column physicalPath */
	public $physicalPath;

	/** @column displayName */
	public $displayName;

	/** @column mediaFor */
	public $mediaFor;

	/** @column createAt */
	public $createAt;

	/** @column avalibleFrom */
	public $avalibleFrom;

	/** @column avalibleTo */
	public $avalibleTo;

	/** @column isPublish */
	public $isPublish;


	public function __construct() {
		parent::__construct();
		$this->id = 0;
		$this->fileType = '';
		$this->fileSize = '';
		$this->physicalPath = '';
		$this->displayName = '';
		$this->mediaFor = '';
		$this->createAt = '';
		$this->avalibleFrom = date_format(new DateTime(), "Y-m-d H:i:s");
		$datetime = new DateTime();
		$datetime->modify('+7 day');
		$this->avalibleTo = date_format($datetime, "Y-m-d H:i:s");
		$this->isPublish = 1;
	}
}
