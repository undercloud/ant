<?php
	namespace Ant\Plugins;

	class PluginBase {}

	class YouTube extends PluginBase
	{
		protected static $service_url = '//www.youtube.com/embed/';

		protected static $default_params = array();

		protected static $default_attrs = array(
			'type'        => 'text/html',
			'width'       => '640',
			'height'      => '390',
			'frameborder' => 0
		);

		public static function setup(array $params, array $attrs)
		{
			static::$default_params = array_merge(
				static::$default_params,
				$params
			);

			static::$default_attrs = array_merge(
				static::$default_attrs,
				$attrs
			);
		}

		//hq, mq, sd, maxres
		public static function preview($video_id, $mode = '')
		{
			return '//img.youtube.com/vi/' . $video_id . '/' . $mode . 'default.jpg';
		}

		public static function embed($video_id, array $params = array(), array $attrs = array())
		{
			$params = array_merge(
				static::$default_params,
				$params
			);

			$attrs = array_merge(
				static::$default_attrs,
				$attrs
			);

			$attrs['src'] = static::$service_url . $video_id . ($params ? ('?' . http_build_query($params)) : '');

			$attrs = array_map(
				function($key, $value){
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
	}
?>
