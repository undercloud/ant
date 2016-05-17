<?php
namespace Ant\Plugins\Validator;

class Strict
{
	private static $requiredAttrs = array(
		'a' => array('href'),
		'img' => array('src', 'alt'),
		'abbr' => array('title'),
		'area' => array('href', 'alt'),
		'input' => array('type')
	);

	private static $deprecatedTags = array(
		'acronym',
		'applet',
		'basefont',
		'center',
		'dir',
		'font',
		'isindex',
		'listing',
		'noframes',
		'strike',
		'tt',
		'xmp'
	);

	private static $nonstandardTags = array(
		'blink',
		'big',
		'bgsound',
		'command',
		'comment',
		'frame',
		'frameset',
		'marquee',
		'multicol',
		'noembed',
		'nobr',
		'noindex',
		'plaintext',
		'spacer'
	);

	private static $deprecatedAttrs = array(
		'rev' => array('link', 'a'),
		'charset' => array('link', 'a'),
		'shape' => array('a'),
		'coords' => array('a'),
		'longdesc' => array('img', 'iframe'),
		'target' => array('link'),
		'nohref' => array('area'),
		'profile' => array('head'),
		'version' => array('html'),
		'name' => array('img'),
		'scheme' => array('meta'),
		'archive' => array('object'),
		'classid' => array('object'),
		'codebase' => array('object'),
		'codetype' => array('object'),
		'declare' => array('object'),
		'standby' => array('object'),
		'valuetype' => array('param'),
		'type' => array('param'),
		'axis' => array('td', 't'),
		'abbr' => array('td', 't'),
		'scope' => array('td'),
		'align' => array('caption', 'iframe', 'img', 'input', 'object', 'legend', 'table', 'hr', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'col', 'colgroup', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr'),
		'alink' => array('body'),
		'link' => array('body'),
		'vlink' => array('body'),
		'text' => array('body'),
		'background' => array('body'),
		'bgcolor' => array('table', 'tr', 'td', 'th', 'body'),
		'border' => array('table', 'object'),
		'cellpadding' => array('table'),
		'cellspacing' => array('table'),
		'char' => array('col', 'colgroup', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr'),
		'charoff' => array('col', 'colgroup', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr'),
		'clear' => array('br'),
		'compact' => array('dl', 'menu', 'ol', 'ul'),
		'frame' => array('table'),
		'frameborder' => array('iframe'),
		'hspace' => array('img', 'object'),
		'vspace' => array('img', 'object'),
		'marginheight' => array('iframe'),
		'marginwidth' => array('iframe'),
		'noshade' => array('hr'),
		'nowrap' => array('td', 'th'),
		'rules' => array('table'),
		'scrolling' => array('iframe'),
		'size' => array('hr'),
		'type' => array('li', 'ol', 'ul'),
		'valign' => array('col', 'colgroup', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr'),
		'width' => array('hr', 'table', 'td', 'th', 'col', 'colgroup', 'pre')
	);

	/**
	 * Init validator
	 *
	 * @param SimpleXMLElement $sxe object
	 *
	 * @return void
	 */
	public function __construct(\SimpleXMLElement $sxe)
	{
		self::walk($sxe);
	}

	/**
	 * Check deprecated tag
	 *
	 * @param string $tag html tag
	 *
	 * @return bool
	 */
	public static function isDeprecatedTag($tag)
	{
		return in_array($tag, self::$deprecatedTags);
	}

	/**
	 * Check non-standard tag
	 *
	 * @param string $tag html tag
	 *
	 * @return bool
	 */
	public static function isNonStandardTag($tag)
	{
		return in_array($tag, self::$nonstandardTags);
	}

	/**
	 * Check deprecated attr
	 *
	 * @param string $tag  html tag
	 * @param string $attr element attr
	 *
	 * @return bool
	 */
	public static function isDeprecatedAttr($tag, $attr)
	{
		return (
			array_key_exists($attr, self::$deprecatedAttrs)
			and
			in_array($tag, self::$deprecatedAttrs[$attr])
		);
	}

	/**
	 * Check required attr
	 *
	 * @param string $tag  html tag
	 * @param array  $attr element attr
	 *
	 * @return bool
	 */
	public static function checkRequired($tag, array $attr)
	{
		if (array_key_exists($tag, self::$requiredAttrs)) {
			foreach (self::$requiredAttrs[$tag] as $ra) {
				if (false == array_key_exists($ra, $attr)) {
					return $ra;
				}
			}
		}

		return true;
	}

	/**
	 * Check tag case
	 *
	 * @param string $tag html tag
	 *
	 * @return bool
	 */
	public static function isLowerCase($tag)
	{
		return ($tag === strtolower($tag));
	}

	/**
	 * Validate node
	 *
	 * @param string $tag  html tag
	 * @param array  $attr element attr
	 *
	 * @return void
	 */
	public static function read($tag, array $attr)
	{
		if (false == self::isLowerCase($tag)) {
			trigger_error(sprintf("Tag '%s' must be lowercase", $tag));
		}

		if (true == self::isDeprecatedTag($tag)) {
			trigger_error(sprintf("Tag '%s' is deprecated", $tag));
		}

		if (true == self::isNonStandardTag($tag)) {
			trigger_error(sprintf("Tag '%s' is non-standard", $tag));
		}

		if (true !== ($require = self::checkRequired($tag, $attr))) {
			trigger_error(sprintf("Required attribute '%s' not found in '%s'", $require, $tag));
		}

		foreach ($attr as $key => $value) {
			if (false == self::isLowerCase($key)) {
				trigger_error(sprintf("Attribute '%s' must be lowercase in tag '%s'", $key, $tag));
			}

			if (true == self::isDeprecatedAttr($tag, $key)) {
				trigger_error(sprintf("Attribute '%s' is deprecated in tag '%s'", $key, $tag));
			}
		}
	}

	/**
	 * DOM recursive walker
	 *
	 * @param SimpleXMLElement $sxe object
	 *
	 * @return void
	 */
	public static function walk(\SimpleXMLElement $sxe)
	{
		foreach ($sxe as $key => $item) {
			if ($item instanceof \SimpleXMLElement) {
				self::walk($item);
			}

			$a = (array)$item;
			self::read($key, isset($a['@attributes']) ? $a['@attributes'] : array());
		}
	}
}
?>