<?php
//
// a4p_sec.inc - Encryption
//

class a4p_sec
{
	public static $key="7VLlGJ9I3TtEv6gk";

	public static $auth = false;

	private static $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

	public static function randomString($length) {
		$pass = "";
		$alphaLength = strlen(self::$alphabet) - 1;
		for ($i = 0; $i < $length; ++$i) {
			$n = mt_rand(0, $alphaLength);
			$pass .= self::$alphabet[$n];
		}
		return $pass;
	}

	public static function encrypt($str) {
	    // Remove the base64 encoding from our key
	    $encryption_key = base64_decode(self::$key);
	    // Generate an initialization vector
	    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	    // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
	    $encrypted = openssl_encrypt($str, 'aes-256-cbc', $encryption_key, 0, $iv);
	    // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
	    return base64_encode($encrypted . '::' . $iv);
	}
	
	public static function decrypt($str) {
	    // Remove the base64 encoding from our key
	    $encryption_key = base64_decode(self::$key);
	    // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
	    list($encrypted_data, $iv) = explode('::', base64_decode($str), 2);
	    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
	}

}
