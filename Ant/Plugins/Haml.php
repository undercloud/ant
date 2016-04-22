<?php
/*
	require https://github.com/xedp3x/HamlPHP
*/
namespace Ant\Plugins;

use HamlPHP;
use Ant\Ant;

/**
 * Haml parser
 */
class Haml extends Base
{
	private $event;

	/**
	 * Register plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function register(Ant $ant)
	{
		$this->event = $ant->bind('build', function ($content) {
			$haml = new HamlPHP();

			return $haml->parseString($content);
		});
	}

	/**
	 * Unregister plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function unregister(Ant $ant)
	{
		$ant->unbind('build', $this->event);
	}
}
?>