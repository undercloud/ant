<?php
namespace Ant;

/**
 * Parser engine
 */
class Parser
{
	private $ant;
	private static $rules    = array();
	private static $skips    = array();
	private static $forstack = array();

	public function __construct(Ant $ant)
	{
		$this->ant = $ant;
	}

	/**
	 * Add custom rule
	 *
	 * @param string $rx   regular expression
	 * @param mixed  $call callback
	 *
	 * @return void
	 */
	public static function rule($rx, $call)
	{
		self::$rules[$rx] = $call;
	}

	/**
	 * Template parser
	 *
	 * @param string $view raw template
	 * @param mixed  $path path for inheritance
	 *
	 * @return string
	 */
	public function parse($view, $path = null)
	{
		foreach (self::$rules as $rx => $call) {
			$view = preg_replace_callback($rx, $call, $view);
		}

		$inherit = new Inherit($this->ant);

		$view = $inherit->extend($view, $path);
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
		$view = preg_replace('/\B::/', '$this->ant->', $view);

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

	/**
	 * Skip parse
	 *
	 * @param array $e stack
	 *
	 * @return string
	 */
	public static function skip($e)
	{
		$uniqid = '~SKIP_' . strtoupper(str_replace('.', '', uniqid('', true))) . '_CONTENT~';
		self::$skips[$uniqid] = $e[0];

		return $uniqid;
	}

	/**
	 * Parse comment
	 *
	 * @param array $e stack
	 *
	 * @return void
	 */
	public static function comment($e)
	{
		return '';
	}

	/**
	 * Parse each
	 *
	 * @param string $view       template
	 * @param mixed  $collection collection
	 * @param string $item       variable name
	 * @param array  $scope      variables in scope
	 *
	 * @return void
	 */
	public static function each($view, $collection, $item = 'item', array $scope = array())
	{
		if (Fn::iterable($collection)) {
			$tmpl = Ant::init()->fromFile($view);

			foreach ($collection as $single) {
				$scope[$item] = $single;

				echo $tmpl->assign($scope)->draw();
			}
		}
	}

	/**
	 * Parse import
	 *
	 * @param array $e stack
	 *
	 * @return string
	 */
	public static function import($e)
	{
		$view = Helper::clean(array('@import', '@widget'), $e[0]);
		$view = Helper::findVariable($view);

		return '<?php echo \Ant\Ant::view' . $view. '; ?>';
	}

	/**
	 * Parse shortcut
	 *
	 * @param array $e stack
	 *
	 * @return string
	 */
	public static function shortcut($e)
	{
		return '<?php echo \Ant\Fn::' . ltrim($e[0], '@') . '; ?>';
	}

	/**
	 * Parse variable
	 *
	 * @param array $e stack
	 *
	 * @return string
	 */
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

	/**
	 * Escape value
	 *
	 * @param array $e stack
	 *
	 * @return string
	 */
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

	/**
	 * Fix extra spaces in switch
	 *
	 * @param array $e stack
	 *
	 * @return string
	 */
	public static function caseSpace($e)
	{
		return ltrim($e[0]);
	}

	/**
	 * Parse control structure
	 *
	 * @param array $e stack
	 *
	 * @return string
	 */
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

	/**
	 * Parse control structure
	 *
	 * @param array $e stack
	 *
	 * @return string
	 */
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