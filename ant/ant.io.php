<?php
	namespace Ant
	{
		class IO
		{
			private $handle = null;

			public static function init()
			{
				return new self();
			}

			public function in($path)
			{
				if(!$path)
					return $this;

				$this->handle = fopen($path,'a+');
				flock($this->handle,LOCK_EX);

				return $this;
			}

			public function get($callback)
			{
				if(!$this->handle)
					return $this;

				rewind($this->handle);

				$data = "";
				while (!feof($this->handle)){
					$data .= fgets($this->handle);
				}

				$callback($data);

				return $this;
			}

			public function set($data)
			{
				if(!$this->handle)
					return $this;

				rewind($this->handle);
				ftruncate($this->handle,0);
				fwrite($this->handle,$data); 
				fflush($this->handle);

				return $this;
			}

			public function out()
			{
				if(!$this->handle)
					return $this;

				flock($this->handle,LOCK_UN);
				fclose($this->handle);
				$this->handle = null;

				return $this;
			}
		}
	}
?>