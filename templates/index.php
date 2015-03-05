<!DOCTYPE html>
<html>
<head>
	<title>{{ $title }} {{ $body.second }}</title>
</head>
<body>
	{@import(header,$body.fortop)}

	Life is {{ $body.first }}

	<input type="text" value="{{{ $escaper.nest }}}">

	some@mail.com

	{@if($x == true)}
		ebelehae
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
</body>
</html>