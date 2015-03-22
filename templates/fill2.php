@extends('inherit')

@inject('css')
{{ Ant::css('durgi.css') }}
@append

@inject('header')
<h1>Template #2</h1>
@rewrite

@inject('article')
Lorem ipsum... Again it!!!
@append