<div data-id="{{$message->id}}" class="message @if($message->isMine()) mine @endif">
	<p class="author"><em> {{ $message->user->name }} <time datetime="{{ $message->updated_at->toIso8601String() }}">{{ $message->updated_at->diffForHumans() }}</time></em></p>
	{!! $message->message !!}
</div>