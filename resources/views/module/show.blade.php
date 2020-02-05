@extends('layouts.app')

@section('title')
{{ $module->title }} 
@if($isediting) 
<div class="tools">
@can('update', $module)
	<a class="edit" href="/module/edit/{{$module->id}}">Aanpassen</a>@endcan
	<a class="edit export" href="/module/{{$module->id}}/export" data-turbolinks="false">Exporteren</a>@endcan
</div> @endif
@endsection

@section('sidebar')
	@include('node.nav', ['id' => ''])
@endsection

@section('content')
@foreach( $module->nodes->sortBy('sort_order') as $node )
	<section>
		<h2>{{$node->title}}</h2>
		{!! $node->content !!}
		@if($isediting)<a class="edit" href="/node/edit/{{$node->id}}">Aanpassen</a>@endif
	</section>
@endforeach
@if($isediting) @can('update', $node)<a class="edit" href="/module/{{$module->id}}/page/create">Toevoegen</a>@endcan @endif
@endsection
