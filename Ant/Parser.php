<?php
	namespace Ant;

	class Parser
	{
		private static $rules    = array();
		private static $skips    = array();
		private static $forstack = array();

		public static function rule($rx, $call)
		{
			self::$rules[$rx] = $call;
		}

		public static function parse($view, $path = null)
		{
			foreach (self::$rules as $rx => $call) {
				$view = preg_replace_callback($rx, $call, $view);
			}

			$view = Inherit::extend($view, $path);
			$view = preg_replace_callback('/@skip.+?@endskip/ms', '\Ant\Parser::skip', $view);
			$view = preg_replace_callback('/@php.+?@endphp/ms', '\Ant\Parser::skip', $view);
			$view = preg_replace_callback('/{{--.*?--}}/ms', '\Ant\Parser::comment', $view);
			$view = preg_replace_callback('/(\x5c)?{{{.+?}}}/ms', '\Ant\Parser::variable', $view);
			$view = preg_replace_callback('/(\x5c)?{{.+?}}/ms', '\Ant\Parser::escape', $view);
			$view = preg_replace_callback('/\B@(import|widget).+/', '\Ant\Parser::import', $view);
			$view = preg_replace_callback('/\B@(css|js).+/', '\Ant\Parser::shortcut', $view);
			$view = preg_replace_callback('/[\s\t]+@(case|default)/', '\Ant\Parser::caseSpace', $view);
			$view = preg_replace_callback('/\B@(forelse|foreach|for|while|switch|case|default|if|elseif|else|unless|each)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', '\Ant\Parser::control', $view);
			$view = preg_replace_callback('/\B@(empty|break|continue|endforeach|endforelse|endfor|endwhile|endswitch|endif|endunless)/', '\Ant\Parser::endControl', $view);
			$view = preg_replace('/\B::/', '$this->', $view);

			if (self::$skips) {
				$view = str_replace(
					array_keys(self::$skips),
					array_values(self::$skips),
					$view
				);

				self::$skips = array();
			}

			$view = str_replace(
				array('@php', '@endphp', '@skip', '@endskip'),
				array('<?php', '?>', '', ''),
				$view
			);

			return $view;
		}

		public static function skip($e)
		{
			$uniqid = '~SKIP_' . strtoupper(str_replace('.', '', uniqid('', true))) . '_CONTENT~';
			self::$skips[$uniqid] = $e[0];

			return $uniqid;
		}

		public static function comment($e)
		{
			return '';
		}

		public static function each($view, $collection, $item = 'item', array $scope = array())
		{
			if (Fn::iterable($collection)) {
				$tmpl = $tmpl = Ant::init()->fromFile($view);

				foreach ($collection as $single) {
					$scope[$item] = $single;

					echo $tmpl->assign($scope)->draw();
				}
			}
		}

		public static function import($e)
		{
			$view = Helper::clean(array('@import', '@widget'), $e[0]);
			$view = Helper::findVariable($view);

			return '<?php echo \Ant\Ant::view' . $view. '; ?>';
		}

		public static function shortcut($e)
		{
			return '<?php echo \Ant\Fn::' . ltrim($e[0], '@') . '; ?>';
		}

		public static function variable($e)
		{
			if (isset($e[1]) and $e[1] === '\\') {
				return ltrim($e[0], '\\');
			}

			$view = Helper::clean(array('{{{','}}}'), $e[0]);

			$view = Helper::findVariable($view);
			$view = Helper::findOr($view);

			return '<?php echo ' . $view . '; ?>';
		}

		public static function escape($e)
		{
			if (isset($e[1]) and $e[1] === '\\') {
				return ltrim($e[0], '\\');
			}

			$view = Helper::clean(array('{{', '}}'), $e[0]);

			$view = Helper::findVariable($view);
			$view = Helper::findOr($view);

			return '<?php echo \Ant\Fn::escape(' . $view . '); ?>';
		}

		public static function caseSpace($e)
		{
			return ltrim($e[0]);
		}

		public static function control($e)
		{
			$op = trim($e[1]);

			if ($op == 'each') {
				$view = 'Ant\Parser::each' . Helper::findVariable($e[3]);
			} else if ($op == 'unless') {
				$view = 'if(!' . Helper::findVariable($e[3]) . ')';
			} else if ($op == 'forelse' or $op == 'foreach') {
				$m = array();
				preg_match(Helper::VARIABLE_REGEXP, $e[4], $m);
				$parsed = Helper::parseVariable($m[0]);

				$view = '';
				if ($op == 'forelse') {
					$view = 'if(\Ant\Fn::iterable(' . $parsed . ') and \Ant\Fn::count(' . $parsed .  ')): ';
				}

				$view .= $parsed . ' = new \Ant\StateIterator(' . $parsed . '); ';
				$view .= 'foreach' . Helper::findVariable($e[3]);

				self::$forstack[] = $parsed;
			} else {
				$view = $op . (isset($e[3]) ? Helper::findVariable($e[3]) : '');
			}

			if ('each' != $op and ':' != substr($view, -1)) {
				$view .= ':';
			}

			return '<?php ' . $view . ' ?>';
		}

		public static function endControl($e)
		{
			$op = trim($e[1]);

			if ($op == 'endforelse' or $op == 'endunless') {
				$view = 'endif';
			} else if ($op == 'empty' or $op == 'endforeach') {
				$restore = array_pop(self::$forstack);
				$restore = $restore . ' = ' . $restore . '->restore()';

				$view = 'endforeach; ' . $restore . '; ';

				if ($op == 'empty') {
					$view .= ' else:';
				}
			} else {
				$view = $op;
			}

			if ($op != 'empty') {
				$view .= ';';
			}

			return '<?php ' . $view . ' ?>';
		}
	}
?>