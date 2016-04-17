<?php

namespace Ant\Plugins;

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
	public function register($ant)
	{
		$ant->register('css', function ($storage) {
			return new \Undercloud\InlineCSS($storage);
		});
	}
}
?>