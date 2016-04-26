<?php
namespace Ant;

/**
 * Template cache
 */
class Cache
{
	private static $map;
	private $isChanged = false;
	private $cachePath;
	private $cacheFile;

	/**
	 * Setup cache folder
	 *
	 * @param string $cachePath cache folder
	 */
	public function __construct($cachePath = '')
	{
		$this->cachePath = $cachePath;
		$this->cacheFile = $cachePath . '/cache.json';
	}

	/**
	 * Load cache map
	 *
	 * @return void
	 */
	public function getMap()
	{
		$io = IO::init()->in($this->cacheFile);
		self::$map = json_decode($io->get(), true);

		if (false == is_array(self::$map)) {
			self::$map = array();
		}

		if (false == isset(self::$map['view']) or false == is_array(self::$map['view'])) {
			self::$map['view'] = array();
		}

		if (false == isset(self::$map['chain']) or false === is_array(self::$map['chain'])) {
			self::$map['chain'] = array();
		}

		//garbage collector
		if (mt_rand(0, 100) < 3) {
			foreach (self::$map['view'] as $k => $v) {
				if (false == file_exists($k)) {
					unset(self::$map['view'][$k]);
					unset(self::$map['chain'][$k]);

					$this->isChanged = true;
				}
			}

			foreach (self::$map['chain'] as $k => $v) {
				foreach ($v as $subk => $subv) {
					if (false == file_exists($subv)) {
						unset(self::$map['chain'][$k][$subk]);

						$this->isChanged = true;
					}
				}
			}
		}

		$io->out();
	}

	/**
	 * Check cache expired
	 *
	 * @param string $path template path
	 *
	 * @return bool
	 */
	public function check($path)
	{
		if (null === self::$map) {
			$this->getMap();
		}

		$snapshot = Ant::snapshot();

		$chainChanged = false;
		if (array_key_exists($path, self::$map['chain'])) {
			foreach (self::$map['chain'][$path] as $item) {
				$mtime = @filemtime($item);

				if (false === $mtime) {
					throw new Exception(
						sprintf('Cannot get file modification time %s', $path)
					);
				}

				$isDelete = false;
				if (false == array_key_exists($item, self::$map['view'])) {
					list($mt, $ss) = explode(':', self::$map['view'][$item]);

					if ($mt != $mtime or $snapshot != $ss) {
						$isDelete = true;
					}
				} else {
					$isDelete = true;
				}

				if ($isDelete) {
					self::$map['view'][$item] = $mtime . ':' . $snapshot;

					$chainPath = $this->cachePath . '/' . basename($item);
					if (file_exists($chainPath)) {
						if (false === @unlink($chainPath)) {
							throw new Exception(
								sprintf('Cannot delete file %s', $chainPath)
							);
						}
					}

					foreach (self::$map['chain'] as $k => $v) {
						$chainPath = $this->cachePath . '/' . basename($k);

						if (in_array($item, $v) and file_exists($chainPath)) {
							if (false === @unlink($chainPath)) {
								throw new Exception(
									sprintf('Cannot delete file %s', $chainPath)
								);
							}
						}
					}

					$this->isChanged = $chainChanged = true;

					break;
				}
			}
		}

		$viewChanged = true;
		$mtime = filemtime($path);

		if (array_key_exists($path, self::$map['view'])) {
			list($mt, $ss) = explode(':', self::$map['view'][$path]);
			$mt = (int)$mt;
			if (false !== $mtime and $mt == $mtime and $ss === $snapshot) {
				$viewChanged = false;
			}
		}

		if ($viewChanged == true) {
			self::$map['view'][$path] = $mtime . ':' . $snapshot;
			$this->isChanged = true;
		}

		if (false == file_exists($this->cachePath)) {
			return false;
		}

		return ($viewChanged == false and $chainChanged == false);
	}

	/**
	 * Inherited template logic
	 *
	 * @param string $path  template path
	 * @param mixed  $chain parent templates
	 *
	 * @return void
	 */
	public function chain($path, $chain)
	{
		if (null === self::$map) {
			$this->getMap();
		}

		if ($chain) {
			self::$map['chain'][$path] = $chain;
		} else {
			unset(self::$map['chain'][$path]);
		}

		$this->isChanged = true;
	}

	/**
	 * Save cache file
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if (true == $this->isChanged) {
			IO::init()
			->in($this->cacheFile)
			->set(
				json_encode(
					self::$map,
					JSON_HEX_TAG  |
					JSON_HEX_AMP  |
					JSON_HEX_APOS |
					JSON_HEX_QUOT
				)
			)
			->out();
		}
	}
}
?>