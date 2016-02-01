<?php
	/*
		require https://github.com/fzaninotto/Faker
	*/

	namespace Ant\Plugins;

	class Faker extends PluginsBase
	{
		private $locale;

		public function __construct($locale = 'en_EN')
		{
			$this->locale = $locale;
		}

		public function register($ant)
		{
			$ant->register('faker', Faker\Factory::create($this->locale));
		}
	}
?>
