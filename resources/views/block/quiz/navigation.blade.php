{{-- Quiz Navigation --}}
<nav class="quiz">
	@if($isediting)<input type="hidden" name="quizsort-{{$block->id or ''}}" id="quizsort-{{$block->id}}" value="" />@endif
	<ol @if($isediting) class="sortable"  data-field="quizsort-{{$block->id}}" data-group="quiz-{{$block->id}}" data-url="/module/{{ $module->id }}/sortnodes/{{$block->node->id}}" @endif>
		@if($block->allow_navigation 
		|| !(class_basename(optional($block->currentNode())->block) == 'QuestionBlock')
		|| optional($block->questionAttemptForUser($block->currentNode())))
			@if($block->nodeBefore($block->currentNode()))
				<li class="prev nosort"><a href="?questionid={{$block->nodeBefore($block->currentNode())->id}}">◀︎ <span>Vorige vraag</span></a></li>
			@endif
		@endif
		@if($block->node->children)
			@foreach($block->node->children as $child_node)
			<li data-id="{{$child_node->id}}" 
				class="
					@if($child_node->is($block->currentNode())) current @endif 
					@if(method_exists($child_node->block, 'attemptForUser') 
						&& $block->questionAttemptForUser($child_node)) answered 
					@endif">
				@if($block->allow_navigation)
					<a href="?questionid={{$child_node->id}}" data-id="{{$loop->iteration}}">
				@else
					<span>
				@endif
				@if(class_basename($child_node->block) == 'QuestionBlock')
					@php echo (isset($i)) ? $i : $i = 1; $i++; @endphp
				@else 
					{{ $child_node->title }}
				@endif
				@if($block->allow_navigation)
					</a>
				@else
					</span>
				@endif
			</li>
		@endforeach
		@endif
		<li class="@if(session($block->id .'-showresults') || request($block->id .'-showresults')) current @endif nosort">
			@if(optional($block->current_user_attempt())->complete)
				<a href="?{{$block->id}}-showresults=true">@lang("Results")</a>
			@else
				{{-- <span>Resultaten</span> --}}
			@endif
		</li>
		@if($block->allow_navigation 
		|| !(class_basename(optional($block->currentNode())->block) == 'QuestionBlock')
		|| $block->questionAttemptForUser($block->currentNode()))
			@if($block->nodeAfter($block->currentNode()))
				<li class="next nosort"><a href="?questionid={{$block->nodeAfter($block->currentNode())->id}}"><span>Volgende vraag</span> ►︎</a></li>
			@elseif(!session($block->id . '-showresults') && !request($block->id . '-showresults') && optional($block->current_user_attempt())->complete)
				<li class="next nosort"><a href="?{{$block->id}}-showresults=true">@lang("Show results") ►︎</a></li>
			@endif
		@endif
	</ol>
</nav>
