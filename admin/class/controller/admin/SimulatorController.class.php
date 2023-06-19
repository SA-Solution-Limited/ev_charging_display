<?php

use PSpell\Config;

require_once('controller/AbstractAdminController.class.php');
require_once('service/CMSService.class.php');

class SimulatorController extends AbstractAdminController
{

	private $service;
	public function __construct()
	{
		parent::__construct();
		$this->service = new CMSService();
	}
	/**
	 * Page load handler
	 * @param array $env Contains variables to bind to view
	 */
	protected function onPageLoad($param = null) {
		if (!isset($param[1])) {
			$param[1] = 'index';
		}

		if(method_exists($this, $param[1])){
			return $this->{$param[1]}();
		}else{
			return $this->index();
		}
	}
	
	public function index() {
		require_once "entity/ChargingStatus.class.php";
		$status = ChargingStatus::findById(1);
		a4p::assign("simulator", $status);

		$this->env ['scripts'] .= <<<js

js;
		$this->env ['init'] .= <<<js
		$("input, select").on('change', function(){
			$.ajax({
				method: "POST",
				url: "/api/updateStatus/?"+$('form').serialize(),
			  })
		})
js;
		return($this->view('admin/simulator/index.php'));
	}

	
}
