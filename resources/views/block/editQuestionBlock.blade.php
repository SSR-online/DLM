<fieldset>
	<label>@lang("Question"):
		<textarea class="edit" name="question">{{$node->block->question}}</textarea>
	</label>
	@if($node->block->question_type)
		@include('block.question.answeroptions-' . $node->block->question_type)
	@else
		@include('block.question.select', ['block' => $node->block])
	@endif
	@if($node->block->question_type != 'poll')
		<label class="check">
			<input type="checkbox" name="show_feedback" @if($node->block->setting('show_feedback')) checked="checked" @endif>@lang("Show feedback after answer")
		</label>
		<label>
			@lang("Feedback (correct answer)"):
			<textarea class="edit" name="feedback_correct">{{ $node->block->feedback_correct }}</textarea>
		</label>
		<label>
			@lang("Feedback (incorrect answer)"):
			<textarea class="edit" name="feedback_incorrect">{{ $node->block->feedback_incorrect }}</textarea>
		</label>
		<label class="check">
			<input type="checkbox" name="can_restart" @if($node->block->setting('can_restart') == -1) checked="checked" @endif>@lang("Allow unlimited attempts")
		</label>
	@endif
</fieldset>