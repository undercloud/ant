<?php
namespace Ant\Plugins;

use Ant\Ant;
/*
 * Inline CSS generator
 */
class InlineCSS extends Base
{
	/**
	 * Register plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function register(Ant $ant)
	{
		$ant->register('css', function ($storage) {
			return new \Undercloud\InlineCSS($storage);
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
		$ant->unregister('css');
	}
}
?>