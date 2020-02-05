@extends('layouts.app')

@section('title')
DLM - {{ $tagline }}
@endsection

@section('sidebar')
@can('create', App\Setting::class)
	<a href="/settings/">@Lang('Settings')</a>
@endcan
@endsection

@section('content')
<a href="{{$archiveurl}}">@lang('Show archived modules')</a>
<table>
	<thead>
		<tr>
			<th><a class="sortable {{$sort['title']['order']}}" href="{{$sort['title']['url']}}">@lang('Title')</a></th>
			<th><a class="sortable {{$sort['datemodified']['order']}}" href="{{$sort['datemodified']['url']}}">@lang('Last modified')</a></th>
			<th><a class="sortable {{$sort['category']['order']}}" href="{{$sort['category']['url']}}">@lang('Category')</a></th>
			<th>@lang('Edit')</th>
		</tr>
	</thead>
	<tbody>
@foreach($modules as $module)
@can('view', $module)
<tr>
	<td><a href="/module/{{$module->id}}">{{$module->title}}</a></td>
	<td>{{$module->updated_at}}</td>
	<td>{{$module->category}}</td>
	<td>
		@can('update', $module)
		<form action="/module/{{$module->id}}/archive" method="post"  class="inline">
			{{ csrf_field() }}
			<input type="submit" value="@lang('Archive')" class="subtle" />
		</form>
		@endcan
	</td>
</tr>
@endcan
@endforeach
</tbody>
</table>
<div class="add_options">
	<a class="add_section" href="/module/create">✚ @lang('Add module')</a>
	<a class="add_section" href="/module/import">✚ @lang('Import module')</a>
</div>
@endsection
