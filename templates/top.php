<div>
	@if(true)
	<div style="border-top:5px solid #651;">
	@endif

	<div>{{{ __DIR__ }}} {{ date('Y:m:d H:i:s') }}</div>

	{{ $.get.p or 'Mamu ebal gan\'don' }}

	{{ GANDURAS }}

	@php
		class X {
			public static function p()
			{
				echo 'All Fine X::p';
			}
		}
	@endphp

	@skip
		@forelse ?
	@endskip

	@php
		echo "@forelse";
	@endphp
	<h1>{{ X::p() }}</h1>
</div>