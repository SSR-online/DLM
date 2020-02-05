<fieldset>
	<label>@lang("Video URL"):
		<input type="url" name="url" value="{{$node->block->url}}" />
	</label>
	<label>@lang("Caption"):
		<textarea name="caption">{!!$node->block->caption!!}</textarea>
	</label>
</fieldset>