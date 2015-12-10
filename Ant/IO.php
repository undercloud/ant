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
				if (!$path) {
					throw new Exception();
				}

				$this->handle = fopen($path, 'a+');

				if (false === $this->handle) {
					throw new Exception();
				}

				if (false === flock($this->handle,LOCK_EX)) {
					throw new Exception();
				}

				return $this;
			}

			public function get()
			{
				if (!$this->handle) {
					throw new Exception();
				}

				if(false === rewind($this->handle))
					throw new Exception();

				$data = '';
				while (!feof($this->handle)) {
					$data .= fgets($this->handle);
				}

				return $data;
			}

			public function set($data)
			{
				if (!$this->handle) {
					return $this;
				}

				rewind($this->handle);
				ftruncate($this->handle, 0);
				fwrite($this->handle, $data); 
				fflush($this->handle);

				return $this;
			}

			public function out()
			{
				if (!$this->handle) {
					return $this;
				}

				flock($this->handle, LOCK_UN);
				fclose($this->handle);
				$this->handle = null;

				return $this;
			}
		}
	}
?>