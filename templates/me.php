
<?php
	$sasai = 'lalka';
	$dp = array(
		'linda' => 'header'
	);

	$range = range(0, 10);
?>
@import($dp.linda, $.scope)
<h1>Comments</h1>
@each('comments',$comments,'com')
<hr />

{{ DateTime::createFromFormat('Y-m-d','2012-01-04')->format('Y-m-d H:s') }}

{{ ::capitalize('moscow') }}

@css('/link/to/style.css')

<h1 style="font-size: 200px;">{{{ ::unicode('\u30BD') }}}</h1>
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
	<i>{{ ::template('Hello {1} from {2}','Username',['Gandaras','Ras','Mas']) }}</i>
</div>

<label class="label label--require">

</label>

<h1>DateTime {{ ::date('-1 day','Y-m-d H:i:s') }}</h1>

<ul>
	@forelse($range as $x)
		<li class="@if($range->isOdd()) odd @else even @endif">
			@if ($range->isFirst())
				<b>{{ $x }}</b>
			@elseif ($range->isLast())
				<i>{{ $x }}</i>
			@else
				{{ $x }}
			@endif
		</li>
	@empty
		:(
	@endforelse
</ul>