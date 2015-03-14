<div>
	<?php if(true):?>
	<div style="border-top:5px solid #651;">
	<?php endif;?>

	<div><?php echo htmlentities(__DIR__,ENT_QUOTES,"UTF-8");?> <?php echo date('Y:m:d H:i:s');?></div>

	<?php echo (isset($_GET['p']) and $_GET['p']) ? $_GET['p'] : 'Mamu ebal gan\'don';?>

	<?php echo GANDURAS;?>

	<?php
		class X {
			public static function p()
			{
				echo 'All Fine X::p';
			}
		}
	?>

	
		@forelse ?
	

	<?php
		echo "@forelse";
	?>
	<h1><?php echo X::p();?></h1>
</div>