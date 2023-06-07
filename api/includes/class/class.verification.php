<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('class.proxy.php');

class Verification {
	
	protected $lockFile = '.lock';
	protected $verifyFile = '.verify';
	
	function __construct() {
		return;
		
		global $site;
		$this->lockFile   = dirname(__FILE__).'/'.$this->lockFile;
		$this->verifyFile = dirname(__FILE__).'/'.$this->verifyFile;
		
		/* handle lock/unlock requests */
		if (preg_match('/system\/(lock|unlock)\//', implode('/', $site->request), $matches)) {
			switch ($matches[1]) {
				case 'lock':
					return($this->lock());
				case 'unlock':
					return($this->unlock());
			}
		}
		
		/* check for site lock */
		if (is_file($this->lockFile) && !$this->verify()) {
			$this->http423();
		}
		
		/* verify domain status */
		if (!is_file($this->verifyFile) || time() - filemtime($this->verifyFile) >= 86400) {
			$this->verify();
		}
	}
	
	protected function lock($reason = null) {
		touch($this->lockFile) && $this->http423('The system is locked down.'.($reason ? ' (Reason: '.$reason.')' : ''));
	}
	
	protected function unlock($mute = false) {
		is_file($this->lockFile) && unlink($this->lockFile) && ($mute ? true : exit('The system is unlocked.'));
	}
	
	protected function verify() {
		global $site;
		
		$proxy = new Proxy('https://activation.technetium.info/api/validate/');
		$proxy->set('data', array(
			'domain' => preg_replace('/^https?:\/\//', '', $site->origin),
		));
		$result = $proxy->execute();
		if ($result['success'] && $result['data']) {
			$data = json_decode($result['data']);
			if ($data->success) {
				touch($this->verifyFile);
				return($this->unlock(true));
			} else {
				return($this->lock($data->message));
			}
		}
		return(false);
	}
	
	protected function http423($msg = null) {
		header('HTTP/1.1 423 Locked');
		exit($msg ? $msg : 'The system is currently locked down. Please contact developer for details.');
	}
	
}
?>
