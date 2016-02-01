<?php
	namespace Ant\Plugins;

	use Ant\Exception;

	class Validator extends Base
	{
		public function validate($content)
		{
			$xml = @simplexml_load_string(
				'<?xml version="1.0"?>' .
				'<container>' .
					$content .
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

		public function register($ant)
		{
			$ant->bind('exec', array($this, 'validate'));
		}
	}
?>