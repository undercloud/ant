@extends('inherit')

@inject('css')
{{ Ant::css('ovarahalla.css') }}
@append

@inject('header')
<h1>CUSTOME HEADER FOR IT section UX Aeae</h1>
@rewrite

@inject('article')
Lorem ipsum...
@append