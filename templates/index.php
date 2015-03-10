<!DOCTYPE html>
<html>
<head>
	<title>{{ $title }} {{ $body.second }} {{ $.server.DOCUMENT_ROOT }}</title>
	{{ Ant::script('ebeleh.js') }}
</head>
<body>
	<h1>{{ $.get.ebeleh or 'Dinahuile' }}</h1>

	{@import( header,array('ovarahalla' => range(1,2)) ) }

	Life is {{ $body.first }}

	<input type="text" value="{{{ $escaper.nest }}}" data-host="{{{ $.server.HTTP_HOST }}}">

	some@mail.com

	{@section(main)}
		Content must be replaced
	{@end}

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

	{@forelse($mas as $m)}
		{{ $m }}
	{@empty}
		Array is empty
	{@endforelse}
</body>
</html>