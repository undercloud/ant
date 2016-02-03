
<?php 
	$sasai = 'lalka';
	$dp = array(
		'linda' => 'header'
	);
?>
@import($dp.linda, $.scope)
<h1>Comments</h1>
@each('comments',$comments,'com')
<hr />