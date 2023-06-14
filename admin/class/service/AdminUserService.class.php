<?php

require_once "entity/AdminUsers.class.php";
class AdminUserService
{

    /**
	 * Login logic
	 * @param string $username
	 * @param string $password
	 * @throws \Exception
	 * @return AdminUsers
	 */
	public function login($username, $password)
	{
		$user = AdminUsers::findFirst("login_id = :login_id and password = :password", array(':login_id' => $username, ':password' => md5($username.$password)));	
	    return $user;
	}
}