<?php
  	/*
  		require https://github.com/erusev/parsedown
	*/

	namespace Ant\Plugins;

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
