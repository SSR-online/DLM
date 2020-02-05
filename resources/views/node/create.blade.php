@extends('layouts.app')

@section('title')
@lang('New node')
@endsection

@section('content')
<form method="post" action="/node/create">
	@include('node.fields')
</form>
@endsection
