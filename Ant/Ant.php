<?php
namespace Ant;

require_once __DIR__ . '/Parser.php';
require_once __DIR__ . '/Helper.php';
require_once __DIR__ . '/IO.php';
require_once __DIR__ . '/Fn.php';
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/Inherit.php';
require_once __DIR__ . '/StateIterator.php';
require_once __DIR__ . '/Plugin.php';
require_once __DIR__ . '/Plugins/Base.php';

/**
 * Main class
 */
class Ant
{
	const MODE_FILE   = 0xFF;
	const MODE_STRING = 0x00;

	private $mode;

	private static $cacheObj;
	private static $settings = array();

	private $preventParent = array();
	private static $globalEvents = array();
	private $localEvents = array();

	private $assign    = array();
	private $tmplPath  = '';
	private $cachePath = '';
	private $logicPath = '';
	private $string    = '';

	private static $plugin;

	/**
	 * Return new instance
	 *
	 * @return Ant\Ant
	*/
	public static function init()
	{
		return new static();
	}

	/**
	 * Setup engine
	 *
	 * @param array $s settings
	 *
	 * @return Ant\Ant
	 */
	public function setup(array $s)
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

		if (false == isset($s['extension'])) {
			$s['extension'] = 'ant';
		}

		if (false == isset($s['debug'])) {
			$s['debug'] = false;
		}

		if (false == isset($s['freeze'])) {
			$s['freeze'] = false;
		}

		$s['cache'] = rtrim($s['cache'], ' 	\\/');
		$s['view']  = rtrim($s['view'], ' 	\\/');

		if (isset($s['logic'])) {
			$s['logic'] = rtrim($s['logic'], ' 	\\/');
		} else {
			$s['logic'] = '';
		}

		self::$settings = $s;
		self::$cacheObj = new Cache($s['cache']);

		self::$plugin = new Plugin;

		Fn::apply($this);

		return $this;
	}

	/**
	 * Return settings value
	 *
	 * @param string $name settings name
	 *
	 * @return mixed
	 */
	public static function settings($name = false)
	{
		return (($name != false) ? self::$settings[$name] : self::$settings);
	}

	/**
	 * Bind global event
	 *
	 * @param string $event event name
	 * @param mixed  $call  callback
	 *
	 * @return Ant\Ant
	 */
	public function bind($event, $call)
	{
		self::$globalEvents[$event][] = $call;

		return $this;
	}

	/**
	 * Bind local event
	 *
	 * @param string $event event name
	 * @param mixed  $call  callback
	 *
	 * @return Ant\Ant
	 */
	public function on($event, $call)
	{
		$this->localEvents[$event][] = $call;

		return $this;
	}

	/**
	 * Cancel event bubble
	 *
	 * @param string $prevent event name
	 *
	 * @return Ant\Ant
	 */
	public function preventParentEvent($prevent)
	{
		$this->preventParent[] = $prevent;

		return $this;
	}

	/**
	 * Trigger event
	 *
	 * @param string $event  event name
	 * @param string $string template
	 *
	 * @return string
	 */
	public function fire($event, $string)
	{
		$queue = array();

		if (isset($this->localEvents[$event])) {
			$queue = array_merge($queue, $this->localEvents[$event]);
		}

		if (false == in_array($event, $this->preventParent)) {
			if (isset(self::$globalEvents[$event])) {
				$queue = array_merge($queue, self::$globalEvents[$event]);
			}
		}

		foreach ($queue as $call) {
			$string = call_user_func($call, $string);
		}

		return $string;
	}

	/**
	 * Share haelper
	 *
	 * @param string $name function name
	 * @param mixed  $call callback
	 *
	 * @return Ant\Ant
	 */
	public function share($name, $call)
	{
		Fn::share($name, $call);

		return $this;
	}

	/**
	 * Register plugin
	 *
	 * @param string $plugin plugin name
	 * @param mixed  $call   callback
	 *
	 * @return Ant\Ant
	 */
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

	/**
	 * Activate plugin
	 *
	 * @param string $plugin  plugin name
	 * @param array  $options settings
	 *
	 * @return Ant\Ant
	 */
	public function activate($plugin, array $options = array())
	{
		self::$plugin->activate($this, $plugin, $options);

		return $this;
	}

	/**
	 * Add custom rule
	 *
	 * @param string $rx   regular expression
	 * @param mixed  $call callback
	 *
	 * @return Ant\Ant
	 */
	public function rule($rx, $call)
	{
		Parser::rule($rx, $call);

		return $this;
	}

	/**
	 * Magic __get
	 *
	 * @param string $key property
	 *
	 * @return mixed
	 */
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

	/**
	 * Magic __call
	 *
	 * @param string $name name
	 * @param array  $args arguments
	 *
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		return self::call($name, $args);
	}

	/**
	 * Magic __callStatic
	 *
	 * @param string $name name
	 * @param array  $args arguments
	 *
	 * @return mixed
	 */
	public static function __callStatic($name, $args)
	{
		return self::call($name, $args);
	}

	/**
	 * Handle call
	 *
	 * @param string $name name
	 * @param array  $args arguments
	 *
	 * @return mixed
	 */
	public static function call($name, $args)
	{
		if (method_exists('\Ant\Fn', $name) or Fn::isShared($name)) {
			return call_user_func_array('\Ant\Fn::' . $name, $args);
		} else {
			throw new Exception(
				sprintf('Undeclared method \\Ant\\Ant::%s', $name)
			);
		}
	}

	/**
	 * Return cache instance
	 *
	 * @return Ant\Cache
	 */
	public static function getCache()
	{
		return self::$cacheObj;
	}

	/**
	 * Setup logic path
	 *
	 * @param string $name logic file name
	 *
	 * @return Ant\Ant
	 */
	public function logic($name)
	{
		$this->logicPath = self::$settings['logic'] . '/' . Helper::realPath($name) . '.php';

		return $this;
	}

	/**
	 * Check template exists
	 *
	 * @param string $name template name
	 *
	 * @return bool
	 */
	public function has($name)
	{
		$path = self::$settings['view'] . '/' . Helper::realPath($name) . '.' . self::$settings['extension'];

		return file_exists($path);
	}

	/**
	 * Select template
	 *
	 * @param string $name template name
	 *
	 * @return Ant\Ant
	 */
	public function get($name)
	{
		if (Fn::isBlank($name)) {
			throw new Exception('Empty template name');
		}

		$this->mode = self::MODE_FILE;

		$this->tmplPath  = self::$settings['view']  . '/' . Helper::realPath($name) . '.' . self::$settings['extension'];
		$this->cachePath = self::$settings['cache'] . '/' . str_replace('/', '.', $name) . '.php';
		$this->logicPath = self::$settings['logic'] . '/' . Helper::realPath($name) . '.php';

		return $this;
	}

	/**
	 * Load template from string
	 *
	 * @param string $s template string
	 *
	 * @return Ant\Ant
	 */
	public function fromString($s)
	{
		$this->mode   = self::MODE_STRING;
		$this->string = $s;

		return $this;
	}

	/**
	 * Load template from file
	 *
	 * @param string $path path to template
	 *
	 * @return Ant\Ant
	 */
	public function fromFile($path)
	{
		if (Fn::isBlank($path)) {
			throw new Exception('Empty template name');
		}

		$fullPath         = self::$settings['view']  . '/' . Helper::realPath($path) . '.' . self::$settings['extension'];
		$this->logicPath = self::$settings['logic'] . '/' . Helper::realPath($path) . '.php';

		$content = IO::init()->in($fullPath)->get();

		return $this->fromString($content);
	}

	/**
	 * Return template string
	 *
	 * @return string
	 */
	public function getString()
	{
		return $this->string;
	}

	/**
	 * Assign template variables
	 *
	 * @param array $data stack
	 *
	 * @return Ant\Ant
	 */
	public function assign(array $data = array())
	{
		$this->assign = $data;

		return $this;
	}

	/**
	 * Render template
	 *
	 * @return string
	 */
	public function draw()
	{
		switch ($this->mode) {
			case self::MODE_STRING:
				ob_start();
				extract($this->assign);

				if ($this->logicPath and file_exists($this->logicPath)) {
					require $this->logicPath;
				}

				eval(
					' ?>' .
					$this->fire(
						'build',
						Parser::parse(
							$this->fire(
								'prepare',
								$this->string
							)
						)
					) .
					'<?php '
				);

				$echo = ob_get_contents();
				ob_end_clean();

				return $this->fire('exec', $echo);

			case self::MODE_FILE:
				if (false === self::$settings['freeze']) {
					if (true === self::$settings['debug'] or false == self::$cacheObj->check($this->tmplPath)) {
						$io = IO::init()->in($this->tmplPath);

						$s = $this->fire(
							'build',
							Parser::parse(
								$this->fire(
									'prepare',
									$io->get()
								),
								$this->tmplPath
							)
						);

						$io->out()
						   ->in($this->cachePath)
						   ->set($s)
						   ->out();
					}
				}

				unset($io, $s);

				ob_start();
				extract($this->assign);

				if (file_exists($this->logicPath)) {
					require $this->logicPath;
				}

				require $this->cachePath;
				$echo = ob_get_contents();
				ob_end_clean();

				return $this->fire('exec', $echo);
		}
	}

	/**
	 * View helper
	 *
	 * @return string
	 */
	public static function view()
	{
		$args = func_get_args();

		switch(count($args)){
			default:
				return;

			case 1:
				list($view) = $args;

				return static::init()->get($view)->draw();

			case 2:
				$inst = static::init();

				if (is_array($args[1])) {
					list($view, $assign) = $args;
					$inst = $inst->get($view)->assign($assign);
				} else {
					list($view, $logic) = $args;
					$inst = $inst->get($view)->logic($logic);
				}

				return $inst->draw();

			case 3:
				list($view, $assign, $logic) = $args;

				return static::init()
					->get($view)
					->assign($assign)
					->logic($logic)
					->draw();
		}
	}
}
?>