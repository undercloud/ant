<?php
namespace Ant\Plugins;

use Ant\Ant;
/**
 * Language plugin
 */
class Lang extends Base
{
	private $options = array();

	public function __construct($options = array())
	{
		$this->options = $options;
	}

	public function register(Ant $ant)
	{
		$ant->register('lang', new \Undercloud\Lang($this->options));
	}

	public function unregister(Ant $ant)
	{
		$ant->unregister('lang');
	}
}
?>