@extends('layouts.app')

@section('title')
LTI client
@endsection
@section('content')
<form action="/lti/consumer/save" method="post">
	{{ csrf_field() }}
	<label>@lang("Domain")
		<input type="url" name="domain" value="https://{{ $key or '' }}" />
	</label>
	<label>@lang("Name")
		<input type="text" name="name" value="{{ $consumer->name or '' }}" />
	</label>
	@if($key)
	<label>@lang("Secret (you usually don't need to change this)")
		<input type="text" name="secret" value="{{ $consumer->secret }}" />
	</label>
	@endif
	<input type="submit" value="@lang("Add client")" />
</form>
@endsection
