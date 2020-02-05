@extends('layouts.app')

@section('title')
Bewerk node {{ $module->title }}
@endsection

@section('content')
<form method="post" action="/node/edit/{{$node->id}}">
	@include('node.fields')
</form>
@endsection
