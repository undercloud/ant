<?php
	namespace Ant\Plugins;

	class Compressor extends Base
	{
		public function compress($buffer)
		{
			$search = array(
				'/<!--.*-->/ms', // strip comments
				'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
				'/[^\S ]+\</s',  // strip whitespaces before tags, except space
				'/(\s)+/s'       // shorten multiple whitespace sequences
			);

			$replace = array(
				'',
				'>',
				'<',
				'\\1'
			);

			return preg_replace($search, $replace, $buffer);
		}

		public function register($ant)
		{
			$ant->bind('build', array($this, 'compress'));
		}
	}
?>