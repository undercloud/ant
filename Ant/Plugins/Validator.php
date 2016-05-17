<?php
namespace Ant\Plugins;

use Ant\Exception;
use Ant\Ant;
/**
 * DOM string validation
 */
class Validator extends Base
{
	private $event;
	private $options;

	/**

	*/
	public function __construct(array $options = array())
	{
		if (false == isset($options['strict'])) {
			$options['strict'] = false;
		}

		$this->options = $options;
	}

	/**
	 * Validate content
	 *
	 * @param string $content content
	 *
	 * @return string
	 */
	public function validate($content)
	{
		$xml = @simplexml_load_string(
			'<?xml version="1.0"?>' .
			'<container>' .
				preg_replace('/<!DOCTYPE.*?>\s*/s', '', $content) .
			'</container>'
		);

		if ($xml instanceof \SimpleXMLElement) {
			if (true === $this->options['strict']) {
				require_once __DIR__ . '/Validator/Strict.php';

				new \Ant\Plugins\Validator\Strict($xml);
			}
		} else {
			$error = libxml_get_last_error();

			trigger_error(sprintf('Validation Error %s', $error->message), E_USER_NOTICE);
		}

		return $content;
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
		$this->event = $ant->bind('exec', array($this, 'validate'));
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
		$ant->unbind('exec', $this->event);
	}
}
?>