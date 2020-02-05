@extends('layouts.app')

@section('title')
@lang("Show submissions for <strong>:quiztitle</strong>", ['quiztitle' => $node->title])
@endsection

@section('sidebar')
	@include('block.quiz.nav', ['active' => 'aggregate'])
@endsection

@section('content')
<ol>
@foreach($results->question_results() as $result)
	<li>
		<h3>{!! $result['question']->question !!}</h3>
		@if($result['totals'])
		@foreach($result['totals'] as $answer)
			{{ $answer['answer']->content }}
			<div class="bar"><div class="filled" style="width: {{ $answer['percentage'] }}%;"></div><div class="text">{{ $answer['count'] }} ({{ $answer['percentage'] }}%)</div></div>
		@endforeach
		@else
			Geen statistieken van deze vraag beschikbaar.
		@endif
	</li>
@endforeach
</ol>
@endsection