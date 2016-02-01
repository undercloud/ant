<?php
	/*
		require https://github.com/xedp3x/HamlPHP
	*/

	namespace Ant\Plugins;

	class Haml extends Base
	{
		public function register($ant)
		{
			$ant->bind('build', function($content){
				$haml = new HamlPHP();

				return $haml->parseString($content);
			});
		}
	}
?>