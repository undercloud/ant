<!DOCTYPE html>
<html>
<head>
	<title>{{ $title }} {{ $body.second }} {{ $.server.DOCUMENT_ROOT }}</title>
</head>
<body>
	<h1>{{ $.get.ebeleh or 'Dinahuile' }}</h1>

	@import(header,array('ovarahalla' => range(1,2)))

	Life is {{ $body.first }}

	<input type="text" value="{{{ $escaper.nest }}}" data-host="{{{ $.server.HTTP_HOST }}}">

	@{{ some@mail.com }}

	@if(true == false)
		ebelehae
	@elseif(false == false)
		wassup
	@else
		ovarahalla
	@endif

	@if($.get and $.get.x)
		<h1>{{ $.get.x }}</h1>
	@endif

	<ul>
	@foreach($inside_suka.arr as $k=>$v)
		<li>{{ $v.id }} - {{ $v.name }}</li>
	@endforeach
	</ul>

	@forelse($mas as $m)
		{{ $m }}
	@empty
		Array is empty
	@endforelse

	@switch($boom): case 'first'
		<h1>{{$boom}}</h1>
		@break; case 'second':
			<h1>Lalka</h1>
		@break

		@default
		<h1>Not boom</h1>
		@break
	@endswitch
</body>
</html>