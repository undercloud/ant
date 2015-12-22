<?php
	namespace Ant\Plugins;

	class Asset extends PluginBase
	{
		public $x = 100500;

		private function isLocalPath($path)
		{
			if (strpos($path, '//') >= max(strpos($path, '.'), strpos($path, '/'))) {
				return false;
			} else {
				return true;
			}
		}

		public function check($path)
		{
			$realpath = $_SERVER['DOCUMENT_ROOT'] . $path;
			if ($this->isLocalPath($path) and file_exists($realpath)) {
				$mtime = filemtime($realpath);

				return $path . '?' . $mtime; 
			}

			return $path;
		}

		public function register($ant)
		{
			$ant->register('asset', new self());
		}
	}
?>