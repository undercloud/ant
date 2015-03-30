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
				$view = preg_replace_callback('/[ ,	]+@(case|default)/', 'Ant\Parser::caseSpace', $view);
				$view = preg_replace_callback('/@(foreach|for|while|switch|case|default|if|else).+?\)/', 'Ant\Parser::control', $view);
				$view = preg_replace_callback('/@(break|continue|endforeach|endforelse|endfor|endwhile|endswitch|endif)/', 'Ant\Parser::endControl', $view);

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
				if(0 === strpos($e[0],'@php'))
					$e[0] = \Ant\Helper::phpMinify('<?php ' . substr($e[0],4,-7) . ';?>');

				$uniqid = '~SKIP_' . strtoupper(str_replace('.','',uniqid('',true))) . '_CONTENT~';
				self::$skips[$uniqid] = $e[0];

				return $uniqid;
			}

			public static function comment($e)
			{
				return '<?php /*'. PHP_EOL . substr($e[0],2,-2) . PHP_EOL . '*/ ?>';
			}

			public static function import($e)
			{
				$view = trim(str_replace('@import', '', $e[0]));
				$view = substr($view,1,-1);
				
				$as = false;
				$pos = strpos($view,',');

				if(false === $pos){
					$tmpl = trim(trim($view),"'\"");
				}else{
					$tmpl = trim(trim(substr($view,0,$pos)),"'\"");
					$as = trim(substr($view,$pos + 1));
				}

				return '<?php echo \Ant::init()->get("' . $tmpl .'")->' . ($as ? 'assign(' . Helper::parseVariable($as) . ')->' : ''). 'draw(); ?>';
			}

			public static function variable($e)
			{
				$view = trim(str_replace(array('{{','}}'), '', $e[0]));
				
				$view = \Ant\Helper::findVariable($view);
				$view = \Ant\Helper::findOr($view);
				
				return '<?php echo ' . $view . ';?>';
			}

			public static function escape($e)
			{
				$view = trim(str_replace(array('{{{','}}}'), '', $e[0]));

				$view = \Ant\Helper::findVariable($view);
				$view = \Ant\Helper::findOr($view);

				return '<?php echo htmlentities(' . $view . ',ENT_QUOTES,"UTF-8");?>';
			}

			public static function caseSpace($e)
			{
				return ltrim($e[0]);
			}

			public static function control($e)
			{
				$view = trim($e[0]);
				$view = ltrim(\Ant\Helper::findVariable($view),'@');

				if(':' != substr($view,-1))	
					$view .= ':';

				return '<?php ' . $view . '?>';
			}

			public static function endControl($e)
			{
				if($e[1] == 'endforelse')
					$e[1] = 'endif';

				return '<?php ' . $e[1] . '; ?>';
			}

			public static function forelse($e)
			{
				$view = $e[0];

				$m = array();
				preg_match('/\$[A-z0-9_.]+/',$view,$m);
				$parsed = \Ant\Helper::parseVariable($m[0]);

				$foreach = trim(str_replace('@forelse', 'foreach', $view));
				$foreach = preg_replace_callback('/\$[A-z0-9_.]+/', function($e){
					return \Ant\Helper::parseVariable($e[0]);
				}, $foreach, 1);		

				return '<?php if(count(' . $parsed .  ') and Ant::iterable(' . $parsed . ')): ' . $foreach . ': ?>';
			}

			public static function isempty($e)
			{
				return '<?php endforeach; else: ?>';
			}
		}
	}
?>
