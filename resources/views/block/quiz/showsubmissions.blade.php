@extends('layouts.app')

@section('title')
@lang("Show submissions for <strong>:quiztitle</strong>", ['quiztitle' => $block->node->title])
@endsection

@section('sidebar')
	@include('block.quiz.nav', ['active' => 'all'])
@endsection

@section('content')

<table>
	<thead>
		<tr>
			<th>@lang("Name")</th>
			<th>@lang("Submitted")</th>
			<th>@lang("Score")</th>
			<th>@lang("Show")</th>
		</tr>
	</thead>
	<tbody>
@foreach($attempts as $attempt)
	<tr>
		<td>{{$attempt->user->name}}</td>
		<td>{{$attempt->updated_at->format('d-m-Y H:i')}}</td>
		<td>{{$attempt->score}}</td>
		<td><a href="/quiz/{{$block->node->id}}/submissions/{{$attempt->id}}">@lang("Show submission")</a></td>
@endforeach
	</tbody>
</table>
@endsection