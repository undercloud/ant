<?php

namespace Ant;

/**
 * File IO
 */
class IO
{
	private $_path;
	private $_handle;

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
	 * @return $this
	 */
	public function in($path)
	{
		$this->_path = $path;

		if (!$this->_path) {
			throw new Exception('Path is empty');
		}

		$this->_handle = @fopen($this->_path, 'a+');

		if (false === $this->_handle) {
			throw new Exception(
				sprintf('Can\'t open file %s', $this->_path)
			);
		}

		if (false === @flock($this->_handle, LOCK_EX)) {
			throw new Exception(
				sprintf('Can\'t lock file %s', $this->_path)
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
		if (false === rewind($this->_handle)) {
			throw new Exception(
				sprintf('Can\'t rewind file %s', $this->_path)
			);
		}

		$data = '';
		while (!feof($this->_handle)) {
			$data .= fgets($this->_handle);
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
		if (false === rewind($this->_handle)) {
			throw new Exception(
				sprintf('Can\'t rewind file %s', $this->_path)
			);
		}

		if (false === @ftruncate($this->_handle, 0)) {
			throw new Exception(
				sprintf('Can\'t truncate file %s', $this->_path)
			);
		}

		if (false === @fwrite($this->_handle, $data)) {
			throw new Exception(
				sprintf('Can\'t write file %s', $this->_path)
			);
		}

		if (false === @fflush($this->_handle)) {
			throw new Exception(
				sprintf('Can\'t flush file %s', $this->_path)
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
		if (false === @flock($this->_handle, LOCK_UN)) {
			throw new Exception(
				sprintf('Can\'t unlock file %s', $this->_path)
			);
		}

		if (false === @fclose($this->_handle)) {
			throw new Exception(
				sprintf('Can\'t close file %s', $this->_path)
			);
		}

		$this->_handle = null;

		return $this;
	}
}
?>