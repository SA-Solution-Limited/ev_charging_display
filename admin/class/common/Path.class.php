<?php
class Path
{
	public static function Combine()
	{
		$paths = func_get_args();
		$full = '';
		foreach ($paths as $path)
		{
			if ($full != '')
				$full .= DIRECTORY_SEPARATOR;
			$full .= $path;
		}

		return $full;
	}
}