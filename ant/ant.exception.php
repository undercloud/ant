<?php
	namespace Ant
	{
		class AntException extends \Exception
		{
			public function __construct($message)
			{
				parent::__construct($message);
			}
		}
	}
?>