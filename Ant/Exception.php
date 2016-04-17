<?php
namespace Ant;

/**
 * Exception handler
 */
class Exception extends \Exception
{
	/**
	 * Handle exception
	 *
	 * @param string $message exception message
	*/
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
?>