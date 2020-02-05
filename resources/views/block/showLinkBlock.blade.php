@if($block->setting('display')=='inline')
	<div style="padding-bottom: 95vh; position: relative; height: 0; overflow: hidden;">
		<iframe style="position: absolute; top:0; left: 0; width: 100%; height: 100%;" frameBorder="0" src="{{ $block->url or optional($block->targetNode)->path() }}"></iframe>
	</div>
@else
	<a class="{{$block->setting('display')}}" href="{{$block->url or optional($block->targetNode)->path() }}">{{$block->node->title or 'geen titel'}}</a>
@endif