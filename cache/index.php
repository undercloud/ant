<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title;?> <?php echo $body['second'];?></title>
	<?php echo Ant::script('ebeleh.js');?>
</head>
<body>
	<?= \Ant::init()->get("header")->assign(array('ovarahalla' => range(1,7)))->draw(); ?>

	Life is <?php echo $body['first'];?>

	<input type="text" value="<?php echo htmlentities($escaper['nest'],ENT_QUOTES,"UTF-8");?>">

	some@mail.com

	<?php if($x == true):?>
		ebelehae
	<?php else:?>
		ovarahalla
	<?php endif;?>

	<?php if($_GET and $_GET['x']):?>
		<h1><?php echo $_GET['x'];?></h1>
	<?php endif;?>

	<ul>
	<?php foreach($inside_suka['arr'] as $k=>$v):?>
		<li><?php echo $v['id'];?> - <?php echo $v['name'];?></li>
	<?php endforeach;?>
	</ul>

	<?php $x = md5(time());
		echo $x;;?>
</body>
</html>