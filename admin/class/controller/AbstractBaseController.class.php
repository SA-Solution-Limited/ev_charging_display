<?php

require_once 'common/Privilege.class.php';
require_once "controller/AbstractBaseController.class.php";
require_once 'common/ExceptionHelper.class.php';
require_once 'common/Helper.class.php';

abstract class AbstractBaseController extends Controller
{
	public $logger;
	public $sections = array ();
	public $template = 'template/template.php';
	public $env = array ();
	public $defaultTitle = "EV Charging Display";
	
	public function __construct() {
		session_start();

		$this->logger = Logger::getLogger('RollingLogFileAppender');
		
		$this->sections ['header'] = 'template/header.php';
		$this->sections ['menu'] = 'template/menu.php';
		$this->sections ['footer'] = 'template/footer.php';
		
		// should be defined otherwise expection thrown
		$this->env ['styles'] = "";
		$this->env ['plugins'] = "";
		$this->env ['scripts'] = "";
		$this->env ['init'] = "";
		
		$this->env['bodyclass'] = "";
		
	}
	
	public function pageLoad() {
		return $this->index();
	}
	
	protected function redirect($path) {
		header ( "Location: $path" );
	}
	
	protected function alert($message, $type = '') {
		return sprintf ( "Boxes.alert(\"%s\");", str_replace ( "\"", "'", $this->alertString ( $message, $type ) ) );
	}
	
	protected function alertString($message, $type = '') {
		$icon = '';
		switch ($type) {
			case 'success' :
				$icon = '<i class="fa fa-check-circle"></i>';
				break;
			case 'fail' :
				$icon = '<i class="fa fa-times-circle"></i>';
				break;
			default :
				break;
		}
		return sprintf ( "%s %s", $message, $icon );
	}
	
	protected function includeStyle($src, $media = 'all') {
		$this->env['styles'] .= '<link rel="stylesheet" type="text/css" media="'.$media.'" href="'.$src.'" />'."\n";
	}
	
	protected function includeScript($type = 'scripts', $src) {
		$this->env[$type] .= '<script type="text/javascript" src="'.$src.'"></script>'."\n";
	}
	
	protected function json($obj) {
		header ( 'Content-Type: application/json; charset=utf-8' );
		return json_encode ( $obj );
	}
	
	
	public function xml($obj)
	{
		header('Content-Type: text/xml');
	
		$converter = new ObjectToXML();
		echo $converter->convert($obj);
	}
	
	protected function view($view, $title = '') {
		// setup env variable
        $this->env ['title'] = ($title == '' ? $this->defaultTitle : $title);
		
		// Assign env to template
		a4p::assign ( 'env', $this->env );
		
		// template content
		$this->sections ['content'] = $view;
		
		// render template
		return template::View ( $this->template, $this->sections );
	}
	
	protected function setCurrentUser(AdminUsers $user) {
		if(!file_exists(SITE_ROOT."/tmp")){
			mkdir(SITE_ROOT."/tmp", 0777, true);
		}
		$sessionVar = a4p::Model ( 'SessionVar' );
		$sessionVar->user = serialize($user);
		$user->latestLoginAt = db::datetime(time());
		$user->SaveOrUpdate();
	}
	
}
