@extends('layouts.app')

@section('title')
@lang('Select block')
@endsection

@section('sidebar')
	@include('node.nav', ['id' => ''])
@endsection

@section('content')
<div class="add_options">
@foreach($node->childTypes as $type)
	<a class="add_section" href="{{$urlPrefix}}/block/create/{{class_basename($type)}}">✚ @lang($type::$displayName)</a>
@endforeach
</div>
@endsection
