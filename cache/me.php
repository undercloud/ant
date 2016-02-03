
<?php 
	$sasai = 'lalka';
	$dp = array(
		'linda' => 'header'
	);
?>
<?php echo \Ant\Ant::init()->get($dp['linda'])->assign( get_defined_vars())->draw(); ?>
<h1>Comments</h1>
<?php Ant\Parser::each('comments',$comments,'com') ?>
<hr />