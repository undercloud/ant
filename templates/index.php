<!DOCTYPE html>
<html>
<head>
	<title>{{ $title }} {{ $body.second }}</title>
	{{ Ant::script('ebeleh.js') }}
</head>
<body>
	{@import(header,array('ovarahalla' => range(1,2)))}

	Life is {{ $body.first }}

	<input type="text" value="{{{ $escaper.nest }}}">

	some@mail.com

	{@if(true == false)}
		ebelehae
	{@elseif(false == false)}
		wassup
	{@else}
		ovarahalla
	{@endif}

	{@if($.get and $.get.x)}
		<h1>{{ $.get.x }}</h1>
	{@endif}

	<ul>
	{@foreach($inside_suka.arr as $k=>$v)}
		<li>{{ $v.id }} - {{ $v.name }}</li>
	{@endforeach}
	</ul>

	{@
		$x = md5(time());
		echo $x;
	}

	{@forelse ($mas as $m)}

	{@empty}
	Array is empty
	{@endforelse}
</body>
</html>