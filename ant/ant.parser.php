<?php
	namespace Ant
	{
		class Parser 
		{
			public static function parse($s)
			{
				$s = preg_replace_callback('/{@extends.*/ms', 'Ant\Parser::xtends', $s);
				$s = preg_replace('/{@section.*?}.*?{@(rewrite|append|prepend)}/ms', '', $s);
				$s = preg_replace_callback('/{\*.*\*}/ms', 'Ant\Parser::comment', $s);
				$s = preg_replace_callback('/{{{.+?}}}/', 'Ant\Parser::escape', $s);
				$s = preg_replace_callback('/{{.+?}}/', 'Ant\Parser::variable', $s);
				$s = preg_replace_callback('/{@import.+?}/', 'Ant\Parser::import', $s);
				$s = preg_replace_callback('/{@forelse.+?}/', 'Ant\Parser::forelse', $s);
				$s = preg_replace_callback('/{@empty}/', 'Ant\Parser::isempty', $s);
				$s = preg_replace_callback('/{@endforelse}/', 'Ant\Parser::endforelse', $s);
				$s = preg_replace_callback('/{@.+?}/ms', 'Ant\Parser::control', $s);

				return $s;
			}

			public static function xtends($e)
			{
				$v = $e[0];

				$name = array();
				preg_match('/{@extends.+?}/',$v,$name);

				$name = $name[0];
				$name = trim($name,'{}');
				$name = str_replace('@extends', '', $name);
				$name = trim($name);
				$name = substr($name,1,-1);

				$path = \Ant::settings('path') . "/" . str_replace('.', '/', $name) . '.php';

				$io = IO::init()->in($path);
				$c = $io->get();
				$io->out();

				$sections = array();
				preg_match_all('/{@section.*?}.*?{@(rewrite|append|prepend)}/ms',$v,$sections);

				$map = array();
				if(isset($sections[0])){
					foreach($sections[0] as $k=>$s){
						$m = array();
						preg_match('/{@section.*?}/',$s,$m);

						$name = trim(str_replace('@section','',$m[0]),' {()}');

						$s = preg_replace('/{@section\s*?\(\s*?' . $name . '\s*?\)\s*?}/','',$s);
						$s = str_replace('{@' . $sections[1][$k] . '}','',$s);

						$map[] = array(
							$name,
							$s,
							$sections[1][$k]
						);
					}
				}
				

				foreach($map as $key=>$value){
					$c = preg_replace_callback(
						'/{@section\s*?\(\s*?' . $value[0] . '\s*?\)\s*?}(.*)?{@end}/ms',
						function($e)use($value){
							switch($value[2]){
								case 'prepend':
									return '{@section(' . $value[0] . ')}' . $value[1] . $e[1] . '{@end}';
								break;

								case 'append':
									return '{@section(' . $value[0] . ')}' . $e[1] . $value[1] . '{@end}';
								break;

								case 'rewrite':
									return '{@section(' . $value[0] . ')}' . $value[1] . '{@end}';
								break;
							}
						},
						$c
					);
				}

				$c = preg_replace('/{@section.*?}/','', $c);
				$c = preg_replace('/{@(rewrite|append|prepend|end)}/','',$c);

				return $c;
			}

			public static function comment($e)
			{
				return '';
			}

			public static function import($e)
			{
				$v = $e[0];
				
				$v = trim($v,'{}');
				$v = str_replace('@import', '', $v);
				$v = trim($v);
				$v = substr($v,1,-1);
				
				$as = false;
				$pos = strpos($v,',');

				if(false === $pos){
					$t = trim($v);
				}else{
					$t = trim(substr($v,0,$pos));
					$as = trim(substr($v,$pos + 1));
				}

				return '<?php echo \Ant::init()->get("' . $t .'")->' . ($as ? 'assign(' . Helper::parseVariable($as) . ')->' : ''). 'draw(); ?>';
			}

			public static function variable($e)
			{
				$v = $e[0];

				$v = str_replace('{{', '', $v);
				$v = str_replace('}}', '', $v);
				$v = trim($v);

				$v = \Ant\Helper::findVariable($v);
				$v = \Ant\Helper::findOr($v);
				
				return '<?php echo ' . $v . ';?>';
			}

			public static function escape($e)
			{
				$v = $e[0];

				$v = str_replace('{{{', '', $v);
				$v = str_replace('}}}', '', $v);
				$v = trim($v);

				$v = \Ant\Helper::findVariable($v);
				$v = \Ant\Helper::findOr($v);

				return '<?php echo htmlentities(' . $v . ',ENT_QUOTES,"UTF-8");?>';
			}

			public static function control($e)
			{
				$v = $e[0];

				$v = substr($v,2,-1);
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

			public static function forelse($e)
			{
				$v = $e[0];

				$m = array();
				preg_match('/\$[A-z0-9_.]+/',$v,$m);

				$foreach = str_replace('{@forelse', 'foreach', $v);
				$foreach = str_replace('}', '', $foreach);

				$parsed = \Ant\Helper::parseVariable($m[0]);

				return '<?php if(count(' . $parsed .  ') and Ant::iterable(' . $parsed . ')): ' . $foreach . ': ?>';
			}

			public static function isempty($e)
			{
				return '<?php endforeach; else: ?>';
			}

			public static function endforelse($e)
			{
				return '<?php endif; ?>';
			}
		}
	}
?>