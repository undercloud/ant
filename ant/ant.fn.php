<?php
	namespace Ant
	{
		class Fn
		{
			public static function script($src,$defer = "")
			{
				return '<script type="text/javascript" src="' . $path . '"' . ($defer ? " " . $defer : '') . '></script>';
			}

			public static function link($href,$type = "text/css",$rel = "stylesheet",$media = "")
			{
				return '<link type="' . $type . ' rel="' . $rel . '" href="' . $href . '"' . ($media ? ' media="' . $media . '"' : '') . '/>';
			}
		}
	}
?>