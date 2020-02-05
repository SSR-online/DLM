{!! $block->content !!}
@if($block->node->children)
	@foreach($block->node->children as $child_node)
		@if(class_basename($child_node->block) == 'AsideBlock')
			@include('block.showAsideBlock', ['block' => $child_node->block])
			{{-- <aside data-type="aside-block" id="{{$child_node->block->html_id}}">
				@if($child_node->title)<h4>{{$child_node->title}}</h4>@endif
				{!! $child_node->block->content !!}
				<a href="#{{$child_node->block->html_id}}-ref">Terug</a>
			</aside> --}}
		@elseif(class_basename($child_node->block) == 'ImageBlock')
			@include('block.showImageBlock', ['block' => $child_node->block])
		@endif
	@endforeach
@endif
{{-- @if($isediting)@can('update', $node)<a class="add_section" href="/module/{{$module->id}}/node/{{$block->node->id}}/block/select">✚ Blok toevoegen in tekstblok</a>@endcan @endif --}}