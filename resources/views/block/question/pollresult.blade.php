<h3>Resultaten</h3>
@foreach($block->answerTotals() as $answer)
	<p>{{$answer['answer']->content }}</p> 
	@if($answer['count'] > 0)
	<p class="bar @if($block->isAnswerForUser($answer['answer'])) selected @endif" style="width: {{ $answer['percentage'] }}%;">{{ $answer['count'] }} ({{ $answer['percentage'] }}%) </p>
	@else
	<p>{{ $answer['count'] }} ({{ $answer['percentage'] }}%) </p>
	@endif
@endforeach
