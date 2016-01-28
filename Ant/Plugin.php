<?php
	namespace Ant;

	class Plugin
	{
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

		public function __call($method, $arguments)
		{
			if (isset($this->{$method}) and is_callable($this->{$method})) {
				return call_user_func_array(
					$this->{$method},
					$arguments
				);
			} else {
				throw new \Exception(
					sprintf("Fatal error: Call to undefined method %s::%s", get_class($this), $method)
				);
			}
		}
	}
?>