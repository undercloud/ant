# ant

[![Join the chat at https://gitter.im/undercloud/ant](https://badges.gitter.im/undercloud/ant.svg)](https://gitter.im/undercloud/ant?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
Awesome New Templates

```PHP
<?php
	require '/path/to/ant.php';

	/* global init */
	Ant::init()
	->setup(
		array(
			//readable path
			'view'  => '/path/to/templates',
			//readable and wrireable path
			'cache' => '/path/to/cache'
		)
	);

	...

	/* usage */
	echo Ant::init()
	->get('path.to.index')
	->assign(
		array(
			'title' => 'Welcome',
			'content' => 'Hello'
		)
	)
	->draw()
?>

```

```HTML
<!DOCTYPE html>
<html>
	<head>
		<title>{{ $title }}</title>
	</head>
	<body>
		{{ $content }}
	</body>
</html>

```
