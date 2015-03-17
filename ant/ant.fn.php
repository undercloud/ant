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

			public static function escape($s)
			{
				return htmlentities($s,ENT_QUOTES,'UTF-8');
			}

			public static function capitalize($s)
			{
				$enc = mb_detect_encoding($s);
				return mb_strtoupper(
					mb_substr($str, 0, 1, $enc), $enc) .
					mb_substr($str, 1, mb_strlen($str, $enc), $enc
				); 
			}

			public static function upper($s)
			{
				return mb_strtoupper($s,mb_detect_encoding($s));
			}

			public static function lower($s)
			{
				return mb_strtolower($s,mb_detect_encoding($s));
			}
		}
	}
?>