@extends('layouts.app')

@section('title')
@lang('Edit') <strong>{{ $node->title }}</strong>
@endsection

@section('content')
<form method="post" enctype="multipart/form-data" action="/node/edit/{{$node->id}}">
	@include('node.fields')
	<a class="delete" href="/node/{{$node->id}}/delete">@lang('Delete')</a>
</form>

{{-- <form method="post" action="/node/{{$node->id}}/delete">
	{{ csrf_field() }}
	<input type="submit" class="delete" value="Verwijderen" />
</form> --}}
@endsection