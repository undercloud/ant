<?php
/*
	require https://github.com/xedp3x/HamlPHP
*/

namespace Ant\Plugins;

use HamlPHP;

/**
 * Haml parser
 */
class Haml extends Base
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
			$haml = new HamlPHP();

			return $haml->parseString($content);
		});
	}
}
?>