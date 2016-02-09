<?php
	namespace Ant;
	
	class Helper
	{
		const VARIABLE_REGEXP = '/(\$|->)[A-Za-z0-9_\.]+/';

		public static function realPath($fakepath)
		{
			return str_replace('.', DIRECTORY_SEPARATOR, $fakepath);
		}
		
		public static function parseVariable($e)
		{
			$exp = explode('.', $e);

			foreach ($exp as $key => $value) {
				if (0 == $key) {
					$exp[$key] = $value;
				} else if ((int)$key == 1 and $exp[0] === '$') {
					if ('plugins' == $value) {
						$exp[0] = '$this->';
						$exp[1] = 'plugins->';
					} else if ('scope' == $value) {
						$exp[$key] = 'get_defined_vars()';
						unset($exp[0]);
					} else {
						$exp[$key] = ($value != 'globals' ? '_' : '') . strtoupper($value);
					}
				} else {
					$exp[$key] = '[\'' . $value . '\']';
				}
			}

			return implode($exp);
		}

		public static function findVariable($v)
		{
			return preg_replace_callback(self::VARIABLE_REGEXP, function($l){
				return Helper::parseVariable($l[0]);
			}, $v);
		}

		public static function findOr($e)
		{
			return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', '(isset($1) and trim((string)$1)) ? $1 : $2', $e);
		}
	}
?>