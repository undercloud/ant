<?php
/*
	require https://github.com/fzaninotto/Faker
*/
namespace Ant\Plugins;

use Ant\Ant;

/**
 * Fake data provider
 */
class Faker extends Base
{
	private $locale;

	/**
	 * Initialize
	 *
	 * @param mixed $options options
	 */
	public function __construct($options = array())
	{
		if (false == isset($options['locale'])) {
			$options['locale'] = 'en_US';
		}

		$this->locale = $options['locale'];
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
		$ant->register('faker', \Faker\Factory::create($this->locale));
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
		$ant->unregister('faker');
	}
}
?>
