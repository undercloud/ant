<?php
	namespace Ant
	{
		class Helper
		{
			public static function realPath($fakepath)
			{
				return str_replace('.', DIRECTORY_SEPARATOR, $fakepath);
			}

			public static function parseVariable($e)
			{
				$exp = explode('.',$e);

				foreach ($exp as $key => $value) {
					if(0 == $key){
						$exp[$key] = $value;
					}else if($key == 1 and $exp[0] === '$'){
						$exp[$key] = '_' . strtoupper($value);
					}else{
						$exp[$key] = '[\'' . $value . '\']';
					}
				}

				return implode('',$exp);
			}

			public static function findVariable($v)
			{
				return preg_replace_callback('/\$[A-Za-z0-9_\.]+/', function($l){
					return Helper::parseVariable($l[0]);
				},$v);
			}

			public static function findOr($e)
			{
				return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', '(isset($1) and $1) ? $1 : $2', $e);
			}

			public static function compress($buffer)
			{
				$search = array(
					'/<!--.*-->/ms', // strip comments
					'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
					'/[^\S ]+\</s',  // strip whitespaces before tags, except space
					'/(\s)+/s'       // shorten multiple whitespace sequences
				);

				$replace = array(
					'',
					'>',
					'<',
					'\\1'
				);

				return preg_replace($search, $replace, $buffer);
			}

			public static function phpMinify($buffer)
			{
				$buffer = preg_replace('~//.*~', '', $buffer);
				$buffer = preg_replace('~/\*.*?\*/~ms', '', $buffer);
				$buffer = preg_replace('/\s+/',' ',$buffer);
				$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
				
				return $buffer;
			}
		}
	}
?>