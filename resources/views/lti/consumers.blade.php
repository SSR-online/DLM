@extends('layouts.app')

@section('title')
LTI clients
@endsection
@section('content')
<table>
	<thead>
		<tr>
			<th>@lang("Domain")</th>
			<th>@lang("Client")</th>
		</tr>
	</thead>
	<tbody>
	@foreach($consumers as $consumer)
		<tr>
			<td>
				<a href="/lti/consumer/{{$consumer->getKey()}}">{{$consumer->getKey()}}</a>
			</td>
			<td>{{$consumer->name }}<pre class="code">{{$consumer->secret }}</pre></td>
		</tr>
@endforeach
	</tbody>
</table>
<a href="/lti/consumer/">@lang("Add LTI consumer")</a>
@endsection