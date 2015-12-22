<?php
	
	require __DIR__ . '/../leaf/Leaf.php';


	echo Leaf::init(array('format' => true,'indent' => '  '))
		->el('input:checkbox:disabled:checked');
?>