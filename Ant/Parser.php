<?php
	namespace Ant
	{
		class Parser 
		{
			private static $skips = array();

			public static function parse($view, $path = null)
			{
				$view = Inherit::extend($view,$path);
				$view = preg_replace_callback('/@skip.+?@endskip/ms', '\Ant\Parser::skip', $view);
				$view = preg_replace_callback('/@php.+?@endphp/ms', '\Ant\Parser::skip', $view);
				$view = preg_replace_callback('/{\*.*?\*}/ms', '\Ant\Parser::comment', $view);
				$view = preg_replace_callback('/{{{.+?}}}/ms', '\Ant\Parser::variable', $view);
				$view = preg_replace_callback('/{{.+?}}/ms', '\Ant\Parser::escape', $view);
				$view = preg_replace_callback('/@import.+/', '\Ant\Parser::import', $view);
				$view = preg_replace_callback('/@forelse.+/', '\Ant\Parser::forelse', $view);
				$view = preg_replace_callback('/@empty/', '\Ant\Parser::isempty', $view);
				$view = preg_replace_callback('/[ 	]+@(case|default)/', '\Ant\Parser::caseSpace', $view);
				$view = preg_replace_callback('/\B@(foreach|for|while|switch|case|default|if|elseif|else)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', '\Ant\Parser::control', $view);
				$view = preg_replace_callback('/@(break|continue|endforeach|endforelse|endfor|endwhile|endswitch|endif)/', '\Ant\Parser::endControl', $view);

				if (self::$skips) {
					$view = str_replace(
						array_keys(self::$skips),
						array_values(self::$skips),
						$view
					);

					self::$skips = array();
				}
				
				$view = str_replace(
					array('@php','@endphp', '@skip', '@endskip'),
					array('<?php','?>', '', ''),
					$view
				);

				return $view;
			}

			public static function skip($e)
			{
				if (0 === strpos($e[0],'@php')) {
					$e[0] = Helper::phpMinify($e[0]);
				}

				$uniqid = '~SKIP_' . strtoupper(str_replace('.', '', uniqid('',true))) . '_CONTENT~';
				self::$skips[$uniqid] = $e[0];

				return $uniqid;
			}

			public static function comment($e)
			{
				return '';
			}

			public static function import($e)
			{
				$view = trim(str_replace('@import', '', $e[0]));
				$view = substr($view,1,-1);
				
				$as = false;
				$pos = strpos($view, ',');

				if (false === $pos) {
					$tmpl = trim(trim($view), "'\"");
				} else {
					$tmpl = trim(trim(substr($view, 0, $pos)), "'\"");
					$as   = trim(substr($view, $pos + 1));
				}

				return '<?php echo \Ant\Ant::init()->get(\'' . $tmpl .'\')->' . ($as ? 'assign(' . \Ant\Helper::findVariable($as) . ')->' : ''). 'draw(); ?>';
			}

			public static function variable($e)
			{
				$view = trim(str_replace(array('{{{','}}}'), '', $e[0]));
				
				$view = \Ant\Helper::findVariable($view);
				$view = \Ant\Helper::findOr($view);
				
				return '<?php echo ' . $view . ';?>';
			}

			public static function escape($e)
			{
				$view = trim(str_replace(array('{{', '}}'), '', $e[0]));

				$view = \Ant\Helper::findVariable($view);
				$view = \Ant\Helper::findOr($view);

				return '<?php echo \Ant\Fn::escape(' . $view . ');?>';
			}

			public static function caseSpace($e)
			{
				return ltrim($e[0]);
			}

			public static function control($e)
			{
				$view = trim($e[0]);
				$view = ltrim(Helper::findVariable($view), '@');

				if (':' != substr($view,-1)) {
					$view .= ':';
				}

				return '<?php ' . $view . '?>';
			}

			public static function endControl($e)
			{
				$view = ltrim(trim($e[0]), '@');

				if ($view == 'endforelse') {
					$view = 'endif';
				}

				return '<?php ' . $view . '; ?>';
			}

			public static function forelse($e)
			{
				$view = $e[0];

				$m = array();
				preg_match('/(\$|->)[A-Za-z0-9_\.]+/', $view, $m);
				$parsed = Helper::parseVariable($m[0]);

				$foreach = trim(str_replace('@forelse', 'foreach', $view));
				$foreach = preg_replace_callback('/(\$|->)[A-Za-z0-9_\.]+/', function($e) {
					return Helper::parseVariable($e[0]);
				}, $foreach, 1);		

				return '<?php if(\Ant\Ant::iterable(' . $parsed . ') and count(' . $parsed .  ')): ' . $foreach . ': ?>';
			}

			public static function isempty($e)
			{
				return '<?php endforeach; else: ?>';
			}
		}
	}
?>
