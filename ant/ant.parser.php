<?php
	namespace Ant
	{
		class Parser 
		{
			private static $skips = array();

			public static function parse($s)
			{
				//$s = preg_replace_callback('/{@extends.*/ms', 'Ant\Parser::xtends', $s);
				//$s = preg_replace('/{@inject.*?}.*?{@(rewrite|append|prepend)}/ms', '', $s);
				$s = preg_replace_callback('/@skip.+?@endskip/ms', 'Ant\Parser::skip', $s);
				$s = preg_replace_callback('/{\*.*?\*}/ms', 'Ant\Parser::comment', $s);
				$s = preg_replace_callback('/@?{{{.+?}}}/', 'Ant\Parser::escape', $s);
				$s = preg_replace_callback('/@?{{.+?}}/', 'Ant\Parser::variable', $s);
				$s = preg_replace_callback('/@import.+/', 'Ant\Parser::import', $s);
				$s = preg_replace_callback('/@forelse.+/', 'Ant\Parser::forelse', $s);
				$s = preg_replace_callback('/@empty/', 'Ant\Parser::isempty', $s);
				$s = preg_replace_callback('/@endforelse/', 'Ant\Parser::endforelse', $s);
				$s = preg_replace_callback('/@(foreach|for|while|switch|case|break|default|continue|endforeach|endfor|endwhile|endswitch|endif|if|else).+/', 'Ant\Parser::control', $s);

				$s = str_replace('@{{','{{', $s);

				if(self::$skips){
					$s = str_replace(
						array_keys(self::$skips),
						array_values(self::$skips),
						$s
					);

					self::$skips = array();
				}
				
				$s = str_replace(
					array('@php','@endphp','@skip','@endskip'),
					array('<?php','?>','',''),
					$s
				);

				return $s;
			}

			public static function skip($e)
			{
				$uniqid = '~SKIP_' . strtoupper(uniqid()) . '_CONTENT~';
				self::$skips[$uniqid] = $e[0];

				return $uniqid;
			}

			/*public static function xtends($e)
			{
				if(false == function_exists('resolve_chain')){
					function resolve_chain($tmpl){
						$name = array();
						preg_match('/{@extends.+?}/',$tmpl,$name);

						if(!$name)
							return false;

						$name = $name[0];
						$name = trim($name,'{}');
						$name = str_replace('@extends', '', $name);
						$name = trim($name);
						$name = substr($name,1,-1);

						$path = \Ant::settings('path') . DIRECTORY_SEPARATOR  . str_replace('.', DIRECTORY_SEPARATOR , $name) . '.php';

						$io = IO::init()->in($path);
						$c = $io->get();
						$io->out();

						return $c;
					}
				}

				$tmpl = $local = $e[0];

				$chain = array($local);
				while(true){	
					$local = resolve_chain($local);
					if(false === $local)
						break;
					else
						$chain[]= $local;  
				}

				$chain = array_reverse($chain);
				$tmpl = implode('',$chain);

				unset($local);

				$inject = array();
				preg_match_all('/{@inject.*?}.*?{@(rewrite|append|prepend)}/ms',$tmpl,$inject);

				$map = array();
				if(isset($inject[0])){
					foreach($inject[0] as $k=>$s){
						$m = array();
						preg_match('/{@inject.*?}/',$s,$m);

						$name = trim(str_replace('@inject','',$m[0]),' {()}');

						$s = preg_replace('/{@inject\s*?\(\s*?' . $name . '\s*?\)\s*?}/','',$s);
						$s = str_replace('{@' . $inject[1][$k] . '}','',$s);

						$map[] = array(
							$name,
							$s,
							$inject[1][$k]
						);
					}
				}

				foreach($map as $key=>$value){
					$tmpl = preg_replace_callback(
						'/{@section\s*?\(\s*?' . $value[0] . '\s*?\)\s*?}.*?{@end}/ms',
						function($e)use($value){
							switch($value[2]){
								case 'prepend':
									return '{@section(' . $value[0] . ')}' . $value[1] . $e[0] . '{@end}';
								break;

								case 'append':
									return '{@section(' . $value[0] . ')}' . $e[0] . $value[1] . '{@end}';
								break;

								case 'rewrite':
									return '{@section(' . $value[0] . ')}' . $value[1] . '{@end}';
								break;
							}
						},
						$tmpl
					);
				}

				$tmpl = str_replace($chain,'',$tmpl);
				$tmpl = preg_replace('/{@(section|inject).*?}/','', $tmpl);
				$tmpl = preg_replace('/{@(rewrite|append|prepend|end)}/','',$tmpl);

				return $tmpl;
			}*/

			public static function comment($e)
			{
				return '<?php /*' . $e[0] . '*/ ?>';
			}

			public static function import($e)
			{
				$v = trim(str_replace('@import', '', $e[0]));
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
				if($e[0][0] == '@')
					return $e[0];

				$v = trim(str_replace(array('{{','}}'), '', $e[0]));
				
				$v = \Ant\Helper::findVariable($v);
				$v = \Ant\Helper::findOr($v);
				
				return '<?php echo ' . $v . ';?>';
			}

			public static function escape($e)
			{
				if($e[0][0] == '@')
					return $e[0];

				$v = trim(str_replace(array('{{{','}}}'), '', $e[0]));

				$v = \Ant\Helper::findVariable($v);
				$v = \Ant\Helper::findOr($v);

				return '<?php echo htmlentities(' . $v . ',ENT_QUOTES,"UTF-8");?>';
			}

			public static function control($e)
			{
				$v = trim(ltrim($e[0],'@'));

				$v = \Ant\Helper::findVariable($v);

				if(
					0 === strpos($v, 'if') ||
					0 === strpos($v, 'else') ||
					0 === strpos($v, 'for') ||
					0 === strpos($v, 'while') ||
					0 === strpos($v, 'switch') ||
					0 === strpos($v, 'case') ||
					0 === strpos($v, 'default')
				){
					if(':' != substr($v,-1))	
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

				$foreach = trim(str_replace('@forelse', 'foreach', $v));

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