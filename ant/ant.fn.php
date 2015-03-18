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

			public function decode($s)
			{
				return html_entity_decode($s,ENT_QUOTES,'UTF-8');
			}

			public static function capitalize($s)
			{
				$enc = mb_detect_encoding($s);
				$s = mb_strtolower($s,$enc);

				return mb_strtoupper(mb_substr($s, 0, 1, $enc), $enc) .
					   mb_substr($s, 1, mb_strlen($str, $enc), $enc); 
			}

			public static function upper($s)
			{
				return mb_strtoupper($s,mb_detect_encoding($s));
			}

			public static function lower($s)
			{
				return mb_strtolower($s,mb_detect_encoding($s));
			}

			public static function url(array $a)
			{
				return http_build_query($a);
			}

			public static function whitespace($s)
			{
				return preg_replace('/\s+/',' ',$s);
			}

			public static function doctype($d = 'HTML5')
			{
				
			}
		}
	}
?>