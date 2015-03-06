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
				
				$as = false;
				$pos = strpos($v,',');

				if(false === $pos){
					$t = $v;
				}else{
					$t = substr($v,0,$pos);
					$as = substr($v,$pos + 1);
				}

				return '<?= \Ant::init()->get("' . $t .'")->' . ($as ? 'assign(' . Helper::parseVariable($as) . ')->' : ''). 'draw(); ?>';
			}

			public static function variable($e)
			{
				$v = $e[0];

				$v = str_replace('{{', '', $v);
				$v = str_replace('}}', '', $v);
				$v = trim($v);

				$v = \Ant\Helper::findVariable($v);
				
				return '<?php echo ' . $v . ';?>';
			}

			public static function escape($e)
			{
				$v = $e[0];

				$v = str_replace('{{{', '', $v);
				$v = str_replace('}}}', '', $v);
				$v = trim($v);

				$v = \Ant\Helper::findVariable($v);

				return '<?php echo htmlentities(' . $v . ',ENT_QUOTES,"UTF-8");?>';
			}

			public static function control($e)
			{
				$v = $e[0];

				$v = str_replace('{@', '', $v);
				$v = str_replace('}', '', $v);
				$v = trim($v);

				$v = \Ant\Helper::findVariable($v);

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