<?php
require_once "controller/AbstractBaseController.class.php";
abstract class AbstractAdminController extends AbstractBaseController {
	public $logger;
    public $sections = array ();
	public $template = 'admin/template/template.php';
	public $env = array ();

    public function __construct() {
		$this->logger = Logger::getLogger('RollingLogFileAppender');
		
		$this->sections ['header'] = 'admin/template/header.php';
		$this->sections ['menu'] = 'admin/template/menu.php';
		$this->sections ['footer'] = 'admin/template/footer.php';
		
		// should be defined otherwise expection thrown
		$this->env ['pluginstyles'] = "";
		$this->env ['styles'] = "";
		$this->env ['plugins'] = "";
		$this->env ['scripts'] = "";
		$this->env ['init'] = "";
		
		$this->env ['current_user'] = $this->getCurrentUser ();
		$this->env['bodyclass'] = 'page-header-fixed page-full-width';
	}

    public function pageLoad() {
		$this->requireLogin();
		return $this->index();
	}
	
	protected function loadDefaultRoute($slug, array $fnMap = array(), $strict = true) {
		if (!$slug) {
			return(false);
		}
		if (Util::arrayInput($fnMap, $slug)) {
			$slug = $fnMap[$slug];
		} else if (($parts = explode('-', $slug)) && count($parts) > 1) {
			for ($i = 1; $i < count($parts); $i++) {
				$parts[$i] = ucfirst($parts[$i]);
			}
			$slug = implode('', $parts);
		}
		if (!method_exists($this, $slug)) {
			return(false);
		}
		if ($strict) {
			$reflection = new ReflectionMethod($this, $slug);
			if (!$reflection->isPublic()) {
				return(false);
			}
		}
		$this->$slug();
		return(true);
	}
	
	public function requireLogin() {
		if (!a4p::isLoggedIn ()) {
			header('Location: /login?redirect='.rawurlencode($_SERVER['REQUEST_URI']));
			exit();
		}
		$this->user = new stdClass();
		$this->user->loginId = $this->getCurrentUser('username');
		$this->user->role = $this->getCurrentUser('role');
	}
	
	public function requireRole() {
		if (!in_array($this->getCurrentUser('role'), func_get_args())) {
			$this->view('/admin/forbidden.php');
			exit;
		}
	}
	
	/**
	 * @ajaxcall
	 */
	public function logout() {
		a4p::setAuth ( false );
		a4p::Reset ( 'SessionVar' );
		session_unset ();
		session_destroy ();
		return a4p::redirect ( "/login" );
	}
	
	protected function getCurrentUser($field = 'username') {
		$sessionVar = a4p::Model('SessionVar');
		try {
			if (@unserialize($sessionVar->{$field}) == null && $sessionVar->{$field} != ""){
				return $sessionVar->{$field};
			}
			a4p::setAuth(false);
			a4p::Reset('SessionVar');
			session_unset();
			session_destroy();
			header('Location: /login');
			exit();
		} catch (Exception $e) {
			a4p::setAuth(false);
			a4p::Reset('SessionVar');
			session_unset();
			session_destroy();
			header('Location: /login');
			exit();
		}
	}
	
	protected function setCurrentUser($user) {
		$sessionVar = a4p::Model ( 'SessionVar' );
		$sessionVar->username = $user->loginId;
		$sessionVar->role = $user->role;
	}
}