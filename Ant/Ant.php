<?php
	/*
		Awesome New Templates
	*/

	namespace Ant;

	require_once __DIR__ . '/Parser.php';
	require_once __DIR__ . '/Helper.php';
	require_once __DIR__ . '/IO.php';
	require_once __DIR__ . '/Fn.php';
	require_once __DIR__ . '/Cache.php';
	require_once __DIR__ . '/Exception.php';
	require_once __DIR__ . '/Inherit.php';
	require_once __DIR__ . '/Plugin.php';
	require_once __DIR__ . '/Plugins/PluginBase.php';

	class Ant
	{
		const MODE_FILE   = 0xFF;
		const MODE_STRING = 0x00;

		private $mode;

		private static $cache_obj;
		private static $settings = array();

		private $prevent_parent = false;
		private static $global_events = array();
		private $local_events = array();

		private $assign     = array();
		private $tmpl_path  = '';
		private $cache_path = '';
		private $logic_path = '';
		private $string     = '';

		private static $fn = array();
		private static $plugin;
		private static $rules = array();
		
		public static function init()
		{
			return new static();
		}

		public function setup($s)
		{
			if (false == isset($s['view'])) {
				throw new Exception('View path is not defined');
			}
			
			if (false == @is_readable($s['view'])) {
				throw new Exception(
					sprintf('View path %s is not available', $s['view'])
				);
			}
				
			if (false == isset($s['cache'])) {
				throw new Exception('Cache path is not defined');
			}

			if (false == @is_readable($s['cache']) or false == @is_writeable($s['cache'])) {
				throw new Exception(
					sprintf('Cache path %s is not available', $s['cache'])
				);
			}

			if (false == isset($s['extension'])) $s['extension'] = 'php';
			if (false == isset($s['debug']))     $s['debug']     = false;
			if (false == isset($s['freeze']))    $s['freeze']    = false;

			$s['cache'] = rtrim($s['cache'], ' 	\\/');
			$s['view']  = rtrim($s['view'], ' 	\\/');

			if (isset($s['logic'])) {
				$s['logic'] = rtrim($s['logic'], ' 	\\/');
			} else {
				$s['logic'] = '';
			}

			self::$settings  = $s;
			self::$cache_obj = new Cache($s['cache']);

			self::$plugin = new Plugin;

			return $this;
		}

		public static function settings($name = false)
		{	
			return (($name != false) ? self::$settings[$name] : self::$settings);
		}

		public function bind($event, $call)
		{
			self::$global_events[$event][] = $call;

			return $this;
		}

		public function on($event, $call)
		{
			$this->local_events[$event][] = $call;

			return $this;
		}

		public function preventParentEvent($prevent)
		{
			$this->prevent_parent = $prevent;

			return $this;
		}

		public function fire($event, $string)
		{
			$queue = array();

			if (isset($this->local_events[$event])) {
				$queue = array_merge($queue, $this->local_events[$event]);
			}

			if (false === $this->prevent_parent) {
				if (isset(self::$global_events[$event])) {
					$queue = array_merge($queue, self::$global_events[$event]);
				}
			}

			foreach ($queue as $call) {
				$string = call_user_func_array($call, array($string));
			}

			return $string;
		}

		public function share($name, $call)
		{
			if (array_key_exists($name, self::$fn)) {
				throw new Exception(
					sprintf('Cannot register %s', $name)
				);
			}

			self::$fn[$name] = $call;

			return $this;
		}

		public function register($plugin, $call)
		{
			if (isset(self::$plugin->{$plugin})) {
				throw new Exception(
					sprintf('Cannot register %s, plugin already exists', $plugin)
				);
			}

			self::$plugin->{$plugin} = $call;

			return $this;
		}

		public function activate($plugin, array $options = array())
		{
			self::$plugin->activate($this, $plugin, $options);

			return $this;
		}

		public static function getRule()
		{
			return self::$rules;
		}

		public function rule($rx, $call)
		{
			self::$rules[$rx] = $call;

			return $this;
		}

		public static function getPlugin()
		{
			return self::$plugin;
		}

		public function __get($key)
		{
			switch ($key) {
				case 'plugin':
					return self::$plugin;

				default:
					throw new \Exception(
						sprintf('Undefined property %s', $key)
					);
			}
		}

		public function __call($name, $arguments)
		{
			return self::call($name, $arguments);
		}

		public static function __callStatic($name, $arguments)
		{
			return self::call($name, $arguments);
		}

		private static function call($name, $arguments)
		{
			if (method_exists('\Ant\Fn',$name)) {
				return call_user_func_array('\Ant\Fn::' . $name, $arguments);
			} else if (array_key_exists($name, self::$fn) and is_callable(self::$fn[$name])) {
				return call_user_func_array(self::$fn[$name], $arguments);
			} else {
				throw new Exception(
					sprintf('Undeclared method \\Ant\\Ant::%s', $name)
				);
			}
		}

		public static function getCache()
		{
			return self::$cache_obj;
		}

		public function logic($path)
		{
			$this->logic_path = self::$settings['logic'] . '/' . Helper::realPath($path) . '.php';
			
			return $this;
		}

		public function has($path)
		{
			$path = self::$settings['view'] . '/' . Helper::realPath($path) . '.' . self::$settings['extension'];
			
			return file_exists($path);
		}

		public function get($path)
		{
			$this->mode = self::MODE_FILE;

			$this->tmpl_path  = self::$settings['view']  . '/' . Helper::realPath($path) . '.' . self::$settings['extension'];
			$this->cache_path = self::$settings['cache'] . '/' . str_replace('/', '.', $path) . '.php';
			$this->logic_path = self::$settings['logic'] . '/' . Helper::realPath($path) . '.php';

			return $this;
		}

		public function fromString($s)
		{
			$this->mode   = self::MODE_STRING;
			$this->string = $s;

			return $this;
		}

		public function fromFile($path)
		{
			$full_path        = self::$settings['view']  . '/' . Helper::realPath($path) . '.' . self::$settings['extension'];
			$this->logic_path = self::$settings['logic'] . '/' . Helper::realPath($path) . '.php';
			
			$content = IO::init()->in($full_path)->get();

			return $this->fromString($content);
		}

		public function assign(array $data = array())
		{
			$this->assign = $data;

			return $this;
		}

		public function draw()
		{
			switch ($this->mode) {
				case self::MODE_STRING:
					$s = $this->fire(
						'build',
						Parser::parse(
							$this->fire(
								'prepare',
								$this->string
							)
						)
					);

					ob_start();
					extract($this->assign);

					if ($this->logic_path and file_exists($this->logic_path)) {
						require $this->logic_path;
					}

					eval(' ?>' . $s . '<?php ');
					$echo = ob_get_contents();
					ob_end_clean();

					return $this->fire('exec', $echo);

				case self::MODE_FILE:
					if (false === self::$settings['freeze']) {
						if (true === self::$settings['debug'] or false == self::$cache_obj->check($this->tmpl_path)) {
							$io = IO::init()->in($this->tmpl_path);

							$s = $this->fire(
								'build',
								Parser::parse(
									$this->fire(
										'prepare',
										$io->get()
									),
									$this->tmpl_path
								)
							);

							$io->out()
							   ->in($this->cache_path)
							   ->set($s)
							   ->out();
						}
					}

					ob_start();
					extract($this->assign);

					if (file_exists($this->logic_path)) {
						require $this->logic_path;
					}

					require $this->cache_path;
					$echo = ob_get_contents();
					ob_end_clean();

					return $this->fire('exec', $echo);
			}
		}
	}
?>