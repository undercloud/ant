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
			return $content;
		} else {
			$error = libxml_get_last_error();

			throw new Exception(
				sprintf('Validation Error %s', $error->message)
			);
		}
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