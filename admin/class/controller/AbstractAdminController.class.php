<?php
require_once "controller/AbstractBaseController.class.php";
require_once "entity/AdminUsers.class.php";
abstract class AbstractAdminController extends AbstractBaseController {
	public $logger;
    public $sections = array ();
	public $template = 'template/template.php';
	public $env = array ();
    private $user;

    public function __construct() {
		$this->logger = Logger::getLogger('RollingLogFileAppender');
		
		$this->sections ['header'] = 'template/header.php';
		$this->sections ['menu'] = 'template/menu.php';
		$this->sections ['footer'] = 'template/footer.php';
		
		// should be defined otherwise expection thrown
		$this->env ['pluginstyles'] = "";
		$this->env ['styles'] = "";
		$this->env ['plugins'] = "";
		$this->env ['scripts'] = "";
		$this->env ['init'] = "";
		
		$this->env ['current_user'] = $this->getCurrentUser ();
		$this->env['bodyclass'] = "";
	}

	
	protected abstract function onPageLoad($param = null);

    public function pageLoad($param = null) {
		$this->requireLogin();
		echo $this->onPageLoad($param);
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
		$this->user = $this->getCurrentUser();
        a4p::assign('currentUser', $this->user);
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
	
	protected function getCurrentUser() {
		$sessionVar = a4p::Model('SessionVar');
		try {
			if (@unserialize($sessionVar->user) != null){
                $this->user = @unserialize($sessionVar->user);
                $this->setCurrentUser($this->user);;
				return $this->user;
			}
			a4p::setAuth(false);
			a4p::Reset('SessionVar');
			session_unset();
			session_destroy();
			header('Location: /login?redirect='.urlencode($_SERVER['REQUEST_URI']));
			exit();
		} catch (Exception $e) {
			a4p::setAuth(false);
			a4p::Reset('SessionVar');
			session_unset();
			session_destroy();
			header('Location: /login?e=403e&m='.$e->getMessage());
			exit();
		}
	}
	
	protected function setCurrentUser(AdminUsers $user) {
		parent::setCurrentUser($user);
	}
}