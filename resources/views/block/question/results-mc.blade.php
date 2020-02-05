@foreach($block->answers as $answer)
<label class="feedback
	@if($block->isCorrectAnwerForUser($answer)) 
		correct 
	@elseif($block->isAnswerForUser($answer))
		incorrect 
	@endif
">
	{{ $answer->content }}
</label>
@endforeach