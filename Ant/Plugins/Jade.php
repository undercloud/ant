<?php
namespace Ant\Plugins;

use Undercloud\PicoJade;
use Ant\Ant;

/*
	Jade parser
*/
class Jade extends Base
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
		$callback = function ($content) {
			$jade = new PicoJade;

			return $jade->compile($content);
		};

		$this->event = $ant->bind('build', $callback);
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