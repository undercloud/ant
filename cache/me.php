<?php if(!(false)):?>
	<?php echo $this->js('/script.js');?>

	<h1><?php echo \Ant\Fn::escape($GLOBALS['sasaika']);?></h1>

	<?php switch('kitty'):?>
<?php case('kitty'):?>
			<?php echo \Ant\Fn::escape($_SERVER['HTTP_HOST']);?>
		<?php break; ?>
	<?php endswitch; ?>

	<img width="100%" src="" />
 
	<?php echo $this->plugin->youtube->embed('hchTjwPZPn8');?>

	<h1><?php echo \Ant\Fn::escape($this->capitalize('sasai'));?></h1>

<?php endif; ?>