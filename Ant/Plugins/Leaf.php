<?php
	/*
		require https://github.com/undercloud/leaf
	*/

	namespace Ant\Plugins;

	class Leaf extends Base
	{
		private $options = array();

		public function __construct(array $options = array())
		{
			$this->options = $options;
		}

		public function register($ant)
		{
			$thisis = $this;

			$ant->register('leaf', function(array $options = array())use($thisis){
				return new self($options ? $options : $thisis->options);
			})
		}
	}
?>