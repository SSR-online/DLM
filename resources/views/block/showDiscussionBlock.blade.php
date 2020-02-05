<div class="messagelist">
	<div class="scroll">
	@include('block.discussion.messages', ['messages' => $block->messages])
	</div>
</div>
<form method="post" action="/discussion/{{$node->id}}/post">
	<div class="discussionstatus" 
		data-lang-typing="@lang("typing")"
		data-lang-present="@lang("present")"
		data-lang-person="@lang("person")"
		data-lang-persons="@lang("persons")"></div>
	{{ csrf_field() }}
	<label>@lang("Write message")
		<textarea name="message"> </textarea>
	</label>
	<input type="submit" value="@lang("Post message")" />
</form>