@extends('layouts.app')

@section('title')
{{$module->title}}
@endsection

@section('pagetools')
@if($isediting) 
@can('update', $node->module)
	<a class="edit" href="/module/edit/{{$module->id}}">@lang('Edit')</a>
	<a class="edit export" href="/module/{{$module->id}}/export" data-turbolinks="false">@lang("Export")</a> 
@endcan
@endif
@endsection

@section('sidebar')
	@include('node.nav', ['id' => ''])
@endsection

@section('content')

@if($isediting)
	@can('update', $node)
	@if(!$ismoving)
		<a class="edit" href="/node/edit/{{$node->id}}">@lang("Edit page")</a>
		<a class="edit duplicate" href="/node/{{$node->id}}/duplicate">@lang("Duplicate page")</a>
		<a class="edit move" href="/node/{{$node->id}}/move">@lang("Move page")</a>
	@endif
	@endcan
	{{-- @if($ismoving)<a class="droptarget" href="/node/{{$ismoving}}/move/targetnode/{{$node->id}}">Verplaats voor node</a>@endif --}}
@endif
<h2>{{ $node->title }}</h2>
{!! $node->content !!}
@if($node->block)
	@include('block.show' . class_basename($node->block), ['block' => $node->block])
@endif

<!-- show layouts -->
@foreach($node->layouts as $layout)
	@if($isediting) @can('update', $node)<a class="edit" href="/layout/{{$layout->id}}/edit">@lang("Edit layout (:type)", ['type' => $layout->type])</a>@endcan @endif
	<div class="layout {{$layout->type}}">
	@foreach($layout->slots as $slot)
		<section class="{{$layout->type}} slot">
			<div>
			@if($isediting)
				@if($ismoving) @can('update', $node)<a class="droptarget" href="/node/{{$ismoving}}/move/targetslot/{{$slot->id}}/top">@lang("Move to first position")</a> @endcan @endif
			
				<input type="hidden" name="order-slot-{{$slot->id}}" id="order-slot-{{$slot->id}}" value="" />
				<div class="sortable" data-field="order-slot-{{$slot->id}}" data-group="node-{{$node->id}}" data-url="/slot/{{$slot->id}}/sort" /">
			@endif
			@foreach($slot->nodes as $slot_node)
				@include('node.display', ['node' => $slot_node])
			@endforeach
			@if($ismoving)</div>@endif
			</div>
		@if($ismoving) @can('update', $node)<a class="droptarget" href="/node/{{$ismoving}}/move/targetslot/{{$slot->id}}/bottom">@lang("Move to last position")</a>@endcan @endif
		@if($isediting &! $ismoving)
		@can('update', $node)
		<div class="add_options">
			<a class="add_section" href="/node/{{$node->id}}/slot/{{$slot->id}}/create">✚ @lang("Add section")</a>
			<a class="add_section" href="/node/{{$node->id}}/slot/{{$slot->id}}/block/select">✚ @lang("Add block")</a>
		</div>
		@endcan
		@endif
		</section>
	@endforeach
	</div>
@endforeach
<!-- show nodes not in layout -->
@foreach($node->children()->whereNull('layout_slot_id')->where('is_page', 0)->get() as $slot_node)
	<section id="node-{{$slot_node->id}}" class="node {{$slot_node->classString() }}">
		{{-- @if($ismoving && $slot_node->id != $ismoving)<a class="droptarget" href="/node/{{$ismoving}}/move/targetnode/{{$slot_node->id}}">Verplaats voor node</a>@endif --}}
		<h2>{{ $slot_node->title }}</h2>
		@if($isediting &! $ismoving)
		@can('update', $node)
			<a class="edit" href="/node/edit/{{$slot_node->id}}">Sectie aanpassen</a>
			@if(!$ismoving)<a class="edit move" href="/node/{{$slot_node->id}}/move">Sectie verplaatsen</a>@endif
		@endcan
		@endif
		{!! $slot_node->content !!}
		@if($slot_node->block)
			@include('block.show' . class_basename($slot_node->block), ['block' => $slot_node->block])
			@if($isediting &! $ismoving) @can('update', $node)<a class="edit move" href="/node/{{$slot_node->id}}/move">Blok verplaatsen</a>@endcan @endif
		@else
			<h2>{{ $slot_node->title }}</h2>
			{!! $slot_node->content !!}
			@if($ismoving && $slot_node->id != $ismoving)<a class="droptarget" href="/node/{{$ismoving}}/move/targetnode/{{$slot_node->id}}">Verplaats naar node</a>@endif
			@if($isediting &! $ismoving)
			@can('update', $node)
			<div class="add_options">
				<a class="add_section" href="/module/{{$module->id}}/node/{{$slot_node->id}}/section/create">✚ @lang("Add section")</a>
				<a class="add_section" href="/module/{{$module->id}}/node/{{$slot_node->id}}/block/select">✚ @lang("Add block")</a>
			</div>
			@endcan
			@endif
			@include('node.children', ['current'=>$slot_node, 'in_layout' => true])
		@endif
		@if($ismoving && $slot_node->id != $ismoving)<a class="droptarget" href="/node/{{$ismoving}}/move/targetnode/{{$slot_node->id}}">Verplaats naar node</a>@endif
		@if($isediting &! $ismoving)
		@can('update', $node)
		<div class="add_options">
			<a class="add_section" href="/module/{{$module->id}}/node/{{$slot_node->id}}/section/create">✚ @lang('Add section')</a>
			<a class="add_section" href="/module/{{$module->id}}/node/{{$slot_node->id}}/block/select">✚ @lang('Add  block')</a>
		</div>
		@endcan
		@endif
	</section>
@endforeach

@if($ismoving && $node->id != $ismoving) @can('update', $node)<a class="droptarget" href="/node/{{$ismoving}}/move/targetnode/{{$node->id}}">@lang('Move to node')</a>@endcan @endif
@if($isediting &! $ismoving)
@can('update', $node)
<div class="add_options">
	<a class="add_section" href="/node/{{$node->id}}/layout/create">✚ @lang('Add layout')</a>
</div>
@endcan
@endif
@endsection

@section('bottomnav')
	@if($node->previous)
		<li class="previous"><a href="{{$node->previous->path()}}" class="previous">◀︎ <strong>{{ $node->previousTitle }}</strong></a></li>
	@endif
	@if($node->next || $node->setting('jump_nodes'))
		<li class="next">
			@if($node->setting('jump_nodes'))
				@foreach($node->setting('jump_nodes') as $jump)
					<a href="{{optional(App\Node::find($jump['id']))->path()}}"><strong>{{$jump['name'] or optional(App\Node::find($jump['id']))->title}}</strong></a>
				@endforeach
			@endif
			@if($node->next)
				<a href="{{$node->next->path()}}" class="next"><strong>{{ $node->nextTitle }} ►</strong></a>
			@endif
		</li>
	@endif
@endsection