<input type="hidden" id="answer-{{$block->node->id}}" name="answer" value="{{ $block->answerForUser() }}" />
<div class="@if(!$isediting) sortable @endif" data-node="{{ $block->node->id }}" data-field="answer-{{$block->node->id}}">
@foreach($block->answers->shuffle() as $answer)
	<div data-id="{{ $answer->id }}">{{ $answer->content }}</div>
@endforeach
</div>