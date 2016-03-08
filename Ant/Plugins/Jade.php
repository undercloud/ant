<?php

	namespace Ant\Plugins;

	use PicoJade;

	class Jade extends Base
	{
		public function register($ant)
		{
			$ant->bind('build', function($content) {
				$jade = new Undercloud\PicoJade;

				return $jade->compile($content, true);
			});
		}
	}
?>