@foreach($block->answers as $answer)
	<label class="check">
		<input type="{{$block->input_type()}}" name="answer[]" value="{{ $answer->id }}"> 
		{{ $answer->content }}
	</label>
@endforeach