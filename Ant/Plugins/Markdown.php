<?php
/*
	require https://github.com/erusev/parsedown
*/
namespace Ant\Plugins;

use Parsedown;
use Ant\Ant;

/**
 * Markdown parser
 */
class Markdown extends Base
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
			$md = new Parsedown;

			return $md->text($content);
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