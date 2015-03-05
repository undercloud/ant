<?php
	namespace Ant
	{
		class Helper
		{
			public static function parseVariable($e)
			{
				$exp = explode('.',$e);

				foreach ($exp as $key => $value) {
					if(0 == $key){
						$exp[$key] = $value;
					}else if($key == 1 and $exp[0] === '$'){
						$exp[$key] = '_' . strtoupper($value);
					}else{
						$exp[$key] = '[\'' . $value . '\']';
					}
				}

				return implode('',$exp);
			}
		}
	}
?>