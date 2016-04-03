<?php

namespace Ant\Plugins;

use Undercloud\PicoJade;

/*
	Jade parser
*/
class Jade extends Base
{
	/**
	 * Register plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function register($ant)
	{
		$ant->bind('build', function($content) {
			$jade = new PicoJade;

			return $jade->compile($content);
		});
	}
}
?>