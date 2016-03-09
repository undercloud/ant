<?php
  	/*
  		require https://github.com/erusev/parsedown
	*/

	namespace Ant\Plugins;

	use Parsedown;

	class Markdown extends Base
	{
		public function register($ant)
		{
			$ant->bind('build', function($content){
				$md = new Parsedown();

				return $md->text($content);
			});
		}
	}
?>
