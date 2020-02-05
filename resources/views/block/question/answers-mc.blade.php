@foreach($block->answersForDisplay as $answer)
	<label class="check @if(isset($quiz) && optional($quiz->current_user_attempt())->complete && $block->hasCorrectAnswer() && $block->isAnswerForUser($answer))
			 @if($block->isCorrectAnwerForUser($answer)) 
			 	correct
			 @else
			 	incorrect
			 @endif
			@endif ">
		<input @if($block->answerForUser()) disabled="disabled" @endif type="{{$block->input_type()}}" name="answer[]" value="{{ $answer->id }}" @if($block->isAnswerForUser($answer)) checked="checked" 
		 @endif
		 >{{ $answer->content }}
	</label>
@endforeach