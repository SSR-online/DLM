<fieldset>
	<label>@lang("Summary"):
		<input name="summary" type="text" value="{{ $node->block->summary }}" />
	</label>
	<label>@lang("Content"):
		<textarea class="edit" name="details">{{ $node->block->details }}</textarea>
	</label>
</fieldset>