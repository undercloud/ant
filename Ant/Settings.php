<?php
namespace Ant;

class Settings
{
	private $stack = array();

	/**
	 * Init options stack
	 *
	 * @param array $stack of options
	 */
	public function __construct(array $stack = array())
	{
		if (false == isset($stack['view'])) {
			throw new Exception('View path is not defined');
		}

		if (false == @is_readable($stack['view'])) {
			throw new Exception(
				sprintf('View path %s is not available', $stack['view'])
			);
		}

		if (false == isset($stack['cache'])) {
			throw new Exception('Cache path is not defined');
		}

		if (false == @is_readable($stack['cache']) or false == @is_writeable($stack['cache'])) {
			throw new Exception(
				sprintf('Cache path %s is not available', $stack['cache'])
			);
		}

		if (false == isset($stack['extension'])) {
			$stack['extension'] = 'ant';
		}

		if (false == isset($stack['debug'])) {
			$stack['debug'] = false;
		}

		if (false == isset($stack['freeze'])) {
			$stack['freeze'] = false;
		}

		$stack['cache'] = rtrim($stack['cache'], ' 	\\/');
		$stack['view']  = rtrim($stack['view'], ' 	\\/');

		if (isset($stack['logic'])) {
			$stack['logic'] = rtrim($stack['logic'], ' 	\\/');
		} else {
			$stack['logic'] = '';
		}

		$this->stack = $stack;
	}

	/**
	 * Write option
	 *
	 * @param mixed $name  option
	 * @param mixed $value option
	 *
	 * @return Ant\Settings
	 */
	public function set($name, $value)
	{
		$this->stack[$name] = $value;

		return $this;
	}

	/**
	 * Read option, if $name is false return all settings
	 *
	 * @param mixed $name of option
	 *
	 * @return mixed
	 */
	public function get($name = false)
	{
		return (($name != false) ? $this->stack[$name] : $this->stack);
	}
}
?>