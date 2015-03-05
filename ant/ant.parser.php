<?php
	namespace Ant
	{
		class Parser 
		{
			public static function comment($e)
			{
				return '';
			}

			public static function import($e)
			{
				$v = $e[0];
				
				$v = str_replace('{@import(', '', $v);
				$v = str_replace(')}', '', $v);
				$v = trim($v);

				$exp = explode(',',$v);

				return '<?= \Ant::init()->get("' . $exp[0] .'")->' . (isset($v[1]) ? 'assign(' . Helper::parseVariable($exp[1]) . ')->' : ''). 'draw(); ?>';
			}

			public static function variable($e)
			{
				$v = $e[0];

				$v = str_replace('{{', '', $v);
				$v = str_replace('}}', '', $v);
				$v = trim($v);

				$v = Helper::parseVariable($v);
				
				return '<?php echo ' . $v . ';?>';
			}

			public static function escape($e)
			{
				$v = $e[0];

				$v = str_replace('{{{', '', $v);
				$v = str_replace('}}}', '', $v);
				$v = trim($v);

				$v = Helper::parseVariable($v);

				return '<?php echo htmlentities(' . $v . ',ENT_QUOTES,"UTF-8");?>';
			}

			public static function control($e)
			{
				$v = $e[0];

				$v = str_replace('{@', '', $v);
				$v = str_replace('}', '', $v);
				$v = trim($v);

				$v = preg_replace_callback('/\$[A-z0-9_.]+/', function($l){
					return Helper::parseVariable($l[0]);
				},$v);

				if(
					0 === strpos($v, 'if') ||
					0 === strpos($v, 'else') ||
					0 === strpos($v, 'for') ||
					0 === strpos($v, 'while') ||
					0 === strpos($v, 'switch')
				){
					$v .= ':';
				}else{
					$v .= ';';
				}

				return '<?php ' . $v . '?>';
			}
		}
	}
?>