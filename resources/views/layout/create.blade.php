@extends('layouts.app')

@section('header')
<meta name="turbolinks-visit-control" content="reload">
@endsection

@section('title')
@lang('New layout')
@endsection

@section('content')
<form method="post" action="/node/{{$node->id}}/layout/create">
	{{ csrf_field() }}
	<label>@lang("Title")
	<input type="text" name="title">
	</label>
	<label>@lang("Type")
		<select name="type">
			@foreach(App\Layout::$types as $type=>$displayname)
				<option value="{{$type}}">@lang($displayname)</option>
			@endforeach
		</select>
	</label>
	<input type="submit" value="@lang("Add layout")" />
</form>
@endsection
