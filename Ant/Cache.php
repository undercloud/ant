<?php

namespace Ant;

/**
 * Template cache
 */
class Cache
{
	private static $_map;
	private $_isChanged = false;
	private $_cachePath;
	private $_cacheFile;

	/**
	 * Setup cache folder
	 *
	 * @param string $cachePath cache folder
	 */
	public function __construct($cachePath = '')
	{
		$this->_cachePath = $cachePath;
		$this->_cacheFile = $cachePath . '/cache.json';
	}

	/**
	 * Return cache map
	 *
	 * @return array
	 */
	public function getMap()
	{
		$io = IO::init()->in($this->_cacheFile);
		self::$_map = json_decode($io->get(), true);

		if (false == is_array(self::$_map)) {
			self::$_map = array();
		}

		if (false == isset(self::$_map['view']) or false == is_array(self::$_map['view'])) {
			self::$_map['view'] = array();
		}

		if (false == isset(self::$_map['chain']) or false === is_array(self::$_map['chain'])) {
			self::$_map['chain'] = array();
		}

		//garbage collector
		if (mt_rand(0, 100) < 3) {
			foreach (self::$_map['view'] as $k => $v) {
				if (false == file_exists($k)) {
					unset(self::$_map['view'][$k]);
					unset(self::$_map['chain'][$k]);

					$this->_isChanged = true;
				}
			}

			foreach (self::$_map['chain'] as $k => $v) {
				foreach ($v as $subk => $subv) {
					if (false == file_exists($subv)) {
						unset(self::$_map['chain'][$k][$subk]);

						$this->_isChanged = true;
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
		if (null === self::$_map) {
			$this->getMap();
		}

		$chainChanged = false;
		if (array_key_exists($path, self::$_map['chain'])) {
			foreach (self::$_map['chain'][$path] as $item) {
				$mtime = @filemtime($item);

				if (false === $mtime) {
					throw new Exception(
						sprintf('Cannot get file modification time %s', $path)
					);
				}

				if (false == array_key_exists($item, self::$_map['view']) or self::$_map['view'][$item] != $mtime) {
					self::$_map['view'][$item] = $mtime;

					$chainPath = $this->_cachePath . '/' . basename($item);
					if (file_exists($chainPath)) {
						if (false === @unlink($chainPath)) {
							throw new Exception(
								sprintf('Cannot delete file %s', $chainPath)
							);
						}
					}

					foreach (self::$_map['chain'] as $k => $v) {
						$chainPath = $this->_cachePath . '/' . basename($k);

						if (in_array($item, $v) and file_exists($chainPath)) {
							if (false === @unlink($chainPath)) {
								throw new Exception(
									sprintf('Cannot delete file %s', $chainPath)
								);
							}
						}
					}

					$this->_isChanged = $chainChanged = true;

					break;
				}
			}
		}

		$viewChanged = true;
		$mtime = filemtime($path);

		if (array_key_exists($path, self::$_map['view'])) {
			if (false !== $mtime and self::$_map['view'][$path] == $mtime) {
				$viewChanged = false;
			}
		}

		if ($viewChanged == true) {
			self::$_map['view'][$path] = $mtime;
			$this->_isChanged = true;
		}

		if (false == file_exists($this->_cachePath)) {
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
		if (null === self::$_map) {
			$this->getMap();
		}

		if ($chain) {
			self::$_map['chain'][$path] = $chain;
		} else {
			unset(self::$_map['chain'][$path]);
		}

		$this->_isChanged = true;
	}

	/**
	 * Save cache file
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if (true == $this->_isChanged) {
			IO::init()
			->in($this->_cacheFile)
			->set(
				json_encode(
					self::$_map,
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