<?php

/** @table admin_users */
class AdminUsers extends Entity
{
	/** @id id */
	public $id;

	/** @column login_id */
	public $loginId;

	/** @column password */
	public $password;

	/** @column display_name */
	public $displayName;

	/** @column create_at */
	public $createAt;

	/** @column latest_login_at */
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
