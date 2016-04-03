<?php


namespace Ant\Plugins;

/**
 * Cache flush
 */
class Asset extends Base
{
	/**
	 * Check path is local
	 *
	 * @param string $path path to file
	 *
	 * @return bool
	 */
	private function _isLocalPath($path)
	{
		if (strpos($path, '//') >= max(strpos($path, '.'), strpos($path, '/'))) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Check given path
	 *
	 * @param string $path path to file
	 *
	 * @return string
	 */
	public function check($path)
	{
		$realpath = $_SERVER['DOCUMENT_ROOT'] . $path;
		if ($this->_isLocalPath($path) and file_exists($realpath)) {
			$mtime = filemtime($realpath);

			return $path . '?' . $mtime;
		}

		return $path;
	}

	/**
	 * Register plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function register($ant)
	{
		$asset = new self();

		$ant->register(
			'asset',
			function ($path) use ($asset) {
				return $asset->check($path);
			}
		);
	}
}
?>