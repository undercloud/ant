<?php
	namespace Ant
	{
		class Parser 
		{
			private static $skips = array();

			public static function parse($view,$path = null)
			{
				$view = \Ant\Inherit::extend($view,$path);
				$view = preg_replace_callback('/@skip.+?@endskip/ms', 'Ant\Parser::skip', $view);
				$view = preg_replace_callback('/@php.+?@endphp/ms', 'Ant\Parser::skip', $view);
				$view = preg_replace_callback('/{\*.*?\*}/ms', 'Ant\Parser::comment', $view);
				$view = preg_replace_callback('/{{{.+?}}}/', 'Ant\Parser::escape', $view);
				$view = preg_replace_callback('/{{.+?}}/', 'Ant\Parser::variable', $view);
				$view = preg_replace_callback('/@import.+/', 'Ant\Parser::import', $view);
				$view = preg_replace_callback('/@forelse.+/', 'Ant\Parser::forelse', $view);
				$view = preg_replace_callback('/@empty/', 'Ant\Parser::isempty', $view);
				$view = preg_replace_callback('/@endforelse/', 'Ant\Parser::endforelse', $view);
				$view = preg_replace_callback('/@(foreach|for|while|switch|case|break|default|continue|endforeach|endfor|endwhile|endswitch|endif|if|else).+/', 'Ant\Parser::control', $view);

				if(self::$skips){
					$view = str_replace(
						array_keys(self::$skips),
						array_values(self::$skips),
						$view
					);

					self::$skips = array();
				}
				
				$view = str_replace(
					array('@php','@endphp','@skip','@endskip'),
					array('<?php','?>','',''),
					$view
				);

				return $view;
			}

			public static function skip($e)
			{
				$uniqid = '~SKIP_' . strtoupper(str_replace('.','',uniqid('',true))) . '_CONTENT~';
				self::$skips[$uniqid] = $e[0];

				return $uniqid;
			}

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
					$t = trim(trim($v),"'\"");
				}else{
					$t = trim(trim(substr($v,0,$pos)),"'\"");
					$as = trim(substr($v,$pos + 1));
				}

				return '<?php echo \Ant::init()->get("' . $t .'")->' . ($as ? 'assign(' . Helper::parseVariable($as) . ')->' : ''). 'draw(); ?>';
			}

			public static function variable($e)
			{
				$v = trim(str_replace(array('{{','}}'), '', $e[0]));
				
				$v = \Ant\Helper::findVariable($v);
				$v = \Ant\Helper::findOr($v);
				
				return '<?php echo ' . $v . ';?>';
			}

			public static function escape($e)
			{
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