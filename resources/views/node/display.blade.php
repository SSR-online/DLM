<section id="node-{{$node->id}}" data-id="{{$node->id}}" data-node="{{$node->id}}" class="node {{$node->classString() }}" data-group="nodes">
 	@if($node->block)
		@if($isediting)
			<div class="tools">
				@can('update', $node)
					
				@endcan 
				@can('update', $node)<a class="edit" title="@lang("Edit block")" href="/node/edit/{{$node->id}}"> </a>
				<a class="edit settings" data-popup="settings-{{$node->id}}" title="Instellingen blok" href="#"></a>
				<ul id="settings-{{$node->id}}" class="popup">
					@if(!$ismoving)
						<li><a class="move" href="/node/{{$node->id}}/move">@lang("Move block")</a></li>
					@elseif($ismoving == $node->id)
						<li><a class="stopmove" href="/node/{{$node->id}}/stopmove">@lang("Stop moving")</a></li>
					@endif
					<li><a href="/node/{{$node->id}}/duplicate">@lang("Duplicate")</a></li>
					<li><a href="/node/{{$node->id}}/delete" class="delete">@lang("Delete")</a></li>
					

				</ul> 
				@endcan
			</div>
		@endif
		@include('block.show' . class_basename($node->block), ['block' => $node->block])
	@else
		<h3>{{$node->title}}</h3>
		{!!$node->content!!}
		@if($isediting &! $ismoving)
		@can('update', $node)
			<div class="tools">
				<a class="edit" href="/node/edit/{{$node->id}}">@lang("Edit section")</a>
				<a class="edit settings" data-popup="settings-{{$node->id}}" title="Instellingen blok" href="#"></a>
				<ul id="settings-{{$node->id}}" class="popup">
					@if(!$ismoving)
						<li><a class="move" title="Node verplaatsen" href="/node/{{$node->id}}/move">@lang("Move")</a></li>
					@elseif($ismoving == $node->id)
						<li><a class="stopmove" title="Stop verplaatsen" href="/node/{{$node->id}}/stopmove">@lang("Stop moving")</a></li>
					@endif
					<li><a href="/node/{{$node->id}}/duplicate">@lang("Duplicate")</a></li>
					<li><a href="/node/{{$node->id}}/delete" class="delete">@lang("Delete")</a></li>
				</ul> 
			</div>
		@endcan
		@endif
		{{-- @if($isediting &! $ismoving)<a class="droptarget" href="/node/{{$ismoving}}/move/targetnode/{{$node->id}}">Verplaats naar  positie achter node</a>@endif --}}
		@if($node->children)
			@include('node.children', ['current'=>$node])
		@endif
	@endif
@if(!$node->block && $isediting)
	@can('update', $node)
		@if($ismoving)
			@if($ismoving != $node->id)<a class="droptarget" href="/node/{{$ismoving}}/move/targetnode/{{$node->id}}">Verplaats naar node</a>@endif
		@else
			<a class="add_section" href="/module/{{$module->id}}/node/{{$node->id}}/block/select">✚ Blok toevoegen in sectie {{ $node->title }}</a>
		@endif
	@endcan
@endif
</section>