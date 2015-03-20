<?php
	namespace Ant
	{
		class Inherit 
		{
			public static function checkNext($view)
			{
				$name = array();
				preg_match('/{@extends.+?}/',$view,$name);

				if(!$name)
					return false;

				$name = $name[0];
				$name = trim($name,'{}');
				$name = str_replace('@extends', '', $name);
				$name = trim($name);
				$name = substr($name,1,-1);

				$path = \Ant::settings('path') . DIRECTORY_SEPARATOR  . str_replace('.', DIRECTORY_SEPARATOR , $name) . '.php';

				$io = IO::init()->in($path);
				$nextview = $io->get();
				$io->out();

				return array(
					'path' => $path,
					'view' => $nextview
				);
			}

			public static function resolveChain($view,$path,$ant)
			{
				$chain = $next = array(
					'path' => $path,
					'view' => $view
				);

				while(true){	
					$next = self::checkNext($next['view']);
					if(false === $next)
						break;
					else
						$chain[$next['path']] = $next['view'];
				}

				return array_reverse($chain);
			}

			public static function extend($view,$path,$ant)
			{
				$chain = self::resolveChain($view,$path,$ant);
				$view = implode('',array_values($chain));

				return $view;

				$injects = array();
				preg_match_all('/{@inject.*?}.*?{@(rewrite|append|prepend)}/ms',$view,$injects);

				$map = array();
				if(isset($injects[0])){
					foreach($injects[0] as $k=>$s){
						$m = array();
						preg_match('/{@inject.*?}/',$s,$m);

						$name = trim(str_replace('@inject','',$m[0]),' {()}');

						$s = preg_replace('/{@inject\s*?\(\s*?' . $name . '\s*?\)\s*?}/','',$s);
						$s = str_replace('{@' . $injects[1][$k] . '}','',$s);

						$map[] = array(
							$name,
							$s,
							$injects[1][$k]
						);
					}
				}

				foreach($map as $key=>$value){
					$view = preg_replace_callback(
						'/{@section\s*?\(\s*?' . $value[0] . '\s*?\)\s*?}.*?{@end}/ms',
						function($e)use($value){
							switch($value[2]){
								case 'prepend':
									return '{@section(' . $value[0] . ')}' . $value[1] . $e[0] . '{@end}';
								break;

								case 'append':
									return '{@section(' . $value[0] . ')}' . $e[0] . $value[1] . '{@end}';
								break;

								case 'rewrite':
									return '{@section(' . $value[0] . ')}' . $value[1] . '{@end}';
								break;
							}
						},
						$view
					);
				}

				$view = str_replace($chain,'',$view);
				$view = preg_replace('/{@(section|inject).*?}/','', $view);
				$view = preg_replace('/{@(rewrite|append|prepend|end)}/','',$view);

				return $view;
			}
		}
	}
?>