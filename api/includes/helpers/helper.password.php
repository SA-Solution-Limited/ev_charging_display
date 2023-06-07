<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

/**
 * A collection of password-related helper functions.
 * @version 1.0.0
 */
class PasswordHelper {

	/**
	 * Generate a random password with a given length.
	 * @since 1.0.0
	 * @param int $length Length of the generated password.
	 * @return string A random password.
	 */
	public static function generate($length = 12) {
		$char = str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()_');
		$pass = '';
		while (strlen($pass) < $length) {
			$pass .= $char[rand(0, count($char)-1)];
		}
		return($pass);
	}

	/**
	 * Generate a hashed password from a given password and salt.
	 * @since 1.0.0
	 * @param string $password Password to hash.
	 * @param string $salt Salt of the hash.
	 * @return string A hashed password.
	 */
	public static function hashPassword($password, $salt) {
		$salt = str_pad($salt, 22, '.');
		return(hash('sha256', "{$password}---{$salt}"));
	}

}
?>
