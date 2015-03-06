<?php
	namespace Ant
	{
		class Cache
		{
			public static $map = null;
			private $is_changed = false;
			private $cache_file = null;

			public function __construct($cache_file = false)
			{
				$this->cache_file = $cache_file;
			}

			public function getMap()
			{
				$io = IO::init()->in($this->cache_file);
				self::$map = json_decode($io->get(),true);

				if(false === is_array(self::$map))
					self::$map = array();

				$io->out();
			}

			public function check($path)
			{
				if(null === self::$map)
					$this->getMap();

				$mtime = filemtime($path);
				if(array_key_exists($path, self::$map))
					if(self::$map[$path] == $mtime)
						return true;

				self::$map[$path] = $mtime;
				$this->is_changed = true;

				return false;
			}

			public function __destruct()
			{
				if(true == $this->is_changed){
					IO::init()
					->in($this->cache_file)
					->set(
						json_encode(
							self::$map,
							JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
						)
					)
					->out();
				}
			}
		}
	}
?>