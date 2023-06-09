<?php
require_once "controller/AbstractBaseController.class.php";

/**
 * @ajaxenable
 */
class LoginController extends AbstractBaseController {
	public function pageLoad($param = null) {
		$env ['title'] = 'Login';
		$env ['bodyclass'] = 'login';
		$env ['styles'] = <<< EOD

EOD;
		$env ['plugins'] = <<< EOD

EOD;
		$env ['scripts'] = <<< EOD

EOD;
		$env ['init'] = <<< EOD

EOD;
		
		// Assign env to template
		a4p::assign ( 'env', $env );
		
		$sections ['content'] = 'login/login.php';
		
		$template = 'template/login_template.php';
		template::View ( $template, $sections, $env );
	}
	
	protected function onPageLoad(array &$env, $param = null) {
	}
	
	
	public function login() {
		$username = $_POST ['username'];
		$password = $_POST ['password'];
		$redirect = $_POST ['redirect'];
		
		//$service = new UserService ();
		$user = null;
		
		try {
			//$user = $service->login ( $username, $password );
		} catch ( Exception $e ) {
			return a4p::javascript ( "alert('Invalid username or password');" );
		}
		
		a4p::setAuth ( true );
		$this->setCurrentUser ( 0, "Demo User" );
		
		return $this->redirect("/home");
	}
}
