<?php
	namespace Ant
	{
		class Fn
		{
			public static function iterable($o)
			{
				return (is_array($o) || $o instanceof Traversable || $o instanceof stdClass);
			}

			public static function js($src,$defer = "")
			{
				return '<script type="text/javascript" src="' . $src . '"' . ($defer ? " " . $defer : '') . '></script>';
			}

			public static function css($href,$media = "")
			{
				return '<link type="text/css" rel="stylesheet" href="' . $href . '"' . ($media ? ' media="' . $media . '"' : '') . '/>';
			}

			/*
				public static function ...
				 - escape
				 - capitalize
				 - upper
				 - lower
				 - format digit
			*/
		}
	}
?>