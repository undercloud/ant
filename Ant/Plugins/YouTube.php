<?php
namespace Ant\Plugins;

use Ant\Ant;

class YouTube extends Base
{
	private $options = array();

	protected static $serviceUrl = '//www.youtube.com/embed/';

	protected static $defaultParams = array();

	protected static $defaultAttrs = array(
		'type'        => 'text/html',
		'width'       => '640',
		'height'      => '390',
		'frameborder' => '0'
	);

	public function __construct(array $options = array())
	{
		$this->options = $options;
	}

	public function __invoke()
	{
		return call_user_func_array(
			array($this,'embed'),
			func_get_args()
		);
	}

	public static function setup(array $params, array $attrs)
	{
		static::$defaultParams = array_merge(
			static::$defaultParams,
			$params
		);

		static::$defaultAttrs = array_merge(
			static::$defaultAttrs,
			$attrs
		);
	}

	//hq, mq, sd, maxres
	public static function preview($videoId, $mode = '')
	{
		return '//img.youtube.com/vi/' . $videoId . '/' . $mode . 'default.jpg';
	}

	public static function embed($videoId, array $params = array(), array $attrs = array())
	{
		$params = array_merge(
			static::$defaultParams,
			$params
		);

		$attrs = array_merge(
			static::$defaultAttrs,
			$attrs
		);

		$attrs['src'] = static::$serviceUrl . $videoId . ($params ? ('?' . http_build_query($params)) : '');

		$attrs = array_map(
			function ($key, $value) {
				if (is_integer($key)) {
					return $value;
				} else {
					return $key . '="' . $value . '"';
				}
			},
			array_keys($attrs),
			array_values($attrs)
		);

		return '<iframe ' . implode(' ', $attrs) . '></iframe>';
	}

	/**
	 * Register plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function register(Ant $ant)
	{
		$params = (isset($this->options['params']) ? $this->options['params'] : array());
		$attrs  = (isset($this->options['attrs'])  ? $this->options['attrs']  : array());

		self::setup($params, $attrs);

		$ant->register('youtube', new self());
	}

	/**
	 * Unregister plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function unregister(Ant $ant)
	{
		$ant->unregister('youtube');
	}
}
?>