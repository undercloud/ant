<?php

namespace Ant;

/**
 * Plugin manager
 *
 * @package Ant
 */
class Plugin
{
	/**
	 * Activate selected plugin
	 *
	 * @param Ant\Ant $ant     instance
	 * @param string  $plugin  plugin name
	 * @param array   $options plugin params
	 *
	 * @throws Ant\Exception
	 *
	 * @return mixed
	 */
	public function activate(Ant $ant, $plugin, array $options = array())
	{
		$path = __DIR__ . '/Plugins/' . $plugin . '.php';

		if (file_exists($path)) {

			require_once $path;

			$classname = '\\Ant\\Plugins\\' . $plugin;

			return call_user_func_array(
				array(new $classname($options), 'register'),
				array($ant)
			);
		} else {
			throw new Exception(
				sprintf('Plugin not exists %s', $plugin)
			);
		}
	}

	/**
	 * Magic __call
	 *
	 * @param string $method    method name
	 * @param array  $arguments call arguments
	 *
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		if (isset($this->{$method}) and is_callable($this->{$method})) {
			return call_user_func_array(
				$this->{$method},
				$arguments
			);
		} else {
			throw new \Exception(
				sprintf(
					"Fatal error: Call to undefined method %s::%s",
					get_class($this),
					$method
				)
			);
		}
	}
}
?>