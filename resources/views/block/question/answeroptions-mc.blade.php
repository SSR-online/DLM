<input type="hidden" name="answer-order" id="answer-order" value="" />
<div class="sortable" data-node="{{ $node->id }}" data-field="answer-order">
@foreach( $node->block->answers as $answer)
	<div data-id="{{$answer->id}}">
		<label>@lang("Answer option"):
			<input type="text" name="answer[{{$answer->id}}]" value="{{$answer->content}}" />
		</label>
		<label class="check"><input type="checkbox" name="answer-correct[{{$answer->id}}]" @if($answer->is_correct) checked="checked" @endif /> @lang("Correct")
			<input type="submit" class="delete" name="remove_answer_option_{{$answer->id}}" value="@lang("Delete")" />
		</label>
	</div>
@endforeach
</div>
<input type="submit" name="add_answer_option" value="@lang("Add answer option")" />
<label class="check">
	<input type="checkbox" name="randomize" @if($node->block->setting('randomize')=='true') checked="checked" @endif />@lang("Randomise answer order")
</label>
<label>
	@lang("Answer type"):
	<select name="mc_type">
		<option value="single" @if($node->block->setting('mc_type')=='single') selected="selected" @endif)>@lang("Single answer")</option>
		<option value="multiple" @if($node->block->setting('mc_type')=='multiple') selected="selected" @endif>@lang("Allow multiple answers")</option>
	</select>
</label>