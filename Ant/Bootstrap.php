<?php
	$class = array(
		'Render',
		'Parser',
		'Helper',
		'IO',
		'Fn',
		'Cache',
		'Exception',
		'Inherit',
		'StateIterator',
		'Settings',
		'Event',
		'Plugin',
		'Plugins/Base'
	);

	foreach ($class as $c) {
		require_once __DIR__ . '/' . $c . '.php';
	}
?>