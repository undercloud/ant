<?php

namespace Ant\Plugins;

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
	abstract public function register($ant);
}
?>