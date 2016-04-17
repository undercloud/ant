<?php
namespace Ant;

class Render
{
	const MODE_FILE   = 0xFF;
	const MODE_STRING = 0x00;

	private $ant;
	private $mode;
	private $tmplPath  = '';
	private $cachePath = '';
	private $logicPath = '';
	private $string    = '';
	private $assign    = array();

	/**
	 * Render constructor
	 *
	 * @param Ant $ant entity
	 */
	public function __construct(Ant $ant)
	{
		$this->ant = $ant;
	}

	/**
	 * Setup logic path
	 *
	 * @param string $path logic file name
	 *
	 * @return Ant\Render
	 */
	public function logic($path)
	{
		$this->logicPath = $path;

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
		$path = $this->ant->settings('view') . '/' . Helper::realPath($name) . '.' . $this->ant->settings('extension');

		return file_exists($path);
	}

	/**
	 * Select template
	 *
	 * @param string $name template name
	 *
	 * @return Ant\Render
	 */
	public function get($name)
	{
		if (Fn::isBlank($name)) {
			throw new Exception('Empty template name');
		}

		$this->mode = self::MODE_FILE;

		$this->tmplPath  = $this->ant->settings('view')  . '/' . Helper::realPath($name) . '.' . $this->ant->settings('extension');
		$this->cachePath = $this->ant->settings('cache') . '/' . $name . '.php';

		return $this;
	}

	/**
	 * Load template from string
	 *
	 * @param string $s template string
	 *
	 * @return Ant\Render
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
	 * @return Ant\Render
	 */
	public function fromFile($path)
	{
		if (Fn::isBlank($path)) {
			throw new Exception('Empty template name');
		}

		$fullPath = $this->ant->settings('view')  . '/' . Helper::realPath($path) . '.' . $this->ant->settings('extension');

		$content = IO::init()->in($fullPath)->get();

		return $this->fromString($content);
	}

	/**
	 * Assign template variables
	 *
	 * @param array $data stack
	 *
	 * @return Ant\Render
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

				set_error_handler(function ($errno, $errstr, $errfile, $errline) {
					if (!(error_reporting() & $errno)) {
						return;
					}

					throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
				});

				eval(
					' ?>' .
					$this->ant->event->fire(
						'build',
						Parser::parse(
							$this->ant->event->fire(
								'prepare',
								$this->string
							)
						)
					) .
					'<?php '
				);

				$echo = ob_get_contents();
				ob_end_clean();

				restore_error_handler();

				return $this->ant->event->fire('exec', $echo);

			case self::MODE_FILE:
				if (!$this->logicPath) {
					$logicRoot = $this->ant->settings('logic');
					if ($logicRoot) {
						$tryLogic = $logicRoot . '/' . pathinfo($this->tmplPath, PATHINFO_FILENAME) . '.php';

						if (file_exists($tryLogic)) {
							$this->logicPath = $tryLogic;
						}
					}
				}

				if (false === $this->ant->settings('freeze')) {
					if (true === $this->ant->settings('debug') or false == $this->ant->cache->check($this->tmplPath)) {
						$io = IO::init()->in($this->tmplPath);

						$s = $this->ant->event->fire(
							'build',
							Parser::parse(
								$this->ant->event->fire(
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

				if ($this->logicPath and file_exists($this->logicPath)) {
					require $this->logicPath;
				}

				require $this->cachePath;
				$echo = ob_get_contents();
				ob_end_clean();

				return $this->ant->event->fire('exec', $echo);
		}
	}
}
?>