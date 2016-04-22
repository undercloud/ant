<?php
namespace Ant;

/**
 * Common helper
 *
 * @package Ant
 */
class Helper
{
	const VARIABLE_REGEXP = '/(\$|->)[A-Za-z0-9_\.]+/';

	/**
	 * Replace and trim string
	 *
	 * @param mixed $what  part
	 * @param mixed $where target
	 *
	 * @return string
	*/
	public static function clean($what, $where)
	{
		return trim(str_replace($what, '', $where));
	}

	/**
	 * Transform fake path to real
	 *
	 * @param string $fakepath template path
	 *
	 * @return string
	 */
	public static function realPath($fakepath)
	{
		return str_replace('.', DIRECTORY_SEPARATOR, $fakepath);
	}

	/**
	 * Parse variable
	 *
	 * @param array $e regexp matches
	 *
	 * @return string
	 */
	public static function parseVariable($e)
	{
		$exp = explode('.', $e);

		foreach ($exp as $key => $value) {
			if (0 == $key) {
				$exp[$key] = $value;
			} else if ((int)$key == 1 and $exp[0] === '$') {
				if ('plugin' == $value) {
					$exp[0] = '$this->ant->';
					$exp[1] = 'plugin';
				} else if (0 === strpos($value, 'lang')) {
					$exp[0] = '$this->ant->plugin->';
				} else if ('scope' == $value) {
					$exp[$key] = 'get_defined_vars()';
					unset($exp[0]);
				} else {
					$exp[$key] = ($value != 'globals' ? '_' : '') . strtoupper($value);
				}
			} else {
				$exp[$key] = '[\'' . $value . '\']';
			}
		}

		return implode($exp);
	}

	/**
	 * Search and parse variables in given string
	 *
	 * @param string $s template string
	 *
	 * @return string
	 */
	public static function findVariable($s)
	{
		return preg_replace_callback(
			self::VARIABLE_REGEXP,
			function ($l) {
				return Helper::parseVariable($l[0]);
			},
			$s
		);
	}

	/**
	 * Find and perform 'or' syntax
	 *
	 * @param string $s template string
	 *
	 * @return string
	 */
	public static function findOr($s)
	{
		return preg_replace(
			'/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s',
			'(isset($1) and !\Ant\Fn::isEmpty($1)) ? $1 : $2',
			$s
		);
	}
}
?>