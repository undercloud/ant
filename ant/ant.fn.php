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
				$enc = 'UTF-8';
				$s = mb_strtolower($s,$enc);

				return mb_strtoupper(mb_substr($s, 0, 1, $enc), $enc) .
					   mb_substr($s, 1, mb_strlen($str, $enc), $enc); 
			}

			public static function upper($s)
			{
				return mb_strtoupper($s,'UTF-8');
			}

			public static function lower($s)
			{
				return mb_strtolower($s,'UTF-8');
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
				switch($d){
					default:
					case 'HTML5':
						return '<!DOCTYPE html>';

					case 'XHTML11':
						return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';

					case 'XHTML1_STRICT':
						return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

					case 'XHTML1_TRANSITIONAL':
						return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

					case 'XHTML1_FRAMESET':
						return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';

					case 'XHTML_BASIC1':
						return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">';

					case 'HTML4_STRICT':
						return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';

					case 'HTML4_LOOSE':
						return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

					case 'HTML4_FRAMESET':
						return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
				}
			}
		}
	}
?>