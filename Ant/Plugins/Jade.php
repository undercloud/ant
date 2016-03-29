<?php

	namespace Ant\Plugins;

	use Undercloud\PicoJade;

	class Jade extends Base
	{
		public function register($ant)
		{
			$ant->bind('build', function($content) {
				$jade = new PicoJade;

				return $jade->compile($content);
			});
		}
	}
?>