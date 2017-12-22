@extends('dapenti.index')

@section('title')
    {{ $title }}
@stop

@section('content')
  @foreach ($content as $single)
      <p> <a href="{{ $single['link'] }}">
        @if(str_contains($single['title'], '喷嚏图卦'))
		<span class="text-danger">{{ $single['title'] }}</span>
        @else
		{{ $single['title'] }}
        @endif
  		</a></p>
    <hr>
  @endforeach
@stop