<?php
namespace Ant;

/**
 * Plugin manager
 */
class Plugin
{
	private $ant;

	/**
	 * Init instance
	 *
	 * @param Ant\Ant $ant instance
	 */
	public function __construct(Ant $ant)
	{
		$this->ant = $ant;
	}

	/**
	 * Activate selected plugin
	 *
	 * @param string $plugin  plugin name
	 * @param array  $options plugin params
	 *
	 * @throws Ant\Exception
	 *
	 * @return mixed
	 */
	public function activate($plugin, array $options = array())
	{
		$path = __DIR__ . '/Plugins/' . $plugin . '.php';

		if (file_exists($path)) {

			require_once $path;

			$classname = '\\Ant\\Plugins\\' . $plugin;

			return call_user_func_array(
				array(new $classname($options), 'register'),
				array($this->ant)
			);
		} else {
			throw new Exception(
				sprintf('Plugin not exists %s', $plugin)
			);
		}

		return $this;
	}

	/**
	 * Activate plugin
	 *
	 * @param string $plugin plugin name
	 * @param call   $call   callback
	 *
	 * @return Ant\Plugin
	 */
	public function register($plugin, $call)
	{
		if (isset($this->{$plugin})) {
			throw new Exception(
				sprintf('Cannot register %s, plugin already exists', $plugin)
			);
		}

		$this->{$plugin} = $call;

		return $this;
	}

	/**
	 * Magic __call
	 *
	 * @param string $method    name
	 * @param array  $arguments arguments
	 *
	 * @throws Ant\Exception
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