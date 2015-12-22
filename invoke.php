<?php
	class In
	{
		public function __invoke($v)
		{
			return $v * $v;
		}
	}

	$s = new stdClass;

	$s->some = new In();

	echo ($s->some)(7);
?>