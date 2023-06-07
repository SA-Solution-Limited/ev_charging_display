<?php

class template
{
	private static $sections = array();
	private static $env = array();

	public static function view($template, $sections)
	{
		if (a4p::isAjaxCall())
			ob_start();
		
		self::$sections = $sections;

		global $controller;
		foreach (a4p::$viewvariables as $name => &$value)
			$$name = &$value;
		require SITE_ROOT . "/view/" . $template;
		
		if (a4p::isAjaxCall()) {
			$buffer = ob_get_clean();
			return a4p::postProcess($buffer);
		}
	}

	public static function section($section)
	{
		if (isset(self::$sections[$section]))
		{
			global $controller;
			foreach (a4p::$viewvariables as $name => &$value)
				$$name = &$value;
			require SITE_ROOT . "/view/" . self::$sections[$section];
		}
	}
}
