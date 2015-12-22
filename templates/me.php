@unless(false)
	{{{ $this->js('/script.js') }}}

	<h1>{{ $.globals.sasaika }}</h1>

	@switch('kitty')
		@case('kitty')
			{{ $.server.HTTP_HOST }}
		@break
	@endswitch

	<img width="100%" src="" />
 
	{{{ $this->plugin->youtube->embed('hchTjwPZPn8') }}}

	<h1>{{ $this->capitalize('sasai') }}</h1>

@endunless