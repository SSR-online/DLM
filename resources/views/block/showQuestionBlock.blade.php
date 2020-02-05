<?php $standalone = (isset($standalone)) ? $standalone : true; ?>
<?php $showfeedback = (isset($showfeedback)) ? $showfeedback : false; ?>
@if($standalone)
@if($block->current_user_can_restart())
	<div class="notice">@lang("You have already answered this question. Starting a new attempt removes your previous answer.")
		<form method="post" action="/question/attempt/{{$block->id}}/delete">
			{{ csrf_field() }}
			<input type="submit" name="start_attempt" value="@lang("Start new attempt")" />
		</form>
	</div>
@endif
<form method="post" action="/question/attempt/{{$block->id}}" class="question">
{{ csrf_field() }}
@endif

{!! $block->question !!}
@if($block->attemptForUser() && $block->question_type == 'poll')
	{!! $block->renderPollResults() !!}
@else
	@include('block.question.answers-' . $block->question_type)
	@if($block->attemptForUser( Auth::user()->id ))
		@if($showfeedback || ($standalone && $block->setting('show_feedback') && $block->feedback()))
			<div class="feedback @if($block->setting('mc_type')=='poll') @elseif($block->attemptForUser( Auth::user()->id )->correct) correct @else incorrect @endif">{!! $block->feedback() !!}</div>
		@endif
	@else
		<input type="hidden" name="questionid" value="{{$block->node->id}}">
		<input type="submit" value="@lang("Save answer")" />
	@endif
@endif
@if($standalone)
</form>
@endif