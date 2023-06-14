<?php
require_once('controller/AbstractAdminController.class.php');

class HomeController extends AbstractAdminController
{
	/**
	 * Page load handler
	 * @param array $env Contains variables to bind to view
	 */
	protected function onPageLoad($param = array()) {
		if (!isset($param[1])) {
			$param[1] = 'index';
		}
		
	}
	
	public function index() {
		return($this->view('home/index.php'));
	}

	public function logout() {
		parent::logout();
		return $this->redirect("/login");
	}
	
}
