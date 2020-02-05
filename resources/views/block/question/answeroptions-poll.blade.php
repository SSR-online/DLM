@foreach( $node->block->answers as $answer)
	<label>Antwoordmogelijkheid:
		<input type="text" name="answer[{{$answer->id}}]" value="{{$answer->content}}" />
	</label>
@endforeach
<input type="submit" name="add_answer_option" value="Antwoordmogelijkheid toevoegen" />
<label>
	Soort antwoord:
	<select name="mc_type">
		<option value="single" @if($node->block->setting('mc_type')=='single') selected="selected" @endif)>Enkel antwoord</option>
		<option value="multiple" @if($node->block->setting('mc_type')=='multiple') selected="selected" @endif>Meerdere antwoorden mogelijk</option>
	</select>
</label>