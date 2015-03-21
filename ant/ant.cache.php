<?php
	namespace Ant
	{
		class Cache
		{
			public static $map = null;
			private $is_changed = false;
			private $cache_file = null;

			public function __construct($cache_path = false)
			{
				$this->cache_file = $cache_path . DIRECTORY_SEPARATOR . 'cache.json';
			}

			public function getMap()
			{
				$io = IO::init()->in($this->cache_file);
				self::$map = json_decode($io->get(),true);

				if(false == is_array(self::$map))
					self::$map = array();

				if(false === is_array(self::$map['view']))
					self::$map['view'] = array();

				if(false === is_array(self::$map['chain']))
					self::$map['chain'] = array();

				//garbage collector
				if(mt_rand(0, 100) < 5){
					foreach(self::$map['view'] as $k=>$v){
						if(false == file_exists($k)){
							unset(self::$map['view'][$k]);
							$this->is_changed = true;
						}
					}
				}

				$io->out();
			}

			public function check($path)
			{
				if(null === self::$map)
					$this->getMap();

				$view_changed = true;
				$chain_changed = true;

				if(array_key_exists($path, self::$map['chain']) and self::$map['chain']){
					foreach(self::$map['chain'][$path] as $item){
						$mtime = filemtime($item);
						if(
							false == array_key_exists($item, self::$map['view']) or
							self::$map['view'][$item] != $mtime
						){
							self::$map['view'][$item] = $mtime;
							$this->is_changed = true;
							break;
						}
					}

					$chain_changed = false;
				}else{
					$chain_changed = false;
				}

				$mtime = filemtime($path);
				if(array_key_exists($path, self::$map['view']))
					if(self::$map['view'][$path] == $mtime)
						$view_changed = false;

				if($view_changed == true){
					self::$map['view'][$path] = $mtime;
					$this->is_changed = true;
				}

				return ($view_changed == false and $chain_changed == false);
			}

			public function chain($path,$chain)
			{
				if(null === self::$map)
					$this->getMap();

				self::$map['chain'][$path] = $chain;
				$this->is_changed = true;
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