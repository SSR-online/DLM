@extends('layouts.app')

@section('title')
Module importeren
@endsection

@section('content')
<form method="post" enctype="multipart/form-data" action="/module/import">
	{{ csrf_field() }}
	<label>Module-bestand (.dlm)<input type="file" name="file" /></label>
	<input type="submit" value="Module importeren" />
</form>
@endsection
