@extends('layouts.app')

@section('title')
@lang('New module')
@endsection

@section('content')
<form method="post" action="/module/create">
	@include('module.fields')
</form>
@endsection
