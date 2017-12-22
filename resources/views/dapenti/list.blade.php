@extends('dapenti.index')

@section('title')
    {{ $title }}
@stop

@section('content')
	@foreach ($content as $single)
	    <p> <a href="{{ $single->link }}" target="_blank">{{ $single->title }}</a></p>
		<p><img src="{{ $single->imgurl }}" alt="{{ $single->title }}"></p>
		<hr>
	@endforeach
@stop