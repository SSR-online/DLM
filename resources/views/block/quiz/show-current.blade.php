<section id="node-{{$block->currentNode()->id}}">
	<div class="tools">
		@if($isediting && $block->currentNode())
			@if(!$ismoving) @can('update', $block->currentNode())<a class="edit move" title="Blok verplaatsen" href="/node/{{$block->currentNode()->id}}/move"> </a>@endcan @endif
			@can('update', $node)<a class="edit" title="Blok aanpassen" href="/node/edit/{{$block->currentNode()->id}}"> </a>@endcan
		@endif
	</div>
	@include('block.show' . class_basename($block->currentNode()->block), ['block' => $block->currentNode()->block, 'standalone' => false, 'quiz' => $block, 'showfeedback' => ($block->feedback_type == 'direct')])
	@if(($block->allow_navigation 
			|| !(class_basename($block->currentNode()->block) == 'QuestionBlock')
			|| optional($block->currentNode())->block->attemptForUser()
		) 
		&& $block->nodeAfter($block->currentNode())
	)
		<a href="?questionid={{$block->nodeAfter($block->currentNode())->id}}">@lang("Next question") ►︎</a>
	@endif
	@if(optional($block->current_user_attempt())->complete)
		<a class="results" href="?{{$block->id}}-showresults=true">@lang("Show results")</a>
	@endif
</section>