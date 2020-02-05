@extends('layouts.app')

@section('title')
Layout {{ $layout->title }} aanpassen
@endsection

@section('content')
<form method="post" action="/layout/{{$layout->id}}/edit">
	{{ csrf_field() }}
	<label>Titel
	<input type="text" name="title" value="{{$layout->title}}">
	</label>
	<label>Type
		<select name="type">
			@foreach(App\Layout::$types as $type=>$displayname)
				<option value="{{$type}}" @if($type==$layout->type) selected="selected" @endif>{{$displayname}}</option>
			@endforeach
		</select>
	</label>
	<input type="submit" value="Layout wijzigen" />
	<a class="delete" href="/layout/{{$layout->id}}/delete">Verwijder</a>
</form>
@endsection
