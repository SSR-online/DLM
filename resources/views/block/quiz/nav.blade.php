<ol class="sticky">
	<li><a href="{{$node->path() }}">@lang("Back to quiz")</a></li>
	<li @if($active=='all') class="active" @endif><a href="/quiz/{{$node->id }}/submissions">@lang("All submissions")</a></li>
	<li @if($active=='aggregate') class="active" @endif><a href="/quiz/{{$node->id }}/submissions/aggregate">@lang("Statistics")</a></li>
</ol>