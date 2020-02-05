@extends('layouts.app')

@section('title')
@lang("Show submission for <strong>:username</strong>", ['username' => $attempt->user->name])
@endsection

@section('sidebar')
	@include('block.quiz.nav', ['active' => ''])
@endsection

@section('content')
<ol>
@foreach($attempt->question_attempts as $question_attempt)
	<li>
		{!!$question_attempt->question->question !!}
		<div class="feedback @if($question_attempt->correct) correct @else incorrect @endif">
			@foreach($question_attempt->answerObjects as $answer)
				{!! $answer->content !!}
			@endforeach
		</div>
	</li>
@endforeach
</ol>
@endsection