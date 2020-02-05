<div class="mediaplayercontainer">
	{!! $block->display() !!}
	@if($block->is_interactive())
	@foreach($block->node->children as $child)
		<div id="video-block-{{$child->id}}" class="hidden video-overlay" data-timestamp="{{$child->block->setting('timestamp')}}" data-id="{{$child->id}}">
			<div>
				<div class="replacenode">
					@include('node.display', ['node' => $child])
				</div>

				<div class="nav @if($child->block->setting('unskippable') == 1) hidden @endif">
					<button type="button" class="play">Verder ►</button>
				</div>
			</div>
		</div>
	@endforeach
	@if($isediting)<a class="add_section" href="/module/{{$module->id}}/node/{{$block->node->id}}/block/select">✚ Vraag / blok toevoegen in video</a>@endif
	@endif
</div>
@if($block->caption)<div class="caption">{!! $block->caption !!}</div>@endif