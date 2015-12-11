@extends('core/base')

@inject('main')
<h1>{{ $one }}</h1>
@unless(false)
<h1>Sasai lalka</h1>
@endunless
@rewrite