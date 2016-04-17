<?php
namespace Ant;

/**
 * File IO
 */
class IO
{
	private $path;
	private $handle;

	/**
	 * Initialize instance
	 *
	 * @return Ant\IO
	 */
	public static function init()
	{
		return new self();
	}

	/**
	 * Select file path
	 *
	 * @param string $path file path
	 *
	 * @return Ant\IO
	 */
	public function in($path)
	{
		$this->path = $path;

		if (!$this->path) {
			throw new Exception('Path is empty');
		}

		$this->handle = @fopen($this->path, 'a+');

		if (false === $this->handle) {
			throw new Exception(
				sprintf('Can\'t open file %s', $this->path)
			);
		}

		if (false === @flock($this->handle, LOCK_EX)) {
			throw new Exception(
				sprintf('Can\'t lock file %s', $this->path)
			);
		}

		return $this;
	}

	/**
	 * Read file content
	 *
	 * @return string
	 */
	public function get()
	{
		if (false === rewind($this->handle)) {
			throw new Exception(
				sprintf('Can\'t rewind file %s', $this->path)
			);
		}

		$data = '';
		while (!feof($this->handle)) {
			$data .= fgets($this->handle);
		}

		return $data;
	}

	/**
	 * Set file contents
	 *
	 * @param string $data string data
	 *
	 * @return $this
	 */
	public function set($data)
	{
		if (false === rewind($this->handle)) {
			throw new Exception(
				sprintf('Can\'t rewind file %s', $this->path)
			);
		}

		if (false === @ftruncate($this->handle, 0)) {
			throw new Exception(
				sprintf('Can\'t truncate file %s', $this->path)
			);
		}

		if (false === @fwrite($this->handle, $data)) {
			throw new Exception(
				sprintf('Can\'t write file %s', $this->path)
			);
		}

		if (false === @fflush($this->handle)) {
			throw new Exception(
				sprintf('Can\'t flush file %s', $this->path)
			);
		}

		return $this;
	}

	/**
	 * Write file
	 *
	 * @return $this;
	 */
	public function out()
	{
		if (false === @flock($this->handle, LOCK_UN)) {
			throw new Exception(
				sprintf('Can\'t unlock file %s', $this->path)
			);
		}

		if (false === @fclose($this->handle)) {
			throw new Exception(
				sprintf('Can\'t close file %s', $this->path)
			);
		}

		$this->handle = null;

		return $this;
	}
}
?>