<?php
namespace Ant\Plugins;

use Ant\Ant;
/**
 * Plugins base
 */
abstract class Base
{
	/**
	 * Register given plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	abstract public function register(Ant $ant);

	/**
	 * Unregister given plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	abstract public function unregister(Ant $ant);
}
?>