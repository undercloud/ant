
<?php
	$sasai = 'lalka';
	$dp = array(
		'linda' => 'header'
	);

	$range = range(0, 10);
?>
<?php echo \Ant\Ant::view($dp['linda'], get_defined_vars()); ?>
<h1>Comments</h1>
<?php Ant\Parser::each('comments',$comments,'com') ?>
<hr />

<?php echo \Ant\Fn::escape(DateTime::createFromFormat('Y-m-d','2012-01-04')->format('Y-m-d H:s')); ?>

<?php echo \Ant\Fn::escape($this->capitalize('moscow')); ?>

<?php echo \Ant\Fn::css('/link/to/style.css'); ?>

<h1 style="font-size: 200px;"><?php echo $this->unicode('\u30BD'); ?></h1>
<style>
.even {
	color: blue;
}

.odd {
	color: red;
}
</style>
lal

<div>
	<i><?php echo \Ant\Fn::escape($this->template('Hello {1} from {2}','Username',['Gandaras','Ras','Mas'])); ?></i>
</div>

<label class="label label--require">

</label>

<h1>DateTime <?php echo \Ant\Fn::escape($this->date('-1 day','Y-m-d H:i:s')); ?></h1>

<ul>
	<?php if(\Ant\Fn::iterable($range) and \Ant\Fn::count($range)): $range = new \Ant\StateIterator($range); foreach($range as $x): ?>
		<li class="<?php if($range->isOdd()): ?> odd <?php else: ?>even <?php endif; ?>">
			<?php if($range->isFirst()): ?>
				<b><?php echo \Ant\Fn::escape($x); ?></b>
			<?php elseif($range->isLast()): ?>
				<i><?php echo \Ant\Fn::escape($x); ?></i>
			<?php else: ?>
				<?php echo \Ant\Fn::escape($x); ?>
			<?php endif; ?>
		</li>
	<?php endforeach; $range = $range->restore();  else: ?>
		:(
	<?php endif; ?>
</ul>