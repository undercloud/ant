<?php

namespace Ant;

/**
 * Functions
 */
class Fn
{
	private static $ant;
	private static $shared   = array();
	private static $encoding = 'UTF-8';

	public static function apply(Ant $ant)
	{
		self::$ant = $ant;
	}

	/**
	 * Share function
	 *
	 * @param string $name function name
	 * @param mixed  $call callback
	 *
	 * @return void
	 */
	public static function share($name, $call)
	{
		if (method_exists('\Ant\Fn', $name) or self::isShared($name)) {
			throw new Exception(
				sprintf('Cannot register %s', $name)
			);
		}

		self::$shared[$name] = $call;
	}

	/**
	 * Check shared function
	 *
	 * @param string $what function name
	 *
	 * @return boolean
	 */
	public static function isShared($what)
	{
		return array_key_exists($what, self::$shared);
	}

	/**
	 * Handle call shared
	 *
	 * @param string $fn   name
	 * @param array  $args arguments
	 *
	 * @return mixed
	 */
	public static function call($fn, $args)
	{
		$check = (
			array_key_exists($fn, self::$shared)
			and is_callable(self::$shared[$fn])
		);

		if ($check) {
			return call_user_func_array(self::$shared, $fn);
		} else {
			throw new Exception(
				sprintf('Cannot call \\Ant\\Fn::%s as function', $name)
			);
		}
	}

	/**
	 * Magic __call
	 *
	 * @param string $fn   name
	 * @param array  $args arguments
	 *
	 * @return mixed
	 */
	public function __call($fn, $args)
	{
		return self::call($fn, $args);
	}

	/**
	 * Magic __callStatic
	 *
	 * @param string $fn   name
	 * @param array  $args arguments
	 *
	 * @return mixed
	 */
	public static function __callStatic($fn, $args)
	{
		return self::call($fn, $args);
	}

	/**
	 * Set encoding for string functions
	 *
	 * @param string $encoding name
	 *
	 * @return void
	 */
	public static function setEncoding($encoding)
	{
		self::$encoding = $encoding;
	}

	/**
	 * Check if instance can be used in loops
	 *
	 * @param mixed $o instance for check
	 *
	 * @return boolean
	 */
	public static function iterable($o)
	{
		return (
			is_array($o)
			or $o instanceof \Traversable
			or $o instanceof \stdClass
		);
	}

	/**
	 * Count collection items
	 *
	 * @param array|object|Traversable|Countable $o collection
	 *
	 * @return integer
	 */
	public static function count($o)
	{
		if ($o instanceof Traversable) {
			return iterator_count($o);
		} else if (is_array($o) or $o instanceof  \Countable) {
			return @count($o);
		} else if (is_object($o)) {
			return count((array)$o);
		}

		return 0;
	}

	/**
	 * Check if entity is blank
	 *
	 * @param mixed $what value for check
	 *
	 * @return boolean
	 */
	public static function isBlank($what)
	{
		if (is_string($what)) {
			$what = trim($what);
		}

		return (
			($what === '')    or
			($what === null)  or
			($what === false) or
			(is_array($what)  and 0 == count($what))
		);
	}

	/**
	 * Check if entity is empty
	 *
	 * @param mixed $what value for check
	 *
	 * @return boolean
	 */
	public static function isEmpty($what)
	{
		if (is_string($what)) {
			$what = trim($what);
		}

		return @empty($what);
	}

	/**
	 * Convert all unicode symbols \uxxxx to html entity &#xxxx;
	 *
	 * @param string $s unicode string
	 *
	 * @return string
	 */
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

	/**
	 * Template with placeholders
	 *
	 * @return string
	 */
	public static function template()
	{
		$args = func_get_args();
		$tmpl = array_shift($args);

		$args = array_map(
			function ($item) {
				if (is_array($item)) {
					return implode(
						', ',
						array_filter(
							$item,
							function ($el) {
								return (is_scalar($el) and !self::isBlank($el));
							}
						)
					);
				}

				return $item;
			},
			$args
		);

		$tmpl = preg_replace_callback(
			'~\{[0-9]{1,2}\}~',
			function ($e) {
				return '%' . trim($e[0], '{}') . '$s';
			},
			$tmpl
		);

		return vsprintf($tmpl, $args);
	}

	/**
	 * Date helper
	 *
	 * @param mixed  $date   date
	 * @param string $format date format
	 * @param mixed  $tz     timezone
	 *
	 * @return string
	 */
	public static function date($date, $format = 'Y-m-d H:i:s', $tz = null)
	{
		if ($tz) {
			if (is_numeric($tz)) {
				$tz = timezone_name_from_abbr('', $tz, 0);
			}

			$tz = new \DateTimeZone($tz);
		}

		$datetime = new \DateTime($date, $tz);

		return $datetime->format($format);
	}

	/**
	 * Embed JS script
	 *
	 * @param string $src   path to string
	 * @param string $defer defer or\and async attr
	 *
	 * @return string
	 */
	public static function js($src, $defer = '')
	{
		if (self::$ant instanceof Ant and isset($ant->plugin->asset)) {
			$src = $ant->plugin->asset($src);
		}

		return '<script type="text/javascript" src="' . $src . '"' . ($defer ? " " . $defer : '') . '></script>';
	}

	/**
	 * Embed CSS script
	 *
	 * @param string $href  path to stylesheet
	 * @param string $media media value
	 *
	 * @return string
	 */
	public static function css($href, $media = '')
	{
		if (self::$ant instanceof Ant and isset($ant->plugin->asset)) {
			$href = $ant->plugin->asset($href);
		}

		return '<link type="text/css" rel="stylesheet" href="' . $href . '"' . ($media ? ' media="' . $media . '"' : '') . ' />';
	}

	/**
	 * Escape string
	 *
	 * @param string $s      unescaped string
	 * @param bool   $double double escaping
	 *
	 * @return string
	 */
	public static function escape($s, $double = true)
	{
		return htmlentities($s, ENT_QUOTES, self::$encoding, $double);
	}

	/**
	 * Unescape string
	 *
	 * @param string $s escaped string
	 *
	 * @return string
	 */
	public static function decode($s)
	{
		return html_entity_decode($s, ENT_QUOTES, self::$encoding);
	}

	/**
	 * Capitalize string
	 *
	 * @param string $s string
	 *
	 * @return string capitalized string
	*/
	public static function capitalize($s)
	{
		$s = mb_strtolower($s, self::$encoding);

		return mb_strtoupper(mb_substr($s, 0, 1, self::$encoding), self::$encoding) .
			   mb_substr($s, 1, mb_strlen($s, self::$encoding), self::$encoding);
	}

	/**
	 * Capitalize all words in string
	 *
	 * @param string $s string
	 *
	 * @return string capitalized words
	 */
	public static function capitalizeAll($s)
	{
		return mb_convert_case($s, MB_CASE_TITLE, self::$encoding);
	}

	/**
	 * Uppercase string
	 *
	 * @param string $s string
	 *
	 * @return string
	 */
	public static function upper($s)
	{
		return mb_strtoupper($s, self::$encoding);
	}

	/**
	 * Lowercase string
	 *
	 * @param string $s string
	 *
	 * @return string
	 */
	public static function lower($s)
	{
		return mb_strtolower($s, self::$encoding);
	}

	/**
	 * Build url
	 *
	 * @param array $a url params
	 *
	 * @return string
	*/
	public static function url(array $a)
	{
		return http_build_query($a, '', '&amp;');
	}

	/**
	 * Remove double whitespace
	 *
	 * @param string $s string
	 *
	 * @return string
	 **/
	public static function whitespace($s)
	{
		return preg_replace('/\s+/', ' ', $s);
	}

	/**
	 * Limit string length
	 *
	 * @param string  $s       string
	 * @param integer $limit   size
	 * @param string  $postfix decoration
	 *
	 * @return string
	 */
	public static function limit($s, $limit = 250, $postfix = '...')
	{
		if (mb_strlen($s, self::$encoding) > $limit) {
			return mb_substr($s, 0, $limit, self::$encoding) . $postfix;
		} else {
			return $s;
		}
	}

	/**
	 * Limit string length soft
	 *
	 * @param string  $s       string
	 * @param integer $limit   size
	 * @param string  $postfix decoration
	 *
	 * @return string
	 */
	public static function limitWords($s, $limit = 250, $postfix = '...')
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

	/**
	 * Limit string length by middle
	 *
	 * @param string  $s       string
	 * @param integer $limit   size
	 * @param string  $postfix decoration
	 *
	 * @return string
	 */
	public static function limitMiddle($s, $limit = 250, $postfix = '...')
	{
		$len = mb_strlen($s, self::$encoding);

		if ($len > $limit) {
			$mid = (int)(($limit - 3) / 2);
			return (
				mb_substr($s, 0, $mid, self::$encoding) . $postfix .
				mb_substr($s, $len - $mid, $len, self::$encoding)
			);
		} else {
			return $s;
		}
	}

	/*
	public static function ordinal($cdnl)
	{
		$c   = abs($cdnl) % 10;
		$ext = ((abs($cdnl) %100 < 21 && abs($cdnl) %100 > 4) ? 'th'
			: (($c < 4) ? ($c < 3) ? ($c < 2) ? ($c < 1)
			? 'th' : 'st' : 'nd' : 'rd' : 'th')
		);

		return $cdnl.$ext;
	}

	public static function number($n)
	{
		return rtrim(rtrim(number_format((float)$n, 2, '.', ' '), '0'), '.');
	}
	*/

	/**
	 * Bytes to human readable
	 *
	 * @param integer|string $size      size in bytes
	 * @param integer        $precision precision
	 *
	 * @return string
	 */
	public static function bytesHuman($size, $precision = 2)
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

	/**
	 * Long number to human readable
	 *
	 * @param integer|string $size      size in bytes
	 * @param integer        $precision precision
	 *
	 * @return string
	 */
	public static function roundHuman($size, $precision = 2)
	{
		$units = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
		foreach ($units as $unit) {
			if ($size >= 1000 && $unit != 'Y') {
				$size = ($size / 1000);
			} else {
				return round($size, $precision) . ($unit ? (' ' . $unit) : '');
			}
		}
	}

	/**
	 * Doctype shortcut
	 *
	 * @param string $d shortcut
	 *
	 * @return string
	 */
	public static function doctype($d = 'HTML5')
	{
		switch($d){
			default:
			case 'HTML5':               return '<!DOCTYPE html>';
			case 'XHTML11':             return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
			case 'XHTML1_STRICT':       return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
			case 'XHTML1_TRANSITIONAL': return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			case 'XHTML1_FRAMESET':     return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
			case 'XHTML_BASIC1':        return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">';
			case 'HTML4_STRICT':        return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
			case 'HTML4_LOOSE':         return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
			case 'HTML4_FRAMESET':      return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
		}
	}

	/**
	 * Fake text generator
	 *
	 * @param integer $limit fake text length
	 *
	 * @return string
	 */
	public static function lorem($limit = 544)
	{
		$n = 544;
		$s = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. ";

		if ($limit > $n) {
			$s = str_repeat($s, (int)($limit / $n) + 1);
		}

		return self::limitWords($s, $limit);
	}
}
?>