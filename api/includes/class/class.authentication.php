<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

class Authentication {
	
	public $id;
	public $user;
	public $role;
	public $time;
	public $try;
	public $isAuth = false;

	function __construct() {
		global $site;
		
		if (!isset($_SESSION[$site->session])) {
			$_SESSION[$site->session] = array(
				'auth_id'    => 0,
				'auth_user'  => null,
				'auth_role'  => 0,
				'auth_time'  => '',
				'auth_try'   => 0,
				'avatar'     => null,
			);
		}
		
		/* for prototyping only */
		if ($site->getSiteConfig('role', 'isPrototype') && $this->id = !!SessionHelper::get('auth_id')) {
			$this->id     = SessionHelper::get('auth_id', 1);
			$this->user   = SessionHelper::get('auth_user', 'Demo User');
			$this->role   = SessionHelper::get('auth_role', 'Demo');
			$this->isAuth = true;
			return;
		}
		
		/* assign user properties */
		$this->id   = SessionHelper::get('auth_id');
		$this->time = SessionHelper::get('auth_time');
		$this->try  = SessionHelper::get('auth_try');
		
		/* authenticate */
		$dbAuth = $this->databaseAuth();
		$auAuth = $this->autoAuth();
		$this->isAuth = $dbAuth || $auAuth;
		
		/* bind user details */
		if ($this->isAuth) {
			require_once('includes/service/service.member.php');
			$mbrSrv = new MemberService();
			$this->user = $mbrSrv->toEntity($mbrSrv->getById($this->id));
		}
	}
	
	public function requireLogin() {
		global $site;
		if (!$this->isAuth) {
			HttpHelper::redirect($site->urlLocaleBase.'account/login/?redirect='.rawurlencode($site->urlLocaleBase.$site->urlQuery), 302);
		}
	}
	
	protected function databaseAuth() {
		// verify login session via database
		if (!!!$this->id) return(false);
		$rs = Db::query('SELECT * FROM [view:member_auth] WHERE `mbrId` = :mbrId', array(
			':mbrId' => $this->id,
		));
		return($rs !== false && count($rs) > 0);
	}
	
	protected function autoAuth() {
		// verify cookie session via database
		global $site;
		
		if (!!!HttpHelper::getCookie('auth_id', 0) || !!!HttpHelper::getCookie('auth_session')) return(false);
		
		$rs = Db::query('SELECT * FROM [view:member_auth] MBR LEFT JOIN [table:membersession] MBRS ON MBR.`mbrId` = MBRS.`mbrId` LEFT JOIN [table:memberext] MBRX ON MBR.`mbrId` = MBRX.`mbrId` WHERE MBR.`mbrId` = :mbrId AND MBRS.mbrSession = :mbrSession', array(
			':mbrId' => HttpHelper::getCookie('auth_id'),
			':mbrSession' => HttpHelper::getCookie('auth_session'),
		));
		
		if ($rs !== false && count($rs) > 0) {
			$rs = $rs[0];
			$this->id    = $rs['mbrId'];
			$this->user  = $rs['mbrFirstName'].' '.$rs['mbrLastName'];
			$this->role  = $rs['mbrRole'];
			$this->time  = $rs['mbrLastActive'];
			
			$expires = strtotime('+1 week');
			$session = PasswordHelper::hashPassword($expires, $this->id);
			Db::update('UPDATE [table:membersession] SET `mbrSession` = :mbrSession WHERE `mbrId` = :mbrId LIMIT 1', array(
				':mbrId' => $this->id,
				':mbrSession' => $session,
			));
			setcookie('auth_id', $this->id, $expires, $site->urlBase);
			setcookie('auth_session', $session, $expires, $site->urlBase);
			
			return(true);
		} else {
			return(false);
		}
	}
}
?>
