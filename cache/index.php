<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title;?> <?php echo $body['second'];?></title>
	<?php echo Ant::script('ebeleh.js');?>
</head>
<body>
	<?php echo \Ant::init()->get("header")->assign(array('ovarahalla' => range(1,2)))->draw(); ?>

	Life is <?php echo $body['first'];?>

	<input type="text" value="<?php echo htmlentities($escaper['nest'],ENT_QUOTES,"UTF-8");?>">

	some@mail.com

	<?php if(true == false):?>
		ebelehae
	<?php elseif(false == false):?>
		wassup
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

	<?php if(count($mas)) :?>

	<?php endforeach; else: ?>
	Array is empty
	<?php endif; ?>
</body>
</html>