<?php
	namespace Ant
	{
		class Fn
		{
			private static $encoding = 'UTF-8';

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

			public static function number($n)
			{
				return rtrim(rtrim(number_format((float)$n, 2, '.', ' '),'0'),'.');
			}

			public static function escape($s)
			{
				return htmlentities($s,ENT_QUOTES,self::$encoding);
			}

			public static function decode($s)
			{
				return html_entity_decode($s,ENT_QUOTES,self::$encoding);
			}

			public static function capitalize($s)
			{
				$enc = self::$encoding;
				$s = mb_strtolower($s,$enc);

				return mb_strtoupper(mb_substr($s, 0, 1, $enc), $enc) .
					   mb_substr($s, 1, mb_strlen($s, $enc), $enc); 
			}

			public static function capitalizeAll($s)
			{
				return mb_convert_case($s,MB_CASE_TITLE,self::$encoding);
			}

			public static function upper($s)
			{
				return mb_strtoupper($s,self::$encoding);
			}

			public static function lower($s)
			{
				return mb_strtolower($s,self::$encoding);
			}

			public static function url(array $a)
			{
				return http_build_query($a);
			}

			public static function whitespace($s)
			{
				return preg_replace('/\s+/',' ',$s);
			}

			public static function limit($s,$limit = 250,$postfix="...")
			{
				$encoding = self::$encoding;
				if(mb_strlen($s,$encoding) > $limit){
					return mb_substr($s,0,$limit,$encoding) . $postfix;
				}else{
					return $s;
				}
			}

			public static function limitWords($s,$limit = 250,$postfix="...")
			{
				$encoding = self::$encoding;
				if(mb_strlen($s,$encoding) > $limit){
					$pos = mb_strpos($s, ' ',$limit,$encoding);
					if(false !== $pos)
						return mb_substr($s,0,$pos,$encoding) . $postfix;
					else
						return $s;
				}else{
					return $s;
				}
			}

			public static function bytes2human($size,$precision = 2) 
			{
			    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			    foreach ($units as $unit) {
			        if ($size >= 1024 && $unit != 'YB') {
			            $size = ($size / 1024);
			        } else {
			            return round($size, $precision) . " " . $unit;
			        }
			    }
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

			public static function lorem($limit = 544)
			{
				$n = 544;
				$s = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. ";
			
				if($limit > $n)
					$s = str_repeat($s, (int)($limit / $n) + 1);

				return self::limitWords($s,$limit);
			}
		}
	}
?>