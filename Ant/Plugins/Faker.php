<?php
	/*
		require https://github.com/fzaninotto/Faker
	*/

	namespace Ant\Plugins;

	class Faker extends Base
	{
		private $locale;

		public function __construct($options = array())
		{
			if (false == isset($options['locale'])) {
				$options['locale'] = 'en_US';
			}

			$this->locale = $options['locale'];
		}

		public function register($ant)
		{
			$ant->register('faker', \Faker\Factory::create($this->locale));
		}
	}
?>
