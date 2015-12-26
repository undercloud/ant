<?php
	namespace Ant;

	class Cache
	{
		private static $map;
		private $is_changed = false;
		private $cache_path;
		private $cache_file;

		public function __construct($cache_path = '')
		{
			$this->cache_path = $cache_path;
			$this->cache_file = $cache_path . DIRECTORY_SEPARATOR . 'cache.json';
		}

		public function getMap()
		{
			$io = IO::init()->in($this->cache_file);
			self::$map = json_decode($io->get(), true);

			if (false == is_array(self::$map)) {
				self::$map = array();
			}

			if (false == isset(self::$map['view']) or false === is_array(self::$map['view'])) {
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

						$this->is_changed = true;
					}
				}

				foreach (self::$map['chain'] as $k => $v) {
					foreach ($v as $subk => $subv) {
						if (false == file_exists($subv)) {
							unset(self::$map['chain'][$k][$subk]);
							
							$this->is_changed = true;
						}
					}
				}
			}

			$io->out();
		}

		public function check($path)
		{
			if (null === self::$map) {
				$this->getMap();
			}

			$chain_changed = false;
			if (array_key_exists($path, self::$map['chain'])) {
				foreach (self::$map['chain'][$path] as $item) {
					$mtime = @filemtime($item);

					if (false === $mtime) {
						throw new Exception(
							sprintf('Cannot get file modification time %s', $path)
						);
					}

					if (
						false == array_key_exists($item, self::$map['view']) 
						or self::$map['view'][$item] != $mtime
					) {
						self::$map['view'][$item] = $mtime;

						$chain_path = $this->cache_path . DIRECTORY_SEPARATOR . basename($item);
						if (file_exists($chain_path)) {
							if (false === @unlink($chain_path)) {
								throw new Exception(
									sprintf('Cannot delete file %s', $chain_path)
								);
							}
						}

						foreach (self::$map['chain'] as $k => $v) {
							$chain_path = $this->cache_path . DIRECTORY_SEPARATOR . basename($k);
							
							if (in_array($item, $v) and file_exists($chain_path)) {
								if (false === @unlink($chain_path)) {
									throw new Exception(
										sprintf('Cannot delete file %s', $chain_path)
									);
								}
							}
						}

						$this->is_changed = $chain_changed = true;
						
						break;
					}
				}
			}

			$view_changed = true;
			$mtime = filemtime($path);

			if (array_key_exists($path, self::$map['view'])) {
				if (false !== $mtime and self::$map['view'][$path] == $mtime) {
					$view_changed = false;
				}
			}

			if ($view_changed == true) {
				self::$map['view'][$path] = $mtime;
				$this->is_changed = true;
			}

			if (false == file_exists($this->cache_path)) {
				return false;
			}

			return ($view_changed == false and $chain_changed == false);
		}

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

			$this->is_changed = true;
		}

		public function __destruct()
		{
			if (true == $this->is_changed) {
				IO::init()
				->in($this->cache_file)
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