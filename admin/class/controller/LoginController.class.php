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
	<link href="/resource/css/pages/login.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="/resource/plugins/select2/select2_metro.css" />
EOD;
		$env ['plugins'] = <<< EOD
	<script src="/resource/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>	
	<script type="text/javascript" src="/resource/plugins/select2/select2.min.js"></script>
EOD;
		$env ['scripts'] = <<< EOD
	<script src="/resource/js/login.js" type="text/javascript"></script> 
EOD;
		$env ['init'] = <<< EOD
		  Login.init();
		  login_init();
EOD;
		
		// Assign env to template
		a4p::assign ( 'env', $env );
		
		$sections ['content'] = 'login/login.php';
		
		$template = 'template/template.php';
		template::View ( $template, $sections, $env );
	}
	
	protected function onPageLoad(array &$env, $param = null) {
	}
	
	/**
	 * @ajaxcall
	 */

	 /*
	public function login() {
		$username = $_POST ['username'];
		$password = $_POST ['password'];
		$redirect = $_POST ['redirect'];
		
		$service = new UserService ();
		$user = null;
		
		try {
			$user = $service->login ( $username, $password );
		} catch ( Exception $e ) {
			return a4p::javascript ( "alert('Invalid username or password');" );
		}
		
		a4p::setAuth ( true );
		$this->setCurrentUser ( $user );
		
		return a4p::redirect ( $redirect ? rawurldecode($redirect) : '/home' );
	}*/
}
