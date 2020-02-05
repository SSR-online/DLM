@extends('layouts.app')

@section('title')
@lang("Settings") - @lang("new")
@endsection

@section('sidebar')
	
@endsection

@section('content')
<form method="post" action="/setting/create">
	{{ csrf_field() }}
	<fieldset>
	<label for="name">
		@lang("Name"):
		<input type="text" name="name" value="" />
	</label>
	<label for="value">
		@lang("Value"):
		<input type="text" name="value" value="" />
	</label>
	<input type="submit" value="@lang("Save")" />
	<a href="/settings/">@lang("Cancel")</a>
</form>
@endsection