###---<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title;?> <?php echo $body['second'];?> <?php echo $_SERVER['DOCUMENT_ROOT'];?></title>
</head>
<body>
	<h1><?php echo (isset($_GET['ebeleh']) and $_GET['ebeleh']) ? $_GET['ebeleh'] : 'Dinahuile';?></h1>

	<?php echo \Ant::init()->get("header")->assign(array('ovarahalla' => range(1,2)))->draw(); ?>

	Life is <?php echo $body['first'];?>

	<input type="text" value="<?php echo htmlentities($escaper['nest'],ENT_QUOTES,"UTF-8");?>" data-host="<?php echo htmlentities($_SERVER['HTTP_HOST'],ENT_QUOTES,"UTF-8");?>">

	 @{{ some@mail.com }} 

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

	<?php if(count($mas) and Ant::iterable($mas)): foreach($mas as $m): ?>
		<?php echo $m;?>
	<?php endforeach; else: ?>
		Array is empty
	<?php endif; ?>

	<?php switch($boom): case 'first':?>
		<h1><?php echo $boom;?></h1>
		<?php break; case 'second':;?>
			<h1>Lalka</h1>
		<?php break;?>

		<?php default:?>
		<h1>Not boom</h1>
		<?php break;?>
	<?php endswitch;?>
</body>
</html>---C:/OpenServer/OpenServer/domains/an.t/trunk/templates\index.php###