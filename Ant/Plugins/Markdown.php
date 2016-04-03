<?php
/*
	require https://github.com/erusev/parsedown
*/

namespace Ant\Plugins;

use Parsedown;

/**
 * Markdown parser
 */
class Markdown extends Base
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
		$ant->bind('build', function($content){
			$md = new Parsedown();

			return $md->text($content);
		});
	}
}
?>
