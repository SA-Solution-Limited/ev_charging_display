<?php
require_once "controller/AbstractBaseController.class.php";
require_once "service/AdminUserService.class.php";

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
		
		$service = new AdminUserService ();
		$user = $service->login ( $username, $password );
		
		if(is_null($user)){
			$_SESSION["js_alert"] = $this->alert("Invalid username or password");
			if(isset($redirect) && strlen($redirect) > 0){
				return $this->redirect("/login?redirect=".urlencode($redirect));
			}else{
				return $this->redirect("/login");
			}
		}
		
		a4p::setAuth ( true );
		$this->setCurrentUser ( $user );
		if(isset($redirect) && strlen($redirect) > 0 && $redirect != "/login"){
			return $this->redirect($redirect);
		}else{
			return $this->redirect("/home");
		}
	}
}
