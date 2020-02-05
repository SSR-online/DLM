@if($block->node)
<form method="post" action="/quiz/attempt/{{$block->node->id}}">
{{ csrf_field() }}
	{{-- Submission button --}}
	@if($isediting || Auth::user()->can('viewResults', $block))
			@can('viewResults', $block)
				<a href="/quiz/{{$block->node->id}}/submissions" class="edit hastext submissions">@lang("Submissions")</a>
			@endcan
	@endif
	<div class="quiz-content @if($block->setting('display') == 'vertical' || $block->node->children->count() > 5) long @endif">
		@include('block.quiz.navigation')
		<div>
			{{-- Restart notice --}}
			@if($block->current_user_can_restart())
				<div class="notice">@lang("You've already completed this quiz.")
					<input type="submit" name="start_attempt" value="@lang("Start new attempt")" />
				</div>
			@endif

			{{-- Show current node / results overview --}}
			@if(session($block->id .'-showresults') || request($block->id .'-showresults'))
				@include('block.quiz.showresults')
			@elseif($block->currentNode())
				@include('block.quiz.show-current')
			@endif
		</div>
	</div>
</form>

{{-- Add block --}}
@if($isediting)<a class="add_section" href="/module/{{$module->id}}/node/{{$block->node->id}}/block/select">✚ @lang("Add question / block to quiz")</a>@endif
@endif