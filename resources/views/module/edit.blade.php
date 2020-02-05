@extends('layouts.app')

@section('title')
@lang('Edit') <strong>{{ $module->title }}</strong>
@endsection

@section('content')
<form method="post" action="/module/edit/{{$module->id}}">
	@include('module.fields')
	<a class="delete" href="/module/{{$module->id}}/delete">@lang("Delete")</a>
</form>
@endsection
