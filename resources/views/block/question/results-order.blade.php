<label class="feedback
	@if($block->attemptForUser()->correct)
		correct 
	@else
		incorrect 
	@endif
">
	@foreach($block->attemptForUser()->answerObjects as $answer)
		{{ $answer->content }}<br />
	@endforeach
</label>