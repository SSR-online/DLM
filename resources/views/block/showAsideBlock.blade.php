<aside class="sticky" data-type="aside-block" id="{{$block->html_id}}">
	@if($block->node->title)<h4>{{$block->node->title}}</h4>@endif
	{!! $block->content !!}
	<a href="#"data-action="close" data-id="{{$block->html_id}}">@lang("close")</a>
</aside>