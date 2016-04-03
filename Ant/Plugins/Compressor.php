<?php

namespace Ant\Plugins;

/**
 * Compress HTML
 */
class Compressor extends Base
{
	/**
	 * Compress given string
	 *
	 * @param string $buffer html string
	 *
	 * @return string
	 */
	public function compress($buffer)
	{
		$search = array(
			'/<!--.*-->/ms', // strip comments
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);

		$replace = array(
			'',
			'>',
			'<',
			'\\1'
		);

		return preg_replace($search, $replace, $buffer);
	}

	/**
	 * Register plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function register($ant)
	{
		$ant->bind('build', array($this, 'compress'));
	}
}
?>