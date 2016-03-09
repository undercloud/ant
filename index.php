<?php
	error_reporting(-1);

	require __DIR__ . '/Ant/Ant.php';
	require __DIR__ . '/vendor/autoload.php';

	//$GLOBALS['sasaika'] = 'Hellow';

	\Ant\Ant::init()
	->setup(
		array(
			'view'  => $_SERVER['DOCUMENT_ROOT'] . "templates",
			'cache' => $_SERVER['DOCUMENT_ROOT'] . "cache",
			'logic' => $_SERVER['DOCUMENT_ROOT'] . "logic",
			'extension' => 'php',
			'debug' => true,
			'minify' => false
		)
	)
	->activate('Asset')
	->activate('YouTube')
	->activate('Faker',['locale' => 'ru_RU'])
	//->activate('Markdown')
	//->activate('Validator')
	->rule('~{@.+?@}~',function($match){

		return '<h1>Ebal ebal</h1>';
	});

	echo \Ant\Ant::init()
		->get('md')
		->assign([
			'comments' => [
				[
					'date' => '12.01.2005',
					'text' => 'Awesome'
				],
				[
					'date' => '11.02.2004',
					'text' => 'Fu... it!'
				],
				[
					'date' => '04.09.2003',
					'text' => 'Nice'
				]
			]
		])
		->draw();
?>