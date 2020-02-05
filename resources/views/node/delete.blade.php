@extends('layouts.app')

@section('title')
@lang("Delete") <strong>{{ $title }}</strong>
@endsection

@section('content')
<p>@lang("Are you sure you want to delete :title", ['title' => ($title) ? $title : __("this node")])</p>

<form method="post" action="{{ $delete_action }}">
	{{ csrf_field() }}
	<input type="submit" value="@lang("Delete")" />
	<a href="{{$cancel_url}}">@lang("Cancel")</a>
</form>
@endsection