<?php
/*
	require https://github.com/undercloud/leaf
*/

namespace Ant\Plugins;

/*
 * DOM generator
 */
class Leaf extends Base
{
	private $options = array();

	/*
	 * Setup generator
	 *
	 * @param array $options params
	 */
	public function __construct(array $options = array())
	{
		$this->options = $options;
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
		$thisis = $this;

		$ant->register('leaf', function(array $options = array())use($thisis){
			return new self($options ? $options : $thisis->options);
		})
	}
}
?>