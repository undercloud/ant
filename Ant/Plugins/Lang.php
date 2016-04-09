<?php

namespace Ant\Plugins;

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

	public function register($ant)
	{
		$ant->register('lang', new \Undercloud\Lang($this->options));
	}
}
?>