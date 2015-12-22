<?php
	namespace Ant
	{
		class Fn
		{
			private static $encoding = 'UTF-8';

			private static function setEncoding($encoding)
			{
				self::$encoding = $encoding;
			}

			public static function iterable($o)
			{
				return (is_array($o) || $o instanceof Traversable || $o instanceof stdClass);
			}

			public static function unicode($s)
			{
				return implode(
					'',
					array_map(
						function ($v) {
							return '&#' . hexdec($v) . ';';
						},
						array_filter(
							explode('\u', $s)
						)
					)
				);
			}

			public static function js($src, $defer = "")
			{
				if (isset(Ant::$plugin->asset)) {
					$src = Ant::$plugin->asset->check($src);
				}

				return '<script type="text/javascript" src="' . $src . '"' . ($defer ? " " . $defer : '') . '></script>';
			}

			public static function css($href, $media = "")
			{
				return '<link type="text/css" rel="stylesheet" href="' . $href . '"' . ($media ? ' media="' . $media . '"' : '') . '/>';
			}

			public static function img($src, $alt = "") 
			{
				return '<img src="' . $src . '" alt="' . $alt . '" />';
			}

			public static function number($n)
			{
				return rtrim(rtrim(number_format((float)$n, 2, '.', ' '), '0'), '.');
			}

			public static function escape($s)
			{
				return htmlentities($s, ENT_QUOTES, self::$encoding);
			}

			public static function decode($s)
			{
				return html_entity_decode($s, ENT_QUOTES, self::$encoding);
			}

			public static function capitalize($s)
			{
				$s = mb_strtolower($s, self::$encoding);

				return mb_strtoupper(mb_substr($s, 0, 1, self::$encoding), self::$encoding) .
					   mb_substr($s, 1, mb_strlen($s, self::$encoding), self::$encoding); 
			}

			public static function capitalizeAll($s)
			{
				return mb_convert_case($s, MB_CASE_TITLE, self::$encoding);
			}

			public static function upper($s)
			{
				return mb_strtoupper($s, self::$encoding);
			}

			public static function lower($s)
			{
				return mb_strtolower($s, self::$encoding);
			}

			public static function url(array $a)
			{
				return http_build_query($a);
			}

			public static function whitespace($s)
			{
				return preg_replace('/\s+/', ' ', $s);
			}

			public static function limit($s, $limit = 250, $postfix="...")
			{
				if (mb_strlen($s,self::$encoding) > $limit) {
					return mb_substr($s, 0, $limit, self::$encoding) . $postfix;
				} else {
					return $s;
				}
			}

			public static function limitWords($s, $limit = 250, $postfix="...")
			{
				if (mb_strlen($s, self::$encoding) > $limit) {
					$pos = mb_strpos($s, ' ', $limit, self::$encoding);
					if (false !== $pos) {
						return mb_substr($s, 0, $pos, self::$encoding) . $postfix;
					} else {
						return $s;
					}
				} else {
					return $s;
				}
			}

			public static function limitMiddle($text,$limit = 128)
			{
				$len = mb_strlen($text,self::$encoding);

				if($len > $limit){
					$mid = (int)(($limit - 3) / 2);
					return mb_substr($text,0,$mid,self::$encoding) . '...' . mb_substr($text,$len - $mid,$len,self::$encoding);
				}else{
					return $text;
				}
			}

			public static function bytes2human($size,$precision = 2) 
			{
				$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
				foreach ($units as $unit) {
					if ($size >= 1024 && $unit != 'YB') {
						$size = ($size / 1024);
					} else {
						return round($size, $precision) . ' ' . $unit;
					}
				}
			}

			public static function roundHuman($size,$precision = 2)
			{
				$units = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
				foreach ($units as $unit) {
					if ($size >= 1000 && $unit != 'Y') {
						$size = ($size / 1000);
					} else {
						return round($size, $precision) . ($unit ? (" " . $unit) : '');
					}
				}
			}

			public static function highlight($string,$word,$class = '')
			{
				$words = array_filter(explode(' ',preg_quote($word)));
				$rx = '/(' . implode('|',$words) . ')/i';

				return preg_replace($rx, '<span class="' . $class . '">$0</span>', $string);
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

			/*public static function slug($text)
			{
				$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
				$text = trim($text, '-');
				$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
				$text = strtolower($text);
				$text = preg_replace('~[^-\w]+~', '', $text);

				return $text;
			}*/

			/*public static function autoUrl($text,$call = null)
			{
				if(null == $call)
					$call = function($s){

					}

				$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
				$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
				$regex .= "([a-z0-9-.]*)\.([a-z]{2,4})"; // Host or IP
				$regex .= "(\:[0-9]{2,5})?"; // Port
				$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
				$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
				$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

			}*/
		}
	}
?>