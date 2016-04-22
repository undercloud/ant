<?php
namespace Ant\Plugins;

use Ant\Ant;
/**
 * Compress HTML
 */
class Compressor extends Base
{
	private $event;

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
	public function register(Ant $ant)
	{
		$this->event = $ant->bind('build', array($this, 'compress'));
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