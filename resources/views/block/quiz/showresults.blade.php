<h3>@lang("Results")</h3>
@if($block->current_user_attempt()->complete)
	@if($block->node->children)
		<ol>
		@foreach($block->current_user_attempt()->questionAttemptsByquizOrder() as $question_attempt)
			<li>
				{!!$question_attempt->question->question !!}
				@include('block.question.answers-' . $question_attempt->question->question_type, ['block' => $question_attempt->question, 'quiz' => $block])
				@if($question_attempt->question->feedback())<div class="feedback @if($question_attempt->correct) correct @else incorrect @endif">
					{!! $question_attempt->question->feedback() !!}
				</div>
				@endif
			</li>
		@endforeach
		</ol>
	@endif
@else
	<p>@lang("Results will be available after you've completed the quiz.")</p>
@endif