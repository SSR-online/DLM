<legend>Onderdelen in juiste volgorde</legend>
<input type="hidden" name="answer-order" id="answer-order" value="" />
<div class="sortable" data-node="{{ $node->id }}" data-field="answer-order">
	@foreach( $node->block->answers as $answer)
		<label data-id="{{$answer->id}}">Onderdeel:
			<input type="text" name="answer[{{$answer->id}}]" value="{{$answer->content}}" />
			<a class="delete" href="/answeroption/{{$answer->id}}/delete" />Verwijder</a>
		</label>
	@endforeach
</div>
<input type="submit" name="add_answer_option" value="Antwoordmogelijkheid toevoegen" />