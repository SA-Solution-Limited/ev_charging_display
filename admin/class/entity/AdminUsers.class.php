<?php

/** @table admin_users */
class AdminUsers extends Entity
{
	/** @id id */
	public $id;

	/** @column loginId */
	public $loginId;

	/** @column password */
	public $password;

	/** @column displayName */
	public $displayName;

	/** @column createAt */
	public $createAt;

	/** @column latestLoginAt */
	public $latestLoginAt;

	public function __construct() {
		parent::__construct();
		$this->id = 0;
		$this->loginId = '';
		$this->password = '';
		$this->displayName = '';
		$this->createAt = '';
		$this->latestLoginAt = '';
	}
}
